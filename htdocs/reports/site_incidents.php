<?php
// site_incidents.php - csv file showing how many incidents each site logged
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

@include ('../set_include_path.inc.php');
$permission = 37; // Run Reports

require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

$title = $strSiteIncidents;

$startdate = cleanvar($_REQUEST['start']);
$enddate = cleanvar($_REQUEST['end']);
$mode = $_REQUEST['mode'];
$zerologged = $_REQUEST['zerologged'];

if (empty($startdate)) $startdate = date('Y-m-d');
if (empty($enddate)) $enddate = date('Y-m-d');

if (empty($mode))
{
    include ('htmlheader.inc.php');

    echo "<h2>{$title}</h2>";

    echo "<form name='date' action='".$_SERVER['PHP_SELF']."?mode=run' method='post'>";
    echo "<table class='vertical'>";
    echo "<tr><th>{$strStartDate}:</th><td title='date picker'>";
    echo "<input name='start' size='10' value='{$date}' /> ";
    echo date_picker('date.start');
    echo "</td></tr>";
    echo "<tr><th>{$strEndDate}:</th><td align='left' class='shade1' title='date picker'> ";
    echo "<input name='end' size='10' />";
    echo date_picker('date.end');
    echo "</td></tr>";
    echo "<tr><td>Show sites that have logged no incidents</td><td><input type='checkbox' name='zerologged' /></td></tr>";
    echo "<tr><th>{$strOutput}</th>";
	echo "<td><select name='mode'><option value='screen'>{$strScreen}</option>";
	echo "<option value='csv'>{$strCSVfile}</option></td></tr>";
    echo "</table>";
    echo "<p align='center'>";
    echo "<input type='hidden' name='user' value='{$user}' />";
    echo "<input type='hidden' name='step' value='1' />";
    echo "<input type='submit' value=\"{$strRunReport}\" /></p>";
    echo "</form>";

    include ('htmlfooter.inc.php');
}
else
{
    // FIXME handle crash were dates are blank
    $sql = "SELECT DISTINCT s.id, s.name AS name, r.name AS resel ";
    $sql .= "FROM `{$dbSites}` AS s, `{$dbMaintenance}` AS m, `{$dbResellers}` AS r ";
    $sql.= "WHERE s.id = m.site AND r.id = m.reseller AND m.term <> 'yes' ORDER BY s.name";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    if (mysql_num_rows($result) > 0)
    {
        while ($site = mysql_fetch_object($result))
        {
            $sql = "SELECT count(i.id) AS incidentz, s.name AS site FROM `{$dbContacts}` AS c, `{$dbSites}` AS s, `{$dbIncidents}` AS i ";
            //$sql.= "WHERE contacts.siteid=sites.id AND sites.id={$site->id} AND incidents.opened > ($now-60*60*24*365.25) AND incidents.contact=contacts.id ";
            $sql.= "WHERE c.siteid = s.id AND s.id={$site->id} AND i.opened >".strtotime($startdate)." AND i.closed < ".strtotime($enddate)." AND i.contact = c.id ";
            $sql.= "GROUP BY site";
            //echo $sql;
            $sresult = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
            $details=mysql_fetch_object($sresult);
            $count=1*($details->incidentz);
            if (!empty($zerologged))
            {
                $csv .="$count,{$site->name},{$site->resel}\n";
            }
            else
            {
                if ($count!=0) $csv .="$count,{$site->name},{$site->resel}\n";
            }
        }
        $csv = "{$strIncidents}, {$strSite}, {$strReseller}\n".$csv;
        if ($_POST['mode'] == 'csv')
        {
        	$csv = "START:,{$startdate}\nEND:,{$enddate}".$csv;
			echo create_report($csv, 'csv', 'yearly_incidents.csv');    		
        }
        else
        {
        	include 'htmlheader.inc.php';
        	echo "<h2>".icon('site', 32)." {$strSiteIncidents}</h2>";
        	echo create_report($csv, 'table');
        }


    }
    else html_redirect('site_incidents.php', FALSE, $strNoResults);

}

?>
