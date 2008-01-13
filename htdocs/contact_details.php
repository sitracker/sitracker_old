
<?php
// contact_details.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//


// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
// Created: 24th May 2001
// Purpose: Show All Contact Details
// This Page Is Valid XHTML 1.0 Transitional! 27Oct05

@include ('set_include_path.inc.php');
$permission = 12;  // view contacts

require ('db_connect.inc.php');
require ('functions.inc.php');
$title = 'Contact Details';

// This page requires authentication
require ('auth.inc.php');

// External variables
$id = mysql_real_escape_string($_REQUEST['id']);
$output = $_REQUEST['output'];

if ($output == 'vcard')
{
    header("Content-type: text/x-vCard\r\n");
    header("Content-disposition-type: attachment\r\n");
    header("Content-disposition: filename=contact.vcf");
    echo contact_vcard($id);
    exit;
}

include ('htmlheader.inc.php');

// Display contacts
$sql="SELECT * FROM `{$dbContacts}` WHERE id='$id' ";
$contactresult = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
while ($contactrow=mysql_fetch_array($contactresult))
{
    // Lookup the site address if this contact hasn't got a specific address set
    if ($contactrow['address1']=='')
    {
        $sitesql = "SELECT * FROM `{$dbSites}` WHERE id='{$contactrow['siteid']}' LIMIT 1";
        $siteresult = mysql_query($sitesql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
        $site = mysql_fetch_object($siteresult);
        $address1 = $site->address1;
        $address2 = $site->address2;
        $city = $site->city;
        $county = $site->county;
        $country = $site->country;
        $postcode = $site->postcode;
    }
    else
    {
        $address1 = $contactrow['address1'];
        $address2 = $contactrow['address2'];
        $city = $contactrow['city'];
        $county = $contactrow['county'];
        $country = $contactrow['country'];
        $postcode = $contactrow['postcode'];
    }

    echo "<table align='center' class='vertical'>";
    echo "<tr><th colspan='2'><h3><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/contact.png' width='32' height='32' alt='' /> {$contactrow['forenames']} {$contactrow['surname']}</h3></th></tr>\n";
    if ($contactrow['active']=='false')
    {
        echo "<tr><th>{$strStatus}:</th><td><span class='expired'>{$strInactive}</span></td></tr>\n";
    }
    $tags = list_tags($id, 1, TRUE);
    if (!empty($tags)) echo "<tr><th>{$strTags}:</th><td>{$tags}</td></tr>\n";
    echo "<tr><th>{$strJobTitle}:</th><td>{$contactrow['jobtitle']}</td></tr>\n";
    echo "<tr><th>{$strSite}:</th><td>";
    echo "<a href='site_details.php?id={$contactrow['siteid']}'>".site_name($contactrow['siteid'])."</a></td></tr>\n";
    if (!empty($contactrow['department']))
    {
        echo "<tr><th>{$strDepartment}:</th><td>{$contactrow['department']}</td></tr>\n";
    }

    if ($contactrow['dataprotection_address'] != 'Yes')
    {
        echo "<tr><th>{$strAddress1}:</th><td>{$address1}</td></tr>\n";
        echo "<tr><th>{$strAddress2}:</th><td>{$address2}</td></tr>\n";
        echo "<tr><th>{$strCity}:</th><td>{$city}</td></tr>\n";
        echo "<tr><th>{$strCounty}:</th><td>{$county}</td></tr>\n";
        echo "<tr><th>{$strPostcode}:</th><td>{$postcode}</td></tr>\n";
        echo "<tr><th>{$strCountry}:</th><td>{$country}</td></tr>\n";
    }

    if ($contactrow['dataprotection_email'] != 'Yes')
    {
        echo "<tr><th>{$strEmail}:</th>";
        echo "<td><a href=\"mailto:{$contactrow['email']}\">{$contactrow['email']}</a></td></tr>\n";
    }

    if ($contactrow['dataprotection_phone'] != 'Yes')
    {
        echo "<tr><th>{$strTelephone}:</th><td>{$contactrow['phone']}</td></tr>\n";
        echo "<tr><th>{$strMobile}:</th><td>{$contactrow['mobile']}</td></tr>\n";
        echo "<tr><th>{$strFax}:</th><td>{$contactrow['fax']}</td></tr>\n";
    }
    echo "<tr><th>{$strDataProtection}:</th><td> ";

    if ($contactrow['dataprotection_email'] == 'Yes')
    {
        echo "<strong>{$strNoEmail}</strong>, ";
    }
    else
    {
        echo "{$strEmailOK}, ";
    }

    if ($contactrow['dataprotection_phone'] == 'Yes')
    {
        echo "<strong>{$strNoCalls}</strong>, ";
    }
    else
    {
        echo "{$strCallsOK}, ";
    }

    if ($contactrow['dataprotection_address'] == 'Yes')
    {
        echo "<strong>{$strNoPost}</strong>";
    }
    else
    {
        echo "{$strPostOK} ";
    }

    echo "</td></tr>\n";
    echo "<tr><th>{$strNotes}:</th><td>".nl2br($contactrow['notes'])."</td></tr>\n";

    echo "<tr><td colspan='2'>&nbsp;</td></tr>\n";
    echo "<tr><th>{$strAccessDetails}:</th><td>{$strUsername}: <code>{$contactrow['username']}</code>";
    // echo ", password: <code>".$contactrow['password']."</code>";  ## Passwords no longer controlled from SiT INL 23Nov04
    echo "</td></tr>\n";
    echo "<tr><th>{$strIncidents}:</th><td>";
    $openincidents = contact_count_open_incidents($id);
    $totalincidents = contact_count_incidents($id);
    if ($totalincidents == 0) echo $strNone;
    //if ($openincidents >= 1) echo "$openincidents open, ";
    if ($openincidents >= 1) echo sprintf($strNumOpenIncidents, $openincidents).", ";


    if ($totalincidents>=1)
    {
        echo "$totalincidents logged, see <a href='contact_support.php?id={$id}'>here</a>";
    }

    echo "</td></tr>\n";

    if ($contactrow['notify_contactid'] > 0)
    {
        echo "<tr><th>{$strNotifyContact}:</th><td>";
        echo contact_realname($contactrow['notify_contactid']);
        $notify_contact1 = contact_notify($contactrow['notify_contactid'], 1);
        if ($notify_contact1 > 0)
        {
            echo " -&gt; ".contact_realname($notify_contact1);
        }

        $notify_contact2 = contact_notify($contactrow['notify_contactid'], 2);
        if ($notify_contact2 > 0)
        {
            echo " -&gt; ".contact_realname($notify_contact2);
        }

        $notify_contact3 = contact_notify($contactrow['notify_contactid'], 3);
        if ($notify_contact3 > 0)
        {
            echo " -&gt; ".contact_realname($notify_contact3);
        }
        echo "</td></tr>\n";
    }

    plugin_do('contact_details');

    if ($contactrow['timestamp_modified'] > 0)
    {
        echo "<tr><th>{$strLastUpdated}:</th>";
        echo "<td>".date($CONFIG['dateformat_datetime'],$contactrow['timestamp_modified'])."</td></tr>\n";
    }
    echo "</table>\n";

    echo "<p align='center'>";
    echo "<a href='add_incident.php?action=findcontact&amp;contactid={$id}'>{$strAddIncident}</a> | ";
    echo "<a href='contact_details.php?id={$id}&amp;output=vcard'>vCard</a> | ";
    echo "<a href='edit_contact.php?action=edit&amp;contact={$id}'>{$strEdit}</a> | ";
    echo "<a href='delete_contact.php?id={$id}'>{$strDelete}</a>";
    echo "</p>\n";


    // Check if user has permission to view maintenace contracts, if so display those related to this contact
    if (user_permission($sit[2],30)) // view supported products
    {
        echo "<h4>{$strContracts}:</h4>";
        $sql  = "SELECT sc.maintenanceid AS maintenanceid, m.product, p.name AS productname, ";
        $sql .= "m.expirydate, m.term ";
        $sql .= "FROM `{$dbSupportContacts}` AS sc, `{$dbMaintenance}` AS m, `{$dbProducts}` AS p ";
        $sql .= "WHERE sc.maintenanceid=m.id AND m.product=p.id AND sc.contactid='$id' ";
        $result=mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        if (mysql_num_rows($result)>0)
        {
            echo "<table align='center' class='vertical'>";
            echo "<tr>";
            echo "<th>{$strID}</th><th>{$strProduct}</th><th>{$strExpiryDate}</th>";
            echo "</tr>\n";

            $supportcount=1;
            $shade='shade2';
            while ($supportedrow=mysql_fetch_array($result))
            {
                if ($supportedrow['term'] == 'yes') $shade='expired';
                if ($supportedrow['expirydate'] < $now) $shade='expired';

                echo "<tr><td class='$shade'>";
                echo "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/contract.png' width='16' height='16' alt='' /> ";
                echo "<a href='contract_details.php?id={$supportedrow['maintenanceid']}'>{$strContract}: {$supportedrow['maintenanceid']}</a></td>";
                echo "<td class='$shade'>{$supportedrow['productname']}</td>";
                echo "<td class='$shade'>".date($CONFIG['dateformat_date'], $supportedrow['expirydate']);
                if ($supportedrow['term'] == 'yes') echo " {$strTerminated}";
                echo "</td>";
                echo "</tr>\n";
                $supportcount++;
                $shade='shade2';
            }
            echo "</table>\n";

        }
        else
        {
            echo "<p align='center'>{$strNone}</p>\n";
        }
        echo "<p align='center'><a href='add_contact_support_contract.php?contactid={$id}&amp;context=contact'>";
        echo "{$strAssociateContactWithContract}</a></p>\n";
    }
    else
    {
        echo "<p align='center'>".sprintf($strPermissionDeniedForX, $strContracts)."</p>\n";
    }
}
mysql_free_result($contactresult);

include ('htmlfooter.inc.php');
?>
