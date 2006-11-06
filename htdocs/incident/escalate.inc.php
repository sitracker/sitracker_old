<?php
// escalate.inc.php - Escalation details of an incident
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>


// Prevent script from being run directly (ie. it must always be included)
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}

if(empty($incidentid)) $incidentid = cleanvar($_REQUEST['id']);

$sql = "SELECT i.escalationpath, i.externalid, i.externalengineer, i.externalemail, e.* ";
$sql .= " FROM incidents i, escalationpaths e WHERE i.escalationpath = e.id AND i.id = '{$incidentid}'";

$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

if(mysql_num_rows($result) == 1)
{
    //incident has been escalated
    $escalated = mysql_fetch_object($result);
    echo "<p align='center'><h2>Escalated to {$escalated->name}</h2></p>";

    echo "<table  summary='Escalation details' align='center' class='vertical'>";
    echo "<tr><th>Escalation ID:</th><td>".format_external_id($escalated->externalid,$escalated->escalationpath)."</td></tr>";
    echo "<tr><th>External engineer:</th><td>{$escalated->externalengineer}</td></tr>";
    echo "<tr><th>External email:</th><td>{$escalated->externalemail}</td></tr>";
    echo "</table>";
}
else
{
    //not escalated
}


?>