<?php
// notices.php - modify and add global notices
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Kieran Hogg[at]users.sourceforge.net>

@include ('set_include_path.inc.php');

$permission = 69;
require ('db_connect.inc.php');
require ('functions.inc.php');
require ('auth.inc.php');
include ('htmlheader.inc.php');


$action = cleanvar($_REQUEST['action']);
if ($action == 'new')
{
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
    include ('htmlfooter.inc.php');
}
elseif ($action == 'post')
{
    $text = cleanvar($_REQUEST['text']);
    $type = cleanvar($_REQUEST['type']);
    $durability = cleanvar($_REQUEST['durability']);
    $gid = md5($text);

    //post new notice
    $sql = "SELECT id FROM `{$dbUsers}` WHERE status != 0";
    $result = mysql_query($sql);
    
    //do this once so we can get a referenceID
    $user = mysql_fetch_object($result);
    $sql = "INSERT INTO `{$dbNotices}` (userid, type, text, timestamp, durability) ";
    $sql .= "VALUES({$user->id}, {$type}, '{$text}', NOW(), '{$durability}')";
    mysql_query($sql);
    if (mysql_error()) 
    {
        trigger_error(mysql_error(),E_USER_WARNING);
    }
    else
    {
        $refid = mysql_insert_id();
        $sql = "UPDATE `$dbNotices` SET referenceid='{$refid}' WHERE id='{$refid}'";
        mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        
        while ($user = mysql_fetch_object($result))
        {
            $sql = "INSERT INTO `{$dbNotices}` (userid, referenceid, type, text, timestamp, durability) ";
            $sql .= "VALUES({$user->id}, '{$refid}', {$type}, '{$text}', NOW(), '{$durability}')";
            mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        }
        html_redirect('notices.php');
    }
}
elseif ($action == 'delete')
{
    $noticeid = cleanvar($_REQUEST['id']);

    $sql = "SELECT referenceid, type FROM `{$dbNotices}` WHERE id='{$noticeid}'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    $noticeobj = mysql_fetch_object($result);

    $sql = "DELETE FROM `{$dbNotices}` WHERE referenceid='{$noticeobj->referenceid}' ";
    $sql .= "AND type='{$noticeobj->type}' ";
    mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

    html_redirect('notices.php');
}
else
{
    echo "<h2>{$strNotices}</h2>";

    //get all notices
    $sql = "SELECT * FROM `{$dbNotices}` WHERE type=".NORMAL_NOTICE_TYPE." OR type=".WARNING_NOTICE_TYPE." ";
    $sql .= "GROUP BY referenceid";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    $shade = 'shade1';
    if (mysql_num_rows($result) > 0)
    {
        echo "<table align='center'>";
        echo "<tr><th>{$strID}</th><th>{$strDate}</th><th>{$strNotice}</th><th>{$strOperation}</th></tr>\n";
        while ($notice = mysql_fetch_object($result))
        {
            echo "<tr class='$shade'><td>{$notice->id}</td><td>{$notice->timestamp}</td>";
            echo "<td>".bbcode($notice->text)."</td>";
            echo "<td>";
            echo "<a href='{$_SERVER[PHP_SELF]}?action=delete&amp;id=";
            echo "{$notice->id}'>{$strRevoke}</a>".help_link('RevokeNotice');
            echo "</td></tr>\n";
            if ($shade == 'shade1') $shade = 'shade2';
            else $shade = 'shade1';
        }
        echo "</table>\n";
    }
    else
    {
        echo "<p align='center'>{$strNoRecords}</p>";
    }

    echo "<p align='center'><a href='{$_SERVER[PHP_SELF]}?action=new'>{$strPostNewNotice}</a></p>";
    include ('htmlfooter.inc.php');
}

?>
