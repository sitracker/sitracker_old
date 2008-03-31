<?php
/*
portal/index.php - Lists incidents in the portal

SiT (Support Incident Tracker) - Support call tracking system
Copyright (C) 2000-2008 Salford Software Ltd. and Contributors

This software may be used and distributed according to the terms
of the GNU General Public License, incorporated herein by reference.
*/

include 'portalheader.inc.php';

$showclosed = cleanvar($_REQUEST['showclosed']);
$site = cleanvar($_REQUEST['site']);

if (empty($showclosed)) $showclosed = "false";
if (empty($site)) $site = "false";

if ($showclosed == "true")
{
    echo "<h2>{$strYourClosedIncidents}</h2>";
    echo "<p align='center'><a href='$_SERVER[PHP_SELF]?page=incidents&amp;showclosed=false&amp;site={$site}'>{$strShowOpenIncidents}</a>";
    if ($site == "true" AND $CONFIG['portal_view_site'] == TRUE)
    {
        echo " | <a href='$_SERVER[PHP_SELF]?page=incidents&amp;showclosed=false&amp;site=false'>{$strShowMine}</a>";
    }
    elseif ($CONFIG['portal_view_site'] == TRUE)
    {
        // Show site
        echo " | <a href='$_SERVER[PHP_SELF]?page=incidents&amp;showclosed=false&amp;site=true'>{$strShowSite}</a>";
    }
    echo "</p>";
    $sql = "SELECT i.*, c.forenames, c.surname FROM `{$dbIncidents}` AS i, `{$dbContacts}` AS c ";
    $sql .= "WHERE status = 2 AND c.id = i.contact ";
    
    if ($site == "true" AND $CONFIG['portal_view_site'] == TRUE)
    {
        $sql .= "AND c.siteid = {$_SESSION['siteid']} ";
    }
    else
    {
        $sql .= "AND contact = '{$_SESSION['contactid']}' ";
    }
    
    $sql .= "ORDER BY closed DESC";
}
else
{
    echo "<h2>{$strYourCurrentOpenIncidents}</h2>";
    echo "<p align='center'><a href='$_SERVER[PHP_SELF]?page=incidents&amp;showclosed=true&amp;site={$site}'>{$strShowClosedIncidents}</a>";
    if ($site == "true" AND $CONFIG['portal_view_site'] == TRUE)
    {
        echo " | <a href='$_SERVER[PHP_SELF]?page=incidents&amp;showclosed=false&amp;site=false'>{$strShowMine}</a>";
    }
    elseif ($CONFIG['portal_view_site'] == TRUE)
    {
        echo " | <a href='$_SERVER[PHP_SELF]?page=incidents&amp;showclosed=false&amp;site=true'>{$strShowSite}</a>";
    }
    echo "</p>";
    $sql = "SELECT i.*, c.forenames, c.surname FROM `{$dbIncidents}` AS i, `{$dbContacts}` AS c WHERE status != 2 ";
    $sql .= "AND c.id = i.contact ";
    
    if ($site == "true" AND $CONFIG['portal_view_site'] == TRUE)
    {
        $sql .= "AND c.siteid = {$_SESSION['siteid']} ";
    }
    else
    {
        $sql .= "AND i.contact = '{$_SESSION['contactid']}' ";
    }
    
    $sql .= "ORDER by i.id DESC";
}

$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
$numincidents = mysql_num_rows($result);

if ($numincidents >= 1)
{
    $shade = 'shade1';
    echo "<table align='center' width='70%'>";
    echo "<tr>";
    echo colheader('id', $strID, $sort, $order, $filter);
    echo colheader('title', $strTitle, $sort, $order, $filter);
    echo colheader('owner', $strOwner, $sort, $order, $filter);
    echo colheader('lastupdated', $strLastUpdated, $sort, $order, $filter);
    echo colheader('contact', $strContact, $sort, $order, $filter);
    echo colheader('status', $strStatus, $sort, $order, $filter);
    if ($showclosed == "false")
    {
        echo colheader('actions', $strOperation, '', '', '', '', 15);
    }

    echo "</tr>\n";
    while ($incident = mysql_fetch_object($result))
    {
        echo "<tr class='$shade'><td>";
        echo "<a href='incident.php?id={$incident->id}'>{$incident->id}</a></td>";
        echo "<td>";
        if (!empty($incident->softwareid))
        {
            echo software_name($incident->softwareid)."<br />";
        }

        echo "<strong><a href='incident.php?id={$incident->id}'>{$incident->title}</a></strong></td>";
        echo "<td>".user_realname($incident->owner)."</td>";
        echo "<td>".format_date_friendly($incident->lastupdated)."</td>";
        echo "<td>{$incident->forenames} {$incident->surname}</td>";
        echo "<td>".incidentstatus_name($incident->status, external)."</td>";
        if ($showclosed == "false")
        {
            echo "<td><a href='update.php?id={$incident->id}'>{$strUpdate}</a> | ";

            //check if the customer has requested a closure
            $lastupdate = list($update_userid, $update_type, $update_currentowner, $update_currentstatus, $update_body, $update_timestamp, $update_nextaction, $update_id)=incident_lastupdate($incident->id);

            if ($lastupdate[1] == "customerclosurerequest")
            {
                echo "{$strClosureRequested}</td>";
            }
            else
            {
                echo "<a href='close.php?id={$incident->id}'>{$strRequestClosure}</a></td>";
            }
        }
        echo "</tr>";
        if ($shade == 'shade1') $shade = 'shade2';
        else $shade = 'shade1';
    }
    echo "</table>";
}
else
{
    echo "<p class='info'>{$strNoIncidents}</p>";
}

echo "<p align='center'>";
if($numcontracts == 1)
{
    //only one contract
    echo "<a href='add.php?contractid={$contractid}&amp;product={$productid}'>";
}
else
{
    echo "<a href='entitlement.php'>";
}

echo "{$strAddIncident}</a></p>";
include ('htmlfooter.inc.php');

?>