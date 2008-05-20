<?php
// add_contact.php - Adds a new contact
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// This Page Is Valid XHTML 1.0 Transitional!  31Oct05

@include ('set_include_path.inc.php');
$permission = 1; // Add new contact

require ('db_connect.inc.php');
require ('functions.inc.php');
// This page requires authentication
require ('auth.inc.php');

// External variables
$siteid = mysql_real_escape_string($_REQUEST['siteid']);
$submit = $_REQUEST['submit'];

if (empty($submit) OR !empty($_SESSION['formerrors']['add_contact']))
{
    include ('htmlheader.inc.php');
    
    ?>
    <script type="text/javascript" src="scripts/dojo/dojo.js"></script>
    <script type='text/javascript'>
    //<![CDATA[
        dojo.require("dojo.widget.ComboBox");
    //]]>
    </script>
    <?php
    echo show_add_contact();
    include ('htmlfooter.inc.php');
}
else
{
    // Add new contact
    // External variables
    $siteid = mysql_real_escape_string($_REQUEST['siteid']);
    $email = strtolower(cleanvar($_REQUEST['email']));
    $dataprotection_email = mysql_real_escape_string($_REQUEST['dataprotection_email']);
    $dataprotection_phone = mysql_real_escape_string($_REQUEST['dataprotection_phone']);
    $dataprotection_address = mysql_real_escape_string($_REQUEST['dataprotection_address']);
    $username = cleanvar($_REQUEST['username']);
    $courtesytitle = cleanvar($_REQUEST['courtesytitle']);
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
    $_SESSION['formdata']['add_contact'] = $_REQUEST;

    $errors = 0;
    // check for blank name
    if ($surname == "")
    {
        $errors++;
        $_SESSION['formerrors']['add_contact']['surname'] = $strMustEnterSurname;
    }
    // check for blank site
    if ($siteid == '')
    {
        $errors++;
        $_SESSION['formerrors']['add_contact']['siteid'] = $strMustSelectCustomerSite;
    }
    // check for blank email
    if ($email == "" OR $email=='none' OR $email=='n/a')
    {
        $errors++;
        $_SESSION['formerrors']['add_contact']['email'] = $strMustEnterEmail;
    }
    if ($siteid==0 OR $siteid=='')
    {
        $errors++;
        $_SESSION['formerrors']['add_contact']['siteid'] = $strMustSelectSite;
    }
    // Check this is not a duplicate
    $sql = "SELECT id FROM `{$dbContacts}` WHERE email='$email' AND LCASE(surname)=LCASE('$surname') LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_num_rows($result) >= 1)
    {
        $errors++;
        $_SESSION['formerrors']['add_contact']['duplicate'] = $strContactRecordExists;
    }


    // add contact if no errors
    if ($errors == 0)
    {
        if (!empty($dataprotection_email))
        {
            $dataprotection_email='Yes';
        }
        else
        {
            $dataprotection_email='No';
        }

        if (!empty($dataprotection_phone))
        {
            $dataprotection_phone='Yes';
        }
        else
        {
            $dataprotection_phone='No';
        }

        if (!empty($dataprotection_address))
        {
            $dataprotection_address='Yes';
        }
        else
        {
            $dataprotection_address='No';
        }

        // generate username and password

        $username = strtolower(substr($surname, 0, strcspn($surname, " ")));
        $prepassword = generate_password();
        $password = md5($prepassword);

        $sql  = "INSERT INTO `{$dbContacts}` (username, password, courtesytitle, forenames, surname, jobtitle, ";
        $sql .= "siteid, address1, address2, city, county, country, postcode, email, phone, mobile, fax, ";
        $sql .= "department, notes, dataprotection_email, dataprotection_phone, dataprotection_address, ";
        $sql .= "timestamp_added, timestamp_modified) ";
        $sql .= "VALUES ('$username', '$password', '$courtesytitle', '$forenames', '$surname', '$jobtitle', ";
        $sql .= "'$siteid', '$address1', '$address2', '$city', '$county', '$country', '$postcode', '$email', ";
        $sql .= "'$phone', '$mobile', '$fax', '$department', '$notes', '$dataprotection_email', ";
        $sql .= "'$dataprotection_phone', '$dataprotection_address', '$now', '$now')";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        // concatenate username with insert id to make unique
        $newid = mysql_insert_id();
        
        if ($CONFIG['portal'] AND $_POST['emaildetails'] == 'on')
        {
            trigger('TRIGGER_NEW_CONTACT', array('contactid' => $newid, 'prepassword' => $prepassword));
        }
        $username = $username . $newid;
        $sql = "UPDATE `{$dbContacts}` SET username='$username' WHERE id='$newid'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        if (!$result) echo "<p class='error'>Addition of Contact Failed\n";
        else
        {
            $sql = "SELECT username, password FROM `{$dbContacts}` WHERE id=$newid";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            $newcontact = mysql_fetch_array($result);
            journal(CFG_LOGGING_NORMAL,'Contact Added',"$forenames $surname was Added",CFG_JOURNAL_CONTACTS,$newid);
            html_redirect("contact_details.php?id=$newid");
        }
        clear_form_data('add_contact');
        clear_form_errors('add_contact');
    }
    else html_redirect("add_contact.php", FALSE);

}
?>
