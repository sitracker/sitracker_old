<?php
// maintenance_details.php - Show contract details
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
// Created: 20th August 2001
// Purpose: Show All Maintenance Contract Details
// This Page Is Valid XHTML 1.0 Transitional! 27Oct05

@include('set_include_path.inc.php');
$permission=19;  // view Maintenance contracts
// FIXME i18n some compound strings

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

$maintrow = mysql_fetch_array($maintresult);

echo "<table align='center' class='vertical'>";
echo "<tr><th>{$strContract} {$strID}:</th><td><h3><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/contract.png' width='32' height='32' alt='' /> ";
echo "{$maintrow['id']}</h3></td></tr>";
echo "<tr><th>{$strStatus}:</th><td>";
if ($maintrow['term']=='yes') echo "<strong>{$strTerminated}</strong>";
else echo $strActive;
if ($maintrow['expirydate']<$now AND $maintrow['expirydate'] != '-1') echo "<span class='expired'>, {$strExpired}</span>";
echo "</td></tr>\n";
echo "<tr><th>{$strSite}:</th><td><a href=\"site_details.php?id=".$maintrow['site']."\">".$maintrow['sitename']."</a></td></tr>";
echo "<tr><th>{$strAdminContact}:</th><td><a href=\"contact_details.php?id=".$maintrow['admincontact']."\">".contact_realname($maintrow['admincontact'])."</a></td></tr>";
if ($maintrow['reseller'] != '0') echo "<tr><th>{$strReseller}:</th><td>".reseller_name($maintrow['reseller'])."</td></tr>";
echo "<tr><th>{$strProduct}:</th><td>".product_name($maintrow['product'])."</td></tr>";
echo "<tr><th>{$strIncidents}:</th>";
echo "<td>";
$incidents_remaining = $maintrow['incident_quantity'] - $maintrow['incidents_used'];
if ($maintrow['incident_quantity']==0) echo "Unlimited Incidents ({$maintrow['incidents_used']} Used)"; // FIXME i18n unlimited used
elseif ($maintrow['incident_quantity']==1) echo "{$maintrow['incident_quantity']} Incident ($incidents_remaining Remaining)";
else echo "{$maintrow['incident_quantity']} Incidents ($incidents_remaining Remaining)";
echo "</td></tr>";
if ($maintrow['licence_quantity'] != '0')
{
    echo "<tr><th>{$strLicense}:</th><td>".$maintrow['licence_quantity'].' '.licence_type($maintrow['licence_type'])."</td></tr>\n";
}
echo "<tr><th>{$strServiceLevel}:</th><td>".servicelevel_name($maintrow['servicelevelid'])."</td></tr>";
echo "<tr><th>{$strExpiryDate}:</th><td>";
if ($maintrow['expirydate'] == '-1')
{
    echo "{$strUnlimited}";
}
else
{
    date($CONFIG['dateformat_date'], $maintrow['expirydate']);
}
echo "</td></tr>";
if ($maintrow['maintnotes'] != '')
    echo "<tr><th>{$strNotes}:</th><td>".$maintrow['maintnotes']."</td></tr>";
echo "</table>";
echo "<p align='center'><a href=\"edit_contract.php?action=edit&amp;maintid=$id\">{$strEditContract}</a></p>";

if (mysql_num_rows($maintresult)<1)
{
    throw_error('No contract found - with ID number:',$id);
}
echo "<h3>{$strSupportedContacts}:</h3>";

//All site contacts are supported
if ($maintrow['allcontactssupported'] == 'Yes')
{
    echo "<p class='info'>{$strAllSiteContactsSupported}</p>";
}
//else count
else
{
    $allowedcontacts = $maintrow['supportedcontacts'];


    $sql  = "SELECT contacts.forenames, contacts.surname, supportcontacts.contactid AS contactid FROM supportcontacts, contacts ";
    $sql .= "WHERE supportcontacts.contactid=contacts.id AND supportcontacts.maintenanceid='$id' ";
    $result=mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    if (mysql_num_rows($result)>0)
    {
        $numberofcontacts = mysql_num_rows($result);
        if ($numcontacts > $allowedcontacts) echo "<p class='error'>There are more contacts linked than this contract should support</p>";
        if ($allowedcontacts == 0) $allowedcontacts = $strUnlimited;
        echo "<p align='center'>".sprintf($strUsedNofN, $numberofcontacts, $allowedcontacts)."</p>\n";
        echo "<table align='center'>";
        $supportcount=1;
        while ($supportedrow=mysql_fetch_array($result))
        {
            echo "<tr><th>{$strContact} #$supportcount:</th><td><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/contact.png' width='16' height='16' alt='' /> ";
            echo "<a href=\"contact_details.php?id={$supportedrow['contactid']}\">{$supportedrow['forenames']} {$supportedrow['surname']}</a>, ";
            echo contact_site($supportedrow['contactid']). "</td>";
            echo "<td><a href=\"delete_maintenance_support_contact.php?contactid=".$supportedrow['contactid']."&amp;maintid=$id&amp;context=maintenance\">{$strRemove}</a></td></tr>\n";
            $supportcount++;
        }
        echo "</table>";
        }
        else
        {
            echo "<p align='center'>{$strNoRecords}<p>";
        }
}
if ($numberofcontacts < $allowedcontacts OR $allowedcontacts == 0)
{
    echo "<p align='center'><a href='add_contact_support_contract.php?maintid={$id}&amp;siteid={$maintrow['site']}&amp;context=maintenance'>";
    echo "{$strAddContact}</a></p>";
}
echo "<br />";
echo "<h3>{$strSkillsSupportedUnderContract}:</h3>";
// supported software
$sql = "SELECT * FROM softwareproducts, software WHERE softwareproducts.softwareid=software.id AND productid='{$maintrow['product']}' ";
$result=mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

if (mysql_num_rows($result)>0)
{
    echo "<table align='center'>";
    while ($software=mysql_fetch_array($result))
    {
        echo "<tr><td> <img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/skill.png' width='16' height='16' alt='' /> ";
        if ($software->lifetime_end > 0 AND $software->lifetime_end < $now) echo "<span class='deleted'>";
        echo $software['name'];
        if ($software->lifetime_end > 0 AND $software->lifetime_end < $now) echo "</span>";
        echo "</td></tr>\n";
    }
    echo "</table>\n";
}
else
{
    echo "<p align='center'>{$strNone} / {$strUnknown}<p>";
}
include('htmlfooter.inc.php');
?>