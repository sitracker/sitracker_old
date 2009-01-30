<?php
// incident_graph.php - Shows incidents opened and closed each day over twelve months
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

@include ('../set_include_path.inc.php');
$permission = 37; // Run Reports
require ($lib_path.'db_connect.inc.php');
require ($lib_path.'functions.inc.php');

// This page requires authentication
require ($lib_path.'auth.inc.php');

// External variables
$startyear = cleanvar($_REQUEST['startyear']);


$title = "Incident Graph";

$openedcolour = '#FF962A';
$closedcolour = '#72B8B8';
$currentcolour = '#1CA772';

$currentyear = date('Y');
include ('./inc/htmlheader.inc.php');
$currentyear = date('Y');
$currentmonth = date('n');
$daysinyear = date('z',mktime(0,0,0,12,31,$year));
flush();

echo "<table summary='Graph' align='center' style='border: 1px solid #000;' width='250'>";
if (empty($startyear))
{
    $startyear = $currentyear;
    $lastyear = $currentyear+1;
}

if (empty($startmonth))
{
    $startmonth = 1;
    $lastyear = $startyear+1;
}
else
{
    $lastyear=$startyear+2;
}

if ($startyear == $currentyear)
{
    $lastmonth = $currentmonth;
}
else
{
    $lastmonth = 12;
}

echo "<h2>Incidents <span style='color: {$openedcolour};'>Opened</span> and <span style='color: {$closedcolour};'>Closed</span> each month</h2>";
echo "<p align='center'>This report shows how many incidents where opened each day.  Hover your mouse over each bar to see the daily figures.<br />";
echo "Compare: <a href='{$_SERVER['PHP_SELF']}?startyear=".($currentyear-2)."'>".($currentyear-2)."</a> | ";
echo "<a href='{$_SERVER['PHP_SELF']}?startyear=".($currentyear-1)."'>".($currentyear-1)."</a> | ";
echo "<a href='{$_SERVER['PHP_SELF']}?startyear=".($currentyear)."'>".($currentyear)."</a>";
echo "</p>";

// If we're starting part way through a year, we need to loop years to ensure we do up to the same time next year
for ($year = $startyear; $year < $lastyear; $year++)
{
    // loop through years
    $grandtotal = 0;
    for ($month = $startmonth; $month <= $lastmonth; $month++)
    {
        // loop through months
        $monthname = date('F',mktime(0,0,0,$month,1,$year));
        $daysinmonth = date('t',mktime(0,0,0,$month,1,$year));
        $colspan = ($daysinmonth*2)+1;  // have to calculate number of cols since ie doesn't seem to do colspan=0
        echo "<tr><td align=\"center\" colspan=\"$colspan\"><h2><a href='{$_SERVER['PHP_SELF']}?startyear=$year&startmonth=$month'>$monthname $year</a></h2></td></tr>\n";
        echo "<tr align=\"center\">";
        echo "<td><img src=\"graph_scale.jpg\" width=\"11\" height=\"279\" alt=\"Graph Scale\"></td>";
        $monthtotal = 0;
        $monthtotalclosed = 0;
        // loop through days
        for ($day = 1; $day <= $daysinmonth; $day++)
        {
            $countdayincidents = countdayincidents($day, $month, $year);
            // not needed $countdaycurrentincidents=countdaycurrentincidents($day, $month, $year);
            $countdayclosedincidents = countdayclosedincidents($day, $month, $year);
            echo "<td valign='bottom' >";
            if ($countdayincidents > 0)
            {
                $height = $countdayincidents*4;
                echo "<div style='cursor: help; height: {$height}px; width: 5px; background-color: {$openedcolour};' title={'$countdayincidents} Incidents Opened on {$day} {$monthname} {$year}'>&nbsp;</div>";
                // echo "<img src=\"/images/vertgraph.gif\" width=\"12\" height=\"$height\" alt=\"$countdayincidents Incidents\" title=\"$countdayincidents Incidents\">";
                $monthtotal += $countdayincidents;
            }
            echo "</td>";

            /*
            current not really needed, slow and looks pretty static
            $currentheight=$countdaycurrentincidents/4;
            $monthtotalcurrent+=$countdaycurrentincidents;
            echo "<td valign=\"bottom\" >";
            if ($countdaycurrentincidents>0)  echo "<div style='cursor: help; height: {$currentheight}px;  width: 5px; background-color: $currentcolour;' title='$countdaycurrentincidents Incidents current on $day $monthname $year'>&nbsp;</div>";
            echo "</td>";
            */

            $closedheight = $countdayclosedincidents*4;
            $monthtotalclosed += $countdayclosedincidents;
            echo "<td valign=\"bottom\" >";
            if ($countdayclosedincidents > 0)
            {
                echo "<div style='cursor: help; height: {$closedheight}px;  width: 5px; background-color: $closedcolour;' title='$countdayclosedincidents Incidents Closed on $day $monthname $year'>&nbsp;</div>";
            }
            echo "</td>";
        }
        echo "</tr>\n";
        echo "<tr><td>&nbsp;</td>";
        for ($day = 1; $day <= $daysinmonth; $day++)
        {
            echo "<td colspan='2' align='center'>$day</td>";
        }
        echo "</tr>\n";
        $grandtotal += $monthtotal;
        $grandtotalclosed += $monthtotalclosed;

        $diff = ($monthtotal - $monthtotalclosed);
        
        if ($diff < 0)
        {
            $diff = "<span style='color: $closedcolour;'>$diff</span>";
        }
        else
        {
            $diff="<span style='color: $openedcolour;'>$diff</span>";
        }
        
        echo "<tr><td align=\"center\" colspan=\"$colspan\" style='border-bottom: 2px solid #000;'>";
        echo "<p>{$strTotal}: <b style='color: $openedcolour;'>$monthtotal</b>";
        echo "opened and <b style='color: $closedcolour;'>$monthtotalclosed</b> closed during $monthname $year, difference: <b>$diff</b><br />";
        
        $diff = ($grandtotal-$grandtotalclosed);
        
        if ($diff < 0)
        {
            $diff = "<span style='color: $closedcolour;'>$diff</span>";
        }
        else
        {
            $diff="<span style='color: $openedcolour;'>$diff</span>";
        }
        
        echo "{$strTotal}: <b style='color: $openedcolour;'>$grandtotal</b> opened and <b style='color: $closedcolour;'>$grandtotalclosed</b> closed up to the end of $monthname $year, difference <b>$diff</b></p><br /></td></tr>\n";
    }
    if ($startmonth > 1)
    {
        $lastmonth=$startmonth-1;
        $startmonth=1;
    }
}
echo "</table>\n\n";
$diff = ($grandtotal - $grandtotalclosed);

if ($diff < 0)
{
    $diff = "<span style='color: $closedcolour;'>$diff</span>";
}
else
{
    $diff="<span style='color: $openedcolour;'>$diff</span>";
}

echo "<h3>Grand Total: <u style='color: $openedcolour;'>$grandtotal</u> incidents opened and <u style='color: $closedcolour;'>$grandtotalclosed</u> closed during the year, difference <u>$diff</u></h3>";

include ('./inc/htmlfooter.inc.php');
?>
