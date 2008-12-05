<?php
// config.php - Interface for configuring SiT
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
$seltab = cleanvar($_REQUEST['tab']);
$action = cleanvar($_REQUEST['action']);

include ('htmlheader.inc.php');

require('configvars.inc.php');

echo "<h2>".icon('settings', 32, $strConfiguration);
echo " {$CONFIG['application_shortname']} {$strConfiguration}</h2>";

if ($action == 'save')
{
    if (!empty($selcat))
    {
        $savevar = array();
        foreach ($CFGCAT[$selcat] AS $catvar)
        {
            $savevar[$catvar] = cleanvar($_REQUEST[$catvar]);
        }
        if ($CONFIG['debug']) echo "<pre>".print_r($savevar,true)."</pre>";
        cfgSave($savevar);
    }
}


echo "<div class='tabcontainer'>";
echo "<ul>";
foreach ($CFGTAB AS $tab => $cat)
{
    if (empty($seltab)) $seltab = 'application';
    echo "<li";
    if ($seltab == $tab) echo " class='active'";
    echo "><a href='{$_SERVER['PHP_SELF']}?tab={$tab}'>{$tab}</a></li>";
}
echo "</ul>";
echo "</div>";

// echo "<div style='clear: both;'></div>";


echo "<div class='tabcontainer smalltabs'>";
echo "<ul>";
foreach ($CFGTAB[$seltab] AS $cat)
{
    if (empty($selcat)) $selcat = $CFGTAB[$seltab][0];
    echo "<li";
    if ($selcat == $cat) echo " class='active'";
    echo "><a href='{$_SERVER['PHP_SELF']}?tab={$seltab}&amp;cat={$cat}'>{$cat}</a></li>";
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
        echo cfgVarInput($catvar, $CONFIG['debug']);
    }

}
echo "</fieldset>";
echo "<input type='hidden' name='cat' value='{$selcat}' />";
echo "<input type='hidden' name='action' value='save' />";
echo "<p><input type='reset' value=\"{$strReset}\" /> <input type='submit' value=\"{$strSave}\" /></p>";


include ('htmlfooter.inc.php');
?>