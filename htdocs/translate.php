<?php
// translate.php - A simple interface for aiding translation.
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Authors: Kieran Hogg <kieran[at]sitracker.org>
//          Ivan Lucas <ivan_lucas[at]users.sourceforge.net>

@include ('set_include_path.inc.php');
$permission = 0; // not required
require ('db_connect.inc.php');
require ('functions.inc.php');

require ('auth.inc.php');
include ('htmlheader.inc.php');

$i18npath = '../includes/i18n/';

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
                   'es-CO' => 'Spanish (Colombia)',
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
                   'uk-UA' => 'Ukrainian',
                   'zh-CN' => 'Chinese (Simplified)',
                   'zh-TW' => 'Chinese (Traditional)'
                  );

if (!$_REQUEST['mode'])
{
    echo "<h2>{$strTranslation}</h2>";
    echo "<div align='center'><p>{$strHelpToTranslate}</p>";
    echo "<p>{$strChooseLanguage}</p>";
    echo "<form action='{$_SERVER['PHP_SELF']}?mode=show' method='get'>";
    //FIXME
    echo "<input name='mode' value='show' type='hidden' />";

    echo "<select name='lang'>";
    foreach ($languages AS $langcode => $language)
    {
        if ($langcode!='en-GB') echo "<option value='{$langcode}'>{$langcode} - {$language}</option>\n";
    }
    echo "</select><br /><br />";
    echo "<input type='submit' value='$strTranslate' />";
    echo "</form></div>\n";
}
elseif ($_REQUEST['mode'] == "show")
{
    //open english file
    $englishfile = "{$i18npath}/en-GB.inc.php";
    $fh = fopen($englishfile, 'r');
    $theData = fread($fh, filesize($englishfile));
    fclose($fh);
    $lines = explode("\n", $theData);
    $langstrings['en-GB'];
    $englishvalues = array();
    foreach ($lines as $values)
    {
        $badchars = array("$", "\"", "\\", "<?php", "?>");
        $values = trim(str_replace($badchars, '', $values));

        //get variable and value
        $vars = explode("=", $values);

        //remove spaces
        $vars[0] = trim($vars[0]);
        $vars[1] = trim($vars[1]);

        if (substr($vars[0], 0, 3) == "str")
        {
            //remove leading and trailing quotation marks
            $vars[1] = substr_replace($vars[1], "",-2);
            $vars[1] = substr_replace($vars[1], "",0, 1);
            $englishvalues[$vars[0]] = $vars[1];
        }
        elseif (substr($vars[0], 0, 2) == "# ")
        {
            $comments[$lastkey] = substr($vars[0], 2, 1024);
        }
        else
        {
            if (substr($values, 0, 4) == "lang")
                $languagestring=$values;
            if (substr($values, 0, 4) == "i18n")
                $i18ncharset=$values;
        }
        $lastkey = $vars[0];
    }
    $origcount = count($englishvalues);
    unset($lines);

    //open foreign file
    $myFile = "$i18npath/{$_REQUEST['lang']}.inc.php";
    if (file_exists($myFile))
    {
        $foreignvalues = array();

        $fh = fopen($myFile, 'r');
        $theData = fread($fh, filesize($myFile));
        fclose($fh);
        $lines = explode("\n", $theData);
        //print_r($lines);
        foreach ($lines as $values)
        {
            $badchars = array("$", "\"", "\\", "<?php", "?>");
            $values = trim(str_replace($badchars, '', $values));
            if (substr($values, 0, 3) == "str")
            {
                $vars = explode("=", $values);
                $vars[0] = trim($vars[0]);
                $vars[1] = trim(substr_replace($vars[1], "",-2));
                $vars[1] = substr_replace($vars[1], "",0, 1);
                $foreignvalues[$vars[0]] = $vars[1];
            }
        }
    }
    echo "<h2>{$strWordList}</h2>";
    echo "<p align='center'>{$strTranslateTheString}<br/>";
    echo "<strong>{$strCharsToKeepWhenTranslating}</strong></p>";
    echo "<form method='post' action='{$_SERVER[PHP_SELF]}?mode=save'>";
    echo "<table align='center'><tr><th>{$strVariable}</th><th>en-GB ({$strEnglish})</th><th>{$_REQUEST['lang']}</th></tr>";

    $shade = 'shade1';
    foreach (array_keys($englishvalues) as $key)
    {
        if ($_REQUEST['lang'] == 'zz') $foreignvalues[$key] = $key;
        echo "<tr class='$shade'><td><label for=\"{$key}\"><code>{$key}</code></td><td><input name='english_{$key}' value=\"".htmlentities($englishvalues[$key], ENT_QUOTES, 'UTF-8')."\" size=\"45\" readonly='readonly' /></td>";
        echo "<td><input id=\"{$key}\" name=\"{$key}\" value=\"".htmlentities($foreignvalues[$key], ENT_QUOTES, 'UTF-8')."\" size=\"45\" /></td></tr>\n";
        if ($shade=='shade1') $shade='shade2';
        else $shade='shade1';
        if (!empty($comments[$key])) echo "<tr><td colspan=3' class='{$shade}'><strong>{$strNotes}:</strong> {$comments[$key]}</td><tr>\n";
    }
    echo "</table>";
    echo "<input type='hidden' name='origcount' value='{$origcount}' />";
    echo "<input name='lang' value='{$_REQUEST['lang']}' type='hidden' /><input name='mode' value='save' type='hidden' />";
    echo "<div align='center'><input type='submit' value='{$strSave}' /></div>";

    echo "</form>\n";
}
elseif ($_REQUEST['mode'] == "save")
{
    $lang = cleanvar($_REQUEST['lang']);
    $origcount = cleanvar($_REQUEST['origcount']);
    $filename = "{$lang}.inc.php";
    echo "<p>".sprintf($strSendTranslation, "<code>{$filename}</code>", "<code>{$i18npath}</code>", 'ivanlucas[at]users.sourceforge.net')." </p>";
    $i18nfile = '';
    $i18nfile .= "<?php\n";
    $i18nfile .= "// SiT! Language File - {$languages[$lang]} ($lang) by {$_SESSION['realname']}\n\n";
    $i18nfile .= "\$languagestring = '{$languages[$lang]} ($lang)';\n";
    $i18nfile .= "\$i18ncharset = 'UTF-8';\n\n";
    //$i18nfile .= "// list of strings (Alphabetical)\n";

    $lastchar='';
    $translatedcount=0;
    foreach (array_keys($_POST) as $key)
    {
        if (!empty($_POST[$key]) AND substr($key, 0, 3) == "str")
        {
            if ($lastchar!='' AND substr($key, 3, 1) != $lastchar) $i18nfile .= "\n";
            $i18nfile .= "\${$key} = '".addslashes($_POST[$key])."';\n";
            $lastchar = substr($key, 3, 1);
            $translatedcount++;
        }
    }
    $percent = number_format($translatedcount / $origcount * 100,2);
    echo "<p>{$strTranslation}: <strong>{$translatedcount}</strong>/{$origcount} = {$percent}% {$strComplete}.</p>";
    $i18nfile .= "?>\n";
    echo "<div style='margin-left: 5%; margin-right: 5%; background-color: white; border: 1px solid #ccc; padding: 1em;'>";
    highlight_string($i18nfile);
    echo "</div>";
}
else
{
    trigger_error('Invalid mode', E_USER_ERROR);
}
include ('htmlfooter.inc.php');
?>
