<?php
// incident_attachments.php -
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

// INL 2Nov05
// This file will be superceded by htdocs/incidents/files.inc.php

@include ('set_include_path.inc.php');
$permission = 62; // View incident attachments

require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

// External variables
$id = cleanvar($_REQUEST['id']);
$incidentid=$id;

$title = $strFiles;
include ('incident_html_top.inc.php');

// append incident number to attachment path to show this users attachments
$incident_attachment_fspath = $CONFIG['attachment_fspath'] . $id;

include ('incident/files.inc.php');

include ('incident_html_bottom.inc.php');

?>
