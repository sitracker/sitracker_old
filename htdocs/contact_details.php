<?php
// contact_details.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//


// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
// Created: 24th May 2001
// Purpose: Show All Contact Details
// This Page Is Valid XHTML 1.0 Transitional! 27Oct05

$permission=12;  // view contacts

require('db_connect.inc.php');
require('functions.inc.php');
$title='Contact Details';

// This page requires authentication
require('auth.inc.php');

// External variables
$id = mysql_escape_string($_REQUEST['id']);
$output = $_REQUEST['output'];

if ($output == 'vcard')
{
    header("Content-type: text/x-vCard\r\n");
    header("Content-disposition-type: attachment\r\n");
    header("Content-disposition: filename=contact.vcf");
    echo contact_vcard($id);
    exit;
}

include('htmlheader.inc.php');

// Display contacts
$sql="SELECT * FROM contacts WHERE id='$id' ";
$contactresult = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
while ($contactrow=mysql_fetch_array($contactresult))
{
    // Lookup the site address if this contact hasn't got a specific address set
    if ($contactrow['address1']=='')
    {
        $sitesql = "SELECT * FROM sites WHERE id='{$contactrow['siteid']}' LIMIT 1";
        $siteresult = mysql_query($sitesql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
        $site = mysql_fetch_object($siteresult);
        $address1 = stripslashes($site->address1);
        $address2 = stripslashes($site->address2);
        $city = stripslashes($site->city);
        $county = stripslashes($site->county);
        $country = stripslashes($site->country);
        $postcode = stripslashes($site->postcode);
    }
    else
    {
        $address1 = stripslashes($contactrow['address1']);
        $address2 = stripslashes($contactrow['address2']);
        $city = stripslashes($contactrow['city']);
        $county = stripslashes($contactrow['county']);
        $country = stripslashes($contactrow['country']);
        $postcode = stripslashes($contactrow['postcode']);
    }

    echo "<table align='center' class='vertical'>";
    echo "<tr><th colspan='2'><h3><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/contact.png' width='32' height='32' alt='' /> ".stripslashes($contactrow['forenames']).' '.stripslashes($contactrow['surname'])."</h3></th></tr>";
    if ($contactrow['active']=='false')
    {
        echo "<tr><th>{$strStatus}:</th><td><span class='expired'>{$strInactive}</span></td></tr>";
    }
    $tags = list_tags($id, 1, TRUE);
    if (!empty($tags)) echo "<tr><th>{$strTags}:</th><td>{$tags}</td></tr>";
    // Flags are deprecated as of v3.30 in favour of tags - INL
    // echo "<tr><th>Flags:</th><td>";
    // print_contact_flags($id);
    //echo "</td></tr>";
    echo "<tr><th>{$strJobTitle}:</th><td>".stripslashes($contactrow['jobtitle'])."</td></tr>";
    echo "<tr><th>{$strSite}:</th><td><a href=\"site_details.php?id=".$contactrow['siteid']."\">".site_name($contactrow['siteid'])."</a></td></tr>";
    if (!empty($contactrow['department'])) echo "<tr><th>{$strDepartment}:</th><td>".stripslashes($contactrow['department'])."</td></tr>";
    if ($contactrow['dataprotection_address']!='Yes')
    {
        echo "<tr><th>{$strAddress1}:</th><td>{$address1}</td></tr>";
        echo "<tr><th>{$strAddress2}:</th><td>{$address2}</td></tr>";
        echo "<tr><th>{$strCity}:</th><td>{$city}</td></tr>";
        echo "<tr><th>{$strCounty}:</th><td>{$county}</td></tr>";
        echo "<tr><th>{$strPostcode}:</th><td>{$postcode}</td></tr>";
        echo "<tr><th>{$strCountry}:</th><td>{$country}</td></tr>";
    }
    if ($contactrow['dataprotection_email']!='Yes')
    {
        echo "<tr><th>{$strEmail}:</th><td><a href=\"mailto:".stripslashes($contactrow['email'])."\">".stripslashes($contactrow['email'])."</a></td></tr>";
    }
    if ($contactrow['dataprotection_phone']!='Yes')
    {
        echo "<tr><th>{$strTelephone}:</th><td>".stripslashes($contactrow['phone'])."</td></tr>";
        echo "<tr><th>{$strMobile}:</th><td>".stripslashes($contactrow['mobile'])."</td></tr>";
        echo "<tr><th>{$strFax}:</th><td>".stripslashes($contactrow['fax'])."</td></tr>";
    }
    echo "<tr><th>{$strDataProtection}:</th><td> ";
    if ($contactrow['dataprotection_email']=='Yes') { echo "<strong>No Email</strong>, "; } else { echo "Email OK, ";}
    if ($contactrow['dataprotection_phone']=='Yes') { echo "<strong>No Calls</strong>, "; } else { echo "Calls OK, ";}
    if ($contactrow['dataprotection_address']=='Yes') { echo "<strong>No Post</strong>"; } else { echo "Post OK ";}
    echo "</td></tr>";
    echo "<tr><th>{$strNotes}:</th><td>".nl2br(stripslashes($contactrow['notes']))."</td></tr>";

    echo "<tr><td colspan='2'>&nbsp;</td></tr>";
    echo "<tr><th>Access Details:</th><td>{$strUsername}: <code>".$contactrow['username']."</code>";
    // echo ", password: <code>".$contactrow['password']."</code>";  ## Passwords no longer controlled from SiT INL 23Nov04
    echo "</td></tr>";
    echo "<tr><th>{$strIncidents}:</th><td>";
    $openincidents=contact_count_open_incidents($id);
    $totalincidents=contact_count_incidents($id);
    if ($totalincidents==0) echo "None";
    if ($openincidents>=1) echo "$openincidents open, ";
    if ($totalincidents>=1) echo "$totalincidents logged, see <a href='contact_support.php?id=$id'>here</a>";
    echo "</td></tr>";

    if ($contactrow['notify_contactid'] > 0)
    {
        echo "<tr><th>Notify Contact:</th><td>";
        echo contact_realname($contactrow['notify_contactid']);
        $notify_contact1 = contact_notify($contactrow['notify_contactid'], 1);
        if ($notify_contact1 > 0) echo " -&gt; ".contact_realname($notify_contact1);
        $notify_contact2 = contact_notify($contactrow['notify_contactid'], 2);
        if ($notify_contact2 > 0) echo " -&gt; ".contact_realname($notify_contact2);
        $notify_contact3 = contact_notify($contactrow['notify_contactid'], 3);
        if ($notify_contact3 > 0) echo " -&gt; ".contact_realname($notify_contact3);
        echo "</td></tr>";
    }

//      DEPRECATED as of v3.30 in favour of Notify contacts (see above)
//     $contact_manager=contact_manager_email($id);
//     if ($contact_manager != '')
//     {
//         echo "<tr><th>Managers Email:</th><td>";
//         echo contact_manager_email($id);
//         echo "</td></tr>";
//     }

    plugin_do('contact_details');

    if ($contactrow['timestamp_modified']>0)
    {
        echo "<tr><td>{$strLastUpdated}:</td><td>".date("jS M Y",$contactrow['timestamp_modified'])."</td></tr>";
    }
    echo "</table>\n";

    echo "<p align='center'>";
    echo "<a href=\"add_incident.php?action=findcontact&amp;contactid=$id\">{$strAddIncident}</a> | ";
    echo "<a href=\"contact_details.php?id=$id&amp;output=vcard\">vCard</a> | ";
    echo "<a href=\"edit_contact.php?action=edit&amp;contact=$id\">{$strEdit}</a> | ";
    echo "<a href=\"delete_contact.php?id=$id\">{$strDelete}</a>";
    echo "</p>\n";


    // Check if user has permission to view maintenace contracts, if so display those related to this contact
    if (user_permission($sit[2],30)) // view supported products
    {
        echo "<h4>Related Contracts:</h4>";
        $sql  = "SELECT supportcontacts.maintenanceid AS maintenanceid, maintenance.product, products.name AS productname, ";
        $sql .= "maintenance.expirydate, maintenance.term ";
        $sql .= "FROM supportcontacts, maintenance, products ";
        $sql .= "WHERE supportcontacts.maintenanceid=maintenance.id AND maintenance.product=products.id AND supportcontacts.contactid='$id' ";
        $result=mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        if (mysql_num_rows($result)>0)
        {
            echo "<table align='center' class='vertical'>";
            echo "<tr>";
            echo "<th>ID</th><th>{$strProduct}</th><th>{$strExpiryDate}</th>";
            echo "</tr>\n";

            $supportcount=1;
            $shade='shade2';
            while ($supportedrow=mysql_fetch_array($result))
            {
                if ($supportedrow['term']=='yes') $shade='expired';
                if ($supportedrow['expirydate']<$now) $shade='expired';

                echo "<tr><td class='$shade'><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/contract.png' width='16' height='16' alt='' /> <a href=\"maintenance_details.php?id=".$supportedrow['maintenanceid']."\">Contract: ".$supportedrow['maintenanceid']."</a></td>";
                echo "<td class='$shade'>".$supportedrow['productname']."</td>";
                echo "<td class='$shade'>".date("jS M Y", $supportedrow['expirydate']);
                if ($supportedrow['term']=='yes') echo " Terminated";
                echo "</td>";
                echo "</tr>\n";
                $supportcount++;
                $shade='shade2';
            }
            echo "</table>\n";

        }
        else
        {
            echo "<p align='center'>This contact is not supported via any contracts</p>\n";
        }
        echo "<p align='center'><a href='add_maintenance_support_contact.php?contactid=$id&amp;context=contact'>Associate this contact with an existing contract</a></p>\n";
    }
    else
    {
        echo "<p align='center'>Related contracts not shown (no permision)</p>\n";
    }
}
mysql_free_result($contactresult);

include('htmlfooter.inc.php');
?>