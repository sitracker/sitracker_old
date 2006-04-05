<?php
// add_contact.php - Adds a new contact
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// This Page Is Valid XHTML 1.0 Transitional!  31Oct05

$permission=1; // Add new contact

require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

include('htmlheader.inc.php');
// External variables
$siteid = mysql_escape_string($_REQUEST['siteid']);
$submit = $_REQUEST['submit'];
?>
<script type='text/javascript'>
function confirm_submit()
{
    return window.confirm('Are you sure you want to add this contact?');
}
</script>
<?php
// Show add contact type form
if (empty($submit))
{
    ?>
    <h2>Add New Contact</h2>
    <h5>Mandatory fields are marked <sup class='red'>*</sup></h5>
    <form name='contactform' action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return confirm_submit()">
    <table>
    <tr><th>Contact Name: <sup class='red'>*</sup><br />Title, Forenames, Surname</th>
    <td><input maxlength="50" name="salutation" title="Salutation (Mr, Mrs, Miss, Dr. etc.)" size="7" />
    <input maxlength="100" name="forenames" size="15" title="Firstnames (or initials)" />
    <input maxlength="100" name="surname" size="20" title="Surname/Last Name" /></td></tr>
    <tr><th>Job Title:</th><td><input maxlength="255" name="jobtitle" size="35" title='e.g. Purchasing Manager' /></td></tr>
    <tr><th>Site: <sup class='red'>*</sup></th><td><?php echo site_drop_down('siteid',$siteid); ?></td></tr>
    <tr><th>Department:</th><td><input maxlength="255" name="department" size="35" /></td></tr>
    <tr><th>Email: <sup class='red'>*</sup></th><td><input maxlength="100" name="email" size="35" /></td></tr>
    <tr><th>Phone:</th><td><input maxlength="50" name="phone" size="35" /></td></tr>
    <tr><th>Mobile:</th><td><input maxlength="100" name="mobile" size="35" value="" /></td></tr>
    <tr><th>Fax:</th><td><input maxlength="50" name="fax" size="35" /></td></tr>
    <tr><th>Data Protection Email:</th><td><?php html_checkbox('dataprotection_email', 'No'); ?> Don't send email</td></tr>
    <tr><th>Data Protection Phone:</th><td><?php html_checkbox('dataprotection_phone', 'No'); ?> Don't call</td></tr>
    <tr><th>Data Protection Address:</th><td><?php html_checkbox('dataprotection_address', 'No'); ?> Don't write</td></tr>
    <tr><th></th><td><input type='checkbox' name='usesiteaddress' value='yes' onclick='togglecontactaddress();' /> Specifiy an address for this contact that is different to the site</td></tr>
    <tr><th>Address 1:</th><td><input maxlength="255" name="address1" size="35" disabled='disabled' /></td></tr>
    <tr><th>Address 2:</th><td><input maxlength="255" name="address2" size="35" disabled='disabled' /></td></tr>
    <tr><th>City:</th><td><input maxlength="255" name="city" size="35" disabled='disabled' /></td></tr>
    <tr><th>County:</th><td><input maxlength="255" name="county" size="35" disabled='disabled' /></td></tr>
    <tr><th>Country:</th><td><?php echo country_drop_down('country', $CONFIG['home_country'], "disabled='disabled'") ?></td></tr>
    <tr><th>Postcode:</th><td><input maxlength="255" name="postcode" size="35" disabled='disabled' /></td></tr>

    <tr><th>notes:</th><td><textarea cols="60" rows="5" name="notes"></textarea></td></tr>
    </table>
    <p><input name="submit" type="submit" value="Add Contact" /></p>
    </form>

    <h5 class='warning'>Please ensure that the contact does not already exist before adding a new contact.</h5>
    <?php
    include('htmlfooter.inc.php');
}
else
{
    // Add new contact
    // External variables
    $siteid = mysql_escape_string($_REQUEST['siteid']);
    $email = strtolower(cleanvar($_REQUEST['email']));
    $dataprotection_email = mysql_escape_string($_REQUEST['dataprotection_email']);
    $dataprotection_phone = mysql_escape_string($_REQUEST['dataprotection_phone']);
    $dataprotection_address = mysql_escape_string($_REQUEST['dataprotection_address']);
    $username = cleanvar($_REQUEST['username']);
    $salutation = cleanvar($_REQUEST['salutation']);
    $forenames = cleanvar($_REQUEST['forenames']);
    $surname = cleanvar($_REQUEST['surname']);
    $jobtitle = cleanvar($_REQUEST['jobtitle']);
    $address1 = cleanvar($_REQUEST['address1']);
    $address2 = cleanvar($_REQUEST['address2']);
    $city = cleanvar($_REQUEST['city']);
    $county = cleanvar($_REQUEST['county']);
    if (!empty($address1)) $country = cleanvar($_REQUEST['country']);
    else $country='';
    $postcode = cleanvar($_REQUEST['postcode']);
    $phone = cleanvar($_REQUEST['phone']);
    $mobile = cleanvar($_REQUEST['mobile']);
    $fax = cleanvar($_REQUEST['fax']);
    $department = cleanvar($_REQUEST['department']);
    $notes = cleanvar($_REQUEST['notes']);

    $errors = 0;
    // check for blank name
    if ($surname == "")
    {
        $errors = 1;
        echo "<p class='error'>You must enter a contact Surname</p>\n";
    }
    // check for blank site
    if ($siteid == '')
    {
        $errors = 1;
        echo "<p class='error'>You must select a site for this customer</p>\n";
    }
    // check for blank email
    if ($email == "" OR $email=='none' OR $email=='n/a')
    {
        $errors = 1;
        echo "<p class='error'>You must enter an email address</p>\n";
    }
    if ($siteid==0 OR $siteid=='')
    {
        $errors++;
        echo "<p class='error'>You must select a site</p>\n";
    }
    // Check this is not a duplicate
    $sql = "SELECT id FROM contacts WHERE email='$email' AND LCASE(surname)=LCASE('$surname') LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_num_rows($result) >= 1)
    {
        $errors++;
        echo "<p class='error'>A contact record already exists with that Surname and Email address</p>";
    }


    // add contact if no errors
    if ($errors == 0)
    {
        if (!empty($dataprotection_email)) $dataprotection_email='Yes'; else $dataprotection_email='No';
        if (!empty($dataprotection_phone)) $dataprotection_phone='Yes'; else $dataprotection_phone='No';
        if (!empty($dataprotection_address)) $dataprotection_address='Yes'; else $dataprotection_address='No';

        // generate username and password
        ## From 23Nov04 (v3.14) Passwords are no longer generated/controlled by SiT - INL
        ## From 24Nov04 (v3.15) Passwords are again generated/controlled by SiT - INL

        $username = substr($surname, 0, strcspn($surname, " "));
        $password = generate_password();

        $sql  = "INSERT INTO contacts (username, password, salutation, forenames, surname, jobtitle, ";
        $sql .= "siteid, address1, address2, city, county, country, postcode, email, phone, mobile, fax, ";
        $sql .= "department, notes, dataprotection_email, dataprotection_phone, dataprotection_address, ";
        $sql .= "timestamp_added, timestamp_modified) ";
        $sql .= "VALUES ('$username', '$password', '$salutation', '$forenames', '$surname', '$jobtitle', ";
        $sql .= "'$siteid', '$address1', '$address2', '$city', '$county', '$country', '$postcode', '$email', ";
        $sql .= "'$phone', '$mobile', '$fax', '$department', '$notes', '$dataprotection_email', ";
        $sql .= "'$dataprotection_phone', '$dataprotection_address', '$now', '$now')";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        // concatenate username with insert id to make unique
        $newid = mysql_insert_id();
        $username = $username . $newid;
        $sql = "UPDATE contacts SET username='$username' WHERE id='$newid'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        if (!$result) echo "<p class='error'>Addition of Contact Failed\n";
        else
        {
            $sql = "SELECT username, password FROM contacts WHERE id=$newid";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            $newcontact = mysql_fetch_array($result);
            journal(CFG_LOGGING_NORMAL,'Contact Added',"$forenames $surname was Added",CFG_JOURNAL_CONTACTS,$newid);
            confirmation_page("2", "contact_details.php?id=$newid", "<h2>Contact Added Successfully</p><p align='center'>Please wait while you are redirected...</p>");
        }
    }
}
?>