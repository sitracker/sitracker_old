<?php
// incident_service_levels.php - Display sla status
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

@include ('set_include_path.inc.php');
$permission = 6; // View Incidents

require ('db_connect.inc.php');
require ('functions.inc.php');

require ('auth.inc.php');
// soon to be replaced by incident/sla.inc.php

$incidentid = cleanvar($_REQUEST['id']);
$id = $incidentid;

$title = 'Service Levels';

// Retrieve incident
// extract incident details
$sql  = "SELECT *, i.id AS incidentid, ";
$sql .= "c.id AS contactid ";
$sql .= "FROM `{$dbIncidents}` AS i, `{$dbContacts}` AS c ";
$sql .= "WHERE (i.id='{$incidentid}' AND i.contact = c.id) ";
$sql .= " OR i.contact=NULL ";
$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(), E_USER_WARNING);
$incident = mysql_fetch_object($result);
$site_name = site_name($incident->siteid);
$product_name = product_name($incident->product);
if ($incident->softwareid > 0) $software_name=software_name($incident->softwareid);
$servicelevel_id = maintenance_servicelevel($incident->maintenanceid);
$servicelevel_tag = $incident->servicelevel;
if ($servicelevel_tag=='') $servicelevel_tag = servicelevel_id2tag(maintenance_servicelevel($incident->maintenanceid));
$servicelevel_name = servicelevel_name($servicelevelid);
$opened_for = format_seconds(time() - $incident->opened);


include ('incident_html_top.inc.php');
include ('incident/sla.inc.php');

include ('incident_html_bottom.inc.php');
exit;

?>
