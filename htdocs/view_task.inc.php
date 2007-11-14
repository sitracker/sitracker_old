<?php
// view_task.inc.php - Display existing task
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Authors: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
//          Kieran Hogg <kieran_hogg[at]users.sourceforge.net>
// included by view_task.php

echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/task.png' width='32' height='32' alt='' /> $title</h2>";

if($mode != 'incident') echo "<div style='width: 90%; margin-left: auto; margin-right: auto;'>";

$sql = "SELECT * FROM tasks WHERE id='{$taskid}'";
$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
if (mysql_num_rows($result) >= 1)
{
    $task = mysql_fetch_object($result);
    if ($task->distribution == 'private' AND $task->owner != $sit[2])
    {
        echo "<p class='error'>{$strTaskPrivateError}</p>";
    }
    elseif($mode != 'incident')
    {
        echo "<div style='width: 48%; float: left;'>";
        $startdate=mysql2date($task->startdate);
        $duedate=mysql2date($task->duedate);
        $enddate=mysql2date($task->enddate);
        echo "<table class='vertical' width='100%'>";
        echo "<tr><th>{$strTitle}</th>";
        echo "<td>".stripslashes($task->name)."</td></tr>";
        echo "<tr><th>{$strDescription}</th>";
        echo "<td>".nl2br(stripslashes($task->description))."</td></tr>";
        if ($task->distribution=='public')
        {
            echo "<tr><th>{$strTags}:</th><td>";
            echo list_tags($taskid, 4);
            echo "</td></tr>";
        }
        if ($task->owner != $sit[2])
        {
            echo "<tr><th>{$strOwner}</th>";
            echo "<td>".user_realname($task->owner,TRUE)."</td></tr>";
        }
        echo "<tr><th>{$strPriority}</th>";
        echo "<td>".priority_icon($task->priority).' '.priority_name($task->priority)."</td></tr>";
        echo "<tr><th>{$strStartDate}</th>";
        echo "<td>";
        if ($startdate > 0) echo date('Y-m-d',$startdate);
        echo "</td></tr>";
        echo "<tr><th>{$strDueDate}</th>";
        echo "<td>";
        if ($duedate > 0) echo date('Y-m-d',$duedate);
        echo "</td></tr>";
        echo "<tr><th>{$strCompletion}</th>";
        echo "<td>".percent_bar($task->completion)."</td></tr>";
        echo "<tr><th>{$strEndDate}</th>";
        echo "<td>";
        if ($enddate > 0) echo date('Y-m-d',$enddate);
        echo "</td></tr>";
        echo "<tr><th>{$strValue}</th>";
        echo "<td>{$task->value}</td></tr>";
        echo "<tr><th>{$strPrivacy}</th>";
        echo "<td>";
        if ($task->distribution=='public') echo $strPublic;
        if ($task->distribution=='private') echo "{$strPrivate} <img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/private.png' width='16' height='16' title='{$strPrivate}' alt='{$strPrivate}' />";
        echo "</td></tr>";
        echo "</table>";
        echo "<p align='center'><a href='edit_task.php?id={$taskid}'>{$strEditTask}</a>";
        if ($task->owner == $sit[2] AND $task->completion==100) echo " | <a href='edit_task.php?id={$taskid}&amp;action=delete'>{$strDeleteTask}</a>";
        if ($task->completion < 100) echo " | <a href='edit_task.php?id={$taskid}&amp;action=markcomplete'>{$strMarkComplete}</a>";
        echo "</p>";

/*
        // Temporarily disabled for 3.30 beta1 release

        echo "<div style='border: 1px solid #CCCCFF; padding: 5px;'>";
        echo "<p><strong>Links</strong>:</p>";
        // Draw links tree
        // Have a look what can be linked from tasks
        echo show_links('tasks', $task->id);

        echo "<p><strong>Reverse Links</strong>:</p>";
        echo show_links('tasks', $task->id, 0, '', 'rl');

        echo "</div>";

        echo show_create_links('tasks', $task->id);
        */

        echo "</div>";
        // Notes
                
        echo "<div style='width: 48%; float: right; border: 1px solid #CCCCFF;'>";
        echo add_note_form(10, $taskid);
        echo show_notes(10, $taskid);

        echo "</div>";
    }
    elseif($mode == 'incident')
    {
        echo "<div style='width: 48%; margin-left: auto; margin-right: auto;border: 1px solid #CCCCFF;'>";
        echo add_note_form(10, $taskid);
        echo show_notes(10, $taskid);

        echo "</div>";
    }
}
else echo "<p class='error'>{$strNoMatchingTask}</p>";

if($mode != 'incident')echo "</div>";
echo "<div style='clear:both; padding-top: 20px;'>";

if($mode != 'incident') echo "<p align='center'><a href='tasks.php'>{$strTaskList}</a></p>";
else echo "<p align='center'><a href=edit_task.php?id={$taskid}&amp;action=markcomplete&amp;incident={$incidentid}>{$strMarkComplete}</a> | <a href='tasks.php?incident={$id}'>{$strActivityList}</a></p>";
echo "</div>";

?>