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
    // FIXME i18n db text?
    $sql = "INSERT into tasks(owner, name, priority, distribution, startdate, created, lastupdated) ";
    $sql .= "VALUES('$sit[2]', 'Activity for Incident {$incident}', 1, 'incident', NOW(), NOW(), NOW())";

    mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

    $taskid = mysql_insert_id();

    $sql = "INSERT into links VALUES(4, {$taskid}, {$incident}, 'left', {$sit[2]})";
    mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    html_redirect("tasks.php?incident={$incident}", TRUE, $strActivityAdded);
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
            $taskuser = cleanvar($_REQUEST['taskuser']);
            $starttime = cleanvar($_REQUEST['starttime']);
            $duetime = cleanvar($_REQUEST['duetime']);
            $endtime = cleanvar($_REQUEST['endtime']);

            $_SESSION['formdata'] = $_REQUEST;

            // Validate input
            $errors = 0;
            if ($name=='')
            {
                $_SESSION['formerrors']['name'] = "Incident title must not be blank";
                $errors++;
            }

            if ($startdate > $duedate AND $duedate != '' AND $duedate > 0 ) $startdate=$duedate. " ".$duetime;
            if ($errors != 0)
            {
                include('htmlheader.inc.php');
                html_redirect("add_task.php", FALSE);
            }
            else
            {
                if ($startdate > 0) $startdate = date('Y-m-d',$startdate)." ".$starttime;
                else $startdate = '';
                if ($duedate > 0) $duedate = date('Y-m-d',$duedate)." ".$duetime;
                else $duedate='';
                if ($startdate < 1 AND $completion > 0) $startdate = date('Y-m-d H:i:s')." ".$starttime;
                $sql = "INSERT INTO tasks ";
                $sql .= "(name,description,priority,owner,duedate,startdate,completion,value,distribution,created) ";
                $sql .= "VALUES ('$name','$description','$priority','$taskuser','$duedate','$startdate','$completion','$value','$distribution','".date('Y-m-d H:i:s')."')";
                mysql_query($sql);
                if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                if (mysql_affected_rows() < 1) trigger_error("Task insert failed",E_USER_ERROR);
                $_SESSION['formdata'] = NULL;
                $_SESSION['formerrors'] = NULL;
                html_redirect("tasks.php");
            }
        break;

        case '':
        default:
            include('htmlheader.inc.php');
            echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/task.png' width='32' height='32' alt='' /> ";
            echo "$title</h2>";
            
            echo show_errors();

            //cleanup errors
            $_SESSION['formerrors'] = NULL;
            
            echo "<form id='addtask' action='{$_SERVER['PHP_SELF']}' method='post'>";
            echo "<table class='vertical'>";
            echo "<tr><th>{$strTitle} <sup class='red'>*</sup></th>";
            echo "<td><input type='text' name='name' size='35' maxlength='255'";
            if($_SESSION['formdata']['name'] != '')
                echo "value='{$_SESSION['formdata']['name']}'";
            echo "/></td></tr>";
            
            echo "<tr><th>{$strDescription}</th>";
            echo "<td><textarea name='description' rows='4' cols='30'>";
            if($_SESSION['formdata']['description'] != '')
                echo $_SESSION['formdata']['description'];
            echo "</textarea></td></tr>";
            
            echo "<tr><th>{$strPriority}</th>";
            if($_SESSION['formdata']['priority'] != '')
                echo "<td>".priority_drop_down('priority', $_SESSION['formdata']['priority'])."</td></tr>";
            else
                echo "<td>".priority_drop_down('priority',1)."</td></tr>";
            echo "<tr><th>{$strStartDate}</th>";
            echo "<td><input type='text' name='startdate' id='startdate' size='10'";
            if($_SESSION['formdata']['startdate'] != '')
                echo "value='{$_SESSION['formdata']['startdate']}'";
            echo "/> ";
            echo date_picker('addtask.startdate');
            echo " ".time_dropdown("starttime", date("H:i"));
            echo "</td></tr>";
            
            echo "<tr><th>{$strDueDate}</th>";
            echo "<td><input type='text' name='duedate' id='duedate' size='10'";
            if($_SESSION['formdata']['duedate'] != '')
                echo "value='{$_SESSION['formdata']['duedate']}'";
            echo "/> ";
            echo date_picker('addtask.duedate');
            if($_SESSION['formdata']['duetime'] != '')
                echo " ".time_dropdown("duetime", $_SESSION['formdata']['duetime']);
            else
                echo " ".time_dropdown("duetime");
            echo "</td></tr>";
            
            echo "<tr><th>{$strCompletion}</th>";
            echo "<td><input type='text' name='completion' size='3' maxlength='3'";;
            if($_SESSION['formdata']['completion'] != '')
                echo "value='{$_SESSION['formdata']['completion']}'";
            else
                echo "value='0'";
            echo "/>&#037;</td></tr>";
            //FIXME: should this be available?
            /*echo "<tr><th>{$strEndDate}</th>";
            echo "<td><input type='text' name='enddate' id='enddate' size='10' /> ";
            echo date_picker('addtask.enddate');
            echo " ".time_dropdown("endtime");
            echo "</td></tr>";*/
            echo "<tr><th>{$strValue}</th>";
            echo "<td><input type='text' name='value' size='6' maxlength='12'";
            if($_SESSION['formdata']['value'] != '')
                echo "value='{$_SESSION['formdata']['value']}'";
            echo "/></td></tr>";
            echo "<tr><th>{$strUser}</th>";
            echo "<td>";
            if($_SESSION['formdata']['taskuser'] != '')
                echo user_drop_down('taskuser', $_SESSION['formdata']['taskuser'], FALSE);
            else
                echo user_drop_down('taskuser', $sit[2], FALSE);
            echo "</td></tr>";
            echo "<tr><th>{$strPrivacy}</th>";
            echo "<td>";
            if($_SESSION['formdata']['distribution'] == 'public')
            {
                echo "<input type='radio' name='distribution' checked='checked'value='public' /> {$strPublic}<br />";
                echo "<input type='radio' name='distribution' value='private' /> {$strPrivate} <img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/private.png' width='16' height='16' title='Public/Private' alt='Private' style='border: 0px;' /></td></tr>";
            }
            
            else
            {
                echo "<input type='radio' name='distribution' value='public' /> {$strPublic}<br />";
                echo "<input type='radio' name='distribution' checked='checked' value='private' /> {$strPrivate} <img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/private.png' width='16' height='16' title='Public/Private' alt='Private' style='border: 0px;' /></td></tr>";
            }
            echo "</table>";
            echo "<p><input name='submit' type='submit' value='{$strAddTask}' /></p>";
            echo "<input type='hidden' name='action' value='addtask' />";
            echo "</form>";
            
            //cleanup form vars
            $_SESSION['formdata'] = NULL;
            
            include('htmlfooter.inc.php');
    }
}

?>