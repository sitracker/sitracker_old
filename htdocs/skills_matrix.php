<?php
// skills_matrix.php - Skills matrix page
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>

$permission=0; // not required
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

include('htmlheader.inc.php');

$sql = "SELECT users.id, users.realname FROM users, usersoftware WHERE users.id = usersoftware.userid GROUP BY users.id ORDER BY users.id";

$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

$count = mysql_num_rows($result);

if($count > 0)
{
    while($row = mysql_fetch_object($result))
    {
        $users[$row->id] = $row->realname;
    }
}

$sql = "SELECT users.id, users.realname, software.name FROM users, software, usersoftware ";
$sql .= "WHERE users.id = usersoftware.userid AND software.id = usersoftware.softwareid ";
$sql .= "ORDER BY software.id, users.id";

$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

$count = mysql_num_rows($result);

if($count > 0)
{

    echo "<table>";
    echo "<tr><td>Software</td>";
    foreach($users AS $u) echo "<th>$u</th>";
    echo "</tr>";
    $previous = "";
    while($row = mysql_fetch_object($result))
    {
        if($previous != $row->name)
        {
            if($started == true) echo "</tr>";
            echo "<tr><th>{$row->name}</th>";
            $started = true;
        }
        

        echo $row->realname." ";
        $previous = $row->name;
    }

    echo "</table>";
}

include('htmlfooter.inc.php');

?>