<?php
// edit_holidays.php - Reset holiday entitlements
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

$permission=22; // Administrate
require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

$title='Edit Holidays';

switch($_REQUEST['action'])
{
    case 'save':
        $max_carryover = cleanvar($_REQUEST['max_carryover']);
        $archivedate = strtotime($_REQUEST['archivedate']);
        if ($archivedate < 1000) $archivedate = $now;
        $default_entitlement = cleanvar($_REQUEST['default_entitlement']);
        $sql = "SELECT * FROM users WHERE status >= 1 ORDER BY realname ASC";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        while ($users = mysql_fetch_object($result))
        {
            $fieldname="user{$users->id}";
            if ($_REQUEST[$fieldname]=='yes')
            {
                $orig_entitlement = user_holiday_entitlement($users->id);
                $used_holidays = user_count_holidays($users->id, 1);
                $remaining_holidays = $orig_entitlement - $used_holidays;
                if ($remaining_holidays < $max_carryover) $carryover = $remaining_holidays;
                else $carryover = $max_carryover;
                $new_entitlement = $default_entitlement + $carryover;

                // Archive previous holiday
                $hsql = "UPDATE holidays SET approved = approved+10 WHERE approved <= 7 AND userid={$users->id} AND startdate < $archivedate";
                mysql_query($hsql);
                if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

                // Update Holiday Entitlement
                $usql = "UPDATE users SET holiday_entitlement = $new_entitlement WHERE id={$users->id} LIMIT 1";
                mysql_query($usql);
                if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
            }
        }
        header("Location: edit_holidays.php");
        exit;
    break;


    case 'form':
    default:
        include('htmlheader.inc.php');
        echo "<h2>$title</h2>";

        $sql = "SELECT * FROM users WHERE status >= 1 ORDER BY realname ASC";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

        echo "<form name='editholidays' action='{$_SERVER['PHP_SELF']}' method='post'>";
        echo "<p>Reset holiday entitlement and carry-over up to <em>n</em> days of unused holiday</p>";
        echo "<div align='center'>Default new entitlement: <input type='text' name='default_entitlement' value='20' size='4' />, ";
        echo "Max. Carry-over <input type='text' name='max_carryover' value='5' size='4' /> days";
        echo "<br />Archive days booked prior to <input type='text' id='archivedate' name='archivedate' size='10' value='".date('Y-m-d')."' />\n ";
        echo date_picker('editholidays.archivedate');
        echo "</div>";

        echo "<table align='center'>";
        echo "<tr><th></th>";
        echo colheader('realname', 'Name', FALSE);
        echo colheader('entitlement', 'Entitlement', FALSE);
        echo colheader('holidaysused', 'Holidays Used', FALSE);
        echo colheader('holidaysremaining', 'Holidays Remaining', FALSE);
        echo "</tr>";
        while ($users = mysql_fetch_object($result))
        {
            // define class for table row shading
            if ($shade=='shade1') $shade = "shade2";
            else $shade = "shade1";
            echo "<tr class='$shade'>";
            echo "<td><input type='checkbox' name='user{$users->id}' value='yes' /></td>";
            echo "<td>{$users->realname} ({$users->username})</td>";

            $entitlement = user_holiday_entitlement($users->id);
            $used_holidays = user_count_holidays($users->id, 1);
            $remaining_holidays = $entitlement - $used_holidays;

            echo "<td style='text-align: right;'>{$entitlement}</td>";
            echo "<td style='text-align: right;'>{$used_holidays}</td>";
            echo "<td style='text-align: right;'>{$remaining_holidays}</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p>";
        echo "<input type='hidden' name='action' value='save' />";
        echo "<input type='submit' name='submit' value='Save' /></p>";
        echo "</form>";
        include('htmlfooter.inc.php');
    break;
}
?>