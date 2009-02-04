<?php
// tasks.php - List tasks
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Kieran Hogg <kieran[at]sitracker.org
// This Page Is Valid XHTML 1.0 Transitional!

$lib_path = dirname( __FILE__ ).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR;
require ($lib_path.'db_connect.inc.php');
require ($lib_path.'functions.inc.php');
require_once ($lib_path . 'billing.inc.php');
require ($lib_path.'auth.inc.php');

if (!$CONFIG['tasks_enabled'])
{
    header("Location: main.php");
}
$permission = 69;

$id = cleanvar($_REQUEST['incident']);
if (!empty($id))
{
    $title = $strActivities;
    include ('inc/incident_html_top.inc.php');
}
else
{
    $title = $strTasks;
    include ('./inc/htmlheader.inc.php');
}


// This page requires authentication

include ('tasks.inc.php');

if (!empty($id))
{
    include ('inc/incident_html_bottom.inc.php');
}
else
{
    include ('./inc/htmlfooter.inc.php');
}

?>
