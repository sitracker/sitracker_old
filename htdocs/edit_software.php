<?php
// edit_software.php - Form for editing software
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

@include ('set_include_path.inc.php');
$permission=56; // Add Software

require ('db_connect.inc.php');
require ('functions.inc.php');
// This page requires authentication
require ('auth.inc.php');

// External variables
$id = cleanvar($_REQUEST['id']);
$action = cleanvar($_REQUEST['action']);

if (empty($action) OR $action=='edit')
{
    $title = $strEditSkill;
    // Show add product form
    include ('htmlheader.inc.php');
    ?>
    <script type="text/javascript">
    function confirm_submit()
    {
        return window.confirm('Are you sure you want to edit this Skill?');
    }
    </script>
    <?php
    echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/skill.png' width='32' height='32' alt='' /> ";
    echo "$title</h2>";
    $sql = "SELECT * FROM software WHERE id='$id' LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    while ($software = mysql_fetch_object($result))
    {
        echo "<h5>".sprintf($strMandatoryMarked,"<sup class='red'>*</sup>")."</h5>";
        echo "<form name='editsoftware' action='{$_SERVER['PHP_SELF']}' method='post' onsubmit='return confirm_submit()'>";
        echo "<table class='vertical'>";
        echo "<tr><th>{$strVendor}:</th><td>".vendor_drop_down('vendor',$software->vendorid)."</td></tr>\n";
        echo "<tr><th>{$strSkill}: <sup class='red'>*</sup></th><td><input maxlength='50' name='name' size='30' value=\"{$software->name}\" /></td></tr>";
        echo "<tr><th>{$strLifetime}:</th><td>";
        echo "<input type='text' name='lifetime_start' id='lifetime_start' size='10' value='";
        if ($software->lifetime_start > 1) echo date('Y-m-d',mysql2date($software->lifetime_start));
        echo "' /> ";
        echo date_picker('editsoftware.lifetime_start');
        echo " To: ";
        echo "<input type='text' name='lifetime_end' id='lifetime_end' size='10' value='";
        if ($software->lifetime_end > 1) echo date('Y-m-d',mysql2date($software->lifetime_end));
        echo "' /> ";
        echo date_picker('editsoftware.lifetime_end');
        echo "</td></tr>";
        echo "<tr><th>{$strTags}:</th>";
        echo "<td><textarea rows='2' cols='30' name='tags'>".list_tags($id, TAG_SKILL, false)."</textarea></td></tr>\n";
        echo "</table>";
    }
    echo "<input type='hidden' name='id' value='$id' />";
    echo "<input type='hidden' name='action' value='save' />";
    echo "<p align='center'><input name='submit' type='submit' value='{$strSave}' /></p>";
    echo "</form>\n";
    echo "<p align='center'><a href='products.php'>Return to products list without saving</a></p>";
    include ('htmlfooter.inc.php');
}
elseif ($action=='delete')
{
    // Delete
    // First check there are no incidents using this software
    $sql = "SELECT count(id) FROM incidents WHERE softwareid='$id'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    list($countincidents) = mysql_fetch_row($result);
    if ($countincidents >=1 )
    {
        include ('htmlheader.inc.php');
        echo "<p class='error'>Sorry, this skill cannot be deleted because it has been associated with one or more incidents</p>";
        echo "<p align='center'><a href='products.php?display=skills'>Return to products list</a></p>";
        include ('htmlfooter.inc.php');
    }
    else
    {
        $sql = "DELETE FROM software WHERE id='$id'";
        mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        $sql = "DELETE FROM softwareproducts WHERE softwareid='$id'";
        mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        $sql = "DELETE FROM usersoftware WHERE softwareid='$id'";
        mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        journal(CFG_LOGGING_DEBUG, 'Skill Deleted', "Skill $id was deleted", CFG_JOURNAL_DEBUG, $id);
        html_redirect("products.php?display=skills");
    }
}
else
{
      // Save
    // External variables
    $name = cleanvar($_REQUEST['name']);
    $vendor = cleanvar($_REQUEST['vendor']);
    $tags = cleanvar($_REQUEST['tags']);
    if (!empty($_REQUEST['lifetime_start'])) $lifetime_start = date('Y-m-d',strtotime($_REQUEST['lifetime_start']));
    else $lifetime_start = '';
    if (!empty($_REQUEST['lifetime_end'])) $lifetime_end = date('Y-m-d',strtotime($_REQUEST['lifetime_end']));
    else $lifetime_end = '';

    // Add new
    $errors = 0;

        // check for blank name
    if ($name == "")
    {
        $errors = 1;
        $errors_string .= "<p class='error'>You must enter a name</p>\n";
    }
    // add product if no errors
    if ($errors == 0)
    {
        replace_tags(TAG_SKILL, $id, $tags);

        $sql = "UPDATE software SET ";
        $sql .= "name='$name', vendorid='{$vendor}', lifetime_start='$lifetime_start', lifetime_end='$lifetime_end' ";
        $sql .= "WHERE id = '$id'";
        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        else
        {
            $id=mysql_insert_id();
            journal(CFG_LOGGING_DEBUG, 'Skill Edited', "Skill $id was edited", CFG_JOURNAL_DEBUG, $id);
            html_redirect("products.php?display=skills");
        }
    }
    else
    {
        include ('htmlheader.inc.php');
        echo $errors_string;
        include ('htmlfooter.inc.php');
    }
}
?>