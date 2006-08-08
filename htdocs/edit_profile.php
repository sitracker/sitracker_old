<?php
// edit_profile.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//


// This Page Is Valid XHTML 1.0 Transitional!  1Nov05

$permission=4; // Edit your profile
require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

// External variables
$submit = $_REQUEST['submit'];
$edituserpermission = user_permission($sit[2],23); // edit user
if (empty($_REQUEST['userid']) OR $_REQUEST['userid']=='current' OR $edituserpermission==FALSE) $userid = mysql_escape_string($sit[2]);
else $userid = cleanvar($_REQUEST['userid']);


if (empty($submit))
{
    include('htmlheader.inc.php');

    $sql = "SELECT * FROM users WHERE id='$userid' LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    if (mysql_num_rows($result) < 1) trigger_error("No such user ".strip_tags($userid),E_USER_ERROR);
    $user = mysql_fetch_object($result);

    // This form should use one SQL query really, not call all these functions to lookup each field
    // Need to change this sometime.

    ?>
    <h2>Edit User Profile For <?php echo $user->realname ?></h2>
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
    <table align='center' class='vertical'>
    <col width="250"></col><col width="*"></col>
    <tr><th colspan='2'>ABOUT <?php if ($userid==$sit[2]) echo "YOU"; else echo strtoupper($user->realname); ?></td></tr>
    <tr><th>Username:</th><td><?php echo $user->username; ?></td></tr>
    <?php
    echo "<tr><th>Role:</th>";
    if ($userid==$sit[2] OR $userid==1) echo "<td>".db_read_column('rolename', 'roles', $user->roleid)."</td>";
    else echo "<td>".role_drop_down('roleid', $user->roleid)."</td>";
    echo "</tr>";
    ?>
    <tr><th>Real Name:</th><td><input maxlength="50" name="realname" size="30" type="text" value="<?php echo $user->realname; ?>" /></td></tr>
    <tr><th>Job Title:</th><td><input maxlength="50" name="jobtitle" size="30" type="text" value="<?php echo $user->title; ?>" /></td></tr>
    <tr><th>Qualifications:<br />
    Enter a comma seperated list of each of the professional qualifications you hold
    </th><td><textarea name="qualifications" rows="3" cols="40"><?php echo $user->qualifications; ?></textarea></td></tr>
    <tr><th>Email Signature:<br />
    Inserted automatically at the bottom of your outgoing emails.
    </th><td><textarea name="signature" rows="4" cols="40"><?php echo strip_tags($user->signature); ?></textarea></td></tr>
    <?php
    $entitlement=user_holiday_entitlement($userid);
    if ($edituserpermission && $userid!=$sit[2])
    {
        echo "<tr><th>Holiday Entitlement:</th><td>";
        echo "<input type='text' name='holiday_entitlement' value='$entitlement' size='2' /> days";
        echo "</td></tr>";
    }
    elseif ($entitlement > 0)
    {
        $holidaystaken=user_count_holidays($userid, 1);
        echo "<tr><th>Holiday Entitlement:</th><td>";
        echo "$entitlement days, ";
        echo "$holidaystaken taken, ";
        echo $entitlement-$holidaystaken." Remaining";
        echo "</td></tr>\n";
        echo "<tr><th>Other Leave:</th><td>";
        echo user_count_holidays($userid, 2)." days sick leave, ";
        echo user_count_holidays($userid, 3)." days working away, ";
        echo user_count_holidays($userid, 4)." days training";
        echo "<br />";
        echo user_count_holidays($userid, 5)." days other leave";
        echo "</td></tr>";
    }
    echo "<tr><th>Group Membership:</th><td valign='top'>";
    if ($user->groupid >= 1)
    {
        $sql="SELECT name FROM groups WHERE id='{$user->groupid}' ";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        $group = mysql_fetch_object($result);
        echo $group->name;
    }
    else
    {
        echo "None set";
    }
    ?>
    </td></tr>
    <tr><th colspan='2'>WORK STATUS</td></tr>
    <tr><th>Status:</th><td><?php userstatus_drop_down("status", $user->status); ?></td></tr>
    <tr><th>Accepting Incidents:</th><td><?php accepting_drop_down("accepting", $userid); ?></td></tr>
    <tr><th>Message:<br />
    e.g. &quot;In france until Tue 22nd&quot;<br />Displayed on the 'view users' page for the benefit of your colleagues.
    </th><td><textarea name="message" rows="4" cols="40"><?php echo strip_tags($user->message); ?></textarea></td></tr>

    <tr><th colspan='2'>CONTACT DETAILS</td></tr>
    <tr><th>Email:<sup class='red'>*</sup></th><td><input maxlength="50" name="email" size="30" type="text" value="<?php echo strip_tags($user->email); ?>" /></td></tr>
    <tr><th>Phone:</th><td><input maxlength="50" name="phone" size="30" type="text" value="<?php echo strip_tags($user->phone); ?>" /></td></tr>
    <tr><th>Fax:</th><td><input maxlength="50" name="fax" size="30" type="text" value="<?php echo strip_tags($user->fax); ?>" /></td></tr>
    <tr><th>Mobile:</th><td><input maxlength="50" name="mobile" size="30" type="text" value="<?php echo user_mobile($userid) ?>" /></td></tr>
    <tr><th>AIM: <img src="images/icons/kdeclassic/16x16/apps/ksmiletris.png" width="16" height="16" alt="AIM" /></th><td><input maxlength="50" name="aim" size="30" type="text" value="<?php echo strip_tags($user->aim); ?>" /></td></tr>
    <tr><th>ICQ: <img src="images/icons/kdeclassic/16x16/apps/licq.png" width="16" height="16" alt="ICQ" /></th><td><input maxlength="50" name="icq" size="30" type="text" value="<?php echo strip_tags($user->icq); ?>" /></td></tr>
    <tr><th>MSN: <img src="images/icons/kdeclassic/16x16/apps/personal.png" width="16" height="16" alt="MSN" /></th><td><input maxlength="50" name="msn" size="30" type="text" value="<?php echo strip_tags($user->msn); ?>" /></td></tr>

    <tr><th colspan='2'>DISPLAY PREFERENCES</td></tr>
    <tr><th>Interface Style (Theme):</th><td>
    <?php interfacestyle_drop_down('style', $user->var_style) ?>
    </td></tr>
    <tr><th>Incident Refresh:</th><td><input maxlength="10" name="incidentrefresh" size="3" type="text" value="<?php echo $user->var_incident_refresh; ?>" /> Seconds</td></tr>

    <tr><th>Incident Update Order:</th><td>
    <select name="updateorder">
    <option <?php if ($user->var_update_order == "desc") echo "selected='selected'" ?> value="desc">Most Recent At Top</option>
    <option <?php if ($user->var_update_order == "asc") echo "selected='selected'" ?> value="asc">Most Recent At Bottom</option>
    </select>
    </td></tr>
    <tr><th>Collapse Data:</th><td><?php html_checkbox('collapse', $user->var_collapse); ?></td></tr>

    <tr><th colspan='2'>NOTIFICATIONS</td></tr>
    <tr><th>Email notification on reassign</th><td><?php html_checkbox('emailonreassign', $user->var_notify_on_reassign); ?></td><tr>

    <?php
    plugin_do('edit_profile_form');

    if ($CONFIG['trusted_server']==FALSE AND $userid==$sit[2])
    {
        echo "<tr><th colspan='2'>CHANGE PASSWORD</td></tr>";
        echo "<tr><th>&nbsp;</th><td>To change your password - first enter your existing password and then type your new password twice to confirm it.</td></tr>";
        echo "<tr><th>Old Password:</th><td><input maxlength='50' name='password' size='30' type='password' /></td></tr>";
        echo "<tr><th>New Password:</th><td><input maxlength='50' name='newpassword1' size='30' type='password' /></td></tr>";
        echo "<tr><th>Confirm New Password:</th><td><input maxlength='50' name='newpassword2' size='30' type='password' /></td></tr>";
    }
    echo "</table>\n";
    echo "<input type='hidden' name='userid' value='{$userid}' />";
    ?>
    <p><input name="reset" type="reset" value="Reset" /> <input name="submit" type="submit" value="Save" /></p>
    </form>
    <?php
    include('htmlfooter.inc.php');
}
else
{
    // External variables
    $message = cleanvar($_POST['message']);
    $realname = cleanvar($_POST['realname']);
    $qualifications = cleanvar($qualifications);
    $userid = cleanvar($_POST['userid']);
    $email = cleanvar($_POST['email']);
    $jobtitle = cleanvar($_POST['jobtitle']);
    $qualifications = cleanvar($_POST['qualifications']);
    $phone = cleanvar($_POST['phone']);
    $mobile = cleanvar($_POST['mobile']);
    $aim = cleanvar($_POST['aim']);
    $icq = cleanvar($_POST['icq']);
    $msn = cleanvar($_POST['msn']);
    $fax = cleanvar($_POST['fax']);
    $incidentrefresh = cleanvar($_POST['incidentrefresh']);
    $updateorder = cleanvar($_POST['updateorder']);
    $signature = cleanvar($_POST['signature']);
    $message = cleanvar($_POST['message']);
    $status = cleanvar($_POST['status']);
    $collapse = cleanvar($_POST['collapse']);
    $emailonreassign = cleanvar($_POST['emailonreassign']);
    $style = cleanvar($_POST['style']);
    $accepting = cleanvar($_POST['accepting']);
    $roleid = cleanvar($_POST['roleid']);
    $holiday_entitlement = cleanvar($_POST['holiday_entitlement']);
    $password = cleanvar($_POST['password']);
    $newpassword1 = cleanvar($_POST['newpassword1']);
    $newpassword2 = cleanvar($_POST['newpassword2']);

    // TODO target v3.24 Add some extra checking here so that users can't edit other peoples profiles

    // Update user profile
    $errors = 0;

    // check for change of password
    if ($password != "" && $newpassword1 != "" && $newpassword2 != "")
    {
        // verify password fields
        if ($newpassword1 == $newpassword2 && strtoupper(md5($password)) == strtoupper(user_password($userid)))
        {
            $password=strtoupper(md5($password));
            $newpassword1=strtoupper(md5($newpassword1));
            $newpassword2=strtoupper(md5($newpassword2));
            $sql = "UPDATE users SET password='$newpassword1' WHERE id='$userid'";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

            if (mysql_affected_rows() < 1) { throw_error('!Error password change failed - new password', '$newpassword1'); }
            $confirm_message = "<h2>Password Changed</h2>\n<p align='center'>You should log out and log back in again</p>\n<p align='center'>Please wait while you are redirected.</p>";
        }
        else
        {
            // TODO produce a better message when password change fails
            throw_error("User Error changing password, please try again typing your passwords carefully. $password ($newpassword1 / $newpassword2)",'');
        }
    }
    // check for blank real name
    if ($realname == "")
    {
        $errors = 1;
        $error_string .= "<h5 class='error'>You must enter a real name</h5>\n";
    }
    // check for blank email address
    if ($email == "")
    {
        $errors = 1;
        $error_string .= "<h5 class='error'>You must enter an email address</h5>\n";
    }

    // update database if no errors
    if ($errors == 0)
    {
        if(!empty($collapse)) $collapse = 'true'; else $collapse = 'false';
        if(!empty($emailonreassign)) $emailonreassign = 'true'; else $emailonreassign = 'false';

        $sql  = "UPDATE users SET realname='$realname', title='$jobtitle', email='$email', qualifications='$qualifications', ";
        $sql .= "phone='$phone', mobile='$mobile', aim='$aim', icq='$icq', msn='$msn', fax='$fax', var_incident_refresh='$incidentrefresh', ";
        if ($userid != 1 AND !empty($_REQUEST['roleid']) AND $edituserpermission==TRUE) $sql .= "roleid='{$roleid}', ";
        if (!empty($holiday_entitlement) AND $edituserpermission==TRUE) $sql .= "holiday_entitlement='{$holiday_entitlement}', ";
        $sql .= "var_update_order='$updateorder', var_style='$style', signature='$signature', message='$message', status='$status', accepting='$accepting', ";
        $sql .= "var_collapse='$collapse', var_notify_on_reassign='$emailonreassign' WHERE id='$userid' LIMIT 1";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        // If this is the current user, update the profile in the users session
        if ($userid == $_SESSION['userid'])
        {
            $_SESSION['style'] = $style;
            $_SESSION['realname'] = $realname;
            $_SESSION['email'] = $email;
            $_SESSION['incident_refresh'] = $incidentrefresh;
            $_SESSION['update_order'] = $updateorder;
        }

        if (!$result)
        {
            include('htmlheader.inc.php');
            throw_error('!Error while updating users table', '');
            include('htmlfooter.inc.php');
        }
        else
        {
            if ($userid==$sit[2]) $redirecturl='index.php';
            else $redirecturl='manage_users.php';
            plugin_do('save_profile_form');

            // password was not changed
            if (!isset($confirm_message)) confirmation_page("2", $redirecturl, "<h2>Profile Modification Successful</h2><h5>Please wait while you are redirected...</h5>");
            // password was changed
            else
            {
                journal(CFG_LOGGING_NORMAL, 'User Profile Edited', "User Profile {$sit[2]} Edited", CFG_JOURNAL_USER, $sit[2]);
                confirmation_page("2", $redirecturl, $confirm_message);
            }
        }
    }
    else
    {
        // print error string
        include('htmlheader.inc.php');
        echo $error_string;
        include('htmlfooter.inc.php');
    }
}
?>