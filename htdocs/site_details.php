<?php
// site_details.php - Show all site details
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
// Created: 9th March 2001
// This Page Is Valid XHTML 1.0 Transitional! 27Oct05

$permission=11; // View Sites
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

// External variables
$id=cleanvar($_REQUEST['id']);

include('htmlheader.inc.php');

if ($id=='')
{
    echo "<p class='error'>You must select a site</p>";
    exit;
}

// Display site
echo "<table align='center' class='vertical'>";
$sql="SELECT * FROM sites WHERE id='$id' ";
$siteresult = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
while ($siterow=mysql_fetch_array($siteresult))
{
    echo "<tr><th>{$strSite}:</th><td><h3><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/site.png' width='32' height='32' alt='' /> ".$siterow['name']."</h3>";
    echo "</td></tr>";
    if ($siterow['active']=='false')
    {
        echo "<tr><th>{$strStatus}:</th><td><span class='expired'>{$strInactive}</span></td></tr>";
    }
    $tags = list_tags($id, 3, TRUE);
    if (!empty($tags)) echo "<tr><th>{$strTags}:</th><td>{$tags}</td></tr>";
    echo "<tr><th>{$strDepartment}:</th><td>".stripslashes($siterow['department'])."</td></tr>";
    echo "<tr><th>{$strAddress1}:</th><td>".stripslashes($siterow['address1'])."</td></tr>";
    echo "<tr><th>{$strAddress2}:</th><td>".stripslashes($siterow['address2'])."</td></tr>";
    echo "<tr><th>{$strCity}:</th><td>".stripslashes($siterow['city'])."</td></tr>";
    echo "<tr><th>{$strCounty}:</th><td>".stripslashes($siterow['county'])."</td></tr>";
    echo "<tr><th>{$strCountry}:</th><td>".stripslashes($siterow['country'])."</td></tr>";
    echo "<tr><th>{$strPostcode}:</th><td>".stripslashes($siterow['postcode'])."</td></tr>";
    echo "<tr><th>{$strTelephone}:</th><td>".stripslashes($siterow['telephone'])."</td></tr>";
    echo "<tr><th>{$strFax}:</th><td>".stripslashes($siterow['fax'])."</td></tr>";
    echo "<tr><th>{$strEmail}:</th><td><a href=\"mailto:".$siterow['email']."\">".$siterow['email']."</a></td></tr>";
    echo "<tr><th>{$strWebsite}:</th><td>";
    if (!empty($siterow['websiteurl'])) echo "<a href='".stripslashes($siterow['websiteurl'])."'>".stripslashes($siterow['websiteurl'])."</a>";
    echo "</td></tr>";
    echo "<tr><th>{$strNotes}:</th><td>".nl2br(stripslashes($siterow['notes']))."</td></tr>";
    echo "<tr><td colspan='2'>&nbsp;</td></tr>";
    echo "<tr><th>{$strIncidents}:</th><td>See <a href=\"contact_support.php?id=".$siterow['id']."&amp;mode=site\">here</a></td></tr>";
    echo "<tr><th>Site Incident Pool:</th><td>{$siterow['freesupport']} Incidents remaining</td></tr>";
    echo "<tr><th>{$strSalesperson}:</th><td>";
    if ($siterow['owner']>=1) echo user_realname($siterow['owner'],TRUE);
    else echo $strNotSet;
    echo "</td></tr>\n";
}
mysql_free_result($siteresult);

plugin_do('site_details');

echo "</table>\n";
echo "<p align='center'><a href='edit_site.php?action=edit&amp;site={$id}'>{$strEdit}</a> | ";
echo "<a href='delete_site.php?id={$id}'>{$strDelete}</a>";
echo "</p>";

// Display Contacts
echo "<h3>{$strContacts}</h3>";

