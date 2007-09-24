<?php
// incident_service_levels.php - Display sla status
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Authro: Ivan Lucas

$permission=6; // View Incidents

require('db_connect.inc.php');
require('functions.inc.php');

require('auth.inc.php');
// soon to be replaced by incident/sla.inc.php

$incidentid = cleanvar($_REQUEST['id']);
$id = $incidentid;

$title = 'Service Levels';

// Retrieve incident
// extract incident details
$sql  = "SELECT *, incidents.id AS incidentid, ";
$sql .= "contacts.id AS contactid ";
$sql .= "FROM incidents, contacts ";
$sql .= "WHERE (incidents.id='{$incidentid}' AND incidents.contact=contacts.id) ";
$sql .= " OR incidents.contact=NULL ";
$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(), E_USER_ERROR);
$incident = mysql_fetch_object($result);
$site_name=site_name($incident->siteid);
$product_name=product_name($incident->product);
if ($incident->softwareid > 0) $software_name=software_name($incident->softwareid);
$servicelevel_id=maintenance_servicelevel($incident->maintenanceid);
$servicelevel_tag = $incident->servicelevel;
if ($servicelevel_tag=='') $servicelevel_tag = servicelevel_id2tag(maintenance_servicelevel($incident->maintenanceid));
$servicelevel_name=servicelevel_name($servicelevelid);
$opened_for=format_seconds(time() - $incident->opened);


include('incident_html_top.inc.php');
include('incident/sla.inc.php');

//start status summary
$sql = "SELECT updates.id as updatesid, incidentid, userid, type, timestamp, currentstatus, incidentstatus.id, incidentstatus.name as name ";
$sql .= "FROM updates, incidentstatus ";
$sql .= " WHERE incidentid='{$incidentid}' ";
$sql .= " AND updates.currentstatus=incidentstatus.id ";
$sql .= " ORDER BY timestamp ASC";

$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(), E_USER_ERROR);

$updatearray = array();
$last = -1;
$laststatus;
while($row = mysql_fetch_object($result))
{
    $updatearray[$row->currentstatus]['name'] = $row->name;
    if($last == -1)
    {
        $last = $row->timestamp;
        $updatearray[$row->currentstatus]['time'] = 0;
    }
    else
        $updatearray[$row->currentstatus]['time'] = calculate_working_time($last, $row->timestamp);
    $laststatus = $row->currentstatus;    
}

$updatearray[$laststatus]['time'] += calculate_working_time($updatearray[$laststatus]['time'], time());

echo "<h3>Status Summary</h3>";
echo "<table align='center'>";
echo "<tr><th>Status</th><th>Time</th></tr>\n";
$data = array();
$legends;
foreach($updatearray as $row)
{
    echo "<tr><td>".$row['name']. "</td><td>".format_seconds($row['time'])."</td></tr>";
    array_push($data, $row['time']);
    $legends .= $row['name']."|";
}

if (extension_loaded('gd'))
{
    $data = implode('|',$data);
    $title = urlencode('Time in each Status');
    echo "<div style='text-align:center;'>";
    echo "<img src='chart.php?type=pie&data=$data&legends=$legends&title=$title' />";
    echo "</div>";
}


include('incident_html_bottom.inc.php');
exit;

