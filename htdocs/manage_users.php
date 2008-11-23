<?php
// manage_users.php - Overview of users, with links to managing them
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// This Page Is Valid XHTML 1.0 Transitional! 16Nov05

@include ('set_include_path.inc.php');
$permission = 22; // Administrate
require ('db_connect.inc.php');
require ('functions.inc.php');
// This page requires authentication
require ('auth.inc.php');

include ('htmlheader.inc.php');

$sql  = "SELECT *,u.id AS userid FROM `{$dbUsers}` AS u, `{$dbRoles}` AS r ";
$sql .= "WHERE u.roleid = r.id ";

// sort users by realname by default
if (!isset($sort) || $sort == "realname") $sql .= " ORDER BY IF(status> 0,1,0) DESC, realname ASC";
else if ($sort == "username") $sql .= " ORDER BY IF(status> 0,1,0) DESC, username ASC";

else if ($sort == "role") $sql .= " ORDER BY roleid ASC";
// sort incidents by job title
else if ($sort == "jobtitle") $sql .= " ORDER BY title ASC";

// sort incidents by email
else if ($sort == "email") $sql .= " ORDER BY email ASC";

// sort incidents by phone
else if ($sort == "phone") $sql .= " ORDER BY phone ASC";

// sort incidents by fax
else if ($sort == "fax") $sql .= " ORDER BY fax ASC";

// sort incidents by status
else if ($sort == "status")  $sql .= " ORDER BY status ASC";

// sort incidents by accepting calls
else if ($sort == "accepting") $sql .= " ORDER BY accepting ASC";

$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

echo "<h2>".icon('user', 32)." {$strManageUsers}</h2>";
echo "<p class='contextmenu' align='center'>";
echo "<a href='add_user.php?action=showform'>{$strAddUser}</a> | ";
echo "<a href='edit_user_permissions.php'>{$strRolePermissions}</a>";
echo "</p>";
echo "<table align='center'>";
echo "<tr>";
echo "<th><a href='{$_SERVER['PHP_SELF']}?sort=realname'>{$strName}</a> ";
echo "(<a href='{$_SERVER['PHP_SELF']}?sort=username'>{$strUsername}</a>)</th>";
echo "<th><a href='{$_SERVER['PHP_SELF']}?sort=role'>{$strRole}</a></th>";
echo "<th>{$strStatus}</th>";
echo "<th>{$strOperation}</th>";
echo "<th><a href='{$_SERVER['PHP_SELF']}?sort=email'>{$strEmail}</a></th>";
echo "<th><a href='{$_SERVER['PHP_SELF']}?sort=phone'>{$strTelephone}</a></th>";
echo "<th><a href='{$_SERVER['PHP_SELF']}?sort=mobile'>{$strMobile}</a></th>";
echo "<th><a href='{$_SERVER['PHP_SELF']}?sort=fax'>{$strFax}</a></th>";
echo "<th><a href='{$_SERVER['PHP_SELF']}?sort=status'>{$strStatus}</a></th>";
echo "<th><a href='{$_SERVER['PHP_SELF']}?sort=accepting'>{$strAccepting}</a></th>";
echo "</tr>\n";

// show results
$class = 'shade1';
while ($users = mysql_fetch_array($result))
{
    // define class for table row shading
    if ($users['status'] == 0) $class = 'expired';
    // print HTML
    echo "<tr class='{$class}'>\n";
    echo "<td>{$users['realname']}";
    echo " (";
    if ($users['userid'] == 1) echo "<strong>";
    echo "{$users['username']}";
    if ($users['userid'] == 1) echo "</strong>";
    echo ")</td>";

    echo "<td>{$users['rolename']}</td>";
    echo "<td>";
    if (user_permission($sit[2],57))
    {
        if ($users['status'] > 0) echo "{$strEnabled}"; 
        else echo "{$strDisabled}";
    }
    else echo "-";

    echo "</td>";

    echo "<td>";
    echo "<a href='edit_profile.php?userid={$users['userid']}'>{$strEdit}</a>";
    if ($users['status'] > 0)
    {
        echo " | ";
        if ($users['userid'] > 1)
        {
            echo "<a href='forgotpwd.php?action=sendpwd&amp;userid={$users['userid']}'>{$strResetPassword}</a> | ";
        }
        echo "<a href='edit_user_skills.php?user={$users['userid']}'>{$strSkills}</a>";
        echo " | <a href='edit_backup_users.php?user={$users['userid']}'>{$strSubstitutes}</a>";
        if ($users['userid'] > 1)
        {
            echo " | <a href='edit_user_permissions.php?action=edit&amp;user={$users['userid']}'>{$strPermissions}</a>";
        }
    }
    echo "</td>";

    echo "<td>";
    echo $users["email"];

    echo "</td><td>";
    if ($users["phone"] == '') echo $strNone;
    else echo $users["phone"];
    echo "</td><td>";
    
    if ($users["mobile"] == '') echo $strNone;
    else echo $users["mobile"];
    
    echo "</td><td>";
    
    if ($users["fax"] == '') echo $strNone;
    else echo $users["fax"];
    
    echo "</td><td>";
    echo userstatus_name($users["status"]);
    echo "</td><td>";
    
    if ($users["accepting"] == 'Yes') echo $strYes;
    else echo "<span class='error'>{$strNo}</span>";
    
    echo "</td></tr>";
    // invert shade
    if ($class == 'shade2') $class = "shade1";
    else $class = "shade2";

}
echo "</table>\n";

// free result and disconnect
mysql_free_result($result);

include ('htmlfooter.inc.php');
?>
