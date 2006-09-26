<?php
// incidents_by_software.php - List the number of incidents for each software
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Authors: Paul Heaney <paulheaney[at]users.sourceforge.net>

// Requested by Tech Support team (26 Spet 06)

// Notes:
//  Counts activate calls withing the specified period (i.e. those with a lastupdate time > timespecified)

$permission=37; // Run Reports
$title='Incidents by Software';
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');


if (empty($_REQUEST['mode']))
{
    include('htmlheader.inc.php');

    echo "<h2>$title</h2>";
    echo "<form action='{$_SERVER['PHP_SELF']}' id='incidentsbysoftware' method='post'>";
    echo "<table class='vertical'>";
    echo "<tr><td class='shade2'>Start Date:</td>";
    echo "<td class='shade2'><input type='text' name='startdate' id='startdate' size='10' /> ";
    echo date_picker('incidentsbysoftware.startdate');
    echo "</td></tr>";
    echo "</table>";
    echo "<p align='center'>";
    echo "<input type='hidden' name='mode' value='report' />";
    echo "<input type='submit' value='report' />";
    echo "</p>";
    echo "</form>";

    include('htmlfooter.inc.php');
}
else
{
    $startdate = strtotime($_REQUEST['startdate']);
    $sql = "SELECT count(software.id), software.name ";
    $sql .= "FROM software, incidents ";
    $sql .= "WHERE software.id = incidents.softwareid AND incidents.lastupdated > '{$startdate}' ";
    $sql .= "GROUP BY software.id";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    $countArray[0]=0;
    $softwareNames[0]='Name';
    $c = 0;
    $count = 0;
    while($row = mysql_fetch_array($result))
    {
        $countArray[$c] = $row['count(software.id)'];
        $count += $countArray[$c];
        $softwareNames[$c]  = $row['name'];
        $c++; 
    }

    include('htmlheader.inc.php');

    echo "<h2>Number of incidents by software since ".$_REQUEST['startdate']."</h2>";
    echo "<p>";
    echo "<table class='vertical' align='center'>";
    echo "<tr><th>Number of calls</th><th>%</th><th>Software</th></tr>";
    for($i = 0; $i < $c; $i++)
    {
        $data .= $countArray[$i]."|";
        $percentage = ($countArray[$i]/$count) * 100;
        $legend .= $softwareNames[$i]." ({$percentage}%)|";
        echo "<tr><td class='shade1'>{$countArray[$i]}</td>";
        echo "<td class='shade1'>{$percentage}%</td>";
        echo "<td class='shade1'>$softwareNames[$i]</td></tr>";
    }
    echo "</table>";

    echo "</p>";

    if (extension_loaded('gd'))
    {
        $data = substr($data,0,strlen($data)-1);
        $legend = substr($legend,0,strlen($legend)-1);
        $title = urlencode("Incidents by software");
        echo "\n<br /><p><div style='text-align:center;'>";
        echo "\n<img src='../chart.php?type=pie&data=$data&legends=$legend&title=$title' />";
        echo "\n</div></p>";
    }

    include('htmlfooter.inc.php');

}

?>