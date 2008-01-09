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
    //display the form
    echo "<h2>$title</h2>";
    echo "<p align='center'>Triggers Blurb</p>";
    echo "<form name='triggers' action='$_SERVER[PHP_SELF]?mode=save' method='post'>";
    echo "<table align='center'><tr><th>{$strTrigger}</th><th>{$strAction}</th><th>{$strTemplate}</th><th>{$strParameters}</th></tr>";

    //get all triggers for this user
    //TODO 3.40 sort the js to make these editable
    $sql = "SELECT * FROM triggers WHERE userid='{$sit[2]}'";
    $query = mysql_query($sql);
    if ($query)
    {
        while ($trigger = mysql_fetch_object($query))
        {
            echo "<tr><td>{$trigger->triggerid}<br /><em>{$trigger->description}</em></td>\n";
            echo "<td>{$trigger->action}</td>\n";
            echo "<td>{$trigger->template}</td>\n";

            echo "<td>{$trigger->parameters}</td></tr>\n";

        }
    }
    
    //new trigger part
    echo "<tr><td><a href=\"javascript:toggleDiv('hidden')\">{$strAdd}</a></td></tr>\n";
    echo "<tbody id='hidden' class='hidden' style='display:none'><tr><td></td>\n";
    echo "<tr><td>";
    echo triggers_drop_down("new_trigger");
    echo "</td>";
    echo "<td><select name='new_action' id='new_action'>";
    echo "<option value='ACTION_NONE'>None</option>\n";
    echo "<option value='ACTION_EMAIL'>Email</option>\n";
    echo "<option value='ACTION_NOTICE'>Notice</option>\n";
    echo "</select></td>";
    echo "<td>".email_templates("new_email_template")." ".notice_templates("new_notice_template")."</td>";
    echo "<td><input id='new_params' name='new_params' /></td></tr>\n";
    echo "</td></tr>";
    echo "</table>";
    echo "<p align='center'><input type='submit' value='$strSave' /></p>";
    echo "</form>";   include ('htmlfooter.inc.php');
}
else
{
    $newtrigger = cleanvar($_POST['new_trigger']);
    $newaction = cleanvar($_POST['new_action']);
    $newparams = cleanvar($_POST['new_params']);
    if($newaction == "ACTION_EMAIL")
    {
        $newtemplate = cleanvar($_POST['new_email_template']);
    }
    elseif($newaction == "ACTION_NOTICE")
    {
        $newtemplate = cleanvar($_POST['new_notice_template']);
    }

    $sql = "INSERT into `$dbTriggers` (triggerid, userid, action, template, parameters) ";
    $sql .= "VALUES('{$newtrigger}', '{$sit[2]}', '{$newaction}', '{$newtemplate}', '{$newparams}')";
    if(mysql_query($sql))
    {
        html_redirect(TRUE, $_SERVER[PHP_SELF]);
    }
}
?>
