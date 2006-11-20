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

$legacy = cleanvar($_REQUEST['legacy']);

$title='Skills Matrix';

include('htmlheader.inc.php');

echo "<h2>$title</h2>";
if(empty($legacy)) echo "<p align='center'><a href='{$_SERVER['PHP_SELF']}?legacy=yes'>Show legacy software</a></p>";
else echo "<p align='center'><a href='{$_SERVER['PHP_SELF']}'>Hide legacy software</a></p>";

$sql = "SELECT users.id, users.realname FROM users, usersoftware, software ";
$sql .= "WHERE users.id = usersoftware.userid AND users.status <> 0 ";
if(empty($legacy)) $sql .= " AND (software.lifetime_end > NOW() OR software.lifetime_end = '0000-00-00' OR software.lifetime_end is NULL) ";
$sql .= "AND software.id = usersoftware.softwareid GROUP BY users.id ORDER BY users.realname";
$usersresult = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

$countusers = mysql_num_rows($usersresult);

if($countusers > 0)
{
    while($row = mysql_fetch_object($usersresult))
    {
        $users[$row->id] = $row->realname;
        $counting[$row->realname]=0;
    }
}
mysql_data_seek($usersresult, 0);

$sql = "SELECT users.id, users.realname, software.name FROM users, software, usersoftware ";
$sql .= "WHERE users.id = usersoftware.userid AND software.id = usersoftware.softwareid ";
$sql .= "AND users.status <> 0 ";
if(empty($legacy)) $sql .= "AND (software.lifetime_end > NOW() OR software.lifetime_end = '0000-00-00' OR software.lifetime_end is NULL) ";
$sql .= " ORDER BY software.name, users.id";
$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

$countskills = mysql_num_rows($result);

if($countskills > 0)
{
    $previous = "";
    while($row = mysql_fetch_object($result))
    {
        $skills[$row->name][$row->realname] = $row->realname;
    }
/*echo "<pre>";
print_r($skills);
echo "</pre>";*/
    mysql_data_seek($result, 0);
    echo "<table align='center'>";
    echo "<tr><td>Software</td>";
    foreach($users AS $u) echo "<th>$u</th>";
    echo "<th>Count</th>";
    echo "</tr>\n";
    $previous = "";
    while($row = mysql_fetch_object($result))
    {
        if($previous != $row->name)
        {
            $count = 0;
            echo "<tr><th>{$row->name}</th>";
            while($user = mysql_fetch_object($usersresult))
            {
                //todo get the proper symbol for a cross
                if(empty($skills[$row->name][$user->realname])) 
                {
                    // No skill in this software
                    echo "<td align='center'>&#215;</td>"; 
                }
                else
                {
                    //Skill in software
                    echo "<td align='center'>&#10004;</td>";
                    $counting[$user->realname]++;
                    $count++;
                }
            }
            echo "<td align='center'>$count</td>";
            echo "</tr>\n";
            $started = true;
        }
        mysql_data_seek($usersresult, 0);
        //echo $row->realname." ";
        $previous = $row->name;
    }
    echo "<th align='right'>COUNT</th>";
    foreach($counting AS $c) echo "<td align='center'>{$c}</td>";
    echo "</table>";
}

include('htmlfooter.inc.php');

?>