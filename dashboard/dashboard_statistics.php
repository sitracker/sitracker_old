<?php
// dashboard_statistics.php - Display summary statistics on the dashboard
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Authors: Paul Heaney <paulheaney[at]users.sourceforge.net>
//          Ivan Lucas <ivanlucas[at]users.sourceforge.net>


$dashboard_statistics_version = 1;

function dashboard_statistics($row,$dashboardid)
{
    global $todayrecent, $dbIncidents, $dbKBArticles, $iconset;
    // Count incidents logged today
    $sql = "SELECT id FROM `{$dbIncidents}` WHERE opened > '$todayrecent'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $todaysincidents=mysql_num_rows($result);
    mysql_free_result($result);

    // Count incidents updated today
    $sql = "SELECT id FROM `{$dbIncidents}` WHERE lastupdated > '$todayrecent'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $todaysupdated=mysql_num_rows($result);
    mysql_free_result($result);

    // Count incidents closed today
    $sql = "SELECT id FROM `{$dbIncidents}` WHERE closed > '$todayrecent'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $todaysclosed=mysql_num_rows($result);
    mysql_free_result($result);

    // count total number of SUPPORT incidents that are open at this time (not closed)
    $sql = "SELECT id FROM `{$dbIncidents}` WHERE status!=2 AND status!=9 AND status!=7 AND type='support'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $supportopen=mysql_num_rows($result);
    mysql_free_result($result);

        // Count kb articles published today
    $sql = "SELECT docid FROM `{$dbKBArticles}` WHERE published > '".date('Y-m-d')."'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $kbpublished=mysql_num_rows($result);
    mysql_free_result($result);

    echo "<div class='windowbox' style='width: 95%' id='$row-$dashboardid'>";
    echo "<div class='windowtitle'><a href='statistics.php'><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/statistics.png' width='16' height='16' alt='' /> {$GLOBALS['strTodaysStats']}</a></div>";
    echo "<div class='window'>";
    if ($todaysincidents == 0) echo $GLOBALS['strNoIncidents'];
    elseif ($todaysincidents == 1) echo "<a href='statistics.php'>{$todaysincidents}</a> {$GLOBALS['strIncident']}";
    elseif ($todaysincidents > 1) echo "<a href='statistics.php'>".sprintf($GLOBALS['strIncidentsMulti'], $todaysincidents)."</a>";
    echo " {$GLOBALS['strLogged']}<br />";

    if ($todaysupdated == 0) echo $GLOBALS['strNoIncidents'];
    elseif ($todaysupdated == 1) echo "{$todaysupdated} {$GLOBALS['strIncident']}";
    elseif ($todaysupdated > 1) echo sprintf($GLOBALS['strIncidentsMulti'],$todaysupdated);
    echo " {$GLOBALS['strUpdated']}<br />";

    if ($todaysclosed == 0) echo $GLOBALS['strNoIncidents'];
    elseif ($todaysclosed == 1) echo "<a href='statistics.php'>{$todaysclosed}</a> {$GLOBALS['$strIncident']}";
    elseif ($todaysclosed > 1) echo "<a href='statistics.php'>".sprintf($GLOBALS['strIncidentsMulti'],$todaysclosed)."</a>";
    echo " {$GLOBALS['strClosed']}<br />";

    if ($supportopen == 0) echo $GLOBALS['strNoIncidents'];
    elseif ($supportopen == 1) echo "{$supportopen} {$GLOBALS['strIncident']}";
    elseif ($supportopen > 1) echo "{$supportopen} {$GLOBALS['strIncidentMulti']}";
    echo " {$GLOBALS['strCurrentlyOpen']}<br />";

    if ($kbpublished == 0) echo $GLOBALS['strNoKBArticles'];
    elseif ($kbpublished == 1) echo "<a href='browse_kb.php?mode=today' title='View articles published today'>{$kbpublished}</a> KB Article";
    elseif ($kbpublished > 1) echo "<a href='browse_kb.php?mode=today' title='View articles published today'>{$kbpublished}</a> KB Articles";
    echo " {$GLOBALS['strPublished']}<br />";

    echo "</div>";
    echo "</div>";
}

?>
