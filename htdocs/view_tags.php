<?php
// view_tags.php - Page to view the tags on either a record or in general
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

$tagid = $_REQUEST['tagid'];

if(empty($tagid))
{
    //show all tags
    $sql = "SELECT DISTINCT(name), tags.tagid FROM tags, set_tags WHERE tags.tagid = set_tags.tagid";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

    include('htmlheader.inc.php');
    echo "<h2>Tags</h2>";
    if(mysql_num_rows($result) > 0)
    {
        echo "<table align='center'><tr><td>";
        while($obj = mysql_fetch_object($result))
        {
            echo "<a href='".$_SERVER['PHP_SELF']."?tagid=$obj->tagid'>$obj->name</a>  ";
        }
        echo "</td></tr></table>";
    }
    include('htmlfooter.inc.php');
}
else
{
    //show only this tag
    $sql = "SELECT * FROM set_tags WHERE tagid = '$tagid'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

    include('htmlheader.inc.php');
    echo "<h2>Tags</h2>";
    if(mysql_num_rows($result) > 0)
    {
        echo "<table align='center'>";
        while($obj = mysql_fetch_object($result))
        {
            echo "<tr><td>";
            switch($obj->type)
            {
                case 1: //contact
                    $sql = "SELECT forenames, surname FROM contacts WHERE id = '$obj->id'";
                    $result = mysql_query($sql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                    if(mysql_num_rows($result))
                    {
                        $obj = mysql_fetch_object($result);
                        echo "CONTACT ".$obj->forenames." ".$obj->surname;
                    }
                    break;
                case 2: //incident
                    $sql = "SELECT title FROM incidents WHERE id = '$obj->id'";
                    $result = mysql_query($sql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                    if(mysql_num_rows($result))
                    {
                        $obj = mysql_fetch_object($result);
                        echo "INCIDENT ".$obj->name;
                    }
                    break;
            }
            echo "</td></tr>";
        }
        echo "</table>";
    }
    include('htmlfooter.inc.php');
}

?>