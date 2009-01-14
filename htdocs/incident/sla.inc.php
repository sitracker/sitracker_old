<?php
// sla.inc.php - Displays a summary service level history of the incident
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
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

echo "<h2>".icon('sla', 32)." ";
echo "{$strServiceHistory}</h2>";
echo "<p align='center'>{$strServiceLevel}: {$servicelevel_tag}</p>";

// Create an array containing the service level history
$slahistory = incident_sla_history($incidentid);

if (count($slahistory) >= 1)
{
    echo "<table align='center'>";
    echo "<tr><th>{$strEvent}</th><th>{$strUser}</th><th>{$strTarget}</th><th>{$strActual}</th><th>{$strDateAndTime}</th></tr>\n";
    foreach ($slahistory AS $history)
    {
        if ($history['targetmet'] == FALSE) $class='critical';
        else $class='shade2';
        echo "<tr class='$class'>";
        echo "<td>";
        echo icon($slatypes[$history['targetsla']]['icon'], 16)." ";
        echo target_type_name($history['targetsla'])."</td>";
        echo "<td>";
        if (!empty($history['userid'])) echo user_realname($history['userid'],TRUE);
        echo "</td>";
        echo "<td>".format_workday_minutes($history['targettime'])."</td>";
        echo "<td>";
        if ($history['timestamp'] == 0) echo "<em>";
        echo format_workday_minutes($history['actualtime']);
        if ($history['timestamp'] == 0) echo "</em>";
        echo "</td>";
        echo "<td>";
        if ($history['timestamp'] > 0) echo ldate($CONFIG['dateformat_datetime'],$history['timestamp']);
        echo "</td>";
    }
    echo "</table>\n";
}
else echo "<p align='center'>There is no history to display.<p>";

//start status summary
$sql = "SELECT u.id AS updatesid, incidentid, userid, type, timestamp, currentstatus, is.id, is.name AS name ";
$sql .= "FROM `{$dbUpdates}` AS u, `{$dbIncidentStatus}` AS `is` ";
$sql .= " WHERE incidentid = '{$incidentid}' ";
$sql .= " AND u.currentstatus = is.id ";
$sql .= " ORDER BY timestamp ASC";

$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(), E_USER_WARNING);

if (mysql_num_rows($result) > 0)
{
    $updatearray = array();
    $last = -1;
    $laststatus;
    while ($row = mysql_fetch_object($result))
    {
        $updatearray[$row->currentstatus]['name'] = $row->name;
        if ($last == -1)
        {
            $updatearray[$row->currentstatus]['time'] = 0;
        }
        else
        {
            $updatearray[$laststatus]['time'] += 60 * calculate_incident_working_time($row->incidentid, $last, $row->timestamp, array(2,7));
        }

        $laststatus = $row->currentstatus;
        $last = $row->timestamp;
    }

    if ($incident->status == 7 OR $incident->status == 2) $end = $incident->closed;
    else $end = $now;

    $publicholidays = get_public_holidays($incident->opened, $end);

    //calculate the last update
    $updatearray[$laststatus]['time'] += 60 * calculate_working_time($last, time(), $publicholidays);
    echo "<h3>{$strStatusSummary}</h3>";
    if (extension_loaded('gd'))
    {
        $data = array();
        $legends;
        foreach ($updatearray as $row)
        {
            array_push($data, $row['time']);
            $legends .= $GLOBALS[$row['name']]."|";
        }
        $data = implode('|',$data);
        $title = urlencode($strStatusSummary);
        echo "<div style='text-align:center;'>";
        echo "<img src='chart.php?type=pie&data=$data&legends=$legends&title=$title&unit=seconds' />";
        echo "</div>";
    }
    else
    {
        echo "<table align='center'>";
        echo "<tr><th>{$strStatus}</th><th>{$strTime}</th></tr>\n";
        foreach ($updatearray as $row)
        {
            echo "<tr><td>".$row['name']. "</td><td>".format_seconds($row['time'])."</td></tr>";
        }
        echo '</table>';
    }
}
?>
