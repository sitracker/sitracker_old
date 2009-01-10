<?php
// planner_schedule_delete.php - deletes an event from the tasks table
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Tom Gerrard <tom.gerrard[at]salfordsoftware.co.uk>

@include ('../set_include_path.inc.php');
$permission = 27; // View your calendar
require ('db_connect.inc.php');
require ('functions.inc.php');
require ('auth.inc.php');

$eventToDelete = cleanvar($_GET['eventToDeleteId']);

if (isset($eventToDelete))
{
    // TODO there should be a permission check here
    if (true)
    {
        mysql_query("DELETE FROM `{$dbTasks}` WHERE id='".$eventToDelete."'");
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        echo "OK";
    }
}

?>