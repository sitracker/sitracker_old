<?php
// add_template.php - Form for adding new templates
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

@include ('set_include_path.inc.php');
$permission = 16; // Add Email Template

require ('db_connect.inc.php');
require ('functions.inc.php');
// This page requires authentication
require ('auth.inc.php');

if (!empty($_POST['type']))
{
    $type = cleanvar($_POST['type']);
    $name = cleanvar($_POST['name']);

    if ($type == 'email')
    {
        $sql = "INSERT INTO `{$dbEmailTemplates}`(name) VALUES('{$name}')";
        mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);		
        $id = mysql_insert_id();
        header("Location: templates.php?id={$id}&action=edit&template=email");
    }
    elseif ($type == 'notice')
    {
        $sql = "INSERT INTO `{$dbNoticeTemplates}`(name) VALUES('{$name}')";
        mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);		
        $id = mysql_insert_id();
        header("Location: templates.php?id={$id}&action=edit&template=notice");		
    }
}
include ('htmlheader.inc.php');

echo "<h2>".icon('add', 32)." {$strAddTemplate}</h2>";

echo "<form action='{$_SERVER['PHP_SELF']}?action=add' method='post'>";
echo "<p align='center'>{$strType}: ";
echo "<select name='type'>";
echo "<option value='email'>{$strEmail}</option>";
echo "<option value='notice'>{$strNotice}</option>";
echo "</select><br /><br />";
echo "{$strName}: <input name='name' />";
echo "<br /><br /><input type='submit' value='{$strAdd}' />";
echo "</p>";
echo "</form>";

include ('htmlfooter.inc.php');

?>
