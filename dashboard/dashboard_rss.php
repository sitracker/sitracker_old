<?php
// dashboard_rss.php - Display your rss feeds on the dashboard
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>


function dashboard_rss($row,$dashboardid)
{
    global $sit, $CONFIG, $iconset;
    require_once('magpierss/rss_fetch.inc');

    $sql = "SELECT url FROM dashboard_rss WHERE owner = {$sit[2]} AND enabled = 'true'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

    define('MAGPIE_CACHE_ON',TRUE);
    define('MAGPIE_CACHE_DIR', $CONFIG['attachment_fspath'].'feeds');
    define('MAGPIE_OUTPUT_ENCODING', 'UTF-8');

    $feedallowedtags = '<img><strong><em><br><p>';

    echo "<div class='windowbox' style='width: 95%' id='$row-$dashboardid'>";
    echo "<div class='windowtitle'><div style='float: right'><a href='edit_rss_feeds.php'>edit</a></div><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/feed-icon.png' width='16' height='16' alt='' /> Feeds</div>";
    echo "<div class='window'>";

    if(mysql_num_rows($result) > 0)
    {
        while($row = mysql_fetch_row($result))
        {
            $url = $row[0];
            if($rss = fetch_rss( $url ))
            {
//                  echo "<pre>".print_r($rss,true)."</pre>";
                echo "<table align='center' style='width: 100%'>";
                echo "<tr><th><span style='float: right;'><a href='".htmlspecialchars($url)."'>";
                echo "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/12x12/feed-icon.png' style='border: 0px;' alt='Feed Icon' />";
                echo "</a></span>";
                echo "<a href='{$rss->channel['link']}' style='color: #000;' class='info'>{$rss->channel['title']}";
                if (!empty($rss->image['url']) OR !empty($rss->channel['description']))
                {
                    echo "<span>";
                    if (!empty($rss->image['url'])) echo "<img src='{$rss->image['url']}' alt='{$rss->image['title']}' style='float: right; margin-right: 2px; margin-left: 5px; margin-top: 2px;' />";
                    echo "{$rss->channel['description']}</span>";
                }
                echo "</a>";
                echo "</th></tr>";
                foreach($rss->items as $item)
                {
//                      echo "<pre>".print_r($item,true)."</pre>";
                    echo "<tr><td><a href='{$item['link']}' class='info'>{$item['title']}";
                    if($rss->feed_type == 'RSS')
                    {
                        if (!empty($item['pubdate'])) $itemdate = strtotime($item['pubdate']);
                        elseif (!empty($item['dc']['date'])) $itemdate = strtotime($item['dc']['date']);
                        else $itemdate = '';
                        $d = strip_tags($item['description'],$feedallowedtags);
                    }
                    elseif($rss->feed_type == 'Atom')
                    {
                        if (!empty($item['issued'])) $itemdate = strtotime($item['issued']);
                        $d = strip_tags($item['atom_content'],$feedallowedtags);
                    }
                    if ($itemdate > 10000) $itemdate = date($CONFIG['dateformat_datetime'], $itemdate);
                    echo "<span>";
                    if (!empty($itemdate)) echo "<strong>{$itemdate}</strong><br />";
                    echo "{$d}</span></a></td></tr>";
                }
                echo "</table>";
            }
            else
            {
                echo "Error: It's not possible to get $url...";
            }
        }
    }
    echo "</div>";
    echo "</div>";
}

function dashboard_rss_install()
{
    $schema = "CREATE TABLE `dashboard_rss` (
    `owner` TINYINT NOT NULL ,
    `url` VARCHAR( 255 ) NOT NULL ,
    `enabled` ENUM( 'true', 'false' ) NOT NULL ,
    INDEX ( `owner` , `url` )
    ) ENGINE = MYISAM ;";

    $result = mysql_query($schema);
    if (mysql_error())
    {
        echo "<p>Dashboard RSS failed to install, please run the following SQL statement on the SiT database to create the required schema.</p>";
        echo "<pre>{$schema}</pre>";
        $res=FALSE;
    } else $res=TRUE;

    return $res;
}

?>