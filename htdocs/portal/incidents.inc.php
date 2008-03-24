<?php
/*
portal/incidents.inc.php - Lists incidents in the portal included by ../portal.php

SiT (Support Incident Tracker) - Support call tracking system
Copyright (C) 2000-2008 Salford Software Ltd. and Contributors

This software may be used and distributed according to the terms
of the GNU General Public License, incorporated herein by reference.
*/

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}


$showclosed = $_REQUEST['showclosed'];
if (empty($showclosed)) $showclosed = "false";

if ($showclosed == "true")
{
    echo "<h2>{$strYourClosedIncidents}</h2>";
    echo "<p align='center'><a href='$_SERVER[PHP_SELF]?page=incidents&amp;showclosed=false'>{$strShowOpenIncidents}</a></p>";
    $sql = "SELECT * FROM `{$dbIncidents}` ";
    $sql .= "WHERE status = 2 AND contact = '{$_SESSION['contactid']}' ";
    $sql .= "ORDER BY closed DESC";
}
else
{
    echo "<h2>{$strYourCurrentOpenIncidents}</h2>";
    echo "<p align='center'><a href='$_SERVER[PHP_SELF]?page=incidents&amp;showclosed=true'>{$strShowClosedIncidents}</a></p>";
    $sql = "SELECT * FROM `{$dbIncidents}` WHERE status != 2 AND contact = '{$_SESSION['contactid']}'";
}
$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
$numincidents = mysql_num_rows($result);

if ($numincidents >= 1)
{
    $shade = 'shade1';
    echo "<table align='center'>";
    echo "<tr>";
    echo colheader('id', $strID, $sort, $order, $filter);
    echo colheader('title', $strTitle);
    echo colheader('lastupdated', $strLastUpdated);
    echo colheader('status', $strStatus);
    if ($showclosed == "false")
    {
        echo colheader('actions', $strOperation);
    }

    echo "</tr>\n";
    while ($incident = mysql_fetch_object($result))
    {
        echo "<tr class='$shade'><td>";
        echo "<a href='portal.php?page=showincident&amp;id={$incident->id}'>{$incident->id}</a></td>";
        echo "<td>";
        if (!empty($incident->softwareid))
        {
            echo software_name($incident->softwareid)."<br />";
        }

        echo "<strong><a href='portal.php?page=showincident&amp;id={$incident->id}'>{$incident->title}</a></strong></td>";
        echo "<td>".format_date_friendly($incident->lastupdated)."</td>";
        echo "<td>".incidentstatus_name($incident->status)."</td>";

        if ($showclosed == "false")
        {
            echo "<td><a href='{$_SERVER[PHP_SELF]}?page=update&amp;id={$incident->id}'>{$strUpdate}</a> | ";

            //check if the customer has requested a closure
            $lastupdate = list($update_userid, $update_type, $update_currentowner, $update_currentstatus, $update_body, $update_timestamp, $update_nextaction, $update_id)=incident_lastupdate($incident->id);

            if ($lastupdate[1] == "customerclosurerequest")
            {
                echo "{$strClosureRequested}</td>";
            }
            else
            {
                echo "<a href='{$_SERVER[PHP_SELF]}?page=close&amp;id={$incident->id}'>{$strRequestClosure}</a></td>";
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

//FIXME i think the add page expects the contractid in a hiddent form field
echo "<p align='center'>";
if($numcontracts == 1)
{
    //only one contract
    echo "<a href='portal.php?page=add'>{$strAddIncident}</a></li>";
}
else
{
    echo "<a href='{$_SERVER[PHP_SELF]}?page=entitlement'>";
}

echo "{$strAddIncident}</a></p>";
?>