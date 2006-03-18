<?php
// edit_service_level.php - Edit a service level
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas

$permission=22; // Administrate
$title = 'Edit Service Level';

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
    echo "<h2>$title</h2>";
    echo "<p align='center'>{$tag} ".priority_name($priority)."</p>";

    $sql = "SELECT * FROM servicelevels WHERE tag='$tag' AND priority='$priority'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    $sla = mysql_fetch_object($result);

    echo "<form name='edit_servicelevel' action='{$_SERVER['PHP_SELF']}' method='post'>";
    echo "<table class='vertical'>";
    echo "<tr><th>Initial Response</th><td><input type='text' size='5' name='initial_response_mins' maxlength='5' value='{$sla->initial_response_mins}' /> Minutes</td></tr>";
    echo "<tr><th>Problem Determination</th><td><input type='text' size='5' name='prob_determ_mins' maxlength='5' value='{$sla->prob_determ_mins}' /> Minutes</td></tr>";
    echo "<tr><th>Action Plan</th><td><input type='text' size='5' name='action_plan_mins' maxlength='5' value='{$sla->action_plan_mins}' /> Minutes</td></tr>";
    echo "<tr><th>Resolution</th><td><input type='text' size='5' name='resolution_days' maxlength='3' value='{$sla->resolution_days}' /> Days</td></tr>";
    echo "<tr><th>Review Days</th><td><input type='text' size='5' name='review_days' maxlength='3' value='{$sla->review_days}' /> Days</td></tr>";
    echo "</table>";
    echo "<input type='hidden' name='action' value='edit' />";
    echo "<input type='hidden' name='tag' value='{$tag}' />";
    echo "<input type='hidden' name='priority' value='{$priority}' />";
    echo "<p align='center'><input type='submit' value='Save' /></p>";
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

    $sql = "UPDATE servicelevels SET initial_response_mins='$initial_response_mins', ";
    $sql .= "prob_determ_mins='$prob_determ_mins', ";
    $sql .= "action_plan_mins='$action_plan_mins', ";
    $sql .= "resolution_days='$resolution_days', ";
    $sql .= "review_days='$review_days' ";
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