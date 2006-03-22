<?php
// incident_relationships.php - Displays and allows editing of incident relationships
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

$permission=61; // View Incident Details

require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

// External variables
$id = cleanvar($_REQUEST['id']);

$title = 'Relations: '.$id . " - " . incident_title($id);
include('incident_html_top.inc.php');

include('incident/relationships.inc.php');

include('incident_html_bottom.inc.php');
?>
