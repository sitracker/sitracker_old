<?php
// edit_rss_feeds.php - Allow users to change their RSS feeds
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>

$permission=0; // not required
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

$action = $_REQUEST['action'];

switch($action)
{
    case 'add':
        include('htmlheader.inc.php');
        echo "<h2>Add RSS/Atom feed</h2>";
        echo "<form action='{$_SERVER['PHP_SELF']}?action=do_add' method='post'>";
        echo "<table class='vertical'>";
        echo "<tr><td><img src='{$CONFIG['application_webpath']}images/feed-icon-12x12.jpg' style='border: 0px;' alt='Feed Icon' /> RSS/Atom Feed URL: <input type='text' name='url' size='45' /></td></tr>\n";
        echo "</table>";
        echo "<p align='center'><input name='submit' type='submit' value='Add' /></p>";
        echo "</form>";
        include('htmlfooter.inc.php');
        break;
    case 'do_add':
        $url = $_REQUEST['url'];
        $enable = $_REQUEST['enable'];
        $sql = "INSERT INTO dashboard_rss VALUES ({$sit[2]},'{$url}','true')"; //SET enabled = '{$enable}' WHERE url = '{$url}' AND owner = {$sit[2]}";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        if(!$result) echo "<p class='error'>Failed to add feed</p>";
        else
        {
            confirmation_page("2", "edit_rss_feeds.php", "<h2>Feed added</h2><h5>Please wait while you are redirected...</h5>");
        }
        break;
    case 'enable':
        $url = $_REQUEST['url'];
        $enable = $_REQUEST['enable'];
        $sql = "UPDATE dashboard_rss SET enabled = '{$enable}' WHERE url = '{$url}' AND owner = {$sit[2]}";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        if(!$result) echo "<p class='error'>Changed enabled state failed</p>";
        else
        {
            confirmation_page("2", "edit_rss_feeds.php", "<h2>RSS feed change succeded</h2><h5>Please wait while you are redirected...</h5>");
        }
        break;
    case 'delete':
        $url = $_REQUEST['url'];
        $enable = $_REQUEST['enable'];
        $sql = "DELETE FROM dashboard_rss WHERE url = '{$url}' AND owner = {$sit[2]}";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        if(!$result) echo "<p class='error'>Delete feedfailed</p>";
        else
        {
            confirmation_page("2", "edit_rss_feeds.php", "<h2>RSS feed removal succeded</h2><h5>Please wait while you are redirected...</h5>");
        }
        break;
    default:
        include('htmlheader.inc.php');
        echo "<h2>Edit RSS/Atom feeds<h2>";

        $sql = "SELECT * FROM dashboard_rss WHERE owner = {$sit[2]}";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        if(mysql_num_rows($result) > 0)
        {
            echo "<table align='center'>";
            echo "<tr><th>URL</th><th>enabled</th><th>actions</th></tr>";
            while($obj = mysql_fetch_object($result))
            {
                if($obj->enabled == "true") $opposite = "false";
                else $opposite = "true";
                echo "<tr><td align='left'><img src='{$CONFIG['application_webpath']}images/feed-icon-12x12.jpg' style='border: 0px;' alt='Feed Icon' /> {$obj->url}</td><td>";
                echo "<a href='{$_SERVER['PHP_SELF']}?action=enable&amp;url=".urlencode($obj->url)."&amp;enable={$opposite}'>{$obj->enabled}</a></td>";
                echo "<td><a href='{$_SERVER['PHP_SELF']}?action=delete&amp;url=".urlencode($obj->url)."'>Remove</a></td></tr>";
            }
            echo "</table>";
        }
        else echo "<p align='center'>No feeds currently present</p>";
        echo "<p align='center'><a href='{$_SERVER['PHP_SELF']}?action=add'>Add</a></p>";
        include('htmlfooter.inc.php');
        break;

}

?>
