<?php
// logout.php - Removes cookies
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

require('db_connect.inc.php');
require('functions.inc.php');

journal(CFG_LOGGING_NORMAL, 'Logout', "User {$sit[2]} logged out", CFG_JOURNAL_LOGIN, $sit[2]);

// expire the cookie, as of v3,23 we don't use cookies, but leave this here for a few versions
// in case there are cookies still left on peoples machines
// TODO v3.3x Remove these setcookie lines
setcookie("sit[0]");
setcookie("sit[1]");
setcookie("sit[2]");


// End the session, remove the cookie and destroy all data registered with the session

$_SESSION = array();

if (isset($_COOKIE[session_name()]))
{
   setcookie(session_name(), '', time()-42000, '/');
}

if (isset($_SESSION['auth']))
{
    session_unset();
    session_destroy();
}

// redirect
if (!empty($CONFIG['logout_url'])) $url = $CONFIG['logout_url'];
else $url = "index.php";
header ("Location: $url");
exit;
?>
