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

function portal_incident_table($sql)
{
    global $CONFIG;
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    $numincidents = mysql_num_rows($result);
    
    if ($numincidents >= 1)
    {
        $shade = 'shade1';
        $html .=  "<table align='center' width='70%'>";
        $html .=  "<tr>";
        $html .=  colheader('id', $GLOBALS['strID'], $sort, $order, $filter);
        $html .=  colheader('title', $GLOBALS['strTitle'], $sort, $order, $filter);
        $html .=  colheader('owner', $GLOBALS['strOwner'], $sort, $order, $filter);
        $html .=  colheader('lastupdated', $GLOBALS['strLastUpdated'], $sort, $order, $filter);
        $html .=  colheader('contact', $GLOBALS['strContact'], $sort, $order, $filter);
        $html .=  colheader('status', $GLOBALS['strStatus'], $sort, $order, $filter);
        if ($showclosed == "false")
        {
            $html .=  colheader('actions', $strOperation);
        }
    
        $html .=  "</tr>\n";
        while ($incident = mysql_fetch_object($result))
        {
            $html .=  "<tr class='$shade'><td>";
            $html .=  "<a href='incident.php?id={$incident->id}'>{$incident->id}</a></td>";
            $html .=  "<td>";
            if (!empty($incident->softwareid))
            {
                $html .=  software_name($incident->softwareid)."<br />";
            }
    
            $html .=  "<strong><a href='incident.php?id={$incident->id}'>{$incident->title}</a></strong></td>";
            $html .=  "<td>".user_realname($incident->owner)."</td>";
            $html .=  "<td>".ldate($CONFIG['dateformat_datetime'], $incident->lastupdated)."</td>";
            $html .=  "<td>{$incident->forenames} {$incident->surname}</td>";
            $html .=  "<td>".incidentstatus_name($incident->status, external)."</td>";
            if ($showclosed == "false")
            {
                $html .=  "<td><a href='update.php?id={$incident->id}'>{$strUpdate}</a> | ";
    
                //check if the customer has requested a closure
                $lastupdate = list($update_userid, $update_type, $update_currentowner, $update_currentstatus, $update_body, $update_timestamp, $update_nextaction, $update_id)=incident_lastupdate($incident->id);
    
                if ($lastupdate[1] == "customerclosurerequest")
                {
                    $html .=  "{$strClosureRequested}</td>";
                }
                else
                {
                    $html .=  "<a href='close.php?id={$incident->id}'>{$strRequestClosure}</a></td>";
                }
            }
            echo "</tr>";
            if ($shade == 'shade1') $shade = 'shade2';
            else $shade = 'shade1';
        }
        $html .=  "</table>";
    }
    else
    {
        $html .= "<p class='info'>{$strNoIncidents}</p>";
    }
    
    return $html;
}


if (empty($showclosed)) $showclosed = "false";

if ($showclosed == "true")
{
    echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/support.png' alt='{$strYourClosedIncidents}' /> ";
    echo "{$strYourClosedIncidents}</h2>";
    echo "<p align='center'><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/reopen.png' alt='{$strShowOpenIncidents}' /> ";
    echo "<a href='$_SERVER[PHP_SELF]?page=incidents&amp;showclosed=false'>{$strShowOpenIncidents}</a>";
    echo "</p>";
    $sql = "SELECT i.*, c.forenames, c.surname FROM `{$dbIncidents}` AS i, `{$dbContacts}` AS c ";
    $sql .= "WHERE status = 2 AND c.id = i.contact ";
    $sql .= "AND contact = '{$_SESSION['contactid']}' ";    
    $sql .= "ORDER BY closed DESC";
}
else
{
    echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/support.png' alt='{$strYourCurrentOpenIncidents}' /> ";
    echo "{$strYourCurrentOpenIncidents}</h2>";
    echo "<p align='center'><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/close.png' alt='{$strShowClosedIncidents}' /> ";
    echo "<a href='{$_SERVER['PHP_SELF']}?page=incidents&amp;showclosed=true'>{$strShowClosedIncidents}</a>";
    echo "</p>";
    $sql = "SELECT i.*, c.forenames, c.surname FROM `{$dbIncidents}` AS i, `{$dbContacts}` AS c WHERE status != 2 ";
    $sql .= "AND c.id = i.contact ";
    $sql .= "AND i.contact = '{$_SESSION['contactid']}' ";    
    $sql .= "ORDER by i.id DESC";
}


echo portal_incident_table($sql);
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

echo "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/add.png' /> {$strAddIncident}</a></p>";

//find list of other incidents we're allowed to see
$otherincidents = array();
$contracts = $_SESSION['contracts'];
if(!empty($contracts))
{
    $sql = "SELECT DISTINCT i.id
        FROM `{$dbIncidents}` AS i, `{$dbMaintenance}` AS m
        WHERE (1=0 ";
        
    foreach($contracts AS $contract)
    {
        $sql .= "OR i.maintenanceid = {$contract} ";
    }
    $sql .= ")";
    $result = mysql_query($sql);
    while($incidents = mysql_fetch_object($result))
    {
        $otherincidents[] = $incidents->id;
    }
}


if ($CONFIG['portal_site_incidents'] AND $otherincidents != NULL)
{
    if ($showclosed == "true")
    {
        echo "<h2>{$strYourSitesIncidents}</h2>";
        $sql = "SELECT DISTINCT i.id AS id, i.*, c.forenames, c.surname ";
        $sql .= "FROM `{$dbIncidents}` AS i, `{$dbContacts}` AS c, `{$dbSites}` AS s ";
        $sql .= "WHERE status = 2 ";
        $sql .= "AND c.id=i.contact ";
        $sql .= "AND i.contact != {$_SESSION['contactid']} ";
        $sql .= "AND opened > ".($CONFIG['hide_closed_incidents_older_than'] * 86400)." ";
        $sql .= "AND c.siteid=s.id AND s.id={$_SESSION['siteid']} ";
        $sql .= "AND (1=0 "; 
        
        foreach($otherincidents AS $maintid)
        {
            $sql .= "OR i.maintenanceid={$maintid} ";
        }

        $sql .= ") ORDER BY closed DESC ";
    }
    else
    {
        echo "<h2>{$strYourSitesClosedIncidents}</h2>";
        $sql = "SELECT DISTINCT i.id AS id, i.*, c.forenames, c.surname ";
        $sql .= "FROM `{$dbIncidents}` AS i, `{$dbContacts}` AS c, `{$dbSites}` AS s ";
        $sql .= "WHERE status != 2 ";
        $sql .= "AND c.id=i.contact ";
        $sql .= "AND i.contact != {$_SESSION['contactid']} ";
        $sql .= "AND c.siteid=s.id AND s.id={$_SESSION['siteid']} ";
        $sql .= "AND (1=0 ";
        
        foreach($otherincidents AS $incident)
        {
            $sql .= "OR i.id={$incident} ";
        }
        
        $sql .= ") ORDER by i.id DESC";
    }
    
    echo portal_incident_table($sql);
    
}

include ('htmlfooter.inc.php');

?>