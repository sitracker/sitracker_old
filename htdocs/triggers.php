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
// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

@include ('set_include_path.inc.php');
$permission = 0; // TODO 3.40 set a permission for triggers
require ('db_connect.inc.php');
require ('functions.inc.php');
// This page requires authentication
require ('auth.inc.php');

$adminuser = user_permission($sit[2],22); // Admin user

// External vars
// We only allow setting the selecteduser if the user is an admin, otherwise we use self
if ($adminuser)
{
    if (is_numeric($_REQUEST['user'])) $selecteduser = $_REQUEST['user'];
    else $selecteduser = 0;
}
else
{
    $selecteduser = $_SESSION['userid'];
}

$title = $strTriggers;

switch ($_REQUEST['mode'])
{
    case 'delete':
        $id = cleanvar($_GET['id']);
        if (!is_numeric($id)) html_redirect($_SERVER['PHP_SELF'], FALSE);

        // TODO needs a check that the user has permission to delete the trigger

        $sql = "DELETE FROM `{$dbTriggers}` WHERE id = $id LIMIT 1";
        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
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
//                 $('parametersbox').show();
                $('emailtemplatesbox').hide();
                $('journalbox').hide();
            }
            else if ($('new_action').value == 'ACTION_EMAIL')
            {
                $('emailtemplatesbox').show();
//                 $('parametersbox').show();
                $('noticetemplatesbox').hide();
                $('journalbox').hide();
            }
            else if ($('new_action').value == 'ACTION_JOURNAL')
            {
//                 $('parametersbox').show();
                $('journalbox').show();
                $('emailtemplatesbox').hide();
                $('noticetemplatesbox').hide();
            }
            else
            {
                $('noticetemplatesbox').hide();
                $('emailtemplatesbox').hide();
//                 $('parametersbox').hide();
                $('journalbox').hide();
            }
        }
        -->
        </script>
        <?php
        echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/triggeraction.png' width='32' height='32' alt='' /> ";
        echo "{$strTriggerActions}</h2>";
        if (!empty($triggerarray[$id]['name'])) $name = $triggerarray[$id]['name'];
        else $name = $id;
        echo "<h3>".sprintf($strAddActionToXTrigger, $name)."</h3>";
        echo "<p align='center'>{$triggerarray[$id]['description']} (Type: {$triggerarray[$id]['type']})</p>"; // FIXME remove displaying type before release
        if (is_array($triggerarray[$id]['optional']))
        {
            echo "<p align='center'>The following optional parameters may be used: ";
            foreach ($triggerarray[$id]['optional'] AS $param)
            {
                echo "<var>{$param}</var> &nbsp; ";
            }
            echo "</p>";
        }
        echo "<form name='addtrigger' action='{$_SERVER['PHP_SELF']}' method='post'>";
        echo "<table align='center'><tr><th>{$strAction}</th><th>{$strTemplate}</th>";
        // echo "<th>Extra {$strParameters}</th>";
        echo "</tr>\n"; // FIXME extra, rules

        // ACTION_NOTICE is only applicable when a userid is specified or for 'all'
        echo "<tr>";
        echo "<td><select name='new_action' id='new_action' onchange='switch_template();'>";
        echo "<option value='ACTION_NONE'>{$strNone}</option>\n";
        echo "<option value='ACTION_EMAIL'>{$strEmail}</option>\n";
        if ($selecteduser != 0) echo "<option value='ACTION_NOTICE'>{$strNotice}</option>\n";
        echo "<option value='ACTION_JOURNAL'>{$strJournal}</option>\n";
        echo "</select></td>";
        echo "<td>";
        echo "<div id='noticetemplatesbox' style='display:none;'>";
        echo notice_templates('noticetemplate');
        echo "</div>\n";
        echo "<div id='emailtemplatesbox' style='display:none;'>";
        echo email_templates($triggerarray[$id]['type'], 'emailtemplate');
        echo "</div>\n";
        echo "<div id='journalbox' style='display:none;'>{$strNone}</div>";
        echo "</td>";
