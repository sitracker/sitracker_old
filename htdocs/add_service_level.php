<?php
// add_service_level.php - Add a new service level
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

@include('set_include_path.inc.php');
$permission=22; // Administrate

require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

// External variables
$tag = mysql_real_escape_string($_REQUEST['tag']);
$priority = mysql_real_escape_string($_REQUEST['priority']);
$action = $_REQUEST['action'];

if (empty($action) OR $action == "showform")
{
    $title = $strAddServiceLevel;
    include('htmlheader.inc.php');
    echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/sla.png' width='32' height='32' alt='' /> ";
    echo "$title</h2>";
    echo "<form name='add_servicelevel' action='{$_SERVER['PHP_SELF']}' method='post'>";

    echo "<p align='center'>{$strTag}: <input type='text' name='tag' value='' /></p>";

    echo "<table align='center'>";
    echo "<tr><th>{$strPriority}</th><th>{$strInitialResponse}</th>";
    echo "<th>{$strProblemDefinition}</th><th>{$strActionPlan}</th><th>{$strResolutionReprioritisation}</th>";
    echo "<th>{$strReview}</th></tr>";
    echo "<tr>";
    echo "<td>{$strLow}</td>";
    echo "<td><input type='text' size='5' name='low_initial_response_mins' maxlength='5' value='320' /> Minutes</td>";
    echo "<td><input type='text' size='5' name='low_prob_determ_mins' maxlength='5' value='380' /> Minutes</td>";
    echo "<td><input type='text' size='5' name='low_action_plan_mins' maxlength='5' value='960' /> Minutes</td>";
    echo "<td><input type='text' size='5' name='low_resolution_days' maxlength='3' value='14' /> Days</td>";
    echo "<td><input type='text' size='5' name='low_review_days' maxlength='3' value='28' /> Days</td>";
    echo "</tr>\n";
    echo "<tr>";
    echo "<td>{$strMedium}</td>";
    echo "<td><input type='text' size='5' name='med_initial_response_mins' maxlength='5' value='240' /> Minutes</td>";
    echo "<td><input type='text' size='5' name='med_prob_determ_mins' maxlength='5' value='320' /> Minutes</td>";
    echo "<td><input type='text' size='5' name='med_action_plan_mins' maxlength='5' value='960' /> Minutes</td>";
    echo "<td><input type='text' size='5' name='med_resolution_days' maxlength='3' value='10' /> Days</td>";
    echo "<td><input type='text' size='5' name='med_review_days' maxlength='3' value='20' /> Days</td>";
    echo "</tr>\n";
    echo "<tr>";
    echo "<td>{$strHigh}</td>";
    echo "<td><input type='text' size='5' name='hi_initial_response_mins' maxlength='5' value='120' /> Minutes</td>";
    echo "<td><input type='text' size='5' name='hi_prob_determ_mins' maxlength='5' value='180' /> Minutes</td>";
    echo "<td><input type='text' size='5' name='hi_action_plan_mins' maxlength='5' value='480' /> Minutes</td>";
    echo "<td><input type='text' size='5' name='hi_resolution_days' maxlength='3' value='7' /> Days</td>";
    echo "<td><input type='text' size='5' name='hi_review_days' maxlength='3' value='14' /> Days</td>";
    echo "</tr>\n";
    echo "<tr>";
    echo "<td>{$strCritical}</td>";
    echo "<td><input type='text' size='5' name='crit_initial_response_mins' maxlength='5' value='60' /> Minutes</td>";
    echo "<td><input type='text' size='5' name='crit_prob_determ_mins' maxlength='5' value='120' /> Minutes</td>";
    echo "<td><input type='text' size='5' name='crit_action_plan_mins' maxlength='5' value='240' /> Minutes</td>";
    echo "<td><input type='text' size='5' name='crit_resolution_days' maxlength='3' value='3' /> Days</td>";
    echo "<td><input type='text' size='5' name='crit_review_days' maxlength='3' value='6' /> Days</td>";
    echo "</tr>\n";
    echo "</table>";

    echo "<input type='hidden' name='action' value='edit' />";
    echo "<p align='center'><input type='submit' value='{$strSave}' /></p>";
    echo "</form>";
    include('htmlfooter.inc.php');
}
elseif ($action == "edit")
{
    // External variables
    $tag = trim(mysql_real_escape_string(strip_tags($_POST['tag'])));
    $low_initial_response_mins = mysql_real_escape_string($_POST['low_initial_response_mins']);
    $low_prob_determ_mins = mysql_real_escape_string($_POST['low_prob_determ_mins']);
    $low_action_plan_mins = mysql_real_escape_string($_POST['low_action_plan_mins']);
    $low_resolution_days = mysql_real_escape_string($_POST['low_resolution_days']);
    $low_review_days = mysql_real_escape_string($_POST['low_review_days']);
    $med_initial_response_mins = mysql_real_escape_string($_POST['med_initial_response_mins']);
    $med_prob_determ_mins = mysql_real_escape_string($_POST['med_prob_determ_mins']);
    $med_action_plan_mins = mysql_real_escape_string($_POST['med_action_plan_mins']);
    $med_resolution_days = mysql_real_escape_string($_POST['med_resolution_days']);
    $med_review_days = mysql_real_escape_string($_POST['med_review_days']);
    $hi_initial_response_mins = mysql_real_escape_string($_POST['hi_initial_response_mins']);
    $hi_prob_determ_mins = mysql_real_escape_string($_POST['hi_prob_determ_mins']);
    $hi_action_plan_mins = mysql_real_escape_string($_POST['hi_action_plan_mins']);
    $hi_resolution_days = mysql_real_escape_string($_POST['hi_resolution_days']);
    $hi_review_days = mysql_real_escape_string($_POST['hi_review_days']);
    $crit_initial_response_mins = mysql_real_escape_string($_POST['crit_initial_response_mins']);
    $crit_prob_determ_mins = mysql_real_escape_string($_POST['crit_prob_determ_mins']);
    $crit_action_plan_mins = mysql_real_escape_string($_POST['crit_action_plan_mins']);
    $crit_resolution_days = mysql_real_escape_string($_POST['crit_resolution_days']);
    $crit_review_days = mysql_real_escape_string($_POST['crit_review_days']);

    // Check input
    $errors=0;
    if (empty($tag)) $errors++;
    if ($errors >= 1)
    {
        echo "<p class='error'>Invalid input. Please go back and check you filled all fields correctly.</p>";
        exit;
    }
    // FIXME as temporary measure until we've completely stopped using ID's, fill in the id field
    // Find highest ID number used, and set the new ID to be one more
    $sql = "SELECT id FROM `{$dbServiceLevels}` ORDER BY id DESC LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    list($newslid) = mysql_fetch_row($result);
    $newslid++;

    // Insert low
    $sql = "INSERT INTO `{$dbServiceLevels}` (id, tag, priority, initial_response_mins, prob_determ_mins, action_plan_mins, resolution_days, review_days) VALUES (";
    $sql .= "'$newslid', '$tag', '1', ";
    $sql .= "'$low_initial_response_mins', ";
    $sql .= "'$low_prob_determ_mins', ";
    $sql .= "'$low_action_plan_mins', ";
    $sql .= "'$low_resolution_days', ";
    $sql .= "'$low_review_days')";
    mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    if (mysql_affected_rows() == 0) trigger_error("INSERT affected zero rows",E_USER_WARNING);

    // Insert medium
    $sql = "INSERT INTO `{$dbServiceLevels}` (id, tag, priority, initial_response_mins, prob_determ_mins, action_plan_mins, resolution_days, review_days) VALUES (";
    $sql .= "'$newslid', '$tag', '2', ";
    $sql .= "'$med_initial_response_mins', ";
    $sql .= "'$med_prob_determ_mins', ";
    $sql .= "'$med_action_plan_mins', ";
    $sql .= "'$med_resolution_days', ";
    $sql .= "'$med_review_days')";
    mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    if (mysql_affected_rows() == 0) trigger_error("INSERT affected zero rows",E_USER_WARNING);

    // Insert high
    $sql = "INSERT INTO `{$dbServiceLevels}` (id, tag, priority, initial_response_mins, prob_determ_mins, action_plan_mins, resolution_days, review_days) VALUES (";
    $sql .= "'$newslid', '$tag', '3', ";
    $sql .= "'$hi_initial_response_mins', ";
    $sql .= "'$hi_prob_determ_mins', ";
    $sql .= "'$hi_action_plan_mins', ";
    $sql .= "'$hi_resolution_days', ";
    $sql .= "'$hi_review_days')";
    mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    if (mysql_affected_rows() == 0) trigger_error("INSERT affected zero rows",E_USER_WARNING);

    // Insert critical
    $sql = "INSERT INTO `{$dbServiceLevels}` (id, tag, priority, initial_response_mins, prob_determ_mins, action_plan_mins, resolution_days, review_days) VALUES (";
    $sql .= "'$newslid', '$tag', '4', ";
    $sql .= "'$crit_initial_response_mins', ";
    $sql .= "'$crit_prob_determ_mins', ";
    $sql .= "'$crit_action_plan_mins', ";
    $sql .= "'$crit_resolution_days', ";
    $sql .= "'$crit_review_days')";
    mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    if (mysql_affected_rows() == 0) trigger_error("INSERT affected zero rows",E_USER_WARNING);

    header("Location: service_levels.php");
    exit;
}
?>