<?php
// holidays.php -
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// TODO target v3.24 link this on the menu

$permission=4; // Edit your profile

require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

include('htmlheader.inc.php');
?>
<h2><?php echo $CONFIG['application_shortname'] ?> Holidays</h2>

<p>
<table align='center' border='0' cellpadding='2' cellspacing='0' width='350'>
<tr><td align='right' class='shade1' width='350'><p class=sectiontitle>YOUR HOLIDAYS</p></td></tr>
<tr><td align='left' class='shade1'><strong>Annual Holiday Entitlement</strong>:</td></tr>
<tr><td class='shade2'>
<?php $entitlement=user_holiday_entitlement($sit[2]);
    $holidaystaken=user_count_holidays($sit[2], 1);

    echo "$entitlement days, ";
    echo "$holidaystaken taken, ";
    echo $entitlement-$holidaystaken." Remaining";
?>
</td></tr>


<tr><td align='left' class='shade1'><strong>Other Leave Taken</strong>:</td></tr>
<tr><td class='shade2'>
<?php echo user_count_holidays($sit[2], 2)." days sick leave, ";
    echo user_count_holidays($sit[2], 3)." days working away, ";
    echo user_count_holidays($sit[2], 4)." days training";
    echo "<br />";
    echo user_count_holidays($sit[2], 5)." days other leave";
?></td></tr>
<tr><td align='left' class='shade1'>&nbsp;</td></tr>

<tr><td class='shade2' width=350><a href="book_holidays.php">Book Holidays</a></td></tr>
<tr><td class='shade2' width=350><a href="reports/holiday_chart.php">Holiday Chart</a></td></tr>
<?php
if (user_permission($sit[2],50)) // Approve holidays
{
    ?>
    <tr><td class='shade2' width=350><a href="holiday_request.php?user=all&mode=approval">Approve/Decline Holiday Requests</a></td></tr>
    <?php
}
?>


</table>
<?php
$sql  = "SELECT * FROM users WHERE status!=0 AND status!=1 ";  // status=0 means left company
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

if (mysql_num_rows($result) >= 1)
{
    ?>
    <table align='center' border=0 cellpadding=2 cellspacing=0 width=350>
    <tr><td align='right' class='shade1' width=350><p class=sectiontitle>WHO IS AWAY TODAY?</p></td></tr>
    <?php
    // show results
    while ($users = mysql_fetch_array($result))
    {
    echo "<tr><td class='shade2' width=350>";
    $title=userstatus_name($users["status"]);
    $title.=" - ";
    if ($users['accepting']=='Yes') $title .= "Accepting";
    else $title .= "Not Accepting";
    $title .= " calls";
    if (!empty($users['message'])) $title.="\n".$users['message'];

    echo "<strong>{$users['realname']}</strong>, $title";
    echo "</td></tr>\n";
    }
    ?>
    </table>
<table align='center' border=0 cellpadding=2 cellspacing=0 width=350>
<tr><td align='right' class='shade1' width=350><p class=sectiontitle>YOUR HOLIDAY LIST</p></td></tr>
    <?php
    echo "<tr><td align='left' class='shade2'><b>Holidays</b>:</td></tr>";
    $sql = "SELECT * FROM holidays WHERE userid='{$sit[2]}' AND type=1 ORDER BY startdate DESC ";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    while ($dates = mysql_fetch_array($result))
    {
    echo "<tr><td class='shade1' width=350>";
    echo date('l jS F Y', $dates['startdate']);
    if ($dates['length']=='am') echo " Morning only";
    if ($dates['length']=='pm') echo " Afternoon only";
    echo " - ".holiday_approval_status($dates['approved']);
    echo "</td></tr>\n";
    }
    echo "<tr><td align='left' class='shade2'><b>Sickness</b>:</td></tr>";
    $sql = "SELECT * FROM holidays WHERE userid='{$sit[2]}' AND type=2 ORDER BY startdate DESC ";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    while ($dates = mysql_fetch_array($result))
    {
    echo "<tr><td class='shade1' width=350>";
    if ($dates['approved']==1) echo "<span style='background: green; color: white;'>";
    echo date('l jS F Y', $dates['startdate']);
    if ($dates['length']=='am') echo " Morning only";
    if ($dates['length']=='pm') echo " Afternoon only";
    echo " - ".holiday_approval_status($dates['approved']);
    if ($dates['approved']==1) echo "</span>";
    echo "</td></tr>\n";
    }
    echo "<tr><td align='left' class='shade2'><b>Working Away</b>:</td></tr>";
    $sql = "SELECT * FROM holidays WHERE userid='{$sit[2]}' AND type=3 ORDER BY startdate DESC ";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    while ($dates = mysql_fetch_array($result))
    {
    echo "<tr><td class='shade1' width=350>";
    if ($dates['startdate'] < $now) echo "<span style='color: #555555;'>";
    elseif ($dates['approved']==1) echo "<span style='background: green; color: white;'>";
    else echo "<span>";
    echo date('l jS F Y', $dates['startdate']);
    if ($dates['length']=='am') echo " Morning only";
    if ($dates['length']=='pm') echo " Afternoon only";
    echo " - ".holiday_approval_status($dates['approved']);
    echo "</span>";
    echo "</td></tr>\n";
    }
    echo "<tr><td align='left' class='shade2'><b>Training</b>:</td></tr>";
    $sql = "SELECT * FROM holidays WHERE userid='{$sit[2]}' AND type=4 ORDER BY startdate DESC ";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    while ($dates = mysql_fetch_array($result))
    {
    echo "<tr><td class='shade1' width=350>";
    if ($dates['startdate'] < $now) echo "<span style='color: #777777;'>";
    elseif ($dates['approved']==1) echo "<span style='background: green; color: white;'>";
    else echo "<span>";
    echo date('l jS F Y', $dates['startdate']);
    if ($dates['length']=='am') echo " Morning only";
    if ($dates['length']=='pm') echo " Afternoon only";
    echo " - ".holiday_approval_status($dates['approved']);
    echo "</span>";
    echo "</td></tr>\n";
    }
    echo "<tr><td align='left' class='shade2'><b>Other Leave</b>:</td></tr>";
    $sql = "SELECT * FROM holidays WHERE userid='{$sit[2]}' AND type=5 ORDER BY startdate DESC ";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    while ($dates = mysql_fetch_array($result))
    {
    echo "<tr><td class='shade1' width=350>";
    if ($dates['startdate'] < $now) echo "<span style='color: #777777;'>";
    elseif ($dates['approved']==1) echo "<span style='background: green; color: white;'>";
    else echo "<span>";
    echo date('l jS F Y', $dates['startdate']);
    if ($dates['length']=='am') echo " Morning only";
    if ($dates['length']=='pm') echo " Afternoon only";
    echo " - ".holiday_approval_status($dates['approved']);
    echo "</span>";
    echo "</td></tr>\n";
    }

    ?>
    </table>
    <?php
    include('htmlfooter.inc.php');
}
?>