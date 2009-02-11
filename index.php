<?php
// index.php - Welcome screen and login form
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// This Page Is Valid XHTML 1.0 Transitional! 31Oct05

$lib_path = dirname( __FILE__ ).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR;

if (!@include ($lib_path.'db_connect.inc.php'))
{
    $msg = urlencode(base64_encode("Could not find database connection information (db_connect.inc.php), the php include path is probably wrong"));
    header("Location: {$CONFIG['application_webpath']}setup.php?msg={$msg}");
    exit;
}

session_name($CONFIG['session_name']);
session_start();
include ($lib_path.'strings.inc.php');
require ($lib_path.'functions.inc.php');

if ($_SESSION['auth'] != TRUE)
{
    // External variables
    $id = cleanvar($_REQUEST['id']);
    $page = urldecode($_REQUEST['page']);
    $page = str_replace(':','', $page);
    $page = str_replace('//','', $page);
    $page = str_replace('..','', $page);
    $page = strip_tags($page);
    $page = htmlentities($page, ENT_COMPAT, $GLOBALS['i18ncharset']);

    // Invalid user, show log in form
    include ('./inc/htmlheader.inc.php');
    if ($id == 1)
    {
        echo "<p class='error'>";
        echo sprintf($strEnterCredentials, $CONFIG['application_shortname']);
        echo "</p><br />";
    }

    if ($id == 2)
    {
        echo "<p class='error'>{$strSessionExpired}</p><br />";
    }

    if ($id == 3)
    {
        throw_user_error("{$strInvalidCredentials}");
    }

    // Check this is current
    $sql = "SELECT version FROM `{$dbSystem}` WHERE id = 0";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
    list($dbversion) = mysql_fetch_row($result);
    if ($dbversion < $application_version)
    {
        echo "<p class='error'><strong>IMPORTANT</strong> The SiT database schema needs to be updated from ";
        echo "v{$dbversion} to v{$application_version}</p>";
        echo "<p class='tip'>Visit <a href='setup.php'>Setup</a> to update the schema";
    }

    // Language selector
    if (!empty($CONFIG['available_i18n']))
    {
        $available_languages = i18n_code_to_name($CONFIG['available_i18n']);
    }
    else
    {
        $available_languages = available_languages();
    }
    if (count($available_languages) == 1 AND array_key_exists($CONFIG['default_i18n'], $available_languages))
    {
        echo "<!-- Language: {$CONFIG['default_i18n']} -->";
    }
    else
    {
        $available_languages = array_merge(array('default'=>$strDefault),$available_languages);
        echo "<div style='margin-left: auto; margin-right: auto; width: 380px;";
        echo " text-align: center; margin-top: 3em;'>";
        echo "<form id='langselectform' action='login.php' method='post'>";
        echo icon('language', 16, $strLanguage)." <label for='lang'>";
        echo "{$strLanguage}:  ";

        if (!empty($_SESSION['lang'])) $setting = $_SESSION['lang'];
        else $setting = 'default';

        echo array_drop_down($available_languages, 'lang', $setting, "onchange='this.form.submit();'", TRUE);
        echo "</form>";
        echo "</div>";
    }
    echo "<div class='windowbox' style='width: 220px;'>\n";
    echo "<div class='windowtitle'>{$CONFIG['application_shortname']} - ";
    echo "{$strLogin}</div>\n";
    echo "<div class='window'>\n";
    echo "<form id='loginform' action='login.php' method='post'>";
    echo "<label for='username'>{$strUsername}:<br /><input id='username' ";
    echo "name='username' size='28' type='text' /></label><br />";
    echo "<label for='password'>{$strPassword}:<br /><input id='password' ";
    echo "name='password' size='28' type='password' /></label><br />";
    echo "<input type='hidden' name='page' value='$page' />";
    echo "<input type='submit' value='{$strLogIn}' /><br />";
    echo "<br /><a href='forgotpwd.php'>{$strForgottenDetails}</a>";
    echo "</form>\n";
    echo "</div>\n</div>\n";
    include ('./inc/htmlfooter.inc.php');
}
else
{
    // User is validated, jump to main
    header("Location: main.php");
    exit;
}
?>