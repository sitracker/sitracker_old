<?php
// edit_rss_feeds.php - Allow users to change their RSS feeds
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>

@include('set_include_path.inc.php');
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
        echo "<h2>{$strAddRSSAtomFeed}</h2>";
        echo "<form action='{$_SERVER['PHP_SELF']}?action=do_add' method='post'>";
        echo "<table class='vertical'>";
        echo "<tr><td><label><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/12x12/feed-icon.png' style='border: 0px;' alt='{$strFeedIcon}' /> ";
        echo "{$strRSSAtomURL}: <input type='text' name='url' size='45' /></label></td></tr>\n";
        echo "<tr><td><label>{$strDisplay}: <input type='text' name='items' size='3' value='0' /></label> ({$str0MeansUnlimited})</td></tr>";
        echo "</table>";
        echo "<p align='center'><input name='submit' type='submit' value='{$strAdd}' /></p>";
        echo "</form>";
        include('htmlfooter.inc.php');
        break;
    case 'do_add':
        $url = cleanvar($_REQUEST['url']);
        $enable = cleanvar($_REQUEST['enable']);
        $items = cleanvar($_REQUEST['items']);
        $sql = "INSERT INTO dashboard_rss (owner, url, items, enabled) VALUES ({$sit[2]},'{$url}','{$items}','true')"; //SET enabled = '{$enable}' WHERE url = '{$url}' AND owner = {$sit[2]}";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        if(!$result) html_redirect("edit_rss_feeds.php", FALSE);
        else
        {
            html_redirect("edit_rss_feeds.php");
        }
        break;
    case 'edit':
        include('htmlheader.inc.php');
        $url = cleanvar(urldecode($_REQUEST['url']));
        $sql = "SELECT * FROM dashboard_rss WHERE owner = {$sit[2]} AND url = '{$url}' LIMIT 1 ";
        if ($CONFIG['debug']) $dbg .= print_r($sql,true);
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        if (mysql_num_rows($result) > 0)
        {
            $feed = mysql_fetch_object($result);
            if ($feed->items=='')
            {
                $feed->items=0;
            }
            
            echo "<h2>{$strEditRSSAtomFeed}</h2>";
            echo "<form action='{$_SERVER['PHP_SELF']}?action=do_edit' method='post'>";
            echo "<table class='vertical'>";
            echo "<tr><td><label><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/12x12/feed-icon.png' style='border: 0px;' alt='Feed Icon' /> ";
            echo "{$strRSSAtomURL}: <input type='text' name='url' size='45' value='{$feed->url}' /></label></td></tr>\n";
            echo "<tr><td><label>{$strDisplay}: <input type='text' name='items' size='3' value='{$feed->items}' /></label> ({$str0MeansUnlimited})</td></tr>";
            echo "</table>";
            echo "<input type='hidden' name='oldurl' size='45' value='{$feed->url}' />";
            echo "<p align='center'><input name='submit' type='submit' value='{$strSave}' /></p>";
            echo "</form>";
        }
        else
        {
            echo "<p class='error'>{$strNoRecords}</p>";
        }
        
        include('htmlfooter.inc.php');

        break;
    case 'do_edit':
        $url = cleanvar($_REQUEST['url']);
        $oldurl = cleanvar($_REQUEST['oldurl']);
        $items = cleanvar($_REQUEST['items']);
        $sql = "UPDATE dashboard_rss SET url = '{$url}', items = '{$items}' WHERE url = '{$oldurl}' AND owner = {$sit[2]}";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        if(!$result) html_redirect("edit_rss_feeds.php", FALSE);
        else html_redirect("edit_rss_feeds.php");
        break;
    case 'enable':
        $url = urldecode(cleanvar($_REQUEST['url']));
        $enable = cleanvar($_REQUEST['enable']);
        $sql = "UPDATE `dashboard_rss` SET `enabled` = '{$enable}' WHERE `url` = '{$url}' AND `owner` = {$sit[2]}";
        mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        if(mysql_affected_rows() < 1)
        {
            html_redirect("edit_rss_feeds.php", FALSE, "Changed enabled state failed");
        }
        else
        {
            html_redirect("edit_rss_feeds.php");
        }
        break;
    case 'delete':
        $url = $_REQUEST['url'];
        $enable = $_REQUEST['enable'];
        $sql = "DELETE FROM dashboard_rss WHERE url = '{$url}' AND owner = {$sit[2]}";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        if(!$result) html_redirect("edit_rss_feeds.php", FALSE);
        else html_redirect("edit_rss_feeds.php");
        break;
    default:
        include('htmlheader.inc.php');
        echo "<h2>{$strEditRSSAtomFeed}</h2>";

        $sql = "SELECT * FROM dashboard_rss WHERE owner = {$sit[2]}";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        if(mysql_num_rows($result) > 0)
        {
            echo "<table align='center'>\n";
            echo "<tr><th>URL</th><th>{$strDisplay}</th><th>{$strEnabled}</th><th>{$strOperation}</th></tr>\n";
            $shade = 'shade1';
            while($obj = mysql_fetch_object($result))
            {
                if($obj->enabled == "true")
                {
                    $opposite = "false";
                }
                else
                {
                    $opposite = "true";
                }
                
                $urlparts = parse_url($obj->url);
                if ($obj->enabled == 'false')
                {
                    $shade='expired';
                }
                
                echo "<tr class='$shade'><td align='left'><a href=\"".htmlentities($obj->url,ENT_NOQUOTES, $GLOBALS['i18ncharset'])."\"><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/12x12/feed-icon.png' style='border: 0px;' alt='{strFeedIcon}' /></a> <a href=\"{$obj->url}\">{$urlparts['host']}</a></td>";
                echo "<td>";
                if ($obj->items >= 1)
                {
                    echo "{$obj->items}";
                }
                else
                {
                    echo $strUnlimited;
                }
                
                echo "</td>";
                echo "<td><a href='{$_SERVER['PHP_SELF']}?action=enable&amp;url=".urlencode($obj->url)."&amp;enable={$opposite}'>{$obj->enabled}</a></td>";
                echo "<td><a href='{$_SERVER['PHP_SELF']}?action=edit&amp;url=".urlencode($obj->url)."'>{$strEdit}</a> | ";
                echo "<a href='{$_SERVER['PHP_SELF']}?action=delete&amp;url=".urlencode($obj->url)."'>{$strRemove}</a></td></tr>\n";
                if ($shade == 'shade1') $shade = 'shade2';
                else $shade = 'shade1';
            }
            echo "</table>\n";
        }
        else
        {
            echo "<p align='center'>{$strNoFeedsCurrentlyPresent}</p>";
        }
        
        echo "<p align='center'><a href='{$_SERVER['PHP_SELF']}?action=add'>{$strAdd}</a></p>";
        include('htmlfooter.inc.php');
        break;
}
?>
