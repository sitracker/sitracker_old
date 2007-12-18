<?php
// statistics.php - Over view and stats of calls logged - intended for last 24hours
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>

@include ('set_include_path.inc.php');
require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

$title=  $strTodaysStats;

$mode = cleanvar($_REQUEST['mode']);


/**
    * @author Paul Heaney
*/
function get_sql_statement($startdate,$enddate,$statementnumber,$count=TRUE)
{
    global $dbIncidents;
    if ($count) $count = "count(*)";
    else $count = "*";
    $sql[0] = "SELECT {$count} FROM `{$dbIncidents}` WHERE opened BETWEEN '{$startdate}' AND '{$enddate}'";
    $sql[1] = "SELECT {$count} FROM `{$dbIncidents}` WHERE closed BETWEEN '{$startdate}' AND '{$enddate}'";
    $sql[2] = "SELECT {$count} FROM `{$dbIncidents}` WHERE lastupdated BETWEEN '{$startdate}' AND '{$enddate}'";
    $sql[3] = "SELECT {$count} FROM `{$dbIncidents}` WHERE opened <= '{$enddate}' AND (closed >= '$startdate' OR closed = 0)";
    $sql[4] = "SELECT count(*), count(DISTINCT userid) FROM `{$dbUpdates}` WHERE timestamp >= '$startdate' AND timestamp <= '$enddate'";
    $sql[5] = "SELECT count(DISTINCT softwareid), count(DISTINCT owner) FROM `{$dbIncidents}` WHERE opened <= '{$enddate}' AND (closed >= '$startdate' OR closed = 0)";
    $sql[6] = "SELECT {$count} FROM `{$dbUpdates}` WHERE timestamp >= '$startdate' AND timestamp <= '$enddate' AND type='email'";
    $sql[7] = "SELECT {$count} FROM `{$dbUpdates}` WHERE timestamp >= '$startdate' AND timestamp <= '$enddate' AND type='emailin'";
    $sql[8] = "SELECT {$count} FROM `{$dbIncidents}` WHERE opened <= '{$enddate}' AND (closed >= '$startdate' OR closed = 0) AND priority >= 3";
    return $sql[$statementnumber];
}


/**
    * Show Open, Closed, Updated today, this week, this month etc.
    * @author Paul Heaney
*/
function count_incidents($startdate, $enddate)
{
    // Counts the number of incidents opened between a start date and an end date
    // Returns an associative array
    // 0
    $sql = get_sql_statement($startdate,$enddate,0);
    $result= mysql_query($sql);
    list($count['opened'])=mysql_fetch_row($result);
    mysql_free_result($result);

    // 1
    $sql = get_sql_statement($startdate,$enddate,1);
    $result= mysql_query($sql);
    list($count['closed'])=mysql_fetch_row($result);
    mysql_free_result($result);

    // 2
    $sql = get_sql_statement($startdate,$enddate,2);
    $result= mysql_query($sql);
    list($count['updated'])=mysql_fetch_row($result);
    mysql_free_result($result);

    // 3
    $sql = get_sql_statement($startdate,$enddate,3);
    $result= mysql_query($sql);
    list($count['handled'])=mysql_fetch_row($result);
    mysql_free_result($result);

    // 4
    $sql = get_sql_statement($startdate,$enddate,4);
    $result= mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    list($count['updates'],$count['users'])=mysql_fetch_row($result);
    mysql_free_result($result);

    // 5
    $sql = get_sql_statement($startdate,$enddate,5);
    $result= mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    list($count['skills'], $count['owners'])=mysql_fetch_row($result);
    mysql_free_result($result);

    // 6
    $sql = get_sql_statement($startdate,$enddate,6);
    $result= mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    list($count['emailtx'])=mysql_fetch_row($result);
    mysql_free_result($result);

    // 7
    $sql = get_sql_statement($startdate,$enddate,7);
    $result= mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    list($count['emailrx'])=mysql_fetch_row($result);
    mysql_free_result($result);

    // 8
    $sql = get_sql_statement($startdate,$enddate,8);
    $result= mysql_query($sql);
    list($count['higherpriority'])=mysql_fetch_row($result);
    mysql_free_result($result);

    return $count;
}


