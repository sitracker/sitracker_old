<?php
// holiday_calendar.php
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

// External variables
$user = cleanvar($_REQUEST['user']);
$nmonth = cleanvar($_REQUEST['nmonth']);
$nyear = cleanvar($_REQUEST['nyear']);
$type = cleanvar($_REQUEST['type']);
$selectedday = cleanvar($_REQUEST['selectedday']);
$selectedmonth = cleanvar($_REQUEST['selectedmonth']);
$selectedyear = cleanvar($_REQUEST['selectedyear']);
$length = cleanvar($_REQUEST['length']);
if (empty($length)) $length='day';

include('htmlheader.inc.php');

if ($user=='current' || $user==0) $user=$sit[2];
if (user_permission($sit[2],50)) $approver=TRUE;

function draw_calendar($nmonth, $nyear)
{
    global $type, $user, $selectedday, $selectedmonth, $selectedyear;

    // Get the current date/time for the users timezone
    $timebase=gmmktime()+($timezone*3600);

    if (!$nday)
    {
    $nday = date('d',$timebase);
    }
    if (!$nmonth)
    {
    $nmonth = date('m',$timebase);
    }
    if (!$nyear) {
    $nyear = date('Y',$timebase);
    }

    # get the first day of the week!
    $firstday = date('w',mktime(0,0,0,$nmonth,1,$nyear));

    # have to perform a loop to test from 31 backwards using this
    # to see which is the last day of the month
    $lastday = 31;
    do
    {
    # This should probably be recursed, but it works as it is
    $monthOrig = date('m',mktime(0,0,0,$nmonth,1,$nyear));
    $monthTest = date('m',mktime(0,0,0,$nmonth,$lastday,$nyear));
    if ($monthTest != $monthOrig) { $lastday -= 1; }
    }
    while ($monthTest != $monthOrig);
    $monthName = date('F',mktime(0,0,0,$nmonth,1,$nyear));

    if ($CONFIG['debug'])
    {
        echo "<p>first day of the first week of $nmonth $nyear is $firstday (from 0 to 6) <p>\n";
        echo "The last day of $nmonth $nyear is $lastday\n<p>";
    }
    $days[0] = 'Sun';
    $days[1] = 'Mon';
    $days[2] = 'Tue';
    $days[3] = 'Wed';
    $days[4] = 'Thu';
    $days[5] = 'Fri';
    $days[6] = 'Sat';

    $dayRow = 0;
    echo "<table>";

    /* Make navigation control for months */

    if ($nmonth>=1)
    {
        $prevmonth=$nmonth-1;
        $prevyear=$nyear;
        $nextmonth=$nmonth+1;
    }
    if ($nmonth==1)
    {
        $prevmonth=12;
        $prevyear=$nyear-1;
    }
    if ($nmonth<12)
    {
        // $nextmonth=nmonth+1;
        $nextyear=$nyear;
    }
    if ($nmonth==12)
    {
        $nextmonth=1;
        $nextyear=$nyear+1;
    }
    echo "<tr><td colspan='7' valign='center' class='calendartitle' align='center'>";
    //       echo "<small><a href=\"blank.php?nmonth=".date('m',$timebase)."&nyear=".date('Y',$timebase)."&nday=".date('d',$timebase)."&sid=$sid\" title=\"jump to today\">".date('D jS M Y')."</a></small><br /> ";
    //       echo "<a href=\"blank.php?nmonth=$prevmonth&nyear=$prevyear&sid=$sid\" title=\"Previous Month\"><img src=\"images/arrow_left.gif\" height=\"9\" width=\"6\" border=\"0\"></a>";
    /* Print Current Month */
    echo "&nbsp;<b>$monthName $nyear</b>";
    echo "&nbsp;";
    echo "<a href=\"blank.php?nmonth=$nextmonth&amp;nyear=$nextyear&amp;sid=$sid\" title=\"Next Month\"><img src=\"images/arrow_right.gif\" height=\"9\" width=\"6\" border=\"0\" /></a>";
    echo "</td></tr>";
    echo "<tr>\n";
    for($i=0; $i<=6; $i++)
    {
        echo"<td width=\"10%\" ";
        if ($i==0 || $i==6)
        { echo "class=\"shade1\""; }
        else
        { echo "class=\"shade2\""; }
        echo ">$days[$i]</td>\n";
    }
    echo "</tr>\n";

    echo "<tr>\n";
    while($dayRow < $firstday)
    {
        echo "<td><!-- This day in last month --></td>";
        $dayRow += 1;
    }
    $day = 0;
    if($frametarget)
    {
        $targetString = 'target = '.$frametarget;
    }
    else
    {
        $targetString = '';
    }
    while($day < $lastday)
    {
        if(($dayRow % 7) == 0) echo "</tr>\n<tr>\n";
        $adjusted_day = $day+1;
        $bold="";
        $notbold="";
        // Colour Today in Red
        if ($adjusted_day==date('d') && $nmonth==date('m'))
        {
            $bold="<span style=\"color: red\"><B>";
            $notbold="</span></B>";
        }
        if (strlen($adjusted_day)==1)  // adjust for days with only one digit
        {
            $calday="0$adjusted_day";
        }
        else
        {
            $calday=$adjusted_day;
        }
        if (strlen($nmonth)==1)  // adjust for months with only one digit
        {
            $nmonth="0$nmonth";
        }
        else
        {
            $nmonth=$nmonth;
        }

        $rowcount=0;
        if ($rowcount>0)
        {
            $calnicedate=date( "l jS F Y", mktime(0,0,0,$nmonth,$calday,$nyear) );
            echo "<td id=\"id$calday\" class=\"calendar\"><a href=\"daymessages.php?month=$nmonth&amp;day=$calday&amp;year=$nyear&amp;sid=$sid\" title=\"$rowcount messages\"
            $targetString target=\"mainscreen\" onMouseOver=\"window.over('id$calday')\" onMouseOut=\"window.out('id$calday')\">$bold$adjusted_day$notbold</a></td>";
        }
        else
        {
            if ($dayRow % 7 == 0 || $dayRow % 7 == 6)
            {
                echo "<td class=\"shade1\">";
                echo "$adjusted_day</td>";
            }
            else
            {
                /////////////////////////////////
                // colors and shading
                $halfday="";
                $style='';
                // check holiday data
                list($dtype, $dlength, $approved, $approvedby)=user_holiday($user, $type, $nyear, $nmonth, $calday, false);

                if ($dlength=='pm')
                {
                    $halfday = "style=\"background-image: url(images/halfday-pm.gif)\" ";
                    $style="background-image: url(images/halfday-pm.gif); ";
                }
                if ($dlength=='am')
                {
                    $halfday = "style=\"background-image: url(images/halfday-am.gif)\" ";
                    $style="background-image: url(images/halfday-am.gif); ";
                }
                if ($calday==$selectedday && $selectedmonth==$nmonth && $selectedyear==$nyear)
                {
                    // consider a border color to indicate the selected cell
                    $style.="border: 1px red solid; ";
                    // $shade="critical";
                }


                // idle = green
                // critical = red
                // urgent = pink
                // expired = grey
                // mainshade = white
                switch ($dtype)
                {
                    case 1:
                        $shade= "mainshade";
                        if ($approved==1) { $shade='idle';  }
                        if ($approved==2) $shade='urgent';
                    break;

                    case 2:
                        $shade= "mainshade";
                        if ($approved==1) { $shade='idle';  }
                        if ($approved==2) $shade='urgent';
                    break;

                    case 3:
                        $shade= "mainshade";
                        if ($approved==1) { $shade='idle';  }
                        if ($approved==2) $shade='urgent';
                    break;

                    case 4:
                        $shade= "mainshade";
                        if ($approved==1) { $shade='idle';  }
                        if ($approved==2) $shade='urgent';
                    break;

                    case 5:
                        $shade= "mainshade";
                        if ($approved==1) { $shade='idle'; $style="border: 1px dotted magenta; "; }
                        if ($approved==2) $shade='urgent';
                    break;

                    case 10: // bank holidays
                        $style="border: 1px dotted blue; ";
                        // $style="background-image: url(images/halfday-am.gif";
                        $shade='shade1';
                    break;

                    default:
                        $shade="shade2";
                    break;
                }
                if ($dtype==1 || $dtype=='' || $dtype==5 || $dtype==3 || $dtype==2 || $dtype==4)
                {
                    echo "<td class=\"$shade\" style=\"$style\">";
                    echo "<a href=\"add_holiday.php?type=$type&amp;user=$user&amp;year=$nyear&amp;month=$nmonth&amp;day=$calday\"  title=\"$celltitle\">$bold$adjusted_day$notbold</a></td>";
                }
                else
                {
                    echo "<td class=\"$shade\" style=\"$style\">$bold$adjusted_day$notbold</td>";
                }
            }
        }
        $day += 1;
        $dayRow += 1;
    }
    echo "\n</tr>\n</table>\n";
    #  echo "$nmonth";
}

