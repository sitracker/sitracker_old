<?php
// auto.php - Regular SiT! maintenance tasks (for scheduling)
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

// This file can be called from a cron job to run tasks periodically

require('db_connect.inc.php');
require('functions.inc.php');

$crlf = "\n";

if ($_SERVER['argv'][1]=='-h' OR $_SERVER['argv'][1]=='help' || $_SERVER['argv'][1]=='-?')
{
    echo "-h    Help\n";
    echo "-v    verbose\n";
    echo "Syntax: auto.php <comma-seperate-list-of-actions> <--verbose>|<-v>";
    exit;
}
if ($_SERVER['argv'][1]!='') $actions=$_SERVER['argv'][1];
else
{
    $actions = $_REQUEST['actions'];
    $crlf = "<br />\n";
}

if ($_SERVER['argv'][2]!='')
{
    if ($_SERVER['argv'][2]=='-v' OR $_SERVER['argv'][2]=='--verbose') $verbose = TRUE;
    else $verbose=FALSE;
}
else
{
    if ($_REQUEST['verbose']!='') $verbose = TRUE;
    else $verbose=FALSE;
}

if ($actions=='all') $actions='';
else $actions = explode(',',$actions);

if ($actions[0]=='' OR in_array('CloseIncidents',$actions))
{
    //
    // Select incidents awaiting closure for more than a week where the next action time is not set or has passed
    //
    $sql = "SELECT * FROM incidents WHERE status='7' AND (($now - lastupdated) > '{$CONFIG['closure_delay']}') AND (timeofnextaction='0' OR timeofnextaction<='$now') ";
    $result=mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    if ($verbose) echo "Found ".mysql_num_rows($result)." Incidents to close{$crlf}";
    while ($irow=mysql_fetch_array($result))
    {
        $sqlb="UPDATE incidents SET lastupdated='$now', closed='$now', status='2', closingstatus='4', timeofnextaction='0' WHERE id='".$irow['id']."'";
        $resultb=mysql_query($sqlb);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        if ($verbose) echo "  Incident ".$row['id']." closed{$crlf}";

        $sqlc="INSERT INTO updates (incidentid, userid, type, currentowner, currentstatus, bodytext, timestamp, nextaction, customervisibility) ";
        $sqlc.="VALUES ('".$irow['id']."', '0', 'closing', '".$irow['owner']."', '".$irow['status']."', 'Incident Closed by {$CONFIG['application_shortname']}', '$now', '', 'show' ) ";
        $resultc=mysql_query($sqlc);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    }
}

if ($actions[0]=='' OR in_array('PurgeJournal',$actions))
{
    $purgedate = date('YmdHis',($now - $CONFIG['journal_purge_after']));
    $sql = "DELETE FROM journal WHERE timestamp < $purgedate";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    if ($verbose) echo "Purged ".mysql_affected_rows()." journal entries{$crlf}";
}

if ($actions[0]=='' OR in_array('TimeCalc',$actions))
{
    require('auto/timecalc.php');
}


if ($actions[0]=='' OR in_array('SetUserStatus',$actions))
{
    // Find users with holidays today who don't have correct status
    $startdate=mktime(0,0,0,date('m'),date('d'),date('Y'));
    $enddate=mktime(23,59,59,date('m'),date('d'),date('Y'));
    $sql = "SELECT * FROM holidays ";
    $sql .= "WHERE startdate >= '$startdate' AND startdate < '$enddate' AND (type >='1' AND type <= 5) ";
    $sql .= "AND (approved=1 OR approved=2 OR approved=11 OR approved=12)";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    while ($huser = mysql_fetch_object($result))
    {
        if ($huser->length == 'day'
            OR ($huser->length == 'am' AND date('H') < 12)
            OR ($huser->length == 'pm' AND date('H') < 12))
        {
            $currentstatus = user_status($huser->userid);
            $newstatus = $currentstatus;
            // Only enabled users
            if ($currentstatus > 0)
            {
                if ($huser->type == 1 AND $currentstatus != 5) $newstatus = 5; // Holiday
                if ($huser->type == 2 AND $currentstatus != 8) $newstatus = 8; // Sickness
                if ($huser->type == 3 AND ($currentstatus != 6 AND $currentstatus != 9)) $newstatus = 9; // Work away
                if ($huser->type == 4 AND $currentstatus != 7) $newstatus = 7; // Training
                if ($huser->type == 5 AND ($currentstatus != 2 AND $currentstatus != 8)) $newstatus = 8; // Compassionate
            }
            if ($newstatus != $currentstatus)
            {
                $accepting='';
                switch ($newstatus)
                {
                    case 1: // in office
                        $accepting='Yes';
                    break;

                    case 2: // Not in office
                        $accepting='No';
                    break;

                    case 3: // In Meeting
                        // don't change
                        $accepting='';
                    break;

                    case 4: // At Lunch
                        $accepting='';
                    break;

                    case 5: // On Holiday
                        $accepting='No';
                    break;

                    case 6: // Working from home
                        $accepting='Yes';
                    break;

                    case 7: // On training course
                        $accepting='No';
                    break;

                    case 8: // Absent Sick
                        $accepting='No';
                    break;

                    case 9: // Working Away
                        // don't change
                        $accepting='';
                    break;
                }
                $usql = "UPDATE users SET status='{$newstatus}'";
                if ($accepting!='') $usql .= ", accepting='{$accepting}'";
                $usql .= " WHERE id='{$huser->userid}' LIMIT 1";
                // if ($accepting=='No') incident_backup_switchover($huser->userid, 'no');
                if ($verbose) echo user_realname($huser->userid).': '.userstatus_name($currentstatus).' -> '.userstatus_name($newstatus).$crlf;
                echo $usql.$crlf;
            }
        }
    }
    // Find users who are set away but have no entry in the holiday calendar
    $sql = "SELECT * FROM users WHERE status=5 OR status=7 OR status=8 OR status=9 ";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
}

plugin_do('automata');

?>
