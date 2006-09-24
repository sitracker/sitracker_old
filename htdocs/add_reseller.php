<?php
// add_maintenance.php - Add a new maintenance contract
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>

$permission=63; //FIXME define a permission

require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

// External variables
$action = $_REQUEST['action'];

switch($action)
{
    case 'add_reseller':
        $name = $_REQUEST['reseller_name'];
        $sql = "INSERT INTO resellers (name) VALUES ('$name')";
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
            include('htmlheader.inc.php');
            echo $addition_errors_string;
            include('htmlfooter.inc.php');
        }
        else
        {
            // show success message
            $id=mysql_insert_id();
            journal(CFG_LOGGING_NORMAL, 'Reseller Added', "Reseller $id Added", CFG_JOURNAL_MAINTENANCE, $id);

            confirmation_page("2", "main.php", "<h2>Reseller Added Successfully</h2><h5>Please wait while you are redirected...</h5>");
        }
        break;
    default:
        include('htmlheader.inc.php');
        echo "<script type=\"text/javascript\">";
        echo "function confirm_submit()
        {
            return window.confirm('Are you sure you want to add this reseller?');
        }
        </script>";
        echo "<h2>Add Reseller</h2>";
        echo "<p align='center'>Mandatory fields are marked <sup class='red'>*</sup></p>";
        echo "<form action=\"".$_SERVER['PHP_SELF']."?action=add\" method=\"post\" onsubmit=\"return confirm_submit()\">";
        echo "<table align='center' class='vertical'>";
        echo "<tr><th>Name: <sup class='red'>*</sup></th><td><input type='text' name='reseller_name' /></td></tr>";
        echo "</table>";
        echo "<p align='center'><input name=\"submit\" type=\"submit\" value=\"Add Reseller\" /></p>";
        echo "<input type='hidden' value='add_reseller' name='action' />";
        echo "</form>";
        include('htmlfooter.inc.php');
}

?>