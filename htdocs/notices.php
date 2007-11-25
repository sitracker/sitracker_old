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
    echo "<label for='session'>{$strDurability}:</label> <select name='session'><option>{$strSticky}</option><option>{$strSession}</option></select><br /><br />";
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
        echo "<td>".stripslashes(bbcode($notice->text))."</td>";
        echo "<td>";
        // Don't allow deleting system messages
        if(!in_array($notice->id, $CONFIG['permanent_notices']))
                echo "<a href='{$_SERVER[PHP_SELF]}?action=delete&amp;id={$notice->id}'>{$strDelete}</a>";
        else
            echo $strDelete;
        echo "</td></tr>\n";
        if ($shade=='shade1') $shade='shade2';
        else $shade='shade1';
    }
    echo "</table>\n";

    echo "<p align='center'><a href='{$_SERVER[PHP_SELF]}?action=new'>{$strPostNewNotice}</a></p>";
    include('htmlfooter.inc.php');
}

?>