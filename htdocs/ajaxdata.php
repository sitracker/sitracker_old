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

@include ('set_include_path.inc.php');
$permission = 0; // not required
require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

$action = $_REQUEST['action'];

if ($_SESSION['auth'] == TRUE)
{
    $styleid = $_SESSION['style'];
}
else
{
    $styleid = $CONFIG['default_interface_style'];
}
$iconsql = "SELECT iconset FROM `{$GLOBALS['dbInterfaceStyles']}` WHERE id='{$styleid}'";
$iconresult = mysql_query($iconsql);
if (mysql_error())trigger_error(mysql_error(),E_USER_WARNING);

list($iconset) = mysql_fetch_row($iconresult);

switch ($action)
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
        // Default back to english if language helpfile isn't found
        if (!file_exists($helpfile)) $helpfile = "{$CONFIG['application_fspath']}htdocs/help/en-GB/{$context}.txt";
        if (file_exists($helpfile))
        {
            $fp = fopen($helpfile, 'r', TRUE);
            $helptext = fread($fp, 1024);
            fclose($fp);
            echo nl2br($helptext);
        }
        else echo "Error: Missing helpfile '{$_SESSION['lang']}/{$context}.txt'";
    break;

    case 'dismiss_notice':
        require ('auth.inc.php');
        $noticeid = cleanvar($_REQUEST['noticeid']);
        $userid = cleanvar($_REQUEST['userid']);
        if (is_numeric($noticeid))
        {
            $sql = "DELETE FROM `{$GLOBALS['dbNotices']}` WHERE id='{$noticeid}' AND userid='{$sit[2]}'";
            mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
            else echo "deleted {$noticeid}";
        }
        elseif ($noticeid == 'all')
        {
            $sql = "DELETE FROM `{$GLOBALS['dbNotices']}` WHERE userid={$userid} LIMIT 20"; // only delete 20 max as we only show 20 max
            mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
            else echo "deleted {$noticeid}";
        }
    break;

    case 'dashboard_display':
        require ('auth.inc.php');
        $dashboard = cleanvar($_REQUEST['dashboard']);
        $dashletid = 'win'.cleanvar($_REQUEST['did']);
        // FIXME need some sanitation here
        include ("{$CONFIG['application_fspath']}dashboard{$fsdelim}dashboard_{$dashboard}.php");
        $dashfn = "dashboard_{$dashboard}_display";
        echo $dashfn($dashletid);
    break;

    case 'dashboard_save':
    case 'dashboard_edit':
        require ('auth.inc.php');

        $dashboard = cleanvar($_REQUEST['dashboard']);
        $dashletid = 'win'.cleanvar($_REQUEST['did']);
        // FIXME need some sanitation here
        include ("{$CONFIG['application_fspath']}dashboard{$fsdelim}dashboard_{$dashboard}.php");
        $dashfn = "dashboard_{$dashboard}_edit";
        echo $dashfn($dashletid);
    break;

    case 'autocomplete_sitecontact':
    break;

    default : break;
}


?>