<?php
// products.php - List products
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
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

// External Variables
$productid = cleanvar($_REQUEST['productid']);
$display = cleanvar($_REQUEST['display']);

include('htmlheader.inc.php');

if (empty($productid) AND $display!='skills')
{
    $sql = "SELECT * FROM vendors ORDER BY name";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    if (mysql_num_rows($result) >= 1)
    {
        while ($vendor = mysql_fetch_object($result))
        {
            echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/product.png' width='32' height='32' alt='' /> ";
            echo "{$vendor->name}</h2>";
            $psql = "SELECT * FROM products WHERE vendorid='{$vendor->id}' ORDER BY name";
            $presult = mysql_query($psql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            if (mysql_num_rows($presult) >= 1)
            {
                echo "<table summary='List of products' align='center' width='95%'>";
                echo "<tr><th width='20%'>Product Name</th><th width='60%'>Description</th><th width='10%'>Linked Skills</th><th width='10%'>Active Contracts</th></tr>\n";
                $shade='shade1';
                while ($product = mysql_fetch_object($presult))
                {
                    // Count linked skills
                    $ssql = "SELECT COUNT(softwareid) FROM softwareproducts WHERE productid={$product->id}";
                    $sresult = mysql_query($ssql);
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
                    list($countlinked)=mysql_fetch_row($sresult);

                    // Count contracts
                    $ssql = "SELECT COUNT(id) FROM maintenance WHERE product='{$product->id}' AND term!='yes' AND expirydate > '{$now}'";
                    $sresult = mysql_query($ssql);
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
                    list($countcontracts)=mysql_fetch_row($sresult);

                    if ($countlinked < 1) $shade='urgent';
                    if ($countcontracts < 1) $shade='expired';
                    echo "<tr class='{$shade}'><td><a href='{$_SERVER['PHP_SELF']}?productid={$product->id}' name='{$product->id}'>{$product->name}</a></td>";
                    echo "<td>{$product->description}</td>";
                    echo "<td align='right'>{$countlinked}</td>";
                    echo "<td align='right'>";
                    if ($countcontracts > 0) echo "<a href='browse_maintenance.php?search_string=&amp;productid={$product->id}&amp;activeonly=yes'>{$countcontracts}</a>";
                    else echo "{$countcontracts}";
                    echo "</td>";
                    // FIXME
                    // echo "<td><a href='edit_product.php?id={$product->id}'>Edit</a> | <a href='delete_product.php?id={$product->id}'>Delete</a></td>";
                    echo "</tr>\n";
                    if ($shade=='shade1') $shade='shade2';
                    else $shade='shade1';
                }
                echo "</table>\n";
            }
            else echo "<p class='warning'>No products for this vendor</p>\n";
        }
    }
    else echo "<p class='error'>No vendors defined</p>";


    $sql = "SELECT software.* FROM software LEFT JOIN softwareproducts ON software.id=softwareproducts.softwareid WHERE softwareproducts.softwareid IS NULL";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    if (mysql_num_rows($result) >= 1)
    {
        echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/skill.png' width='32' height='32' alt='' /> Skills not linked</h2>";
        echo "<p align='center'>These skills are not linked to any product</p>";
        echo "<table summary='' align='center' width='55%'>";
        echo "<tr><th>Skill</th><th>Lifetime</th><th>Engineers</th><th>Incidents</th><th>Actions</th></tr>";
        while ($software = mysql_fetch_array($result))
        {
            $ssql = "SELECT COUNT(userid) FROM usersoftware, users WHERE usersoftware.userid = users.id AND users.status!=0 AND usersoftware.softwareid='{$software['id']}'";
            $sresult = mysql_query($ssql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
            list($countengineers) = mysql_fetch_row($sresult);

            $ssql = "SELECT COUNT(id) FROM incidents WHERE softwareid='{$software['id']}'";
            $sresult = mysql_query($ssql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
            list($countincidents) = mysql_fetch_row($sresult);

            echo "<tr class='$shade'><td><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/skill.png' width='16' height='16' alt='' /> ";
            echo "{$software['name']}</td>";
            echo "<td>";
            if ($software['lifetime_start'] > 1) echo date($CONFIG['dateformat_shortdate'],mysql2date($software['lifetime_start'])).' to ';
            else echo "&#8734;";
            if ($software['lifetime_end'] > 1) echo date($CONFIG['dateformat_shortdate'],mysql2date($software['lifetime_end']));
            elseif ($software['lifetime_start'] >1) echo "&#8734;";
            echo "</td>";
            echo "<td>{$countengineers}</td>";
            echo "<td>{$countincidents}</td>";
            echo "<td><a href='add_product_software.php?softwareid={$software['id']}'>Link</a> ";
            echo "| <a href='edit_software.php?id={$software['id']}'>Edit</a> ";
            echo "| <a href='edit_software.php?id={$software['id']}&amp;action=delete'>Delete</a>";
            echo "</td>";
            echo "</tr>\n";
            if ($shade=='shade1') $shade='shade2';
            else $shade='shade1';
        }
        echo "</table>";
    }
}
elseif (empty($productid) AND ($display=='skills' OR $display=='software'))
{
    echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/skill.png' width='32' height='32' alt='' /> Skills</h2>";
    $sql = "SELECT * FROM software ORDER BY name";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    if (mysql_num_rows($result) >= 1)
    {
        echo "<table align='center'>";
        echo "<tr><th>Skill</th><th>Lifetime</th><th>Linked to # Products</th><th>Engineers</th><th>Incidents</th><th>Actions</th></tr>";
        $shade='shade1';
        while ($software = mysql_fetch_object($result))
        {

            $ssql = "SELECT COUNT(userid) FROM usersoftware, users WHERE usersoftware.userid = users.id AND users.status!=0 AND usersoftware.softwareid='{$software->id}'";
            $sresult = mysql_query($ssql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
            list($countengineers) = mysql_fetch_row($sresult);

            // Count linked products
            $ssql = "SELECT COUNT(productid) FROM softwareproducts WHERE softwareid={$software->id}";
            $sresult = mysql_query($ssql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            list($countlinked)=mysql_fetch_row($sresult);

            $ssql = "SELECT COUNT(id) FROM incidents WHERE softwareid='{$software->id}'";
            $sresult = mysql_query($ssql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
            list($countincidents) = mysql_fetch_row($sresult);

            $lifetime_start=mysql2date($software->lifetime_start);
            $lifetime_end=mysql2date($software->lifetime_end);

            if ($countengineers < 1) $shade = "notice";
            if ($countlinked < 1) $shade = "urgent";
            if ($lifetime_start > $now OR ($lifetime_end > 1 AND $lifetime_end < $now)) $shade='expired';
            echo "<tr class='$shade'>";
            echo "<td>{$software->name}</td>";
            echo "<td>";
            if ($software->lifetime_start > 1) echo date($CONFIG['dateformat_shortdate'],$lifetime_start).' to ';
            else echo "&#8734;";
            if ($software->lifetime_end > 1) echo date($CONFIG['dateformat_shortdate'],$lifetime_end);
            elseif ($software->lifetime_start >1) echo "&#8734;";
            echo "</td>";
            echo "<td>{$countlinked}</td>";
            echo "<td>{$countengineers}</td>";
            echo "<td>{$countincidents}</td>";
            echo "<td><a href='add_product_software.php?softwareid={$software->id}'>Link</a> ";
            echo "| <a href='edit_software.php?id={$software->id}'>Edit</a> ";
            echo "| <a href='edit_software.php?id={$software->id}&amp;action=delete'>Delete</a>";
            echo "</td>";
            echo "</tr>\n";
            if ($shade=='shade1') $shade='shade2';
            else $shade='shade1';
        }
        echo "</table>";
    }
    else echo "<p class='warning'>No records to display</p>";

}
else
{
    $psql = "SELECT * FROM products WHERE id='{$productid}' LIMIT 1";
    $presult = mysql_query($psql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    if (mysql_num_rows($presult) >= 1)
    {
        while ($product = mysql_fetch_object($presult))
        {
            echo "<table summary='List of skills linked to product' align='center'>";
            echo "<tr><thead><th colspan='0'><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/product.png' width='32' height='32' alt='' /> Product: {$product->name} (<a href='edit_product.php?id={$product->id}'>Edit</a> | <a href='delete_product.php?id={$product->id}'>Delete</a>)</th></thead></tr>";
            if (!empty($product->description)) echo "<tr class='shade1'><td colspan='0'>".nl2br($product->description)."</td></tr>";

            $swsql = "SELECT * FROM softwareproducts, software WHERE softwareproducts.softwareid=software.id AND productid='{$product->id}' ORDER BY name";
            $swresult=mysql_query($swsql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

            if (mysql_num_rows($swresult) > 0)
            {
                echo "<tr><th>Skill</th><th>Lifetime</th><th>Engineers</th><th>Incidents</th><th>Actions</th></tr>";
                $shade='shade2';
                while ($software=mysql_fetch_array($swresult))
                {
                    $ssql = "SELECT COUNT(userid) FROM usersoftware, users WHERE usersoftware.userid = users.id AND users.status!=0 AND usersoftware.softwareid='{$software['id']}'";
                    $sresult = mysql_query($ssql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                    list($countengineers) = mysql_fetch_row($sresult);

                    $ssql = "SELECT COUNT(id) FROM incidents WHERE softwareid='{$software['id']}'";
                    $sresult = mysql_query($ssql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                    list($countincidents) = mysql_fetch_row($sresult);

                    echo "<tr class='$shade'><td><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/skill.png' width='16' height='16' alt='' /> ";
                    echo "{$software['name']}</td>";
                    echo "<td>";
                    if ($software['lifetime_start'] > 1) echo date($CONFIG['dateformat_shortdate'],mysql2date($software['lifetime_start'])).' to ';
                    else echo "&#8734;";
                    if ($software['lifetime_end'] > 1) echo date($CONFIG['dateformat_shortdate'],mysql2date($software['lifetime_end']));
                    elseif ($software['lifetime_start'] >1) echo "&#8734;";
                    echo "</td>";
                    echo "<td>{$countengineers}</td>";
                    echo "<td>{$countincidents}</td>";
                    echo "<td><a href='delete_product_software.php?productid={$product->id}&amp;softwareid={$software['softwareid']}'>Unlink</a> ";
                    echo "| <a href='edit_software.php?id={$software['softwareid']}'>Edit</a> ";
                    echo "| <a href='edit_software.php?id={$software['softwareid']}&amp;action=delete'>Delete</a>";
                    echo "</td>";
                    echo "</tr>\n";
                    if ($shade=='shade1') $shade='shade2';
                    else $shade='shade1';
                }
            }
            else
            {
                echo "<tr><td>&nbsp;</td><td><em>No skills linked to this product</em></td><td>&nbsp;</td></tr>\n";
            }
            echo "</table>\n";
            echo "<p align='center'><a href='add_product_software.php?productid={$product->id}'>Link skill to {$product->name}</a></p>\n";

            $sql = "SELECT * FROM maintenance WHERE product='{$product->id}' ORDER BY id DESC";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
            if (mysql_num_rows($result) >= 1)
            {
                echo "<h3>Related Contracts</h3>";
                echo "<table align='center'>";
                echo "<tr><th>Contract</th><th>Site</th></tr>";
                $shade = 'shade1';
                while ($contract = mysql_fetch_object($result))
                {
                    if ($contract->term=='yes' OR $contract->expirydate < $now) $shade = "expired";
                    echo "<tr class='{$shade}'>";
                    echo "<td><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/contract.png' width='16' height='16' alt='' /> ";
                    echo "<a href='maintenance_details.php?id={$contract->id}'>Contract {$contract->id}</a></td>";
                    echo "<td>".site_name($contract->site)."</td>";
                    echo "</tr>\n";
                    if ($shade=='shade1') $shade='shade2';
                    else $shade='shade1';
                }
                echo "</table>\n";
            }

            $sql = "SELECT * FROM incidents WHERE product={$product->id} ORDER BY id DESC";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
            if (mysql_num_rows($result) >= 1)
            {
                echo "<h3>Related Incidents</h3>";
                echo "<table align='center'>";
                echo "<tr><th>Incident</th><th>Contact</th><th>Site</th><th>Title</th></tr>";
                $shade = 'shade1';
                while ($incident = mysql_fetch_object($result))
                {
                    echo "<tr class='{$shade}'>";
                    echo "<td><a href=\"javascript:incident_details_window('{$incident->id}','incident{$incident->id}');\">Incident {$incident->id}</a></td>";
                    echo "<td>".contact_realname($incident->contact)."</td><td>".contact_site($incident->contact)."</td>";
                    echo "<td>".stripslashes($incident->title)."</td>";
                    echo "</tr>\n";
                    if ($shade=='shade1') $shade='shade2';
                    else $shade='shade1';
                }
                echo "</table>\n";

            }
        }

    }
    else echo "<p class='error'>No matching product</p>";

    echo "<p align='center'><a href='{$_SERVER['PHP_SELF']}#{$productid}'>Back to list of products</a></p>";
}

echo "<p align='center'><a href='add_vendor.php'>Add Vendor</a> | <a href='add_product.php'>Add Product</a> | <a href='add_software.php'>Add Skill</a>";
if ($display=='skills' OR $display=='software') echo " | <a href='products.php'>List Products</a>";
else echo " | <a href='products.php?display=skills'>List Skills</a>";
echo "</p>";

include('htmlfooter.inc.php');
?>
