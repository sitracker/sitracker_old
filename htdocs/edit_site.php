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

// FIXME i18n

$permission=3; // Edit existing site details
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

// External variables
$action = $_REQUEST['action'];
$site = cleanvar($_REQUEST['site']);

$title = $strEditSite;
include('htmlheader.inc.php');


// Show select site form
if (empty($action) OR $action == "showform" OR empty($site))
{
    echo "<h3>{$title}</h3>";
    echo "<form action='{$_SERVER['PHP_SELF']}?action=edit' method='post'>";
    echo "<table class='vertical'>";
    echo "<tr><th>{$strSite}:</th><td>".site_drop_down("site", 0)."</td></tr>\n";
    echo "</table>\n";
    echo "<p><input name='submit' type='submit' value=\"{$strContinue}\" /></p>\n";
    echo "</form>\n";
}
elseif ($action == "edit")
{
    //  Show edit site form
    if ($site == 0)
    {
        echo "<p class='error'>You must select a site</p>"; // FIXME i18n error message
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
            echo "{$strEditSite}: {$site} - ".site_name($site)."</h2>";
            ?>
            <form name='edit_site' action="<?php echo $_SERVER['PHP_SELF'] ?>?action=update" method="post" onsubmit="return confirm_submit()">
            <?php echo "<h5>".sprintf($strMandatoryMarked,"<sup class='red'>*</sup>")."</h5>";
            echo "<table align='center' class='vertical'>";
            echo "<tr><th>{$strName}: <sup class='red'>*</sup></th><td><input maxlength='50' name='name' size='40' value=\"{$siterow['name']}\" /></td></tr>\n";
            echo "<tr><th>{$strTags}:</th><td><textarea rows='2' cols='60' name='tags'>".list_tags($site, TAG_SITE, false)."</textarea>\n";
            echo "<tr><th>{$strDepartment}: <sup class='red'>*</sup></th><td><input maxlength='50' name='department' size='40' value=\"{$siterow['department']}\" /></td></tr>\n";
            echo "<tr><th>{$strAddress1}: <sup class='red'>*</sup></th><td><input maxlength='50' name='address1' size='40' value=\"{$siterow['address1']}\" /></td></tr>\n";
            echo "<tr><th>{$strAddress2}: </th><td><input maxlength='50' name='address2' size='40' value=\"{$siterow['address2']}\" /></td></tr>\n";
            echo "<tr><th>{$strCity}:</th><td><input maxlength='255' name='city' size='40' value=\"{$siterow['city']}\" /></td></tr>\n";
            echo "<tr><th>{$strCounty}:</th><td><input maxlength='255' name='county' size='40' value=\"{$siterow['county']}\" /></td></tr>\n";
            echo "<tr><th>{$strPostcode}:</th><td><input maxlength='255' name='postcode' size='40' value=\"{$siterow['postcode']}\" /></td></tr>\n";
            echo "<tr><th>{$strCountry}:</th><td>".country_drop_down('country', $siterow['country'])."</td></tr>\n";
            echo "<tr><th>{$strTelephone}:</th><td><input maxlength='255' name='telephone' size='40' value=\"{$siterow['telephone']}\" /></td></tr>\n";
            echo "<tr><th>{$strFax}:</th><td><input maxlength='255' name='fax' size='40' value=\"{$siterow['fax']}\" /></td></tr>\n";
            echo "<tr><th>{$strEmail}:</th><td><input maxlength='255' name='email' size='40' value=\"{$siterow['email']}\" /></td></tr>\n";
            echo "<tr><th>{$strWebsite}:</th><td><input maxlength='255' name='websiteurl' size='40' value=\"{$siterow['websiteurl']}\" /></td></tr>\n";
            echo "<tr><th>{$strSiteType}:</th><td>\n";
            echo sitetype_drop_down('typeid', $siterow['typeid']);
            echo "</td></tr>\n";

            echo "<tr><th>{$strSalesperson}:</th><td>";
            user_drop_down('owner', $siterow['owner'], $accepting=FALSE);
            echo "</td></tr>\n";
            echo "<tr><th>{$strIncidentPool}:</th>";
            $incident_pools = explode(',', "{$strNone},{$CONFIG['incident_pools']}");
            if (array_key_exists($siterow['freesupport'], $incident_pools)==FALSE) array_unshift($incident_pools,$siterow['freesupport']);
            echo "<td>".array_drop_down($incident_pools,'incident_poolid',$siterow['freesupport'])."</td></tr>";
            echo "<tr><th>{$strActive}:</th><td><input type='checkbox' name='active' ";
            if ($siterow['active']=='true') echo "checked='".$siterow['active']."'";
            echo " value='true' /></td></tr>\n";
            echo "<tr><th>{$strNotes}:</th><td><textarea rows='5' cols='30' name='notes'>{$siterow['notes']}</textarea></td></tr>\n";
            plugin_do('edit_site_form');
            echo "</table>\n";
            echo "<input name='site' type='hidden' value='$site' />";
            echo "<p><input name='submit' type='submit' value='{$strSave}' /></p>";
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
            html_redirect("site_details.php?id={$site}");
            exit;
        }
    }
    else
    {
        echo $errors_string;
    }
}
include('htmlfooter.inc.php');
?>