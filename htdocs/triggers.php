<?php
// triggers.php - Page for setting user trigger preferences
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Kieran Hogg <kieran_hogg[at]users.sourceforge.net>

@include ('set_include_path.inc.php');
$permission = 0;
require ('db_connect.inc.php');
require ('functions.inc.php');
require ('triggers.inc.php');

// This page requires authentication
require ('auth.inc.php');

$mode = $_GET['mode'];
$title = $strTriggers;
include ('htmlheader.inc.php');

if ($mode != "save")
{
    echo "<h2>$title</h2>";
    echo "<p align='center'>Triggers Blurb</p>";
    echo "<form action='$_SERVER[PHP_SELF]?mode=save' method='post'>";
    echo "<table align='center'><tr><th>{$strTrigger}</th><th>{$strAction}</th><th>{$strParameters}</th></tr>";

    foreach ($triggerarray as $trigger)
    {
        $sql = "SELECT * FROM triggers WHERE userid={$sit[2]} AND triggerid=".constant($trigger['id']);
        $query = mysql_query($sql);
        $result = mysql_fetch_object($query);
        $resultaction = $result->action;
        echo "<tr><td>{$trigger['id']}<br /><em>{$trigger['description']}</em></td><td>\n";
        echo "<select id='select-{$trigger['id']}'>\n";
        foreach ($actionarray as $action)
        {
            echo "<option value='".constant($action)."'";
            if ($resultaction == constant($action))
            {
                echo "selected='selected' ";
            }
            echo ">$action</option>\n";
        }
        echo "</td><td>";
        echo "<input id='input-{$trigger['id']}' /></td></tr>\n";
    }
    echo "<tr><td><a href=\"javascript:toggleDiv('hidden')\">{$strAdd}</a></td></tr>\n";
    echo "<tbody id='hidden' class='hidden' style='display:none'><tr><td><select>";
    foreach ($triggerarray as $trigger)
    {
        echo "<option>{$trigger['id']}</option>";
    }
    echo "</select></td>";
    echo "<td><select>";
    foreach ($actionarray as $action)
    {
        echo "<option value='".constant($action)."' ";
        if ($resultaction == constant($action))
        {
            echo "selected='selected'";
        }
        echo ">$action</option>\n";
    }
    echo "</td><td><input /></td></tr>\n";
    echo "</td></tr>";
    echo "</table>";
    echo "<p align='center'><input type='submit' value='$strSave' /></p>";
    echo "</form>";
    include ('htmlfooter.inc.php');
}
else
{

}
?>
