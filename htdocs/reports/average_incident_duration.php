<?php
// average_incident_duration.php - Report showing average duration of incidents
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Authors: Ivan Lucas <ivanlucas[at]users.sourceforge.net> & Tom Gerrard
//
// Comments: How long do we take to close incidents?

$title='Average Incident Duration';
$permission=37; // Run Reports

require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

$id = cleanvar($_REQUEST['id']);
$mode = cleanvar($_REQUEST['mode']);

// Increment selects the number of months to group together
if (empty($_REQUEST['increment'])) $increment = 1;
else $increment = cleanvar($_REQUEST['increment']);

if (empty($_REQUEST['states'])) $states = array('2,6,7,8');
else $states = explode(',',$_REQUEST['states']);

function count_incident_owners($incidentid)
{
    $sql = "SELECT count(DISTINCT userid) FROM updates WHERE incidentid='$incidentid' AND userid!=0 GROUP BY userid";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    list($unique_users) = mysql_fetch_row($result);
    return $unique_users;
}



function average_incident_duration($start,$end,$states)
{
    // Returns number of closed incidents that were opened within the period giving
    // the average duration in minutes
    // and the average worked time in minutes
    $sql = "SELECT *, (closed - opened) AS duration_closed, incidents.id AS incidentid ";
    $sql .= "FROM incidents ";
    $sql .= "WHERE status='2' ";
    if ($start > 0) $sql .= "AND opened >= $start ";
    if ($end > 0) $sql .= "AND opened <= $end ";

    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

    $totalduration=0;
    $totalworkingduration=0;
    $countclosed=0;
    $total_unique_owners=0;
    while ($row=mysql_fetch_object($result))
    {
        $working_time=calculate_incident_working_time($row->incidentid, $row->opened, $row->closed, $states);
        if ($working_time > 0)
        {
            $totalduration=$totalduration+$row->duration_closed;
            $totalworkingduration=$totalworkingduration+$working_time;
            $countclosed++;
        }
        $total_unique_owners += count_incident_owners($row->incidentid);
    }
    $average_owners = ($countclosed == 0) ? 0 : ($total_unique_owners / $countclosed);
    $average_incident_duration = ($countclosed == 0) ? 0 : ($totalduration / $countclosed) / 60;
    $average_worked_minutes = ($countclosed == 0) ? 0 : $totalworkingduration / $countclosed;

    return array($countclosed, $average_incident_duration, $average_worked_minutes,$total_unique_owners);
}


// get the first date
$sql = "SELECT opened FROM incidents ORDER BY id ASC LIMIT 1";
$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
list($firstdate)=mysql_fetch_row($result);

$current_time=$firstdate;

$html .= "<h2>$title</h2>";
$html .= "<table align='center'>";
$html .= "<tr><th>Period</th><th># Incidents</th><th>Total Duration</th><th>Worked Time</th><th>Users</th></tr>\n";
$csv .= "Period,# Incidents,Total Duration,Worked Time\n";
$shade='shade1';
while ($current_time<time()) {

  $current_month=date('m', $current_time);
  $current_year=date('Y', $current_time);

  $next_month=$current_month+$increment;
  $next_year=$current_year;
  if ($next_month>12) {
    $next_year++;
    $next_month%=12;
  }

  $next_time=mktime(0,0,0,$next_month,1,$next_year);

  $stats=average_incident_duration($current_time,$next_time,$states);


  $html .= "<tr class='$shade'>";
  $html .= "<td>".date('F Y',mktime(0,0,0,$current_month,1,$current_year));
  if ($next_month > $current_month+1 AND $next_year==$current_year)  $html .= " - ".date('F Y',mktime(0,0,0,$next_month,1,$next_year))."</td>";
  $html .= "<td>{$stats[0]}</td>";
  $html .= "<td>".format_seconds($stats[1]*60)."</td>";
  $html .= "<td>".round($stats[2]/60)." hours</td>";
  $html .= "<td>{$stats[3]}</td>";
  $html .= "</tr>\n";
  $csv .= date('F Y',mktime(0,0,0,$current_month,1,$current_year))." - ".date('F Y',mktime(0,0,0,$next_month,1,$next_year));
  $csv .= ",{$stats[0]},".($stats[1]/60).",".round($stats[2]/60)."\n";
  if ($shade=='shade1') $shade='shade2';
  else $shade='shade1';
  $current_time=$next_time;

}
$html .= "</table>";
$html .= "<p align='center'><a href='{$_SERVER['PHP_SELF']}?mode={$mode}&amp;output=csv'>Save this report in CSV format</a></p>";

if ($_REQUEST['output']=='csv')
{
    // --- CSV File HTTP Header
    header("Content-type: text/csv\r\n");
    header("Content-disposition-type: attachment\r\n");
    header("Content-disposition: filename=average_incident_duration_{$increment}_months.csv");
    echo $csv;
}
else
{
    include('htmlheader.inc.php');
    echo $html;
    include('htmlfooter.inc.php');
}


?>