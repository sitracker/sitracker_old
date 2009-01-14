<?php
// portal/addcontact.php - Add a site contact
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author Kieran Hogg <kieran_hogg[at]users.sourceforge.net>

@include ('../set_include_path.inc.php');
require 'db_connect.inc.php';
require 'functions.inc.php';

$accesslevel = 'admin';

include 'portalauth.inc.php';
include 'portalheader.inc.php';

if (isset($_POST['submit']))
{
    echo process_add_contact('external');
}

echo show_add_contact($_SESSION['siteid'], 'external');

include 'htmlfooter.inc.php';
?>
