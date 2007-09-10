<?php
// view_tags.php - Page to view the tags on either a record or in general
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
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

$tagid = cleanvar($_REQUEST['tagid']);
$orderby = cleanvar($_REQUEST['orderby']);

if(empty($orderby)) $orderby = "name";

if(empty($tagid))
{
    //show all tags
    include('htmlheader.inc.php');
    echo "<h2>Tags</h2>";
    echo show_tag_cloud($orderby);
    include('htmlfooter.inc.php');
}
else
{
    $sql = "SELECT name FROM tags WHERE tagid = '$tagid' LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    list($tagname)=mysql_fetch_row($result);

    include('htmlheader.inc.php');
    echo "<h2><a href='view_tags.php'>Tag</a>: $tagname";
    if (array_key_exists($tagname, $CONFIG['tag_icons'])) echo "&nbsp;<img src='images/icons/sit/32x32/{$CONFIG['tag_icons'][$tagname]}.png' style='border:0px;' alt='' />";
    echo "</h2>";


    //show only this tag
    $sql = "SELECT * FROM set_tags WHERE tagid = '$tagid'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

    if(mysql_num_rows($result) > 0)
    {
        echo "<table align='center'>";
        while($obj = mysql_fetch_object($result))
        {
            echo "<tr>";
            switch($obj->type)
            {
                case 1: //contact
                    $sql = "SELECT forenames, surname FROM contacts WHERE id = '{$obj->id}'";
                    $resultcon = mysql_query($sql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                    if(mysql_num_rows($resultcon) > 0)
                    {
                        $objcon = mysql_fetch_object($resultcon);
                        echo "<th>CONTACT</th><td><a href='contact_details.php?id={$obj->id}'>";
                        echo $objcon->forenames." ".$objcon->surname."</a></td>";
                    }
                break;

                case 2: //incident
                    $sql = "SELECT title FROM incidents WHERE id = '$obj->id'";
                    $resultinc = mysql_query($sql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                    if(mysql_num_rows($resultinc) > 0)
                    {
                        $objinc = mysql_fetch_object($resultinc);
                        echo "<th>INCIDENT</th><td><a href=\"javascript:incident_details_window('{$obj->id}','incident{$obj->id}')\">";
                        echo $objinc->title."</a></td>";
                    }
                break;

                case 3: //site
                    $sql = "SELECT name FROM sites WHERE id = '{$obj->id}'";
                    $resultsite = mysql_query($sql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                    if(mysql_num_rows($resultsite) > 0)
                    {
                        $objsite = mysql_fetch_object($resultsite);
                         echo "<th>SITE</th><td><a href='site_details.php?id={$obj->id}&amp;action=show'>";
                        echo $objsite->name."</a></td>";
                    }
                break;

                case 4: // task
                    $sql = "SELECT name FROM tasks WHERE id = '{$obj->id}'";
                    $resulttask = mysql_query($sql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                    if(mysql_num_rows($resulttask) > 0)
                    {
                        $objtask = mysql_fetch_object($resulttask);
                         echo "<th>TASK</th><td><a href='view_task.php?id={$obj->id}'>";
                        echo $objtask->name."</a></td>";
                    }
                break;

                default:
                    echo "<th>Other</th><td>{$obj->id}</td>";
            }
            echo "</tr>\n";
        }
        echo "</table>";
    }
    include('htmlfooter.inc.php');
}

?>