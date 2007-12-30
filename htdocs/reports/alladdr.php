<?php
// alladdr.php - Addresses of all supported customers
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author:   Paul Lees
// Email:    paul.lees[at]salfordsoftware.co.uk
// Comments: hack of Ivan's code, Addresses of ALL Supported Customers

@include ('../set_include_path.inc.php');

$permission = 37; // Run Reports

require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

include ('htmlheader.inc.php');

echo "<h2>Report: Address of ALL Supported Customers</h2>";
echo "<h3>(Doesn't check for expiry)</h3>";


$sql  = "SELECT DISTINCT c.address1, c.address2, c.city, c.county, c.country, c.postcode ";
$sql .= "FROM `{$dbContacts}` AS c LEFT JOIN `{$dbContactProducts}` ON c.id = `{$dbContactProducts}`.contactid ";
//$sql.="WHERE productid='1' OR productid='77' OR productid='55' ";
//$sql.="ORDER BY email ASC ";
//$sql.="LIMIT 100";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
$count = mysql_num_rows($result);
echo "<strong>Found $count records</strong><br /><br />";

if ($result)
{
    while ($address=mysql_fetch_array($result))
    {
        echo $address['address1'];
        echo "<br />";
        echo $address['address2'];
        echo "<br />";
        echo $address['city'];
        echo "<br />";
        echo $address['county'];
        echo "<br />";
        echo $address['country'];
        echo "<br />";
        echo $address['postcode'];
        echo "<hr />";
    }
}
else
{
    echo "Error: Failed to fetch contacts.";
}
mysql_free_result($result);


include ('htmlfooter.inc.php');
?>