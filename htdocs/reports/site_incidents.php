<?php
// site_incidents.php - csv file showing how many incidents each site logged
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

$permission=37; // Run Reports
$title='Number of incidents per site';
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');


$startdate=$_REQUEST['start'];
$enddate=$_REQUEST['end'];
$mode=$_REQUEST['mode'];
$zerologged=$_REQUEST['zerologged'];

if(empty($mode))
{
    include('htmlheader.inc.php');

    echo "<h2>Incidents by site</h2>";

    echo "<form name='date' action='".$_SERVER['PHP_SELF']."?mode=run' method='post'>";
    echo "<table class='vertical'>";
    echo "<tr><th>Start Date:</th><td title='date picker'>";
    echo "<input name='start' size='10' value='{$date}' /> ";
    echo date_picker('date.start');
    echo "</td></tr>";
    echo "<tr><th>End Date:</th><td align='left' class='shade1' title='date picker'> ";
    echo "<input name='end' size='10' />";
    echo date_picker('date.end');
    echo "</td></tr>";
    echo "<tr><td>Show sites that have logged no incidents</td><td><input type='checkbox' name='zerologged' /></td></tr>";
    echo "</table>";
    echo "<p align='center'>";
    echo "<input type='hidden' name='user' value='{$user}' />";
    echo "<input type='hidden' name='step' value='1' />";
    echo "<input type='submit' value='Run Report' /></p>";
    echo "</form>";

    include('htmlfooter.inc.php');
}
else
{
    $sql = "SELECT DISTINCT sites.id, sites.name as name, resellers.name as resel FROM sites, maintenance, resellers ";
    $sql.= "WHERE sites.id=maintenance.site AND resellers.id=maintenance.reseller AND maintenance.term<>'yes' ORDER BY name";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    $csv.="START:,{$startdate}";
    $csv.="END:,{$enddate}";
    while ($site = mysql_fetch_object($result))
    {
        $sql = "SELECT count(incidents.id) AS incidentz, sites.name as site FROM contacts, sites, incidents ";
        //$sql.= "WHERE contacts.siteid=sites.id AND sites.id={$site->id} AND incidents.opened > ($now-60*60*24*365.25) AND incidents.contact=contacts.id ";
        $sql.= "WHERE contacts.siteid=sites.id AND sites.id={$site->id} AND incidents.opened >".strtotime($startdate)." AND incidents.closed < ".strtotime($enddate)." AND incidents.contact=contacts.id ";
        $sql.= "GROUP BY site";
        //echo $sql;
        $sresult = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        $details=mysql_fetch_object($sresult);
        $count=1*($details->incidentz);
        if(!empty($zerologged))
        {
            $csv .="$count,'{$site->name},'{$site->resel}'\n";
        }
        else
        {
            if($count!=0) $csv .="$count,'{$site->name},'{$site->resel}'\n";
        }
    }
    header("Content-type: text/csv\r\n");
    header("Content-disposition-type: attachment\r\n");
    header("Content-disposition: filename=yearly_incidents.csv");
    echo "incidents, site, reseller\n";
    echo $csv;
}

?>
