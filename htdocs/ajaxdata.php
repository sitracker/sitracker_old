<?php
// ajaxdata.php - Return data for AJAX calls
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

// TODO Merge autocomplete.php into here?

@include('set_include_path.inc.php');
$permission = 0; // not required
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
// require('auth.inc.php');

$action = $_REQUEST['action'];

switch($action)
{
    case 'servicelevel_timed':
        $sltag = servicelevel_id2tag(cleanvar($_REQUEST['servicelevel']));
        if (servicelevel_timed($sltag))
        {
            echo "TRUE";
        }
        else
        {
            echo "FALSE";
        }
    break;

    case 'contexthelp':
        $context = cleanvar($_REQUEST['context']);
        $helpfile = "{$CONFIG['application_fspath']}htdocs/help/{$_SESSION['lang']}/{$context}.txt";
        if (!file_exists($helpfile)) $helpfile = "{$CONFIG['application_fspath']}htdocs/help/en-GB/{$context}.txt";
        if (file_exists($helpfile))
        {
            $fp = fopen($helpfile, 'r', TRUE);
            $helptext = fread($fp, 1024);
            fclose($fp);
        }
        else echo "Error: Missing helpfile '{$context}.txt'";
        echo nl2br($helptext);
    break;

    case 'autocomplete_sitecontact':
    break;

    default : break;
}


?>