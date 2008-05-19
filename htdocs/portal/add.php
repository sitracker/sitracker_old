<?php

// portal/add.inc.php - Add an incident in the portal
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author Kieran Hogg <kieran_hogg[at]users.sourceforge.net>
@include ('set_include_path.inc.php');
require 'db_connect.inc.php';
require 'functions.inc.php';

$accesslevel = 'any';

include 'portalauth.inc.php';
include 'portalheader.inc.php';

$contractid = cleanvar($_REQUEST['contractid']);
$productid = cleanvar($_REQUEST['product']);

if (!empty($_SESSION['formerrors']['portaladdincident']))
{
    echo $_SESSION['formerrors']['portaladdincident'];
    $_SESSION['formerrors']['portaladdincident'] = NULL;
}

if (!$_REQUEST['action'])
{
    $sql = "SELECT *, p.id AS productid, m.id AS id, ";
    $sql .= "(m.incident_quantity - m.incidents_used) AS availableincidents ";
    $sql .= "FROM `{$dbSupportContacts}` AS s, `{$dbMaintenance}` AS m, `{$dbProducts}` AS p ";
    $sql .= "WHERE m.product=p.id ";
    $sql .= "AND ((s.contactid='{$_SESSION['contactid']}' AND s.maintenanceid=m.id) ";
    $sql .= "OR m.allcontactssupported='yes') ";
    $sql .= "AND m.id='{$contractid}'";

    $checkcontract = mysql_query($sql);
    $contract = mysql_fetch_object($checkcontract);
    $productid = $contract->productid;
    echo "<h2>".icon('add', 32, $strAddIncident)." {$strAddIncident}</h2>";

    if(mysql_num_rows($checkcontract) == 0)
    {
        echo "<p class='error'>{$strPermissionDenied}</p>";
       	include 'htmlfooter.inc.php';
       	exit;
    }

    echo "<form action='{$_SERVER[PHP_SELF]}?page=add&amp;action=submit' method='post'>";
    echo "<table align='center' width='50%' class='vertical'>";
    echo "<tr><th>{$strArea}:</th><td class='shade1'>".softwareproduct_drop_down('software', 0, $productid, 'external')."<br />";
    //FIXME 3.35 which language
    echo "NOTE: Not setting one may slow down processing your incident</td></tr>"; // FIXME i18n
    echo "<tr><th>{$strTitle}:</th><td class='shade1'>";
    echo "<input class='required' maxlength='100' name='title' size='40' type='text' />";
    echo " <span class='required'>{$strRequired}</span></td></tr>";
    echo "<tr><th width='20%'>{$strProblemDescription}:</th><td class='shade1'>";
    echo "The more information you can provide, the better<br />";
    echo "<textarea name='probdesc' rows='20' cols='60'>";
    if (!empty($_SESSION['formdata']['portaladdincident']['probdesc']))
    {
        echo $_SESSION['formdata']['portaladdincident']['probdesc'];
        $_SESSION['formdata']['portaladdincident']['probdesc'] = NULL;
    }
    echo "</textarea></td></tr>";

    echo "</table>";
    echo "<input name='contractid' value='{$contractid}' type='hidden' />";
    echo "<input name='productid' value='{$productid}' type='hidden' />";
    echo "<p align='center'><input type='submit' value='{$strAddIncident}' /></p>";
    echo "</form>";

    include ('htmlfooter.inc.php');
}
else //submit
{
    $contactid = $_SESSION['contactid'];
    $contractid = cleanvar($_REQUEST['contractid']);
    $software = cleanvar($_REQUEST['software']);
    $softwareversion = cleanvar($_REQUEST['version']);
    $softwareservicepacks = cleanvar($_REQUEST['productservicepacks']);
    $incidenttitle = cleanvar($_REQUEST['title']);
    $probdesc = cleanvar($_REQUEST['probdesc']);
    $workarounds = cleanvar($_REQUEST['workarounds']);
    $reproduction = cleanvar($_REQUEST['reproduction']);
    $servicelevel = servicelevel_id2tag(maintenance_servicelevel($contractid));
    $productid = cleanvar($_REQUEST['productid']);
    
    $_SESSION['formdata']['portaladdincident'] = $_POST;
    
    $errors = 0;
    if (!isset($incidenttitle))
    {
        $_SESSION['formerrors']['portaladdincident'] .= "<p class='error'>{$strYouMustEnterAnIncidentTitle}</p>";
        $errors = 1;
    }

    if ($errors == 0)
    {
        $updatetext = "Opened via the portal by <b>".contact_realname($contactid)."</b>\n\n";
        if (!empty($probdesc))
        {
            $updatetext .= "<b>{$strProblemDescription}</b>\n{$probdesc}\n\n";
        }
    
        //create new incident
        $sql  = "INSERT INTO `{$dbIncidents}` (title, owner, contact, priority, servicelevel, status, type, maintenanceid, ";
        $sql .= "product, softwareid, productversion, productservicepacks, opened, lastupdated) ";
        $sql .= "VALUES ('{$incidenttitle}', '0', '{$contactid}', '1', '{$servicelevel}', '1', 'Support', '{$contractid}', ";
        $sql .= "'{$productid}', '{$software}', '{$softwareversion}', '{$softwareservicepacks}', '{$now}', '{$now}')";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    
        $incidentid = mysql_insert_id();
        $_SESSION['incidentid'] = $incidentid;
    
        // Create a new update
        $sql  = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, bodytext, timestamp, currentowner, ";
        $sql .= "currentstatus, customervisibility) ";
        $sql .= "VALUES ('{$incidentid}', '0', 'opening', '{$updatetext}', '{$now}', '', ";
        $sql .= "'1', 'show')";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    
        // get the service level
        // find out when the initial response should be according to the service level
        if (empty($servicelevel) OR $servicelevel == 0)
        {
            // FIXME: for now we use id but in future use tag, once maintenance uses tag
            $servicelevel = maintenance_servicelevel($contractid);
            $sql = "SELECT * FROM `{$dbServiceLevels}` WHERE id='{$servicelevel}' AND priority='{$priority}' ";
        }
        else
        {
            $sql = "SELECT * FROM `{$dbServiceLevels}` WHERE tag='{$servicelevel}' AND priority='{$priority}' ";
        }
    
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        $level = mysql_fetch_object($result);
    
        $targetval = $level->initial_response_mins * 60;
        $initialresponse = $now + $targetval;
    
        // Insert the first SLA update, this indicates the start of an incident
        // This insert could possibly be merged with another of the 'updates' records, but for now we keep it seperate for clarity
        $sql  = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, timestamp, currentowner, currentstatus, customervisibility, sla, bodytext) ";
        $sql .= "VALUES ('{$incidentid}', '0', 'slamet', '{$now}', '0', '1', 'hide', 'opened','The incident is open and awaiting action.')";
        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    
        // Insert the first Review update, this indicates the review period of an incident has started
        // This insert could possibly be merged with another of the 'updates' records, but for now we keep it seperate for clarity
        $sql  = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, timestamp, currentowner, currentstatus, customervisibility, sla, bodytext) ";
        $sql .= "VALUES ('{$incidentid}', '0', 'reviewmet', '{$now}', '0', '1', 'hide', 'opened','')";
        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    
        plugin_do('incident_created');
        $_SESSION['formdata']['portaladdincident'] = NULL;
        $_SESSION['formerrors']['portaladdincident'] = NULL;
        html_redirect("index.php", TRUE, $strIncidentAdded);
        exit;
    }
    else
    {
        html_redirect("{$_SERVER['PHP_SELF']}?contractid={$contractid}", FALSE);
    }
}
?>