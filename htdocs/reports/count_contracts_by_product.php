<?php
// count_contracts_by_product.php -
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Report Type: Maintenance

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

@include ('../set_include_path.inc.php');
$permission = 37; // Run Reports

require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

$title = $strCountContractsByProduct;

include ('htmlheader.inc.php');

echo "<h2>{$title}</h2>";

$sql = "SELECT * FROM `{$dbProducts}` ";
$result = mysql_query($sql);

while ($product = mysql_fetch_object($result))
{
    $csql = "SELECT COUNT(id) AS count FROM `{$dbMaintenance}` WHERE product = {$product->id} AND NOT term = 'yes' AND expirydate > $now";
    $cresult = mysql_query($csql);
    list($contract_count) = mysql_fetch_row($cresult);
    if ($contract_count > 0) $productlist[$product->id] = $contract_count;
}
arsort($productlist, SORT_NUMERIC);

echo "<table align='center'>";
echo "<tr><th>#</th><th>{$strProduct}</th><th>{$strContracts}</th></tr>\n";
$count = 1;
$shade = 'shade1';
foreach ($productlist AS $prod => $contcount)
{
    echo "<tr class='$shade'><td>{$count}</td><td>".product_name($prod)."</td><td><a href='../browse_contract.php?activeonly=yes&amp;productid={$prod}'>{$contcount}</a></td></tr>\n";
    $count++;
    if ($shade=='shade1') $shade='shade2';
    else $shade='shade1';
}
echo "</table>\n";
include ('htmlfooter.inc.php');

?>
