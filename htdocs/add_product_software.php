<?php
// add_product_software.php - Associates software with a product
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

// This Page Is Valid XHTML 1.0 Transitional!  11Oct06

$permission=24;  // Add Product
require('db_connect.inc.php');
require('functions.inc.php');
$title="Associate skill with a product";
// This page requires authentication
require('auth.inc.php');

// External variables
$action = mysql_escape_string($_REQUEST['action']);
$productid = cleanvar($_REQUEST['productid']);
$softwareid = cleanvar($_REQUEST['softwareid']);
$context = cleanvar($_REQUEST['context']);
$return = cleanvar($_REQUEST['return']);

if (empty($action) OR $action == "showform")
{
    include('htmlheader.inc.php');
    ?>
    <h2>Link skill with a product</h2>
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>?action=add" method="post">
    <input type="hidden" name="context" value="<?php echo $context; ?>" />
    <?php
    if (empty($productid))
    {
        $name = db_read_column('name', 'software', $softwareid);
        echo "<h3><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/skill.png' width='16' height='16' alt='' /> Skill: $name</h3>";
        echo "<input name=\"softwareid\" type=\"hidden\" value=\"$softwareid\" />\n";
        echo "<p align='center'>Link Product: <img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/product.png' width='16' height='16' alt='' /> ";
        echo product_drop_down("productid", 0);
        echo "</p>";
    }
    else
    {
        $sql = "SELECT name FROM products WHERE id='$productid' ";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        list($product) = mysql_fetch_row($result);
        echo "<h3>Product: $product</h3>";
        echo "<input name=\"productid\" type=\"hidden\" value=\"$productid\" />\n";
    }
    if (empty($softwareid))
    {
        echo "<p align='center'>Link Skill: <img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/skill.png' width='16' height='16' alt='' /> ";
        echo software_drop_down("softwareid", 0);
        echo "</p>\n";
    }
    echo "<p align='center'><input name='submit' type='submit' value='Save Link' />";
    echo "<input type='checkbox' name='return' value='true' ";
    if ($return=='true') echo "checked='checked' ";
    echo "/> Return to this page after saving</p>\n";
    echo "</form>";

    echo "<p align='center'><a href='products.php?productid={$productid}'>Return to product without saving</a></p>";
    include('htmlfooter.inc.php');
}
elseif ($action == "add")
{
    $errors = 0;
    // check for blank
    if ($productid == 0)
    {
        $errors = 1;
        $errors_string .= "<p class='error'>You must select a product</p>\n";
    }
    // check for blank software id
    if ($softwareid == 0)
    {
        $errors = 1;
        $errors_string .= "<p class='error'>Skill ID cannot be blank</p>\n";
    }

    // add record if no errors
    if ($errors == 0)
    {
        // First have a look if we already have this link
        $sql = "SELECT productid FROM softwareproducts WHERE productid='$productid' AND softwareid='$softwareid'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        if (mysql_num_rows($result) >= 1)
        {
            confirmation_page("1", "add_product_software.php?productid={$productid}&return=$return", "<h2>Software Link Already Exists</h2><p align='center'>Please wait while you are redirected...</p>");
            exit;
        }

        $sql  = "INSERT INTO softwareproducts (productid, softwareid) VALUES ($productid, $softwareid)";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        // show error message if addition failed
        if (!$result)
        {
            include('htmlheader.inc.php');
            throw_error("Addition of skill/product failed",$sql);
            include('htmlfooter.inc.php');
        }
        // update db and show success message
        else
        {
            journal(CFG_LOGGING_NORMAL, 'Product Added', "Skill $softwareid was added to product $productid", CFG_JOURNAL_PRODUCTS, $productid);
            if ($return=='true') confirmation_page("1", "add_product_software.php?productid={$productid}&return=true", "<h2>Skill Linked to Product Successfully</h2><p align='center'>Please wait while you are returned...</p>");
            else confirmation_page("1", "products.php?productid={$productid}", "<h2>Skill Linked to Product Successfully</h2><p align='center'>Please wait while you are redirected...</p>");
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
