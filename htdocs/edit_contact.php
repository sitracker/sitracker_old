<?php
// edit_contact.php - Form for editing a contact
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// This Page Is Valid XHTML 1.0 Transitional!  31Oct05

$permission=10; // Edit Contacts
$title='Edit Contact';

require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

// External variables
$contact = cleanvar($_REQUEST['contact']);
$action = $_REQUEST['action'];

include('htmlheader.inc.php');
?>
<script type='text/javascript'>
function confirm_submit()
{
    return window.confirm('Are you sure you want to make these changes?');
}
</script>
<?php

// User has access
if (empty($action) OR $action == "showform" OR empty($contact))
{
    // Show select contact form
    ?>
    <h2>Select Contact To Edit</h2>
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>?action=edit" method="post">
    <table align='center'>
    <tr><th>Contact:</th><td><?php echo contact_site_drop_down("contact", 0); ?></td></tr>
    </table>
    <p align='center'><input name="submit" type="submit" value="Continue" /></p>
    </form>
    <?php
}
elseif ($action == "edit" && isset($contact))
{
    // Show edit contact form
    $sql="SELECT * FROM contacts WHERE id='$contact' ";
    $contactresult = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    while ($contactrow=mysql_fetch_array($contactresult))
    {                                                   // User does not have access
        ?>
        <h2>Edit Contact <?php  echo $contact; ?></h2>
        <form name='contactform' action="<?php echo $_SERVER['PHP_SELF'] ?>?action=update" method="post" onsubmit="return confirm_submit()">
        <p align='center'>Mandatory fields are marked <sup class='red'>*</sup></p>
        <table align='center' class='vertical'>
        <tr><th>Contact Name: <sup class='red'>*</sup><br />Title, Forenames, Surname</th>
        <td><input maxlength="50" name="salutation" title="Salutation (Mr, Mrs, Miss, Dr. etc.)" size="7" value="<?php echo $contactrow['salutation'] ?>" />
        <input maxlength="100" name="forenames" size="15" title="Firstnames (or initials)" value="<?php echo $contactrow['forenames'] ?>" />
        <input maxlength="100" name="surname" size="20" title="Surname/Last Name" value="<?php echo $contactrow['surname'] ?>" /></td></tr>
        <tr><th>Tags:<br />Separated by commas</th><td><textarea rows='2' cols='60' name='tags'><?php  /* print_contact_flags($contact);*/ echo list_tags($contact, 1, false); ?>
        </textarea>
        <!-- <a href="edit_tags.php?recordid=<?php echo $contact ?>&amp;tagtype=1">Edit</a>
        <a href="add_tag.php?recordid=<?php echo $contact ?>&amp;tagtype=1">Add</a> -->
        </td></tr>
        <tr><th>Job Title:</th><td><input maxlength="255" name="jobtitle" size="40" value="<?php echo $contactrow['jobtitle'] ?>" /></td></tr>
        <tr><th>Site: <sup class='red'>*</sup></th><td><?php echo site_drop_down('siteid',$contactrow['siteid']); ?></td></tr>
        <tr><th>Department:</th><td><input maxlength="100" name="department" size="40" value="<?php echo $contactrow['department'] ?>" /></td></tr>
        <tr><th>Email: <sup class='red'>*</sup></th><td><input maxlength="100" name="email" size="40" value="<?php  echo $contactrow['email'] ?>" /></td></tr>
        <tr><th>Phone:</th><td><input maxlength="50" name="phone" size="40" value="<?php  echo $contactrow['phone'] ?>" /></td></tr>
        <tr><th>Mobile:</th><td><input maxlength="50" name="mobile" size="40" value="<?php  echo $contactrow['mobile'] ?>" /></td></tr>
        <tr><th>Fax:</th><td><input maxlength="50" name="fax" size="40" value="<?php  echo $contactrow['fax'] ?>" /></td></tr>
        <tr><th>Data Protection Email:</th><td><?php html_checkbox('dataprotection_email', $contactrow['dataprotection_email']); ?> Don't send email</td></tr>
        <tr><th>Data Protection Phone:</th><td><?php html_checkbox('dataprotection_phone', $contactrow['dataprotection_phone']); ?> Don't call</td></tr>
        <tr><th>Data Protection Address:</th><td><?php html_checkbox('dataprotection_address', $contactrow['dataprotection_address']); ?> Don't write</td></tr>
        <tr><th></th><td>
        <?php
        echo "<input type='checkbox' name='usesiteaddress' value='yes' onclick='togglecontactaddress();' ";
        if ($contactrow['address1'] !='')
        {
            echo "checked='checked'";
            $extraattributes = '';
        }
        else
        {
          $extraattributes = "disabled='disabled' ";
        }
        echo "/> ";
        echo "Specifiy an address for this contact that is different to the site</td></tr>\n";
        echo "<tr><th>Address1:</th><td><input maxlength='255' name='address1' size='40' value='{$contactrow['address1']}' {$extraattributes} /></td></tr>\n";
        echo "<tr><th>Address2:</th><td><input maxlength='255' name='address2' size='40' value='{$contactrow['address2']}' {$extraattributes} /></td></tr>\n";
        echo "<tr><th>City:</th><td><input maxlength=255' name='city' size='40' value='{$contactrow['city']}' {$extraattributes} /></td></tr>\n";
        echo "<tr><th>County:</th><td><input maxlength='255' name='county' size='40' value='{$contactrow['county']}' {$extraattributes} /></td></tr>\n";
        echo "<tr><th>Postcode:</th><td><input maxlength='255' name='postcode' size='40' value='{$contactrow['postcode']}' {$extraattributes} /></td></tr>\n";
        echo "<tr><th>Country:</th><td>";
        echo country_drop_down('country', $contactrow['country'], $extraattributes);
        echo "</td></tr>\n";
        echo "<tr><th>Notify contact:</th><td>".contact_site_drop_down('notify_contactid', $contactrow['notify_contactid'], $contactrow['siteid'])."</td></tr>\n";
        echo "<tr><th>Notes:</th><td><textarea rows='5' cols='60' name='notes'>{$contactrow['notes']}</textarea></td></tr>\n";

        plugin_do('edit_contact_form');
        echo "</table>";
        ?>
        <input name="contact" type="hidden" value="<?php echo $contact ?>" />
        <p align='center'><input name="submit" type="submit" value="Save" /></p>
        <?php
        echo "</form>\n";
    }
}
else if ($action == "update")
{
    // External variables
    $contact = cleanvar($_POST['contact']);
    $salutation = cleanvar($_POST['salutation']);
    $surname = cleanvar($_POST['surname']);
    $forenames = cleanvar($_POST['forenames']);
    $siteid = cleanvar($_POST['siteid']);
    $email = strtolower(cleanvar($_POST['email']));
    $phone = cleanvar($_POST['phone']);
    $mobile = cleanvar($_POST['mobile']);
    $fax = cleanvar($_POST['fax']);
    $address1 = cleanvar($_POST['address1']);
    $address2 = cleanvar($_POST['address2']);
    $city = cleanvar($_POST['city']);
    $county = cleanvar($_POST['county']);
    $postcode = cleanvar($_POST['postcode']);
    $country = cleanvar($_POST['country']);
    $notes = cleanvar($_POST['notes']);
    $dataprotection_email = cleanvar($_POST['dataprotection_email']);
    $dataprotection_address = cleanvar($_POST['dataprotection_address']);
    $dataprotection_phone = cleanvar($_POST['dataprotection_phone']);
    $jobtitle = cleanvar($_POST['jobtitle']);
    $department = cleanvar($_POST['department']);
    $notify_contactid = cleanvar($_POST['notify_contactid']);
    $tags = cleanvar($_POST['tags']);

    // Save changes to database
    $errors = 0;

    // VALIDATION CHECKS */

    // check for blank name
    if ($surname == '')
    {
        $errors = 1;
        echo "<p class='error'>You must enter a surname</p>\n";
    }
    // check for blank site
    if ($siteid == "")
    {
        $errors = 1;
        echo "<p class='error'>You must enter a site name</p>\n";
    }
    // check for blank name
    if ($email == "" OR $email=='none' OR $email=='n/a')
    {
        $errors = 1;
        echo "<p class='error'>You must enter an email address</p>\n";
    }
    // check for blank contact id
    if ($contact == "")
    {
        $errors = 1;
        echo "<p class='error'>Something weird has happened, better call technical support</p>\n";
    }

    // edit contact if no errors
    if ($errors == 0)
    {
        // update contact
        if ($dataprotection_email != '') $dataprotection_email='Yes'; else $dataprotection_email='No';
        if ($dataprotection_phone  != '') $dataprotection_phone='Yes'; else $dataprotection_phone='No';
        if ($dataprotection_address  != '') $dataprotection_address='Yes'; else $dataprotection_address='No';

        /*
            TAGS
        */

        // first remove old tags
        $sql = "DELETE FROM set_tags WHERE id = '$contact' AND type = '1'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        $tag_array = explode(",", $tags);
        foreach($tag_array AS $tag)
        {
            add_tag($contact, 1, trim($tag));
        }

        $sql = "UPDATE contacts SET salutation='$salutation', surname='$surname', forenames='$forenames', siteid='$siteid', email='$email', phone='$phone', mobile='$mobile', fax='$fax', ";
        $sql .= "address1='$address1', address2='$address2', city='$city', county='$county', postcode='$postcode', ";
        $sql .= "country='$country', dataprotection_email='$dataprotection_email', dataprotection_phone='$dataprotection_phone', ";
        $sql .= "notes='$notes', dataprotection_address='$dataprotection_address' , department='$department' , jobtitle='$jobtitle', ";
        $sql .= "notify_contactid='$notify_contactid', ";
        $sql .= "timestamp_modified=$now WHERE id='$contact'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        if (!$result) throw_error('Update of contact failed:',$sql);
        else
        {
            plugin_do('save_contact_form');

            journal(CFG_LOGGING_NORMAL, 'Contact Edited', "Contact $contact was edited", CFG_JOURNAL_CONTACTS, $contact);
            confirmation_page("2", "contact_details.php?id=$contact", "<h2>Contact Edited Successfully</h2><p align='center'>Please wait while you are redirected...</p>");
        }
    }
}
include('htmlfooter.inc.php');
?>