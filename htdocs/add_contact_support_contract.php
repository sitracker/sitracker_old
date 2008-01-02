<?php
// add_maintenance_support_contract.php - Associates a contact with a contract
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

@include('set_include_path.inc.php');
$permission=32;  // Edit Supported Products
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

// External Variables
$maintid = cleanvar($_REQUEST['maintid']);
$contactid = cleanvar($_REQUEST['contactid']);
$context = cleanvar($_REQUEST['context']);
$action = $_REQUEST['action'];

// Valid user, check permissions
if (empty($action) || $action == "showform")
{
    $title="Associate person with Contract"; // TODO i18n
    include('htmlheader.inc.php');
    echo "<h2>Link a contract with a support contact</h2>"; //TODO i18n
    echo "<form action='{$_SERVER['PHP_SELF']}?action=add' method='post'>";
    echo "<input type='hidden' name='context' value='{$context}' />";
    echo "<table align='center' class='vertical'>";

    if (empty($maintid))
    {
        echo "<tr><th>{$strContract} <img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/contract.png' width='16' height='16' alt='' /></th>";
        echo "<td width='400'>";
        maintenance_drop_down("maintid", 0);
        echo "</td></tr>";
    }
    else
    {
        $sql = "SELECT sites.name, products.name FROM maintenance, sites, products WHERE maintenance.site=sites.id ";
        $sql .= "AND maintenance.product=products.id AND maintenance.id='$maintid'";
        $result=mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        list($sitename, $product)=mysql_fetch_row($result);

        echo "<tr><th>{$strContract} <img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/contract.png' width='16' height='16' alt='' /></th><td>$maintid - $sitename, $product</td></tr>";
        echo "<input name=\"maintid\" type=\"hidden\" value=\"$maintid\" />";
    }

    if (empty($contactid))
    {
        echo "<tr><th>{$strContact} <img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/contact.png' width='16' height='16' alt='' /></th>";
        echo "<td>".contact_drop_down("contactid", 0, TRUE)."</td></tr>";
    }
    else
    {
        echo "<tr><th>{$strContact} <img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/contact.png' width='16' height='16' alt='' /></th><td>$contactid - ".contact_realname($contactid).", ".site_name(contact_site($contactid));
        echo "<input name=\"contactid\" type=\"hidden\" value=\"$contactid\" />";
        echo "</td></tr>";
    }
    echo "</table>";
    echo "<p align='center'><input name='submit' type='submit' value='{$strContinue}' /></p>";
    echo "</form>";

    include('htmlfooter.inc.php');
}
else if ($action == "add")
{
    // Add support contact
    $errors = 0;
    // check for blank contact
    if ($contactid == 0)
    {
        $errors = 1;
        $errors_string .= "<p class='error'>You must select a contact</p>\n";
    }
    // check for blank maintenance id
    if ($maintid == 0)
    {
        $errors = 1;
        $errors_string .= "<p class='error'>Something weird has happened, better call technical support</p>\n";
    }

    $sql = "SELECT * FROM supportcontacts WHERE maintenanceid = '{$maintid}' AND contactid = '{$contactid}'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

    if(mysql_num_rows($result) > 0)
    {
        $errors = 1;
        $errors_string .= "<p class='error'>A contact can only be listed once per support contract</p>\n";
    }

    // add maintenance support contact if no errors
    if ($errors == 0)
    {
        $sql  = "INSERT INTO supportcontacts (maintenanceid, contactid) VALUES ($maintid, $contactid)";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        // show error message if addition failed
        if (!$result)
        {
            include('htmlheader.inc.php');
            echo "<p class='error'>Addition of support contact failed\n";
            include('htmlfooter.inc.php');
        }
        // update database and show success message
        else
        {
            if ($context=='contact') html_redirect("contact_details.php?id=$contactid");
            else html_redirect("contract_details.php?id=$maintid");
        }
    }
    else
    {
        // show error message if errors
        include('htmlheader.inc.php');
        echo $errors_string;

        echo "<p align='center'><a href='contract_details.php?id={$maintid}'>Return</a></p>";
        include('htmlfooter.inc.php');
    }
}
?>
