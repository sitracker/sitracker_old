<?php
// reseller_add.php - Add a new reseller contract
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>

@include ('set_include_path.inc.php');
$permission = 63;

require ($lib_path.'db_connect.inc.php');
require ($lib_path.'functions.inc.php');
// This page requires authentication
require ($lib_path.'auth.inc.php');

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
            html_redirect($_SERVER['PHP_SELF'], FALSE);
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
                include ('./inc/htmlheader.inc.php');
                echo $addition_errors_string;
                include ('./inc/htmlfooter.inc.php');
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
        include ('./inc/htmlheader.inc.php');
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
        include ('./inc/htmlfooter.inc.php');
        break;
}

?>
