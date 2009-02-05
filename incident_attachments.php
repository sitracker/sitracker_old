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

$lib_path = dirname( __FILE__ ).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR;
$permission = 62; // View incident attachments

require ($lib_path.'db_connect.inc.php');
require ($lib_path.'functions.inc.php');

// This page requires authentication
require ($lib_path.'auth.inc.php');

// External variables
$id = cleanvar($_REQUEST['id']);
$incidentid=$id;

$title = $strFiles;
include ('inc/incident_html_top.inc.php');

// append incident number to attachment path to show this users attachments
$incident_attachment_fspath = $CONFIG['attachment_fspath'] . $id;

include ('inc/incident_files.inc.php');

include ('inc/incident_html_bottom.inc.php');

?>
