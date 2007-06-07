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

    // Show Open, Closed, Updated today, this week, this month etc.
    function count_incidents($startdate, $enddate)
    {
        // Counts the number of incidents opened between a start date and an end date
        // Returns an associative array
        $sql = "SELECT count(*) FROM incidents ";
        $sql .= "WHERE opened BETWEEN '{$startdate}' AND '{$enddate}' ";
        $result= mysql_query($sql);
        list($count['opened'])=mysql_fetch_row($result);
        mysql_free_result($result);

        $sql = "SELECT count(*) FROM incidents ";
        $sql .= "WHERE closed BETWEEN '{$startdate}' AND '{$enddate}' ";
        $result= mysql_query($sql);
        list($count['closed'])=mysql_fetch_row($result);
        mysql_free_result($result);

        $sql = "SELECT count(*) FROM incidents ";
        $sql .= "WHERE lastupdated BETWEEN '{$startdate}' AND '{$enddate}' ";
        $result= mysql_query($sql);
        list($count['updated'])=mysql_fetch_row($result);
        mysql_free_result($result);

        $sql = "SELECT count(*) FROM incidents WHERE opened <= '{$enddate}' AND (closed >= '$startdate' OR closed = 0) ";
        $result= mysql_query($sql);
        list($count['handled'])=mysql_fetch_row($result);
        mysql_free_result($result);

        $sql = "SELECT count(*) FROM updates WHERE timestamp >= '$startdate' AND timestamp <= '$enddate' ";
        $result= mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        list($count['updates'])=mysql_fetch_row($result);
        mysql_free_result($result);

        return $count;
    }

    function stats_period_row($desc, $start, $end)
    {
        global $shade;
        if ($shade=='') $shade='shade1';
        $count = count_incidents($start,$end);
        $html = "<tr class='$shade'><td>$desc</td><td>{$count['opened']}</td><td>{$count['updated']}</td><td>{$count['closed']}</td><td>{$count['handled']}</td><td>{$count['updates']}</td><td>".number_format($count['updates']/$count['updated'], 2)."</td></tr>\n";
        if ($shade=='shade1') $shade='shade2';
        else $shade='shade1';
        return $html;
    }

    echo "<table align='center'>";
    echo "<tr><th>Period</th><th>Opened</th><th>Updated</th><th>Closed</th><th>Handled</th><th>Updates</th><th>per incident</th></tr>\n";
    echo stats_period_row('Today', mktime(0,0,0,date('m'),date('d'),date('Y')),mktime(23,59,59,date('m'),date('d'),date('Y')));
    echo stats_period_row('Yesterday', mktime(0,0,0,date('m'),date('d')-1,date('Y')),mktime(23,59,59,date('m'),date('d')-1,date('Y')));
    echo stats_period_row(date('l',mktime(0,0,0,date('m'),date('d')-2,date('Y'))), mktime(0,0,0,date('m'),date('d')-2,date('Y')),mktime(23,59,59,date('m'),date('d')-2,date('Y')));
    echo stats_period_row(date('l',mktime(0,0,0,date('m'),date('d')-3,date('Y'))), mktime(0,0,0,date('m'),date('d')-3,date('Y')),mktime(23,59,59,date('m'),date('d')-3,date('Y')));
    echo stats_period_row(date('l',mktime(0,0,0,date('m'),date('d')-4,date('Y'))), mktime(0,0,0,date('m'),date('d')-4,date('Y')),mktime(23,59,59,date('m'),date('d')-4,date('Y')));
    echo stats_period_row(date('l',mktime(0,0,0,date('m'),date('d')-5,date('Y'))), mktime(0,0,0,date('m'),date('d')-5,date('Y')),mktime(23,59,59,date('m'),date('d')-5,date('Y')));
    echo stats_period_row(date('l',mktime(0,0,0,date('m'),date('d')-6,date('Y'))), mktime(0,0,0,date('m'),date('d')-6,date('Y')),mktime(23,59,59,date('m'),date('d')-6,date('Y')));
    echo stats_period_row('Past 7 days', mktime(0,0,0,date('m'),date('d')-6,date('Y')),mktime(23,59,59,date('m'),date('d'),date('Y')));
    echo stats_period_row('Previous 7 days', mktime(0,0,0,date('m'),date('d')-13,date('Y')),mktime(23,59,59,date('m'),date('d')-7,date('Y')));

    echo stats_period_row('This month', mktime(0,0,0,date('m'),1,date('Y')),mktime(23,59,59,date('m'),date('d'),date('Y')));
    echo stats_period_row(date('F y',mktime(0,0,0,date('m')-1,1,date('Y'))), mktime(0,0,0,date('m')-1,date('d'),date('Y')),mktime(23,59,59,date('m'),0,date('Y')));
    echo stats_period_row(date('F y',mktime(0,0,0,date('m')-2,1,date('Y'))), mktime(0,0,0,date('m')-2,date('d'),date('Y')),mktime(23,59,59,date('m')-1,0,date('Y')));
    echo stats_period_row(date('F y',mktime(0,0,0,date('m')-3,1,date('Y'))), mktime(0,0,0,date('m')-3,date('d'),date('Y')),mktime(23,59,59,date('m')-2,0,date('Y')));
    echo stats_period_row(date('F y',mktime(0,0,0,date('m')-4,1,date('Y'))), mktime(0,0,0,date('m')-4,date('d'),date('Y')),mktime(23,59,59,date('m')-3,0,date('Y')));
    echo stats_period_row(date('F y',mktime(0,0,0,date('m')-5,1,date('Y'))), mktime(0,0,0,date('m')-5,date('d'),date('Y')),mktime(23,59,59,date('m')-4,0,date('Y')));
    echo stats_period_row(date('F y',mktime(0,0,0,date('m')-6,1,date('Y'))), mktime(0,0,0,date('m')-6,date('d'),date('Y')),mktime(23,59,59,date('m')-5,0,date('Y')));

    echo stats_period_row('This year', mktime(0,0,0,1,1,date('Y')),mktime(23,59,59,date('m'),date('d'),date('Y')));
    echo stats_period_row('Last year', mktime(0,0,0,1,1,date('Y')-1),mktime(23,59,59,12,31,date('Y')-1));
    echo stats_period_row(date('Y',mktime(0,0,0,1,1,date('Y')-2)), mktime(0,0,0,1,1,date('Y')-2),mktime(23,59,59,12,31,date('Y')-2));
    echo stats_period_row(date('Y',mktime(0,0,0,1,1,date('Y')-3)), mktime(0,0,0,1,1,date('Y')-3),mktime(23,59,59,12,31,date('Y')-3));
    echo stats_period_row(date('Y',mktime(0,0,0,1,1,date('Y')-4)), mktime(0,0,0,1,1,date('Y')-4),mktime(23,59,59,12,31,date('Y')-4));
    echo stats_period_row(date('Y',mktime(0,0,0,1,1,date('Y')-5)), mktime(0,0,0,1,1,date('Y')-5),mktime(23,59,59,12,31,date('Y')-5));
    echo "</table>\n";

    echo "<br />\n";

    $sql = "SELECT COUNT(incidents.id), incidentstatus.name FROM incidents, incidentstatus ";
    $sql .= "WHERE incidents.status = incidentstatus.id AND status != 2 AND status != 7 GROUP BY incidents.status";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    echo "<h2>Current Incidents</h2>";
    echo "<table class='vertical' align='center'>";
    if(mysql_num_rows($result) > 0)
    {
       // echo "<table align='center' class='vertical' width='20%'>";
        $openCalls = 0;
        echo "<td><table class='vertical' align='center'>";
        while($row = mysql_fetch_array($result))
        {
            echo "<tr><th>".$row['name']."</th><td class='shade2' align='left'>".$row['COUNT(incidents.id)']."</td></tr>";
            if(strpos(strtolower($row['name']), "clos") === false) $openCalls += $row['COUNT(incidents.id)'];
        }
        echo "<tr><th>Total Open</th><td class='shade2' align='left'><strong>$openCalls</strong></td></tr></table></td>";
    }
    plugin_do('statistics_table_overview');
    echo "</table>";
    mysql_free_result($result);

    //count incidents by Vendor

    $sql = "SELECT DISTINCT products.vendorid, vendors.name FROM incidents, products, vendors ";
    $sql .= "WHERE status != 2 AND status != 7 AND incidents.product = products.id AND vendors.id = products.vendorid ORDER BY vendorid";

    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    if(mysql_num_rows($result) > 1)
    {
        echo "<h2>By vendor</h2><table class='vertical' align='center'>";
        while($vendors = mysql_fetch_array($result))
        {
            $sqlVendor = "SELECT COUNT(incidents.id), incidentstatus.name FROM incidents, incidentstatus, products ";
            $sqlVendor .= "WHERE incidents.status = incidentstatus.id AND closed = 0 AND incidents.product = products.id ";
            $sqlVendor .= "AND products.vendorid = ".$vendors['vendorid']." ";
            $sqlVendor .= "GROUP BY incidents.status";

            $resultVendor = mysql_query($sqlVendor);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

            if(mysql_num_rows($resultVendor) > 0)
            {
                $openCallsVendor = 0;
                echo "<td style='vertical-align:top' align='center'><strong>".$vendors['name']."</strong>";
                echo "<table class='vertical' align='center'>";
                while($rowVendor = mysql_fetch_array($resultVendor))
                {
                    echo "<tr><th>".$rowVendor['name']."</th><td class='shade2' align='left'>".$rowVendor['COUNT(incidents.id)']."</td></tr>";
                    if(strpos(strtolower($rowVendor['name']), "clos") === false) $openCallsVendor += $rowVendor['COUNT(incidents.id)'];
                }
                echo "<tr><th>Total Open</th><td class='shade2' align='left'><strong>$openCallsVendor</strong></td></tr></table></td>";
            }
        }
        echo "</table>";
    }


    // Count incidents logged today
    $sql = "SELECT id FROM incidents WHERE opened > '$todayrecent'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $todaysincidents=mysql_num_rows($result);
    mysql_free_result($result);

    $string = "<h4>$todaysincidents Incidents logged today</h4>";
    if($todaysincidents > 0)
    {
        $string .= "<table align='center' width='50%'><tr><td colspan='2'>Assigned as follows:</td></tr>";
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
                $string .= "<small><a href=\"javascript:incident_details_window('".$irow['id']."', 'incident".$irow['id']."')\"  title=\"".stripslashes($irow['title'])."\">[".$irow['id']."]</a></small> ";
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
        $string .= "<tr><th>ID</th><th>Title</th><th>Owner</th><th>Closing status</th></tr>\n";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        while($row = mysql_fetch_array($result))
        {
            $string .= "<tr><th colspan='4' align='left'>".$row['count(incidents.id)']." Closed by ".$row['realname']."</th></tr>\n";

            $sql = "SELECT incidents.id, incidents.title, closingstatus.name ";
            $sql .= "FROM incidents, closingstatus ";
            $sql .= "WHERE incidents.closingstatus = closingstatus.id AND closed > '$todayrecent' AND incidents.owner = '".$row['owner']."' ORDER BY closed";

            $iresult = mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            while($irow = mysql_fetch_array($iresult))
            {
                $string .= "<tr><th><a href=\"javascript:incident_details_window('".$irow['id']."', 'incident".$irow['id']."')\" title='[".$irow['id']."] - ".stripslashes($irow['title'])."'>".$irow['id']."</a></th>";
                $string .= "<td class='shade2' align='left'>".$irow['title']."</td><td class='shade2' align='left'>".$row['realname']."</td><td class='shade2'>".$irow['name']."</td></tr>\n";
            }
            // $string .= "</table>\n";
        }
        $string .= "</table>\n\n";
    }

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
