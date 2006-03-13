<?php
// help.php - Get context sensitive help
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//


$permission=26; // Help
require('db_connect.inc.php');
require('functions.inc.php');
$title="Help";

// This page requires authentication
require('auth.inc.php');

// Valiod user,  Check users permissions
if (!user_permission($sit[2],$permission))
{
    header("Location: noaccess.php?id=$permission");
    exit;
}

// External variables
$id = cleanvar($_REQUEST['id']);

include('htmlheader.inc.php');
journal(CFG_LOGGING_MAX, 'Help Viewed', "Help document $id was viewed", CFG_JOURNAL_OTHER, $id);
echo "<h2>".permission_name($id)." Help</h2>";
echo "<div id='help'>";
if ($id<0 OR $id>200 OR $id=='' OR strlen($id)>3) $id=0;
include("help/help-$id.inc.php");
echo "</div>";

include('htmlfooter.inc.php');
include('db_disconnect.inc.php');
?>