<?php
// add_to_dashboard.php - Page for users to add components to their dashboard
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

$dashboardid = $_REQUEST['id'];

$sql = "SELECT dashboard FROM users WHERE id = '".$_SESSION['userid']."'";
$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

if(mysql_num_rows($result) > 0)
{
    $obj = mysql_fetch_object($result);
    $dashboardstr = $obj->dashboard;
    $dashboardcomponents = explode(",",$obj->dashboard);
}

if(empty($dashboardid))
{

    foreach($dashboardcomponents AS $db)
    {
        $c = explode("-",$db);
        $ondashboard[$c[1]] = $c[1];
    }

    include('htmlheader.inc.php');

    $sql = "SELECT * FROM dashboard WHERE enabled = 'true'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

    echo "<h2>Add to dashboard</h2>\n";

    if(mysql_num_rows($result) > 0)
    {
        echo "<table align='center'>\n";
        while($obj = mysql_fetch_object($result))
        {
            if(empty($ondashboard[$obj->id]))
            {
                //not already on dashbaord
                echo "<tr><th>Name:</th><td>$obj->name</td><td><a href='{$_SERVER['PHP_SELF']}?action=add&id=$obj->id'>Add</a></td></tr>\n";
            }
            else
            {
                echo "<tr><th>Name:</th><td>$obj->name</td><td><a href='{$_SERVER['PHP_SELF']}?action=remove&id=$obj->id'>Remove</a></td></tr>\n";
            }
        }

        echo "</table>\n";
    }

    include('htmlfooter.inc.php');
}
else
{
    $action = $_REQUEST['action'];
    switch($action)
    {
        case 'add':
            $dashboardstr = $dashboardstr.",0-".$dashboardid;
            break;
        case 'remove':
            $regex = "/[012]-".$dashboardid."[,]?/";
            $dashboardstr = preg_replace($regex,"",$dashboardstr);
            break;
    }

    $sql = "UPDATE users SET dashboard = '$dashboardstr' WHERE id = '".$_SESSION['userid']."'";
    $contactresult = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    confirmation_page("2", "main.php", "<h2>Added to dashboard successfully</h2><p align='center'>Please wait while you are redirected...</p>");
}

?>