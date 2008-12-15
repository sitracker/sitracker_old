<?php
// note_add.php - Add a new note
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

@include ('set_include_path.inc.php');
$permission = 0; // Allow all auth users

require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

// External variables
$action = $_REQUEST['action'];

switch ($action)
{
    case 'addnote':
        // External variables
        $link = cleanvar($_REQUEST['link']);
        $refid = cleanvar($_REQUEST['refid']);
        $bodytext = cleanvar($_REQUEST['bodytext'],FALSE,FALSE);
        $rpath = cleanvar($_REQUEST['rpath']);

        // Input validation
        // Validate input
        $error=array();
        if (empty($link)) $error[] = 'Link must not be zero or blank';
        if (empty($refid)) $error[] = 'Refid must not be zero or blank';
        if (empty($bodytext)) $error[] = 'Note must not be blank';
        if (count($error) >= 1)
        {
            include ('htmlheader.inc.php');
            echo "<p class='error'>Please check the data you entered</p>";
            echo "<ul class='error'>";
            foreach ($error AS $err)
            {
                echo "<li>$err</li>";
            }
            echo "</ul>";
            include ('htmlfooter.inc.php');
        }
        else
        {
            $sql = "INSERT INTO `{$dbNotes}` (userid, bodytext, link, refid) ";
            $sql .= "VALUES ('{$sit[2]}', '{$bodytext}', '{$link}', '{$refid}')";
            mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
            if (mysql_affected_rows() < 1) trigger_error("Note insert failed",E_USER_ERROR);

            $sql = "UPDATE `{$dbTasks}` SET lastupdated=NOW() WHERE id=$refid";
            mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

            html_redirect($rpath);
        }
    break;

    case '':
    default:
        include ('htmlheader.inc.php');
        echo add_note_form(0,0);
        include ('htmlfooter.inc.php');
}

?>