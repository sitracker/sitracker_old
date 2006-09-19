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

// Defaults
if (empty($user)) $user=$sit[2];

include('htmlheader.inc.php');

echo "<h2>".user_realname($user) . "'s Tasks:</h2>";


$sql = "SELECT * FROM tasks WHERE owner='$user'";
$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
if (mysql_num_rows($result) >=1 )
{
    echo "<table align='center'>";
    echo "<tr><th>ID</th><th>Task</th><th>Priority</th><th>Completion</th><th>Due Date</th><th>Start Date</th></tr>\n";
    $shade='shade1';
    while ($task = mysql_fetch_object($result))
    {
        echo "<tr class='$shade'>";
        echo "<td>{$task->id}</td>";
        echo "<td>{$task->name}</td>";
        echo "<td>{$task->priority}</td>";
        echo "<td>{$task->completion}</td>";
        echo "<td>{$task->duedate}</td>";
        echo "<td>{$task->startdate}</td>";
        echo "</tr>\n";
        if ($shade=='shade1') $shade='shade2';
        else $shade='shade1';
    }
    echo "</table>\n";
}
else echo "<p align='center'>No tasks</p>";



include('htmlfooter.inc.php');

?>