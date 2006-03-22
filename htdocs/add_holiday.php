<?php
// add_holiday.php - Adds a holiday to the database
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

$permission=27; // View your calendar
require('db_connect.inc.php');
require('functions.inc.php');
$title="Holiday Calendar";
// This page requires authentication
require('auth.inc.php');

// Valid user

// External Variables
$day = cleanvar($_REQUEST['day']);
$month = cleanvar($_REQUEST['month']);
$year = cleanvar($_REQUEST['year']);
$user = cleanvar($_REQUEST['user']);
$type = cleanvar($_REQUEST['type']);
$length = cleanvar($_REQUEST['length']);

// startdate in unix format
$startdate=mktime(0,0,0,$month,$day,$year);
if ($length=='') $length='day';

// check to see that we're not booking holiday for the past
/*
if ($startdate < $todayrecent && $type==1)
header("Location: holiday_calendar.php?selectedyear=$year&selectedmonth=$month&selectedday=$day&type=$type&length=0&approved=0");
exit;
// -------------------
*/

if (user_permission($sit[2],50)) $approver=TRUE;

// check to see if there is a holiday on this day already.
list($dtype, $dlength, $dapproved)=user_holiday($user, 0, $year, $month, $day, FALSE);
// type above

if ($dapproved==1)
{
    // the holiday has been approved - so don't do anything to it
    // DO NOTHING

    // allow approver to unbook holidays already approved
    if ($length=='0' && $approver==TRUE)
    {
        $sql = "DELETE FROM holidays ";
        $sql .= "WHERE userid='$user' AND startdate='$startdate' AND type='$type' AND approvedby='$sit[2]' ";
        $result = mysql_query($sql);
        // echo $sql;
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        $dlength=0;
        $dapproved=0;
    }
}
else
{
    if ($dtype==1 || $dtype==3 || $dtype==4)
    {
        if ($length=='0')
        {
            // bugbug: doesn't check permission or anything
            $sql = "DELETE FROM holidays ";
            $sql .= "WHERE userid='$user' AND startdate='$startdate' AND type='$type' ";
            $result = mysql_query($sql);
            // echo $sql;
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            $dlength=0;
            $dapproved=0;
        }
        else
        {
            // there is an existing booking so alter it
            $sql = "UPDATE holidays SET length='$length' ";
            $sql .= "WHERE userid='$user' AND startdate='$startdate' AND type='$type' AND length='$dlength'";
            $result = mysql_query($sql);
            // echo $sql;
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            $dlength=$length;
        }
    }
    else
    {
        // there is no holiday on this day, so make one
        $sql = "INSERT INTO holidays ";
        $sql .= "SET userid='$user', type='$type', startdate='$startdate', length='$length' ";
        $result = mysql_query($sql);
        $dlength=$length;
        $approved=0;
    }
}
header("Location: holiday_calendar.php?selectedyear=$year&selectedmonth=$month&selectedday=$day&type=$type&length=$dlength&user=$user&selectedtype=$dtype&approved=$dapproved");
exit;
?>
