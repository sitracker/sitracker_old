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

if ($verbose) echo "Calculating SLA times{$crlf}";

$sql="SELECT id,maintenanceid,priority,slaemail,servicelevel,status FROM incidents WHERE status != ".STATUS_CLOSED;
//$sql="SELECT id,maintenanceid,priority,slaemail,servicelevel,status FROM incidents WHERE id=32138";
$incident_result=mysql_query($sql);

while ($incident=mysql_fetch_array($incident_result)) {

    $now=time();

    // Get the service level timings for this class of incident, we may have one
    // from the incident itself, otherwise look at contract type
    if ($incident['servicelevel']=="") {
    $sql="SELECT tag FROM servicelevel, maintenance WHERE maintenance.id='{$incident['maintenanceid']}' AND servicelevel.id=maintenance.servicelevelid";
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

    while ($update=mysql_fetch_array($update_result)) {
    // Read (backwards) through the updates to find the most recent sla and review information
    $interval[] = $update;
    end($interval);

    // Find the most recent already calculated if one exists
    if ( ($slaStart==0) && ($update['slacalculated']=='true') ) $slaStart=key($interval);
    if ( ($reviewStart==0) && (($update['reviewcalculated']=='true') || ($update['type']=='reviewmet')) ) $reviewStart=key($interval);

    // Get the last update that met an SLA.  (there should always be at least one)
    if ( ($currentSla=="") && ($update['sla']!="") ) {
        $currentSla=$update['sla'];

        // If we haven't already found the last calculated entry first then this one takes precendence over previous entries
        if ($slaStart==0) $slaStart=key($interval);
    }

    // If we have all we need then we can carry on
    if ( ($reviewStart!="") && ($currentSla!="") ) break;
    }
    mysql_free_result($update_result);

    if ($currentSla=="") {
    // We have a problem, or SLAs are turned off, bail out of this one
    if ($verbose) echo "Cannot find SLA information for incident ".$incident['id'].", skipping{$crlf}";
    } else {

        // We need to calculate the working time for both review and SLA, so we may as well do it in one go
        for ($i=count($interval)-2; $i>0; $i--)
            $interval[$i]['timesincelastupdate']=calculate_working_time($interval[$i+1]['timestamp'],$interval[$i]['timestamp']);
        $interval[0]['timesincelastupdate']=calculate_working_time($interval[0]['timestamp'],$now);

    // If there have been a few updates since the last run we need to update them historically
    // First do review ...
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


    // And now SLA - this is pretty much the same as above
    for ($i=$slaStart-1; $i>=0; $i--) {
        if ($interval[$i]['type']=='slamet') $lastTime=0;
        else $lastTime=$interval[$i+1]['timesincesla'];

    // if we are waiting for the customer it doesn't count
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