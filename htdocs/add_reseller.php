<?php
// add_reseller.php - Add a new reseller contract
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>

@include ('set_include_path.inc.php');
$permission = 63;

require ('db_connect.inc.php');
require ('functions.inc.php');
// This page requires authentication
require ('auth.inc.php');

// External variables
$action = $_REQUEST['action'];

switch ($action)
{
    case 'add':
        $name = $_REQUEST['reseller_name'];

        $errors = 0;
        if (empty($name))
        {
            $_SESSION['formerrors']['name'] = 'Name cannot be empty';
            $errors++;
        }

        if ($errors != 0)
        {
            html_redirect("add_reseller.php", FALSE);
        }
        else
        {
            $sql = "INSERT INTO `{$dbResellers}` (name) VALUES ('$name')";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

            if (!$result)
            {
                $addition_errors = 1;
                $addition_errors_string .= "<p class='error'>Addition of reseller failed</p>\n";
            }


            if ($addition_errors == 1)
            {
                // show addition error message
                include ('htmlheader.inc.php');
                echo $addition_errors_string;
                include ('htmlfooter.inc.php');
            }
            else
            {
                // show success message
                $id = mysql_insert_id();
                journal(CFG_LOGGING_NORMAL, 'Reseller Added', "Reseller $id Added", CFG_JOURNAL_MAINTENANCE, $id);
                clear_form_errors('formerrors');

                html_redirect("main.php");
            }
        }
        break;
    default:
        include ('htmlheader.inc.php');
        echo show_form_errors('add_reseller');
        clear_form_errors('formerrors');
        echo "<h2>{$strAddReseller}</h2>";
        echo "<p align='center'>".sprintf($strMandatoryMarked, "<sup class='red'>*</sup>")."</p>";
        echo "<form action='{$_SERVER['PHP_SELF']}?action=add' method='post' onsubmit=\"return confirm_action('{$strAreYouSureAdd}')\">";
        echo "<table align='center' class='vertical'>";
        echo "<tr><th>{$strName}: <sup class='red'>*</sup></th><td><input type='text' name='reseller_name' /></td></tr>";
        echo "</table>";
        echo "<p align='center'><input name='submit' type='submit' value='{$strAddReseller}' /></p>";
        echo "</form>";
        include ('htmlfooter.inc.php');
        break;
}

?>
