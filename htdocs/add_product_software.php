<?php
// add_product_software.php - Associates software with a product
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas

$permission=24;  // Add Product
require('db_connect.inc.php');
require('functions.inc.php');
$title="Associate software with a product";
// This page requires authentication
require('auth.inc.php');

// Valid user, check permissions
if (!user_permission($sit[2],$permission))
{
    header("Location: noaccess.php?id=$permission");
    exit;
}

// External variables
$action = mysql_escape_string($_REQUEST['action']);
$productid = cleanvar($_REQUEST['productid']);
$softwareid = cleanvar($_REQUEST['softwareid']);
$contactid = cleanvar($_REQUEST['contactid']);
$context = cleanvar($_REQUEST['context']);


if (empty($action) OR $action == "showform")
{
    include('htmlheader.inc.php');
    ?>
    <h2>Link software with a product</h2>
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>?action=add" method="post">
    <input type="hidden" name="context" value="<?php echo $context ?>" />
    <table class='vertical'>
    <?php
    if (empty($productid))
    {
        ?>
        <tr><th>Product:</th><td><?php echo product_drop_down("productid", 0) ?></td></tr>
        <?php
    }
    else
    {
        $sql = "SELECT name FROM products WHERE id='$productid' ";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        list($product) = mysql_fetch_row($result);
        echo "<h3>$product</h3>";
        echo "<input name=\"productid\" type=\"hidden\" value=\"$productid\" />\n";
    }

    if (empty($softwareid))
    {
        ?>
        <tr><th>Link Software:</hd><td><?php echo software_drop_down("softwareid", 0) ?></td></tr>
        <?php
    }
    else
    {
        echo "<tr><th>Contact:</th><td>$contactid - ".contact_realname($contactid).", ".site_name(contact_site($contactid))."</td></tr>";
    }
    ?>
    </table>
    <p align='center'><input name="submit" type="submit" value="Save Link" /></p>
    </form>
    <?php
    include('htmlfooter.inc.php');
}
elseif ($action == "add")
{
    // Add support contact

    $errors = 0;
    // check for blank contact
    if ($productid == 0)
    {
        $errors = 1;
        $errors_string .= "<p class='error'>You must select a product</p>\n";
    }
    // check for blank software id
    if ($softwareid == 0)
    {
        $errors = 1;
        $errors_string .= "<p class='error'>Something weird has happened, better call technical support</p>\n";
    }

    // add record if no errors
    if ($errors == 0)
    {
        $sql  = "INSERT INTO softwareproducts (productid, softwareid) VALUES ($productid, $softwareid)";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        // show error message if addition failed
        if (!$result)
        {
            include('htmlheader.inc.php');
            throw_error("Addition of software product failed",$sql);
            include('htmlfooter.inc.php');
        }
        // update db and show success message
        else
        {
            journal(CFG_LOGGING_NORMAL, 'Product Added', "Software $softwareid was added to product $productid", CFG_JOURNAL_PRODUCTS, $productid);
            confirmation_page("1", "add_product_software.php?productid={$productid}", "<h2>Software Linked to Product Successfully</h2><p align='center'>Please wait while you are redirected...</p>");
        }
    }
    else
    {
        // show error message if errors
        include('htmlheader.inc.php');
        echo $errors_string;
        include('htmlfooter.inc.php');
    }
}
?>