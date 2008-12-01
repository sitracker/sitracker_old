<?php
// config.php - Configure SiT
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Ivan Lucas, <ivanlucas[at]users.sourceforge.net

@include ('set_include_path.inc.php');
$permission = 22; // Administrate

require ('db_connect.inc.php');
require ('functions.inc.php');
// This page requires authentication
require ('auth.inc.php');

// External variables
$selcat = cleanvar($_REQUEST['cat']);

include ('htmlheader.inc.php');

require('configvars.inc.php');

echo "<h2>".icon('settings', 32, $strConfiguration);
echo " {$CONFIG['application_shortname']} {$strConfiguration}</h2>";

echo "<div class='tabcontainer'>";
echo "<ul>";
foreach ($CFGCAT AS $cat => $catvar)
{
    echo "<li";
    if ($selcat == $cat) echo " class='active'";
    echo "><a href='{$_SERVER['PHP_SELF']}?cat={$cat}'>{$cat}</a></li>";
}
echo "</ul>";
echo "</div>";

echo "<div style='clear: both;'></div>";

echo "<form action='{$_SERVER['PHP_SELF']}' method='post'>";
echo "<fieldset>";
echo "<legend>{$selcat}</legend>";
if (!empty($selcat))
{
    foreach ($CFGCAT[$selcat] AS $catvar)
    {
        echo cfgVarInput($catvar);
    }

}
echo "</fieldset>";
echo "<input type='hidden' name='cat' value='{$selcat}' />";
echo "<p><input type='reset' value=\"{$strReset}\" /> <input type='submit' value=\"{$strSave}\" /></p>";


include ('htmlfooter.inc.php');
?>