echo "<h2>";
switch ($type)
{
    case 1:
        if ($user=='all' && $approver==TRUE) echo "Everybody";
        else echo user_realname($user);
        echo "'s Holiday Calendar</h2>";
        echo "<p align='center'>You have used ".user_count_holidays($user, $type)." of your ".user_holiday_entitlement($user)." days entitlement.<br />";
    break;

    case 2:
        if ($user=='all' && $approver==TRUE) echo "Everybody";
        else echo user_realname($user);
        echo "'s Sickness Calendar</h2>";
    break;

    case 3:
        if ($user=='all' && $approver==TRUE) echo "Everybody";
        else echo user_realname($user);
        echo "'s Working Away Calendar</h2>";
    break;

    case 4:
        if ($user=='all' && $approver==TRUE) echo "Everybody";
        else echo user_realname($user);
        echo "'s Training Calendar</h2>";
    break;

    case 10:
        echo "Set Bank Holidays</h2>";
    break;

    default:
        trigger_error("Error: Holiday type '$type' not handled ", E_USER_ERROR);
}
?>
<table align='center'>
<tr>
<td align='center'>
<?php
if (isset($selectedday))
{
echo "$selectedday/$selectedmonth/$selectedyear is ";
switch ($length)
{
    case 'am':
    echo "booked for the <b>morning";
    break;

    case 'pm':
    echo "booked for the <b>afternoon";
    break;

    case 'day':
    echo "booked for the <b>full day";
    break;

    default:
    echo "<b>not booked";
}
echo "</b> ";
echo " as ".holiday_type($selectedtype).".  ";

if ($approved==0)
{
    switch ($length)
    {
        case 'am':
            echo "You can make it <a href='add_holiday.php?type=$type&amp;user=$user&amp;year=$selectedyear&amp;month=$selectedmonth&amp;day=$selectedday&amp;length=pm'>the afternoon instead</a>, or book <a href='add_holiday.php?type=$type&amp;user=$user&amp;year=$selectedyear&amp;month=$selectedmonth&amp;day=$selectedday&amp;length=day'>full day</a>. ";
        break;

        case 'pm':
            echo "You can make it <a href='add_holiday.php?type=$type&amp;user=$user&amp;year=$selectedyear&amp;month=$selectedmonth&amp;day=$selectedday&amp;length=am'>the morning</a> instead, or book <a href='add_holiday.php?type=$type&amp;user=$user&amp;year=$selectedyear&amp;month=$selectedmonth&amp;day=$selectedday&amp;length=day'>full day</a>. ";
        break;

        case 'day':
            echo "You can make it <a href='add_holiday.php?type=$type&amp;user=$user&amp;year=$selectedyear&amp;month=$selectedmonth&amp;day=$selectedday&amp;length=am'>the morning</a>, or <a href='add_holiday.php?type=$type&amp;user=$user&amp;year=$selectedyear&amp;month=$selectedmonth&amp;day=$selectedday&amp;length=pm'>the afternoon</a> instead. ";
    }
    if ($length!='0') echo "Or you can <a href='add_holiday.php?type=$type&amp;user=$user&amp;year=$selectedyear&amp;month=$selectedmonth&amp;day=$selectedday&amp;length=0'>unbook</a> it.";
}
elseif ($approved==1)
{
    list($xtype, $xlength, $xapproved, $xapprovedby)=user_holiday($user, $type, $selectedyear, $selectedmonth, $selectedday, FALSE);
    echo "The Holiday has been Approved by ".user_realname($xapprovedby).".";
    if ($length!='0' && $approver==TRUE && $sit[2]==$xapprovedby) echo "&nbsp;As approver for this holiday you can <a href='add_holiday.php?type=$type&amp;user=$user&amp;year=$selectedyear&amp;month=$selectedmonth&amp;day=$selectedday&amp;length=0'>unbook</a> it.";
}
else
{
    echo "<span class='error'>The Holiday has been Declined</span>.  You should <a href='add_holiday.php?type=$type&amp;user=$user&amp;year=$selectedyear&amp;month=$selectedmonth&amp;day=$selectedday&amp;length=0'>unbook</a> it.";
}
}
else
{
echo "Click on a day to book it";
}

?>
</p>
</td>
</tr>
</table>

<?php
echo "<table align='center' summary=\"calendar\" width='100%'>";
if (date('m')<=7) { $displayyear=date('Y')-1; $displaymonth=8; }
if (date('m')>7) { $displayyear=date('Y'); $displaymonth=8; }
if (date('m')>11) { $displayyear=date('Y'); $displaymonth=12; }
for ($r==1;$r<5;$r++)
{
echo "<tr>";
for ($c=1;$c<=4;$c++)
{
    echo "<td valign=top align='center' class='shade1'>";
    draw_calendar($displaymonth,$displayyear);
    echo "</td>";
    if ($displaymonth==12) { $displayyear++; $displaymonth=0; }
    $displaymonth++;
}
echo "</tr>";
}
echo "</table>";

include('htmlfooter.inc.php');
?>
