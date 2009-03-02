<?php
// edit_user_permissions.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

$permission = 9; // Edit User Permissions

require ('core.php');
require (APPLICATION_LIBPATH . 'functions.inc.php');
// This page requires authentication
require (APPLICATION_LIBPATH.'auth.inc.php');

$title = $strSetPermissions;

// Restrict resetting permissions in demo mode for all but the first user (usually admin)
if ($CONFIG['demo'] AND $_SESSION['userid']!=1)
{
    html_redirect("manage_users.php", FALSE, $strCannotPerformOperationInDemo);
}


include (APPLICATION_INCPATH . 'htmlheader.inc.php');


// External variables
$user = cleanvar($_REQUEST['user']);
$role = cleanvar($_REQUEST['role']);
$action = $_REQUEST['action'];
$permselection = $_REQUEST['perm'];
$permid = $_REQUEST['permid'];

if (empty($action) OR $action == "showform")
{
    $sql = "SELECT * FROM `{$dbRoles}` ORDER BY id ASC";
    $result= mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

    if (mysql_num_rows($result) >= 1)
    {
        echo "<h2>{$strRolePermissions}</h2>";
        echo "<form action='{$_SERVER['PHP_SELF']}' method='post' onsubmit=\"return confirm_action('{$strAreYouSureMakeTheseChanges}')\">";
        echo "<table align='center'>";
        echo "<tr>";
        echo "<th>{$strPermission}</th>";
        while ($rolerow = mysql_fetch_object($result))
        {
            echo "<th>{$rolerow->rolename}</th>";
        }
        echo "</tr>\n";
        $psql = "SELECT * FROM `{$dbPermissions}`";
        $presult = mysql_query($psql);
        $class='shade1';
        while ($perm = mysql_fetch_object($presult))
        {
            echo "<tr class='$class'>";
            echo "<td><a href='{$PHP_SELF}?action=check&amp;permid={$perm->id}' title='{$strCheckWhoHasThisPermission}'>{$perm->id}</a> {$perm->name}</td>";
            mysql_data_seek($result, 0);
            while ($rolerow = mysql_fetch_object($result))
            {
                $rpsql = "SELECT * FROM `{$dbRolePermissions}` WHERE roleid='{$rolerow->id}' AND permissionid='{$perm->id}'";
                $rpresult = mysql_query($rpsql);
                $rp = mysql_fetch_object($rpresult);
                echo "<td><input name='{$rolerow->id}perm[]' type='checkbox' value='{$perm->id}' ";
                if ($rp->granted=='true') echo " checked='checked'";
                echo " /></td>";
            }
            echo "</tr>\n";
            if ($class=='shade2') $class = "shade1";
            else $class = "shade2";
        }
        echo "</table>";
        echo "<p><input name='reset' type='reset' value='{$strReset}' />";
        echo "<input type='hidden' name='action' value='update' />";
        echo "<input name='submit' type='submit' value='{$strSave}' /></p>";
        echo "</form>";
    }
}
elseif ($action == "edit" && (!empty($user) OR !empty($role)))
{
    // Show form
    if (!empty($role) AND !empty($user))
    {
        trigger_error("Can't edit users and roles at the same time", E_USER_ERROR);
    }
    if (!empty($user))
    {
        $object = "user: ".user_realname($user);
    }
    else
    {
        $object = "role: ".db_read_column('rolename', $dbRoles, $role);
    }
    echo "<h2>Set Permissions for {$object}</h2>";
    if (!empty($user)) echo "<p align='center'>Permissions that are inherited from the users role can not be changed.</p>";

    // Next lookup the permissions
    $sql = "SELECT * FROM `{$dbUsers}` AS u, `{$dbRolePermissions}` AS rp WHERE u.roleid = rp.roleid AND u.id = '$user' AND granted='true'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    $userrolepermission=array();
    if (mysql_num_rows($result) >= 1)
    {
        while ($roleperm = mysql_fetch_object($result))
        {
           $userrolepermission[]=$roleperm->permissionid;
        }
    }
    echo "<form action='{$_SERVER['PHP_SELF']}?action=update' method='post' onsubmit=\"return confirm_action('{$strAreYouSureMakeTheseChanges}')\">";
    echo "<table align='center'>
    <tr>
    <th>{$strID}</th>
    <th>{$strPermission}</th>
    </tr>\n";
    if (empty($role) AND !empty($user))
    {
        $sql = "SELECT id, name, up.granted AS granted FROM `{$dbPermissions}` AS p, `{$dbUserPermissions}` AS up ";
        $sql.= "WHERE p.id = up.permissionid ";
        $sql.= "AND up.userid='$user' ";
    }
    else
    {
        $sql = "SELECT id, name, rp.granted AS granted FROM `{$dbPermissions}` AS p, `{$dbRolePermissions}` AS rp ";
        $sql.= "WHERE p.id = rp.permissionid ";
        $sql.= "AND rp.roleid='$role' ";
    }
    $permission_result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    while ($row = mysql_fetch_array($permission_result))
    {
        $permission_array[$row['id']] = $row;
    }

    $sql = "SELECT * FROM `{$dbPermissions}`";
    $result= mysql_query($sql);
    $class='shade1';

    while ($permissions = mysql_fetch_array($result))
    {
        if (!in_array($permissions['id'],$userrolepermission))
        {
            echo "<tr class='$class'><td align='right'><a href='{$_SERVER['PHP_SELF']}?action=check&amp;permid={$permissions['id']}'  title='Check who has this permission'>".$permissions['id']."</a></td>";
            echo "<td>";
            echo "<input name=\"perm[]\" type=\"checkbox\" value=\"".$permissions['id']."\"";


            if ($permission_array[$permissions['id']]['granted'] == 'true') echo " checked='checked'";
            echo " /> ".$permissions['name'];

            echo "</td></tr>\n";
        }
        else
        {
            echo "<tr class='$class'><td align='right'><a href='{$_SERVER['PHP_SELF']}?action=check&amp;permid={$permissions['id']}' title='Check who has this permission'>{$permissions['id']}</a></td>";
            echo "<td><input name='dummy[]' type='checkbox' checked='checked' disabled='disabled' /> {$permissions['name']}";
            echo "<input type='hidden' name='perm[]' value='{$permissions['id']}' /></td></tr>\n";
        }
        if ($class=='shade2') $class = "shade1";
        else $class = "shade2";
    }
    echo "</table>";
    echo "<p><input name='user' type='hidden' value='{$user}' />";
    echo "<input name='role' type='hidden' value='{$role}' />";
    echo "<input name='submit' type='submit' value='{$strSave}' /></p>";
    echo "</form>";
}
elseif ($action == "update")
{
    $errors = 0;
    // If no role or user is specified we're setting all role permissions
    if (empty($role) AND empty($user))
    {
        $sql = "SELECT * FROM `{$dbRoles}` ORDER BY id ASC";
        $result= mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        while ($rolerow = mysql_fetch_object($result))
        {
            // First pass, set all access to false
            $sql = "UPDATE `{$dbRolePermissions}` SET granted='false' WHERE roleid='{$rolerow->id}'";
            $aresult = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

            if (!$aresult) echo user_alert("Update of role permissions failed on pass 1", E_USER_WARNING);

            // Second pass, loop through checkbox array setting access to true where boxes are checked
            if (is_array($_POST["{$rolerow->id}perm"]))
            {
                reset ($_POST["{$rolerow->id}perm"]);
                while ($x = each($_POST["{$rolerow->id}perm"]))
                {
                    $sql = "UPDATE `{$dbRolePermissions}` SET granted='true' WHERE roleid='{$rolerow->id}' AND permissionid='".$x[1]."' ";
                    // echo "Updating permission ".$x[1]."<br />";
                    // flush();
                    $uresult = mysql_query($sql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                    if (mysql_affected_rows() < 1 || $uresult == FALSE)
                    {
                        // Update failed, this could be because of a missing userpemissions record so try and create one
                        // echo "Update of permission ".$x[1]."failed, no problem, will try insert instead.<br />";
                        $isql = "INSERT INTO `{$dbRolePermissions}` (roleid, permissionid, granted) ";
                        $isql .= "VALUES ('{$rolerow->id}', '".$x[1]."', 'true')";
                        $iresult = mysql_query($isql);
                        if (mysql_error()) trigger_error(mysql_error(), E_USER_ERROR);
                        if (mysql_affected_rows() < 1) echo user_alert("Update of user permission ".$x[1]." failed on pass 2", E_USER_WARNING);
                    }
                }
            }

        }
        html_redirect("manage_users.php");
        exit;
    }

    journal(CFG_LOGGING_NORMAL, 'User Permissions Edited', "User $user permissions edited", CFG_JOURNAL_USERS, $user);

    // Edit the users permissions
    if (empty($role) AND !empty($user))
    {
        // First pass, set all access to false
        $sql = "UPDATE `{$dbUserPermissions}` SET granted='false' WHERE userid='$user'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        // Second pass, loop through checkbox array setting access to true where boxes are checked
        if (is_array($permselection))
        {
            //reset ($permselection);
            while ($x = each($permselection))
            {
                $sql = "UPDATE `{$dbUserPermissions}` SET granted='true' WHERE userid='$user' AND permissionid='".$x[1]."' ";
                # echo "Updating permission ".$x[1]."<br />";
                # flush();
                $result = mysql_query($sql);
                if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                if (mysql_affected_rows() < 1 || $result == FALSE)
                {
                    // Update failed, this could be because of a missing userpemissions record so try and create one
                    // echo "Update of permission ".$x[1]."failed, no problem, will try insert instead.<br />";
                    $isql = "INSERT INTO `{$dbUserPermissions}` (userid, permissionid, granted) ";
                    $isql .= "VALUES ('$user', '".$x[1]."', 'true')";
                    $iresult = mysql_query($isql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                    if (mysql_affected_rows() < 1)
                    {
                        echo user_alert("Update of user permission ".$x[1]." failed on pass 2", E_USER_WARNING);
                    }
                }
            }
        }
        html_redirect("manage_users.php");
        exit;
    }
    else
    {
        // Edit the role permissions
        // First pass, set all access to false
        $sql = "UPDATE `{$dbRolePermissions}` SET granted='false' WHERE roleid='$role'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        if (!$result) echo user_alert("Update of role permissions failed on pass 1", E_USER_WARNING);
        else
        {
            html_redirect("manage_users.php");
            exit;
        }

        // Second pass, loop through checkbox array setting access to true where boxes are checked
        if (is_array($permselection))
        {
            reset ($permselection);
            while ($x = each($permselection))
            {
                $sql = "UPDATE {$dbRolePermissions}` SET granted='true' WHERE roleid='$role' AND permissionid='".$x[1]."' ";
                # echo "Updating permission ".$x[1]."<br />";
                #flush();
                $result = mysql_query($sql);
                if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                if (mysql_affected_rows() < 1 || $result == FALSE)
                {
                    // Update failed, this could be because of a missing userpemissions record so try and create one
                    // echo "Update of permission ".$x[1]."failed, no problem, will try insert instead.<br />";
                    $isql = "INSERT INTO `{$dbRolePermissions}` (roleid, permissionid, granted) ";
                    $isql .= "VALUES ('$role', '".$x[1]."', 'true')";
                    $iresult = mysql_query($isql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                    if (mysql_affected_rows() < 1) echo user_alert("Update of user permission ".$x[1]." failed on pass 2", E_USER_WARNING);
                }
            }
        }
    }
}
elseif ($action == "check")
{
    echo "<h2>{$strCheckUserAndRolePermissions}</h2>";
    if (!empty($permid))
    {
        echo "<h3>Role Permission: {$permid} - ".permission_name($permid)."</h3>";
        $sql = "SELECT rp.roleid AS roleid, username, u.id AS userid, realname, rolename ";
        $sql .= "FROM `{$dbRolePermissions}` AS rp, `{$dbRoles}` AS r, `{$dbUsers}` AS u ";
        $sql .= "WHERE rp.roleid = r.id ";
        $sql .= "AND r.id = u.roleid ";
        $sql .= "AND permissionid = '$permid' AND granted='true' ";
        $sql .= "AND u.status > 0";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        if (mysql_num_rows($result) >= 1)
        {
            echo "<table align='center'>";
            echo "<tr><th>{$strUser}</th><th>{$strRole}</th></tr>";
            $shade = 'shade1';
            while ($user = mysql_fetch_object($result))
            {
                echo "<tr class='$shade'><td>&#10004; ";
                echo "<a href='user_profile_edit.php?userid={$user->userid}'>";
                echo "{$user->realname}";
                echo "</a>";
                echo " ({$user->username})</td><td>{$user->rolename}</td></tr>\n";
                if ($shade=='shade1') $shade='shade2';
                else $shade='shade1';
            }
            echo "</table>";
        } else echo "<p align='center'>{$strNone}</p>";

        echo "<p align='center'><a href='edit_user_permissions.php'>Set role permissions</a></p>";

        echo "<h3>User Permission: $permid - ".permission_name($permid)."</h3>";
        $sql = "SELECT up.userid AS userid, username, realname ";
        $sql .= "FROM `{$dbUserPermissions}` AS up, `{$dbUsers}` AS u ";
        $sql .= "WHERE up.userid = u.id ";
        $sql .= "AND permissionid = '$permid' AND granted = 'true' AND u.status > 0";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        if (mysql_num_rows($result) >= 1)
        {
            echo "<table align='center'>";
            echo "<tr><th>{$strUser}</th></tr>";
            $shade='shade1';
            while ($user = mysql_fetch_object($result))
            {
                echo "<tr class='$shade'><td>&#10004; <a href='{$_SERVER['PHP_SELF']}?action=edit&amp;userid={$user->userid}#perm{$perm}'>{$user->realname}</a> ({$user->username})</td></tr>\n";
                if ($shade=='shade1') $shade='shade2';
                else $shade='shade1';
            }
            echo "</table>";
        } else echo "<p align='center'>{$strNone}</p>";
    }
    else
    {
        echo user_alert(sprintf($strFieldMustNotBeBlank, "'{$strPermission}'"), E_USER_ERROR);
    }
}
else
{
    echo user_alert("No changes to make", E_USER_WARNING);
}
include (APPLICATION_INCPATH . 'htmlfooter.inc.php');
?>
