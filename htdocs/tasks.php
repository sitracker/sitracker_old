<?php
// tasks.php - List tasks
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Kieran Hogg <kieran_hogg[at]users.sourceforge.net
// This Page Is Valid XHTML 1.0 Transitional!

@include ('set_include_path.inc.php');
require ('db_connect.inc.php');
require ('functions.inc.php');
require ('auth.inc.php');
if (!$CONFIG['tasks_enabled'])
{
    header("Location: main.php");
}
$permission = 69;

$id = cleanvar($_REQUEST['incident']);
if (!empty($id))
{
    $title = $strActivities;
    include ('incident_html_top.inc.php');
}
else
{
    $title = $strTasks;
    include ('htmlheader.inc.php');
}


// This page requires authentication

include ('tasks.inc.php');

if (!empty($id))
{
    include ('incident_html_bottom.inc.php');
}
else
{
    include ('htmlfooter.inc.php');
}

?>