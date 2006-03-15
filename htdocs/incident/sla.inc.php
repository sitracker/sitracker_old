<?php
// Included by ../incident.php

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}

echo "<h2>Service Level History</h2>";
echo "<p align='center'>Current Service Level: {$servicelevel_tag}</p>";

// Create an object containing the service level history
$slahistory = incident_sla_history($incidentid);

echo "<table align='center'>";
echo "<tr><th>Event</th><th>User</th><th>Target</th><th>Actual</th><th>Date &amp; Time</th></tr>\n";
foreach($slahistory AS $history)
{
    echo "<tr class='shade2'>";
    echo "<td>".target_type_name($history['targetsla'])."</td>";
    echo "<td>";
    if (!empty($history['userid'])) echo user_realname($history['userid']);
    echo "</td>";
    echo "<td>".format_workday_minutes($history['targettime'])."</td>";
    echo "<td>".format_workday_minutes($history['actualtime'])."</td>";
    echo "<td>";
    if ($history['timestamp'] > 0) echo date($CONFIG['dateformat_datetime'],$history['timestamp']);
    echo "</td>";
}
echo "</table>\n";
?>