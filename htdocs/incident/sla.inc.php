<?php
// sla.inc.php - Displays a summary service level history of the incident
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Included by ../incident.php

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}

echo "<h2>Service Level History</h2>";
echo "<p align='center'>Current Service Level: {$servicelevel_tag}</p>";

// Create an array containing the service level history
$slahistory = incident_sla_history($incidentid);

if (count($slahistory) >= 1)
{
    echo "<table align='center'>";
    echo "<tr><th>Event</th><th>User</th><th>Target</th><th>Actual</th><th>Date &amp; Time</th></tr>\n";
    foreach($slahistory AS $history)
    {
        if ($history['targetmet']==FALSE) $class='critical';
        else $class='shade2';
        echo "<tr class='$class'>";
        echo "<td>";
        echo "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/{$slatypes[$history['targetsla']]['icon']}' width='16' height='16' alt='' /> ";
        echo target_type_name($history['targetsla'])."</td>";
        echo "<td>";
        if (!empty($history['userid'])) echo user_realname($history['userid'],TRUE);
        echo "</td>";
        echo "<td>".format_workday_minutes($history['targettime'])."</td>";
        echo "<td>".format_workday_minutes($history['actualtime'])."</td>";
        echo "<td>";
        if ($history['timestamp'] > 0) echo date($CONFIG['dateformat_datetime'],$history['timestamp']);
        echo "</td>";
    }
    echo "</table>\n";
}
else echo "<p align='center'>There is no history to display.<p>";

?>
