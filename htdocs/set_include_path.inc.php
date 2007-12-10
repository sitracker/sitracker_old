<?php
// set_include_path.inc.php - Allow setting of include path without altering php.ini
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>




    // If for any reason you cannot edit php.ini to set your include path you
    // can uncomment the following line and set it here.


// $include_path = '/var/www/sit/includes';

    // Or for Windows users

// $include_path = 'c:\www\sit\includes';







// ====================================================================
if (!empty($include_path))
{
    $delim = (strstr($_SERVER['SCRIPT_FILENAME'],"/")) ? ":" : ";";
    ini_set('include_path',ini_get('include_path')."{$delim}{$include_path}");
}
?>