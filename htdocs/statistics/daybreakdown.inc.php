<?php
// daybreakdown.inc.php - Displays the incident breakdown for a day
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>


// Included by ../statistics.php

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}


switch($offset)
{
    case 0: $dayString='Today';
        break;
    case 1: $dayString='Yesterday';
        break;
    default:
        $dayString = date("l", mktime(0,0,0,date('m'),date('d')-$offset,date('Y')));
        break;
}

echo "<h2>Statistics from {$dayString}</h2>";

echo "<table align='center'>";
echo "<tr><th>Period</th><th>Opened</th><th>Updated</th><th>Closed</th><th>Handled</th><th>Updates</th><th>per incident</th><th>Skills</th><th>Owners</th><th>Users</th><th>upd per user</th><th>inc per owner</th><th>Email Rx</th><th>Email Tx</th><th>Higher Priority</th><th>Activity</th></tr>\n";

echo stats_period_row($dayString, mktime(0,0,0,date('m'),date('d')-$offset,date('Y')),mktime(23,59,59,date('m'),date('d')-$offset,date('Y')));

echo "</table>";

?>