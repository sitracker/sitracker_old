<?php
// tasks.php - List tasks
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

$title='Tasks';

// This page requires authentication
require('auth.inc.php');

// External variables
$user = cleanvar($_REQUEST['user']);
$show = cleanvar($_REQUEST['show']);
$sort = cleanvar($_REQUEST['sort']);
$order = cleanvar($_REQUEST['order']);

// Defaults
if (empty($user) OR $user=='current') $user=$sit[2];
// If the user is passed as a username lookup the userid
if (!is_number($user) AND $user!='current' AND $user!='all')
{
    $usql = "SELECT id FROM users WHERE username='$user' LIMIT 1";
    $uresult = mysql_query($usql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    if (mysql_num_rows($uresult) >= 1) list($user) = mysql_fetch_row($uresult);
    else $user=$sit[2]; // force to current user if username not found
}

include('htmlheader.inc.php');

echo "<h2>".user_realname($user,TRUE) . "'s Tasks:</h2>";

// show drop down select for task view options
echo "<form action='{$_SERVER['PHP_SELF']}' style='text-align: center;'>";
echo "View: <select class='dropdown' name='queue' onchange='window.location.href=this.options[this.selectedIndex].value'>\n";
echo "<option ";
if ($show == '' OR $show == 'active') echo "selected='selected' ";
echo "value='{$_SERVER['PHP_SELF']}?user=$user&amp;show=active&amp;sort=$sort&amp;order=$order'>Active</option>\n";
echo "<option ";
if ($show == 'completed') echo "selected='selected' ";
echo "value='{$_SERVER['PHP_SELF']}?user=$user&amp;show=completed&amp;sort=$sort&amp;order=$order'>Completed</option>\n";
echo "</select>\n";
echo "</form><br />";



$sql = "SELECT * FROM tasks WHERE owner='$user' ";
if ($show=='' OR $show=='active' ) $sql .= "AND (completion < 100 OR completion='' OR completion IS NULL) ";
elseif ($show=='completed') $sql .= "AND (completion = 100) ";
else $sql .= "AND 1=2 "; // force no results for other cases
if ($user != $sit[2]) $sql .= "AND distribution='public' ";

if (!empty($sort))
{
    if ($sort=='id') $sql .= "ORDER BY id ";
    elseif ($sort=='name') $sql .= "ORDER BY name ";
    elseif ($sort=='priority') $sql .= "ORDER BY priority ";
    elseif ($sort=='completion') $sql .= "ORDER BY completion ";
    elseif ($sort=='startdate') $sql .= "ORDER BY startdate ";
    elseif ($sort=='duedate') $sql .= "ORDER BY duedate ";
    elseif ($sort=='enddate') $sql .= "ORDER BY enddate ";
    elseif ($sort=='distribution') $sql .= "ORDER BY distribution ";
    else $sql = "ORDER BY id ";
    if ($order=='a' OR $order=='ASC' OR $order='') $sql .= "ASC";
    else $sql .= "DESC";
}
else $sql .= "ORDER BY IF(duedate,duedate,99999999) ASC, duedate ASC, startdate DESC, priority DESC, completion ASC";

$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);



if (mysql_num_rows($result) >=1 )
{
    echo "<table align='center'>";
    echo "<tr>";
    if ($user == $sit[2]) echo colheader('distribution', "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/apps/password.png' width='16' height='16' title='Public/Private' alt='Private' style='border: 0px;' />", $sort, $order);
    echo colheader('id', 'ID', $sort, $order);
    echo colheader('name', 'Task', $sort, $order);
    echo colheader('priority', 'Priority', $sort, $order);
    echo colheader('completion', 'Completion', $sort, $order);
    echo colheader('startdate', 'Start Date', $sort, $order);
    echo colheader('duedate', 'Due Date', $sort, $order);
    if ($show=='completed') echo colheader('enddate', 'End Date', $sort, $order);
    echo "</tr>\n";
    $shade='shade1';
    while ($task = mysql_fetch_object($result))
    {
        $duedate = mysql2date($task->duedate);
        $startdate = mysql2date($task->startdate);
        $enddate = mysql2date($task->enddate);
        echo "<tr class='$shade'>";
        if ($user == $sit[2])
        {
            echo "<td>";
            if ($task->distribution=='private') echo " <img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/apps/password.png' width='16' height='16' title='Private' alt='Private' />";
            echo "</td>";
        }
        echo "<td>";
        echo "{$task->id}";
        echo "</td>";
        echo "<td>";
        echo "<a href='view_task.php?id={$task->id}' class='info'>".stripslashes($task->name);
        if (!empty($task->description)) echo "<span>".nl2br(stripslashes($task->description))."</span>";
        echo "</a>";

        echo "</td>";
        echo "<td>".priority_icon($task->priority).priority_name($task->priority)."</td>";
        echo "<td>".percent_bar($task->completion)."</td>";
        echo "<td";
        if ($startdate > 0 AND $startdate <= $now AND $task->completion <= 0) echo " class='urgent'";
        elseif ($startdate > 0 AND $startdate <= $now AND $task->completion >= 1 AND $task->completion < 100) echo " class='idle'";
        echo ">";
        if ($startdate > 0) echo date($CONFIG['dateformat_date'],$startdate);
        echo "</td>";
        echo "<td";
        if ($duedate > 0 AND $duedate <= $now AND $task->completion < 100) echo " class='urgent'";
        echo ">";
        if ($duedate > 0) echo date($CONFIG['dateformat_date'],$duedate);
        echo "</td>";
        if ($show=='completed')
        {
            echo "<td>";
            if ($enddate > 0) echo date($CONFIG['dateformat_date'],$enddate);
            echo "</td>";
        }
        echo "</tr>\n";
        if ($shade=='shade1') $shade='shade2';
        else $shade='shade1';
    }
    echo "</table>\n";
}
else
{
    echo "<p align='center'>";
    if ($sit[2]==$user) echo "No tasks";
    else echo "No public tasks";
    echo "</p>";
}

echo "<p align='center'><a href='add_task.php'>Add Task</a></p>";

include('htmlfooter.inc.php');

?>
