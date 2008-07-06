<?php
// storedashboard.php - Stored dashboard layout
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

@include ('set_include_path.inc.php');
$permission = 0; // not required
require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

// TODO merge storedashboard.php this with ajaxdata.php

$id = $_REQUEST['id'];
$val = $_REQUEST['val'];

if ($id == $_SESSION['userid'])
{
    //check you're changing your own
    $sql = "UPDATE `{$dbUsers}` SET dashboard = '$val' WHERE id = '$id'";
    $contactresult = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
}

?>
