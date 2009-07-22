<?php
// kb_rss.php - Output an RSS representation showing new kb articles
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2006-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

require ('core.php');
require (APPLICATION_LIBPATH . 'functions.inc.php');

// This script requires no authentication
// The information it reveals should not be sensitive

$c = cleanvar($_GET['c']);
$salt = md5($CONFIG['db_password']);
$usql = "SELECT id FROM `{$dbUsers}` WHERE MD5(CONCAT(`username`, '{$salt}')) = '$c' LIMIT 1";
// $usql = "SELECT id FROM `{$dbUsers}` WHERE username = '$c' LIMIT 1";
$uresult = mysql_query($usql);

if ($uresult)
{
    list($userid) = mysql_fetch_row($uresult);
}

// $userid = cleanvar($_REQUEST['user']);

if (!is_numeric($userid))
{
    header("HTTP/1.1 403 Forbidden");
    echo "<html><head><title>403 Forbidden</title></head><body><h1>403 Forbidden</h1></body></html>\n";
    exit;
}

// Feed stuff goes here (obviously) ;)


?>