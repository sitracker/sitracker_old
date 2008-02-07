<?php
// scheduler.php -
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

@include ('set_include_path.inc.php');
$permission = 19; // View Contracts

require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

include ('htmlheader.inc.php');

echo "<p>This page will, in future, allow setting up scheduled actions, it does nothing at the moment.</p>";
// TODO complete the scheduler gui

$sql = "SELECT * FROM `{$dbScheduler}` ORDER BY action";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);

if (mysql_num_rows($result) >= 1)
{
    echo "<table align='center'>";
    echo "<tr><th>{$strAction}</th><th>{$strStartDate}</th><th>{$strInterval}</th><th>{$strEndDate}</th><th>{$strLastRan}</th></tr>\n";
    while ($schedule = mysql_fetch_object($result))
    {
        echo "<tr>";
        echo "<td>{$schedule->action}</td>";
        echo "<td>{$schedule->start}</td>";
        echo "<td>{$schedule->interval}</td>";
        echo "<td>{$schedule->end}</td>";
        echo "<td>{$schedule->lastran}</td>";
        echo "</tr>";
    }
    echo "</table>\n";


}


include ('htmlfooter.inc.php');
?>