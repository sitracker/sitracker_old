<?php
// spellcheck_email.php - Checks spelling of an email
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// TODO HTML to PHP

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

@include ('set_include_path.inc.php');
$title="Spellcheck Email";
include ('incident_html_top.inc.php');

echo "<p>";

if (isset($addword))
{
    spellcheck_addword($addword);
}
if (isset($spellid))
{
    $sql = "SELECT updateid, bodytext, newincidentstatus, timetonextaction_none, ";
    $sql .= "timetonextaction_days, timetonextaction_hours, timetonextaction_minutes, ";
    $sql .= "day, month, year, fromfield, replytofield, ccfield, bccfield, tofield, ";
    $sql .= "subjectfield, attachmenttype, filename ";
    $sql .= "FROM `{$dbSpellCheck}` WHERE id='$spellid'";
    $result = mysql_query($sql);
    list($updateid, $bodytext, $newincidentstatus, $timetonextaction_none, $timetonextaction_days, $timetonextaction_hours, $timetonextaction_minutes, $day, $month, $year, $fromfield, $replytofield, $ccfield, $bccfield, $tofield, $subjectfield, $attachmenttype, $filename) = mysql_fetch_row($result);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
}
else
{
    // try and stop html getting through in the source text (INL 2July03)
    $bodytext = str_replace('<','&#060;', $bodytext);
    $bodytext = str_replace('>','&#062;', $bodytext);

    $isql = "INSERT INTO `{$dbSpellCheck}` (updateid, bodytext, newincidentstatus, timetonextaction_none, timetonextaction_days, timetonextaction_hours, timetonextaction_minutes, day, month, year, fromfield, replytofield, ccfield, bccfield, tofield, subjectfield, attachmenttype, filename) ";
    $isql .= "VALUES (0, '".mysql_real_escape_string($bodytext)."', '$newincidentstatus', '$timetonextaction_none', '$timetonextaction_days', '$timetonextaction_hours', '$timetonextaction_minutes', '$day', '$month', '$year', '$fromfield', '$replytofield', '$ccfield', '$bccfield', '$tofield', '$subjectfield', '$attachmenttype', '$filename')";
    $result = mysql_query($isql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    if (!$result) trigger_error("Problem inserting spellcheck temp data", E_USER_WARNING);
    $spellid=mysql_insert_id();
}
// removed by INL 10Dec01 - appears not to be needed and causes all slashes to be removed.
// $bodytext=stripslashes($bodytext);

if (isset($changepos) && $changepos>0)
{
    ## echo "Change position $changepos to word: $replacement<br />";
    $texttospell = replace_word(urldecode($bodytext), $changepos, $replacement);
    $sql =  "UPDATE `{$dbSpellCheck}` SET bodytext='".mysql_real_escape_string($texttospell)."' WHERE id='$spellid'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    if (!$result) trigger_error("Problem updating spellcheck temp data", E_USER_WARNING);
    echo $newtext;
}
else
{
    $texttospell=$bodytext;
}
$spelltext = spellcheck_text($texttospell, "&id=$id&spellid=$spellid&step=3&spellcheck=yes");
// $spelltext=$texttospell;
echo "<table summary=\"spellchecker\" width=\"80%\" align=\"center\" class=\"shade2\">";
echo "<tr class='shade1'><td align='center'><em>Spellcheck Complete</em></td></tr>";
echo "<tr><td>";
echo "<p>";
echo nl2br($spelltext);
echo "</p></td></tr>";
echo "</table>";

// INL 2Jul03 the 'bodytext' field is the email we're going to send, not the email we're checking
?>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>?id=<?php echo $id ?>" name="updateform" method="post">
<input name="newincidentstatus" type="hidden" value="<?php echo $newincidentstatus; ?>" />
<input name="timetonextaction_none" type="hidden" value="<?php echo $timetonextaction_none; ?>" />
<input name="timetonextaction_days" type="hidden" value="<?php echo $timetonextaction_days; ?>" />
<input name="timetonextaction_hours" type="hidden" value="<?php echo $timetonextaction_hours; ?>" />
<input name="timetonextaction_minutes" type="hidden" value="<?php echo $timetonextaction_minutes; ?>" />
<input name="day" type="hidden" value="<?php echo $day; ?>" />
<input name="month" type="hidden" value="<?php echo $month; ?>" />
<input name="year" type="hidden" value="<?php echo $year; ?>" />
<input type="hidden" name="fromfield" value="<?php echo $fromfield; ?>" />
<input type="hidden" name="replytofield" value="<?php echo $replytofield; ?>" />
<input type="hidden" name="ccfield" value="<?php echo $ccfield; ?>" />
<input type="hidden" name="bccfield" value="<?php echo $bccfield; ?>" />
<input type="hidden" name="tofield" value="<?php echo $tofield; ?>" />
<input type="hidden" name="subjectfield" value="<?php echo $subjectfield; ?>" />
<input type="hidden" name="attachmenttype" value="<?php echo $attachmenttype; ?>" />
<input type="hidden" name="filename" value="<?php echo $filename; ?>" />
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<input type="hidden" name="bodytext" value="
<?php
$spelltext = strip_tags($spelltext);
$spelltext = str_replace('&#060;', '<', $spelltext);
$spelltext = str_replace('&#062;', '>', $spelltext);
echo urlencode($spelltext); ?>" />
<input type="hidden" name="step" value="3" />
<input type="hidden" name="encoded" value="yes" />
<input type="hidden" name="submit3" value="continue" />
<?php
echo "<p align='center'><input name=\"submit3\" type=\"submit\" value=\"Continue\" /></p></form>";
?>
