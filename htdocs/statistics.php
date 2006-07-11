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
            $string .= "<tr><th>".$row['count(incidents.id)']."</th>";
            $string .= "<td class='shade2' align='left'><a href='incidents.php?user=".$row['owner']."&amp;queue=1&amp;type=support'>".$row['realname']."</a></td></tr>";
        }
        $string .= "</table>";
    }
    

    // Count incidents updated today
    $sql = "SELECT id FROM incidents WHERE lastupdated > '$todayrecent'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $todaysupdated=mysql_num_rows($result);
    mysql_free_result($result);
    
    // Count incidents closed today
    $sql = "SELECT id FROM incidents WHERE closed > '$todayrecent'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $todaysclosed=mysql_num_rows($result);
    mysql_free_result($result);
    
    // count total number of SUPPORT incidents that are open at this time (not closed)
    $sql = "SELECT id FROM incidents WHERE status!=2 AND status!=9 AND status!=7 AND type='support'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $supportopen=mysql_num_rows($result);
    mysql_free_result($result);
    
        // Count kb articles published today
    $sql = "SELECT docid FROM kbarticles WHERE published > '".date('Y-m-d')."'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $kbpublished=mysql_num_rows($result);
    mysql_free_result($result);


    return $string;
}

include('htmlheader.inc.php');

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