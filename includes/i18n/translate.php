<?php
// translate.php - A simple interface for aiding translation.
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Kieran Hogg <kieran_hogg[at]users.sourceforge.net>
echo "<html><head><title></title><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head>";
echo "<body>";

if(!$_REQUEST['mode'])
{
    echo "<h1>Choose Language</h1>";
    echo "<form action='{$_SERVER['PHP_SELF']}?mode=do&' method='get'>";
    echo "<input name='mode' value='do' type='hidden'>";
    echo "<select name='lang'>";
    if ($handle = opendir('.')) 
    {
        while (false !== ($file = readdir($handle))) 
        {
            $ext = explode(".", $file);
            if($ext[1] == "inc" && $ext[2] == "php")
                echo "<option value='$ext[0]'>$ext[0]<br />";
        }
        closedir($handle);
    }
    echo "<input type='submit'>";
}
else
{
       
    $englishfile = "en-gb.inc.php";
    $fh = fopen($englishfile, 'r');
    $theData = fread($fh, filesize($englishfile));
    fclose($fh);
    $lines = explode(";", $theData);
    $langstrings['en-gb'];
    $englishvalues = array();
    foreach($lines as $values)
    {
        $badchars = array("$", "\"", "\\", "<?php", "?>");
        $values = trim(str_replace($badchars, '', $values));
        if(substr($values, 0, 3) == "str")
        {
            $vars = explode("=", $values);
            $englishvalues[$vars[0]] = $vars[1];
        }
    }

    $myFile = $_REQUEST['lang'].".inc.php";
    $fh = fopen($myFile, 'r');
    $theData = fread($fh, filesize($myFile));
    fclose($fh);
    $lines = explode(";", $theData);
    //print_r($lines);
    $foreignvalues = array();
    foreach($lines as $values)
    {
        $badchars = array("$", "\"", "\\", "<?php", "?>");
        $values = trim(str_replace($badchars, '', $values));
        if(substr($values, 0, 3) == "str")
        {
            $vars = explode("=", $values);
            $foreignvalues[$vars[0]] = $vars[1];
        }
    }
    
}

echo "<table><th>Variable</th><th>English</th><th>{$_REQUEST['lang']}</th>";
foreach(array_keys($englishvalues) as $key)
{
    echo "<tr><td>$key</td><td><input value=\"$englishvalues[$key]\"></input></td><td><input value=\"$foreignvalues[$key]\"></td></tr>";
}

echo "</table>";
echo "</body></html>";
?>