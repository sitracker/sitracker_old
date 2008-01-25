<?php
// timecalc.php - Calculate SLA times
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
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

$sql = "SELECT id, title, maintenanceid, priority, slaemail, slanotice, servicelevel, status, owner ";
$sql .= "FROM `{$dbIncidents}` WHERE status != ".STATUS_CLOSED." AND status != ".STATUS_CLOSING;
$incident_result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

while ($incident=mysql_fetch_array($incident_result)) {

    $now=time();

    // Get the service level timings for this class of incident, we may have one
    // from the incident itself, otherwise look at contract type
    if ($incident['servicelevel']=="")
    {
        $sql = "SELECT tag FROM `{$dbServiceLevels}` s, `{$dbMaintenance}` m ";
        $sql .= "WHERE m.id = '{$incident['maintenanceid']}' AND s.id = m.servicelevelid";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        $t = mysql_fetch_row($sql);
        $tag = $t[0];
        mysql_free_result($result);
    }
    else $tag=$incident['servicelevel'];

    if ($verbose) echo $incident['id']." is a $tag incident{$crlf}";

    $newReviewTime=-1;
    $newSlaTime=-1;

    $sql= "SELECT id, type, sla, timestamp, currentstatus FROM `{$dbUpdates}` WHERE incidentid='{$incident['id']}' ";
    $sql.="AND type = 'slamet' ORDER BY id DESC LIMIT 1";
    $update_result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

    if (mysql_num_rows($update_result)!=1)
    {
        if ($verbose) echo "Cannot find SLA information for incident ".$incident['id'].", skipping{$crlf}";
    }
    else
    {
        $slaInfo = mysql_fetch_array($update_result);
        $newSlaTime = calculate_incident_working_time($incident['id'],$slaInfo['timestamp'],$now);
        if ($verbose) echo "   Last SLA record is ".$slaInfo['sla']." at ".date("jS F Y H:i",$slaInfo['timestamp'])." which is $newSlaTime working minutes ago{$crlf}";
    }
    mysql_free_result($update_result);

    $sql = "SELECT id, type, sla, timestamp, currentstatus, currentowner FROM `{$dbUpdates}` WHERE incidentid='{$incident['id']}' ";
    $sql .= "AND type='reviewmet' ORDER BY id DESC LIMIT 1";
    $update_result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

    if (mysql_num_rows($update_result) !=1)
    {
        if ($verbose) echo "   Cannot find review information for incident ".$incident['id'].", skipping{$crlf}";
    }
    else
    {
        $reviewInfo=mysql_fetch_array($update_result);
        $newReviewTime=floor($now-$reviewInfo['timestamp'])/60;
        if ($verbose)
        {
            if ($reviewInfo['currentowner'] != 0) echo "   There has been no review on this incident, which was opened $newReviewTime minutes ago{$crlf}";
            else echo "   The last review took place $newReviewTime minutes ago{$crlf}";
        }
    }
    mysql_free_result($update_result);


    if ($newSlaTime!=-1)
    {
        // Get these time of NEXT SLA requirement in minutes
        $coefficient=1;

        switch ($slaInfo['sla'])
        {
            case 'opened':          $slaRequest='initial_response_mins'; break;
            case 'initialresponse': $slaRequest='prob_determ_mins';      break;
            case 'probdef':         $slaRequest='action_plan_mins';      break;
            case 'actionplan':      $slaRequest='resolution_days';       $coefficient=($CONFIG['end_working_day']-$CONFIG['start_working_day'])/60; break;
            case 'solution':        $slaRequest='initial_response_mins'; break;
        }

        // Query the database for the next SLA and review times...

        $sql = "SELECT ($slaRequest*$coefficient) as 'next_sla_time', review_days ";
        $sql .= "FROM `{$dbServiceLevels}` WHERE tag = '$tag' AND priority = '{$incident['priority']}'";
        $result=mysql_query($sql);
        $times=mysql_fetch_assoc($result);
        mysql_free_result($result);

        if ($verbose)
        {
            echo "   The next SLA target should be met in ".$times['next_sla_time']." minutes{$crlf}";
            echo "   Reviews need to be made every ".($times['review_days']*24*60)." minutes{$crlf}";
        }

        if ($incident['slanotice']==0)
        {
            //reaching SLA
            if ($times['next_sla_time'] > 0) $reach = $newSlaTime / $times['next_sla_time'];
            else $reach = 0;
            if ($reach >= ($CONFIG['urgent_threshold'] * 0.01))
            {
                $timetil = $times['next_sla_time']-$newSlaTime;

                trigger("TRIGGER_INCIDENT_NEARING_SLA", array('incidentid' => $incident['id'], 'nextslatime' => $times['next_sla_time']));
            }
        }
    }
}

mysql_free_result($incident_result);

?>
