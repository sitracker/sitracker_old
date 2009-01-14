<?php
// planner_schedule_getitems.php - read event tasks and output XML
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Tom Gerrard <tom.gerrard[at]salfordsoftware.co.uk>

@include ('../set_include_path.inc.php');
$permission = 27; // View your calendar FIXME
require ('db_connect.inc.php');
require ('functions.inc.php');
require ('auth.inc.php');

include ('calendar.inc.php');

foreach (array('year', 'month', 'day', 'user') as $var)
    eval("\$$var=cleanvar(\$_REQUEST['$var']);");

header('Content-Type: text/xml');
echo '<?xml version="1.0" ?>' . "\n";

$items = array();

$startOfWeek = mktime(0, 0, 0, $month, $day, $year);
$endOfWeek = $startOfWeek + 86400 * 7;
$items = get_users_appointments($user, $startOfWeek, $endOfWeek);

foreach ($items as $item)
{
    echo "<item>\n";
    foreach ($item as $key => $value)
    {
        echo "  <$key>$value</$key>\n";
    }
    echo "</item>\n";
}

?>
