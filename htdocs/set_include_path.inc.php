<?php
// set_include_path.inc.php - Allow setting of include path without altering php.ini
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>




    // If for any reason you cannot edit php.ini to set your include path you
    // can uncomment the following line and set it here.


$include_path = '/var/www/sit/includes';
$lib_path = './lib/';
    // Or for Windows users

// $include_path = 'c:\www\sit\includes';







// ====================================================================
if (!empty($include_path))
{
    set_include_path(get_include_path() . PATH_SEPARATOR . $include_path);
}
?>
