<?php
// calendar.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
//         Tom Gerrard <tom.gerrard[at]salfordsoftware.co.uk>

@include('set_include_path.inc.php');
$permission=27; // View your calendar
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');
include('calendar/calendar.inc.php');

// External variables
foreach (array(
              'user', 'nmonth', 'nyear', 'type', 'selectedday', 'selectedmonth',
              'selectedyear', 'selectedtype', 'approved', 'length', 'display',
              'weeknumber'
			  ) as $var)
	eval("\$$var=cleanvar(\$_REQUEST['$var']);");
if (empty($length)) $length='day';
$title = $strCalendar;  //TODO add this
include('htmlheader.inc.php');
// FIXME not sure if stylesheet should go here, check validation.  inl 22 jan 08
echo "<link rel='stylesheet' href='{$CONFIG['application_webpath']}calendar/planner.css.php' />\n";

if (empty($user) || $user=='current') $user=$sit[2];
elseif ($user=='all') $user='';
if (empty($type)) $type=1;
if (user_permission($sit[2],50)) $approver=TRUE; else $approver=FALSE;



// Defaults
if (empty($_REQUEST['year'])) $year=date('Y');
else $year=$_REQUEST['year'];
if (empty($_REQUEST['month'])) $month=date('m');
else $month=$_REQUEST['month'];
if (empty($_REQUEST['day'])) $day=date('d');
else $day=$_REQUEST['day'];
$calendarTypes = array('list','year','month','week','day','timesheet');

// Prevent people from including any old file - this also handles any cases
// where $display == 'chart'
if (!in_array($display, $calendarTypes)) $display = 'month';

// Navigation
echo "<p>{$strDisplay}: ";
foreach($calendarTypes as $navType)
{
    $navHtml[$navType]  = "<a href='{$_SERVER['PHP_SELF']}?display={$navType}";
    $navHtml[$navType] .= "&amp;year={$year}&amp;month={$month}&amp;day={$day}";
    $navHtml[$navType] .= "&amp;type={$type}'>";
    $navi18n = eval('return $str' . ucfirst($navType) . ';');
    if ($display == $navType) $navHtml[$navType] .= '<em>' . $navi18n . '</em>';
    else $navHtml[$navType] .= $navi18n;
    $navHtml[$navType] .= "</a>";
}
echo implode(' | ', $navHtml);
echo "</p>";

include("calendar/{$display}.inc.php");

include('htmlfooter.inc.php');
?>