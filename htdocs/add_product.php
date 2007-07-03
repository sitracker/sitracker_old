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
    <h2>Add New Product</h2>
    <p align='center'>Mandatory fields are marked <sup class='red'>*</sup></p>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return confirm_submit()">
    <table align='center'>
    <tr><th>Vendor: <sup class='red'>*</sup></th><td><?php echo vendor_drop_down('vendor', 0); ?></td></tr>
    <tr><th>Product Name: <sup class='red'>*</sup></th><td><input maxlength="50" name="name" size="40" /></td></tr>
    <?php
    echo "<tr><th>Description:</th>";
    echo "<td>";
    echo "<textarea name='description' cols='40' rows='6'></textarea>";
    echo "</td></tr>";
    ?>
    </table>
    <p><input name="submit" type="submit" value="Add Product" /></p>
    <p class='warning'>Please check that the product does not already exist <em>before</em> adding it</p>
    </form>
    <?php
    echo "<p align='center'><a href='products.php'>Return to products list without saving</a></p>";
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

            confirmation_page("2", "products.php", "<h2>Product Addition Successful</h2><p align='center'>Please wait while you are redirected...</p>");
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
