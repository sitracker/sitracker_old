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
        $id = cleanvar($_GET['id']);
        // Check that this is a defined trigger
        if (!array_key_exists($id, $triggerarray))
        {
            html_redirect($_SERVER['PHP_SELF'], FALSE);
            exit;
        }

        include ('htmlheader.inc.php');
        ?>
        <script type="text/javascript">
        <!--
        function switch_template()
        {
            if ($('new_action').value == 'ACTION_NOTICE')
            {
                $('noticetemplatesbox').show();
                $('parametersbox').show();
                $('emailtemplatesbox').hide();
                $('journalbox').hide();
            }
            else if ($('new_action').value == 'ACTION_EMAIL')
            {
                $('emailtemplatesbox').show();
                $('parametersbox').show();
                $('noticetemplatesbox').hide();
                $('journalbox').hide();
            }
            else if ($('new_action').value == 'ACTION_JOURNAL')
            {
                $('parametersbox').show();
                $('journalbox').show();
                $('emailtemplatesbox').hide();
                $('noticetemplatesbox').hide();
            }
            else
            {
                $('noticetemplatesbox').hide();
                $('emailtemplatesbox').hide();
                $('parametersbox').hide();
                $('journalbox').hide();
            }
        }
        -->
        </script>
        <?php
        echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/triggeraction.png' width='32' height='32' alt='' /> ";
        echo "$title</h2>";
        if (!empty($triggerarray[$id]['name'])) $name = $triggerarray[$id]['name'];
        else $name = $id;
        echo "<h3>Add Action to '{$name}' trigger</h3>"; // FIXME i18n add action/new action

        echo "<table align='center'><tr><th>{$strAction}</th><th>{$strTemplate}</th><th>{$strParameters}</th></tr>\n";
        echo "<tr>";
        echo "<td><select name='new_action' id='new_action' onchange='switch_template();'>";
        echo "<option value='ACTION_NONE'>{$strNone}</option>\n";
        echo "<option value='ACTION_EMAIL'>{$strEmail}</option>\n";
        echo "<option value='ACTION_NOTICE'>{$strNotice}</option>\n";
        echo "<option value='ACTION_JOURNAL'>{$strJournal}</option>\n";
        echo "</select></td>";
        echo "<td>";
        echo "<div id='noticetemplatesbox' style='display:none;'>";
        echo notice_templates('noticetemplate');
        echo "</div>\n";
        echo "<div id='emailtemplatesbox' style='display:none;'>";
        echo email_templates('emailtemplate');
        echo "</div>\n";
        echo "<div id='journalbox' style='display:none;'>{$strNone}</div>";
        echo "</td>";
        echo "<td><div id='parametersbox' style='display:none;'><input type='text' name='parameters' size='30' /></div></td>";
        echo "</table>\n";

        echo "<p align='center'><a href='{$_SERVER['PHP_SELF']}'>{$strBackToList}</a></p>\n";
        include ('htmlfooter.inc.php');
        break;

    case 'list':
    default:
        //display the list
        $adminuser = user_permission($sit[2],22); // Admin user
        include ('htmlheader.inc.php');
        echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/trigger.png' width='32' height='32' alt='' /> ";
        echo "$title</h2>";
        echo "<p align='center'>A list of available triggers and the actions that are set when triggers occur</p>"; // TODO triggers blurb
        echo "<table align='center'><tr><th>{$strTrigger}</th><th>{$strActions}</th><th>{$strOperation}</th></tr>\n";

        $shade = 'shade1';
        foreach($triggerarray AS $trigger => $triggervar)
        {
            echo "<tr class='$shade'>";
            echo "<td><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/trigger.png' width='16' height='16' alt='' /> ";
            echo "<strong>";
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
            if (mysql_num_rows($result) >= 1)
            {
                while ($trigaction = mysql_fetch_object($result))
                {
                    echo "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/triggeraction.png' width='16' height='16' alt='' /> {$trigaction->action}";
                    if (!empty($trigaction->checks)) echo " ({$trigaction->checks})";
                    echo "<br />\n";
                }
            }
            else
            {
                echo "{$strNone}";
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