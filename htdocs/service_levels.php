<?php
// service_levels.php - Displays current service level settings
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

$permission=22; // Administrate
$title = 'Service Levels';

require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

include('htmlheader.inc.php');

echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/sla.png' width='32' height='32' alt='' /> ";
echo "$title</h2>";

echo "<p align='center'><a href='add_service_level.php'>Add a service level</a></p>";

$tsql = "SELECT DISTINCT * FROM servicelevels GROUP BY tag";
$tresult = mysql_query($tsql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
if (mysql_num_rows($tresult) >= 1)
{
    echo "<table align='center'>";
    while ($tag = mysql_fetch_object($tresult))
    {
        echo "<thead><tr><th colspan='8'>{$tag->tag}</th></tr></thead>";
        $sql = "SELECT * FROM servicelevels WHERE tag='{$tag->tag}' ORDER BY priority";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

        echo "<tr><th colspan='2'>Priority</th><th>Initial Response</th>";
        echo "<th>Problem Determination</th><th>Action Plan</th><th>Resolution</th>";
        echo "<th>Review</th><th></th></tr>";
        while ($sla = mysql_fetch_object($result))
        {
            echo "<tr>";
            echo "<td align='right'>".priority_icon($sla->priority)."</td><td>".priority_name($sla->priority)."</td>";
            echo "<td>".format_workday_minutes($sla->initial_response_mins)."</td>";
            echo "<td>".format_workday_minutes($sla->prob_determ_mins)."</td>";
            echo "<td>".format_workday_minutes($sla->action_plan_mins)."</td>";
            echo "<td>".round($sla->resolution_days)." working days</td>"; // why is this a float?
            echo "<td>{$sla->review_days} days</td>";
            echo "<td><a href='edit_service_level.php?tag={$sla->tag}&amp;priority={$sla->priority}'>Edit</a></th>";
            echo "</tr>\n";
        }
    }
    echo "</table>";
    }
    else echo "<p class='error'>No service levels defined.</p>";
    include('htmlfooter.inc.php');
?>
