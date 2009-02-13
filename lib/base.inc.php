<?php
// base.inc.php - core constants and files
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}

//**** Begin constant/variable definitions ****//

// For tempincoming
define("REASON_POSSIBLE_NEW_INCIDENT", 1);
define("REASON_INCIDENT_CLOSED", 2);

// Version number of the application, (numbers only)
$application_version = '3.45';

// Revision string, e.g. 'beta2' or 'svn' or ''
$application_revision = 'beta2';

// Clean PHP_SELF server variable to avoid potential XSS security issue
$_SERVER['PHP_SELF'] = substr($_SERVER['PHP_SELF'], 0,
                            (strlen($_SERVER['PHP_SELF'])
                            - @strlen($_SERVER['PATH_INFO'])));

$fsdelim = DIRECTORY_SEPARATOR;

// Time settings
$now = time();
// next 16 hours, based on reminders being run at midnight this is today
$today = $now + (16 * 3600);
$lastweek = $now - (7 * 86400); // the previous seven days
$todayrecent = $now -(16 * 3600);  // past 16 hours

$CONFIG['upload_max_filesize'] = return_bytes($CONFIG['upload_max_filesize']);

//**** Begin internal functions ****//
// Append SVN data for svn versions
if ($application_revision == 'svn')
{
    // Add the svn revision number
    preg_match('/([0-9]+)/','$LastChangedRevision$',$revision);
    $application_revision .= $revision[0];
}

// Set a string to be the full version number and revision of the application
$application_version_string = trim("v{$application_version} {$application_revision}");

// Report all PHP errors
error_reporting(E_ALL);
$oldeh = set_error_handler("sit_error_handler");

// Decide which language to use and setup internationalisation
require (dirname( __FILE__ ).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'i18n/en-GB.inc.php');
if ($CONFIG['default_i18n'] != 'en-GB')
{
    include (dirname( __FILE__ ).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR."i18n/{$CONFIG['default_i18n']}.inc.php");
}
if (!empty($_SESSION['lang'])
    AND $_SESSION['lang'] != $CONFIG['default_i18n'])
{
    include (dirname( __FILE__ ).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR."i18n/{$_SESSION['lang']}.inc.php");
}
ini_set('default_charset', $i18ncharset);


//**** Begin functions ****//

/**
    * Strip slashes from an array
    * @param $data an array
    * @return An array with slashes stripped
*/
function stripslashes_array($data)
{
    if (is_array($data))
    {
        foreach ($data as $key => $value)
        {
            $data[$key] = stripslashes_array($value);
        }
        return $data;
    }
    else
    {
        return stripslashes($data);
    }
}


/**
    * Make an external variable safe for database and HTML display
    * @author Ivan Lucas, Kieran Hogg
    * @param mixed $var variable to replace
    * @param bool $striphtml whether to strip html
    * @param bool $transentities whether to translate all aplicable chars (true) or just special chars (false) into html entites
    * @param bool $mysqlescape whether to mysql_escape()
    * @param array $disallowedchars array of chars to remove
    * @param array $replacechars array of chars to replace as $orig => $replace
    * @returns variable
*/
function cleanvar($vars, $striphtml = TRUE, $transentities = TRUE,
                $mysqlescape = TRUE, $disallowedchars = array(),
                $replacechars = array())
{
    if (is_array($vars))
    {
        foreach ($vars as $key => $singlevar)
        {
            $var[$key] = cleanvar($singlevar, $striphtml, $transentities, $mysqlescape,
                    $disallowedchars, $replacechars);
        }
    }
    else
    {
        $var = $vars;
        if ($striphtml === TRUE)
        {
            $var = strip_tags($var);
        }

        if (!empty($disallowedchars))
        {
            $var = str_replace($disallowedchars, '', $var);
        }

        if (!empty($replacechars))
        {
            foreach ($replacechars as $orig => $replace)
            {
                $var = str_replace($orig, $replace, $var);
            }
        }

        if ($transentities)
        {
            $var = htmlentities($var, ENT_COMPAT, $GLOBALS['i18ncharset']);
        }
        else
        {
            $var = htmlspecialchars($var, ENT_COMPAT, $GLOBALS['i18ncharset']);
        }

        if ($mysqlescape)
        {
            $var = mysql_real_escape_string($var);
        }

        $var = trim($var);
    }
    return $var;
}


/**
  * Return an array of available languages codes by looking at the files
  * in the i18n directory
  * @author Ivan Lucas
  * @param bool $test - (optional) Include test language (zz) in results
  * @retval array Language codes
**/
function available_languages($test = FALSE)
{
    $i18nfiles = list_dir('.'.DIRECTORY_SEPARATOR.'i18n');
    $i18nfiles = array_filter($i18nfiles, 'filter_i18n_filenames');
    array_walk($i18nfiles, 'i18n_filename_to_code');
    asort($i18nfiles);
    foreach ($i18nfiles AS $code)
    {
        if ($code != 'zz')
        {
            $available[$code] = i18n_code_to_name($code);
        }
        elseif ($code == 'zz' AND $test === TRUE)
        {
            $available[$code] = 'Test Language (zz)';
        }
    }

    return $available;
}

?>
