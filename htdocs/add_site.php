<?php
// add_site.php - Form for adding sites
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
// This Page Is Valid XHTML 1.0 Transitional! 27Oct05

@include('set_include_path.inc.php');
$permission=2; // Add new site
require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

// External variables
$action = $_REQUEST['action'];

if ($action == "showform" OR $action == '')
{
    // Show add site form
    include('htmlheader.inc.php');
    echo show_form_errors('add_site');
    clear_form_errors('add_site');

    echo "<script type='text/javascript'>";
    echo 'function confirm_submit()
    {
        return window.confirm(\'Are you sure you want to add this site?\');
    }
    </script>';
    echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/site.png' width='32' height='32' alt='' /> ";
    echo "{$strNewSite}</h2>";
    echo "<h5>".sprintf($strMandatoryMarked, "<sup class='red'>*</sup>")."</h5>";
    echo "<form action='{$_SERVER['PHP_SELF']}?action=add' method='post' onsubmit='return confirm_submit();'>";
    echo "<table align='center' class='vertical'>";
    echo "<tr><th>{$strName} <sup class='red'>*</sup></th><td><input maxlength='255' name='name' size='30' ";
    if($_SESSION['formdata']['add_site']['name'] != "")
        echo "value='{$_SESSION['formdata']['add_site']['name']}'";
    echo " /></td></tr>\n";

    echo "<tr><th>{$strDepartment}</th><td><input maxlength='255' name='department' size='30'";
    if($_SESSION['formdata']['add_site']['department'] != "")
        echo "value='{$_SESSION['formdata']['add_site']['department']}'";
    echo " /></td></tr>\n";

    echo "<tr><th>{$strAddress1}<sup class='red'>*</sup></th><td><input maxlength='255' name='address1' size='30'";
    if($_SESSION['formdata']['add_site']['address1'] != "")
        echo "value='{$_SESSION['formdata']['add_site']['address1']}'";
    echo " /></td></tr>\n";

    echo "<tr><th>{$strAddress2}</th><td><input maxlength='255' name='address2' size='30'";
    if($_SESSION['formdata']['add_site'][''] != "address2")
        echo "value='{$_SESSION['formdata']['add_site']['address2']}'";
    echo " /></td></tr>\n";

    echo "<tr><th>{$strCity}</th><td><input maxlength='255' name='city' size='30'";
    if($_SESSION['formdata']['add_site']['city'] != "")
        echo "value='{$_SESSION['formdata']['add_site']['city']}'";
    echo " /></td></tr>\n";

    echo "<tr><th>{$strCounty}</th><td><input maxlength='255' name='county' size='30'";
    if($_SESSION['formdata']['add_site']['county'] != "")
        echo "value='{$_SESSION['formdata']['add_site']['county']}'";
    echo " /></td></tr>\n";

    echo "<tr><th>{$strCountry} <sup class='red'>*</sup></th><td>";
    if($_SESSION['formdata']['add_site']['country'] != "")
        echo country_drop_down('country', $_SESSION['formdata']['add_site']['country'])."</td></tr>\n";
    else
        echo country_drop_down('country', $CONFIG['home_country'])."</td></tr>\n";

    echo "<tr><th>{$strPostcode}</th><td><input maxlength='255' name='postcode' size='30'";
    if($_SESSION['formdata']['add_site']['postcode'] != "")
        echo "value='{$_SESSION['formdata']['add_site']['postcode']}'";
    echo " /></td></tr>\n";

    echo "<tr><th>{$strTelephone}</th><td><input maxlength='255' name='telephone' size='30'";
    if($_SESSION['formdata']['add_site']['telephone'] != "")
        echo "value='{$_SESSION['formdata']['add_site']['telephone']}'";
    echo " /></td></tr>\n";
    ;
    echo "<tr><th>{$strEmail} <sup class='red'>*</sup></th><td><input maxlength='255' name='email' size='30'";
    if($_SESSION['formdata']['add_site']['email'] != "")
        echo "value='{$_SESSION['formdata']['add_site']['email']}'";
    echo " /></td></tr>\n";

    echo "<tr><th></th><td><a href=\"javascript:toggleDiv('hidden')\">{$strMore}</a></td></tr>\n";
    echo "<tbody id='hidden' class='hidden' style='display:none'>";
    echo "<tr><th>{$strFax}</th><td><input maxlength='255' name='fax' size='30'";
    if($_SESSION['formdata']['add_site']['fax'] != "")
        echo "value='{$_SESSION['formdata']['add_site']['fax']}'";
    echo " /></td></tr>\n";

    echo "<tr><th>{$strWebsite}</th><td><input maxlength='255' name='websiteurl' size='30'";
    if($_SESSION['formdata']['add_site']['websiteurl'] != "")
        echo "value='{$_SESSION['formdata']['add_site']['websiteurl']}'";
    echo " /></td></tr>\n";

    echo "<tr><th>{$strSiteType}</th><td>";
    if($_SESSION['formdata']['add_site']['typeid'] != "")
        echo sitetype_drop_down('typeid', $_SESSION['formdata']['add_site']['typeid'])."</td></tr>\n";
    else
        echo sitetype_drop_down('typeid', 1)."</td></tr>\n";

    echo "<tr><th>{$strSalesperson}</th><td>";
    if($_SESSION['formdata']['add_site']['owner'] != "")
        user_drop_down('owner', $_SESSION['formdata']['add_site']['owner'], $accepting=FALSE);
    else
        user_drop_down('owner', 0, $accepting=FALSE);

    echo "</td></tr>\n";
    echo "<tr><th>{$strNotes}</th><td><textarea cols='30' name='notes' rows='5'>";
    if($_SESSION['formdata']['add_site']['notes'] != "")
        echo $_SESSION['formdata']['add_site']['notes'];

    echo "</textarea></td></tr>\n";
    echo "</tbody>";
    echo "</table>\n";
    echo "<p><input name='submit' type='submit' value=\"{$strAddSite}\" /></p>";
    echo "<p class='warning'>{$strAvoidDupes}</p>\n";
    echo "</form>\n";
    include('htmlfooter.inc.php');

    clear_form_data('add_site');
}
elseif ($action == "add")
{
    // External variables
    $name = cleanvar($_POST['name']);
    $department = cleanvar($_POST['department']);
    $address1 = cleanvar($_POST['address1']);
    $address2 = cleanvar($_POST['address2']);
    $city = cleanvar($_POST['city']);
    $county = cleanvar($_POST['county']);
    $country = cleanvar($_POST['country']);
    $postcode = cleanvar($_POST['postcode']);
    $telephone = cleanvar($_POST['telephone']);
    $fax = cleanvar($_POST['fax']);
    $email = cleanvar($_POST['email']);
    $websiteurl = cleanvar($_POST['websiteurl']);
    $notes = cleanvar($_POST['notes']);
    $typeid = cleanvar($_POST['typeid']);
    $owner = cleanvar($_POST['owner']);

    $_SESSION['formdata']['add_site'] = $_REQUEST;

    include('htmlheader.inc.php');

    $errors = 0;
    // check for blank name
    if ($name == "")
    {
        $errors++;
        $_SESSION['formerrors']['add_site']['name'] = "Site name cannot be blank";
    }
    if ($address1 == "")
    {
        $errors++;
        $_SESSION['formerrors']['add_site']['address1'] = "Address1 cannot be blank";
    }
    if ($email == "")
    {
        $errors++;
        $_SESSION['formerrors']['add_site']['email'] = "Email cannot be blank";
    }

    // add site if no errors
    if ($errors == 0)
    {
        if ($owner=='') $owner=0;
        $sql  = "INSERT INTO sites (name, department, address1, address2, city, county, country, postcode, telephone, fax, email, websiteurl, notes, typeid, owner) ";
        $sql .= "VALUES ('$name', '$department' ,'$address1', '$address2', '$city', '$county', '$country', '$postcode', ";
        $sql .= "'$telephone', '$fax', '$email', '$websiteurl', '$notes', '$typeid','$owner')";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        // show error message if addition failed
        if (!$result)
        {
            echo "<p class='error'>Addition of site failed</p>\n";
        }
        // show success message
        else
        {
            $id=mysql_insert_id();

            plugin_do('site_created');
            journal(CFG_LOGGING_NORMAL, 'Site Added', "Site $id was added", CFG_JOURNAL_SITES, $id);
            html_redirect("site_details.php?id={$id}");
        }
        clear_form_data('add_site');
        clear_form_errors('add_site');
    }
    else
    {
        html_redirect("add_site.php", FALSE);
    }
}
?>
