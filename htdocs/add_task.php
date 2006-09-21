<?php
// add_task.php - Add a new task
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

$title='Add Task';

// External variables
$action = $_REQUEST['action'];

switch ($action)
{
    case 'addtask':
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
            $sql = "INSERT INTO tasks ";
            $sql .= "(name,description,priority,owner,duedate,startdate,completion,value,distribution,created) ";
            $sql .= "VALUES ('$name','$description','$priority','{$sit[2]}','$duedate','$startdate','$completion','$value','$distribution','".date('Y-m-d')."')";
            mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
            if (mysql_affected_rows() < 1) trigger_error("Task insert failed",E_USER_ERROR);
            confirmation_page("2", "tasks.php", "<h2>Task added successfully</h2><p align='center'>Please wait while you are redirected...</p>");
        }
    break;

    case '':
    default:
        include('htmlheader.inc.php');
        echo "<h2>$title</h2>";
        echo "<form id='addtask' action='{$_SERVER['PHP_SELF']}' method='post'>";
        echo "<table class='vertical'>";
        echo "<tr><th>Title</th>";
        echo "<td><input type='text' name='name' size='35' maxlength='255' /></tr>";
        echo "<tr><th>Description</th>";
        echo "<td><textarea name='description' rows='4' cols='30'></textarea></tr>";
        echo "<tr><th>Priority</th>";
        echo "<td>".priority_drop_down('priority',1)."</tr>";
        echo "<tr><th>Start Date</th>";
        echo "<td><input type='text' name='startdate' id='startdate' size='10' /> ";
        echo date_picker('addtask.startdate');
        echo "</td></tr>";
        echo "<tr><th>Due Date</th>";
        echo "<td><input type='text' name='duedate' id='duedate' size='10' /> ";
        echo date_picker('addtask.duedate');
        echo "</td></tr>";
        echo "<tr><th>Completion</th>";
        echo "<td><input type='text' name='completion' size='3' maxlength='3' value='0' />&#037;</tr>";
        echo "<tr><th>Value</th>";
        echo "<td><input type='text' name='value' size='6' maxlength='12' /></tr>";
        echo "<tr><th>Privacy</th>";
        echo "<td><input type='radio' name='distribution' value='public' /> Public<br />";
        echo "<input type='radio' name='distribution' checked='checked' value='private' /> Private</tr>";
        echo "</table>";
        echo "<p><input name='submit' type='submit' value='Add Task' /></p>";
        echo "<input type='hidden' name='action' value='addtask' />";
        echo "</form>";
        include('htmlfooter.inc.php');
}

?>