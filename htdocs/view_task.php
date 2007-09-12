<?php
// view_task.php - Display existing task
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

$permission=0; // Allow all auth users

require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

$title='View Task';

// External variables
$action = $_REQUEST['action'];
$id = cleanvar($_REQUEST['id']);

include('htmlheader.inc.php');
echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/task.png' width='32' height='32' alt='' /> $title</h2>";

echo "<div style='width: 90%; margin-left: auto; margin-right: auto;'>";

$sql = "SELECT * FROM tasks WHERE id='{$id}'";
$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
if (mysql_num_rows($result) >= 1)
{
    $task = mysql_fetch_object($result);
    if ($task->distribution == 'private' AND $task->owner != $sit[2])
    {
        echo "<p class='error'>Sorry, you cannot view this task as it has been marked private and you are not the owner.</p>";
    }
    else
    {
        echo "<div style='width: 48%; float: left;'>";
        $startdate=mysql2date($task->startdate);
        $duedate=mysql2date($task->duedate);
        $enddate=mysql2date($task->enddate);
        echo "<table class='vertical' width='100%'>";
        echo "<tr><th>Title</th>";
        echo "<td>".stripslashes($task->name)."</td></tr>";
        echo "<tr><th>Description</th>";
        echo "<td>".nl2br(stripslashes($task->description))."</td></tr>";
        if ($task->distribution=='public')
        {
            echo "<tr><th>Tags:</th><td>";
            echo list_tags($id, 4);
            echo "</td></tr>";
        }
        if ($task->owner != $sit[2])
        {
            echo "<tr><th>Owner</th>";
            echo "<td>".user_realname($task->owner,TRUE)."</td></tr>";
        }
        echo "<tr><th>Priority</th>";
        echo "<td>".priority_icon($task->priority).' '.priority_name($task->priority)."</td></tr>";
        echo "<tr><th>Start Date</th>";
        echo "<td>";
        if ($startdate > 0) echo date('Y-m-d',$startdate);
        echo "</td></tr>";
        echo "<tr><th>Due Date</th>";
        echo "<td>";
        if ($duedate > 0) echo date('Y-m-d',$duedate);
        echo "</td></tr>";
        echo "<tr><th>Completion</th>";
        echo "<td>".percent_bar($task->completion)."</td></tr>";
        echo "<tr><th>End Date</th>";
        echo "<td>";
        if ($enddate > 0) echo date('Y-m-d',$enddate);
        echo "</td></tr>";
        echo "<tr><th>Value</th>";
        echo "<td>{$task->value}</td></tr>";
        echo "<tr><th>Privacy</th>";
        echo "<td>";
        if ($task->distribution=='public') echo "Public";
        if ($task->distribution=='private') echo "Private <img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/private.png' width='16' height='16' title='Private' alt='Private' />";
        echo "</td></tr>";
        echo "</table>";
        echo "<p align='center'><a href='edit_task.php?id={$id}'>Edit Task</a>";
        if ($task->completion < 100) echo " | <a href='edit_task.php?id={$id}&amp;action=markcomplete'>Mark Complete</a>";
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
        echo add_note_form(10, $id);
        echo show_notes(10, $id);

        echo "</div>";
    }
}
else echo "<p class='error'>No matching task found</p>";

echo "</div>";
echo "<div style='clear:both; padding-top: 20px;'>";
echo "<p align='center'><a href='tasks.php'>Tasks List</a></p>";
echo "</div>";

include('htmlfooter.inc.php');

?>
