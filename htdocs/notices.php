<?php
// notices.php - modify and add global notices
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Kieran Hogg[at]users.sourceforge.net>

//TODO: seperate permission or just admin users?
//$permission=;

require('db_connect.inc.php');
require('functions.inc.php');
require('auth.inc.php');

include('htmlheader.inc.php');

echo "<h2>{$strNotices}</h2>";

//get all notices
$sql = "SELECT * FROM notices";
$result = mysql_query($sql);
print_r($notice);

echo "<table align='center'>";
echo "<tr><th>{$strID}</th><th>{$strDate}</th><th>Text</th><th>{$strActions}</th></tr>";
while($notice = mysql_fetch_object($result))
{
    echo "<tr><td>{$notice->id}</td><td>{$notice->timestamp}</td><td>";
    echo "{$notice->text}</td><td>";
    echo "<a href='{$_SERVER[PHP_SELF]}?action=update&id={$notice->id}'>{$strUpdate}</a> | <a href='{$_SERVER[PHP_SELF]}?action=delete&id={$notice->id}'>{$strDelete}</a></tr>";
}
echo "</table>";

echo "<p align='center'><a href='{$_SERVER[PHP_SELF]}?action=new'>{$strPostNewNotice}</a></p>";


?>