<?php
// add_user.php - Form for adding users
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

$permission=20; // Add Users

require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

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
    ?>
    <h2>Add User</h2>
    <p align='center'>Mandatory fields are marked <sup class='red'>*</sup></p>
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return confirm_submit()">
    <table>
    <tr><th>Real Name: <sup class='red'>*</sup></th><td><input maxlength="50" name="realname" size="30" /></td></tr>
    <tr><th>Username: <sup class='red'>*</sup></th><td><input maxlength="50" name="username" size="30" /></td></tr>
    <tr><th>Password: <sup class='red'>*</sup></th><td><input maxlength="50" name="password" size="30" /></td></tr>
    <?php
    echo "<tr><th>Role:</th>";
    echo "<td>".role_drop_down('roleid', 1)."</td>";
    echo "</tr>";
    ?>
    <tr><th>Job Title: <sup class='red'>*</sup></th><td><input maxlength="50" name="jobtitle" size="30" /></td></tr>
    <tr><th>Email: <sup class='red'>*</sup></th><td><input maxlength="50" name="email" size="30" /></td></tr>
    <tr><th>Phone:</th><td><input maxlength="50" name="phone" size="30" /></td></tr>
    <tr><th>Fax:</th><td><input maxlength="50" name="fax" size="30" /></td></tr>
    </table>
    <p><input name="submit" type="submit" value="Add User" /></p>
    </form>
    <?php
}
else
{
    // External variables
    $username = mysql_escape_string(strtolower(trim(strip_tags($_REQUEST['username']))));
    $realname = cleanvar($_REQUEST['realname']);
    $password = mysql_escape_string($_REQUEST['password']);
    $roleid = cleanvar($_REQUEST['roleid']);
    $jobtitle = cleanvar($_REQUEST['jobtitle']);
    $email = cleanvar($_REQUEST['email']);
    $phone = cleanvar($_REQUEST['phone']);
    $fax = cleanvar($_REQUEST['fax']);

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

    // add information if no errors
    if ($errors == 0)
    {
        $password=strtoupper(md5($password));
        $sql = "INSERT INTO users (username, password, realname, roleid, title, email, phone, fax, status) ";
        $sql .= "VALUES ('$username', '$password', '$realname', '$roleid', '$jobtitle', '$email', '$phone', '$fax', 1)";
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
        if (!$result) echo "<p class='error'>Addition of user failed\n";
        else
        {
            journal(CFG_LOGGING_NORMAL, 'User Added', "User $username was added", CFG_JOURNAL_ADMIN, $id);
            confirmation_page("2", "edit_user_permissions.php?action=edit&user={$newuserid}", "<h2>New User Added Successfully</h2><p align='center'>Please wait while you are redirected...</p>");
        }
    }
}
include('htmlfooter.inc.php');
?>