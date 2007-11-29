<?php
// timecalc.php - Calculate SLA times
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
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

define("STATUS_CLOSING",7);
define("STATUS_CLOSED",2);
define("STATUS_CUSTOMER",8);

// FIXME this should only run INSIDE the working day
// FIXME ? this will not update the database fully if two SLAs have been met since last run - does it matter ?

if ($verbose) echo "Calculating SLA times{$crlf}";

$sql="SELECT id,title,maintenanceid,priority,slaemail,slanotice,servicelevel,status,owner FROM incidents WHERE status != ".STATUS_CLOSED." AND status != ".STATUS_CLOSING;
//$sql="SELECT id,maintenanceid,priority,slaemail,servicelevel,status FROM incidents WHERE id=34833";
$incident_result=mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

while ($incident=mysql_fetch_array($incident_result)) {

    $now=time();

    // Get the service level timings for this class of incident, we may have one
    // from the incident itself, otherwise look at contract type
    if ($incident['servicelevel']=="") {
        $sql="SELECT tag FROM servicelevels, maintenance WHERE maintenance.id='{$incident['maintenanceid']}' AND servicelevels.id=maintenance.servicelevelid";
        $result=mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        $t=mysql_fetch_row($sql);
        $tag=$t[0];
        mysql_free_result($result);
    } else $tag=$incident['servicelevel'];

    if ($verbose) echo $incident['id']." is a $tag incident{$crlf}";

    $newReviewTime=-1;
    $newSlaTime=-1;

    $sql= "SELECT id, type, sla, timestamp, currentstatus FROM updates WHERE incidentid='{$incident['id']}' ";
    $sql.="AND type='slamet' ORDER BY id DESC LIMIT 1";
    $update_result=mysql_query($sql);

    if (mysql_num_rows($update_result)!=1) {
        if ($verbose) echo "Cannot find SLA information for incident ".$incident['id'].", skipping{$crlf}";
    } else {
        $slaInfo=mysql_fetch_array($update_result);
        $newSlaTime=calculate_incident_working_time($incident['id'],$slaInfo['timestamp'],$now);
        if ($verbose) echo "   Last SLA record is ".$slaInfo['sla']." at ".date("jS F Y H:i",$slaInfo['timestamp'])." which is $newSlaTime working minutes ago{$crlf}";

    }
    mysql_free_result($update_result);

    $sql= "SELECT id, type, sla, timestamp, currentstatus, currentowner FROM updates WHERE incidentid='{$incident['id']}' ";
    $sql.="AND type='reviewmet' ORDER BY id DESC LIMIT 1";
    $update_result=mysql_query($sql);

    if (mysql_num_rows($update_result)!=1) {
        if ($verbose) echo "   Cannot find review information for incident ".$incident['id'].", skipping{$crlf}";
    } else {

        $reviewInfo=mysql_fetch_array($update_result);
        $newReviewTime=floor($now-$reviewInfo['timestamp'])/60;
        if ($verbose) {
            if ($reviewInfo['currentowner']!=0) echo "   There has been no review on this incident, which was opened $newReviewTime minutes ago{$crlf}";
            else echo "   The last review took place $newReviewTime minutes ago{$crlf}";
        }

    }
    mysql_free_result($update_result);


    if ($newSlaTime!=-1) {

        // Get these time of NEXT SLA requirement in minutes
        $coefficient=1;

        switch ($slaInfo['sla']) {
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

        if ($verbose) {
            echo "   The next SLA target should be met in ".$times['next_sla_time']." minutes{$crlf}";
            echo "   Reviews need to be made every ".($times['review_days']*24*60)." minutes{$crlf}";
        }

        if($incident['slanotice']==0)
        {
            //reaching SLA
            if ($times['next_sla_time'] > 0) $reach = $newSlaTime / $times['next_sla_time'];
            else $reach = 0;
            if($reach >= ($CONFIG['urgent_threshold'] * 0.01))
            {
                //create notice, workaround until triggers are implemented - KMH 26/11/07
                $timetil = $times['next_sla_time']-$newSlaTime;

                $sql = "INSERT into notices(userid, type, text, linktext, link, referenceid, timestamp) ";

                if($timetil >= 0)
                {
                    $text = "will exceed its SLA soon";
                    $sql .= "VALUES({$incident['owner']}, {$CONFIG['NEARING_SLA_TYPE']}, 'Incident {$incident['id']} - \'{$incident['title']}\' $text', 'View Incident', 'javascript:incident_details_window(\'{$incident['id']}\',\'incident{$incident['id']}\')', {$incident['id']}, NOW())";
                }
                elseif($timetil < 0)
                {
                    $text = "has exceeded its SLA";
                    $sql .= "VALUES({$incident['owner']}, {$CONFIG['OUT_OF_SLA_TYPE']}, 'Incident {$incident['id']} - \'{$incident['title']}\' $text', 'View Incident', 'javascript:incident_details_window(\'{$incident['id']}\',\'incident{$incident['id']}\')', {$incident['id']}, NOW())";
                }
                echo $sql;
                mysql_query($sql);
                if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

                $sql="UPDATE incidents SET slanotice='1' WHERE id='{$incident['id']}'";
                mysql_query($sql);
                if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
            }
        }

        // Check if we have already sent an out of SLA/Review period mail
        // This attribute is reset when an update to the incident meets sla/review time
        if ($incident['slaemail']==0) {
           
        $emailSent=0;
            // First check SLA
            if ($times['next_sla_time'] < ($newSlaTime*.01*$CONFIG['urgent_threshold']) ) {
                //send_template_email('OUT_OF_SLA',$incident['id'],$tag,$times['next_sla_time']-$newSlaTime);
                $sql = "INSERT into notices(text, linktext, link, timestamp) ";
                $sql .= "VALUES('Incident {$incident['id']} is about to go out of sla', 'View Incident', '', NOW())";
                mysql_query($sql);
                $noticeid = mysql_insert_id();

                $sql = "INSERT into usernotices(noticeid, userid) ";
                $sql .= "VALUES($noticeid, {$reviewInfo['currentowner']})";
                mysql_query($sql);

                $emailSent=1;
            }

            /*if (($times['review_days'] * 24 * 60) < ($newReviewTime) ) {
                if ($verbose) echo "   Incident {$incident['id']} out of Review{$crlf}";
                send_template_email('OUT_OF_REVIEW',$incident['id'],"",-1);
                $emailSent=1;
            }*/

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
