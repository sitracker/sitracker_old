<?php

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
        $string .= "<table align='center' width='30%'><tr><td colspan='2'>Which where assigned as follows:</td></tr>";
        $sql = "SELECT count(incidents.id), realname, users.id AS owner FROM incidents, users WHERE opened > '$todayrecent' AND incidents.owner = users.id GROUP BY owner";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        while($row = mysql_fetch_array($result))
        {
            $sql = "SELECT id FROM incidents WHERE opened > '$todayrecent' AND owner = '".$row['owner']."'";
            
            $string .= "<tr><th>".$row['count(incidents.id)']."</th>";
            $string .= "<td class='shade2' align='left'><a href='incidents.php?user=".$row['owner']."&amp;queue=1&amp;type=support'>".$row['realname']."</a> ";

            $iresult = mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            while($irow = mysql_fetch_array($iresult))
            {
                $string .= "<a href=\"javascript:incident_details_window('".$irow['id']."', 'incident".$irow['id']."')\">[".$irow['id']."]</a> ";
            }
            
            $string .= "</td></tr>";
        }
        $string .= "</table>";
    }

    
    // Count incidents closed today
    $sql = "SELECT incidents.id, incidents.title, realname FROM incidents, users WHERE incidents.owner = users.id AND closed > '$todayrecent'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $todaysclosed=mysql_num_rows($result);
    

    $string .= "<h4>$todaysclosed Incidents closed today</h4>";
    if($todaysclosed > 0)
    {
        $string .= "<table align='center' width='30%'>";
	$string .= "<tr><th>ID</th><th>Title</th><th>Owner</th></tr>";
        while($row = mysql_fetch_array($result))
        {
            $string .= "<tr><th><a href=\"javascript:incident_details_window('".$row['id']."', 'incident".$row['id']."')\">".$row['id']."</a></th><td class='shade2' align='left'>".$row['title']."</td><td class='shade2' align='left'>".$row['realname']."</td></tr>";
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
