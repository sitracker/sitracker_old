<?php
// portal/addcontact.php - Add a site contact
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author Kieran Hogg <kieran[at]sitracker.org>

$lib_path = dirname( __FILE__ ).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR;
require $lib_path.'db_connect.inc.php';
require $lib_path.'functions.inc.php';

$accesslevel = 'admin';

include 'portalauth.inc.php';
include 'portalheader.inc.php';

if (isset($_POST['submit']))
{
    echo process_add_contact('external');
}

echo show_add_contact($_SESSION['siteid'], 'external');

include './inc/htmlfooter.inc.php';
?>
