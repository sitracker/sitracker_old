<?php

function dashboard_statistics()
{
    global $todayrecent;
    global $i18n_TODAYS_STATS;
    global $i18n_NO_INCIDENTS;
    global $i18n_LOGGED;
    global $i18n_UPDATED;
    global $i18n_CLOSED;
    global $i18n_CURRENTLY_OPEN;
    global $i18n_PUBLISHED;
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


    echo "<div class='windowbox' style='width: 95%'>";
    echo "<div class='windowtitle'><a href='statistics.php'>{$i18n_TODAYS_STATS}</a></div>";
    echo "<div class='window'>";
    if ($todaysincidents == 0) echo $i18n_NO_INCIDENTS;
    elseif ($todaysincidents == 1) echo "<a href='statistics.php'>{$todaysincidents}</a> Incident";
    elseif ($todaysincidents > 1) echo "<a href='statistics.php'>{$todaysincidents}</a> Incidents";
    echo " {$i18n_LOGGED}<br />";

    if ($todaysupdated == 0) echo $i18n_NO_INCIDENTS;
    elseif ($todaysupdated == 1) echo "{$todaysupdated} Incident";
    elseif ($todaysupdated > 1) echo "{$todaysupdated} Incidents";
    echo " {$i18n_UPDATED}<br />";

    if ($todaysclosed == 0) echo $i18n_NO_INCIDENTS;
    elseif ($todaysclosed == 1) echo "<a href='statistics.php'>{$todaysclosed}</a> Incident";
    elseif ($todaysclosed > 1) echo "<a href='statistics.php'>{$todaysclosed}</a> Incidents";
    echo " {$i18n_CLOSED}<br />";

    if ($supportopen == 0) echo $i18n_NO_INCIDENTS;
    elseif ($supportopen == 1) echo "{$supportopen} Incident";
    elseif ($supportopen > 1) echo "{$supportopen} Incidents";
    echo " {$i18n_CURRENTLY_OPEN}<br />";

    if ($kbpublished == 0) echo $i18n_NO_KB_ARTICLES;
    elseif ($kbpublished == 1) echo "<a href='browse_kb.php?mode=today' title='View articles published today'>{$kbpublished}</a> KB Article";
    elseif ($kbpublished > 1) echo "<a href='browse_kb.php?mode=today' title='View articles published today'>{$kbpublished}</a> KB Articles";
    echo " {$i18n_PUBLISHED}<br />";

    echo "</div>";
    echo "</div>";
}

?>