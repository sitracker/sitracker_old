<?php
// holidays.php -
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// This Page Is Valid XHTML 1.0 Transitional!  13Sep06

$permission=4; // Edit your profile

require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

$approver = user_permission($sit[2],50); // Approve holidays

if (!empty($_REQUEST['user'])) $user = cleanvar($_REQUEST['user']);
else $user = $sit[2];

if ($user==$sit[2]) $title="{$_SESSION['realname']}'s Holidays";
else $title = user_realname($user)."'s Holidays";

include('htmlheader.inc.php');
echo "<h2>$title</h2>";

echo "<p align='center'>";
echo "<a href='book_holidays.php?user={$user}'>Book Holidays</a>";
echo " | <a href='holiday_calendar.php'>Holiday Planner</a>";
if ($approver)
{
    echo " | <a href='holiday_request.php?user=";
    if (user==$sit[2]) echo "all";
    else echo $user;
    echo "&amp;mode=approval'>Approve/Decline Holiday Requests</a>";
}

// Entitlement
if ($user==$sit[2] OR $approver==TRUE)
{
    // Only shown when viewing your own holidays or when you're an approver
    echo "<table align='center' width='450'>\n";
    echo "<tr><th align='right'>HOLIDAYS</th></tr>\n";
    echo "<tr class='shade1'><td><strong>Annual Holiday Entitlement</strong>:</td></tr>\n";
    echo "<tr class='shade2'><td>";
    $entitlement=user_holiday_entitlement($user);
    $holidaystaken=user_count_holidays($user, 1);
    echo "$entitlement days, ";
    echo "$holidaystaken taken, ";
    echo $entitlement-$holidaystaken." Remaining";
    echo "</td></tr>\n";
    echo "<tr class='shade1'><td ><strong>Other Leave Taken</strong>:</td></tr>\n";
    echo "<tr class='shade2'><td>";
    echo user_count_holidays($user, 2)." days sick leave, ";
    echo user_count_holidays($user, 3)." days working away, ";
    echo user_count_holidays($user, 4)." days training";
    echo "<br />";
    echo user_count_holidays($user, 5)." days other leave";
    echo "</td></tr>\n";
    echo "</table>\n";
}

// Holiday List
echo "<table align='center' width='450'>\n";
echo "<tr><th align='right' colspan='4'>HOLIDAY LIST</th></tr>\n";
$sql = "SELECT * from holidays, holidaytypes WHERE holidays.type=holidaytypes.id AND userid='{$user}' AND approved=0 ORDER BY startdate ASC";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
$numwaiting=mysql_num_rows($result);
if ($numwaiting > 0)
{
    if ($user==$sit[2])
    {
        // Show dates waiting approval, but only to owner
        echo "<tr class='shade2'><td colspan='4'><strong>Dates not yet approved</strong>:</td></tr>";
        while ($dates = mysql_fetch_array($result))
        {
            echo "<tr class='shade1'><td>{$dates['name']}</td>";
            echo "<td>".date('l', $dates['startdate'])." ";
            if ($dates['length']=='am') echo "<u>Morning</u> ";
            if ($dates['length']=='pm') echo "<u>Afternoon</u> ";
            echo date('jS F Y', $dates['startdate']);
            echo "</td>";
            echo "<td>";
            echo holiday_approval_status($dates['approved'], $dates['approvedby']);
            echo "</td>";
            echo "<td>";
            if ($dates['length']=='pm' OR $dates['length']=='day') echo "<a href='add_holiday.php?type={$dates['type']}&amp;user=$user&amp;year=".date('Y',$dates['startdate'])."&amp;month=".date('m',$dates['startdate'])."&amp;day=".date('d',$dates['startdate'])."&amp;length=am' onclick=\"return window.confirm('".date('l jS F Y', $dates['startdate']).": Are you sure you want to make this Morning only?');\" title='Make this Morning only'>am</a> | ";
            if ($dates['length']=='am' OR $dates['length']=='day') echo "<a href='add_holiday.php?type={$dates['type']}&amp;user=$user&amp;year=".date('Y',$dates['startdate'])."&amp;month=".date('m',$dates['startdate'])."&amp;day=".date('d',$dates['startdate'])."&amp;length=pm' onclick=\"return window.confirm('".date('l jS F Y', $dates['startdate']).": Are you sure you want to make this Afternoon only?');\" title='Make this Afternoon only'>pm</a> | ";
            if ($dates['length']=='am' OR $dates['length']=='pm') echo "<a href='add_holiday.php?type={$dates['type']}&amp;user=$user&amp;year=".date('Y',$dates['startdate'])."&amp;month=".date('m',$dates['startdate'])."&amp;day=".date('d',$dates['startdate'])."&amp;length=day' onclick=\"return window.confirm('".date('l jS F Y', $dates['startdate']).": Are you sure you want to make this for the Full Day?');\" title='Make this for the Full Day'>all day</a> | ";
            if ($sit[2]==$user) echo "<a href='add_holiday.php?year=".date('Y',$dates['startdate'])."&amp;month=".date('m',$dates['startdate'])."&amp;day=".date('d',$dates['startdate'])."&amp;user={$sit[2]}&amp;type={$dates['type']}&amp;length=0&return=holidays' onclick=\"return window.confirm('".date('l jS F Y', $dates['startdate']).": Are you sure you want to cancel this?');\" title='Cancel this holiday'>cancel</a>";
            echo "</td></tr>\n";
        }
        echo "<tr class='shade1'><td colspan='4'><a href='holiday_request.php?action=resend'>Send reminder request</a></td></tr>";
    }
}
mysql_free_result($result);

