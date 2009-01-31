<?php
// unlock_update.php - Unlocks incident updates
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// This page is called from includes/inc/incident_html_top.inc.php

@include ('set_include_path.inc.php');
$permission = 42;
require ($lib_path.'db_connect.inc.php');
require ($lib_path.'functions.inc.php');

// This page requires authentication
require ($lib_path.'auth.inc.php');

// External variables
$incomingid = cleanvar($_REQUEST['id']);

if (empty($incomingid)) trigger_error("Update ID was not set:{$updateid}", E_USER_WARNING);

$sql = "UPDATE `{$dbTempIncoming}` SET locked = NULL, lockeduntil = NULL ";
$sql .= "WHERE tempincoming.id='{$incomingid}' AND locked = '{$sit[2]}'";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
echo "<html><head><title></title></head><body onload=\"window.opener.location='review_incoming_updates.php'; window.close();\">";
echo "</body><html>\n";
?>
