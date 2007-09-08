<?php
// dashboard_tags.php - Show tags
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//


function dashboard_tags($row,$dashboardid)
{
    echo "<div class='windowbox' style='width: 95%' id='$row-$dashboardid'>";
    echo "<div class='windowtitle'><a href='view_tags.php'>Tags</a></div>";
    echo "<div class='window'>";
    echo show_tag_cloud();
    echo "</div>";
    echo "</div>";
}

?>
