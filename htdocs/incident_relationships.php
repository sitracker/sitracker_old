<?php
// incident_relationships.php - Displays and allows editing of incident relationships
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

@include ('set_include_path.inc.php');
$permission = 61; // View Incident Details

require ($lib_path.'db_connect.inc.php');
require ($lib_path.'functions.inc.php');

// This page requires authentication
require ($lib_path.'auth.inc.php');

// External variables
$id = cleanvar($_REQUEST['id']);

$title = 'Relations';
include ('incident_html_top.inc.php');

include ('incident/relationships.inc.php');

include ('incident_html_bottom.inc.php');
?>
