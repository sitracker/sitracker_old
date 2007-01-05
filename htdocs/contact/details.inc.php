<?php

// Lookup the site address if this contact hasn't got a specific address set

    if ($contactrow['address1']=='')
    {
        $sitesql = "SELECT * FROM sites WHERE id='{$contactrow['siteid']}' LIMIT 1";
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
    echo "<tr><th colspan='2'><h3>".stripslashes($contactrow['forenames']).' '.stripslashes($contactrow['surname'])."</h3></th></tr>";
    echo "<tr><th>Tags:</th><td>";
    //print_contact_flags($id);
    list_tag_links($id, 1); //contacts
    echo "</td></tr>";
    echo "<tr><th>Job Title:</th><td>".$contactrow['jobtitle']."</td></tr>";
    echo "<tr><th>Site:</th><td><a href=\"site_details.php?id=".$contactrow['siteid']."\">".site_name($contactrow['siteid'])."</a></td></tr>";
    if (!empty($contactrow['department'])) echo "<tr><th>Department:</th><td>".$contactrow['department']."</td></tr>";
    echo "<tr><th>Address1:</th><td>{$address1}</td></tr>";
    echo "<tr><th>Address2:</th><td>{$address2}</td></tr>";
    echo "<tr><th>City:</th><td>{$city}</td></tr>";
    echo "<tr><th>County:</th><td>{$county}</td></tr>";
    echo "<tr><th>Postcode:</th><td>{$postcode}</td></tr>";
    echo "<tr><th>Country:</th><td>{$country}</td></tr>";
    echo "<tr><th>Email:</th><td><a href=\"mailto:".$contactrow['email']."\">".$contactrow['email']."</a></td></tr>";
    echo "<tr><th>Phone:</th><td>".$contactrow['phone']."</td></tr>";
    echo "<tr><th>Mobile:</th><td>".$contactrow['mobile']."</td></tr>";
    echo "<tr><th>Fax:</th><td>".$contactrow['fax']."</td></tr>";
    echo "<tr><th>Data Protection:</th><td> ";
    if ($contactrow['dataprotection_email']=='Yes') { echo "<strong>No Email</strong>, "; } else { echo "Email OK, ";}
    if ($contactrow['dataprotection_phone']=='Yes') { echo "<strong>No Calls</strong>, "; } else { echo "Calls OK, ";}
    if ($contactrow['dataprotection_address']=='Yes') { echo "<strong>No Post</strong>"; } else { echo "Post OK ";}
    echo "</td></tr>";
    echo "<tr><th>Notes:</th><td>".$contactrow['notes']."</td></tr>";

    echo "<tr><td colspan='2'>&nbsp;</td></tr>";
    echo "<tr><th>Access Details:</th><td>username: <code>".$contactrow['username']."</code>";
    // echo ", password: <code>".$contactrow['password']."</code>";  ## Passwords no longer controlled from SiT INL 23Nov04
    echo "</td></tr>";
    echo "<tr><th>Support Incidents:</th><td>";
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
        echo "</td></tr>";
    }

    $contact_manager=contact_manager_email($id);
    if ($contact_manager != '')
    {
        echo "<tr><th>Managers Email:</th><td>";
        echo contact_manager_email($id);
        echo "</td></tr>";
    }

    plugin_do('contact_details');

    if ($contactrow['timestamp_modified']>0)
    {
        echo "<tr><th>Record Modified:</th><td>".date("jS M Y",$contactrow['timestamp_modified'])."</td></tr>";
    }
    echo "</table>\n";

    echo "<p align='center'>";
    echo "<a href=\"add_incident.php?action=findcontact&amp;contactid=$id\">Add Incident</a> | ";
    echo "<a href=\"contact_details.php?id=$id&amp;output=vcard\">vCard</a> | ";
    echo "<a href=\"edit_contact.php?action=edit&amp;contact=$id\">Edit</a> | ";
    echo "<a href=\"delete_contact.php?id=$id\">Delete</a>";
    echo "</p>\n";


?>