$sql = "SELECT * from holidaytypes";
$tresult = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
while ($holidaytype=mysql_fetch_array($tresult))
{
    $sql = "SELECT *, from_unixtime(startdate) AS start FROM holidays WHERE userid='{$user}' AND type={$holidaytype['id']} ";
    $sql.= "AND (approved=1 OR (approved=11 AND startdate >= $now)) ORDER BY startdate ASC ";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $numtaken = mysql_num_rows($result);
    if ($numtaken > 0)
    {
        echo "<tr class='shade2'><td colspan='4'><strong>{$holidaytype['name']}</strong>:</td></tr>";
        while ($dates = mysql_fetch_array($result))
        {
            echo "<tr class='shade1'>";
            echo "<td colspan='2'>".date('l', $dates['startdate'])." ";
            if ($dates['length']=='am') echo "<u>Morning</u> ";
            if ($dates['length']=='pm') echo "<u>Afternoon</u> ";
            echo date('jS F Y', $dates['startdate']);
            echo "</td>";
            echo "<td colspan='2'>";
            echo holiday_approval_status($dates['approved'], $dates['approvedby']);
            echo "</td></tr>\n";
        }
    }
    mysql_free_result($result);
}

if ($numtaken < 1 AND $numwaiting < 1) echo "<tr class='shade2'><td colspan='4'><em>None</em</td></tr>\n";
echo "</table>\n";


// AWAY TODAY
if ($user==$sit[2])
{
    // Only show when viewing your own holiday page
    $sql  = "SELECT * FROM users WHERE status!=0 AND status!=1 ";  // status=0 means left company
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    echo "<table align='center' width='450'>";
    echo "<tr><th align='right'>WHO IS AWAY TODAY?</th></tr>\n";
    if (mysql_num_rows($result) >=1)
    {
        while ($users = mysql_fetch_array($result))
        {
            echo "<tr><td class='shade2'>";
            $title=userstatus_name($users["status"]);
            $title.=" - ";
            if ($users['accepting']=='Yes') $title .= "Accepting";
            else $title .= "Not Accepting";
            $title .= " calls";
            if (!empty($users['message'])) $title.="\n".$users['message'];

            echo "<strong>{$users['realname']}</strong>, $title";
            echo "</td></tr>\n";
        }
    }
    else echo "<tr class='shade2'><td><em>Nobody</em></td></tr>\n";
    echo "</table>";
}
include('htmlfooter.inc.php');
?>
