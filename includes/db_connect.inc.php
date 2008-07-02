<?php
// db_connect.inc.php - Initiate a database connection
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Load config defaults
include ("defaults.inc.php");
// Server Configuration
@include ('/etc/webtrack.conf');  // Legacy, for compatibility
@include ('/etc/sit.conf');
// Load config file with customisations
@include ("config.inc.php");
// TODO determine which language to use, for now we're hardcoded to English (British)
// i18n
@include ('i18n/en-gb.inc.php');

if ($CONFIG['debug'] > 0)
{
    // Set Start Time for Execution Timing
    function getmicrotime()
    {
        list($usec, $sec) = explode(" ",microtime());
        return ((float)$usec + (float)$sec);
    }
    $exec_time_start = getmicrotime();
}

if ($CONFIG['db_username'] == '' OR $CONFIG['db_database'] == '')
{
    header("Location: {$CONFIG['application_webpath']}setup.php");
    exit;
}

// Connect to Database server
$db = @mysql_connect($CONFIG['db_hostname'], $CONFIG['db_username'], $CONFIG['db_password']);
if (mysql_error())
{
    header("Location: {$CONFIG['application_webpath']}setup.php");
    exit;
}
// mysql_query("SET CHARACTER SET utf8");
mysql_query("SET NAMES utf8");

// mysql_query("SET time_zone = {$CONFIG['timezone']}");

// Select database
mysql_select_db($CONFIG['db_database'], $db);
if (mysql_error())
{
    // TODO add some detection for missing database
    if (strpos(mysql_error(), 'Unknown database')!==FALSE)
    {
        header("Location: {$CONFIG['application_webpath']}setup.php");
        exit;
    }
    // Attempt socket connection to database to check if server is alive
    if (!fsockopen($CONFIG['db_hostname'], 3306, $errno, $errstr, 5))
    {
        trigger_error("!Error: No response from database server within 5 seconds, Database Server ({$CONFIG['db_hostname']}) is probably down - contact a Systems Administrator",E_USER_ERROR);
    }
    else
    {
        header("Location: {$CONFIG['application_webpath']}setup.php");
        exit;
    }
}

// Soft table names
require('tablenames.inc.php');
?>