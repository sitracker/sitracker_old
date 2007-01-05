<?php
// edit_tags.php - Edit the tags associated with a record
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


$recordid = $_REQUEST['recordid'];
$action = $_REQUEST['action'];

if(!empty($recordid) AND empty($action))
{
    $sql = "SELECT set_tags.*, tags.name FROM  set_tags, tags WHERE ";
    $sql .= "set_tags.tagid = tags.tagid AND set_tags.id = '$recordid'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

    if(mysql_num_rows($result) > 0)
    {
        include('htmlheader.inc.php');
        echo "<h2>Edit tags</h2>";
        echo "<table align='center' class='vertical'>";
        while($obj = mysql_fetch_object($result))
        {
            echo "<tr><th>$obj->name</th>";
            echo "<td><a href='".$_SERVER['PHP_SELF']."?action=delete&recordid=$recordid&tagid=$obj->tagid&type=$obj->type'>Delete</a></td></tr>";
        }
        echo "</table>";
        include('htmlfooter.inc.php');
    }
    else
    {
        include('htmlheader.inc.php');
        echo "<h2>No tags on this record</h2>";
        include('htmlfooter.inc.php');
    }
}
elseif(!empty($action))
{
    $tagid = $_REQUEST['tagid'];
    $type = $_REQUEST['type'];
    if($action == "delete")
    {
        $sql = "DELETE FROM set_tags WHERE id = '$recordid' AND type = '$type' AND tagid = '$tagid'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        confirmation_page("2", "edit_tags.php?recordid=$recordid", "<h2>Tag deleted Successfully</h2><p align='center'>Please wait while you are redirected...</p>");
    }
}
else
{
    //shouldn't really get here though you could
    include('htmlheader.inc.php');
    echo "<h2>Error, no tag details provided</h2>";
    include('htmlfooter.inc.php');
}

?>