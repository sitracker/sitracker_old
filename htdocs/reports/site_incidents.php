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
$mode = cleanvar($_REQUEST['mode']);
$zerologged = cleanvar($_REQUEST['zerologged']);
$shownoprefferedcontractsonly = cleanvar($_REQUEST['shownoprefferedcontractsonly']);
$showsitesloggedfewerthanxcalls = cleanvar($_REQUEST['showsitesloggedfewerthanxcalls']);
$numberofcalls = cleanvar($_REQUEST['numberofcalls']);
$showincidentdetails = cleanvar($_REQUEST['showincidentdetails']);

if (empty($mode))
{
    include ('htmlheader.inc.php');

	?>
	<script type='text/javascript'>
	function checkBoxToggle()
	{
		if ($('showsitesloggedfewerthanxcalls').checked == true)
		{
			$('numberofcalls').show();
			$('labelforxcalls').show();
			$('zerologged').checked = true;
			$('zerologged').disable();
		}
		else
		{
			$('numberofcalls').hide();
			$('labelforxcalls').hide();
			$('zerologged').checked = false;
			$('zerologged').enable();			
		}
	}
	</script>
	<?php

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
    echo "<tr><td>{$strShowSitesThatHaveLoggedNoIncidents}</td><td><input type='checkbox' name='zerologged' id='zerologged' /></td></tr>";
    echo "<tr><td>{$strShowOnlySitesWithNoPrefferredContract}</td><td><input type='checkbox' name='shownoprefferedcontractsonly' /></td></tr>";
    echo "<tr><td>{$strShowSitesWhichHaveLoggedLessThanCalls}</td><td>";
	echo "<input type='checkbox' name='showsitesloggedfewerthanxcalls' id='showsitesloggedfewerthanxcalls' onclick=\"checkBoxToggle();\" />";
	echo "<input type='text' name='numberofcalls' id='numberofcalls' id='numberofcalls' style='display:none'/><label id='labelforxcalls' for='showsitesloggedfewerthanxcalls' style='display:none'>{$strIncidents}</label></td></tr>";
	echo "<tr><td>{$strShowIncidentDetails}</td><td><input type='checkbox' name'showincidentdetails' id='showincidentdetails' /></td></tr>";
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
	if (empty($startdate)) $startdate = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d"), date("Y")-1)); // 1 year ago
	if (empty($enddate)) $enddate = date('Y-m-d');
    
    if ($shownoprefferedcontractsonly == 'on' AND count($CONFIG['preferred_maintenance']) > 0)
    {
    	$sql = "SELECT DISTINCT id FROM `{$dbServiceLevels}` WHERE ";
    	foreach ($CONFIG['preferred_maintenance'] AS $p)
    	{
    		if (!empty($asql)) $asql .= " AND ";
    		$asql .= " tag = '{$p}' ";
    	}
    	
    	$sql .= $asql;
    	
    	$result = mysql_query($sql);
	    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
	    if (mysql_num_rows($result) > 0)
	    {
	    	while ($obj = mysql_fetch_object($result))
	    	{
	    		$preferred_ids[] = $obj->id;
	    	} 
	    }
    }
    
    $sql = "SELECT DISTINCT s.id, s.name AS name, r.name AS resel, m.reseller ";
    $sql .= "FROM `{$dbSites}` AS s, `{$dbMaintenance}` AS m, `{$dbResellers}` AS r ";
    $sql.= "WHERE s.id = m.site AND r.id = m.reseller AND m.term <> 'yes' ORDER BY s.name";
    // echo $sql;
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    if (mysql_num_rows($result) > 0)
    {
        while ($site = mysql_fetch_object($result))
        {
            $sql = "SELECT count(i.id) AS incidentz, s.name AS site FROM `{$dbContacts}` AS c, `{$dbSites}` AS s, `{$dbIncidents}` AS i, `{$dbMaintenance}` AS m ";
            $sql.= "WHERE c.siteid = s.id AND s.id={$site->id} AND i.opened >".strtotime($startdate)." AND i.closed < ".strtotime($enddate)." AND i.contact = c.id ";
            $sql .= "AND m.id = i.maintenanceid AND m.reseller = '{$site->reseller}' ";
            if ($shownoprefferedcontractsonly == 'on' AND count($preferred_ids) > 0)
            {
            	// TODO change so  theres an IF just after the while which checks if this site has a preffered contract and this is enabled is so jump over
            }
            $sql.= "GROUP BY site";
            echo $sql;
            // echo $sql;
            $sresult = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
            $details = mysql_fetch_object($sresult);
            $count = $details->incidentz;
            if (!empty($zerologged))
            {
            	if ($showsitesloggedfewerthanxcalls == 'on' AND $count <= $numberofcalls)
            	{
                	$csv .= "$count,{$site->name},{$site->resel}\n";
            	}
            	else if (empty($showsitesloggedfewerthanxcalls))
            	{
            		$csv .= "$count,{$site->name},{$site->resel}\n";
            	}
            }
            else
            {
            	// Dont need to check $showsitesloggedfewerthanxcalls as $zerologged will always be selected
                if ($count != 0) $csv .="$count,{$site->name},{$site->resel}\n";
            }
        }
        $csv = "{$strIncidents}, {$strSite}, {$strReseller}\n".$csv;
        if ($_POST['mode'] == 'csv')
        {
        	$csv = "{$strStartDate}:,{$startdate}\n{$strEndDate}:,{$enddate}".$csv;
			echo create_report($csv, 'csv', 'yearly_incidents.csv');    		
        }
        else
        {
        	include ('htmlheader.inc.php');
        	echo "<h2>".icon('site', 32)." {$strSiteIncidents}</h2>";
        	echo "<p align='center'>{$strStartDate}: {$startdate}. {$strEndDate}: {$enddate}</p>";
        	echo create_report($csv, 'table');
        	include ('htmlfooter.inc.php');
        }


    }
    else html_redirect('site_incidents.php', FALSE, $strNoResults);

}

?>