/**
    * @author Paul Heaney
    * @returns string. HTML
*/
function stats_period_row($desc, $start, $end)
{
    global $shade;
    if ($shade=='') $shade='shade1';
    $count = count_incidents($start,$end);
    if ($count['users'] > 0) $updatesperuser = @number_format($count['updates']/$count['users'], 2);
    else $updatesperuser = 0;
    if ($count['updated'] > 0) $updatesperincident = @number_format($count['updates']/$count['updated'], 2);
    else $updatesperincident = 0;
    if ($count['owners'] > 0) $incidentsperowner = @number_format($count['handled']/$count['owners'], 2);
    else $incidentsperowner = 0;
/*
    $workload = $count['handled'] + $count['emailrx'] + $count['skills'] + $count['updates'] + $count['higherpriority'];
    $resource = $count['owners'] + $count['users'] + $count['emailtx'] + ($count['opened'] - $count['closed']);
    $busyrating = ($resource / $workload * 100);
    $busyrating = @number_format($busyrating * 4.5,1);
*/
    if ($count['updated'] > 10) $freshness = ($count['updated'] / $count['handled'] * 100);
    else $freshness=$count['updated'];
    if ($count['owners'] > 0) $load = (($count['handled'] / $count['owners']) / $count['handled'] * 100);
    else $load = 0;
    if ($count['updates'] > 10) $busyness = (($count['updates'] / $count['users']) / $count['updates'] * 100);
    else $busyness=$count['updates'];
    if ($count['users'] > 0 && $count['emailtx'] > 0) $busyness2 = (($count['emailtx'] / $count['users']) / $count['handled'] * 100);
    else $busyness2 = 0;
    $activity = ($freshness+$load+$busyness+$busyness2 / 400 * 100);
    $activity = @number_format($activity,1);
    if ($activity > 100) $activity=100;
    if ($activity < 0) $activity = 0;

    $html = "<tr class='$shade'><td>$desc</td>";
    $html .= "<td><a href='{$_SERVER['PHP_SELF']}?mode=breakdown&query=0&start={$start}&end={$end}'>{$count['opened']}</a></td>";
    $html .= "<td><a href='{$_SERVER['PHP_SELF']}?mode=breakdown&query=2&start={$start}&end={$end}'>{$count['updated']}</a></td>";
    $html .= "<td><a href='{$_SERVER['PHP_SELF']}?mode=breakdown&query=1&start={$start}&end={$end}'>{$count['closed']}</a></td>";
    $html .= "<td>{$count['handled']}</td>";
    $html .= "<td>{$count['updates']}</td>";
    $html .= "<td>{$updatesperincident}</td>";
    $html .= "<td>{$count['skills']}</td>";
    $html .= "<td>{$count['owners']}</td>";
    $html .= "<td>{$count['users']}</td>";
    $html .= "<td>{$updatesperuser}</td>";
    $html .= "<td>{$incidentsperowner}</td>";
    $html .= "<td>{$count['emailrx']}</td><td>{$count['emailtx']}</td>";
    $html .= "<td>{$count['higherpriority']}</td>";
    $html .= "<td>".percent_bar($activity)."</td>";
    $html .= "</tr>\n";
    if ($shade=='shade1') $shade='shade2';
    else $shade='shade1';
    return $html;
}


