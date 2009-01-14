<?php
// site_incidents.php - csv file showing how many incidents each site logged
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
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
$showsitesloggedfewerthanxcalls = cleanvar($_REQUEST['showsitesloggedfewerthanxcalls']);
$numberofcalls = cleanvar($_REQUEST['numberofcalls']);
$showincidentdetails = cleanvar($_REQUEST['showincidentdetails']);
$onlyshowactivesites = cleanvar($_REQUEST['onlyshowactivesites']);
$slas = cleanvar($_REQUEST['slas']);
$showproducts = cleanvar($_REQUEST['showproducts']);

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
			$('zerologged').readOnly = true;
		}
		else
		{
			$('numberofcalls').hide();
			$('labelforxcalls').hide();
			$('zerologged').checked = false;
			$('zerologged').readOnly = false;
		}
	}
	</script>
	<?php

    echo "<h2>{$title}</h2>";

    echo "<form name='date' action='".$_SERVER['PHP_SELF']."?mode=run' method='post'>\n";
    echo "<table class='vertical'>\n";
    echo "<tr><th>{$strStartDate}:</th><td title='date picker'>\n";
    echo "<input name='start' size='10' value='{$date}' />\n";
    echo date_picker('date.start');
    echo "</td></tr>\n";
    echo "<tr><th>{$strEndDate}:</th><td align='left' class='shade1' title='date picker'>\n";
    echo "<input name='end' size='10' />\n";
    echo date_picker('date.end');
    echo "</td></tr>\n";
    echo "<tr><th>{$strShowSitesThatHaveLoggedNoIncidents}</th><td><input type='checkbox' name='zerologged' id='zerologged' /></td></tr>\n";
    
    echo "<tr><th>{$strExcludeSitesWith}</th><td>\n";

	$sql = "SELECT DISTINCT id, tag FROM `{$dbServiceLevels}`";
	$result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    if (mysql_num_rows($result) > 0)
    {
    	echo "<select name='slas[]' multiple='multiple' size='5'>\n";
    	while ($obj = mysql_fetch_object($result))
    	{
    		echo "<option value='$obj->id'>{$obj->tag}</option>\n";
    	}
    	echo "</select>\n";
    }

	echo "</td></tr>\n";
    
    echo "<tr><th>{$strShowSitesWhichHaveLoggedLessThanCalls}</th><td>\n";
	echo "<input type='checkbox' name='showsitesloggedfewerthanxcalls' id='showsitesloggedfewerthanxcalls' onclick=\"checkBoxToggle();\" />\n";
	echo "<input type='text' name='numberofcalls' id='numberofcalls' style='display:none'/><label id='labelforxcalls' for='showsitesloggedfewerthanxcalls' style='display:none'>{$strIncidents}</label></td></tr>\n";
	echo "<tr><th>{$strShowIncidentDetails}</th><td><input type='checkbox' name='showincidentdetails' id='showincidentdetails' /></td></tr>\n";
	echo "<tr><th>{$strOnlyShowSitesWithActiveContracts}</th><td><input type='checkbox' name='onlyshowactivesites' id='onlyshowactivesites' /></td></tr>\n";
	echo "<tr><th>{$strShowProducts}</th><td><input type='checkbox' name='showproducts' id='showproducts' /></td></tr>";
    echo "<tr><th>{$strOutput}</th>\n";
	echo "<td><select name='mode'><option value='screen'>{$strScreen}</option>\n";
	echo "<option value='csv'>{$strCSVfile}</option></select></td></tr>\n";
    echo "</table>\n";
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

    function does_site_have_certain_sla_contract($siteid, $slas)
    {
    	$toReturn = false;
    	global $CONFIG, $dbMaintenance, $dbServiceLevels;
    	
    	if (!empty($slas))
    	{
    		$ssql = "SELECT id FROM `{$dbMaintenance}` WHERE site = '{$siteid}' AND ";
	    	
	    	foreach ($slas AS $s)
	    	{
	    		if (!empty($qsql))$qsql .= " OR ";
	    		$qsql .= " servicelevelid = {$s} ";
	    	}
	    	
	    	$ssql .= "({$qsql})";
	    	
	    	$sresult = mysql_query($ssql);
	    	if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
	    	if (mysql_num_rows($sresult) > 0)
	    	{
	    		$toReturn = true;
	    	}
	    }

    	return $toReturn;
    }
    
    $sql = "SELECT DISTINCT s.id, s.name AS name, r.name AS resel, m.reseller, u.realname ";
    $sql .= "FROM `{$dbSites}` AS s, `{$dbMaintenance}` AS m, `{$dbResellers}` AS r, `{$dbUsers}` AS u ";
    $sql .= "WHERE s.id = m.site AND r.id = m.reseller AND m.term <> 'yes' AND s.owner = u.id ";
    if ($onlyshowactivesites == 'on')
    {
    	$sql .= "AND m.expirydate > '{$now}' "; 
    }
    $sql .= "ORDER BY s.name";
    /*
SELECT DISTINCT s.id, s.name AS name, r.name AS resel, m.reseller, u.realname 
FROM `sites` AS s, `maintenance` AS m, `resellers` AS r, `users` AS u 
WHERE s.id = m.site AND r.id = m.reseller AND m.term <> 'yes' AND s.owner = u.id AND m.expirydate > '1231609928' ORDER BY s.name 
     */
    // echo $sql;
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    if (mysql_num_rows($result) > 0)
    {
        while ($site = mysql_fetch_object($result))
        {
        	if ($showproducts == 'on')
        	{
        		$product = "";
        		$psql  = "SELECT m.id AS maintid, m.term AS term, p.name AS product, ";
		        $psql .= "m.admincontact AS admincontact, ";
		        $psql .= "r.name AS reseller, licence_quantity, lt.name AS licence_type, expirydate, admincontact, c.forenames AS admincontactsforenames, c.surname AS admincontactssurname, m.notes AS maintnotes ";
		        $psql .= "FROM `{$dbMaintenance}` AS m, `{$dbContacts}` AS c, `{$dbProducts}` AS p, `{$dbLicenceTypes}` AS lt, `{$dbResellers}` AS r ";
		        $psql .= "WHERE m.product = p.id AND m.reseller = r.id AND licence_type = lt.id AND admincontact = c.id ";
		        $psql .= "AND m.site = '{$site->id}' ";
		        $psql .= "ORDER BY p.name ASC";
		        $presult = mysql_query($psql);
		        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
		        while ($prod = mysql_fetch_object($presult))
		        {
		            $product .= "{$prod->product}<br />";
		        }
        	}
        	
        	if ((!empty($slas) AND !does_site_have_certain_sla_contract($site->id, $slas)) OR empty($slas))
        	{
	            $sql = "SELECT count(i.id) AS incidentz, s.name AS site FROM `{$dbContacts}` AS c, `{$dbSites}` AS s, `{$dbIncidents}` AS i, `{$dbMaintenance}` AS m ";
	            $sql.= "WHERE c.siteid = s.id AND s.id={$site->id} AND i.opened >".strtotime($startdate)." AND i.closed < ".strtotime($enddate)." AND i.contact = c.id ";
	            $sql .= "AND m.id = i.maintenanceid AND m.reseller = '{$site->reseller}' ";
	            $sql.= "GROUP BY site";
	            // echo $sql;
	            $sresult = mysql_query($sql);
	            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
	            $details = mysql_fetch_object($sresult);
	            $count = $details->incidentz;
	            if (!empty($zerologged))
	            {
	            	if (empty($count)) $count = 0;
	            	if ($showsitesloggedfewerthanxcalls == 'on' AND $count <= $numberofcalls)
	            	{
	                	$csv .= "\"{$count}\",\"{$site->name}\",\"{$site->realname}\",\"{$site->resel}";
	            	}
	            	else if (empty($showsitesloggedfewerthanxcalls))
	            	{
	            		$csv .= "\"{$count}\",\"{$site->name}\",\"{$site->realname}\",\"{$site->resel}";
	            	}
	            }
	            else
	            {
	            	// Dont need to check $showsitesloggedfewerthanxcalls as $zerologged will always be selected
	                if ($count != 0) $csv .= "\"{$count}\",\"{$site->name}\",\"{$site->realname}\",\"{$site->resel}";
	            }
	            
            	if ($showproducts == 'on')
	        	{
	        		$csv .= "\",\"{$product}";
	        	}
        	}
        	      	
			$csv .= "\"\n";
        }
        
        echo "<pre>";
        echo $csv;
        echo "</pre>";
        
        $header = "\"{$strIncidents}\",\"{$strSite}\",\"{$strAccountManager}\",\"{$strReseller}";
		if ($showproducts == 'on')
		{
			$header .= "\",\"{$strProducts}";
		}
		$csv = $header."\"\n".$csv;
        
        if ($_REQUEST['mode'] == 'csv')
        {
        	$csv = "\"{$strStartDate}:\",\"{$startdate}\"\n{$strEndDate}:\",\"{$enddate}".$csv;
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
