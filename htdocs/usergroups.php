<?php
// usergroups.php - Manage user group membership
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

$permission=23; // Edit user
$title = 'User Groups';

require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

$action = cleanvar($_REQUEST['action']);

switch ($action)
{
    case 'savemembers':
        $sql = "SELECT * FROM users ORDER BY realname";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        while ($user = mysql_fetch_object($result))
        {
            $usql = "UPDATE users SET groupid = '".cleanvar($_POST["group{$user->id}"])."' WHERE id='{$user->id}'";
            mysql_query($usql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        }
        confirmation_page("2", "usergroups.php", "<h2>Group Membership Saved</h2><p align='center'>Please wait while you are redirected...</p>");
    break;

    case 'addgroup':
        $group = cleanvar($_REQUEST['group']);
        $sql = "INSERT INTO groups (name) VALUES ('{$group}')";
        mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        confirmation_page("2", "usergroups.php", "<h2>User Group Added</h2><p align='center'>Please wait while you are redirected...</p>");
    break;

    case 'deletegroup':
        $groupid = cleanvar($_REQUEST['groupid']);
        // Remove group membership for all users currently assigned to this group
        $sql = "UPDATE users SET groupid = '' WHERE groupid = '{$groupid}'";
        mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        // Remove the group
        $sql = "DELETE FROM groups WHERE id='{$groupid}' LIMIT 1";
        mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        confirmation_page("2", "usergroups.php", "<h2>User Group Deleted</h2><p align='center'>Please wait while you are redirected...</p>");
    break;

    default:
        include('htmlheader.inc.php');

        echo "<h2>$title</h2>";

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
            $html .= "<option value='0'>None</option>\n";
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

        echo "<form action='{$_SERVER['PHP_SELF']}' method='post'>";
        echo "<table summary='User Groups' align='center'>";
        echo "<tr><th>Group</th><th>Action</th></tr>\n";
        if ($numgroups >= 1)
        {
            foreach($grouparr AS $groupid => $groupname)
            {
                echo "<tr><td>$groupname</td><td><a href='usergroups.php?groupid={$groupid}&amp;action=deletegroup'>Delete</a></td></tr>\n";
            }
        }
        echo "<tr><td><input type='text' name='group' value='' size='10' maxlength='255' />";
        echo "<input type='hidden' name='action' value='addgroup' />";
        echo "</td><td><input type='submit' name='add' value='Add' /></td></tr>\n";
        echo "</table>";
        echo "</form>";

        echo "<h3>Membership</h3>";

        $sql = "SELECT * FROM users WHERE status !=0 ORDER BY realname";  // status=0 means left company
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        echo "<form action='{$_SERVER['PHP_SELF']}' method='post'>";
        echo "<table summary='User Group Membership' align='center'>";
        echo "<tr><th>User</th><th>Group</th>";
        while ($user = mysql_fetch_object($result))
        {
            echo "<tr><td>{$user->realname} ({$user->username})</td>";
            echo "<td>".group_drop_down("group{$user->id}",$user->groupid)."</td></tr>\n";
        }
        echo "</table>\n";

        echo "<p><input type='hidden' name='action' value='savemembers' /><input type='submit' value='Save' /></p>";
        echo "</form>";

        include('htmlfooter.inc.php');
}
?>