<?php
// statistics.php - Over view and stats of calls logged - intended for last 24hours
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>


$title='Todays statistics';
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');


function give_overview()
{
    global $todayrecent;

    // Count incidents logged today
    $sql = "SELECT id FROM incidents WHERE opened > '$todayrecent'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $todaysincidents=mysql_num_rows($result);
    mysql_free_result($result);
    
    $string = "<h4>$todaysincidents Incidents logged today<h4>";
    if($todaysincidents > 0)
    {
        $string .= "<table align='center' width='50%'><tr><td colspan='2'>Which where assigned as follows:</td></tr>";
        $sql = "SELECT count(incidents.id), realname, users.id AS owner FROM incidents, users WHERE opened > '$todayrecent' AND incidents.owner = users.id GROUP BY owner DESC";

        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        while($row = mysql_fetch_array($result))
        {
            $sql = "SELECT id, title FROM incidents WHERE opened > '$todayrecent' AND owner = '".$row['owner']."'";
            
            $string .= "<tr><th>".$row['count(incidents.id)']."</th>";
            $string .= "<td class='shade2' align='left'><a href='incidents.php?user=".$row['owner']."&amp;queue=1&amp;type=support'>".$row['realname']."</a> ";

            $iresult = mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            while($irow = mysql_fetch_array($iresult))
            {
                $string .= "<small><a href=\"javascript:incident_details_window('".$irow['id']."', 'incident".$irow['id']."')\"  title='".$irow['title']."'>[".$irow['id']."]</a></small> ";
            }
            
            $string .= "</td></tr>";
        }
        $string .= "</table>";
    }

    
    // Count incidents closed today

    $sql = "SELECT id FROM incidents WHERE closed > '$todayrecent'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $todaysclosed=mysql_num_rows($result);
    

    $string .= "<h4>$todaysclosed Incidents closed today</h4>";
    if($todaysclosed > 0)
    {
        
        $sql = "SELECT count(incidents.id), realname, users.id AS owner FROM incidents, users WHERE closed > '$todayrecent' AND incidents.owner = users.id GROUP BY owner";
        $string .= "<table align='center' width='50%'>";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        while($row = mysql_fetch_array($result))
        {
            $string .= "<tr><th colspan='4' align='left'>".$row['count(incidents.id)']." Closed by ".$row['realname']."</th></tr>";
            
            $sql = "SELECT incidents.id, incidents.title, closingstatus.name ";
            $sql .= "FROM incidents, closingstatus ";
            $sql .= "WHERE incidents.closingstatus = closingstatus.id AND closed > '$todayrecent' AND incidents.owner = '".$row['owner']."' ORDER BY closed";

            
    	    $string .= "<tr><th>ID</th><th>Title</th><th>Owner</th><th>Closing status</th></tr>";
            $iresult = mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            while($irow = mysql_fetch_array($iresult))
            {
                $string .= "<tr><th><a href=\"javascript:incident_details_window('".$irow['id']."', 'incident".$irow['id']."')\" title='[".$irow['id']."] - ".$irow['title']."'>".$irow['id']."</a></th>";
                $string .= "<td class='shade2' align='left'>".$irow['title']."</td><td class='shade2' align='left'>".$row['realname']."</td><td class='shade2'>".$irow['name']."</td></tr>";
            }
            $string .= "</table>";
        }
        $string .= "</table>";
    }

    mysql_free_result($result);

    return $string;
}

include('htmlheader.inc.php');

echo "<script src='/webtrack.js' type='text/javascript'></script>";

$mode = cleanvar($_REQUEST['mode']);

switch($mode)
{
    case 'overview': //this is the default so just fall though
    default:
        echo "<h2>$title - Overview</h2>";
        echo give_overview();
        break;        
}

include('htmlfooter.inc.php');
?>
