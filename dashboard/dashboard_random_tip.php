<?php

function dashboard_random_tip()
{
    global $i18n_RANDOM_TIP;
    echo "<span id='dragList2'>";
    echo "<div class='windowbox' style='width: 95%'>";
    echo "<div class='windowtitle'>{$i18n_RANDOM_TIP}</div>";
    echo "<div class='window'>";
    echo random_tip();
    echo "</div>";
    echo "</div>";
    echo "</span>";
}

?>