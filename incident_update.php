<?php
// update_incident.php - For for logging updates to an incident
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

$lib_path = dirname( __FILE__ ).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR;
$permission = 8; // Update Incident
require ($lib_path.'db_connect.inc.php');
require ($lib_path.'functions.inc.php');

$disable_priority = TRUE;

// This page requires authentication
require ($lib_path.'auth.inc.php');

// External Variables
// $bodytext = cleanvar($_REQUEST['bodytext'],FALSE,FALSE);
$bodytext = cleanvar($_REQUEST['bodytext'], FALSE, TRUE);
$id = cleanvar($_REQUEST['id']);
$incidentid = $id;
$action = cleanvar($_REQUEST['action']);

include ('inc/incident_update.inc.php');

include ('inc/incident_html_bottom.inc.php');
exit;

?>
