<?php

function dashboard_random_tip()
{
    global $i18n_RANDOM_TIP;
    echo "<div class='windowtitle'>{$i18n_RANDOM_TIP}</div>";
    echo "<div class='window'>";
    echo random_tip();
    echo "</div>";
    echo "</div>";
}

?>