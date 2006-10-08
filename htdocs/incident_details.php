<?php
// incident_details.php - Show incident details
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// This file will soon be superceded by incident.php - 20Oct05 INL

$permission=61; // View Incident Details
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

// External variables
$incidentid = cleanvar($_REQUEST['id']);
$id = $incidentid;

include('incident_html_top.inc.php');

?>

<?php

include('incident/details.inc.php');
echo "<br />";

include('incident/log.inc.php');

include('incident_html_bottom.inc.php');
?>
