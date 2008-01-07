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
// if ($CONFIG['debug'])
//     $debug .= print_r($_SESSION['formdata']['add_contact']);
//
//     echo "<p class='error'>Form Error</p>";
if (empty($submit) OR !empty($_SESSION['formerrors']['add_contact']))
{
    include ('htmlheader.inc.php');
    ?>
    <script type="text/javascript" src="scripts/dojo/dojo.js"></script>
    <script type='text/javascript'>
        dojo.require("dojo.widget.ComboBox");
    </script>
    <?php
    echo show_form_errors('add_contact');
    clear_form_errors('add_contact');
    echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/contact.png' width='32' height='32' alt='' /> ";
    echo "{$strNewContact}</h2>";

    echo "<h5>".sprintf($strMandatoryMarked, "<sup class='red'>*</sup>")."</h5>";
    echo "<form name='contactform' action='{$_SERVER['PHP_SELF']}' method='post' onsubmit='return confirm_submit(\"{$strAddContractConfirm}\");'>";
    echo "<table align='center' class='vertical'>";
    echo "<tr><th>{$strName} <sup class='red'>*</sup><br /></th>\n";

    echo "<td>";
    echo "\n<table><tr><td align='center'>{$strTitle}<br />";
    echo "<input maxlength='50' name='salutation' title='Salutation (Mr, Mrs, Miss, Dr. etc.)' size='7'"; //FIXME i18n
    // FIXME throughout sit the name salutation is used to mean 'courtesy title' (eg. mr, miss) - it's a mistake
    if ($_SESSION['formdata']['add_contact']['salutation'] != '')
    {
        echo "value='{$_SESSION['formdata']['add_contact']['salutation']}'";
    }
    echo "/></td>\n";

    echo "<td align='center'>{$strForenames}<br />";
    echo "<input maxlength='100' name='forenames' size='15' title='Firstnames (or initials)'";
    if ($_SESSION['formdata']['add_contact']['forenames'] != '')
    {
        echo "value='{$_SESSION['formdata']['add_contact']['forenames']}'";
    }
    echo "/></td>\n";

    echo "<td align='center'>{$strSurname}<br /><input maxlength='100' name='surname' size='20' title=\"{$strSurname}\"";
    if ($_SESSION['formdata']['add_contact']['surname'] != '')
    {
        echo "value='{$_SESSION['formdata']['add_contact']['surname']}'";
    }
    echo " /></td></tr>\n";
    echo "</table>\n</td></tr>\n";

    echo "<tr><th>{$strJobTitle}</th><td><input maxlength='255' name='jobtitle' size='35' title='e.g. Purchasing Manager'";
    if ($_SESSION['formdata']['add_contact']['jobtitle'] != '')
    {
        echo "value='{$_SESSION['formdata']['add_contact']['jobtitle']}'";
    }
    echo " /></td></tr>\n";
    echo "<tr><th>{$strSite} <sup class='red'>*</sup></th><td>";
    echo site_drop_down('siteid',$siteid)."</td></tr>\n";
    // KMH REMOVED 12/12/07, form fails as dojo doesn't have the siteID
//     echo "<input dojoType='ComboBox' dataUrl='autocomplete.php?action=sites' style='width: 300px;' name='search_string' />";

    echo "<tr><th>{$strDepartment}</th><td><input maxlength='255' name='department' size='35'";
    if ($_SESSION['formdata']['add_contact']['department'] != '')
    {
        echo "value='{$_SESSION['formdata']['add_contact']['department']}'";
    }
    echo "/></td></tr>\n";

    echo "<tr><th>{$strEmail} <sup class='red'>*</sup></th><td><input maxlength='100' name='email' size='35'";
    if ($_SESSION['formdata']['add_contact']['email'])
    {
        echo "value='{$_SESSION['formdata']['add_contact']['email']}'";
    }
    echo "/> ";

    echo "<label>";
    html_checkbox('dataprotection_email', 'No');
    echo "{$strEmail} {$strDataProtection}</label>";
    echo "</td></tr>\n";

    echo "<tr><th>{$strTelephone}</th><td><input maxlength='50' name='phone' size='35'";
    if ($_SESSION['formdata']['add_contact']['phone'] != '')
    {
        echo "value='{$_SESSION['formdata']['add_contact']['phone']}'";
    }
    echo "/> ";

    echo "<label>";
    html_checkbox('dataprotection_phone', 'No');
    echo "{$strTelephone} {$strDataProtection}</label>";
    echo "</td></tr>\n";

    echo "<tr><th>{$strMobile}</th><td><input maxlength='100' name='mobile' size='35'";
    if ($_SESSION['formdata']['add_contact']['mobile'] != '')
    {
        echo "value='{$_SESSION['formdata']['add_contact']['mobile']}'";
    }
    echo "/></td></tr>\n";

    echo "<tr><th>{$strFax}</th><td><input maxlength='50' name='fax' size='35'";
    if ($_SESSION['formdata']['add_contact']['fax'])
    {
        echo "value='{$_SESSION['formdata']['add_contact']['fax']}'";
    }
    echo "/></td></tr>\n";

    echo "<tr><th>{$strAddress}</th><td><label>";
    html_checkbox('dataprotection_address', 'No');
    echo " {$strAddress} {$strDataProtection}</label></td></tr>\n";
    echo "<tr><th></th><td><label><input type='checkbox' name='usesiteaddress' value='yes' onclick=\"toggleDiv('hidden')\" /> {$strSpecifyAddress}</label></td></tr>\n";
    echo "<tbody id='hidden' style='display:none'>";
    echo "<tr><th>{$strAddress1}</th><td><input maxlength='255' name='address1' size='35' /></td></tr>\n";
    echo "<tr><th>{$strAddress2}</th><td><input maxlength='255' name='address2' size='35' /></td></tr>\n";
    echo "<tr><th>{$strCity}</th><td><input maxlength='255' name='city' size='35' /></td></tr>\n";
    echo "<tr><th>{$strCounty}</th><td><input maxlength='255' name='county' size='35' /></td></tr>\n";
    echo "<tr><th>{$strCountry}</th><td>".country_drop_down('country', $CONFIG['home_country'])."</td></tr>\n";
    echo "<tr><th>{$strPostcode}</th><td><input maxlength='255' name='postcode' size='35' /></td></tr>\n";
    echo "</tbody>";
    echo "<tr><th>{$strNotes}</th><td><textarea cols='60' rows='5' name='notes'>";
    if ($_SESSION['formdata']['add_contact']['notes'] != '')
    {
        echo $_SESSION['formdata']['add_contact']['notes'];
    }
    echo "</textarea></td></tr>\n";
    echo "</table>\n\n";
    echo "<p><input name='submit' type='submit' value=\"{$strAddContact}\" /></p>";
    echo "</form>\n";

    //cleanup form vars
    clear_form_data('add_contact');
    echo "<h5 class='warning'>{$strAvoidDupes}.</h5>";
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
        if (!empty($dataprotection_email)) $dataprotection_email='Yes'; else $dataprotection_email='No';
        if (!empty($dataprotection_phone)) $dataprotection_phone='Yes'; else $dataprotection_phone='No';
        if (!empty($dataprotection_address)) $dataprotection_address='Yes'; else $dataprotection_address='No';

        // generate username and password

        $username = strtolower(substr($surname, 0, strcspn($surname, " ")));
        $password = generate_password();

        $sql  = "INSERT INTO `{$dbContacts}` (username, password, salutation, forenames, surname, jobtitle, ";
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