/*
$title = 'Service Levels: '.$id . " - " . incident_title($id);
include('incident_html_top.inc.php');
// extract incident details
$sql  = "SELECT incidents.id AS incidentid, owner, title, contact, externalid, externalengineer, externalemail, maintenanceid, priority, status, type, product, productversion, productservicepacks, contacts.id AS contactid, surname, email, phone, fax, address1, opened, lastupdated, timeofnextaction, initialresponseminutes, determinationminutes, planminutes, resdays, regularcontactdays, ($now - lastupdated) AS timesincelastupdate ";
$sql .= "FROM incidents, contacts, priority ";
$sql .= "WHERE (incidents.id='$id' AND incidents.contact=contacts.id AND incidents.priority=priority.id) OR incidents.contact=NULL ";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
$incident = mysql_fetch_array($result);
$opened_string = date("D jS M Y @ g:i A", $incident["opened"]);
$lastupdated_string = date("D jS M Y @ g:i A", $incident["lastupdated"]);
$now = time();
if ($incident["timeofnextaction"] == 0) $timetonextaction_string = "None";
else
{
    if (($incident["timeofnextaction"] - $now) > 0)
    {
        $timetonextaction_string = format_seconds($incident["timeofnextaction"] - $now);
        $timetonextaction_date = date("D jS M Y @ g:i A", $incident["timeofnextaction"]);
        $timetonextaction_string .= "($timetonextaction_date)";
    }
    else
    {
        $timetonextaction_string = "<span class=\"expired\">Now ";
        $timetonextaction_date = date("D jS M Y @ g:i A", $incident["timeofnextaction"]);
        $timetonextaction_string .= "($timetonextaction_date)</span>";
    }
}

// get service level
$servicelevel=maintenance_servicelevel($incident['maintenanceid']);

$sql = "SELECT * FROM servicelevels WHERE id='$servicelevel' AND priority='{$incident['priority']}' ";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
$level = mysql_fetch_object($result);

?>

<h2>Service Level Summary</h2>
<table>
<tr><td align='right' class='shade1' width='150'><b>Incident</b>:</td><td class='shade1' width='300'><p class=large><?php echo $id; ?></p></td></tr>
<tr><td align='right' class='shade2' width='150'><b>Title</b>:</td><td class='shade2' width='300'><?php echo $incident['title']; ?></td></tr>
</table>
<p></p>

<table>
<tr><td align='right' class='shade1' width='150'><b>Service Level</b>:</td><td class='shade1' width='300'><p class=large><?php echo servicelevel_name($servicelevel); ?></p></td></tr>
<tr><td align='right' class='shade1' width='150'><b>Current Priority</b>:</td><td class='shade1' width='300'><p class=large><?php echo priority_name($incident["priority"]) ?></p></td></tr>
<?php
echo "<tr><td align='center' class='shade1' colspan='2'><strong>Service Level Targets:</strong></td></tr>\n";
echo "<tr><td align='right' class='shade1' width='150'><b>Initial Response</b>:</td><td class='shade2' width='300'> within ".format_workday_minutes($level->initial_response_mins)."</td></tr>\n";
echo "<tr><td align='right' class='shade1' width='150'><b>Problem Definition</b>:</td><td class='shade2' width='300'> within ".format_workday_minutes($level->prob_determ_mins)."</td></tr>\n";
echo "<tr><td align='right' class='shade1' width='150'><b>Action Plan</b>:</td><td class='shade2' width='300'> within ".format_workday_minutes($level->action_plan_mins)."</td></tr>\n";
echo "<tr><td align='right' class='shade1' width='150'><b>Resolution</b>:</td><td class='shade2' width='300'> within {$level->resolution_days} working days</td></tr>\n";

echo "<tr><td align='center' class='shade1' colspan='2'><strong>Actual Service Level History:</strong></td></tr>\n";
echo "<tr><td align='right' class='shade1' width='150'><b>Opened</b>:</td><td class='shade2' width='300'>".date("D jS M Y @ g:i A", $incident["opened"])."</td></tr>\n";

$sql = "SELECT * FROM updates WHERE type='slamet' AND incidentid='$id' ORDER BY id ASC, timestamp ASC";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

while ($history = mysql_fetch_object($result))
{
echo "<tr><td align='right' class='shade1' width='150'><b>".target_type_name($history->sla)."</b>:</td><td class='shade2' width='300'>".date("D jS M Y @ g:i A", $history->timestamp)." by ".user_realname($history->userid)."</td></tr>\n";
}


// Target table
/*
$sql = "SELECT * FROM updates WHERE type='slamet' AND incidentid='$id' ORDER BY id ASC";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

if (mysql_num_rows($result) >= 1)
{
echo "<table align='center' border='1' bordercolor='#FFFFFF' cellpadding='2' cellspacing='0' width='450'>\n";
echo "<tr><th align='center' class='shade1' colspan='3'><b>Target Table</b></td></tr>";
echo "<tr><td align='right' class='shade1' width='150'>&nbsp;</td><td class='shade1' width='150' align='center'><b>Target</b></td><td width='150' class='shade1' align='center'><b>Actual</b></td></tr>";
while ($targetrow = mysql_fetch_object($result))
{
    echo "<tr><td align='right' class='shade1' width='150'><b>".target_type_name($targetrow->type)."</b>:</td><td class='shade2' width='150'>";
    echo format_seconds($targetrow->targetval);
    echo "</td><td width='150' class='shade2'>";
    if ($targetrow->met < 1)
    {
    //if ($targetrow->time < $now) echo "<i style='color: red;'>".format_seconds($now - $targetrow->time)." Overdue</i>";
    //else echo format_seconds($targetrow->time-$now)." Left";
    }
    else
    echo format_seconds($targetrow->met - $targetrow->time);
    echo "</td></tr>";
}

echo "</table>";
}
else
{
echo "<p align='center'>This incident does not have any associated service targets.</p>";
}
//
include('incident_htmlfooter.inc.php');
*/
?>
