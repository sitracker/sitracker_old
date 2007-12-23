<?php
// add_feedback_form.php - Form for adding feedback forms
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// by Ivan Lucas, June 2004

@include ('set_include_path.inc.php');
$permission = 48; // Add Feedback Forms

require ('db_connect.inc.php');
require ('functions.inc.php');
// This page requires authentication
require ('auth.inc.php');

// External Variables
$formid = cleanvar($_REQUEST['id']);
$action = $_REQUEST['action'];
$name = cleanvar($_POST['name']);
$description = cleanvar($_POST['description']);
$introduction = cleanvar($_POST['introduction']);
$thanks = cleanvar($_POST['thanks']);
$numquestions = cleanvar($_POST['numquestions']);

switch ($action)
{
    case 'save':
        $sql = "INSERT INTO `{$dbFeedbackForms}` (name, description, introduction, thanks) VALUES (";
        $sql .= "'{$name}', ";
        $sql .= "'{$description}', ";
        $sql .= "'{$introduction}', ";
        $sql .= "'{$thanks}') ";
        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        $formid=mysql_insert_id();
        header("Location: add_feedback_question.php?fid=1&id=1&maxq={$numquestions}");
        exit;
    break;

    default:
        $title='Add feedback form';
        include ('htmlheader.inc.php');
        echo "<h2 align='center'>$title</h2>\n";

        echo "<form action='{$_SERVER['PHP_SELF']}' method='post'>";
        echo "<table summary='Form' align='center' class='vertical'>";
        echo "<tr>";

        echo "<th>Name:</th>";
        echo "<td><input type='text' name='name' size='35' maxlength='255' value='{$form->name}' /></td>";
        echo "</tr>\n<tr>";

        echo "<th>{$strDescription}:<br />(For Internal Use, not displayed)</th>";
        echo "<td><textarea name='description' cols='80' rows='6'>";
        echo $form->description."</textarea></td>";
        echo "</tr>\n<tr>";

        echo "<th>Introduction:<br />(Simple HTML Allowed)</th>";
        echo "<td><textarea name='introduction' cols='80' rows='10'>";
        echo $form->introduction."</textarea></td>";
        echo "</tr>\n<tr>";

        echo "<th>Closing Thanks:<br />(Simple HTML Allowed)</th>";
        echo "<td><textarea name='thanks' cols='80' rows='10'>";
        echo $form->thanks."</textarea></td>";
        echo "</tr>\n";

        echo "<th>How many questions do you want?</th>";
        echo "<td><input type='text' size='2' maxlength='2' name='numquestions' value='5' />";
        echo "</td></tr>\n";

        echo "<tr>";
        echo "<td><input type='hidden' name='id' value='{$formid}' />";
        echo "<input type='hidden' name='action' value='save' /></td>";
        echo "<td><input type='submit' value='{$strSave}' /></td>";
        echo "</tr>";

        echo "</table>";
        include ('htmlfooter.inc.php');
}

?>