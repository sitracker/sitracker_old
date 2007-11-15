<?php
// tasks.inc.php - List tasks
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Authors: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
//          Kieran Hogg <kieran_hogg[at]users.sourceforge.net>
// called by tasks.php

// External variables
$user = cleanvar($_REQUEST['user']);
$show = cleanvar($_REQUEST['show']);
$sort = cleanvar($_REQUEST['sort']);
$order = cleanvar($_REQUEST['order']);
$incident = cleanvar($_REQUEST['incident']);
$mode;


if(!empty($incident))
{
    $mode = 'incident';

    //get info for incident-->task linktype
    $sql = "SELECT DISTINCT origcolref, linkcolref ";
    $sql .= "FROM links, linktypes ";
    $sql .= "WHERE links.linktype=4 ";
    $sql .= "AND linkcolref={$incident} ";
    $sql .= "AND direction='left'";
    $result = mysql_query($sql);

    //get list of tasks
    $sql = "SELECT * FROM tasks WHERE 1=0 ";
    while($tasks = mysql_fetch_object($result))
    {
        $sql .= "OR id={$tasks->origcolref} ";
    }
    $result = mysql_query($sql);

    if($mode == 'incident')
    {
        echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/task.png' width='32' height='32' alt='' /> ";
        echo "{$strActivities}</h2>";
    }
    echo "<p align='center'>{$strIncidentActivitiesIntro}</p>";
}
else
{// Defaults
    if (empty($user) OR $user=='current') $user=$sit[2];
    // If the user is passed as a username lookup the userid
    if (!is_number($user) AND $user!='current' AND $user!='all')
    {
        $usql = "SELECT id FROM users WHERE username='{$user}' LIMIT 1";
        $uresult = mysql_query($usql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        if (mysql_num_rows($uresult) >= 1) list($user) = mysql_fetch_row($uresult);
        else $user=$sit[2]; // force to current user if username not found
    }
    echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/task.png' width='32' height='32' alt='' /> ";
    echo user_realname($user,TRUE) . "'s {$strTasks}:</h2>";

    // show drop down select for task view options
    echo "<form action='{$_SERVER['PHP_SELF']}' style='text-align: center;'>";
    echo "{$strView}: <select class='dropdown' name='queue' onchange='window.location.href=this.options[this.selectedIndex].value'>\n";
    echo "<option ";
    if ($show == '' OR $show == 'active') echo "selected='selected' ";
    echo "value='{$_SERVER['PHP_SELF']}?user=$user&amp;show=active&amp;sort=$sort&amp;order=$order'>{$strActive}</option>\n";
    echo "<option ";
    if ($show == 'completed') echo "selected='selected' ";
    echo "value='{$_SERVER['PHP_SELF']}?user=$user&amp;show=completed&amp;sort=$sort&amp;order=$order'>{$strCompleted}</option>\n";
    echo "</select>\n";
    echo "</form><br />";

    $sql = "SELECT * FROM tasks WHERE owner='$user' AND distribution != 'incident' ";
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
}

    //common code
    if (mysql_num_rows($result) >=1 )
    {
        if($show) $filter=array('show' => $show);
        echo "<br /><table align='center'>";
        echo "<tr>";

        if($mode != 'incident')
        {
            $totalduration;
            if ($user == $sit[2])
            {
                echo colheader('distribution', "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/private.png' width='16' height='16' title='Public/Private' alt='Private' style='border: 0px;' />", $sort, $order, $filter);
            }
            else $filter['user'] = $user;

            echo colheader('id', $strID, $sort, $order, $filter);
            echo colheader('name', $strTask, $sort, $order, $filter);
            echo colheader('priority', $strPriority, $sort, $order, $filter);
            echo colheader('completion', $strCompletion, $sort, $order, $filter);
            echo colheader('startdate', $strStartDate, $sort, $order, $filter);
            echo colheader('duedate', $strDueDate, $sort, $order, $filter);
            if ($show=='completed') echo colheader('enddate', $strEndDate, $sort, $order, $filter);
        }
        else
        {
            echo colheader('id', $strID, $sort, $order, $filter);
            echo colheader('startdate', $strStartDate, $sort, $order, $filter);
            echo colheader('completeddate', $strCompleted, $sort, $order, $filter);
            echo colheader('duration', $strDuration, $sort, $order, $filter);
            echo colheader('lastupdated', $strLastUpdated, $sort, $order, $filter);
            echo colheader('owner', $strOwner, $sort, $order, $filter);
        }
        echo "</tr>\n";
        $shade='shade1';
        while ($task = mysql_fetch_object($result))
        {
            $duedate = mysql2date($task->duedate);
            $startdate = mysql2date($task->startdate);
            $enddate = mysql2date($task->enddate);
            $lastupdated = mysql2date($task->lastupdated);
            echo "<tr class='$shade'>";
            if ($user == $sit[2])
            {
                echo "<td>";
                if ($task->distribution=='private') echo " <img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/private.png' width='16' height='16' title='Private' alt='Private' />";
                echo "</td>";
            }
            if($mode == 'incident')
            {
                if($enddate == '0') echo "<td><a href='view_task.php?id={$task->id}&mode=incident&incident={$id}' class='info'>{$task->id}</td>";
                else echo "<td>{$task->id}</td>";
            }
            else
            {
                echo "<td>";
                echo "{$task->id}";
                echo "</td>";
                echo "<td>";
                echo "<a href='view_task.php?id={$task->id}' class='info'>".stripslashes($task->name);
                echo "</a>";

                echo "</td>";
                echo "<td>".priority_icon($task->priority).priority_name($task->priority)."</td>";
                echo "<td>".percent_bar($task->completion)."</td>";
            }

            if($mode != 'incident')
            {
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
            }
            else
            {
                echo "<td>".format_date_friendly($startdate)."</td>";
                if($enddate == '0')
                {
                    echo "<td>$strNotCompleted</td>";
                    $duration = $now - $startdate;
                    echo "<td><em>".format_seconds($duration)."</em></td>";

                }
                else
                {
                    $duration = $enddate - $startdate;                    
                    echo "<td>".format_date_friendly($enddate)."</td>";
                    echo "<td>".format_seconds($duration)."</td>";
                }
                $totalduration += $duration;

                echo "<td>".format_date_friendly($lastupdated)."</td>";
            }

            if ($show=='completed')
            {
                echo "<td>";
                if ($enddate > 0) echo date($CONFIG['dateformat_date'],$enddate);
                echo "</td>";
            }
            if($mode == 'incident')
            {
                echo "<td>".user_realname($task->owner)."</td>";
            }
            echo "</tr>\n";
            if ($shade=='shade1') $shade='shade2';
            else $shade='shade1';
    }
    
    if($mode == 'incident')
    {
        echo "<tr class=$shade><td><strong>{$strTotal}:</strong></td><td colspan=5>".format_seconds($totalduration)."</td></tr>";
        echo "<tr class=$shade><td><strong>{$strExact}:</strong></td><td colspan=5>".seconds_to_string($totalduration)."</td></tr>";
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
if($mode == 'incident') echo "<p align='center'><a href='add_task.php?incident={$id}'>{$strAddActivity}</a></p>";
else echo "<p align='center'><a href='add_task.php'>{$strAddTask}</a></p>";
?>