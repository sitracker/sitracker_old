<?php
// allnames.php - Names of all customers in alphabetical order, for duplicate finding
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

//  Author:   Ivan Lucas
//  Email:    ivanlucas[at]users.sourceforge.net
//  Comments: Names of all customers in alphabetical order, for duplicate finding

// Report Type: House Keeping
// FIXME Not on menu

@include ('../set_include_path.inc.php');
$permission = 37; // Run Reports

require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

include ('htmlheader.inc.php');


$sql  = "SELECT * ";
$sql.="FROM `{$dbContacts}` ";
$sql.="ORDER BY surname, forenames ASC ";
//$sql.="LIMIT 100";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);

$count = mysql_num_rows($result);
echo "<strong>Report showing all $count contact records</strong> - ".ldate(r)."<br /><br />";

if ($result)
{
    echo "<table summary=\"\" width=\"100%\">";
    while ($row=mysql_fetch_array($result))
    {
        echo "<tr>";
        echo "<td>";
        if ($lastsurname==$row['surname'] && $lastforenames==$row['forenames'])
        {
            echo "<em>".$row['id']."</em>";
        }
        else
        {
            echo $row['id'];
        }
        echo "</td>";
        echo "<td><strong>";
        echo $row['surname'];
        echo "</strong>, ".$row['forenames']."</td>";
        echo "<td>".$row['email']."</td>";
        echo "<td>".site_name($row['siteid'])."</td>";
        echo "<td>".contact_count_incidents($row['id'])." Incidents</td>";
        $lastsurname=$row['surname'];
        $lastforenames=$row['forenames'];
        echo "</tr>";
    }
    echo "</table>";
}
else
{
    echo "Error: Failed to fetch contacts.";
}
mysql_free_result($result);
mysql_close($db);

include ('htmlfooter.inc.php');
?>
