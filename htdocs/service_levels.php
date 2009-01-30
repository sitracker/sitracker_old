<?php
// service_levels.php - Displays current service level settings
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

@include ('set_include_path.inc.php');
$permission = 22; // Administrate
require ($lib_path.'db_connect.inc.php');
require ($lib_path.'functions.inc.php');

// This page requires authentication
require ($lib_path.'auth.inc.php');

include ('./inc/htmlheader.inc.php');

$title = $strServiceLevels;

echo "<h2>".icon('sla', 32)." {$title}</h2>";

echo "<p align='center'><a href='service_level_add.php'>{$strAddServiceLevel}</a></p>";

$tsql = "SELECT DISTINCT * FROM `{$dbServiceLevels}` GROUP BY tag";
$tresult = mysql_query($tsql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
if (mysql_num_rows($tresult) >= 1)
{
    echo "<table align='center'>";
    while ($tag = mysql_fetch_object($tresult))
    {
        echo "<thead><tr><th colspan='9'>{$tag->tag}</th></tr></thead>";
        $sql = "SELECT * FROM `{$dbServiceLevels}` WHERE tag='{$tag->tag}' ORDER BY priority";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

        echo "<tr><th colspan='2'>{$strPriority}</th><th>{$strInitialResponse}</th>";
        echo "<th>{$strProblemDefinition}</th><th>{$strActionPlan}</th><th>{$strResolutionReprioritisation}</th>";
        echo "<th>{$strReview}</th><th>{$strTimed}</th><th>{$strOperation}</th></tr>";
        while ($sla = mysql_fetch_object($result))
        {
            echo "<tr>";
            echo "<td align='right'>".priority_icon($sla->priority)."</td>";
            echo "<td>".priority_name($sla->priority)."</td>";
            echo "<td>".format_workday_minutes($sla->initial_response_mins)."</td>";
            echo "<td>".format_workday_minutes($sla->prob_determ_mins)."</td>";
            echo "<td>".format_workday_minutes($sla->action_plan_mins)."</td>";
            // 480 mins in a working day
            echo "<td>".format_workday_minutes($sla->resolution_days*480)."</td>";
            echo "<td>".sprintf($strXDays, $sla->review_days)."</td>";
            if ($sla->timed == 'yes')
            {
                echo "<td>{$strYes}</td>";
            }
            else echo "<td>{$strNo}</td>";
            echo "<td><a href='edit_service_level.php?tag={$sla->tag}&amp;priority={$sla->priority}'>{$strEdit}</a></td>";
            echo "</tr>\n";
        }
    }
    echo "</table>";
}
else
{
    echo "<p class='error'>{$strNoRecords}</p>";
}

include ('./inc/htmlfooter.inc.php');
?>
