<?php
// dashboard_tasks.php - List of tasks
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//


function dashboard_tasks()
{
    global $sit;
    $user = $sit[2];
    echo "<span id='dragList1' >";
    echo "<div class='windowbox' style='width: 95%;'>";
    echo "<div class='windowtitle'><a href='tasks.php'>".user_realname($user,TRUE)."'s Tasks</a></div>";
    echo "<div class='window'>";

    $sql = "SELECT * FROM tasks WHERE owner='$user' AND (completion < 100 OR completion='' OR completion IS NULL) ";
    if (!empty($sort))
    {
        if ($sort=='id') $sql .= "ORDER BY id ";
        elseif ($sort=='name') $sql .= "ORDER BY name ";
        elseif ($sort=='priority') $sql .= "ORDER BY priority ";
        elseif ($sort=='completion') $sql .= "ORDER BY completion ";
        elseif ($sort=='startdate') $sql .= "ORDER BY startdate ";
        elseif ($sort=='duedate') $sql .= "ORDER BY duedate ";
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
        echo "<table align='center' width='100%'>";
        echo "<tr>";
        echo colheader('id', 'ID');
        echo colheader('name', 'Task');
        echo colheader('priority', 'Priority');
        echo colheader('completion', 'Completion');
        echo "</tr>\n";
        $shade='shade1';
        while ($task = mysql_fetch_object($result))
        {
            $duedate = mysql2date($task->duedate);
            $startdate = mysql2date($task->startdate);
            echo "<tr class='$shade'>";
            echo "<td>{$task->id}</td>";
            echo "<td><a href='view_task.php?id={$task->id}' class='info'>".stripslashes($task->name);
            if (!empty($task->description)) echo "<span>".nl2br(stripslashes($task->description))."</span>";
            echo "</a></td>";
            echo "<td>".priority_icon($task->priority).priority_name($task->priority)."</td>";
            echo "<td>".percent_bar($task->completion)."</td>";
            echo "</tr>\n";
            if ($shade=='shade1') $shade='shade2';
            else $shade='shade1';
        }
        echo "</table>\n";
    }
    else
    {
        echo "<p align='center'>No tasks</p>";
    }

    echo "</div>";
    echo "</div>";
    echo "</span>";
}

?>