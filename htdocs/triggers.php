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
$permission = 22; // TODO 3.40 set a permission for triggers
require ('db_connect.inc.php');
require ('functions.inc.php');
// This page requires authentication
require ('auth.inc.php');

$title = $strTriggers;

switch ($_REQUEST['mode'])
{
    case 'save':
        $newtrigger = cleanvar($_POST['new_trigger']);
        $newaction = cleanvar($_POST['new_action']);
        $newparams = cleanvar($_POST['new_params']);
        if ($newaction == "ACTION_EMAIL")
        {
            $newtemplate = cleanvar($_POST['new_email_template']);
        }
        elseif ($newaction == "ACTION_NOTICE")
        {
            $newtemplate = cleanvar($_POST['new_notice_template']);
        }

        $sql = "INSERT into `{$dbTriggers}` (triggerid, userid, action, template, parameters) ";
        $sql .= "VALUES ('{$newtrigger}', '{$sit[2]}', '{$newaction}', '{$newtemplate}', '{$newparams}')";
        if (mysql_query($sql))
        {
            html_redirect($_SERVER[PHP_SELF], TRUE);
        }
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        break;

    case 'delete':
        $id = cleanvar($_GET['id']);
        if (!is_numeric($id)) html_redirect($_SERVER['PHP_SELF'], FALSE);

        $sql = "DELETE FROM `{$dbTriggers}` WHERE triggerid = $id LIMIT 1";
        mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        if (mysql_affected_rows() >= 1) html_redirect($_SERVER['PHP_SELF']);
        else html_redirect($_SERVER['PHP_SELF'], FALSE);
        break;

    case 'form':
    default:
        //display the form
        include ('htmlheader.inc.php');
        echo "<h2>$title</h2>";
        echo "<p align='center'>Triggers Blurb</p>"; // TODO triggers blurb
        echo "<form name='triggers' action='{$_SERVER[PHP_SELF]}?mode=save' method='post'>";
        echo "<table align='center'><tr><th>{$strTrigger}</th><th>{$strAction}</th><th>{$strTemplate}</th>";
        echo "<th>{$strParameters}</th><th>{$strOperation}</th></tr>";

        //get all triggers for this user
        $sql = "SELECT * FROM triggers WHERE userid='{$sit[2]}'";
        $query = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        if ($query)
        {
            while ($trigger = mysql_fetch_object($query))
            {
                echo "<tr><td>{$trigger->triggerid}<br /><em>{$trigger->description}</em></td>\n";
                echo "<td>{$trigger->action}</td>\n";
                echo "<td>{$trigger->template}</td>\n";

                echo "<td>{$trigger->parameters}</td>";
                echo "<td>{$strEdit} | <a href='{$_SERVER['PHP_SELF']}?mode=delete&amp;id={$trigger->triggerid}'>{$strDelete}</a></td>";
                echo "</tr>\n";
            }
        }

        //new trigger part
        echo "<tr><td><a href=\"javascript:toggleDiv('hidden')\">{$strAdd}</a></td></tr>\n";
        echo "<tbody id='hidden' class='hidden' style='display:none'><tr><td></td>\n";
        echo "<tr><td>";
        echo triggers_drop_down("new_trigger");
        echo "</td>";
        echo "<td><select name='new_action' id='new_action'>";
        echo "<option value='ACTION_NONE'>{$strNone}</option>\n";
        echo "<option value='ACTION_EMAIL'>{$strEmail}</option>\n";
        echo "<option value='ACTION_NOTICE'>{$strNotice}</option>\n";
        echo "</select></td>";
        echo "<td>".email_templates("new_email_template")." ".notice_templates("new_notice_template")."</td>";
        echo "<td><input id='new_params' name='new_params' /></td></tr>\n";
        echo "</td></tr>";
        echo "</table>";
        echo "<p align='center'><input type='submit' value='$strSave' /></p>";
        echo "</form>";
        include ('htmlfooter.inc.php');
}
?>
