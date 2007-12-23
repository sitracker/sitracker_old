<?php
// help.php - Get context sensitive help
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

@include ('set_include_path.inc.php');
$permission = 26; // Help
require ('db_connect.inc.php');
require ('functions.inc.php');
$title = "Help";

// This page requires authentication
require ('auth.inc.php');

// External variables
$id = cleanvar($_REQUEST['id']);

include ('htmlheader.inc.php');
journal(CFG_LOGGING_MAX, 'Help Viewed', "Help document $id was viewed", CFG_JOURNAL_OTHER, $id);
echo "<h2>".permission_name($id)." {$strHelp}</h2>";
echo "<div id='help'>";
if ($id<0 OR $id>200 OR $id=='' OR strlen($id)>3) $id=0;
include ("help/help-$id.inc.php");
echo "</div>";

include ('htmlfooter.inc.php');
?>
