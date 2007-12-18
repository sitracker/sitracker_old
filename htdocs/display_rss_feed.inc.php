<?php
// display_rss_feed.inc.php - Page to render the RSS feed so it can be done in a different thread
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>

@include ('set_include_path.inc.php');
$permission=0; // not required
require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

if ($_SESSION['auth'] == TRUE) $styleid = $_SESSION['style'];
else $styleid= $CONFIG['default_interface_style'];
$csssql = "SELECT cssurl, iconset FROM interfacestyles WHERE id='{$styleid}'";
$cssresult = mysql_query($csssql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
else list($cssurl, $iconset) = mysql_fetch_row($cssresult);

/*
  Originally from dashboard/dashboard.inc.php
*/


require_once('magpierss/rss_fetch.inc');

$sql = "SELECT url, items FROM dashboard_rss WHERE owner = {$sit[2]} AND enabled = 'true'";
$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

define('MAGPIE_CACHE_ON',TRUE);
define('MAGPIE_CACHE_DIR', $CONFIG['attachment_fspath'].'feeds');
define('MAGPIE_OUTPUT_ENCODING', $i18ncharset);

$feedallowedtags = '<img><strong><em><br><p>';

if (mysql_num_rows($result) > 0)
{
    while ($row = mysql_fetch_row($result))
    {
        $url = $row[0];
        if ($rss = fetch_rss( $url ))
        {
//              if ($CONFIG['debug']) echo "<pre>".print_r($rss,true)."</pre>";
            echo "<table align='center' style='width: 100%'>";
            echo "<tr><th><span style='float: right;'><a href='".htmlspecialchars($url)."'>";
            echo "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/12x12/feed-icon.png' style='border: 0px;' alt='Feed Icon' />";
            echo "</a></span>";
            echo "<a href='{$rss->channel['link']}' style='color: #000;' class='info'>{$rss->channel['title']}";
            if (!empty($rss->image['url']) OR !empty($rss->channel['description']) OR !empty($rss->channel['icon']))
            {
                echo "<span>";
                if (!empty($rss->image['url'])) echo "<img src='{$rss->image['url']}' alt='{$rss->image['title']}' style='float: right; margin-right: 2px; margin-left: 5px; margin-top: 2px;' />";
                elseif (!empty($rss->channel['icon'])) echo "<img src='{$rss->channel['icon']}' style='float: right; margin-right: 2px; margin-left: 5px; margin-top: 2px;' />";
                echo "{$rss->channel['description']}</span>";
            }
            echo "</a>";
            echo "</th></tr>\n";
            $counter=0;
            foreach ($rss->items as $item)
            {
                //echo "<pre>".print_r($item,true)."</pre>";
                echo "<tr><td>";
                echo "<a href='{$item['link']}' class='info'>{$item['title']}";
                if ($rss->feed_type == 'RSS')
                {
                    if (!empty($item['pubdate'])) $itemdate = strtotime($item['pubdate']);
                    elseif (!empty($item['dc']['date'])) $itemdate = strtotime($item['dc']['date']);
                    else $itemdate = '';
                    $d = strip_tags($item['description'],$feedallowedtags);
                }
                elseif ($rss->feed_type == 'Atom')
                {
                    if (!empty($item['issued'])) $itemdate = strtotime($item['issued']);
                    elseif (!empty($item['published'])) $itemdate = strtotime($item['published']);
                    $d = strip_tags($item['atom_content'],$feedallowedtags);
                }
                if ($itemdate > 10000) $itemdate = date($CONFIG['dateformat_datetime'], $itemdate);
                echo "<span>";
                if (!empty($itemdate)) echo "<strong>{$itemdate}</strong><br />";
                echo "{$d}</span></a></td></tr>\n";
                $counter++;
                if (($row[1] > 0) AND $counter > $row[1]) break;
            }
            echo "</table>\n";
        }
        else
        {
            echo "Error: It's not possible to get $url...";
        }
    }
}
else
{
    echo "<p align='center'>{$strNoRecords}</p>";
}

?>