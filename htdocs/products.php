<?php
// products.php - List products
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

$permission=28; // View Products and Software
$title='Products List';

require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

include('htmlheader.inc.php');

$sql = "SELECT * FROM vendors ORDER BY name";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

if (mysql_num_rows($result) >= 1)
{
    echo "<table summary='' align='center'>";
    while ($vendor = mysql_fetch_object($result))
    {
        echo "<tr><th colspan='3'><h3>{$vendor->name}</h3></th></tr>";
        $psql = "SELECT * FROM products WHERE vendorid='{$vendor->id}' ORDER BY name";
        $presult = mysql_query($psql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        while ($product = mysql_fetch_object($presult))
        {
            echo "<tr><td class='shade1'><a href='edit_product.php?id={$product->id}'>Edit</a></td>";
            echo "<td class='shade1' colspan='2'>";
            echo "<strong>{$product->name}</strong><br />";
            echo "</td></tr>";
            if (!empty($product->description))
                echo "<tr><td class='shade1'>&nbsp;</td><td class='shade1' colspan='2'>".nl2br($product->description)."</td></tr>\n";

            $swsql = "SELECT * FROM softwareproducts, software WHERE softwareproducts.softwareid=software.id AND productid='{$product->id}' ORDER BY name";
            $swresult=mysql_query($swsql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

            if (mysql_num_rows($swresult) > 0)
            {
                while ($software=mysql_fetch_array($swresult))
                {
                    echo "<tr><td colspan='2'>{$software['name']}&nbsp; ";
                    echo "(<a href='delete_product_software.php?productid={$product->id}&amp;softwareid={$software['softwareid']}'>Remove</a>)</td></tr>\n";
                }
            }
            else
            {
                echo "<tr><td>&nbsp;</td><td><em>No supported software associated</em></td><td>&nbsp;</td></tr>\n";
            }
            echo "<tr><td>&nbsp;</td><td >&nbsp;</td><td><a href='add_product_software.php?productid={$product->id}'>Insert</a></td></tr>\n";
        }
    }
    echo "</table>\n";
}
else echo "<p class='error'>No software vendors defined</p>";

echo "<p align='center'><a href='add_vendor.php'>Add Vendor</a> | <a href='add_product.php'>Add Product</a> | <a href='add_software.php'>Add Software</a></p>";

include('htmlfooter.inc.php');
?>
