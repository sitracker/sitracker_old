<?php
// add_site.php - Form for adding sites
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
// This Page Is Valid XHTML 1.0 Transitional! 27Oct05

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
    ?>
    <script type='text/javascript'>
    function confirm_submit()
    {
        return window.confirm('Are you sure you want to add this site?');
    }
    </script>
    <h2>Add New Site</h2>
    <h5>Mandatory fields are marked <sup class='red'>*</sup></h5>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>?action=add" method="post" onsubmit="return confirm_submit()">
    <table align='center'>
    <tr><th>Name: <sup class="red">*</sup></th><td><input maxlength="255" name="name" size="30" /></td></tr>
    <tr><th>Department: <sup class="red">*</sup></th><td><input maxlength="255" name="department" size="30" /></td></tr>
    <tr><th>Address1:<sup class="red">*</sup></th><td><input maxlength="255" name="address1" size="30" /></td></tr>
    <tr><th>Address2:</th><td><input maxlength="255" name="address2" size="30" /></td></tr>
    <tr><th>City:</th><td><input maxlength="255" name="city" size="30" /></td></tr>
    <tr><th>County:</th><td><input maxlength="255" name="county" size="30" /></td></tr>
    <tr><th>Country: <sup class="red">*</sup></th><td><?php echo country_drop_down('country', $CONFIG['home_country']); ?></td></tr>
    <tr><th>Postcode:</th><td><input maxlength="255" name="postcode" size="30" /></td></tr>
    <tr><th>Telephone:</th><td><input maxlength="255" name="telephone" size="30" /></td></tr>
    <tr><th>Fax:</th><td><input maxlength="255" name="fax" size="30" /></td></tr>
    <tr><th>Email: <sup class="red">*</sup></th><td><input maxlength="255" name="email" size="30" /></td></tr>
    <tr><th>Website:</th><td><input maxlength="255" name="websiteurl" size="30" /></td></tr>
    <tr><th>Site Type:</th><td>
    <?php echo sitetype_drop_down('typeid', 1) ?>
    </td></tr>
    <tr><th>Salesperson:</th><td>
    <?php
    user_drop_down('owner', 0, $accepting=FALSE)
    ?>
    </td></tr>
    <tr><th>Notes:</th><td><textarea cols="30" name="notes" rows="5"></textarea></td></tr>
    </table>
    <p><input name="submit" type="submit" value="Add Site" /></p>
    <p class='warning'>Please ensure that the site does not already exist before adding a new site</p>
    </form>
    <?php
    include('htmlfooter.inc.php');
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

    // Add new site
    include('htmlheader.inc.php');

    $errors = 0;
    // check for blank name
    if ($name == "")
    {
        $errors = 1;
        $errors_string .= "You must enter a name for the site";
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
            confirmation_page("2", "site_details.php?id=$id", "<h2>Site Addition Successful</h2><p align='center'>Please wait while you are redirected...</p>");
        }
    }
    else
    {
        throw_user_error($errors_string);
    }
}
?>
