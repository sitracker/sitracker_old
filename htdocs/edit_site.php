<?php
// edit_site.php - Form for editing a site
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// This Page Is Valid XHTML 1.0 Transitional!  6Feb06

$permission=3; // Edit existing site details
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

// External variables
$action = $_REQUEST['action'];
$site = cleanvar($_REQUEST['site']);

$title="Edit Site";
include('htmlheader.inc.php');


// Show select site form
if (empty($action) OR $action == "showform" OR empty($site))
{
    ?>
    <h3>Select Site To Edit</h3>
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>?action=edit" method="post">
    <table class='vertical'>
    <tr><th>Site:</th><td><?php echo site_drop_down("site", 0); ?></td></tr>
    </table>
    <p><input name="submit" type="submit" value="Continue" /></p>
    </form>
    <?php
}
elseif ($action == "edit")
{
    //  Show edit site form
    if ($site == 0)
    {
        echo "<p class='error'>You must select a site</p>";
    }
    else
    {
        $sql="SELECT * FROM sites WHERE id='$site' ";
        $siteresult = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        while ($siterow=mysql_fetch_array($siteresult))
        {
            ?>
            <script type='text/css'>
            function confirm_submit()
            {
                return window.confirm('Are you sure you want to make these changes?');
            }
            </script>
            <?php
            echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/site.png' width='32' height='32' alt='' /> ";
            echo "Edit Site {$site} - ".site_name($site)."</h2>";
            ?>
            <form name='edit_site' action="<?php echo $_SERVER['PHP_SELF'] ?>?action=update" method="post" onsubmit="return confirm_submit()">
            <p align='center'>Mandatory fields are marked <sup class='red'>*</sup></p>
            <table align='center' class='vertical'>
            <tr><th>Name: <sup class='red'>*</sup></th><td><input maxlength="50" name="name" size="40" value="<?php echo $siterow['name'] ?>" /></td></tr>
            <tr><th>Tags:</th><td><textarea rows='2' cols='60' name='tags'><?php echo list_tags($site, 3, false); ?></textarea>
            <tr><th>Department: <sup class='red'>*</sup></th><td><input maxlength="50" name="department" size="40" value="<?php echo $siterow['department'] ?>" /></td></tr>
            <tr><th>Address1: <sup class='red'>*</sup></th><td><input maxlength="50" name="address1" size="40" value="<?php echo $siterow['address1'] ?>" /></td></tr>
            <tr><th>Address2: </th><td><input maxlength="50" name="address2" size="40" value="<?php echo $siterow['address2'] ?>" /></td></tr>
            <tr><th>City:</th><td><input maxlength="255" name="city" size="40" value="<?php echo $siterow['city'] ?>" /></td></tr>
            <tr><th>County:</th><td><input maxlength="255" name="county" size="40" value="<?php echo $siterow['county'] ?>" /></td></tr>
            <tr><th>Postcode:</th><td><input maxlength="255" name="postcode" size="40" value="<?php echo $siterow['postcode'] ?>" /></td></tr>
            <tr><th>Country:</th><td>
            <?php echo country_drop_down('country', $siterow['country']) ?>
            </td></tr>
            <tr><th>Telephone:</th><td><input maxlength="255" name="telephone" size="40" value="<?php echo $siterow['telephone'] ?>" /></td></tr>
            <tr><th>Fax:</th><td><input maxlength="255" name="fax" size="40" value="<?php echo $siterow['fax'] ?>" /></td></tr>
            <tr><th>Email:</th><td><input maxlength="255" name="email" size="40" value="<?php echo $siterow['email'] ?>" /></td></tr>
            <tr><th>Website:</th><td><input maxlength="255" name="websiteurl" size="40" value="<?php echo $siterow['websiteurl'] ?>" /></td></tr>
            <tr><th>Site Type:</th><td>
            <?php echo sitetype_drop_down('typeid', $siterow['typeid']) ?>
            </td></tr>

            <tr><th>Salesperson:</th><td>
            <?php
            user_drop_down('owner', $siterow['owner'], $accepting=FALSE)
            ?>
            </td></tr>
            <?php
            echo "<tr><th>Site Incident Pool:</th>";
            $incident_pools = explode(',', "None,{$CONFIG['incident_pools']}");
            if (array_key_exists($siterow['freesupport'], $incident_pools)==FALSE) array_unshift($incident_pools,$siterow['freesupport']);
            echo "<td>".array_drop_down($incident_pools,'incident_poolid',$siterow['freesupport'])."</td></tr>";
            ?>
            <tr><th>Active:</th><td><input type='checkbox' name='active' <?php if($siterow['active']=='true') echo "checked='".$siterow['active']."'"; ?> value='true' /></td></tr>
            <tr><th>Notes:</th><td><textarea rows="5" cols="30" name="notes"><?php echo stripslashes($siterow['notes']); ?></textarea></td></tr>
            <?php
            plugin_do('edit_site_form');
            echo "</table>\n";
            echo "<input name='site' type='hidden' value='$site' />";
            echo "<p><input name='submit' type='submit' value='Save' /></p>";
            echo "</form>";
        }
    }
}
elseif ($action == "update")
{
    // External Variables
    $incident_pools = explode(',', "0,{$CONFIG['incident_pools']}");
    $incident_quantity = $incident_pools[$_POST['incident_poolid']];
    $name = cleanvar($_POST['name']);
    $department = cleanvar($_POST['department']);
    $address1 = cleanvar($_POST['address1']);
    $address2 = cleanvar($_POST['address2']);
    $city = cleanvar($_POST['city']);
    $county = cleanvar($_POST['county']);
    $postcode = cleanvar($_POST['postcode']);
    $country = cleanvar($_POST['country']);
    $telephone = cleanvar($_POST['telephone']);
    $fax = cleanvar($_POST['fax']);
    $email = cleanvar($_POST['email']);
    $websiteurl = cleanvar($_POST['websiteurl']);
    $notes = cleanvar($_POST['notes']);
    $typeid = cleanvar($_POST['typeid']);
    $owner = cleanvar($_POST['owner']);
    $site = cleanvar($_POST['site']);
    $tags = cleanvar($_POST['tags']);
    $active = cleanvar($_POST['active']);

    // Edit site, update the database
    $errors = 0;
    // check for blank name
    if ($name == "")
    {
        $errors = 1;
        $errors_string .= "<p class='error'>You must enter a name</p>\n";
    }

    // edit site if no errors
    if ($errors == 0)
    {

        replace_tags(3, $site, $tags);
        if (isset($licenserx)) $licenserx='1'; else $licenserx='0';
        // update site

        if($active=='true') $activeStr = 'true';
        else $activeStr = 'false';

        $sql = "UPDATE sites SET name='$name', department='$department', address1='$address1', address2='$address2', city='$city', ";
        $sql .= "county='$county', postcode='$postcode', country='$country', telephone='$telephone', fax='$fax', email='$email', ";
        $sql .= "websiteurl='$websiteurl', notes='$notes', typeid='$typeid', owner='$owner', freesupport='$incident_quantity', active='$activeStr' WHERE id='$site' LIMIT 1";

        // licenserx='$licenserx'
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        else
        {
            plugin_do('edit_site_save');
            journal(CFG_LOGGING_NORMAL, 'Site Edited', "Site $site was edited", CFG_JOURNAL_SITES, $site);
            confirmation_page("2", "site_details.php?id=$site", "<h2>Site Update Successful</h2><p align='center'>Please wait while you are redirected...</p>");
        }
    }
    else
    {
        echo $errors_string;
    }
}
include('htmlfooter.inc.php');
?>