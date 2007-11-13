<?php
// browse_contracts.php - List of contracts
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

// This Page Is Valid XHTML 1.0 Transitional! 27Oct05

$permission=19; // View Maintenance Contracts
$title='Browse Contracts';
require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

// External variables
$productid = cleanvar($_REQUEST['productid']);
$search_string = cleanvar($_REQUEST['search_string']);
$sort = cleanvar($_REQUEST['sort']);
$order = cleanvar($_REQUEST['order']);
$activeonly = cleanvar($_REQUEST['activeonly']);

include('htmlheader.inc.php');
?>
<script type="text/javascript" src="scripts/dojo/dojo.js"></script>
<script type="text/javascript">
    dojo.require("dojo.widget.ComboBox");
</script>
<?php
echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/contract.png' width='32' height='32' alt='' /> ";
echo "Browse Maintenance</h2>";
?>
<table summary="alphamenu" align="center">
<tr>
<td align="center">
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
Browse Contracts by Site (or ID): <!--<input type="text" name="search_string" />-->
<input dojoType='ComboBox' dataUrl='autocomplete.php?action=sites' style='width: 300px;' name='search_string' />
<?php
echo "<label><input type='checkbox' name='activeonly' value='yes' ";
if ($activeonly=='yes') echo "checked='checked' ";
echo "/> Show Active Only</label>";
?>
<br />
and/or by product:
<?php
echo product_drop_down('productid', $productid)
?>
<input type="submit" value="Go" />
</form>
</td>
</tr>
<tr>
<td valign="middle">
    <a href="add_maintenance.php">Add Contract</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=A">A</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=B">B</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=C">C</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=D">D</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=E">E</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=F">F</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=G">G</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=H">H</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=I">I</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=J">J</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=K">K</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=L">L</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=M">M</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=N">N</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=O">O</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=P">P</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=Q">Q</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=R">R</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=S">S</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=T">T</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=U">U</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=V">V</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=W">W</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=X">X</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=Y">Y</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=Z">Z</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=*">All</a>
</td>
</tr>
</table>

<?php

// check input
/*
if (empty($search_string) && empty($productid))
{
    $errors = 1;
    echo "<p class='error'>You must enter a search string</p>\n";
}
*/

// search for criteria
$sql  = "SELECT maintenance.id AS maintid, sites.name AS site, products.name AS product, resellers.name AS reseller, licence_quantity, ";
$sql .= "licencetypes.name AS licence_type, expirydate, admincontact, contacts.forenames AS admincontactforenames, contacts.surname AS admincontactsurname, maintenance.notes, sites.id AS siteid, ";
$sql .= "maintenance.term AS term, maintenance.productonly AS productonly ";
$sql .= "FROM maintenance, sites, contacts, products, licencetypes, resellers ";
$sql .= "WHERE (maintenance.site=sites.id AND product=products.id AND reseller=resellers.id AND licence_type=licencetypes.id AND admincontact=contacts.id) ";
if ($activeonly=='yes') $sql .= "AND term!='yes' AND expirydate > $now ";
if ($search_string != '*')
{
    if (strlen($search_string)==1)
    {
        $sql .= "AND SUBSTRING(sites.name,1,1)=('$search_string') ";
    }
    else
    {
        $sql .= "AND (sites.name LIKE '%$search_string%' ";
        $sql .= "OR maintenance.id = '$search_string') ";
    }

    if ($productid)
    {
        $sql .= "AND maintenance.product='$productid' ";
    }
}
if (!empty($sort))
{
    if ($sort=='expiry') $sql .= "ORDER BY expirydate ";
    elseif ($sort=='id') $sql .= "ORDER BY maintenance.id ";
    elseif ($sort=='product') $sql .= " ORDER BY products.name ";
    elseif ($sort=='site') $sql .= " ORDER BY sites.name ";
    elseif ($sort=='reseller') $sql .= " ORDER BY resellers.name ";
    else $sql .= " ORDER BY sites.name ";

    if ($order=='a' OR $order=='ASC' OR $order='') $sql .= "ASC";
    else $sql .= "DESC";
}

$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

if (mysql_num_rows($result) == 0)
{
    echo "<p align='center'>Sorry, unable to find any maintenance contracts";
    if (!empty($search_string)) echo " matching '<em>{$search_string}</em>";
    echo "</p>\n";
}
else
{
    ?>
    <script type="text/javascript">

    function contact_details_window(contactid)
    {
        URL = "contact_details.php?contactid=" + contactid;
        window.open(URL, "contact_details_window", "toolbar=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=450,height=240");
    }
    </script>
    <?php
    echo "<p align='center'>Displaying ".mysql_num_rows($result)." contract(s)";
    if (!empty($search_string)) echo " matching '<em>{$search_string}</em>";
    if ($productid) echo " where product matches <em>'".product_name($productid)."'</em>";
    echo "</p>\n";
    ?>
    <table align='center' style='width: 95%;'>
    <tr>
        <?php
        $filter=array('search_string' => $search_string,
                      'productid' => $productid);
        echo colheader('id', 'ID', $sort, $order, $filter);
        echo colheader('product', 'Product', $sort, $order, $filter);
        echo colheader('site', 'Site', $sort, $order, $filter);
        echo colheader('reseller', 'Reseller', $sort, $order. $filter);
        echo "<th>Licence</th>";
        echo colheader('expiry', 'Expiry Date', $sort, $order, $filter);
        echo "<th width='200'>Notes</th>";
    echo "</tr>\n";
    $shade = 0;
    while ($results = mysql_fetch_array($result))
    {
        // define class for table row shading
        if ($results['expirydate']<$now || ( $results['term']=='yes' AND $results['productonly']=='no'))
        {
            $class = 'expired';
        }
        elseif ($results['productonly']=='yes')
        {
            $class = 'notice';
        }
        else
        {
            if ($shade) $class = "shade1";
            else $class = "shade2";
        }
        ?>
        <tr class='<?php echo $class ?>'>
        <td><a href="maintenance_details.php?&amp;id=<?php echo $results['maintid'] ?>">Contract <?php echo $results["maintid"] ?></a></td>
        <td><?php echo $results["product"] ?></td>
        <?php
        echo "<td><a href='site_details.php?id={$results['siteid']}#contracts'>".htmlspecialchars($results['site'])."</a><br />";
        echo "Admin: <a href='contact_details.php?mode=popup&amp;id={$results['admincontact']}' target='_blank'>{$results['admincontactforenames']} {$results['admincontactsurname']}</a></td>";
        ?>

        <td><?php echo $results["reseller"] ?></td>
        <td><?php echo $results["licence_quantity"] ?> <?php echo $results["licence_type"] ?></td>
        <td><?php echo date("jS M Y", $results["expirydate"]); ?></td>

        <td><?php if ($results["notes"] == "") echo "&nbsp;"; else echo nl2br($results["notes"]); ?></td>
        </tr>
        <?php
        // invert shade
        if ($shade == 1) $shade = 0;
        else $shade = 1;
    }
    ?>
    </table>
    <?php
    // free result and disconnect
    mysql_free_result($result);
    include('htmlfooter.inc.php');
}
?>
