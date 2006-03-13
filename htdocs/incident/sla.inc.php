<?php
// Included by ../incident.php

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}

$opened_string = date("D jS M Y @ g:i A", $incident["opened"]);
$lastupdated_string = date("D jS M Y @ g:i A", $incident["lastupdated"]);
$now = time();
if ($incident["timeofnextaction"] == 0) $timetonextaction_string = "None";
else
{
    if (($incident["timeofnextaction"] - $now) > 0)
    {
        $timetonextaction_string = format_seconds($incident["timeofnextaction"] - $now);
        $timetonextaction_date = date("D jS M Y @ g:i A", $incident["timeofnextaction"]);
        $timetonextaction_string .= "($timetonextaction_date)";
    }
    else
    {
        $timetonextaction_string = "<span class=\"expired\">Now ";
        $timetonextaction_date = date("D jS M Y @ g:i A", $incident["timeofnextaction"]);
        $timetonextaction_string .= "($timetonextaction_date)</span>";
    }
}
// get service level
//$servicelevel=maintenance_servicelevel($incident['maintenanceid']);

$sql = "SELECT * FROM servicelevels WHERE tag='$servicelevel_tag' AND priority='{$incident->priority}' ";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
$level = mysql_fetch_object($result);

echo "<h2>Service Level History</h2>";
echo "<p align='center'>Current Service Level: {$servicelevel_tag}</p>";
echo "<table align='center'>";
echo "<tr><th>Date &amp; Time</th><th>Event</th><th>Target</th><th>Actual</th></tr>";
echo "<tr><td>".date($CONFIG['dateformat_datetime'], $incident->opened)."</td><td>Incident logged</td><td><em>n/a</em></td><td><em>n/a</em></td></tr>\n";
$sql = "SELECT * FROM updates WHERE type='slamet' AND incidentid='$id' ORDER BY id ASC, timestamp ASC";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
$prevtime=$incident->opened;
while ($history = mysql_fetch_object($result))
{
    echo "<tr><td>".date($CONFIG['dateformat_datetime'],$history->timestamp)."</td>";
    echo "<td>".target_type_name($history->sla)." by ".user_realname($history->userid)."</td>";
    echo "<td>";
    switch ($history->sla)
    {
        case 'initialresponse': echo format_workday_minutes($level->initial_response_mins); break;
        case 'probdef': echo format_workday_minutes($level->prob_determ_mins); break;
        case 'actionplan': echo format_workday_minutes($level->action_plan_mins); break;
        case 'solution': echo "{$level->resolution_days} working days"; break;
        default:
            echo "{$history->sla}";
    }
    echo "</td>";
    $timetaken=working_day_diff($prevtime, $history->timestamp);
    echo "<td>".format_workday_minutes($timetaken / 60)."</td></tr>";
    $prevtime=$history->timestamp;
}


//echo "<tr><th>Service Level:</th><td>".servicelevel_name($servicelevel)."</td></tr>";

echo "<tr><th colspan='2'></td></tr>\n";


/*
     echo "<tr><td align='right' class='shade1' width='150'><b>Resolution</b>:</td><td class='shade2' width='300'> within {$level->resolution_days} working days</td></tr>\n";

     echo "<tr><td align='center' class='shade1' colspan='2'><strong>Actual Service Level History:</strong></td></tr>\n";




*/
?>
