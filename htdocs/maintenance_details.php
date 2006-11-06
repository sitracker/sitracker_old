<?php
// maintenance_details.php - Show contract details
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
// Created: 20th August 2001
// Purpose: Show All Maintenance Contract Details
// This Page Is Valid XHTML 1.0 Transitional! 27Oct05

$permission=19;  // view Maintenance contracts

require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

$id = cleanvar($_REQUEST['id']);

include('htmlheader.inc.php');

// Display Maintenance
$sql  = "SELECT maintenance.*, maintenance.notes AS maintnotes, sites.name AS sitename FROM maintenance, sites ";
$sql .= "WHERE sites.id = maintenance.site AND maintenance.id='$id' ";
$maintresult = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

$maintrow=mysql_fetch_array($maintresult);
?>
<table align='center' class='vertical'>
<?php
echo "<tr><th>Contract ID:</th><td><h3>".$maintrow['id']."</h3></td></tr>";
echo "<tr><th>Status:</th><td>";
if ($maintrow['term']=='yes') echo '<strong>Terminated</strong>';
else echo 'Active';
if ($maintrow['expirydate']<$now) echo "<span class='expired'>, Expired</span>";
echo "</td></tr>";
echo "<tr><th>Site:</th><td><a href=\"site_details.php?id=".$maintrow['site']."\">".$maintrow['sitename']."</a></td></tr>";
echo "<tr><th>Admin Contact:</th><td><a href=\"contact_details.php?id=".$maintrow['admincontact']."\">".contact_realname($maintrow['admincontact'])."</a></td></tr>";
echo "<tr><th>Reseller:</th><td>".reseller_name($maintrow['reseller'])."</td></tr>";
echo "<tr><th>Product:</th><td>".product_name($maintrow['product'])."</td></tr>";
echo "<tr><th>Incidents:</th>";
echo "<td>";
$incidents_remaining = $maintrow['incident_quantity'] - $maintrow['incidents_used'];
if ($maintrow['incident_quantity']==0) echo "Unlimited Incidents ({$maintrow['incidents_used']} Used)";
elseif ($maintrow['incident_quantity']==1) echo "{$maintrow['incident_quantity']} Incident ($incidents_remaining Remaining)";
else echo "{$maintrow['incident_quantity']} Incidents ($incidents_remaining Remaining)";
echo "</td></tr>";
echo "<tr><th>License:</th><td>".$maintrow['licence_quantity'].' '.licence_type($maintrow['licence_type'])."</td></tr>";
echo "<tr><th>Service Level:</th><td>".servicelevel_name($maintrow['servicelevelid'])."</td></tr>";
echo "<tr><th>Expires:</th><td>".date("jS M Y", $maintrow['expirydate'])."</td></tr>";
echo "<tr><th>Notes:</th><td>".$maintrow['maintnotes']."</td></tr>";
?>
</table>
<?php
echo "<p align='center'><a href=\"edit_maintenance.php?action=edit&amp;maintid=$id\">Edit this contract</a></p>";

if (mysql_num_rows($maintresult)<1)
{
    throw_error('No contract found - with ID number:',$id);
}
?>
<h3>Supported Contacts:</h3>
<?php
$sql  = "SELECT contacts.forenames, contacts.surname, supportcontacts.contactid AS contactid FROM supportcontacts, contacts ";
$sql .= "WHERE supportcontacts.contactid=contacts.id AND supportcontacts.maintenanceid='$id' ";
$result=mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
if (mysql_num_rows($result)>0)
{
    ?>
    <table align='center'>
    <?php
    $supportcount=1;
    while ($supportedrow=mysql_fetch_array($result))
    {
        echo "<tr><th>Contact #$supportcount:</th><td><a href=\"contact_details.php?id=".$supportedrow['contactid']."\">".$supportedrow['forenames'].' '.$supportedrow['surname']."</a>, ";
        echo contact_site($supportedrow['contactid']). "</td>";
        echo "<td><a href=\"delete_maintenance_support_contact.php?contactid=".$supportedrow['contactid']."&amp;maintid=$id&amp;context=maintenance\">Remove</a></td></tr>\n";
        $supportcount++;
    }
    ?>
    </table>
    <?php
}
else
{
    echo "<p align='center'>This site has no supported contacts<p>";
}
?>
<p align='center'><a href="add_maintenance_support_contact.php?maintid=<?php echo $id; ?>&amp;siteid=<?php echo $maintrow['site'] ?>&amp;context=maintenance">Add a support contact to this contract</a></p>
<?php

echo "<br />";
echo "<h3>Software supported under this contract:</h3>";
// supported software
$sql = "SELECT * FROM softwareproducts, software WHERE softwareproducts.softwareid=software.id AND productid='{$maintrow['product']}' ";
$result=mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

if (mysql_num_rows($result)>0)
{
    ?>
    <table align='center'>
    <?php
    while ($software=mysql_fetch_array($result))
    {
        echo "<tr><td>{$software['name']}</td></tr>\n";
    }
    echo "</table>\n";
}
else
{
    echo "<p align='center'>None / Unknown<p>";
}
include('htmlfooter.inc.php');
?>
