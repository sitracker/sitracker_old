<?php
// dashboard_random_tip.php - A random tip
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//


function dashboard_random_tip($row,$dashboardid)
{
    global $iconset;
    echo "<div class='windowbox' style='width: 95%' id='$row-$dashboardid'>";
    echo "<div class='windowtitle'><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/tip.png' width='16' height='16' alt='' /> {$GLOBALS['strRandomTip']}</div>";
    echo "<div class='window'>";
    echo random_tip();
    echo "</div>";
    echo "</div>";
}

?>
