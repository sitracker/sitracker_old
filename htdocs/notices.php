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

$permission=70;

require('db_connect.inc.php');
require('functions.inc.php');
require('auth.inc.php');


$action = cleanvar($_REQUEST['action']);
if($action == 'new')
{
    include('htmlheader.inc.php');
    echo "<h2>{$strPostNewNotices}</h2>";
    echo "<p align='center'>This page allows you to post a new global notice. BB code is allowed and useful variables are shown below.</p>";
    echo "<table align='center'><tr><th>{$strVariable}</th><th>{$strValue}</th></tr>";
    echo '<tr><td>$CONFIG[\'application_name\']</td><td>'.$CONFIG['application_name'].'</td></tr>';
    echo '<tr><td>$CONFIG[\'application_shortname\']</td><td>'.$CONFIG['application_shortname'].'</td></tr>';
    echo "</table>";
    echo "<div align='center'><form action='{$_SERVER[PHP_SELF]}?action=post' method='post'>";
    echo "<h3>{$strNotice}</h3>";
    echo "<textarea cols='60' rows='4' name='text'></textarea><br />";
    echo "<input type='submit' value='{$strSave}' />";
    echo "</form></div>";
    echo "<p align='center'><a href='notices.php'>{$strReturnWithoutSaving}</a></p>";
    include('htmlfooter.inc.php');
}
elseif($action == 'post')
{
    $text = cleanvar($_REQUEST['text']);

    //post new notice
    $sql = "INSERT INTO notices (type, text, timestamp) VALUES(2, '$text', NOW())";
    mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

    $noticeid = mysql_insert_id();

    $sql = "SELECT id FROM users WHERE status != 0";
    $result = mysql_query($sql);
    while($user = mysql_fetch_object($result))
    {
        $sql = "INSERT INTO usernotices (noticeid, userid, durability) VALUES({$noticeid}, {$user->id}, 'sticky')";
        mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    }

    confirmation_page(2, 'notices.php', '<h2>Notice Added</h2><p>You will be redirected, please wait...</p>');

}
elseif($action == 'delete')
{
    $noticeid = cleanvar($_REQUEST['id']);
    $sql = "DELETE FROM notices WHERE id={$noticeid} LIMIT 1";
    mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

    $sql = "SELECT id FROM users WHERE status != 0";
    $result = mysql_query($sql);
    while($user = mysql_fetch_object($result))
    {
        $sql = "DELETE FROM usernotices WHERE noticeid={$noticeid} AND userid={$user->id}";
        mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    }

    confirmation_page(2, 'notices.php', '<h2>Notice Deleted</h2><p>You will be redirected, please wait...</p>');
}
else
{
    include('htmlheader.inc.php');
    echo "<h2>{$strNotices}</h2>";

    //get all notices
    $sql = "SELECT * FROM notices";
    $result = mysql_query($sql);
    print_r($notice);

    echo "<table align='center'>";
    echo "<tr><th>{$strID}</th><th>{$strDate}</th><th>{$strNotice}</th><th>{$strOperation}</th></tr>\n";
    $shade='shade1';
    while($notice = mysql_fetch_object($result))
    {
        echo "<tr class='$shade'><td>{$notice->id}</td><td>{$notice->timestamp}</td>";
        echo "<td>".stripslashes($notice->text)."</td>";
        echo "<td>";
        // Don't allow deleting system messages
        if ($type != 1) echo "<a href='{$_SERVER[PHP_SELF]}?action=delete&amp;id={$notice->id}'>{$strDelete}</a>";
        echo "</td></tr>\n";
        if ($shade=='shade1') $shade='shade2';
        else $shade='shade1';
    }
    echo "</table>\n";

    echo "<p align='center'><a href='{$_SERVER[PHP_SELF]}?action=new'>{$strPostNewNotice}</a></p>";
    include('htmlfooter.inc.php');
}

?>