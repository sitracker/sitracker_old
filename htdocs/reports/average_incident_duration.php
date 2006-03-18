<?php
// average_incident_duration.php - Report showing average duration of incidents
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author:   Ivan Lucas
// Email:    ivan.lucas@salfordsoftware.co.uk
// Comments: How long do we take to close incidents?

// FIXME

$permission=6; // view incidents

require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

$id = cleanvar($_REQUEST['id']);
$mode = cleanvar($_REQUEST['mode']);

include('htmlheader.inc.php');
if ($mode=='site') echo "<h2>".site_name($id)."'s Support Incidents</h2>";
else echo "<h2>".contact_realname($id)."'s Support Incidents</h2>";

if ($mode=='site') $sql = "SELECT *, (closed - opened) AS duration_closed, incidents.id AS incidentid FROM incidents, contacts WHERE incidents.contact=contacts.id AND status='2' AND owner='$owner' ORDER BY opened DESC";
else $sql = "SELECT *, (closed - opened) AS duration_closed, incidents.id AS incidentid FROM incidents WHERE status='2' AND owner='$owner' ORDER BY opened DESC";
$result = mysql_query($sql);

echo "<table class='tablelist' align='center' border=0 bordercolor=#FFFFFF cellpadding=2 cellspacing=0>";
echo "<tr>";
echo "<th class='shade2'>Date</th>";
echo "<th class='shade2'>Incident ID</th>";
echo "<th class='shade2'>Title</th>";
if ($mode=='site') echo "<th class='shade2'>Contact</th>";
echo "<th class='shade2'>Status</th>";
echo "<th class='shade2'>Engineer</th>";
echo "</tr>";
$shade='shade1';
$totalduration=0;
$countclosed=0;
while ($row=mysql_fetch_object($result))
{
    if ($row->status==2) $shade='expired';
    else $shade='shade1';
    echo "<tr class='$shade'>";
    if ($row->status==2)
    {
        echo "<td>Closed: ".date('j M Y', $row->closed).' ('.format_seconds($row->duration_closed).")</td>";
        $totalduration=$totalduration+$row->duration_closed;
        $countclosed++;
    }
    else echo "<td>Opened: ".date('j M Y',$row->opened)."</td>";
    echo "<td>".$row->incidentid."</td>";
    // title
    echo "<td>";
    echo "<a href=\"javascript:incident_details_window('".$row->id."','incident".$row->id."')\">";
    if (trim($row->title) !='') echo $row->title; else echo 'Untitled';
    echo "</a>";
    echo "</td>";
    if ($mode=='site') echo "<td>".contact_realname($row->contact)."</td>";
    if ($row->status==2) echo "<td>Closed, ".closingstatus_name($row->closingstatus)."</td>";
    else echo "<td>".incidentstatus_name($row->status)."</td>";
    echo "<td>".user_realname($row->owner)."</td>";
    echo "</tr>\n";
}
echo "</table>\n";
if (mysql_num_rows($result)>=1)
{
    echo "<p align='center'>Average incident duration: ".format_seconds($totalduration/$countclosed)."</p>";
}
else
{
    echo "<p align='center'>None</p>";
}
include('htmlfooter.inc.php');
?>