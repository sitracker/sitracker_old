<?php
// view_task.php - Display existing task
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Authors: Kieran Hogg <kieran[at]sitracker.org>

$lib_path = dirname( __FILE__ ).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR;
$permission = 0; // Allow all auth users

require ($lib_path.'db_connect.inc.php');
require ($lib_path.'functions.inc.php');

// This page requires authentication
require ($lib_path.'auth.inc.php');

$title = $strViewTask;

// External variables
$action = $_REQUEST['action'];
$id = cleanvar($_REQUEST['incident']);
$taskid = cleanvar($_REQUEST['id']);
$mode = cleanvar($_REQUEST['mode']);

if ($mode == 'incident')
{
    include ('inc/incident_html_top.inc.php');
}
else
{
    include ('./inc/htmlheader.inc.php');
}

require ('./inc/task_view.inc.php');
include ('./inc/htmlfooter.inc.php');

?>
