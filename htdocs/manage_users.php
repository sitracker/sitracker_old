<?php
// manage_users.php - Overview of users, with links to managing them
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// This Page Is Valid XHTML 1.0 Transitional! 16Nov05


$permission=22; // Administrate
require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

include('htmlheader.inc.php');

$sql  = "SELECT *,users.id AS userid FROM users, roles ";
$sql .= "WHERE users.roleid=roles.id ";

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

?>
<h2>Manage Users</h2>
<p class='contextmenu' align='center'><a href="add_user.php?action=showform">Add User</a> |
<a href="edit_user_permissions.php">Set Role Permissions</a>
</p>
<table align='center'>
<tr>
    <th><a href="<?php echo $_SERVER['PHP_SELF'] ?>?sort=realname">Name</a> (<a href="<?php echo $_SERVER['PHP_SELF'] ?>?sort=username">Username</a>)</th>
    <th><a href="<?php echo $_SERVER['PHP_SELF'] ?>?sort=role">Role</a></th>
    <th>Account Status</th>
    <th>Actions</th>

    <th><a href="<?php echo $_SERVER['PHP_SELF'] ?>?sort=email">Email</a></th>
    <th><a href="<?php echo $_SERVER['PHP_SELF'] ?>?sort=phone">Phone</a></th>
    <th><a href="<?php echo $_SERVER['PHP_SELF'] ?>?sort=mobile">Mobile</a></th>
    <th><a href="<?php echo $_SERVER['PHP_SELF'] ?>?sort=fax">Fax</a></th>
    <th><a href="<?php echo $_SERVER['PHP_SELF'] ?>?sort=status">Status</a></th>
    <th><a href="<?php echo $_SERVER['PHP_SELF'] ?>?sort=accepting">Accepting</a></th>
</tr>
<?php
// show results
$shade = 0;
while ($users = mysql_fetch_array($result))
{
    // define class for table row shading
    if ($shade) $class = "shade1";
    else $class = "shade2";
    if ($users['status']==0) $class='expired';
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
    if ($users['status']>0) echo "Enabled";  // echo "<a href=\"javascript:alert('You cannnot currently disable accounts from here, go to the raw database and set the users status to zero')\">Disable</a>";
    else echo "Disabled";
    }
    else echo "-";

    echo "</td>";

    echo "<td>";
    echo "<a href='edit_profile.php?userid={$users['userid']}'>Edit</a>";
    if ($users['status']>0)
    {
        echo " | ";
        if ($users['userid'] >1) echo "<a href='reset_user_password.php?id={$users['userid']}'>Reset Password</a> | ";
        echo "<a href='edit_user_software.php?user={$users['userid']}'>Skills</a>";
        echo " | <a href='edit_backup_users.php?user={$users['userid']}'>Substitutes</a>";
        if ($users['userid'] >1) echo " | <a href='edit_user_permissions.php?action=edit&amp;user={$users['userid']}'>Permissions</a>";
    }
    echo "</td>";


    echo "<td>";
    echo $users["email"];


    echo "</td><td>";
    if ($users["phone"] == "") echo "None";
    else echo $users["phone"]; 
    echo "</td><td>";
    if ($users["mobile"] == "") echo "None";
    else echo $users["mobile"]; 
    echo "</td><td>";
    if ($users["fax"] == "") echo "None";
    else echo $users["fax"];
    echo "</td><td>";
    echo userstatus_name($users["status"]);
    echo "</td><td>";
    if($users["accepting"]=='Yes') echo "Yes";
    else echo "<span class='error'>No</span>";
    echo "</td></tr>";
    // invert shade
    if ($shade == 1) $shade = 0;
    else $shade = 1;
}
echo "</table>\n";

// free result and disconnect
mysql_free_result($result);

include('htmlfooter.inc.php');
?>
