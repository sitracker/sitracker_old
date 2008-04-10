<?php
// dashboard_random_tip.php - A random tip
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

$dashboard_random_tip_version = 1;

function dashboard_random_tip($row,$dashboardid)
{
    global $iconset, $CONFIG;
    echo "<div class='windowbox' style='width: 95%' id='$row-$dashboardid'>";
    echo "<div class='windowtitle'><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/tip.png' width='16' height='16' alt='' /> {$GLOBALS['strRandomTip']}</div>";
    echo "<div class='window'>";
    
    $delim="\n";
    if (!file_exists($CONFIG['tipsfile']))
    {
        trigger_error("Tips file '{$CONFIG['tipsfile']}' was not found!  check your paths!",E_USER_WARNING);
    }
    else
    {
        $fp = fopen($CONFIG['tipsfile'], "r");
        if (!$fp) trigger_error("{$CONFIG['tipsfile']} was not found!", E_USER_WARNING);
    }
    $contents = fread($fp, filesize($CONFIG['tipsfile']));
    $tips = explode($delim,$contents);
    fclose($fp);
    srand((double)microtime()*1000000);
    $atip = (rand(1, sizeof($tips)) - 1);
    echo "#".($atip+1).": ".$tips[$atip];

    echo "</div>";
    echo "</div>";
}

?>
