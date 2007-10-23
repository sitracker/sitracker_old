<?php
// add_task.php - Add a new task
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Authors: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
//          Kieran Hogg <kieran_hogg[at]users.sourceforge.net>

$permission=0; // Allow all auth users

require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

$title = $strAddTask;

// External variables
$action = $_REQUEST['action'];
$incident = $_REQUEST['incident'];

if($incident)
{
    $sql = "INSERT into tasks(owner, priority, startdate, created, lastupdated) VALUES('$sit[2]', 1, NOW(), NOW(), NOW())";
    mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    
    $taskid = mysql_insert_id();
    
    $sql = "INSERT into links VALUES(4, {$taskid}, {$incident}, 'left', {$sit[2]})";
    mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    confirmation_page(2, "tasks.php?incident=".$incident, "<h2>{$strTask} {$strAdded}</h2><p align='center'>{$strPleaseWaitRedirect}...</p>");

}    


else
{
    switch ($action)
    {
        case 'addtask':
            // External variables
            $name = cleanvar($_REQUEST['name']);
            $description = cleanvar($_REQUEST['description']);
            $priority = cleanvar($_REQUEST['priority']);
            if (!empty($_REQUEST['duedate'])) $duedate = strtotime($_REQUEST['duedate']);
            else $duedate = '';
            if (!empty($_REQUEST['startdate'])) $startdate = strtotime($_REQUEST['startdate']);
            else $startdate = '';
            $completion = cleanvar($_REQUEST['completion']);
            $value = cleanvar($_REQUEST['value']);
            $distribution = cleanvar($_REQUEST['distribution']);
    
            // Validate input
            $error=array();
            if ($name=='') $error[]='Task name must not be blank';
            if ($startdate > $duedate AND $duedate != '' AND $duedate > 0 ) $startdate=$duedate;
            if (count($error) >= 1)
            {
                include('htmlheader.inc.php');
                echo "<p class='error'>$strPleaseCheckData</p>";
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
                $sql = "INSERT INTO tasks ";
                $sql .= "(name,description,priority,owner,duedate,startdate,completion,value,distribution,created) ";
                $sql .= "VALUES ('$name','$description','$priority','{$sit[2]}','$duedate','$startdate','$completion','$value','$distribution','".date('Y-m-d H:i:s')."')";
                mysql_query($sql);
                if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                if (mysql_affected_rows() < 1) trigger_error("Task insert failed",E_USER_ERROR);
                confirmation_page("2", "tasks.php", "<h2>Task added successfully</h2><p align='center'>{$strPleaseWaitRedirect}...</p>");
            }
        break;
    
        case '':
        default:
            include('htmlheader.inc.php');
            echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/task.png' width='32' height='32' alt='' /> ";
            echo "$title</h2>";
            echo "<form id='addtask' action='{$_SERVER['PHP_SELF']}' method='post'>";
            echo "<table class='vertical'>";
            echo "<tr><th>{$strTitle}</th>";
            echo "<td><input type='text' name='name' size='35' maxlength='255' /></td></tr>";
            echo "<tr><th>{$strDescription}</th>";
            echo "<td><textarea name='description' rows='4' cols='30'></textarea></td></tr>";
            echo "<tr><th>{$strPriority}</th>";
            echo "<td>".priority_drop_down('priority',1)."</td></tr>";
            echo "<tr><th>{$strStartDate}</th>";
            echo "<td><input type='text' name='startdate' id='startdate' size='10' /> ";
            echo date_picker('addtask.startdate');
            echo "</td></tr>";
            echo "<tr><th>{$strDueDate}</th>";
            echo "<td><input type='text' name='duedate' id='duedate' size='10' /> ";
            echo date_picker('addtask.duedate');
            echo "</td></tr>";
            echo "<tr><th>{$strCompletion}</th>";
            echo "<td><input type='text' name='completion' size='3' maxlength='3' value='0' />&#037;</td></tr>";
            echo "<tr><th>{$strEndDate}</th>";
            echo "<td><input type='text' name='enddate' id='enddate' size='10' /> ";
            echo date_picker('addtask.enddate');
            echo "</td></tr>";
            echo "<tr><th>{$strValue}</th>";
            echo "<td><input type='text' name='value' size='6' maxlength='12' /></td></tr>";
            echo "<tr><th>{$strPrivacy}</th>";
            echo "<td><input type='radio' name='distribution' value='public' /> {$strPublic}<br />";
            echo "<input type='radio' name='distribution' checked='checked' value='private' /> {$strPrivate} </td></tr>";
            echo "</table>";
            echo "<p><input name='submit' type='submit' value='{$strAddTask}' /></p>";
            echo "<input type='hidden' name='action' value='addtask' />";
            echo "</form>";
            include('htmlfooter.inc.php');
    }
}

?>