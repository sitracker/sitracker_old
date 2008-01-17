<?php
// index.php - Welcome screen and login form
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// This Page Is Valid XHTML 1.0 Transitional! 31Oct05

@include('set_include_path.inc.php');

if (version_compare(PHP_VERSION, "5.0.0", ">="))
{
    try
    {
        if (!@include('db_connect.inc.php')) throw new Exception('Failed to include essential file, include path is probably wrong');
    }
    catch (Exception $e)
    {
        header("Location: setup.php");
        exit;
        // print $e->getMessage();
    }
}
else require('db_connect.inc.php');

session_name($CONFIG['session_name']);
session_start();
include('strings.inc.php');
require('functions.inc.php');

if ($_SESSION['auth'] != TRUE)
{
    // External variables
    $id = cleanvar($_REQUEST['id']);
    $page = htmlentities(strip_tags(str_replace('..','',str_replace('//','',str_replace(':','',urldecode($_REQUEST['page']))))),ENT_COMPAT, $GLOBALS['i18ncharset']);

    // Invalid user, show log in form
    include('htmlheader.inc.php');
    if ($id == 1)
    {
        echo "<p class='error'>".sprintf($strEnterCredentials, $CONFIG['application_shortname'])."</p><br />";
    }

    if ($id == 2)
    {
        echo "<p class='error'>{$strSessionExpired}</p><br />";
    }

    if ($id == 3)
    {
        throw_user_error("{$strInvalidCredentials}");
    }

    echo "<div style='margin-left: auto; margin-right: auto; width: 380px; text-align: center; margin-top: 3em;'>";
    echo "<form id='langselectform' action='login.php' method='post'>";
    echo "<label for='lang'>{$strLanguage}:  <select name='lang' id='lang' onchange='this.form.submit();'>";
    echo "<option value='default'";
    if (empty($_SESSION['lang']))
    {
        echo " selected='selected'";
    }

    echo ">{$strDefault}</option>\n";
    if ($_GET['lang'] == 'zz')
    {
        $availablelanguages['zz'] = 'Test Language (zz)';
    }

    foreach ($availablelanguages AS $langcode => $language)
    {
        if ($langcode == $_SESSION['lang'])
        {
            echo "<option value='{$langcode}' selected='selected'>{$language}</option>\n";
        }
        else
        {
            echo "<option value='{$langcode}'>{$language}</option>\n";
        }
    }
    echo "</select></label>";
    echo "</form>";
    echo "</div>";
    echo "<div class='windowbox' style='width: 220px;'>\n";
    echo "<div class='windowtitle'>{$CONFIG['application_shortname']} - {$strLogin}</div>\n";
    echo "<div class='window'>\n";
    echo "<form id='loginform' action='login.php' method='post'>";
    echo "<label for='username'>{$strUsername}:<br /><input id='username' name='username' size='28' type='text' /></label><br />";
    echo "<label for='password'>{$strPassword}:<br /><input id='password' name='password' size='28' type='password' /></label><br />";
    echo "<input type='hidden' name='page' value='$page' />";
    echo "<input type='submit' value='{$strLogIn}' /><br />";
    echo "<br /><a href='forgotpwd.php'>{$strForgottenDetails}</a>";
    echo "</form>\n";
    echo "</div>\n</div>\n";
    include('htmlfooter.inc.php');
}
else
{
    // User is validated, jump to main
    header("Location: main.php");
    exit;
}
?>
