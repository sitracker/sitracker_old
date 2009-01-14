<?php
// allemail.php - Email addresses of customers
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author:   Ivan Lucas
// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
// Comments: Email Addresses of Customers

// Report Type: House Keeping
// FIXME Not on menu

@include ('../set_include_path.inc.php');

$permission = 37; // Run Reports

require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

include ('htmlheader.inc.php');

echo "<h2>Report: Email Address of Supported Customers</h2>";
echo "<h4><em>(Doesn't check for expiry)</em></h4>";

$sql  = "SELECT DISTINCT c.email ";
$sql .= "FROM `{$dbContacts}` AS c LEFT JOIN `{$dbSupportContacts}` ON c.id = `{$dbSupportContacts}`.contactid ";
$sql .= "WHERE dataprotection_email != 'Yes' ";
$sql .="ORDER BY email ASC ";
//$sql.="LIMIT 100";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
$count = mysql_num_rows($result);
echo "<strong>Found $count records</strong><br /><br />";
if ($result)
{
    while (list($email) = mysql_fetch_row($result))
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

include ('htmlfooter.inc.php');

?>
