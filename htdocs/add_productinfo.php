<?php
// add_product_info.php - Form to add product information
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// This Page Is Valid XHTML 1.0 Transitional!  16Dec05

// Product information is the info related to a product that is requested when adding an incident

$permission=25; // Add Product Info

require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

include('htmlheader.inc.php');
?>
<script type="text/javascript">
function confirm_submit()
{
    return window.confirm('Are you sure you want to add this product information?');
}
</script>
<?php
// Show add product information form
if (empty($_REQUEST['submit']))
{
    ?>
    <h2>Add Product Question</h2>
    <p align='center'>Mandatory fields are marked <sup class='red'>*</sup></p>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return confirm_submit()">
    <table align='center'>
    <tr><th>Product:</th><td><?php echo product_drop_down("product", 0) ?></td></tr>
    <tr><th>Question: <sup class='red'>*</sup></th><td><input name="information" size="30" /></td></tr>
    <tr><th>More Information: <sup class='red'>*</sup></th><td><input name="moreinformation" size="30" /></td></tr>
    </table>
    <p align='center'><input name="submit" type="submit" value="Add" /></p>
    </form>
    <?php
}
else
{
    // External variables
    $product = mysql_escape_string($_POST['product']);
    $information = cleanvar($_POST['information']);
    $moreinformation = cleanvar($_POST['moreinformation']);

    // Add product information
    $errors = 0;
    // check for blank product
    if ($product == 0)
    {
        $errors = 1;
        echo "<p class='error'>You must select a product</p>\n";
    }
    // check for blank information
    if ($information == "")
    {
        $errors = 1;
        echo "<p class='error'>You must enter some product information</p>\n";
    }

    // add product information if no errors
    if ($errors == 0)
    {
        $sql = "INSERT INTO productinfo (productid, information, moreinformation) ";
        $sql .= "VALUES ('$product', '$information', '$moreinformation')";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        if (!$result) echo "<p class='error'>Addition of product information failed\n";
        else
        {
            journal(CFG_LOGGING_NORMAL, 'Product Info Added', "Info was added to Product $product", CFG_JOURNAL_PRODUCTS, $product);
            confirmation_page("2", "products.php", "<h2>Product Information Added</h2><p align='center'>Please wait while you are redirected...</p>");
        }
    }
}
include('htmlfooter.inc.php');
?>
