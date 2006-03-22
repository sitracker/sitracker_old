<?php
// edit_user_permissions.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

$permission=9;
$title='Edit Permissions';

require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

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
$perm = $_POST['perm'];

if (empty($action) OR $action == "showform")
{
    $sql = "SELECT * FROM roles ORDER BY id ASC";
    $result= mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

    if(mysql_num_rows($result) >= 1)
    {
        echo "<h2>Set Role Permissions</h2>";
        echo "<form action='{$_SERVER['PHP_SELF']}' method='post' onsubmit='return confirm_submit()'>";
        echo "<table align='center'>";
        echo "<tr>";
        echo "<th>Permission</th>";
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
            echo "<td>{$perm->id} {$perm->name}</td>";
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
        echo "<p><input name='reset' type='reset' value='Reset' />";
        echo "<input type='hidden' name='action' value='update' />";
        echo "<input name='submit' type='submit' value='Save' /></p>";
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
    ?>
    <table align='center'>
    <tr>
    <th>ID</th>
    <th>Permission</th>
    </tr>
    <?php
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
            echo "<tr class='$class'><td align='right'>".$permissions['id']."</td>";
            echo "<td>";
            echo "<input name=\"perm[]\" type=\"checkbox\" value=\"".$permissions['id']."\"";


            if ($permission_array['granted']=='true') echo " checked='checked'";
            echo " /> ".$permissions['name'];

            echo "</td></tr>\n";
        }
        else
        {
            echo "<tr class='$class'><td align='right'>{$permissions['id']}</td>";
            echo "<td><input name='dummy[]' type='checkbox' checked='checked' disabled='disabled' /> {$permissions['name']}";
            echo "<input type='hidden' name='perm[]' value='{$permissions['id']}' /></td></tr>\n";
        }
        if ($class=='shade2') $class = "shade1";
        else $class = "shade2";
    }
    echo "</table>";
    echo "<p><input name='user' type='hidden' value='{$user}' />";
    echo "<input name='role' type='hidden' value='{$role}' />";
    echo "<input name='submit' type='submit' value='Make Changes' /></p>";
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
        confirmation_page("2", "manage_users.php", "<h2>Role Permissions Successfully Set</h2><p align='center'>Please wait while you are redirected...</p>");
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
            else confirmation_page("2", "manage_users.php", "<h2>Permissions Successfully Set</h2><p align='center'>Please wait while you are redirected...</p>");

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
            else confirmation_page("2", "manage_users.php", "<h2>Permissions Successfully Set</h2><p align='center'>Please wait while you are redirected...</p>");

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
else
{
    echo "<p class='error'>No changes to make</p>";
}
include('htmlfooter.inc.php');
?>
