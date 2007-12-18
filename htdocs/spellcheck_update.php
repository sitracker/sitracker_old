<?php
// spellcheck_update.php - Checks spelling of an incident update
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

@include ('set_include_path.inc.php');
$title='Spell Check';
$permission=8;  // Update Incident
require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

// External Variables
$addword = cleanvar($_REQUEST['addword']);
$spellid = cleanvar($_REQUEST['spellid']);
$updateid = cleanvar($_REQUEST['updateid']);
$changepos = cleanvar($_REQUEST['changepos']);
$replacement = cleanvar($_REQUEST['replacement']);


include ('incident_html_top.inc.php');
echo "<h2>$title</h2>";
if (!empty($addword))
{
    spellcheck_addword($addword);
}

if (!isset($spellid))
{
    if (!isset($updateid)) throw_error('!Error no updateid or spellid', '');
    $sql = "SELECT bodytext FROM `{$dbUpdates}` WHERE id='$updateid'";
    $result=mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    list($bodytext) = mysql_fetch_row($result);
    $isql = "INSERT INTO spellcheck (updateid, bodytext) VALUES ('$updateid', '".mysql_real_escape_string($bodytext)."')";
    $result=mysql_query($isql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    if (!$result) throw_error("Problem inserting spellcheck temp data", '');
    $spellid=mysql_insert_id();
}
else
{
    $sql = "SELECT updateid, bodytext FROM spellcheck WHERE id='$spellid'";
    $result=mysql_query($sql);
    list($updateid, $bodytext) = mysql_fetch_row($result);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
}

if (isset($changepos) && $changepos>0)
{
    ## echo "Change position $changepos to word: $replacement<br />";
    $texttospell=replace_word($bodytext, $changepos, $replacement);
    $sql =  "UPDATE spellcheck SET bodytext='".mysql_real_escape_string($texttospell)."' WHERE id='$spellid'";
    $result=mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    if (!$result) throw_error("Problem updating spellcheck temp data", '');
    echo $newtext;
}
else
{
    $texttospell=$bodytext;
}
$spelltext=spellcheck_text($texttospell, "&spellid=$spellid");
// $spelltext=$texttospell;
echo "<table summary=\"spellchecker\" width=\"80%\" align=\"center\" class=\"shade2\"><tr><td>";
echo "<p>";
echo nl2br($spelltext);
echo "</p></td></tr></table>\n";
?>
