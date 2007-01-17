<?php
// contact_details.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//


// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
// Created: 24th May 2001
// Purpose: Show All Contact Details
// This Page Is Valid XHTML 1.0 Transitional! 27Oct05

$permission=12;  // view contacts

require('db_connect.inc.php');
require('functions.inc.php');
$title='Contact Details';

// This page requires authentication
require('auth.inc.php');

// External variables
$id = mysql_escape_string($_REQUEST['id']);
$output = $_REQUEST['output'];

if ($output == 'vcard')
{
    header("Content-type: text/x-vCard\r\n");
    header("Content-disposition-type: attachment\r\n");
    header("Content-disposition: filename=contact.vcf");
    echo contact_vcard($id);
    exit;
}

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
// Display contacts
$sql="SELECT * FROM contacts WHERE id='$id' ";
$contactresult = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
while ($contactrow=mysql_fetch_array($contactresult))
{

    echo "<h2>".stripslashes($contactrow['forenames']).' '.stripslashes($contactrow['surname'])."</h2>";
    echo "<div id='mainTabContainer' class='dojo-TabContainer' selectedTab='details'>";

    echo "<div id='details' class='dojo-ContentPane' label='Details'>";
    include('contact/details.inc.php');
    echo "</div>";

    if (user_permission($sit[2],30)) // view supported products
    {
        echo "<div id='Contracts' class='dojo-ContentPane' label='Contracts'>";
        include('contact/contracts.inc.php');
        echo "</div>";
    }

    if(user_permission($sit[2],6)) //view incidents
    {

        echo "<a dojoType='LinkPane' href='contact_support.php?id={$id}&view=embeded' refreshOnShow='true' style='display: none'>Incidents</a>";
    }


    echo "</div>";
}
mysql_free_result($contactresult);

include('htmlfooter.inc.php');
?>