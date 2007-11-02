<?php
// add_user.php - Form for adding users
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

$permission=20; // Add Users

require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

$title = $strAddUser;

// External variables
$submit = $_REQUEST['submit'];

include('htmlheader.inc.php');
?>
<script type="text/javascript">
function confirm_submit()
{
    return window.confirm('Are you sure you want to add this user?');
}
</script>

<?php
if (empty($submit))
{
    // Show add user form
    $gsql = "SELECT * FROM groups ORDER BY name";
    $gresult = mysql_query($gsql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    while ($group = mysql_fetch_object($gresult))
    {
        $grouparr[$group->id]=$group->name;
    }

    $numgroups = count($grouparr);

    function group_drop_down($name, $selected)
    {
        global $grouparr, $numgroups;
        $html = "<select name='$name'>";
        $html .= "<option value='0'>{$strNone}</option>\n";
        if ($numgroups >= 1)
        {
            foreach($grouparr AS $groupid => $groupname)
            {
                $html .= "<option value='$groupid'";
                if ($groupid == $selected) $html .= " selected='selected'";
                $html .= ">$groupname</option>\n";
            }
        }
        $html .= "</select>\n";
        return $html;
    }
    echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/user.png' width='32' height='32' alt='' /> ";
    echo "{$strNewUser}</h2>";
    echo "<h5>{$strMandatoryMarked} <sup class='red'>*</sup></h5>";
    echo "<form action='{$_SERVER['PHP_SELF']}' method='post' onsubmit='return confirm_submit();'>";
    echo "<table align='center'>\n";
    echo "<tr><th>{$strRealName}: <sup class='red'>*</sup></th><td><input maxlength='50' name='realname' size='30' /></td></tr>\n";
    echo "<tr><th>{$strUsername}: <sup class='red'>*</sup></th><td><input maxlength='50' name='username' size='30' /></td></tr>\n";
    echo "<tr id='password'><th>{$strPassword}: <sup class='red'>*</sup></th><td><input maxlength='50' name='password' size='30' /></td></tr>\n";
    echo "<tr><th>{$strGroup}:</th>";
    echo "<td>".group_drop_down('groupid', 0)."</td>";
    echo "</tr>";
    echo "<tr><th>{$strRole}:</th>";
    echo "<td>".role_drop_down('roleid', 1)."</td>";
    echo "</tr>";
    echo "<tr><th>{$strJobTitle}: <sup class='red'>*</sup></th><td><input maxlength='50' name='jobtitle' size='30' /></td></tr>\n";
    echo "<tr id='email'><th>{$strEmail}: <sup class='red'>*</sup></th><td><input maxlength='50' name='email' size='30' /></td></tr>\n";
    echo "<tr><th>{$strTelephone}:</th><td><input maxlength='50' name='phone' size='30' /></td></tr>\n";
    echo "<tr><th>{$strMobile}:</th><td><input maxlength='50' name='mobile' size='30' /></td></tr>\n";
    echo "<tr><th>{$strFax}:</th><td><input maxlength='50' name='fax' size='30' /></td></tr>\n";
    echo "<tr><th>{$strHolidayEntitlement} Entitlement:</th><td><input maxlength='3' name='holiday_entitlement' size='3' /> days</td></tr>\n";
    // i18n ^^
    plugin_do('add_user_form');
    echo "</table>\n";
    echo "<p><input name='submit' type='submit' value=\"$strAddUser}\" /></p>";
    echo "</form>\n";
}
else
{
    // External variables
    $username = mysql_escape_string(strtolower(trim(strip_tags($_REQUEST['username']))));
    $realname = cleanvar($_REQUEST['realname']);
    $password = mysql_escape_string($_REQUEST['password']);
    $groupid = cleanvar($_REQUEST['groupid']);
    $roleid = cleanvar($_REQUEST['roleid']);
    $jobtitle = cleanvar($_REQUEST['jobtitle']);
    $email = cleanvar($_REQUEST['email']);
    $phone = cleanvar($_REQUEST['phone']);
    $mobile = cleanvar($_REQUEST['mobile']);
    $fax = cleanvar($_REQUEST['fax']);
    $holiday_entitlement = cleanvar($_REQUEST['holiday_entitlement']);

    // Add user
    $errors = 0;
    // check for blank real name
    if ($realname == "")
    {
        $errors = 1;
        echo "<p class='error'>You must enter a real name</p>\n";
    }
    // check for blank username
    if ($username == "")
    {
        $errors = 1;
        echo "<p class='error'>You must enter a username</p>\n";
    }
    // check for blank password
    if ($password == "")
    {
        $errors = 1;
        echo "<p class='error'>You must enter a password</p>\n";
    }
    // check for blank job title
    if ($jobtitle == "")
    {
        $errors = 1;
        echo "<p class='error'>You must enter a job title</p>\n";
    }
    // check for blank email
    if ($email == "")
    {
        $errors = 1;
        echo "<p class='error'>You must enter an email address</p>\n";
    }
    // Check username is unique
    $sql = "SELECT COUNT(id) FROM users WHERE username='$username'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    list($countexisting) = mysql_fetch_row($result);
    if ($countexisting >= 1)
    {
        $errors++;
        echo "<p class='error'>Username must be unique</p>\n";
    }

    // add information if no errors
    if ($errors == 0)
    {
        $password=strtoupper(md5($password));
        $sql = "INSERT INTO users (username, password, realname, roleid, groupid, title, email, phone, mobile, fax, status, var_style, holiday_entitlement) ";
        $sql .= "VALUES ('$username', '$password', '$realname', '$roleid', '$groupid', '$jobtitle', '$email', '$phone', '$mobile', '$fax', 1, '{$CONFIG['default_interface_style']}', '$holiday_entitlement')";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        $newuserid = mysql_insert_id();

        // Create permissions (set to none)
        $sql = "SELECT * FROM permissions";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        while ($perm = mysql_fetch_object($result))
        {
            $psql = "INSERT INTO userpermissions (userid, permissionid, granted) ";
            $psql .= "VALUES ('$newuserid', '{$perm->id}', 'false')";
            mysql_query($psql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        }

        plugin_do('user_created');

        if (!$result) echo "<p class='error'>Addition of user failed\n";
        else
        {
            journal(CFG_LOGGING_NORMAL, 'User Added', "User $username was added", CFG_JOURNAL_ADMIN, $id);
            confirmation_page("2", "manage_users.php?#userid{$newuserid}", "<h2>New User Added Successfully</h2><p align='center'>{$strPleaseWaitRedirect}...</p>");
        }
    }
}
include('htmlfooter.inc.php');
?>
