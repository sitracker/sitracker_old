<?php
// edit_service_level.php - Edit a service level
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

$permission=53; // Edit Service Levels
$title = $strEditServiceLevel;

require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

// External variables
$tag = cleanvar($_REQUEST['tag']);
$priority = cleanvar($_REQUEST['priority']);
$action = $_REQUEST['action'];

if (empty($action) OR $action == "showform")
{
    include('htmlheader.inc.php');
    echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/sla.png' width='32' height='32' alt='' /> ";
    echo "$title</h2>";
    echo "<p align='center'>{$tag} ".priority_name($priority)."</p>";

    $sql = "SELECT * FROM servicelevels WHERE tag='$tag' AND priority='$priority'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    $sla = mysql_fetch_object($result);

    // FIXME i18n days/minutes
    echo "<form name='edit_servicelevel' action='{$_SERVER['PHP_SELF']}' method='post'>";
    echo "<table class='vertical'>";
    echo "<tr><th>{$strInitialResponse} <img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/initialresponse.png' width='16' height='16' alt='' /></th>";
    echo "<td><input type='text' size='5' name='initial_response_mins' maxlength='5' value='{$sla->initial_response_mins}' /> {$strMinutes}</td></tr>";
    echo "<tr><th>{$strProblemDefinition} <img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/probdef.png' width='16' height='16' alt='' /></th>";
    echo "<td><input type='text' size='5' name='prob_determ_mins' maxlength='5' value='{$sla->prob_determ_mins}' /> {$strMinutes}</td></tr>";
    echo "<tr><th>{$strActionPlan} <img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/actionplan.png' width='16' height='16' alt='' /></th>";
    echo "<td><input type='text' size='5' name='action_plan_mins' maxlength='5' value='{$sla->action_plan_mins}' /> {$strMinutes}</td></tr>";
    echo "<tr><th>{$strResolutionReprioritisation} <img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/solution.png' width='16' height='16' alt='' /></th>";
    echo "<td><input type='text' size='5' name='resolution_days' maxlength='3' value='{$sla->resolution_days}' /> {$strDays}</td></tr>";
    echo "<tr><th>{$strReview} <img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/review.png' width='16' height='16' alt='' /></th>";
    echo "<td><input type='text' size='5' name='review_days' maxlength='3' value='{$sla->review_days}' /> {$strDays}</td></tr>";
    echo "<tr><th>{$strTimed} <img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/sla.png' width='16' height='16' alt='' /></th><td>";    
    if($sla->timed == 'yes') echo "<input type='checkbox' name='timed' checked>";
    else echo "<input type='checkbox' name='timed'>";
    echo "</td></tr>";
    echo "</table>";
    echo "<input type='hidden' name='action' value='edit' />";
    echo "<input type='hidden' name='tag' value='{$tag}' />";
    echo "<input type='hidden' name='priority' value='{$priority}' />";
    echo "<p align='center'><input type='submit' value='{$strSave}' /></p>";
    echo "</form>";
    include('htmlfooter.inc.php');
}
elseif ($action == "edit")
{
    // External variables
    $initial_response_mins = cleanvar($_POST['initial_response_mins']);
    $prob_determ_mins = cleanvar($_POST['prob_determ_mins']);
    $action_plan_mins = cleanvar($_POST['action_plan_mins']);
    $resolution_days = cleanvar($_POST['resolution_days']);
    $review_days = cleanvar($_POST['review_days']);
    if($_POST['timed'] != 'on') $timed = 0;
    else $timed = 1;

    $sql = "UPDATE servicelevels SET initial_response_mins='$initial_response_mins', ";
    $sql .= "prob_determ_mins='$prob_determ_mins', ";
    $sql .= "action_plan_mins='$action_plan_mins', ";
    $sql .= "resolution_days='$resolution_days', ";
    $sql .= "review_days='$review_days', ";
    $sql .= "timed='$timed' ";
    $sql .= "WHERE tag='$tag' AND priority='$priority'";
    mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    if (mysql_affected_rows() == 0) trigger_error("UPDATE affected zero rows",E_USER_WARNING);
    else
    {
        header("Location: service_levels.php");
        exit;
    }
}
?>
