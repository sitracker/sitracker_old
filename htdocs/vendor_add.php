<?php
// vendor_add.php - Form for adding software vendors
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

@include ('set_include_path.inc.php');
$permission = 56; // Add Software

require ('db_connect.inc.php');
require ('functions.inc.php');
// This page requires authentication
require ('auth.inc.php');

$title = $strAddVendor;

// External variables
$submit = $_REQUEST['submit'];

if (empty($submit))
{
    // Show form
    include ('htmlheader.inc.php');

    echo show_form_errors('add_vendor');
    clear_form_errors('add_vendor');
    echo "<h2>{$strAddVendor}</h2>";
    echo "<h5>".sprintf($strMandatoryMarked,"<sup class='red'>*</sup>")."</h5>";
    echo "<form action='{$_SERVER['PHP_SELF']}' method='post' onsubmit='return confirm_action(\"{$strAreYouSureAddVendor}\")'>";
    echo "<table align='center' class='vertical'>";
    echo "<tr><th>{$strVendor}<sup class='red'>*</sup></th><td><input maxlength='50' name='name' size='30' /></td></tr>\n";
    echo "</table>";
    echo "<p align='center'><input name='submit' type='submit' value='{$strSave}' /></p>";
    echo "<p class='warning'>{$strAvoidDupes}</p>";
    echo "</form>\n";
    echo "<p align='center'><a href='products.php'>{$strReturnWithoutSaving}</a></p>";
    include ('htmlfooter.inc.php');
}
else
{
    // External variables
    $name = cleanvar($_REQUEST['name']);
    $_SESSION['formdata'] = $_REQUEST;
    // Add new
    $errors = 0;

    // check for blank name
    if ($name == '')
    {
        $errors++;
        $_SESSION['formerrors']['name'] = $strMustEnterName;
    }

    // add product if no errors
    if ($errors == 0)
    {
        $sql = "INSERT INTO `{$dbVendors}` (name) VALUES ('$name')";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        if (!$result)
        {
            echo "<p class='error'>Addition of Vendor Failed\n";
        }
        else
        {
            $id=mysql_insert_id();
            journal(CFG_LOGGING_DEBUG, 'Vendor Added', "Vendor {$id} was added", CFG_JOURNAL_DEBUG, $id);
            html_redirect("products.php");
        }
        clear_form_data('add_vendor');
        clear_form_errors('add_vendor');
    }
    else
    {
        include ('htmlheader.inc.php');
        html_redirect($_SERVER['PHP_SELF'], FALSE);
    }
}
?>
