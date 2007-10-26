<?php
// related_incidents.php - try to find incidents that look similar to the current one
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Kieran Hogg <kieran_hogg[at]users.sourceforge.net>

$permission=61; // View Incident Details
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

// External variables
$incidentid = cleanvar($_REQUEST['id']);
$id = $incidentid;
$title = 'Related Incidents';
$searchmode = 'related';

include('incident_html_top.inc.php');

$search_string = $incident->title;
$search_domain = 'incidents';
$software = $incident->softwareid;
include('search.inc.php');
include('incident_html_bottom.inc.php');
