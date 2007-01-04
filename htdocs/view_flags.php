<?php
// view_flags.php - Page to view the flags on either a record or in general
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

$flagid = $_REQUEST['flagid'];

if(empty($flagid))
{
    //show all flags
    $sql = "SELECT DISTINCT(name), new_flags.flagid FROM new_flags, set_flags WHERE new_flags.flagid = set_flags.flagid";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

    include('htmlheader.inc.php');
    echo "<h2>Flags</h2>";
    if(mysql_num_rows($result) > 0)
    {
        echo "<table align='center'><tr><td>";
        while($obj = mysql_fetch_object($result))
        {
            echo "<a href='".$_SERVER['PHP_SELF']."?flagid=$obj->flagid'>$obj->name</a>  "; 
        }
        echo "</td></tr></table>";
    }
    include('htmlfooter.inc.php');
}
else
{
    //show only this flag
    $sql = "SELECT * FROM set_flags WHERE flagid = '$flagid'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

    include('htmlheader.inc.php');
    echo "<h2>Flags</h2>";
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