<?php
// holidays.php -
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
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

$title="{$_SESSION['realname']}'s Holidays";

include('htmlheader.inc.php');
echo "<h2>$title</h2>";
?>
<table align='center' width='350'>
<tr><th align='right'>YOUR HOLIDAYS</th></tr>
<tr class='shade1'><td><strong>Annual Holiday Entitlement</strong>:</td></tr>
<tr class='shade2'><td>
<?php $entitlement=user_holiday_entitlement($sit[2]);
    $holidaystaken=user_count_holidays($sit[2], 1);

    echo "$entitlement days, ";
    echo "$holidaystaken taken, ";
    echo $entitlement-$holidaystaken." Remaining";
?>
</td></tr>

<tr class='shade1'><td ><strong>Other Leave Taken</strong>:</td></tr>
<tr class='shade2'><td>
<?php echo user_count_holidays($sit[2], 2)." days sick leave, ";
    echo user_count_holidays($sit[2], 3)." days working away, ";
    echo user_count_holidays($sit[2], 4)." days training";
    echo "<br />";
    echo user_count_holidays($sit[2], 5)." days other leave";
?></td></tr>
<tr class='shade1'><td>&nbsp;</td></tr>
<tr class='shade2'><td><a href="holiday_calendar.php?type=1">My Holiday Calendar</a></td></tr>
<tr class='shade2'><td><a href="book_holidays.php">Book Holidays</a></td></tr>
<tr class='shade2'><td><a href="holiday_calendar.php">Holiday Planner</a></td></tr>
<?php
if (user_permission($sit[2],50)) // Approve holidays
{
    ?>
    <tr class='shade2'><td><a href="holiday_request.php?user=all&amp;mode=approval">Approve/Decline Holiday Requests</a></td></tr>
    <?php
}
?>
</table>



<?php
$sql  = "SELECT * FROM users WHERE status!=0 AND status!=1 ";  // status=0 means left company
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
?>
<table align='center' width='350'>
<tr><th align='right'>WHO IS AWAY TODAY?</th></tr>
<?php
// show results
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
else echo "<tr><td>Nobody</td></tr>\n";
?>
</table>

<table align='center' width='350'>
<tr><th align='right'>YOUR HOLIDAY LIST</th></tr>
<?php

$sql = "SELECT * from holidays WHERE userid='{$sit[2]}' AND approved=0";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
if (mysql_num_rows($result)) 
{
    echo "<tr class='shade2'><td><strong>Dates waiting for approval</strong>:</td></tr>";
    while ($dates = mysql_fetch_array($result))
    {
        echo "<tr class='shade1'><td>";
        echo date('l jS F Y', $dates['startdate']);
        if ($dates['length']=='am') echo " Morning only";
        if ($dates['length']=='pm') echo " Afternoon only";
        echo "</td></tr>\n";
    }
    echo "<tr class='shade1'><td><a href='holiday_request.php'>Send reminder request</a></td></tr>";
}
mysql_free_result($result);

$sql = "SELECT * from holidaytypes";
$tresult = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
while ($holidaytype=mysql_fetch_array($tresult)) 
{
    $sql = "SELECT * FROM holidays WHERE userid='{$sit[2]}' AND type={$holidaytype['id']} ";
    $sql.= "AND approved>0 ORDER BY startdate DESC ";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    if (mysql_num_rows($result))
    {
        echo "<tr class='shade2'><td><strong>{$holidaytype['name']}</strong>:</td></tr>";     
        while ($dates = mysql_fetch_array($result))
        {
            echo "<tr class='shade1'><td>";
            echo date('l jS F Y', $dates['startdate']);
            if ($dates['length']=='am') echo " Morning only";
            if ($dates['length']=='pm') echo " Afternoon only";
            echo "</td></tr>\n";
        }
    }
    mysql_free_result($result);
}

?>
</table>
<?php
include('htmlfooter.inc.php');
?>