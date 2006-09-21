<?php
// tasks.php - List tasks
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

$title='Tasks';

// This page requires authentication
require('auth.inc.php');

// External variables
$user = cleanvar($_REQUEST['user']);
$sort = cleanvar($_REQUEST['sort']);

// Defaults
if (empty($user)) $user=$sit[2];

include('htmlheader.inc.php');

echo "<h2>".user_realname($user) . "'s Tasks:</h2>";


$sql = "SELECT * FROM tasks WHERE owner='$user' ";
if ($sort=='id') $sql .= "ORDER BY id ASC";
elseif ($sort=='name') $sql .= "ORDER BY name ASC";
elseif ($sort=='priority') $sql .= "ORDER BY priority DESC";
elseif ($sort=='completion') $sql .= "ORDER BY completion ASC";
elseif ($sort=='startdate') $sql .= "ORDER BY startdate ASC";
elseif ($sort=='duedate') $sql .= "ORDER BY duedate ASC";

$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
if (mysql_num_rows($result) >=1 )
{
    echo "<table align='center'>";
    echo "<tr><th><a href='{$_SERVER['PHP_SELF']}?sort=id'>ID</a></th>";
    echo "<th><a href='{$_SERVER['PHP_SELF']}?sort=name'>Task</a></th>";
    echo "<th><a href='{$_SERVER['PHP_SELF']}?sort=priority'>Priority</a></th>";
    echo "<th><a href='{$_SERVER['PHP_SELF']}?sort=completion'>Completion</a></th>";
    echo "<th><a href='{$_SERVER['PHP_SELF']}?sort=startdate'>Start Date</a></th>";
    echo "<th><a href='{$_SERVER['PHP_SELF']}?sort=duedate'>Due Date</a></th>";
    echo "</tr>\n";
    $shade='shade1';
    while ($task = mysql_fetch_object($result))
    {
        $duedate = mysql2date($task->duedate);
        $startdate = mysql2date($task->startdate);
        echo "<tr class='$shade'>";
        echo "<td>{$task->id}</td>";
        echo "<td><a href='edit_task.php?id={$task->id}' class='info'>{$task->name}";
        if (!empty($task->description)) echo "<span>".nl2br($task->description)."</span>";
        echo "</a></td>";
        echo "<td>".priority_icon($task->priority).priority_name($task->priority)."</td>";
        echo "<td>".percent_bar($task->completion)."</td>";
        echo "<td";
        if ($startdate > 0 AND $startdate <= $now) echo " class='critical'";
        echo ">";
        if ($startdate > 0) echo date($CONFIG['dateformat_date'],$startdate);
        echo "</td>";
        echo "<td";
        if ($duedate > 0 AND $duedate <= $now) echo " class='critical'";
        echo ">";
        if ($duedate > 0) echo date($CONFIG['dateformat_date'],$duedate);
        echo "</td>";

        echo "</tr>\n";
        if ($shade=='shade1') $shade='shade2';
        else $shade='shade1';
    }
    echo "</table>\n";
}
else echo "<p align='center'>No tasks</p>";

echo "<p align='center'><a href='add_task.php'>Add Task</a></p>";

include('htmlfooter.inc.php');

?>