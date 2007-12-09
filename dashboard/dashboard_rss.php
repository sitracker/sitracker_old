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

$dashboard_rss_version = 2;


function dashboard_rss($row,$dashboardid)
{
    global $sit, $CONFIG, $iconset;

    echo "<div class='windowbox' style='width: 95%' id='$row-$dashboardid'>";
    echo "<div class='windowtitle'><div style='float: right'><a href='edit_rss_feeds.php'>{$GLOBALS['strEdit']}</a></div><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/feed-icon.png' width='16' height='16' alt='' /> {$GLOBALS['strFeeds']}</div>";
    echo "<div class='window' id='rss_window'>";

    echo "<p align='center'><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/ajax-loader.gif' alt='Loading icon' /></p>";

    echo "</div>";
    echo "</div>";
    echo "<script type='text/javascript'>get_and_display('display_rss_feed.inc.php','rss_window');</script>";
}

function dashboard_rss_install()
{
    $schema = "CREATE TABLE `dashboard_rss` (
    `owner` tinyint(4) NOT NULL,
  `url` varchar(255) NOT NULL,
  `items` int(5) default NULL,
  `enabled` enum('true','false') NOT NULL,
    KEY `owner` (`owner`,`url`)
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

function dashboard_rss_upgrade()
{
    $upgrade_schema[2] = "
        -- INL 22Nov07
        ALTER TABLE `dashboard_rss` ADD `items` INT( 5 ) NULL AFTER `url`;
    ";

    return $upgrade_schema;
}

function dashboard_rss_get_version()
{
    global $dashboard_rss_version;
    return $dashboard_rss_version;
}


?>