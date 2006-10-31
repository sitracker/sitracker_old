<?php
// book_holidays.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// This Page Is Valid XHTML 1.0 Transitional! 13Sep06
// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
$permission=27; // view your calendar

require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

// External variables
$step = $_REQUEST['step'];

if (empty($step))
{
    include('htmlheader.inc.php');
    // The JavaScript date picker used on this page came from an article at
    // http://www.dagblastit.com/~tmcclure/dhtml/calendar.html
    // The website states
    // "You may use the strategies and code in these articles license and royalty free unless otherwise directed.
    // "If I helped you build something cool I'd like to hear about it. Drop me a line at tom@dagblastit.com."
    ?>
    <h2>Book Holidays</h2>

    <form name="date" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <table class='vertical'>
    <tr><th>Holiday Type:</th><td class='shade2'><?php holidaytype_drop_down('type', 1) ?></td></tr>
    <tr><th>Start Date:</th><td align='left' class='shade1' title='date picker'>
    <input name='start' size="10" />
    <?php
    echo date_picker('date.start');
    ?>
    <tr><th>End Date:</th><td align='left' class='shade1' title='date picker'>
    <input name='end' size="10" />
    <?php
    echo date_picker('date.end');
    // <img onmouseup="toggleDatePicker('enddate','date.end')" id='enddatePos' width='16' height='16' src="images/icons/kdeclassic/16x16/actions/1day.png" align='top' border='0' alt="date picker" /><div id="enddate" style="position:absolute;"></div></td></tr>
    ?>

    </table>
    <p align='center'>
    <input type='hidden' name='step' value='1' />
    <input type='submit' value='Book' /></p>
    </form>

    <?php
    include('htmlfooter.inc.php');
}
elseif ($step=='1')
{
    // External variables
    $start = cleanvar($_REQUEST['start']);
    $end = cleanvar($_REQUEST['end']);
    $type = cleanvar($_REQUEST['type']);

    include('htmlheader.inc.php');
    $start=strtotime($start);
    $end=strtotime($end);

    echo "<h2>Book ".holiday_type($type)."</h2>";
    if ($type=='2') echo "<p align='center'>Sickness, can of course only be booked for days that have passed.</p>";

    if ($type=='1')
    {
        $entitlement=user_holiday_entitlement($sit[2]);
        $holidaystaken=user_count_holidays($sit[2], 1);
        if (($entitlement-$holidaystaken) <= 0 )
        echo "<p class='error'>You have used up all your holiday entitlement for this year</p>";
    }

    // swap dates around if end is before start
    if ($start > $end)
    {
        $newend = $start;
        $start = $end;
        $end = $newend;
        unset($newend);
    }

    echo "<form name='date' action='{$_SERVER['PHP_SELF']}' method='post'>";

    echo "<p align='center'>Send the request(s) to: ";
    // extract approvers
    $sql  = "SELECT id, realname, accepting FROM users, userpermissions ";
    $sql .= "WHERE users.id=userpermissions.userid AND permissionid=50 AND granted=TRUE AND users.status !=0 ORDER BY realname ASC";
    $result = mysql_query($sql);

    $id=0;
    echo "<select class='dropdown' name='approvaluser'>";
    if ($id == 0)
        echo "<option selected value='0'>Select A User\n";
    while ($users = mysql_fetch_array($result))
    {
        if($users['id'] != $sit[2])
        {
            echo "<option ";
            if ($users['id'] == $id) echo "selected='selected' ";
            echo "value='{$users['id']}'>";
            echo $users['realname']."</option>\n";
        }
        echo "\n";
    }
    echo "</select> <em>(Your Manager)</em>";
    echo "<br /><br />Send comments with your request: (or leave blank)<br />";
    echo "<textarea name='memo' rows='3' cols='40'></textarea>";
    echo "<input type='hidden' name='user' value='$user' />";
    echo "<input type='hidden' name='type' value='$type' />";
    echo "</p>\n";

    echo "<p align='center'><strong>Select Days</strong></p>";

    echo "<table align='center' border='0' cellpadding='2' cellspacing='0' width='550'>";
    echo "<tr><td align='right' class='shade1' width='200'><strong>Start Date</strong>:</td><td class='shade2' colspan='4'>".date('D d M Y',$start)."</td></tr>";
    echo "<tr><td align='right' class='shade1' width='200'><strong>End Date</strong>:</td><td class='shade2' colspan='4'>".date('D d M Y',$end)."</td></tr>";
    echo "<tr><td class='shade2' colspan='2'>&nbsp;</td><td class='shade1' align='center'><strong>Day</strong></td><td class='shade2' align='center'><strong>AM</strong></td><td class='shade1'  align='center'><strong>PM</strong></td></tr>";

    $daynumber=1;
    // if ($end==$start)
    $end+=86400;  // ensure we still loop for single day bookings by setting end to next day
    for($day=$start;$day < $end; $day=$day+86400)
    {
        if (date('D',$day)!='Sat' && date('D',$day)!='Sun')
        {
            $sql = "SELECT * FROM holidays WHERE startdate = '$day' AND userid='{$sit[2]}'";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

            // need to do something different when there are more than one row
            if (mysql_num_rows($result) > 0)
            {
                while ($existing_holiday = mysql_fetch_array($result))
                {
                    $holiday_type=holiday_type($existing_holiday['type']);
                    $holiday_legend=strtoupper(substr($holiday_type,0,1));
                    echo "<tr><td align='right'>&nbsp;</td>";
                    echo "<td class='shade2' align='right'> ".date('D d M Y',$day)." </td>";
                    echo "<td class='shade1' align='center'>";
                    if ($existing_holiday['length']=='day') echo "$holiday_legend";
                    echo "</td>";

                    // am
                    echo "<td class='shade2' align='center'>";
                    if ($existing_holiday['length']=='am' ) echo "$holiday_legend";
                    elseif ($existing_holiday['length']!='day')
                    {
                        if (($type=='2' && $day < $now) || ($type!='2'))
                            echo "<input type='radio' name='length{$daynumber}' value='am' checked='checked' />";
                        else echo "-";
                    }
                    else echo "-";
                    echo "</td>";

                    // pm
                    echo "<td class='shade1' align='center'>";
                    if ($existing_holiday['length']=='pm') echo "$holiday_legend";
                    elseif ($existing_holiday['length']!='day')
                    {
                        if (($type=='2' && $day < $now) || ($type!='2'))
                        echo "<input type='radio' name='length{$daynumber}' value='pm' checked='checked' />";
                        else echo "-";
                    }
                    else echo "-";
                    echo "</td>";
                    echo "</tr>";
                }
            }
            else
            {
                $sql = "SELECT * FROM holidays WHERE startdate = '$day' AND type='10' ";
                $result = mysql_query($sql);
                if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
                if (mysql_num_rows($result) > 0)
                {
                    echo "<tr><td align='right' class='shade1' width='200'><strong>Bank Holiday</strong>:</td><td class='shade2'>".date('D d M Y',$day)."</td></tr>";
                }
                else
                {
                    echo "<tr><td align='right' width='200'>&nbsp;</td><td class='shade2' align='right'>".date('D d M Y',$day)." </td>";
                    if (($type=='2' && $day < $now) || ($type!=2))
                        echo "<td class='shade1' align='center'><input type='radio' name='length{$daynumber}' value='day' checked='checked'/></td><td class='shade2' align='center'><input type='radio' name='length{$daynumber}' value='am' /></td><td class='shade1' align='center'><input type='radio' name='length{$daynumber}' value='pm' /></td>";
                    else
                        echo "<td class='shade1' align='center'>-</td><td class='shade2' align='center'>-</td><td class='shade1' align='center'>-</td>";
                    echo "</tr>\n";
                }
            }
            echo "<input type='hidden' name='day{$daynumber}' value='$day' />";
            $daynumber++;
        }
    }
    echo "</table>";
    echo "<input type='hidden' name='numberofdays' value='$daynumber' />";
    echo "<input type='hidden' name='step' value='3' />";

    echo "<p align='center'>";
    echo "<input type='submit' value='Book' />";
    echo "</p>";
    echo "</form>";


    echo "<br />";

    echo "<p align='center'><a href='book_holidays.php'>Abandon this booking and try again</a></p>";
    include('htmlfooter.inc.php');
}
else
{
    $approvaluser = cleanvar($_REQUEST['approvaluser']);
    $memo = cleanvar($_REQUEST['memo']);
    $type = cleanvar($_REQUEST['type']);
    $numberofdays = cleanvar($_REQUEST['numberofdays']);
    for ($h=1;$h < $numberofdays;$h++)
    {
        $dayfield="day{$h}";
        $lengthfield="length{$h}";
        $$dayfield = cleanvar($_REQUEST[$dayfield]);
        $$lengthfield = cleanvar($_REQUEST[$lengthfield]);
    }

    // check that approval user is set
    // FIXME: don't die, do nice msg
    if ($approvaluser < 1) die('Please hit back and select a user to send the holiday request to.');

    include('htmlheader.inc.php');
    //
    // SAVE REQUEST TO DATABASE
    //
    echo "<h2>Holiday Booking</h2>";
    echo "<p align='center'>You have requested a holiday booking as shown below</p>";
    echo "<table class='vertical' align='center'>";
    echo "<tr>";
    echo "<th>Date</th><th>Length</th><th>Type</th>";
    echo "</tr>\n";
    for ($holiday=1;$holiday < $numberofdays;$holiday++)
    {
        $len="length{$holiday}";
        $d="day{$holiday}";
        if (empty($$len)) $$len='day';
        echo "<tr class='shade2'>";
        echo "<td>" . date('D d M Y', $$d) . "</td>";
        echo "<td>{$$len}</td>";
        echo "<td>".holiday_type($type)."</td>";
        echo "</tr>\n";

        // check to see if there is other holiday booked on this day
        // and modify that where required.

        $sql = "INSERT INTO holidays (userid, type, startdate, length, approved, approvedby) ";
        $sql .= "VALUES ('{$sit[2]}', '$type', '{$$d}', '{$$len}', '0', '$approvaluser') ";
        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    }
    echo "</table>";
    echo "<p align='center'>You should check your holiday page in a few days to see the approval status.</p>";
    echo "<p align='center'><a href=\"holidays.php\">Return to Holidays Page</a>";
    include('htmlfooter.inc.php');
}
?>