<?php
// auth.inc.php - Checks whether the user is allowed to access the page
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// This file is to be included on any page that requires authentication
// it requires the functions.inc.php file to be already included
// This file must be included before any page output

session_start();

// Attempt to prevent session fixation attacks
if (!isset($_SESSION['auth']))
{
    session_regenerate_id();
    $_SESSION['auth'] = FALSE;
}

if (authenticate($sit[0], $sit[1]) != 1)
{
    // Invalid user
    $_SESSION['auth']=FALSE;
    $page = urlencode($_SERVER['PHP_SELF']);
    header("Location: index.php?id=1&page=$page");
    exit;
}
else
{
    $_SESSION['auth']=TRUE;
    // Continue executing...
}

// if ($permission=='') trigger_error("Could not determine required permissions",E_USER_ERROR);
if (!is_array($permission)) { $permission = array($permission); }
// Valid user, check permissions
if (user_permission($userid, $permission) == FALSE)
{
    // No access permission
    $refused = implode(',',$permission);
    header("Location: noaccess.php?id=$refused");
    exit;
}

?>