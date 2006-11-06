<?php
// site_details.php - Show all site details
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
// Created: 9th March 2001
// This Page Is Valid XHTML 1.0 Transitional! 27Oct05

$permission=11; // View Sites
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

// External variables
$id=cleanvar($_REQUEST['id']);

include('htmlheader.inc.php');
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

if (empty($id))
{
    echo "<p class='error'>You must select a site</p>";
    exit;
}

echo "<div id='mainTabContainer' dojoType='TabContainer' style='width: 80%; height: 500px' selectedTab='details'>";

echo "<div id='details' dojoType='ContentPane' label='Details'>";
include('site/details.inc.php');
echo "</div>";

echo "<div id='contacts' dojoType='ContentPane' label='Contacts'>";
include('site/contacts.inc.php');
echo "</div>";

// Valid user, check perms
if (user_permission($sit[2],19)) // View contracts
{
    echo "<div id='contracts' dojoType='ContentPane' label='Contracts'>";
    include('site/contracts.inc.php');
    echo "</div>";
}

if(user_permission($sit[2],6)) //view incidents
{
    echo "<a dojoType='LinkPane' href='contact_support.php?id={$id}&mode=site&view=embeded' refreshOnShow='true' style='display: none'>Incidents</a>";
}


echo "</div>";
include('htmlfooter.inc.php');

?>
