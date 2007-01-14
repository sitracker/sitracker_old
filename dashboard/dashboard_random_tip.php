<?php
// dashboard_random_tip.php - A random tip
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//


function dashboard_random_tip($row,$dashboardid)
{
    global $i18n_RANDOM_TIP;
    echo "<div class='windowbox' style='width: 95%' id='$row-$dashboardid'>";
    echo "<div class='windowtitle'>{$i18n_RANDOM_TIP}</div>";
    echo "<div class='window'>";
    echo random_tip();
    echo "</div>";
    echo "</div>";
}

?>