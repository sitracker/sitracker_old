<?php
// allemail.php - Email addresses of customers
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author:   Ivan Lucas
// Email:    ivan.lucas@salfordsoftware.co.uk
// Comments: Email Addresses of Customers

// FIXME Not on menu

require('db_connect.inc.php');
require('functions.inc.php');

include('htmlheader.inc.php');

echo "<h2>Report: Email Address of Supported Customers</h2>";
echo "<h4><i>(Doesn't check for expiry)</i></h4>";

$sql  = "SELECT DISTINCT contacts.email ";
$sql .= "FROM contacts LEFT JOIN contactproducts ON contacts.id=contactproducts.contactid ";
$sql .= "WHERE dataprotection_email != 'Yes' ";
/*$sql.="WHERE productid='1' OR productid='77' OR productid='55' ";
*/
$sql.="ORDER BY email ASC ";
//$sql.="LIMIT 100";
$result=mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
$count=mysql_num_rows($result);
echo "<b>Found $count records</b><br><br>";
if($result)
{
    while(list($email)=mysql_fetch_row($result))
    {
        echo "$email";
        echo "<br />";
    }
}
else
{
    echo "Error: Failed to fetch contacts.";
}
mysql_free_result($result);
mysql_close($db);

include('htmlfooter.inc.php');

?>