<?php
// tasks.php - List tasks
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Kieran Hogg <kieran_hogg[at]users.sourceforge.net

require('db_connect.inc.php');
require('functions.inc.php');
require('auth.inc.php');
$permission=0; // Allow all auth users
$title='Tasks';
$id = $_REQUEST['incident'];
if($_REQUEST['incident'])
    include('incident_html_top.inc.php');
else
    include('htmlheader.inc.php');


// This page requires authentication

include('tasks.inc.php');

include('htmlfooter.inc.php');

?>
