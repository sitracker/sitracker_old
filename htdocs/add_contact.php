<?php
// add_contact.php - Adds a new contact
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// This Page Is Valid XHTML 1.0 Transitional!  31Oct05

@include('set_include_path.inc.php');
$permission=1; // Add new contact

require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

// External variables
$siteid = mysql_real_escape_string($_REQUEST['siteid']);
$submit = $_REQUEST['submit'];
// if($CONFIG['debug'])
//     $debug .= print_r($_SESSION['formdata']);
//
//     echo "<p class='error'>Form Error</p>";
if (empty($submit) OR !empty($_SESSION['formerrors']))
{
    include('htmlheader.inc.php');
    ?>
    <script type="text/javascript" src="scripts/dojo/dojo.js"></script>
    <script type='text/javascript'>
    function confirm_submit()
    {
        return window.confirm('<?php echo $strAddContractConfirm ?>');
    }
    dojo.require("dojo.widget.ComboBox");
    </script>
    <?php
    echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/contact.png' width='32' height='32' alt='' /> ";
    echo "{$strNewContact}</h2>";

    echo show_errors();

    //cleanup errors
    $_SESSION['formerrors'] = NULL;

    echo "<h5>".sprintf($strMandatoryMarked, "<sup class='red'>*</sup>")."</h5>";
    echo "<form name='contactform' action='{$_SERVER['PHP_SELF']}' method='post' onsubmit='return confirm_submit();'>";
    echo "<table align='center' class='vertical'>";
    echo "<tr><th>{$strName} <sup class='red'>*</sup><br /></th>\n";

    echo "<td><table><tr><td align='center'>Salutation<br /><input maxlength='50' name='salutation' title='Salutation (Mr, Mrs, Miss, Dr. etc.)' size='7'"; //FIXME i18n
    if($_SESSION['formdata']['salutation'] != '')
        echo "value='{$_SESSION['formdata']['salutation']}'";
    echo "/></td>\n";

    echo "<td align='center'>{$strTitle}<br />";
    echo "<input maxlength='100' name='forenames' size='15' title='Firstnames (or initials)'";
    if($_SESSION['formdata']['forenames'] != '')
        echo "value='{$_SESSION['formdata']['forenames']}'";
    echo "/></td>\n";

    echo "<td align='center'>{$strSurname}<br /><input maxlength='100' name='surname' size='20' title=\"{$strSurname}\"";
    if($_SESSION['formdata']['surname'] != '')
        echo "value='{$_SESSION['formdata']['surname']}'";
    echo " /></td>";
    echo "</tr></table></tr>\n";

    echo "<tr><th>{$strJobTitle}</th><td><input maxlength='255' name='jobtitle' size='35' title='e.g. Purchasing Manager'";
    if($_SESSION['formdata']['jobtitle'] != '')
        echo "value='{$_SESSION['formdata']['jobtitle']}'";
    echo " />";
    //FIXME do this one
    echo "<tr><th>{$strSite} <sup class='red'>*</sup></th><td>";
//     ".site_drop_down('siteid',$siteid)."</td></tr>\n";
    echo "<input dojoType='ComboBox' dataUrl='autocomplete.php?action=sites' style='width: 300px;' name='search_string' />";

    echo "<tr><th>{$strDepartment}</th><td><input maxlength='255' name='department' size='35'";
    if($_SESSION['formdata']['department'] != '')
        echo "value='{$_SESSION['formdata']['department']}'";
    echo "/></td></tr>\n";

    echo "<tr><th>{$strEmail} <sup class='red'>*</sup></th><td><input maxlength='100' name='email' size='35'";
    if($_SESSION['formdata']['email'])
        echo "value='{$_SESSION['formdata']['email']}'";
    echo "/> ";

    //FIXME do this one
    echo "<label>";
    html_checkbox('dataprotection_email', 'No');
    echo "{$strEmail} {$strDataProtection}</label>";
    echo "</td></tr>\n";

    echo "<tr><th>{$strTelephone}</th><td><input maxlength='50' name='phone' size='35'";
    if($_SESSION['formdata']['phone'] != '')
        echo "value='{$_SESSION['formdata']['phone']}'";
    echo "/> ";

    //FIXME do this one
    echo "<label>";
    html_checkbox('dataprotection_phone', 'No');
    echo "{$strTelephone} {$strDataProtection}</label>";
    echo "</td></tr>\n";

    echo "<tr><th>{$strMobile}</th><td><input maxlength='100' name='mobile' size='35'";
    if($_SESSION['formdata']['mobile'] != '')
        echo "value='{$_SESSION['formdata']['mobile']}'";
    echo "/></td></tr>\n";

    echo "<tr><th>{$strFax}</th><td><input maxlength='50' name='fax' size='35'";
    if($_SESSION['formdata']['fax'])
        echo "value='{$_SESSION['formdata']['fax']}'";
    echo "/></td></tr>\n";

    //FIXME all of these
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
    if($_SESSION['formdata']['notes'] != '')
        echo $_SESSION['formdata']['notes'];
    echo "</textarea></td></tr>\n";
    echo "</table>\n\n";
    echo "<p><input name='submit' type='submit' value=\"{$strAddContact}\" /></p>";
    echo "</form>\n";

    //cleanup form vars
    $_SESSION['formdata'] = NULL;
    echo "<h5 class='warning'>{$strAvoidDupes}.</h5>";
    include('htmlfooter.inc.php');
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

    $_SESSION['formdata'] = $_REQUEST;

    $errors = 0;
    // check for blank name
    if ($surname == "")
    {
        $errors++;
        $_SESSION['formerrors']['surname'] = $strMustEnterSurname;
    }
    // check for blank site
    if ($siteid == '')
    {
        $errors++;
        $_SESSION['formerrors']['siteid'] = $strMustSelectCustomerSite;
    }
    // check for blank email
    if ($email == "" OR $email=='none' OR $email=='n/a')
    {
        $errors++;
        $_SESSION['formerrors']['email'] = $strMustEnterEmail;
    }
    if ($siteid==0 OR $siteid=='')
    {
        $errors++;
        $_SESSION['formerrors']['siteid'] = $strMustSelectSite;
    }
    // Check this is not a duplicate
    $sql = "SELECT id FROM contacts WHERE email='$email' AND LCASE(surname)=LCASE('$surname') LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_num_rows($result) >= 1)
    {
        $errors++;
        $_SESSION['formerrors']['duplicate'] = $strContactRecordExists;
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
            html_redirect("contact_details.php?id=$newid");
        }
    }
    else html_redirect("add_contact.php", FALSE);

}
?>