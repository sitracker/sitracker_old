<?php
// add_product.php - Form to add products
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

$permission=24; // Add Product

require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

$title = $strAddProduct;

// External variables
$submit = $_REQUEST['submit'];

if (empty($submit))
{
    // Show add product form
    include('htmlheader.inc.php');
    ?>
    <script type="text/javascript">
    function confirm_submit()
    {
        return window.confirm('Are you sure you want to add this product?');
    }
    </script>
    <?php
    echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/product.png' width='32' height='32' alt='' /> ";
    echo "{$strNewProduct}</h2>";
    echo "<h5>".sprintf($strMandatoryMarked, "<sup class='red'>*</sup>")."</h5>";
    echo "<form action='{$_SERVER['PHP_SELF']}' method='post' onsubmit='return confirm_submit();'>";
    echo "<table align='center'>";
    echo "<tr><th>{$strVendor}: <sup class='red'>*</sup></th><td>".vendor_drop_down('vendor', 0)."</td></tr>\n";
    echo "<tr><th>{$strProduct}: <sup class='red'>*</sup></th><td><input maxlength='50' name='name' size='40' /></td></tr>\n";
    echo "<tr><th>{$strDescription}:</th>";
    echo "<td>";
    echo "<textarea name='description' cols='40' rows='6'></textarea>";
    echo "</td></tr>";
    echo "</table>\n";
    echo "<p><input name='submit' type='submit' value=\"{$strAddProduct}\" /></p>";
    echo "<p class='warning'>{$strAvoidDupes}</p>";
    echo "</form>\n";
    echo "<p align='center'><a href='products.php'>{$strReturnWithoutSaving}</a></p>";
    include('htmlfooter.inc.php');
}
else
{
    // External variables
    $name = cleanvar($_REQUEST['name']);
    $vendor = cleanvar($_REQUEST['vendor']);
    $description = cleanvar($_REQUEST['description']);

    // Add New
    $errors = 0;

    // check for blank name
    if ($name == "")
    {
        $errors = 1;
        $errors_string .= "<p class='error'>You must enter a name</p>\n";
    }
    // add product if no errors
    if ($errors == 0)
    {
        $sql = "INSERT INTO products (name, vendorid, description) VALUES ('$name', '$vendor', '$description')";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        if (!$result) echo "<p class='error'>Addition of Product Failed\n";
        else
        {
            $id=mysql_insert_id();
            journal(CFG_LOGGING_NORMAL, 'Product Added', "Product $id was added", CFG_JOURNAL_PRODUCTS, $id);

            confirmation_page("2", "products.php", "<h2>Product Addition Successful</h2><p align='center'>{$strPleaseWaitRedirect}...</p>");
        }
    }
    else
    {
        include('htmlheader.inc.php');
        echo $errors_string;
        include('htmlfooter.inc.php');
    }
}
?>
