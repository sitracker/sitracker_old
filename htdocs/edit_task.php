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
        if (!empty($_REQUEST['duedate'])) $duedate = strtotime($_REQUEST['duedate']);
        else $duedate = '';
        if (!empty($_REQUEST['startdate'])) $startdate = strtotime($_REQUEST['startdate']);
        else $startdate = '';
        $completion = cleanvar(str_replace('%','',$_REQUEST['completion']));
        if ($completion!='' AND !is_numeric($completion)) $completion=0;
        $value = cleanvar($_REQUEST['value']);
        $distribution = cleanvar($_REQUEST['distribution']);
        $old_name = cleanvar($_REQUEST['old_name']);
        $old_description = cleanvar($_REQUEST['old_description']);
        $old_priority = cleanvar($_REQUEST['old_priority']);
        $old_startdate = cleanvar($_REQUEST['old_startdate']);
        $old_duedate = cleanvar($_REQUEST['old_duedate']);
        $old_completion = cleanvar($_REQUEST['old_completion']);
        $old_value = cleanvar($_REQUEST['old_value']);
        $old_distribution = cleanvar($_REQUEST['old_distribution']);

        // Validate input
        $error=array();
        if ($name=='') $error[]='Task name must not be blank';
        if ($startdate > $duedate AND $duedate != '' AND $duedate > 0 ) $startdate=$duedate;
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
            if ($startdate > 0) $startdate = date('Y-m-d',$startdate);
            else $startdate = '';
            if ($duedate > 0) $duedate = date('Y-m-d',$duedate);
            else $duedate='';
            if ($startdate < 1 AND $completion > 0) $startdate = date('Y-m-d H:i:s');
            $sql = "UPDATE tasks ";
            $sql .= "SET name='$name', description='$description', priority='$priority', ";
            $sql .= "duedate='$duedate', startdate='$startdate', ";
            $sql .= "completion='$completion', value='$value', ";
            $sql .= "distribution='$distribution' ";
            $sql .= "WHERE id='$id' LIMIT 1";
            mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
            // if (mysql_affected_rows() < 1) trigger_error("Task update failed",E_USER_ERROR);

            // Add a note to say what changed (if required)
            $bodytext='';
            if ($name != $old_name) $bodytext .= "Name: {$old_name} -&gt; [b]{$name}[/b]\n";
            if ($description != $old_description) $bodytext .= "Description: {$old_description} -&gt; [b]{$description}[/b]\n";
            if ($priority != $old_priority) $bodytext .= "Priority: ".priority_name($old_priority)." -&gt; [b]".priority_name($priority)."[/b]\n";
            $old_startdate = substr($old_startdate,0,10);
            if ($startdate != $old_startdate) $bodytext .= "Start Date: {$old_startdate} -&gt; [b]{$startdate}[/b]\n";
            $old_duedate = substr($old_duedate,0,10);
            if ($duedate != $old_duedate) $bodytext .= "Due Date: {$old_duedate} -&gt; [b]{$duedate}[/b]\n";
            if ($completion != $old_completion) $bodytext .= "Completion: {$old_completion}% -&gt; [b]{$completion}%[/b]\n";
            if ($value != $old_value) $bodytext .= "Value: {$old_value} -&gt; [b]{$value}[/b]\n";
            if ($distribution != $old_distribution) $bodytext .= "Privacy: {$old_distribution} -&gt; [b]{$distribution}[/b]\n";
            if (!empty($bodytext))
            {
                $bodytext="Task Edited by {$_SESSION['realname']}:\n\n".$bodytext;
                // Link 10 = Tasks
                $sql = "INSERT INTO notes ";
                $sql .= "(userid, bodytext, link, refid) ";
                $sql .= "VALUES ('0', '{$bodytext}', '10',' $id')";
                mysql_query($sql);
                if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
            }
            confirmation_page("2", "view_task.php?id={$id}", "<h2>Task edited successfully</h2><p align='center'>Please wait while you are redirected...</p>");
        }
    break;

    case 'markcomplete':
        $sql = "UPDATE tasks ";
        $sql .= "SET completion='100' ";
        $sql .= "WHERE id='$id' LIMIT 1";
        mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        // Insert note to say what happened
        $bodytext="Task marked 100% complete by {$_SESSION['realname']}:\n\n".$bodytext;
        $sql = "INSERT INTO notes ";
        $sql .= "(userid, bodytext, link, refid) ";
        $sql .= "VALUES ('0', '{$bodytext}', '10',' $id')";
        mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        confirmation_page("2", "view_task.php?id={$id}", "<h2>Task marked complete successfully</h2><p align='center'>Please wait while you are redirected...</p>");
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
                $startdate=mysql2date($task->startdate);
                $duedate=mysql2date($task->duedate);
                echo "<form id='edittask' action='{$_SERVER['PHP_SELF']}' method='post'>";
                echo "<table class='vertical'>";
                echo "<tr><th>Title</th>";
                echo "<td><input type='text' name='name' size='35' maxlength='255' value=\"".stripslashes($task->name)."\" /></td></tr>";
                echo "<tr><th>Description</th>";
                echo "<td><textarea name='description' rows='4' cols='30'>".stripslashes($task->description)."</textarea></td></tr>";
                echo "<tr><th>Priority</th>";
                echo "<td>".priority_drop_down('priority',$task->priority)."</td></tr>";
                echo "<tr><th>Start Date</th>";
                echo "<td><input type='text' name='startdate' id='startdate' size='10' value='";
                if ($startdate > 0) echo date('Y-m-d',$startdate);
                echo "' /> ";
                echo date_picker('edittask.startdate');
                echo "</td></tr>";
                echo "<tr><th>Due Date</th>";
                echo "<td><input type='text' name='duedate' id='duedate' size='10' value='";
                if ($duedate > 0) echo date('Y-m-d',$duedate);
                echo "' /> ";
                echo date_picker('edittask.duedate');
                echo "</td></tr>";
                echo "<tr><th>Completion</th>";
                echo "<td><input type='text' name='completion' size='3' maxlength='3' value='{$task->completion}' />&#037;</td></tr>";
                echo "<tr><th>Value</th>";
                echo "<td><input type='text' name='value' size='6' maxlength='12' value='{$task->value}' /></td></tr>";
                echo "<tr><th>Privacy</th>";
                echo "<td>";
                echo "<input type='radio' name='distribution' ";
                if ($task->distribution=='public') echo "checked='checked' ";
                echo "value='public' /> Public<br />";
                echo "<input type='radio' name='distribution' ";
                if ($task->distribution=='private') echo "checked='checked' ";
                echo "value='private' /> Private <img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/apps/password.png' width='16' height='16' title='Private' alt='Private' /></td></tr>";
                echo "</table>";
                echo "<p><input name='submit' type='submit' value='Save' /></p>";
                echo "<input type='hidden' name='action' value='edittask' />";
                echo "<input type='hidden' name='id' value='{$id}' />";
                // Send copy of existing data so we can see when it is changed
                echo "<input type='hidden' name='old_name' value=\"".stripslashes($task->name)."\" />";
                echo "<input type='hidden' name='old_description' value=\"".stripslashes($task->description)."\" />";
                echo "<input type='hidden' name='old_priority' value='{$task->priority}' />";
                echo "<input type='hidden' name='old_startdate' value='{$task->startdate}' />";
                echo "<input type='hidden' name='old_duedate' value='{$task->duedate}' />";
                echo "<input type='hidden' name='old_completion' value='{$task->completion}' />";
                echo "<input type='hidden' name='old_value' value='{$task->value}' />";
                echo "<input type='hidden' name='old_distribution' value='{$task->distribution}' />";
                echo "</form>";
            }
        }
        else echo "<p class='error'>No matching task found</p>";


        echo "<p align='center'><a href='tasks.php'>Tasks List</a></p>";
        include('htmlfooter.inc.php');
}

?>