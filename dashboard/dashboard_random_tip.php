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
    echo "<div class='windowbox' id='$row-$dashboardid'>";
    echo "<div class='windowtitle'><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/tip.png' width='16' height='16' alt='' /> {$GLOBALS['strRandomTip']}</div>";
    echo "<div class='window'>";

    $delim="\n";
    $tipsfile = "{$CONFIG['application_fspath']}htdocs/help/{$_SESSION['lang']}/tips.txt";
    if (!file_exists($tipsfile)) $tipsfile = "{$CONFIG['application_fspath']}htdocs/help/en-GB/tips.txt";
    if (!file_exists($tipsfile))
    {
        trigger_error("Tips file '{$tipsfile}' was not found!",E_USER_WARNING);
    }
    else
    {
        $fp = fopen($tipsfile, "r");
        if (!$fp) trigger_error("{$tipsfile} was not found!", E_USER_WARNING);
    }
    $contents = fread($fp, filesize($tipsfile));
    $tips = explode($delim,$contents);
    fclose($fp);
    srand((double)microtime()*1000000);
    $atip = (rand(1, sizeof($tips)) - 1);
    echo "#".($atip).": ".$tips[$atip];

    echo "</div>";
    echo "</div>";
}

?>