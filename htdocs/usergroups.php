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

include('htmlheader.inc.php');

echo "<h2>$title</h2>";

$gsql = "SELECT * FROM groups ORDER BY name";
$gresult = mysql_query($gsql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
while ($group = mysql_fetch_object($gresult))
{
    $grouparr[$group->id]=$grouparr->name;
}
// TODO finish a group selection drop down


$sql = "SELECT * FROM users ORDER BY realname";
$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

echo "<table summary='User Groups' align='center'>";
echo "<tr><th>User</th><th>Group</th>";
while ($user = mysql_fetch_object($result))
{
    echo "<tr><td>{$user->realname} ({$user->username})</td>";
    echo "<td>{$user->groupid}</td></tr>\n";
}
echo "</table>\n";

include('htmlfooter.inc.php');

?>