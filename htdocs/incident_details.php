<?php
// incident_details.php - Show incident details
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//


// This file will soon be superceded by incident.php - 20Oct05 INL

$permission=61; // View Incident Details
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

// External variables
$incidentid = cleanvar($_REQUEST['id']);
$javascript = cleanvar($_REQUEST['javascript']); //is javascript enabled
$id = $incidentid;
$title='Details';

include('incident_html_top.inc.php');
?>

<script type="text/javascript" src="scripts/dojo/dojo.js"></script>

<script type="text/javascript">
    dojo.require("dojo.widget.TabContainer");
    dojo.require("dojo.widget.LinkPane");
    dojo.require("dojo.widget.ContentPane");
    dojo.require("dojo.widget.LayoutContainer");
</script>

<style type="text/css">
body {
    font-family : sans-serif;
}
.dojoTabPaneWrapper {
  padding : 10px 10px 10px 10px;
}

</style>
<?php
//include('incident/details.inc.php');


//include('incident/log.inc.php');
echo "<div id='mainTabContainer' dojoType='TabContainer' style='width: 100%; height: 500px' selectedTab='log'>";

echo "<div id='log' dojoType='ContentPane' label='Incident Log'>";
include('incident/log.inc.php');
echo "</div>";

if (incident_status($id) != 2)
{
    if(user_permission($sit[2],8)) //udpate incidents
    {
        echo "<a dojoType='LinkPane' href='update_incident.php?id={$id}' refreshOnShow='true' style='display: none'>Update</a>";
    }

    if(user_permission($sit[2],12)) //Reassign incident
    {
        echo "<a dojoType='LinkPane' href=reassign_incident.php?id={$id}' refreshOnShow='true' style='display: none'>Reassign</a>";
    }

    if(user_permission($sit[2],7)) //Edit incident
    {
        //echo "<a dojoType='LinkPane' href=edit_incident.php?id={$id}' refreshOnShow='true' style='display: none'>Edit</a>";
        echo "<div id='Edit' dojoType='ContentPane' label='Edit'>";
        include('incident/edit.inc.php');
        echo "</div>";
    }

    if(user_permission($sit[2],6)) //View incident
    {
        //echo "<a dojoType='LinkPane' href=incident_service_levels.php?id={$id}' refreshOnShow='true' style='display: none'>Service</a>";
        echo "<div id='sla' dojoType='ContentPane' label='Service'>";
        include('incident/sla.inc.php');
        echo "</div>";
    }

    if(user_permission($sit[2],61)) //Incident details
    {
        //echo "<a dojoType='LinkPane' href=incident_relationships.php?id={$id}' refreshOnShow='true' style='display: none'>Relationships</a>";
        echo "<div id='Relationships' dojoType='ContentPane' label='Relationships'>";
        include('incident/relationships.inc.php');
        echo "</div>";
    }

    if(user_permission($sit[2],62)) //View attachements
    {
        //echo "<a dojoType='LinkPane' href=incident_attachments.php?id={$id}' refreshOnShow='true' style='display: none'>Files</a>";
        echo "<div id='Files' dojoType='ContentPane' label='Files'>";
        include('incident/files.inc.php');
        echo "</div>";
    }

    echo "<div id='escalate' dojoType='ContentPane' label='Escalate'>";
    include('incident/escalate.inc.php');
    echo "</div>";

    //echo "<a class='barlink' href='javascript:email_window({$id})' accesskey='E'><em>E</em>mail</a> | ";
        
}
echo "</div>";

include('incident_html_bottom.inc.php');
?>
