<?php

function dashboard_statistics($row,$dashboardid)
{
    global $todayrecent;
    // Count incidents logged today
    $sql = "SELECT id FROM incidents WHERE opened > '$todayrecent'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $todaysincidents=mysql_num_rows($result);
    mysql_free_result($result);

    // Count incidents updated today
    $sql = "SELECT id FROM incidents WHERE lastupdated > '$todayrecent'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $todaysupdated=mysql_num_rows($result);
    mysql_free_result($result);

    // Count incidents closed today
    $sql = "SELECT id FROM incidents WHERE closed > '$todayrecent'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $todaysclosed=mysql_num_rows($result);
    mysql_free_result($result);

    // count total number of SUPPORT incidents that are open at this time (not closed)
    $sql = "SELECT id FROM incidents WHERE status!=2 AND status!=9 AND status!=7 AND type='support'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $supportopen=mysql_num_rows($result);
    mysql_free_result($result);

        // Count kb articles published today
    $sql = "SELECT docid FROM kbarticles WHERE published > '".date('Y-m-d')."'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $kbpublished=mysql_num_rows($result);
    mysql_free_result($result);

    echo "<div class='windowbox' style='width: 95%' id='$row-$dashboardid'>";
    echo "<div class='windowtitle'><a href='statistics.php'>{$GLOBALS['strTodaysStats']}</a></div>";
    echo "<div class='window'>";
    if ($todaysincidents == 0) echo $GLOBALS['strNoIncidents'];
    elseif ($todaysincidents == 1) echo "<a href='statistics.php'>{$todaysincidents}</a> Incident";
    elseif ($todaysincidents > 1) echo "<a href='statistics.php'>{$todaysincidents}</a> Incidents";
    echo " {$GLOBALS['strLogged']}<br />";

    if ($todaysupdated == 0) echo $GLOBALS['strNoIncidents'];
    elseif ($todaysupdated == 1) echo "{$todaysupdated} Incident";
    elseif ($todaysupdated > 1) echo "{$todaysupdated} Incidents";
    echo " {$GLOBALS['strUpdated']}<br />";

    if ($todaysclosed == 0) echo $GLOBALS['strNoIncidents'];
    elseif ($todaysclosed == 1) echo "<a href='statistics.php'>{$todaysclosed}</a> Incident";
    elseif ($todaysclosed > 1) echo "<a href='statistics.php'>{$todaysclosed}</a> Incidents";
    echo " {$GLOBALS['strClosed']}<br />";

    if ($supportopen == 0) echo $GLOBALS['strNoIncidents'];
    elseif ($supportopen == 1) echo "{$supportopen} Incident";
    elseif ($supportopen > 1) echo "{$supportopen} Incidents";
    echo " {$GLOBALS['strCurrentlyOpen']}<br />";

    if ($kbpublished == 0) echo $GLOBALS['strNoKBArticles'];
    elseif ($kbpublished == 1) echo "<a href='browse_kb.php?mode=today' title='View articles published today'>{$kbpublished}</a> KB Article";
    elseif ($kbpublished > 1) echo "<a href='browse_kb.php?mode=today' title='View articles published today'>{$kbpublished}</a> KB Articles";
    echo " {$GLOBALS['strPublished']}<br />";

    echo "</div>";
    echo "</div>";
}

?>