<?php
// dashboard_watch_incidents.php - Watch incidents on your dashboard either from a site, a customer or a user
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>

$dashboard_watch_incidents_version = 1;

function dashboard_watch_incidents($row,$dashboardid)
{
    global $sit, $CONFIG, $iconset;

    echo "<div class='windowbox' id='$row-$dashboardid'>";
    echo "<div class='windowtitle'><div><a href='edit_watch_incidents.php'>";
    echo "{$GLOBALS['strEdit']}</a></div>".icon('support', 16)." ";
    echo printf($GLOBALS['strWatchIncidents'], user_realname($user,TRUE));
    echo "</div><div class='window' id='watch_incidents_windows'>";

    echo "<p align='center'><img src='{$CONFIG['application_webpath']}images/ajax-loader.gif' alt='Loading icon' /></p>";

    echo "</div>";
    echo "</div>";
    echo "<script type='text/javascript'>\n//<![CDATA[\nget_and_display('display_watch_incidents.inc.php','watch_incidents_windows');\n//]]>\n</script>";
}

function dashboard_watch_incidents_install()
{
    $schema = "CREATE TABLE IF NOT EXISTS `{$CONFIG['db_tableprefix']}dashboard_watch_incidents` (
        `userid` tinyint(4) NOT NULL,
        `type` tinyint(4) NOT NULL,
        `id` int(11) NOT NULL,
        PRIMARY KEY  (`userid`,`type`,`id`)
        ) ENGINE=MyISAM ;";

    $result = mysql_query($schema);
    if (mysql_error())
    {
        echo "<p>Dashboard watch incidents failed to install, please run the following SQL statement on the SiT database to create the required schema.</p>";
        echo "<pre>{$schema}</pre>";
        $res = FALSE;
    }
    else $res = TRUE;

    return $res;
}


?>
