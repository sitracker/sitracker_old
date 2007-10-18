<?php
// auth.inc.php - Checks whether the user is allowed to access the page
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// This file is to be included on any page that requires authentication
// it requires the functions.inc.php file to be already included
// This file must be included before any page output

session_name($CONFIG['session_name']);
session_start();

// Check session is authenticated, if not redirect to login page
if (!isset($_SESSION['auth']) OR $_SESSION['auth'] == FALSE)
{
    $_SESSION['auth'] = FALSE;
    // Invalid user
    $page = $_SERVER['PHP_SELF'];
    if (!empty($_SERVER['QUERY_STRING'])) $page .= '?'.$_SERVER['QUERY_STRING'];
    $page = urlencode($page);
    header("Location: {$CONFIG['application_webpath']}index.php?id=2&page=$page");
    exit;
}
else
{
    // Attempt to prevent session fixation attacks
    session_regenerate_id();
    // Conversions for when register_globals=off
    // We've migrated away from using cookies and now use sessions
    $sit[0] = $_SESSION['username'];
    $sit[1] = 'obsolete'; // FIXME Check $sit[1] is unused then remove
    $sit[2] = $_SESSION['userid'];
}

if (!is_array($permission)) { $permission = array($permission); }
// Valid user, check permissions
if (user_permission($userid, $permission) == FALSE)
{
    // No access permission
    $refused = implode(',',$permission);
    header("Location: {$CONFIG['application_webpath']}noaccess.php?id=$refused");
    exit;
}

?>