/**
    * @author Paul Heaney
*/
function give_overview()
{
    global $todayrecent, $mode, $CONFIG;

    echo "<table align='center'>";
    // FIXME i18n per incident etc.
    echo "<tr><th>{$GLOBALS['strPeriod']}</th><th>{$GLOBALS['strOpened']}</th><th>{$GLOBALS['strUpdated']}</th><th>{$GLOBALS['strClosed']}</th><th>{$GLOBALS['strHandled']}</th>";
    echo "<th>{$GLOBALS['strUpdates']}</th><th>per incident</th><th>{$GLOBALS['strSkills']}</th><th>{$GLOBALS['strOwners']}</th><th>{$GLOBALS['strUsers']}</th>";
    echo "<th>upd per user</th><th>inc per owner</th><th>{$GLOBALS['strEmail']} Rx</th><th>{$GLOBALS['strEmail']} Tx</th><th>{$GLOBALS['strHigherPriority']}</th>";
    echo "<th>{$GLOBALS['strActivity']}</th></tr>\n";
    // FIXME i18n Yesterday
    // FIXME i18n date ranges
    echo stats_period_row("<a href='{$_SERVER['PHP_SELF']}?mode=daybreakdown&offset=0'>{$GLOBALS['strToday']}</a>", mktime(0,0,0,date('m'),date('d'),date('Y')),mktime(23,59,59,date('m'),date('d'),date('Y')));
    echo stats_period_row("<a href='{$_SERVER['PHP_SELF']}?mode=daybreakdown&offset=1'>{$GLOBALS['strYesterday']}</a>", mktime(0,0,0,date('m'),date('d')-1,date('Y')),mktime(23,59,59,date('m'),date('d')-1,date('Y')));
    echo stats_period_row("<a href='{$_SERVER['PHP_SELF']}?mode=daybreakdown&offset=2'>".date('l',mktime(0,0,0,date('m'),date('d')-2,date('Y')))."</a>", mktime(0,0,0,date('m'),date('d')-2,date('Y')),mktime(23,59,59,date('m'),date('d')-2,date('Y')));
    echo stats_period_row("<a href='{$_SERVER['PHP_SELF']}?mode=daybreakdown&offset=3'>".date('l',mktime(0,0,0,date('m'),date('d')-3,date('Y')))."</a>", mktime(0,0,0,date('m'),date('d')-3,date('Y')),mktime(23,59,59,date('m'),date('d')-3,date('Y')));
    echo stats_period_row("<a href='{$_SERVER['PHP_SELF']}?mode=daybreakdown&offset=4'>".date('l',mktime(0,0,0,date('m'),date('d')-4,date('Y')))."</a>", mktime(0,0,0,date('m'),date('d')-4,date('Y')),mktime(23,59,59,date('m'),date('d')-4,date('Y')));
    echo stats_period_row("<a href='{$_SERVER['PHP_SELF']}?mode=daybreakdown&offset=5'>".date('l',mktime(0,0,0,date('m'),date('d')-5,date('Y')))."</a>", mktime(0,0,0,date('m'),date('d')-5,date('Y')),mktime(23,59,59,date('m'),date('d')-5,date('Y')));
    echo stats_period_row("<a href='{$_SERVER['PHP_SELF']}?mode=daybreakdown&offset=6'>".date('l',mktime(0,0,0,date('m'),date('d')-6,date('Y')))."</a>", mktime(0,0,0,date('m'),date('d')-6,date('Y')),mktime(23,59,59,date('m'),date('d')-6,date('Y')));
    echo "<tr><td colspan='*'></td></tr>";
    echo stats_period_row('Past 7 days', mktime(0,0,0,date('m'),date('d')-6,date('Y')),mktime(23,59,59,date('m'),date('d'),date('Y')));
    echo stats_period_row('Previous 7 days', mktime(0,0,0,date('m'),date('d')-13,date('Y')),mktime(23,59,59,date('m'),date('d')-7,date('Y')));
    echo "<tr><td colspan='*'></td></tr>";
    if ($mode=='detail')
    {
        echo stats_period_row('This month', mktime(0,0,0,date('m'),1,date('Y')),mktime(23,59,59,date('m'),date('d'),date('Y')));
        echo stats_period_row('Last month', mktime(0,0,0,date('m')-1,date('d'),date('Y')),mktime(23,59,59,date('m'),0,date('Y')));
        echo stats_period_row(date('F y',mktime(0,0,0,date('m')-2,1,date('Y'))), mktime(0,0,0,date('m')-2,date('d'),date('Y')),mktime(23,59,59,date('m')-1,0,date('Y')));
        echo stats_period_row(date('F y',mktime(0,0,0,date('m')-3,1,date('Y'))), mktime(0,0,0,date('m')-3,date('d'),date('Y')),mktime(23,59,59,date('m')-2,0,date('Y')));
        echo stats_period_row(date('F y',mktime(0,0,0,date('m')-4,1,date('Y'))), mktime(0,0,0,date('m')-4,date('d'),date('Y')),mktime(23,59,59,date('m')-3,0,date('Y')));
        echo stats_period_row(date('F y',mktime(0,0,0,date('m')-5,1,date('Y'))), mktime(0,0,0,date('m')-5,date('d'),date('Y')),mktime(23,59,59,date('m')-4,0,date('Y')));
        echo stats_period_row(date('F y',mktime(0,0,0,date('m')-6,1,date('Y'))), mktime(0,0,0,date('m')-6,date('d'),date('Y')),mktime(23,59,59,date('m')-5,0,date('Y')));
        echo "<tr><td colspan='*'></td></tr>";
        echo stats_period_row('This year', mktime(0,0,0,1,1,date('Y')),mktime(23,59,59,date('m'),date('d'),date('Y')));
        echo stats_period_row('Last year', mktime(0,0,0,1,1,date('Y')-1),mktime(23,59,59,12,31,date('Y')-1));
        echo stats_period_row(date('Y',mktime(0,0,0,1,1,date('Y')-2)), mktime(0,0,0,1,1,date('Y')-2),mktime(23,59,59,12,31,date('Y')-2));
        echo stats_period_row(date('Y',mktime(0,0,0,1,1,date('Y')-3)), mktime(0,0,0,1,1,date('Y')-3),mktime(23,59,59,12,31,date('Y')-3));
        echo stats_period_row(date('Y',mktime(0,0,0,1,1,date('Y')-4)), mktime(0,0,0,1,1,date('Y')-4),mktime(23,59,59,12,31,date('Y')-4));
        echo stats_period_row(date('Y',mktime(0,0,0,1,1,date('Y')-5)), mktime(0,0,0,1,1,date('Y')-5),mktime(23,59,59,12,31,date('Y')-5));
    }
    echo "</table>\n";

    echo "<br />\n";

    $sql = "SELECT COUNT(i.id), incidentstatus.name FROM `{$dbIncidents}` AS i, incidentstatus ";
    $sql .= "WHERE i.status = incidentstatus.id AND status != 2 AND status != 7 GROUP BY i.status";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    echo "<h2>{$GLOBALS['strCurrentlyOpen']}</h2>";
    echo "<table class='vertical' align='center'>";
    if (mysql_num_rows($result) > 0)
    {
       // echo "<table align='center' class='vertical' width='20%'>";
        $openCalls = 0;
        echo "<td><table class='vertical' align='center'>";
        while ($row = mysql_fetch_array($result))
        {
            echo "<tr><th>".$row['name']."</th><td class='shade2' align='left'>".$row['COUNT(incidents.id)']."</td></tr>";
            if (strpos(strtolower($row['name']), "clos") === false) $openCalls += $row['COUNT(incidents.id)'];
        }
        echo "<tr><th>{$strTotal}</th><td class='shade2' align='left'><strong>$openCalls</strong></td></tr></table></td>";
    }
    plugin_do('statistics_table_overview');
    echo "</table>";
    mysql_free_result($result);

    //count incidents by Vendor
    $sql = "SELECT DISTINCT software.vendorid, vendors.name FROM `{$dbIncidents}` AS i, software, vendors ";
    $sql .= "WHERE (status != 2 AND status != 7) AND i.softwareid = software.id AND vendors.id = software.vendorid ORDER BY vendorid";

    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    if (mysql_num_rows($result) > 1)
    {
        echo "<h2>By vendor</h2><table class='vertical' align='center'>";
        while ($vendors = mysql_fetch_array($result))
        {
            // This should use the software and relate to the product and then to the vendor
            $sqlVendor = "SELECT COUNT(i.id), incidentstatus.name FROM `{$dbIncidents}` AS i, incidentstatus, software ";
            $sqlVendor .= "WHERE i.status = incidentstatus.id AND closed = 0 AND i.softwareid = software.id ";
            $sqlVendor .= "AND software.vendorid = ".$vendors['vendorid']." ";
            $sqlVendor .= "GROUP BY i.status";

            $resultVendor = mysql_query($sqlVendor);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

            if (mysql_num_rows($resultVendor) > 0)
            {
                $openCallsVendor = 0;
                echo "<td style='vertical-align:top' align='center'><strong>".$vendors['name']."</strong>";
                echo "<table class='vertical' align='center'>";
                while ($rowVendor = mysql_fetch_array($resultVendor))
                {
                    echo "<tr><th>".$rowVendor['name']."</th><td class='shade2' align='left'>".$rowVendor['COUNT(incidents.id)']."</td></tr>";
                    if (strpos(strtolower($rowVendor['name']), "clos") === false) $openCallsVendor += $rowVendor['COUNT(incidents.id)'];
                }
                // FIXME i18n Total open
                echo "<tr><th>{$strTotal} Open</th><td class='shade2' align='left'><strong>$openCallsVendor</strong></td></tr></table></td>";
            }
        }
        echo "</table>";
    }


    // Count incidents logged today
    $sql = "SELECT id FROM `{$dbIncidents}` WHERE opened > '$todayrecent'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $todaysincidents=mysql_num_rows($result);
    mysql_free_result($result);

    $string = "<h4>$todaysincidents Incidents logged today</h4>"; // FIXME i18n Incidents logged today, assigned as follows
    if ($todaysincidents > 0)
    {
        $string .= "<table align='center' width='50%'><tr><td colspan='2'>Assigned as follows:</td></tr>";
        $sql = "SELECT count(i.id), realname, users.id AS owner ";
        $sql .= "FROM `{$dbIncidents}` AS i, users ";
        $sql .= "WHERE opened > '$todayrecent' AND i.owner = users.id "
        $sql .= "GROUP BY owner DESC";

        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        while ($row = mysql_fetch_array($result))
        {
            $sql = "SELECT id, title FROM `{$dbIncidents}` WHERE opened > '$todayrecent' AND owner = '".$row['owner']."'";

            $string .= "<tr><th>".$row['count(incidents.id)']."</th>";
            $string .= "<td class='shade2' align='left'><a href='incidents.php?user=".$row['owner']."&amp;queue=1&amp;type=support'>".$row['realname']."</a> ";

            $iresult = mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            while ($irow = mysql_fetch_array($iresult))
            {
                $string .= "<small><a href=\"javascript:incident_details_window('".$irow['id']."', 'incident".$irow['id']."')\"  title=\"".$irow['title']."\">[".$irow['id']."]</a></small> ";
            }

            $string .= "</td></tr>";
        }
        $string .= "</table>";
    }


    // Count incidents closed today
    $sql = "SELECT COUNT(id) FROM `{$dbIncidents}` WHERE closed > '$todayrecent'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    list($todaysclosed)=mysql_fetch_row($result);

    $string .= "<h4>$todaysclosed Incidents closed today</h4>"; // FIXME i18n closed today
    if ($todaysclosed > 0)
    {
        $sql = "SELECT count(i.id), realname, users.id AS owner FROM `{$dbIncidents}` AS i LEFT JOIN users ON i.owner = users.id WHERE closed > '$todayrecent' GROUP BY owner";
        $string .= "<table align='center' width='50%'>";
        $string .= "<tr><th>ID</th><th>Title</th><th>Owner</th><th>Closing status</th></tr>\n";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        while ($row = mysql_fetch_array($result))
        {
            $string .= "<tr><th colspan='4' align='left'>".$row['count(incidents.id)']." Closed by ".$row['realname']."</th></tr>\n";

            $sql = "SELECT i.id, i.title, cs.name ";
            $sql .= "FROM `{$dbIncidents}` AS i, `{$dbClosingStatus}` AS cs ";
            $sql .= "WHERE i.closingstatus = cs.id AND closed > '$todayrecent' AND i.owner = '".$row['owner']."' ORDER BY closed";

            $iresult = mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            while ($irow = mysql_fetch_array($iresult))
            {
                $string .= "<tr><th><a href=\"javascript:incident_details_window('".$irow['id']."', 'incident".$irow['id']."')\" title='[".$irow['id']."] - ".$irow['title']."'>".$irow['id']."</a></th>";
                $string .= "<td class='shade2' align='left'>".$irow['title']."</td><td class='shade2' align='left'>".$row['realname']."</td><td class='shade2'>".$irow['name']."</td></tr>\n";
            }
            // $string .= "</table>\n";
        }
        $string .= "</table>\n\n";
    }

    mysql_free_result($result);

    $string .= "<h2>{$GLOBALS['strCustomerFeedback']}</h2>";
    $totalresult=0;
    $numquestions=0;
    $qsql = "SELECT * FROM feedbackquestions WHERE formid='1' AND type='rating' ORDER BY taborder";
    $qresult = mysql_query($qsql);
    if (mysql_error()) trigger_error(mysql_error(), E_USER_ERROR);

    if (mysql_num_rows($qresult) >= 1)
    {
        $string .= "<table align='center' class='vertical'>";
        while ($qrow = mysql_fetch_object($qresult))
        {
            $numquestions++;
            $string .= "<tr><th>Q{$qrow->taborder}: {$qrow->question}</th>";
            $sql = "SELECT * FROM feedbackrespondents, incidents, users, feedbackresults ";
            $sql .= "WHERE feedbackrespondents.incidentid=incidents.id ";
            $sql .= "AND incidents.owner=users.id ";
            $sql .= "AND feedbackrespondents.id=feedbackresults.respondentid ";
            $sql .= "AND feedbackresults.questionid='$qrow->id' ";
            $sql .= "AND feedbackrespondents.completed = 'yes' \n";
            $sql .= "ORDER BY incidents.owner, incidents.id";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(), E_USER_ERROR);
            $numsurveys=mysql_num_rows($result);
            $numresults=0;
            $cumul=0;
            $percent=0;
            $average=0;
            while ($row = mysql_fetch_object($result))
            {
                if (!empty($row->result))
                {
                    $cumul+=$row->result;
                    $numresults++;
                }
            }
            if ($numresults>0) $average=number_format(($cumul/$numresults), 2);
            $percent =number_format((($average -1) * (100 / ($CONFIG['feedback_max_score'] -1))), 0);
            $totalresult+=$average;
            $string .= "<td>{$average}</td></tr>";
            // <strong>({$percent}%)</strong><br />";
        }
        $string .= "</table>\n";
        $total_average=number_format($totalresult/$numquestions,2);
        $total_percent=number_format((($total_average -1) * (100 / ($CONFIG['feedback_max_score'] -1))), 0);
        if ($total_percent < 0) $total_percent=0;
        $string .= "<p align='center'>{$GLOBALS['strPositivity']}: {$total_average} <strong>({$total_percent}%)</strong> from $numsurveys results.</p>";
        $surveys+=$numresults;
    }
    return $string;
}

include ('htmlheader.inc.php');

switch ($mode)
{
    case 'breakdown':
        $query = $_REQUEST['query'];
        $startdate = $_REQUEST['start'];
        $enddate = $_REQUEST['end'];
        include ('statistics/breakdown.inc.php');
        break;
    case 'daybreakdown':
        $offset = $_REQUEST['offset'];
        include ('statistics/daybreakdown.inc.php');
        break;
    case 'overview': //this is the default so just fall though
    default:
        echo "<h2>$title - Overview</h2>";
        echo give_overview();
        break;
}




include ('htmlfooter.inc.php');
?>