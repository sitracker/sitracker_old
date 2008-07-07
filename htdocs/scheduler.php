<?php
// scheduler.php - List and allow editing of scheduled actions
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

@include ('set_include_path.inc.php');
$permission = 22; // Admin

require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

// External vars
$id = cleanvar($_REQUEST['id']);


switch ($_REQUEST['mode'])
{
    case 'edit':
        $sql = "SELECT * FROM `{$dbScheduler}` WHERE id = $id LIMIT 1";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
        if (mysql_num_rows($result) > 0)
        {
            $saction = mysql_fetch_object($result);
            include ('htmlheader.inc.php');
            echo "<h2>{$strScheduler}</h2>";
            echo "<form name='scheduleform' action='{$_SERVER['PHP_SELF']}' method='post'>";
            echo "<table class='vertical' width='350'>";
            echo "<tr><th>{$strAction}</th>";
            echo "<td><strong>{$saction->action}</strong><br />{$saction->description}</td></tr>\n";
            echo "<tr><th><label for='status'>{$strStatus}</label>".help_link('SchedulerStatus')."</th>";
            $statuslist = array('enabled' => $strEnabled ,'disabled' => $strDisabled);
            echo "<td>".array_drop_down($statuslist, 'status', $saction->status);
            echo "</td></tr>\n";
            if (!empty($saction->paramslabel))
            {
                echo "<tr><th><label for='params'>{$strParameters}</label>".help_link('SchedulerStatus')."</th>";
                echo "<td>{$saction->paramslabel}: <input type='text' id='params' name='params' value='{$saction->params}' size='15' maxlength='255' />";
                echo "</tr>";
            }
            echo "<tr><th><label for='startdate'>{$strStartDate}</label></th>";
            $startdate = date('Y-m-d',mysql2date($saction->start));
            $starttime = date('H:i',mysql2date($saction->start));
            echo "<td><input type='text' id='startdate' name='startdate' value='{$startdate}' size='10' /> ";
            echo date_picker('scheduleform.startdate');
            echo " <input type='text' id='starttime' name='starttime' value='{$starttime}' size='5' /> ";
            echo "</td></tr>\n";
            echo "<tr><th><label for='enddate'>{$strEndDate}</label></th>";
            if (mysql2date($saction->end) > 0) $enddate = date('Y-m-d',mysql2date($saction->end));
            else $enddate = '';
            if (mysql2date($saction->end) > 0) $endtime = date('H:i',mysql2date($saction->end));
            else $endtime = '';
            echo "<td><input type='text' id='enddate' name='enddate' value='{$enddate}' size='10' /> ";
            echo date_picker('scheduleform.enddate');
            echo " <input type='text' id='endtime' name='endtime' value='{$endtime}' size='5' /> ";
            echo "</td></tr>\n";
            echo "<tr><th><label for='interval'>{$strInterval}</label></th>";
            echo "<td><input type='text' id='interval' name='interval' value='{$saction->interval}' size='5' /> ({$strSeconds})";
            echo "</td></tr>\n";
            echo "</table>";
            echo "<input type='hidden' name='mode' value='save' />";
            echo "<input type='hidden' name='id' value='{$id}' />";
            echo "<p><input type='reset' value=\"{$strReset}\" /> <input type='submit' value=\"{$strSave}\" /></p>";
            echo "</form>";
            echo "<p align='center'><a href='{$_SERVER['PHP_SELF']}'>{$strReturnWithoutSaving}</a></p>";
            include ('htmlfooter.inc.php');
        }
        else
        {
            html_redirect($_SERVER['PHP_SELF'], FALSE);
        }
    break;

    case 'save':
        $start = strtotime($_REQUEST['startdate'].' '.$_REQUEST['starttime']);
        if ($start > 0) $start = date('Y-m-d H:i', $start);
        else $tart = $now;
        $end = strtotime($_REQUEST['enddate'].' '.$_REQUEST['endtime']);
        if ($end > 0) $end = date('Y-m-d H:i', $end);
        else $end = '0000-00-00 00:00';

        $status = cleanvar($_REQUEST['status']);
        $params = cleanvar($_REQUEST['params']);
        $interval = cleanvar($_REQUEST['interval']);
        if ($interval <= 0)
        {
            $status = 'disabled';
            $interval = 0;
        }

        $sql = "UPDATE `{$dbScheduler}` SET `status`='{$status}', `start`='{$start}', `end`='{$end}', `interval`='{$interval}'";
        if ($status = 'enabled')
        {
            $sql .= " , `success` = '1'";
        }
        if (!empty($params))
        {
            $sql .= " , `params` = '{$params}'";
        }
        $sql .= " WHERE `id` = $id LIMIT 1";
        mysql_query($sql);
        if (mysql_error())
        {
            trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
            html_redirect($_SERVER['PHP_SELF'], FALSE);
        }
        else html_redirect($_SERVER['PHP_SELF'], TRUE);
    break;

    case 'list':
    default:
        $refresh = 60;
        include ('htmlheader.inc.php');
        echo "<h2>{$strScheduler}</h2>";
        echo "<h3>".ldate($CONFIG['dateformat_datetime'], $GLOBALS['now'], FALSE)."</h3>";
        $sql = "SELECT * FROM `{$dbScheduler}` ORDER BY action";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);

        if (mysql_num_rows($result) >= 1)
        {
            echo "<table align='center'>";
            echo "<tr><th>{$strAction}</th><th>{$strStartDate}</th><th>{$strInterval}</th>";
            echo "<th>{$strEndDate}</th><th>{$strLastRan}</th><th>Next Run</th></tr>\n";
            $shade = 'shade1';
            while ($schedule = mysql_fetch_object($result))
            {
                $lastruntime = mysql2date($schedule->lastran);
                if ($schedule->success == 0) $shade = 'critical';
                elseif ($schedule->status == 'disabled') $shade = 'expired';
                elseif ($lastruntime > 0 AND $lastruntime + $schedule->interval < $now) $shade = 'notice';
                echo "<tr class='{$shade}'>";
                echo "<td><a class='info' href='{$_SERVER['PHP_SELF']}?mode=edit&amp;id={$schedule->id}'>{$schedule->action}";
                echo "<span>";
                echo "{$schedule->description}";
                if (!empty($schedule->params)) echo "\n<br /><strong>{$schedule->paramslabel} = {$schedule->params}</strong>";
                echo "</span></a></td>";
                echo "<td>{$schedule->start}</td>";
                echo "<td>".format_seconds($schedule->interval)."</td>";
                echo "<td>";
                if (mysql2date($schedule->end) > 0) echo "{$schedule->end}";
                else echo "-";
                echo "</td>";
                echo "<td>";
                $lastruntime = mysql2date($schedule->lastran);
                if ($lastruntime > 0) echo ldate($CONFIG['dateformat_datetime'], $lastruntime);
                else echo $strNever;
                echo "</td>";
                echo "<td>";
                if ($schedule->status == 'enabled')
                {
                    if ($lastruntime > 0) $nextruntime = $lastruntime + $schedule->interval;
                    else $nextruntime = $now;;
                    echo ldate($CONFIG['dateformat_datetime'],$nextruntime);
                }
                else echo $strNever;
                echo "</td>";
                echo "</tr>";
                if ($shade == 'shade1') $shade = 'shade2';
                else $shade = 'shade1';
            }
            echo "</table>\n";
            echo "<p align='center'>".help_link('Scheduler')."</p>";

            // TODO add a check to see if any of the above actions are long overdue, if they are
            // print a message explaining how to set up cron/scheduling
        }

        include ('htmlfooter.inc.php');
}

?>