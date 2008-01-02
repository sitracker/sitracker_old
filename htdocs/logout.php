<?php
// logout.php - Removes cookies
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

@include('set_include_path.inc.php');
require('db_connect.inc.php');
require('functions.inc.php');

session_name($CONFIG['session_name']);
session_start();

journal(CFG_LOGGING_NORMAL, 'Logout', "User {$sit[2]} logged out", CFG_JOURNAL_LOGIN, $sit[2]);

// expire the cookie, as of v3,23 we don't use cookies, but leave this here for a few versions
// in case there are cookies still left on peoples machines
// TODO v3.3x Remove these setcookie lines
setcookie("sit[0]");
setcookie("sit[1]");
setcookie("sit[2]");


// End the session, remove the cookie and destroy all data registered with the session
$_SESSION['auth']=FALSE;
$_SESSION['portalauth']=FALSE;
$_SESSION = array();

session_unset();
session_destroy();

if (isset($_COOKIE[session_name()]))
{
   setcookie(session_name(), '', time()-42000, '/');
}

// redirect
if (!empty($CONFIG['logout_url'])) $url = $CONFIG['logout_url'];
else $url = $CONFIG['application_webpath']."index.php";
header ("Location: $url");
exit;
?>
