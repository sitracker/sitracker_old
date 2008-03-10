<?php
// new_feedback.php - Feedback report menu
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author:  Paul Heaney Paul Heaney <paulheaney[at]users.sourceforge.net>

@include ('../set_include_path.inc.php');
$permission = 37; // Run Reports

require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

$type = cleanvar($_REQUEST['type']);
$dates = cleanvar($_REQUEST['dates']);
$startdate = strtotime(cleanvar($_REQUEST['startdate']));
$enddate = strtotime(cleanvar($_REQUEST['enddate']));

/// echo "Start: {$startdate}";

include ('htmlheader.inc.php');

echo "<h2>Feedback Reports</h2>";

if (empty($type))
{
    include ('feedback/form.inc.php');
}
elseif ($type == 'byengineer')
{
    include ('feedback/engineer.inc.php');
}
elseif ($type = 'bycustomer')
{
    include ('feedback/contact.inc.php');
}

include ('htmlfooter.inc.php');

?>