<?php
/*
portal/details.inc.php - Page to view and edit your contact details in the portal included by ../portal.php

SiT (Support Incident Tracker) - Support call tracking system
Copyright (C) 2000-2008 Salford Software Ltd. and Contributors

This software may be used and distributed according to the terms
of the GNU General Public License, incorporated herein by reference.
*/

include 'portalheader.inc.php';

//if new details posted
if (cleanvar($_REQUEST['action']) == 'update')
{
    if($CONFIG['portal_usernames_can_be_changed'] AND contact)
    {
        $username = cleanvar($_REQUEST['username']);
        $oldusername = cleanvar($_REQUEST['oldusername']);
    }
    $forenames = cleanvar($_REQUEST['forenames']);
    $surname = cleanvar($_REQUEST['surname']);
    $department = cleanvar($_REQUEST['department']);
    $address1 = cleanvar($_REQUEST['address1']);
    $address2 = cleanvar($_REQUEST['address2']);
    $county = cleanvar($_REQUEST['county']);
    $country = cleanvar($_REQUEST['country']);
    $postcode = cleanvar($_REQUEST['postcode']);
    $phone = cleanvar($_REQUEST['phone']);
    $mobile = cleanvar($_REQUEST['mobile']);
    $fax = cleanvar($_REQUEST['fax']);
    $email = cleanvar($_REQUEST['email']);
    $newpass = cleanvar($_REQUEST['newpassword']);
    $newpass2 = cleanvar($_REQUEST['newpassword2']);

    $errors = 0;

    // VALIDATION CHECKS */
    if($CONFIG['portal_usernames_can_be_changed'] AND ($oldusername != $username))
    {
        if (!valid_username($username))
        {
            $errors++;
            echo "<p class='error'>{$strInvalidUsername}</p>\n";
        }
    }
    
    if(!empty($newpass) AND empty($newpass2))
    {
        $errors++;
        echo "<p class='error'>{$strYouMustEnterYourNewPasswordTwice}</p>\n";
    }
    elseif($newpass != $newpass2)
    {
        $errors++;
        echo "<p class='error'>{$strPasswordsDoNotMatch}</p>";
    }
    
    if ($surname == '')
    {
        $errors++;
        echo "<p class='error'>".sprintf($strYouMustEnter, $strSurname)."</p>\n";
    }

    if ($email == "" OR $email=='none' OR $email=='n/a')
    {
        $errors++;
        echo "<p class='error'>{$strMustEnterEmail}</p>\n";
    }

    if ($errors == 0)
    {
        $updatesql = "UPDATE `{$dbContacts}` SET username='$username', forenames='$forenames', surname='$surname', ";
        $updatesql .= "department='$department', address1='$address1', address2='$address2', ";
        $updatesql .= "county='$county', country='$country', postcode='$postcode', ";
        $updatesql .= "phone='$phone', mobile='$mobile', fax='$fax', email='$email', password='$newpass' ";
        $updatesql .= "WHERE id='{$_SESSION['contactid']}'";
        mysql_query($updatesql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    }
    
    html_redirect($_SERVER['PHP_SELF']);
}
else
{
    echo "<h2>{$strYourDetails}</h2>";
    $sql = "SELECT c.username, c.password, c.forenames, c.surname, c.department, c.address1, c.address2, ";
    $sql .= "c.county, c.country, c.postcode, c.phone, c.mobile, c.fax, c.email ";
    $sql .= "FROM `{$dbContacts}` AS c, `{$dbSites}` AS s ";
    $sql .= "WHERE c.siteid = s.id ";
    $sql .= "AND c.id={$_SESSION['contactid']}";
    $query = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $user = mysql_fetch_object($query);
    
    echo "<form action='$_SERVER[PHP_SELF]?action=update' method='post'>";
    echo "<table align='center' class='vertical'>";
    echo "<tr><th colspan='2'><h3><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/contact.png' width='32' height='32' alt='' />";
    echo "{$user->forenames} {$user->surname}</h3></th></tr>\n";
    if($CONFIG['portal_usernames_can_be_changed'])
    {
        echo "<tr><th>{$strUsername}: </th><td><input name='username' value='{$user->username}' /></td></tr>";
        echo "<input name='oldusername' value='{$user->username}' type='hidden' />";
    
    }
    echo "<tr><th>{$strForenames}: </th><td><input name='forenames' value='{$user->forenames}' /></td></tr>";
    echo "<tr><th>{$strSurname}: </th><td><input name='surname' value='{$user->surname}' /></td></tr>";
    echo "<tr><th>{$strDepartment}: </th><td><input name='department' value='{$user->department}' /></td></tr>";
    echo "<tr><th>{$strAddress1}: </th><td><input name='address1' value='{$user->address1}' /></td></tr>";
    echo "<tr><th>{$strAddress2}: </th><td><input name='address2' value='{$user->address2}' /></td></tr>";
    echo "<tr><th>{$strCounty}: </th><td><input name='county' value='{$user->county}' /></td></tr>";
    echo "<tr><th>{$strCountry}: </th><td><input name='country' value='{$user->country}' /></td></tr>";
    echo "<tr><th>{$strPostcode}: </th><td><input name='postcode' value='{$user->postcode}' /></td></tr>";
    echo "<tr><th>{$strTelephone}: </th><td><input name='phone' value='{$user->phone}' /></td></tr>";
    echo "<tr><th>{$strMobile}: </th><td><input name='mobile' value='{$user->mobile}' /></td></tr>";
    echo "<tr><th>{$strFax}: </th><td><input name='fax' value='{$user->fax}' /></td></tr>";
    echo "<tr><th>{$strEmail}: </th><td><input name='email' value='{$user->email}' /></td></tr>";
    echo "<tr><th>{$strNewPassword}: </th><td><input name='newpassword' value='' type='password' /></td></tr>";
    echo "<tr><th>{$strRepeat}: </th><td><input name='newpassword2' value='' type='password' /></td></tr>";
    echo "</table>";
    echo "<p align='center'><input type='submit' value='{$strUpdate}' /></p></form>";
    
    include 'htmlfooter.inc.php';
}
?>