//         echo "<td><div id='parametersbox' style='display:none;'><input type='text' name='parameters' size='30' /></div></td>";
        echo "</tr>";
        if (!empty($triggerarray[$id]['optional']))
        {
            echo "<tr><td colspan='3'><label>Rules:</label> <textarea cols='30' rows='5' name='rules'></textarea></td></tr>";
        }
        else echo "<tr><td colspan='3'>Rules are not definable for this trigger action<td></tr>";
        echo "</table>\n";
        echo "<input type='hidden' name='mode' value='save' />";
        echo "<input type='hidden' name='id' value='{$id}' />";
        echo "<input type='hidden' name='user' value='{$selecteduser}' />";
        echo "<p><input type='submit' value=\"{$strSave}\" /></p>";
        echo "</form>";

        echo "<p align='center'><a href='{$_SERVER['PHP_SELF']}'>{$strBackToList}</a></p>\n";
        include ('htmlfooter.inc.php');
        break;

    case 'save':
        $id = cleanvar($_POST['id']);
        // Check that this is a defined trigger
        if (!array_key_exists($id, $triggerarray))
        {
            html_redirect($_SERVER['PHP_SELF'], FALSE);
            exit;
        }
        $action = cleanvar($_POST['new_action']);
        $noticetemplate = cleanvar($_POST['noticetemplate']);
        $emailtemplate = cleanvar($_POST['emailtemplate']);
        $parameters = cleanvar($_POST['parameters']);
        $rules = cleanvar($_POST['rules']);

        if ($action == 'ACTION_NOTICE') $templateid = $noticetemplate;
        elseif ($action == 'ACTION_EMAIL') $templateid = $emailtemplate;
        else $templateid = 0;

        $sql = "INSERT INTO `{$dbTriggers}` (triggerid, userid, action, template, parameters, checks) ";
        $sql .= "VALUES ('{$id}', '{$userid}', '{$action}', '{$templateid}', '{$parameters}', '{$rules}')";
        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
        //drop through and list...

    case 'list':
    default:
        //display the list
        include ('htmlheader.inc.php');
        echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/trigger.png' width='32' height='32' alt='' /> ";
        echo "$title</h2>";
        echo "<p align='center'>A list of available triggers and the actions that are set when triggers occur</p>"; // TODO triggers blurb

        if ($adminuser)
        {
            $sql  = "SELECT id, realname FROM `{$dbUsers}` WHERE status > 0 ORDER BY realname ASC";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

            $userarr[-1] = $strAll;
            $userarr[0] = $CONFIG['application_shortname'];

            while ($userobj = mysql_fetch_object($result))
            {
                $userarr[$userobj->id] = $userobj->realname;
            }
            echo "<form action=''>";
            echo "<p>{$strUser}: ".array_drop_down($userarr, 'user', $selecteduser, "onchange=\"window.location.href='{$_SERVER['PHP_SELF']}?user=' + this.options[this.selectedIndex].value;\"")."</p>\n";
            echo "</form>\n";
        }
        else
        {
            // User has no admin rights so force the selection to the current user
            $selecteduser = $sit[2];
        }
        echo "<table align='center'><tr><th>{$strTrigger}</th><th>{$strActions}</th><th>{$strOperation}</th></tr>\n";

        $shade = 'shade1';
        foreach($triggerarray AS $trigger => $triggervar)
        {
            echo "<tr class='$shade'>";
            echo "<td style='vertical-align: top; width: 25%;'>";
            echo trigger_description($triggervar);
            echo "</td>";
            // List actions for this trigger
            echo "<td>";
            $sql = "SELECT * FROM `{$dbTriggers}` WHERE triggerid = '$trigger' ";
            if ($selecteduser > -1) $sql .= "AND userid = {$selecteduser} ";
            $sql .= "ORDER BY action, template";
            if (!$adminuser) $sql .= "AND userid='{$sit[2]}'";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
            if (mysql_num_rows($result) >= 1)
            {
                while ($trigaction = mysql_fetch_object($result))
                {
                    echo triggeraction_description($trigaction, TRUE);

                    echo " <a href='{$_SERVER['PHP_SELF']}?mode=delete&amp;id={$trigaction->id}' title=\"{$strDelete}\">";
                    echo "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/12x12/delete.png' width='12' height='12' alt='' /></a>";
                    echo "<br />\n";
                }
            }
            else
            {
                echo "{$strNone}";
            }
            echo "</td>";
            echo "<td><a href='{$_SERVER['PHP_SELF']}?mode=add&amp;id={$trigger}&amp;user={$selecteduser}'>{$strAdd}</a></td>"; // TODO link to add page
            echo "</tr>\n";
            if ($shade == 'shade1') $shade = 'shade2';
            else $shade = 'shade1';
        }
        echo "</table>";
        echo "<p align='center'><a href='triggertest.php'>Test Triggers</a></p>";
        include ('htmlfooter.inc.php');
}
?>