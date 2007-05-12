<?php
// auto.php - Regular SiT! maintenance tasks (for scheduling)
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
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

plugin_do('automata');

?>
