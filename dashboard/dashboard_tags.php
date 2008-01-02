<?php
// dashboard_tags.php - Show tags
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

$dashboard_tags_version = 1;

function dashboard_tags($row,$dashboardid)
{
    global $CONFIG, $iconset;
    echo "<div class='windowbox' style='width: 95%' id='$row-$dashboardid'>";
    echo "<div class='windowtitle'><a href='view_tags.php'><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/tag.png' width='16' height='16' alt='' /> {$GLOBALS['strTags']}</a></div>";
    echo "<div class='window'>";
    echo show_tag_cloud();
    echo "</div>";
    echo "</div>";
}

?>
