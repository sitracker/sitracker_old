<?php
// timecalc.php - Calculate SLA times
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Tom Gerrard

// Prevent script from being run directly (ie. it must always be included by auto.php)
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}

define("STATUS_CLOSED",2);
define("STATUS_CUSTOMER",8);

// FIXME this should only run INSIDE the working day
// FIXME ? this will not update the database fully if two SLAs have been met since last run - does it matter ?

if ($verbose) echo "Calculating SLA times{$crlf}";

$sql="SELECT id,maintenanceid,priority,slaemail,servicelevel,status FROM incidents WHERE status != ".STATUS_CLOSED;
$incident_result=mysql_query($sql);

while ($incident=mysql_fetch_array($incident_result)) {

    $now=time();

    // Get the service level timings for this class of incident, we may have one
    // from the incident itself, otherwise look at contract type
    if ($incident['servicelevel']=="") {
        $sql="SELECT tag FROM servicelevels, maintenance WHERE maintenance.id='{$incident['maintenanceid']}' AND servicelevels.id=maintenance.servicelevelid";
        $result=mysql_query($sql);
        $t=mysql_fetch_row($sql);
        $tag=$t[0];
        mysql_free_result($result);
    } else $tag=$incident['servicelevel'];


    $interval=array();
    $currentSla="";

    $slaStart=0;
    $reviewStart=0;
    $lastReview=0;

    // Pull out all the timing information for this incident from the updates table
    $sql="SELECT id, type, sla, timestamp, currentstatus, timesincesla, slacalculated, timesincereview, reviewcalculated FROM updates WHERE incidentid='{$incident['id']}' ORDER BY id DESC";
    $update_result=mysql_query($sql);

    // We are looking for three things here:
    // 1) The most recent update that has ReviewCalculated set (reviewStart)
    // 2) The most recent update that has SlaCalculated set, OR has met an SLA (slaStart)
    // 3) The most recent SLA type that was met, opened, initialresponse, etc. (currentSla)

    while ($update=mysql_fetch_array($update_result)) {
        // Read (backwards) through the updates to find the most recent sla and review information
        $interval[] = $update;
        end($interval);

        if ( ($slaStart==0) && ($update['slacalculated']=='true') ) 
            $slaStart=key($interval);
    
        if ( ($reviewStart==0) && 
            (($update['reviewcalculated']=='true') || ($update['type']=='reviewmet')) ) 
            $reviewStart=key($interval);

        if ( ($currentSla=="") && ($update['sla']!="") ) {
            $currentSla=$update['sla'];
            if ($slaStart==0) $slaStart=key($interval);
        }

        // If we have found the three items then we don't need to look back any more
        if ( ($reviewStart!="") && ($currentSla!="") ) break;
    }

    mysql_free_result($update_result);

    if ($currentSla=="") {
        // We have a problem, or SLAs are turned off, bail out of this one
        if ($verbose) echo "Cannot find SLA information for incident ".$incident['id'].", skipping{$crlf}";
    } 
    else 
    {

        // We need to calculate the working time for both review and SLA
        // and it could be a fairly slow process, so only do it once
        for ($i=count($interval)-2; $i>0; $i--)
            $interval[$i]['timesincelastupdate']=calculate_working_time($interval[$i+1]['timestamp'],$interval[$i]['timestamp']);

        $interval[0]['timesincelastupdate']=calculate_working_time($interval[0]['timestamp'],$now);

        // If there have been a few updates since the last run we need to update them historically
        // First do review ...

        $newReviewTime=0; // reviewStart-1 might be < 0, so this is important

        for ($i=$reviewStart-1; $i>=0; $i--) {

            // If we have just reviewed the incident then we start counting from 0
            if ($interval[$i]['type']=='reviewmet') $lastTime=0;
            else $lastTime=$interval[$i+1]['timesincereview'];
            $newReviewTime=$interval[$i]['timesincelastupdate']+$lastTime;

            // Now we have the time, put it back in the array in case the next iteration needs it
            $interval[$i]['timesincereview']=$newReviewTime;

            // And update the database
            $sql="UPDATE updates SET timesincereview=$newReviewTime";

            // We only set reviewcalculated when the time won't change, i.e.
            // all except the most recent
            if ($i>0) $sql.=",reviewcalculated='true'";
            $sql.=" WHERE id=".$interval[$i]['id'];
            mysql_query($sql);
        }


        // And now SLA.  This is slightly different than above, 
        // as if the status is 'Awaiting Customer Action' then we 
        // do not increase the time counter

        $newSlaTime=0;

        for ($i=$slaStart-1; $i>=0; $i--) {
            if ($interval[$i]['type']=='slamet') $lastTime=0;
            else $lastTime=$interval[$i+1]['timesincesla'];

            // 
            if ($interval[$i]['currentstatus']!=STATUS_CUSTOMER)
                $newSlaTime=$interval[$i]['timesincelastupdate']+$lastTime;
            else
                $newSlaTime=$lastTime;

            $interval[$i]['timesincesla']=$newSlaTime;
            $sql="UPDATE updates SET timesincesla=$newSlaTime";
            if ($i>0) $sql.=",slacalculated='true'";
            $sql.=" WHERE id=".$interval[$i]['id'];
            mysql_query($sql);
        }

        // Get these time of NEXT SLA requirement in minutes
        $coefficient=1;

        switch ($currentSla) {
            case 'opened':          $slaRequest='initial_response_mins'; break;
            case 'initialresponse': $slaRequest='prob_determ_mins';      break;
            case 'probdef':         $slaRequest='action_plan_mins';      break;
            case 'actionplan':      $slaRequest='resolution_days';       $coefficient=($CONFIG['end_working_day']-$CONFIG['start_working_day'])/60; break;
            case 'solution':        $slaRequest='initial_response_mins'; break;
        }

        // Query the database for the next SLA and review times...

        $sql="SELECT ($slaRequest*$coefficient) as 'next_sla_time', review_days from servicelevels WHERE tag='$tag' AND priority='{$incident['priority']}'";
        $result=mysql_query($sql);
        $times=mysql_fetch_assoc($result);
        mysql_free_result($result);
    
        // Check if we have already sent an out of SLA/Review period mail
        // This attribute is reset when an update to the incident meets sla/review time
        if ($incident['slaemail']==0) {
    
            // If not, check if we need to
    
            $emailSent=0;
            // First check SLA
            if ($times['next_sla_time'] < $newSlaTime) {
                if ($verbose) echo "Incident {$incident['id']} out of SLA{$crlf}";
                send_template_email('OUT_OF_SLA',$incident['id'],$tag,$newSlaTime-$times['next_sla_time']);
                $emailSent=1;
            }
    
            if (($times['review_days'] * 24 * 60) < $newReviewTime) {
                if ($verbose) echo "Incident {$incident['id']} out of Review{$crlf}";
                send_template_email('OUT_OF_REVIEW',$incident['id'],"",-1);
                $emailSent=1;
            }
    
            // If we just sent one then update the incident so we don't send another next time
            if ($emailSent) {
                $sql="UPDATE incidents SET slaemail='1' WHERE id='{$incident['id']}'";
                mysql_query($sql);
            }
    
        }

    }

}

mysql_free_result($incident_result);

?>
