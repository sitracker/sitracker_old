<?php
// help.php - Get context sensitive help
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

$lib_path = dirname( __FILE__ ).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR;
$permission = 26; // Help
require ($lib_path.'db_connect.inc.php');
require ($lib_path.'functions.inc.php');
$title = "Help";

// This page requires authentication
require ($lib_path.'auth.inc.php');

// External variables
$id = cleanvar($_REQUEST['id']);

include ('./inc/htmlheader.inc.php');
journal(CFG_LOGGING_MAX, 'Help Viewed', "Help document $id was viewed", CFG_JOURNAL_OTHER, $id);
echo "<h2>".icon('help', 32, $strHelp)." ";
if ($id > 0) echo permission_name($id).' ';
echo "{$strHelp}</h2>";
echo "<div id='help'>";

$helpfile = dirname( __FILE__ ).DIRECTORY_SEPARATOR."help/{$_SESSION['lang']}/help.html";
if (!file_exists($helpfile)) $helpfile = dirname( __FILE__ ).DIRECTORY_SEPARATOR."help/en-GB/help.html";
if (file_exists($helpfile))
{
    $helptext = file_get_contents($helpfile);
}
else echo "<p class='error'>Error: Missing helpfile 'help.html'</p>";

echo $helptext;

echo "</div>";

include ('./inc/htmlfooter.inc.php');
?>
