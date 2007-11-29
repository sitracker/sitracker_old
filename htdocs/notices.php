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

$permission=69;

require('db_connect.inc.php');
require('functions.inc.php');
require('auth.inc.php');


$action = cleanvar($_REQUEST['action']);
if($action == 'new')
{
    include('htmlheader.inc.php');
    echo "<h2>{$strNotices}</h2>";
    echo "<p align='center'>{$strNoticesBlurb}</p>";
    echo "<table align='center'><tr><th>{$strCode}</th><th>{$strOutput}</th></tr>";
    echo "<tr><th>[b][/b]</th><td>".bbcode("[b]Bold[/b]")."</td></tr>";
    echo "<tr><th>[i][/i]</th><td>".bbcode("[i]Italic[/i]")."</td></tr>";
    echo "<tr><th>[u][/u]</th><td>".bbcode("[u]Underline[/u]")."</td></tr>";
    echo "<tr><th>[quote][/quote]</th><td>".bbcode("[quote]Quote[/quote]")."</td></tr>";
    echo "<tr><th>[quote=][/quote]</th><td>".bbcode("[quote=Quote]Quote[/quote]")."</td></tr>";
    echo "<tr><th>[url][/url]</th><td>".bbcode("[url]http://url.com[/url]")."</td></tr>";
    echo "<tr><th>[url=][/url]</th><td>".bbcode("[url=http://url.com]URL[/url]")."</td></tr>";
    echo "<tr><th>[img][/img]</th><td>".bbcode("[img]http://sitdemo.salfordsoftware.co.uk/images/sit_favicon.png[/img]")."</td></tr>";
    echo "<tr><th>[color=][/color]</th><td>".bbcode("[color=red]Red[/color]")."</td></tr>";
    echo "<tr><th>[size=][/size]</th><td>".bbcode("[size=12]Size 12[/size]")."</td></tr>";
    echo "<tr><th>[code][/code]</th><td>".bbcode("[code]echo 'code';[/code]")."</td></tr>";
    echo "<tr><th>[hr]</th><td>".bbcode("[hr]")."</td></tr>";
    echo "</table>";
    echo "<div align='center'><form action='{$_SERVER[PHP_SELF]}?action=post' method='post'><br /><br />";
    echo "<h3>{$strNotice}</h3>";
    echo "<textarea cols='60' rows='4' name='text'></textarea><br />";
    echo "<label for='session'>{$strDurability}:</label> <select name='durability'><option value='sticky'>{$strSticky}</option><option value='session'>{$strSession}</option></select><br /><br />";
    echo "<label for='type'>{$strType}:</label> <select name='type'><option value='".NORMAL_NOTICE_TYPE."'>{$strInfo}</option><option value='".WARNING_NOTICE_TYPE."'>{$strWarning}</option></select><br /><br />";
    echo "<input type='submit' value='{$strSave}' />";
    echo "</form></div>";
    echo "<p align='center'><a href='notices.php'>{$strReturnWithoutSaving}</a></p>";
    include('htmlfooter.inc.php');
}
elseif($action == 'post')
{
    $text = cleanvar($_REQUEST['text']);
    $type = cleanvar($_REQUEST['type']);
    $durability = cleanvar($_REQUEST['durability']);
    $gid = md5($text);

    //post new notice
    $sql = "SELECT id FROM users WHERE status != 0";
    $result = mysql_query($sql);
    while($user = mysql_fetch_object($result))
    {
        $sql = "INSERT INTO notices (userid, gid, type, text, timestamp, durability) ";
        $sql .= "VALUES({$user->id}, '{$gid}', {$type}, '{$text}', NOW(), '{$durability}')";
        mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    }

    confirmation_page(2, 'notices.php', '<h2>Notice Added</h2><p>You will be redirected, please wait...</p>');

}
elseif($action == 'delete')
{
    $noticeid = cleanvar($_REQUEST['id']);

    $sql = "SELECT gid FROM notices WHERE id='{$noticeid}'";
    $result = mysql_query($sql);
    $gid = mysql_fetch_object($result);

    $sql = "DELETE FROM notices WHERE gid='{$gid->gid}'";
    mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

    confirmation_page(2, 'notices.php', '<h2>Notice Deleted</h2><p>You will be redirected, please wait...</p>');
}
else
{
    include('htmlheader.inc.php');
    echo "<h2>{$strNotices}</h2>";

    //get all notices
    $sql = "SELECT * FROM notices WHERE type=".NORMAL_NOTICE_TYPE." OR type=".WARNING_NOTICE_TYPE." ";
    $sql .= "GROUP BY gid";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    $shade='shade1';
    if(mysql_num_rows($result) > 0)
    {
        echo "<table align='center'>";
        echo "<tr><th>{$strID}</th><th>{$strDate}</th><th>{$strNotice}</th><th>{$strOperation}</th></tr>\n";
        while($notice = mysql_fetch_object($result))
        {
            echo "<tr class='$shade'><td>{$notice->id}</td><td>{$notice->timestamp}</td>";
            echo "<td>".stripslashes(bbcode($notice->text))."</td>";
            echo "<td>";
            echo "<a href='{$_SERVER[PHP_SELF]}?action=delete&amp;id={$notice->id}'>{$strDelete}</a>";
            echo "</td></tr>\n";
            if ($shade=='shade1') $shade='shade2';
            else $shade='shade1';
        }
        echo "</table>\n";
    }
    else echo "<p align='center'>$strNoRecords</p>";

    echo "<p align='center'><a href='{$_SERVER[PHP_SELF]}?action=new'>{$strPostNewNotice}</a></p>";
    include('htmlfooter.inc.php');
}

?>