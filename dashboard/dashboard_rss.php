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

    echo "<div class='windowbox' style='width: 95%' id='$row-$dashboardid'>";
    echo "<div class='windowtitle'><div style='float: right'><a href='edit_rss_feeds.php'>edit</a></div>Feeds</div>";
    echo "<div class='window'>";

    if(mysql_num_rows($result) > 0)
    {
        while($row = mysql_fetch_row($result))
        {
            $url = $row[0];
            if($rss = fetch_rss( $url ))
            {
//                 echo "<pre>".print_r($rss,true)."</pre>";
                echo "<table align='center' style='width: 100%'>";
                echo "<tr><th><span style='float: right;'><a href='".htmlspecialchars($url)."'><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/12x12/feed-icon.png' style='border: 0px;' alt='Feed Icon' /></a></span>{$rss->channel['title']}</th></tr>";
                foreach($rss->items as $item)
                {
//                     echo "<pre>".print_r($item,true)."</pre>";
                    echo "<tr><td><a href='{$item['link']}' class='info'>{$item['title']}";
                    if($rss->feed_type == 'RSS') $d = parse_updatebody($item['description']);
                    else if($rss->feed_type == 'Atom') $d = parse_updatebody($item['atom_content']);
                    echo "<span>{$d}</span></a></td></tr>";
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