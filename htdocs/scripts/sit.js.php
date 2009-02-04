<?php
// sit.js.php - JAVASCRIPT file
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

// Note: This file is PHP that outputs Javascript code, this is primarily
//       to enable us to pass variables from PHP to Javascript.
//

$lib_path = dirname( __FILE__ ).'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR;

$permission=0; // not required
require ($lib_path.'db_connect.inc.php');
require ($lib_path.'functions.inc.php');

header('Content-type: text/javascript');

echo "
var application_webpath = '{$CONFIG['application_webpath']}';




";

?>
