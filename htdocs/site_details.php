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
/* <![CDATA[ */
    dojo.require("dojo.widget.TabContainer");
    dojo.require("dojo.widget.LinkPane");
    dojo.require("dojo.widget.ContentPane");
    dojo.require("dojo.widget.LayoutContainer");
/* ]]> */
</script>

<?php

/*
<style type="text/css">
body {
    font-family : sans-serif;
}
.dojoTabPaneWrapper {
  padding : 10px 10px 10px 10px;
}

</style>
*/

if (empty($id))
{
    echo "<p class='error'>You must select a site</p>";
    exit;
}

$sql = "SELECT name FROM sites WHERE id = '$id'";
$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
if(mysql_num_rows($result) > 0)
{
    $obj = mysql_fetch_object($result);
//echo "<h2>".stripslashes($contactrow['forenames']).' '.stripslashes($contactrow['surname'])."</h2>";
    echo "<h2>".stripslashes($obj->name)."</h2>";
}

echo "<div id='mainTabContainer' class='dojo-TabContainer' dojo:selectedTab='details'>";

echo "<div id='details' class='dojo-ContentPane' label='Details' style='overflow: auto;'>";
include('site/details.inc.php');
echo "</div>";

echo "<div id='contacts' class='dojo-ContentPane' label='Contacts' style='overflow: auto;'>";
include('site/contacts.inc.php');
echo "</div>";

// Valid user, check perms
if (user_permission($sit[2],19)) // View contracts
{
    echo "<div id='contracts' class='dojo-ContentPane' label='Contracts' style='overflow: auto;'>";
    include('site/contracts.inc.php');
    echo "</div>";
}

if(user_permission($sit[2],6)) //view incidents
{
    echo "<a dojoType='LinkPane' href='contact_support.php?id={$id}&mode=site&view=embeded' refreshOnShow='true' style='display: nonel overflow: auto;'>Incidents</a>";
}


echo "</div>";
include('htmlfooter.inc.php');

?>