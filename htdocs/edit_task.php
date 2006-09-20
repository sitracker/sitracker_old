<?php
// exit_task.php - Edit existing task
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
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

$title='Edit Task';

// External variables
$action = $_REQUEST['action'];
$id = cleanvar($_REQUEST['id']);

switch ($action)
{
    case 'edittask':
        // External variables
        $name = cleanvar($_REQUEST['name']);
        $description = cleanvar($_REQUEST['description']);
        $priority = cleanvar($_REQUEST['priority']);
        if (!empty($_REQUEST['duedate'])) $duedate = date('Y-m-d',strtotime($_REQUEST['duedate']));
        else $duedate = '';
        if (!empty($_REQUEST['startdate'])) $startdate = date('Y-m-d',strtotime($_REQUEST['startdate']));
        else $startdate = '';
        $completion = cleanvar($_REQUEST['completion']);
        $value = cleanvar($_REQUEST['value']);
        $distribution = cleanvar($_REQUEST['distribution']);

        // Validate input
        $error=array();
        if ($name=='') $error[]='Task name must not be blank';
        if (count($error) >= 1)
        {
            include('htmlheader.inc.php');
            echo "<p class='error'>Please check the data you entered</p>";
            echo "<ul class='error'>";
            foreach ($error AS $err)
            {
                echo "<li>$err</li>";
            }
            echo "</ul>";
            include('htmlfooter.inc.php');
        }
        else
        {
            $sql = "UPDATE tasks ";
            $sql .= "SET name='$name', description='$description', priority='$priority', ";
            $sql .= "duedate='$duedate', startdate='$startdate', ";
            $sql .= "completion='$completion', value='$value', ";
            $sql .= "distribution='$distribution' ";
            $sql .= "WHERE id='$id' LIMIT 1";
            mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
            if (mysql_affected_rows() < 1) trigger_error("Task update failed",E_USER_ERROR);
            confirmation_page("2", "tasks.php", "<h2>Task edited successfully</h2><p align='center'>Please wait while you are redirected...</p>");
        }
    break;

    case '':
    default:
        include('htmlheader.inc.php');
        echo "<h2>$title</h2>";
        $sql = "SELECT * FROM tasks WHERE id='$id'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        if (mysql_num_rows($result) >= 1)
        {
            while ($task = mysql_fetch_object($result))
            {
                echo "<form id='edittask' action='{$_SERVER['PHP_SELF']}' method='post'>";
                echo "<table class='vertical'>";
                echo "<tr><th>Title</th>";
                echo "<td><input type='text' name='name' size='35' maxlength='255' value='{$task->name}' /></tr>";
                echo "<tr><th>Description</th>";
                echo "<td><textarea name='description' rows='4' cols='30'>{$task->description}</textarea></tr>";
                echo "<tr><th>Priority</th>";
                echo "<td>".priority_drop_down('priority',$task->priority)."</tr>";
                echo "<tr><th>Start Date</th>";
                echo "<td><input type='text' name='startdate' id='startdate' size='10' value='".date('Y-m-d',mysql2date($task->startdate))."' /> ";
                echo date_picker('edittask.startdate');
                echo "</td></tr>";
                echo "<tr><th>Due Date</th>";
                echo "<td><input type='text' name='duedate' id='duedate' size='10' value='".date('Y-m-d',mysql2date($task->duedate))."' /> ";
                echo date_picker('edittask.duedate');
                echo "</td></tr>";
                echo "<tr><th>Completion</th>";
                echo "<td><input type='text' name='completion' size='3' maxlength='3' value='{$task->completion}' />&#037;</tr>";
                echo "<tr><th>Value</th>";
                echo "<td><input type='text' name='value' size='6' maxlength='12' value='{$task->value}' /></tr>";
                echo "<tr><th>Privacy</th>";
                echo "<td>";
                echo "<input type='radio' name='distribution' ";
                if ($task->distribution=='public') echo "checked='checked' ";
                echo "value='public' /> Public<br />";
                echo "<input type='radio' name='distribution' ";
                if ($task->distribution=='private') echo "checked='checked' ";
                echo "value='private' /> Private</tr>";
                echo "</table>";
                echo "<p><input name='submit' type='submit' value='Edit Task' /></p>";
                echo "<input type='hidden' name='action' value='edittask' />";
                echo "<input type='hidden' name='id' value='{$id}' />";
                echo "</form>";
            }
        }
        else echo "<p class='error'>No matching task found</p>";
        include('htmlfooter.inc.php');
}

?>