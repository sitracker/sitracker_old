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

    case 'add':
        include ('htmlheader.inc.php');
        echo "<h2>$title</h2>";
        echo "<h3>Add Action</h3>"; // FIXME i18n add action/new action
        include ('htmlfooter.inc.php');
        break;

    case 'list':
    default:
        //display the list
        $adminuser = user_permission($sit[2],22); // Admin user
        include ('htmlheader.inc.php');
        echo "<h2>$title</h2>";
        echo "<p align='center'>A list of available triggers and the actions that are set when triggers occur</p>"; // TODO triggers blurb
        echo "<table align='center'><tr><th>{$strTrigger}</th><th>{$strActions}</th><th>{$strOperation}</th></tr>\n";

        $shade = 'shade1';
        foreach($triggerarray AS $trigger => $triggervar)
        {
            echo "<tr class='$shade'>";
            echo "<td><strong>";
            if (!empty($triggervar['name'])) echo "{$triggervar['name']}";
            else echo "{$trigger}";
            echo "</strong><br />\n";
            echo $triggervar['description'];
            echo "</td>";
            // List actions for this trigger
            echo "<td>";
            $sql = "SELECT * FROM `{$dbTriggers}` WHERE triggerid = '$trigger' ";
            if (!$adminuser) $sql .= "AND userid='{$sit[2]}'";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
            while ($trigaction = mysql_fetch_object($result))
            {
                echo "&bull; {$trigaction->action}";
                if (!empty($trigaction->checks)) echo " ({$trigaction->checks})";
                echo "<br />\n";
            }
            echo "</td>";
            echo "<td><a href='{$_SERVER['PHP_SELF']}?mode=add&amp;id={$trigger}'>Add Action</a></td>"; // TODO link to add page
            echo "</tr>\n";
            if ($shade == 'shade1') $shade = 'shade2';
            else $shade = 'shade1';
        }
        echo "</table>";
        include ('htmlfooter.inc.php');
}
?>