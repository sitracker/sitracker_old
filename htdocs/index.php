<?php
// index.php - Welcome screen and login form
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// This Page Is Valid XHTML 1.0 Transitional! 31Oct05

// Since this is the first page people will visit check if the include path is correct
if (($fp = @fopen($filename, 'r', 1)) and fclose($fp) == FALSE)
{
    header("Location: setup.php");
    exit;
}

require('db_connect.inc.php');
require('functions.inc.php');

session_name($CONFIG['session_name']);
session_start();

$languages = array('ar' => 'Arabic',
                   'bg-BG' => 'Bulgarian',
                   'bn-IN' => 'Bengali',
                   'ca-ES' => 'Catalan',
                   'cs-CZ' => 'Czech',
                   'cy-GB' => 'Welsh',
                   'da-DK' => 'Danish',
                   'de-DE' => 'German',
                   'el-GR' => 'Greek',
                   'en-GB' => 'English (British)',
                   'en-US' => 'English (US)',
                   'es-ES' => 'Spanish',
                   'et-EE' => 'Estonian',
                   'eu-ES' => 'Basque',
                   'fa-IR' => 'Farsi',
                   'fi-FI' => 'Finish',
                   'fo-FO' => 'Faroese',
                   'fr-FR' => 'French',
                   'he-IL' => 'Hebrew',
                   'hr-HR' => 'Croation',
                   'hu-HU' => 'Hungarian',
                   'id-ID' => 'Indonesian',
                   'is-IS' => 'Icelandic',
                   'it-IT' => 'Italian',
                   'ja-JP' => 'Japanese',
                   'ka' => 'Georgian',
                   'ko-KR' => 'Korean',
                   'lt-LT' => 'Lithuanian',
                   'ms-MY' => 'Malay',
                   'nb-NO' => 'Norwegian (Bokmål)',
                   'nl-NL' => 'Dutch',
                   'nn-NO' => 'Norwegian (Nynorsk)',
                   'pl-PL' => 'Polish',
                   'pt-BR' => 'Portuguese (Brazil)',
                   'pt-PT' => 'Portuguese (Portugal)',
                   'ro-RO' => 'Romanian',
                   'ru-UA' => 'Ukrainian Russian',
                   'ru-RU' => 'Russian',
                   'sk-SK' => 'Slovak',
                   'sl-SL' => 'Slovenian',
                   'sr-YU' => 'Serbian',
                   'sv-SE' => 'Swedish',
                   'th-TH' => 'Thai',
                   'tr_TR' => 'Turkish',
                   'uk-UA' => 'Ukrainian'
                  );

if ($_SESSION['auth'] != TRUE)
{
    // External variables
    $id = cleanvar($_REQUEST['id']);
    $page = strip_tags(str_replace('..','',str_replace('//','',str_replace(':','',urldecode($_REQUEST['page'])))));

    // Invalid user, show log in form
    include('htmlheader.inc.php');
    if ($id==1) echo "<p class='error'>".sprintf($strEnterCredentials, $CONFIG['application_shortname'])."</p><br />";
    if ($id==2) echo "<p class='error'>{$strSessionExpired}</p><br />";
    if ($id==3) throw_user_error("{$strInvalidCredentials}");

    echo "<div class='windowbox' style='width: 220px;'>";
    echo "<div class='windowtitle'>{$CONFIG['application_shortname']} - {$strLogin}</div>";
    echo "<div class='window'>";
    echo "<form action='login.php' method='post'>";
    echo "<label>{$strUsername}:<br /><input name='username' size='28' type='text' /></label><br />";
    echo "<label>{$strPassword}:<br /><input name='password' size='28' type='password' /></label><br />";
    echo "<label for='lang'>{$strLanguage}: <br /><select name='lang'>";
    foreach($languages AS $langcode => $language)
    {
        if($langcode == $CONFIG['default_i18n']) echo "<option value='$langcode' selected>$language</option>\n";
        echo "<option value='$langcode'>$language</option>\n";
        print_r($langcode);
    }
    echo "</select></label><br /><br />";
    echo "<input type='hidden' name='page' value='$page' />";
    echo "<input type='submit' value='{$strLogIn}' /><br />";
    echo "<br /><a href='forgotpwd.php'>{$strForgottenDetails}</a>";
    echo "</form>";
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