// List Contacts
$sql="SELECT * FROM contacts WHERE siteid='$id' ORDER BY surname, forenames";
$contactresult = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
$countcontacts = mysql_num_rows($contactresult);
if ($countcontacts > 0)
{
    echo "<p align='center'>".sprintf($strContactsMulti, $countcontacts)."</p>";
    echo "<table align='center'>";
    echo "<tr><th>{$strName}</th><th>{$strJobTitle}</th><th>{$strDepartment}</th><th>{$strTelephone}</th><th>{$strEmail}</th><th>{$strAddress}</th><th>{$strDataProtection}</th><th>{$strNotes}</th></tr>";
    $shade='shade1';
    while ($contactrow=mysql_fetch_array($contactresult))
    {
        if ($contactrow['active']=='false') $shade='expired';
        echo "<tr class='$shade'>";
        echo "<td><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/contact.png' width='16' height='16' alt='' /> <a href=\"contact_details.php?id=".$contactrow['id']."\">".stripslashes($contactrow['forenames']).' '.stripslashes($contactrow['surname'])."</a></td>";
        echo "<td>".stripslashes($contactrow['jobtitle'])."</td>";
        echo "<td>".stripslashes($contactrow['department'])."</td>";
        if ($contactrow['dataprotection_phone']!='Yes') echo "<td>".stripslashes($contactrow['phone'])."</td>";
        else echo "<td><strong>{$strWithheld}</strong></td>";
        if ($contactrow['dataprotection_email']!='Yes') echo "<td>".stripslashes($contactrow['email'])."</td>";
        else echo "<td><strong>{$strWithheld}</strong></td>";
        if ($contactrow['dataprotection_address']!='Yes')
        {
            echo "<td>";
            if (!empty($contactrow['address1'])) echo stripslashes($contactrow['address1']);
            echo "</td>";
        }
        else echo "<td><strong>{$strWithheld}</strong></td>";
        echo "<td>";
        if ($contactrow['dataprotection_email']=='Yes') { echo "<strong>{$strNoEmail}</strong>, "; }
        if ($contactrow['dataprotection_phone']=='Yes') { echo "<strong>{$strNoCalls}</strong>, "; }
        if ($contactrow['dataprotection_address']=='Yes') { echo "<strong>{$strNoPost}</strong>"; }
        echo "</td>";
        echo "<td>".nl2br(stripslashes(substr($contactrow['notes'], 0, 500)))."</td>";
        echo "</tr>";
        if ($shade=='shade1') $shade='shade2';
        else $shade='shade1';
    }
    echo "</table>\n";
}
else
{
    echo "<p align='center'>{$strNoContactsForSite}</p>";
}
echo "<p align='center'><a href='add_contact.php?siteid={$id}'>{$strAddContact}</a></p>";


// Valid user, check perms
if (user_permission($sit[2],19)) // View contracts
{
    echo "<h3>{$strContracts}<a id='contracts'></a></h3>";

    // Display contracts
    $sql  = "SELECT maintenance.id AS maintid, maintenance.term AS term, products.name AS product, resellers.name AS reseller, licence_quantity, licencetypes.name AS licence_type, expirydate, admincontact, contacts.forenames AS admincontactsforenames, contacts.surname AS admincontactssurname, maintenance.notes AS maintnotes ";
    $sql .= "FROM maintenance, contacts, products, licencetypes, resellers ";
    $sql .= "WHERE maintenance.product=products.id AND maintenance.reseller=resellers.id AND licence_type=licencetypes.id AND admincontact=contacts.id ";
    $sql .= "AND maintenance.site = '$id' ";
    $sql .= "ORDER BY expirydate DESC";

    // connect to database and execute query
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $countcontracts=mysql_num_rows($result);
    if ($countcontracts > 0)
    {
        ?>
        <script type="text/javascript">
        function support_contacts_window(maintenanceid)
        {
            URL = "support_contacts.php?maintid=" + maintenanceid;
            window.open(URL, "support_contacts_window", "toolbar=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=450,height=240");
        }
        function contact_details_window(contactid)
        {
            URL = "contact_details.php?contactid=" + contactid;
            window.open(URL, "contact_details_window", "toolbar=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=450,height=240");
        }
        </script>
        <p align='center'>
        <?php
        echo mysql_num_rows($result)." $strContracts</p>";
        echo "<table align='center'>
        <tr>
            <th>{$strContractID}</th>
            <th>{$strProduct}</th>
            <th>{$strReseller}</th>
            <th>{$strLicense}</th>
            <th>{$strExpiryDate}</th>
            <th>{$strAdminContact}</th>
            <th>{$strNotes}</th>
        </tr>";
        $shade = 0;
        while ($results = mysql_fetch_array($result))
        {
            // define class for table row shading
            if ($shade) $class = "shade1";
            else $class = "shade2";
            if ($results['term']=='yes' || $results['expirydate']<$now) $class = "expired";
            echo "<tr>";
                echo "<td class='<?php echo $class ?>'><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/contract.png' width='16' height='16' alt='' /> ";
                echo "<a href='maintenance_details.php?id={$results['maintid']}'>{$strContract} {$results['maintid']}</a></td>";
                ?>
                <td class='<?php echo $class ?>'><?php echo stripslashes($results["product"]); ?></td>
                <td class='<?php echo $class ?>'><?php echo stripslashes($results["reseller"]); ?></td>
                <td class='<?php echo $class ?>'><?php echo $results["licence_quantity"] ?> <?php echo stripslashes($results["licence_type"]); ?></td>
                <td class='<?php echo $class ?>'><?php echo date($CONFIG['dateformat_date'], $results["expirydate"]); ?></td>
                <td class='<?php echo $class ?>'><?php echo stripslashes($results['admincontactsforenames'].' '.$results['admincontactssurname']); ?></td>
                <td class='<?php echo $class ?>'><?php if ($results['maintnotes'] == '') echo '&nbsp;'; else echo nl2br(stripslashes($results['maintnotes'])); ?></td>
            </tr>
            <?php
            // invert shade
            if ($shade == 1) $shade = 0;
            else $shade = 1;
        }
        echo "</table>\n";
    }
    else echo "<p align='center'>{$strNoContractsForSite}</p>";
    echo "<p align='center'><a href='add_contract.php?action=showform&amp;siteid=$id'>{$strAddContract}</a></p>";
}

include('htmlfooter.inc.php');

?>