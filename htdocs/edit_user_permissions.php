<?php
// edit_user_permissions.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

$permission=9; // Edit User Permissions

require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

$title = $strSetPermissions;

// Restrict resetting passwords in demo mode for all but the first user (usually admin)
if ($CONFIG['demo'] AND $_SESSION['userid']!=1)
{
    html_redirect("manage_users.php", FALSE, "You cannot reset passwords while in DEMO MODE"); // FIXME i18n demo mode
}


include('htmlheader.inc.php');
?>
<script type="text/javascript">
function confirm_submit()
{
    return window.confirm('Are you sure you want to make these changes?');
}
</script>
<?php
// External variables
$user = cleanvar($_REQUEST['user']);
$role = cleanvar($_REQUEST['role']);
$action = $_REQUEST['action'];
$perm = $_REQUEST['perm'];

if (empty($action) OR $action == "showform")
{
    $sql = "SELECT * FROM roles ORDER BY id ASC";
    $result= mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

    if(mysql_num_rows($result) >= 1)
    {
        echo "<h2>{$strRolePermissions}</h2>";
        echo "<form action='{$_SERVER['PHP_SELF']}' method='post' onsubmit='return confirm_submit()'>";
        echo "<table align='center'>";
        echo "<tr>";
        echo "<th>{$strPermission}</th>";
        while ($rolerow = mysql_fetch_object($result))
        {
            echo "<th>{$rolerow->rolename}</th>";
        }
        echo "</tr>\n";
        $psql = "SELECT * FROM permissions";
        $presult = mysql_query($psql);
        $class='shade1';
        while ($perm = mysql_fetch_object($presult))
        {
            echo "<tr class='$class'>";
            // FIXME i18n tooltip 'check who has permissions'
            echo "<td><a href='{$PHP_SELF}?action=check&amp;perm={$perm->id}' title='Check who has this permission'>{$perm->id}</a> {$perm->name}</td>";
            mysql_data_seek($result, 0);
            while ($rolerow = mysql_fetch_object($result))
            {
                $rpsql = "SELECT * FROM rolepermissions WHERE roleid='{$rolerow->id}' AND permissionid='{$perm->id}'";
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
        echo "<p><input name='reset' type='reset' value='$strReset' />";
        echo "<input type='hidden' name='action' value='update' />";
        echo "<input name='submit' type='submit' value='{$strSave}' /></p>";
        echo "</form>";
    }
}
elseif ($action == "edit" && (!empty($user) OR !empty($role)))
{
    // Show form
    if (!empty($role) AND !empty($user)) trigger_error("Can't edit users and roles at the same time", E_USER_ERROR);
    if (!empty($user)) $object = "user: ".user_realname($user);
    else $object = "role: ".db_read_column('rolename', 'roles', $role);
    echo "<h2>Set Permissions for {$object}</h2>";
    if (!empty($user)) echo "<p align='center'>Permissions that are inherited from the users role can not be changed.</p>";

    // Next lookup the permissions
    $sql = "SELECT * FROM users, rolepermissions WHERE users.roleid=rolepermissions.roleid AND users.id = '$user' AND granted='true'";
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
    echo "<form action='{$_SERVER['PHP_SELF']}?action=update' method='post' onsubmit='return confirm_submit()'>";
    echo "<table align='center'>
    <tr>
    <th>ID</th>
    <th>{$strPermission}</th>
    </tr>\n";
    if (empty($role) AND !empty($user))
    {
        $sql = "SELECT id, name, userpermissions.granted AS granted FROM permissions, userpermissions ";
        $sql.= "WHERE permissions.id=userpermissions.permissionid ";
        $sql.= "AND userpermissions.userid='$user' ";
    }
    else
    {
        $sql = "SELECT id, name, rolepermissions.granted AS granted FROM permissions, rolepermissions ";
        $sql.= "WHERE permissions.id=rolepermissions.permissionid ";
        $sql.= "AND rolepermissions.roleid='$role' ";
    }
    $permission_result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

    $sql = "SELECT * FROM permissions";
    $result= mysql_query($sql);
    $class='shade1';
    while ($permissions = mysql_fetch_array($result))
    {
        $permission_array=mysql_fetch_array($permission_result);
        if (!in_array($permissions['id'],$userrolepermission))
        {
            echo "<tr class='$class'><td align='right'><a href='{$_SERVER['PHP_SELF']}?action=check&amp;perm={$permissions['id']}'  title='Check who has this permission'>".$permissions['id']."</a></td>";
            echo "<td>";
            echo "<input name=\"perm[]\" type=\"checkbox\" value=\"".$permissions['id']."\"";


            if ($permission_array['granted']=='true') echo " checked='checked'";
            echo " /> ".$permissions['name'];

            echo "</td></tr>\n";
        }
        else
        {
            echo "<tr class='$class'><td align='right'><a href='{$_SERVER['PHP_SELF']}?action=check&amp;perm={$permissions['id']}' title='Check who has this permission'>{$permissions['id']}</a></td>";
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
    // check for blank name
    if (empty($role) AND empty($user))
    {
        $sql = "SELECT * FROM roles ORDER BY id ASC";
        $result= mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        while ($rolerow = mysql_fetch_object($result))
        {
            // First pass, set all access to false
            $sql = "UPDATE rolepermissions SET granted='false' WHERE roleid='{$rolerow->id}'";
            $aresult = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

            if (!$aresult) echo "<p class='error'>Update of role permissions failed on pass 1\n";

            // Second pass, loop through checkbox array setting access to true where boxes are checked
            if (is_array($_POST["{$rolerow->id}perm"]))
            {
                reset ($_POST["{$rolerow->id}perm"]);
                while($x = each($_POST["{$rolerow->id}perm"]))
                {
                    $sql = "UPDATE rolepermissions SET granted='true' WHERE roleid='{$rolerow->id}' AND permissionid='".$x[1]."' ";
                    // echo "Updating permission ".$x[1]."<br />";
                    // flush();
                    $uresult = mysql_query($sql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                    if (mysql_affected_rows()<1 || $uresult==FALSE)
                    {
                        // Update failed, this could be because of a missing userpemissions record so try and create one
                        // echo "Update of permission ".$x[1]."failed, no problem, will try insert instead.<br />";
                        $isql = "INSERT INTO rolepermissions (roleid, permissionid, granted) ";
                        $isql .= "VALUES ('{$rolerow->id}', '".$x[1]."', 'true')";
                        $iresult = mysql_query($isql);
                        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                        if (mysql_affected_rows()<1) echo "<p class='error'>Update of user permission ".$x[1]." failed on pass 2\n";
                    }
                }
            }

        }
        html_redirect("manage_users.php");
        exit;
    }

    // edit contact if no errors
    if ($errors == 0)
    {
        // update contact
        $now = time();

        journal(CFG_LOGGING_NORMAL, 'User Permissions Edited', "User $user permissions edited", CFG_JOURNAL_USERS, $user);

        if (empty($role) AND !empty($user))
        {
            // First pass, set all access to false
            $sql = "UPDATE userpermissions SET granted='false' WHERE userid='$user'";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

            if (!$result) echo "<p class='error'>Update of user permissions failed on pass 1\n";
            else html_redirect("manage_users.php");

            // Second pass, loop through checkbox array setting access to true where boxes are checked
            if (is_array($perm))
            {
                reset ($perm);
                while($x = each($perm))
                {
                    $sql = "UPDATE userpermissions SET granted='true' WHERE userid='$user' AND permissionid='".$x[1]."' ";
                    # echo "Updating permission ".$x[1]."<br />";
                    # flush();
                    $result = mysql_query($sql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                    if (mysql_affected_rows()<1 || $result==FALSE)
                    {
                        // Update failed, this could be because of a missing userpemissions record so try and create one
                        // echo "Update of permission ".$x[1]."failed, no problem, will try insert instead.<br />";
                        $isql = "INSERT INTO userpermissions (userid, permissionid, granted) ";
                        $isql .= "VALUES ('$user', '".$x[1]."', 'true')";
                        $iresult = mysql_query($isql);
                        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                        if (mysql_affected_rows()<1) echo "<p class='error'>Update of user permission ".$x[1]." failed on pass 2\n";
                    }
                }
            }
        }
        else
        {
            // First pass, set all access to false
            $sql = "UPDATE rolepermissions SET granted='false' WHERE roleid='$role'";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

            if (!$result) echo "<p class='error'>Update of role permissions failed on pass 1\n";
            else html_redirect("manage_users.php");

            // Second pass, loop through checkbox array setting access to true where boxes are checked
            if (is_array($perm))
            {
                reset ($perm);
                while($x = each($perm))
                {
                    $sql = "UPDATE rolepermissions SET granted='true' WHERE roleid='$role' AND permissionid='".$x[1]."' ";
                    # echo "Updating permission ".$x[1]."<br />";
                    #flush();
                    $result = mysql_query($sql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                    if (mysql_affected_rows()<1 || $result==FALSE)
                    {
                        // Update failed, this could be because of a missing userpemissions record so try and create one
                        // echo "Update of permission ".$x[1]."failed, no problem, will try insert instead.<br />";
                        $isql = "INSERT INTO rolepermissions (roleid, permissionid, granted) ";
                        $isql .= "VALUES ('$role', '".$x[1]."', 'true')";
                        $iresult = mysql_query($isql);
                        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                        if (mysql_affected_rows()<1) echo "<p class='error'>Update of user permission ".$x[1]." failed on pass 2\n";
                    }
                }
            }
        }
    }
}
elseif ($action == "check")
{
    echo "<h2>Check User &amp; Role Permissions</h2>"; // FIXME i18n check user and role permissions
    if (!empty($perm))
    {
        echo "<h3>Role Permission: $perm - ".permission_name($perm)."</h3>";
        $sql = "SELECT rolepermissions.roleid AS roleid, username, users.id AS userid, realname, rolename ";
        $sql .= "FROM rolepermissions, roles, users ";
        $sql .= "WHERE rolepermissions.roleid=roles.id ";
        $sql .= "AND roles.id=users.roleid ";
        $sql .= "AND permissionid='$perm' AND granted='true' ";
        $sql .= "AND users.status > 0";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        if (mysql_num_rows($result) >= 1)
        {
            echo "<table align='center'>";
            echo "<tr><th>{$strUser}</th><th>{$strRole}</th></tr>";
            $shade='shade1';
            while($user = mysql_fetch_object($result))
            {
                echo "<tr class='$shade'><td>&#10004; ";
                echo "<a href='edit_profile.php?userid={$user->userid}'>";
                echo "{$user->realname}";
                echo "</a>";
                echo " ({$user->username})</td><td>{$user->rolename}</td></tr>\n";
                if ($shade=='shade1') $shade='shade2';
                else $shade='shade1';
            }
            echo "</table>";
        } else echo "<p align='center'>{$strNone}</p>";

        echo "<p align='center'><a href='edit_user_permissions.php'>Set role permissions</a></p>";

        echo "<h3>User Permission: $perm - ".permission_name($perm)."</h3>";
        $sql = "SELECT userpermissions.userid AS userid, username, realname FROM userpermissions, users ";
        $sql .= "WHERE userpermissions.userid=users.id ";
        $sql .= "AND permissionid='$perm' AND granted='true' AND users.status > 0";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        if (mysql_num_rows($result) >= 1)
        {
            echo "<table align='center'>";
            echo "<tr><th>{$strUser}</th></tr>";
            $shade='shade1';
            while($user = mysql_fetch_object($result))
            {
                echo "<tr class='$shade'><td>&#10004; <a href='{$_SERVER['PHP_SELF']}?action=edit&amp;userid={$user->userid}#perm{$perm}'>{$user->realname}</a> ({$user->username})</td></tr>\n";
                if ($shade=='shade1') $shade='shade2';
                else $shade='shade1';
            }
            echo "</table>";
        } else echo "<p align='center'>{$strNone}</p>";
    }
    else echo "<p class='error'>No permission specified</p>";
}
else
{
    echo "<p class='error'>No changes to make</p>";
}
include('htmlfooter.inc.php');
?>