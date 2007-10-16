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
    echo "<div class='windowbox' style='width: 95%' id='$row-$dashboardid'>";
    echo "<div class='windowtitle'>{$GLOBALS['strRandomTip']}</div>";
    echo "<div class='window'>";
    echo random_tip();
    echo "</div>";
    echo "</div>";
}

?>
