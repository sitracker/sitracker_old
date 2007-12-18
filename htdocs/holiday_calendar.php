<?php
// holiday_calendar.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
//FIXME i18n
@include ('set_include_path.inc.php');
$permission=27; // View your calendar
require ('db_connect.inc.php');
require ('functions.inc.php');


// This page requires authentication
require ('auth.inc.php');

// External variables
$user = cleanvar($_REQUEST['user']);
$nmonth = cleanvar($_REQUEST['nmonth']);
$nyear = cleanvar($_REQUEST['nyear']);
$type = cleanvar($_REQUEST['type']);
$selectedday = cleanvar($_REQUEST['selectedday']);
$selectedmonth = cleanvar($_REQUEST['selectedmonth']);
$selectedyear = cleanvar($_REQUEST['selectedyear']);
$selectedtype = cleanvar($_REQUEST['selectedtype']);
$approved = cleanvar($_REQUEST['approved']);
$length = cleanvar($_REQUEST['length']);
if (empty($length)) $length='day';
$display = cleanvar($_REQUEST['display']);

$title = $strHolidayPlanner;
include ('htmlheader.inc.php');

if (empty($user) || $user=='current') $user=$sit[2];
elseif ($user=='all') $user='';
if (empty($type)) $type=1;
if (user_permission($sit[2],50)) $approver=TRUE; else $approver=FALSE;

/**
    * @author Ivan Lucas
*/
function draw_calendar($nmonth, $nyear)
{
    global $type, $user, $selectedday, $selectedmonth, $selectedyear;

    // Get the current date/time for the users timezone
    $timebase=gmmktime()+($timezone*3600);

    if (!$nday) $nday = date('d',$timebase);
    if (!$nmonth) $nmonth = date('m',$timebase);
    if (!$nyear) $nyear = date('Y',$timebase);

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
    $days[0] = $strSun;
    $days[1] = $strMon;
    $days[2] = $strTue;
    $days[3] = $strWed;
    $days[4] = $strThu;
    $days[5] = $strFri;
    $days[6] = $strSat;

    $dayRow = 0;
    echo "\n<table summary='{$monthName} {$nyear}'>";

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
    echo "<tr><th colspan='7'>";
    //       echo "<small><a href=\"blank.php?nmonth=".date('m',$timebase)."&nyear=".date('Y',$timebase)."&nday=".date('d',$timebase)."&sid=$sid\" title=\"jump to today\">".date('D jS M Y')."</a></small><br /> ";
    //       echo "<a href=\"blank.php?nmonth=$prevmonth&nyear=$prevyear&sid=$sid\" title=\"Previous Month\"><img src=\"images/arrow_left.gif\" height=\"9\" width=\"6\" border=\"0\"></a>&nbsp;";
    /* Print Current Month */
    echo "<a href='{$_SERVER['PHP_SELF']}?display=month&amp;year={$nyear}&amp;month=$nmonth'>{$monthName} {$nyear}</a>";
    //    echo "&nbsp;<a href=\"blank.php?nmonth=$nextmonth&amp;nyear=$nextyear&amp;sid=$sid\" title=\"Next Month\"><img src=\"images/arrow_right.gif\" height=\"9\" width=\"6\" border=\"0\" /></a>";
    echo "</th></tr>\n";
    echo "<tr>\n";
    for($i=0; $i<=6; $i++)
    {
        echo"<td ";
        if ($i==0 || $i==6) echo "class='expired'"; // Weekend
        else echo "class='shade1'";
        echo ">{$days[$i]}</td>";
    }
    echo "</tr>\n";

    echo "<tr>\n";
    while ($dayRow < $firstday)
    {
        echo "<td><!-- This day in last month --></td>";
        $dayRow += 1;
    }
    $day = 0;
    while ($day < $lastday)
    {
        if (($dayRow % 7) == 0 AND $dayRow >0) echo "</tr>\n<tr>\n";
        $adjusted_day = $day+1;
        $bold="";
        $notbold="";
        // Colour Today in Red
        if ($adjusted_day==date('d') && $nmonth==date('m') && $nyear==date('Y'))
        {
            $bold="<span style='color: red'>";
            $notbold="</span>";
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
            echo "<td id=\"id$calday\" class=\"calendar\"><a href=\"daymessages.php?month=$nmonth&amp;day=$calday&amp;year=$nyear&amp;sid=$sid\" title=\"$rowcount messages\">{$bold}{$adjusted_day}{$notbold}</a></td>";
        }
        else
        {
            if ($dayRow % 7 == 0 || $dayRow % 7 == 6)
            {
                echo "<td class='expired'>";
                echo "$adjusted_day</td>";
            }
            else
            {
                /////////////////////////////////
                // colors and shading
                $halfday="";
                $style='';

                // Get the holiday information for a single day
                list($dtype, $dlength, $approved, $approvedby)=user_holiday($user, $type, $nyear, $nmonth, $calday, false);

                if ($dlength=='pm')
                {
                    $halfday = "style=\"background-image: url(images/halfday-pm.gif); background-repeat: no-repeat;\" ";
                    $style="background-image: url(images/halfday-pm.gif); background-repeat: no-repeat; ";
                }
                if ($dlength=='am')
                {
                    $halfday = "style=\"background-image: url(images/halfday-am.gif); background-position: bottom right; background-repeat: no-repeat;\" ";
                    $style="background-image: url(images/halfday-am.gif); background-position: bottom right; background-repeat: no-repeat;";
                }
                if ($calday==$selectedday && $selectedmonth==$nmonth && $selectedyear==$nyear)
                {
                    // consider a border color to indicate the selected cell
                    $style.="border: 1px red dashed; ";
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

                    case 10: // public holidays
                        $style="background: #D6D6D6;";
                        $shade='shade1';
                    break;

                    default:
                        $shade="shade2";
                    break;
                }
                if ($dtype==1 || $dtype=='' || $dtype==5 || $dtype==3 || $dtype==2 || $dtype==4)
                {
                    echo "<td class=\"$shade\" style=\"width: 15px; $style\">";
                    echo "<a href=\"add_holiday.php?type=$type&amp;user=$user&amp;year=$nyear&amp;month=$nmonth&amp;day=$calday\"  title=\"$celltitle\">$bold$adjusted_day$notbold</a></td>";
                }
                else
                {
                    echo "<td class=\"$shade\" style=\"width:15px; $style\">{$bold}{$adjusted_day}{$notbold}</td>";
                }
            }
        }
        $day += 1;
        $dayRow += 1;
    }
    echo "\n</tr>\n</table>\n";
}


/**
    * @author Ivan Lucas
*/
function appointment_popup($mode, $year, $month, $day, $time, $group, $user)
{
    global $sit, $approver;
    $html = '';
    if ($user==$sit[2] OR $approver==TRUE)
    {
        // Note: this first div is closed inline
        $html .= "<div class='appointment' onclick=\"appointment('app{$user}{$year}{$month}{$day}{$time}');\">";
        $html .= "<div id='app{$user}{$year}{$month}{$day}{$time}' class='appointmentdata'>";
        $html .= "<h2><a href=\"javascript:void();\">[X]</a> {$year}-{$month}-{$day} {$time}</h2>";
        if ($mode=='book') $html .= "<a href='add_holiday.php?type=1&amp;user={$user}&amp;year={$year}&amp;month={$month}&amp;day={$day}&amp;length={$time}'>{$GLOBALS['strBookHoliday']}</a><br />";
//         else $html .= "<a href=''>Cancel Holiday</a><br />";
//          TODO: Add the ability to cancel holiday from the holiday planner
        $html .= "</div>";
    }
    return $html;
}


/**
    * Holiday planner chart
    * @author Ivan Lucas
    * @param $mode string. modes: 'month', 'week', 'day'
*/
function draw_chart($mode, $year, $month='', $day='', $groupid='', $userid='')
{
    global $plugin_calendar, $sit, $dbUsers;
    if (empty($day)) $day = date('d');

    if ($mode=='month')
    {
        $day=1;
        $daysinmonth=date('t',mktime(0,0,0,$month,$day,$year));
        $lastday=$daysinmonth;
        $daywidth=1;
    }
    elseif ($mode=='week')
    {
        $daysinmonth=7;
        $lastday=($day+$daysinmonth)-1;
        $daywidth=3;
    }
    elseif ($mode=='day')
    {
        $daysinmonth=1;
        $lastday=$day;
        $daywidth=25;
    }
    else
    {
        $daysinmonth=date('t',mktime(0,0,0,$month,$day,$year));
        $lastday=$daysinmonth;
        $daywidth=1;
    }

    $startdate = mktime(0,0,0,$month,$day,$year);
    $enddate  = mktime(23,59,59,$month,$lastday,$year);

    // Get list of user groups
    $gsql = "SELECT * FROM groups ORDER BY name";
    $gresult = mysql_query($gsql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    $grouparr[0]='None';
    while ($group = mysql_fetch_object($gresult))
    {
        $grouparr[$group->id]=$group->name;
    }
    $numgroups = count($grouparr);

    // Get list of holiday types
    $holidaytype[1] = $GLOBALS['strHoliday'];
    $holidaytype[2] = $GLOBALS['strAbsentSick'];
    $holidaytype[3] = $GLOBALS['strWorkingAway'];
    $holidaytype[4] = $GLOBALS['strTraining'];
    $holidaytype[5] = $GLOBALS['strCompassionateLeave'];

    $html .= "<table align='center' border='1' cellpadding='0' cellspacing='0' style='border-collapse:collapse; border-color: #AAA; width: 99%;'>";
    $usql  = "SELECT * FROM `{$dbUsers}` WHERE status!=0 ";
    if ($numgroups > 1) $usql .= "AND groupid > 0 ";  // there is always 1 group (ie. 'none')
    if (!empty($user)) $usql .= "AND id={$user} ";
    $usql .= "ORDER BY groupid, realname";  // status=0 means left company
    $uresult = mysql_query($usql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

    $numusers = mysql_num_rows($uresult);
    $prevgroupid='000';
    if ($numusers > 0)
    {
        while ($user = mysql_fetch_object($uresult))
        {
            unset($hdays);

            $hsql = "SELECT * FROM `{$dbHolidays}` WHERE userid={$user->id} AND startdate >= $startdate AND startdate <= $enddate ";
            $hsql .= "AND type != 10";
            $hresult = mysql_query($hsql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
            while ($holiday = mysql_fetch_object($hresult))
            {
                $cday = date('j',$holiday->startdate);
                $hdays[$cday] = $holiday->length;
                $htypes[$cday] = $holiday->type;
                $happroved[$cday] = $holiday->approved;
            }
            // Public holidays
            $phsql = "SELECT * FROM `{$dbHolidays}` WHERE type=10 AND startdate >= $startdate AND startdate <= $enddate ";
            $phresult = mysql_query($phsql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
            while ($pubhol = mysql_fetch_object($phresult))
            {
                $cday = date('j',$pubhol->startdate);
                $pubholdays[$cday] = $pubhol->length;
            }
            if ($prevgroupid != $user->groupid)
            {
                if ($user->groupid=='') $user->groupid=0;
                $html .= "<tr>";
                $html .= "<td align='left' colspan='2' class='shade2'>{$GLOBALS['strGroup']}: <strong>{$grouparr[$user->groupid]}</strong></td>";
                for($cday=$day;$cday<=$lastday;$cday++)
                {
                    $shade='shade1';
                    if (date('D',mktime(0,0,0,$month,$cday,$year))=='Sat')
                    {
                        $shade='expired';
                        $html .= "<td class='$shade' style='text-align: center; font-size: 80%; border-left: 1px solid black;'><strong title='Week Number' >wk<br />".substr(date('W',mktime(0,0,0,$month,$cday,$year))+1,0, 1)."".substr(date('W',mktime(0,0,0,$month,$cday,$year))+1,1, 1)."</strong></td>";
                    }
                    elseif (date('D',mktime(0,0,0,$month,$cday,$year))=='Sun') $html .= '';  // nothing
                    else
                    {
                        $html .= "<td align='center' class=\"$shade\"";
                        if (mktime(0,0,0,$month,$cday,$year)==mktime(0,0,0,date('m'),date('d'),date('Y'))) $html .= " style='background: #FFFF00;' title='Today'";
                        $html .= ">";
                        $html .= substr(date('l',mktime(0,0,0,$month,$cday,$year)),0,$daywidth)."<br />";
                        if ($mode=='day') $html .= date('dS F Y',mktime(0,0,0,$month,$cday,$year));
                        else $html .= "<a href='{$_SERVER['PHP_SELF']}?display=day&amp;year={$year}&amp;month={$month}&amp;day={$cday}'>".date('d',mktime(0,0,0,$month,$cday,$year))."</a>" ;
                        $html .= "</td>";
                    }
                }
                $html .= "</tr>\n";
            }
            $prevgroupid = $user->groupid;


            $html .= "<tr><th rowspan='2' style='width: 10%'>{$user->realname}</th>";
            // AM
            $html .= "<td style='width: 2%'>{$GLOBALS['strAM']}</td>";
            for($cday=$day;$cday<=$lastday;$cday++)
            {
                $shade='shade1';
                if ((date('D',mktime(0,0,0,$month,$cday,$year))=='Sat' OR date('D',mktime(0,0,0,$month,$cday,$year))=='Sun'))
                {
                    // Add  day on for a weekend
                    if ($weekend==FALSE) $displaydays+=1;
                    $weekend=TRUE;
                }
                if (date('D',mktime(0,0,0,$month,$cday,$year))=='Sat')
                {
                    $html .= "<td class='expired'>&nbsp;</td>";
                }
                elseif (date('D',mktime(0,0,0,$month,$cday,$year))=='Sun')
                {
                    // Do nothing on sundays
                }
                else
                {
                    $weekend=FALSE;
                    if ($hdays[$cday]=='am' OR $hdays[$cday]=='day')
                    {
                        if ($happroved[$cday] == 0
                            OR $happroved[$cday]==10
                            OR $happroved[$cday]==8
                            OR $happroved[$cday]==-2) $html .= "<td class='review'>";  // Waiting approval
                        elseif ($htypes[$cday] <= 4
                                AND ($happroved[$cday] == 1
                                OR $happroved[$cday]==11)) $html .= "<td class='idle'>"; // Approved
                        elseif ($htypes[$cday] <= 4
                                AND ($happroved[$cday] == 2
                                OR $happroved[$cday]==12)) $html .= "<td class='notice'>"; // Approved Free
                        elseif ($htypes[$cday] == 5
                                AND ($happroved[$cday] == 1
                                OR $happroved[$cday] == 2
                                OR $happroved[$cday]== 11
                                OR $happroved[$cday] == 12)) $html .= "<td class='notice'>"; // Approved Free
                        elseif ($happroved[$cday] == -1 OR $happroved[$cday]==9) $html .= "<td class='urgent'>"; // Denied
                        else $html .= "<td class='shade2'>";
                        if ($user->id == $sit[2]) $html .= appointment_popup('cancel', $year, $month, $cday, 'am', $group, $user->id);
                        $html .= "<span title='{$holidaytype[$htypes[$cday]]}'>".substr($holidaytype[$htypes[$cday]],0,$daywidth)."</span>";
                        // This plugin function takes an optional param with an associative array containing the day
                        $pluginparams = array('plugin_calendar' => $plugin_calendar,
                                              'year'=> $year,
                                              'month'=> $month,
                                              'day'=> $cday,
                                              'useremail' => $user->email);
                        $html .= plugin_do('holiday_chart_day_am',$pluginparams);
                        if ($user->id == $sit[2]) $html .= "</div>";
                        $html .= "</td>";
                    }
                    else
                    {
                        if ($pubholdays[$cday]=='am' OR $pubholdays[$cday]=='day') $html .= "<td class='expired'>PH</td>";
                        else
                        {
                            $html .= "<td class='shade2'>";
                            if ($user->id == $sit[2]) $html .= appointment_popup('book', $year, $month, $cday, 'am', $group, $user->id);
                            $html .= '&nbsp;';
                            // This plugin function takes an optional param with an associative array containing the day
                            $pluginparams = array('plugin_calendar' => $plugin_calendar,
                                              'year'=> $year,
                                              'month'=> $month,
                                              'day'=> $cday,
                                              'useremail' => $user->email);
                            $html .= plugin_do('holiday_chart_day_am',$pluginparams);
                            if ($user->id == $sit[2]) $html .= "</div>";
                            $html .= "</td>";
                        }
                    }
                }
            }
            $html .= "</tr>\n";
            // PM
            $html .= "<tr><td>{$GLOBALS['strPM']}</td>";
            for($cday=$day;$cday<=$lastday;$cday++)
            {
                $shade='shade1';
                if ((date('D',mktime(0,0,0,$month,$cday,$year))=='Sat' OR date('D',mktime(0,0,0,$month,$cday,$year))=='Sun'))
                {
                    // Add  day on for a weekend
                    if ($weekend==FALSE) $displaydays+=1;
                    $weekend=TRUE;
                }
                if (date('D',mktime(0,0,0,$month,$cday,$year))=='Sat')
                {
                    $html .= "<td class='expired'>&nbsp;</td>";
                }
                elseif (date('D',mktime(0,0,0,$month,$cday,$year))=='Sun')
                {
                    // Do nothing on sundays
                }
                else
                {
                    $weekend=FALSE;  $hello='';
                    if ($hdays[$cday]=='pm' OR $hdays[$cday]=='day')
                    {
                        if ($happroved[$cday] == 0
                            OR $happroved[$cday]==10
                            OR $happroved[$cday]==8
                            OR $happroved[$cday]==-2) $html .= "<td class='review'>";  // Waiting approval
                        elseif ($htypes[$cday] <= 4
                                AND ($happroved[$cday] == 1
                                OR $happroved[$cday]==11)) $html .= "<td class='idle'>"; // Approved
                        elseif ($htypes[$cday] <= 4
                                AND ($happroved[$cday] == 2
                                OR $happroved[$cday]==12)) $html .= "<td class='notice'>"; // Approved Free
                        elseif ($htypes[$cday] == 5
                                AND ($happroved[$cday] == 1
                                OR $happroved[$cday] == 2
                                OR $happroved[$cday]== 11
                                OR $happroved[$cday] == 12)) $html .= "<td class='notice'>"; // Approved Free
                        elseif ($happroved[$cday] == -1 OR $happroved[$cday]==9) $html .= "<td class='urgent'>"; // Denied
                        else $html .= "<td class='shade2'>";
                        if ($user->id == $sit[2]) $html .= appointment_popup('cancel', $year, $month, $cday, 'pm', $group, $user->id);
                        $html .= "<span title='{$holidaytype[$htypes[$cday]]}'>".substr($holidaytype[$htypes[$cday]],0,$daywidth)."</span>";
                        // This plugin function takes an optional param with an associative array containing the day
                        $pluginparams = array('plugin_calendar' => $plugin_calendar,
                                              'year'=> $year,
                                              'month'=> $month,
                                              'day'=> $cday,
                                              'useremail' => $user->email);
                        $html .= plugin_do('holiday_chart_day_pm',$pluginparams);
                        if ($user->id == $sit[2]) $html .= "</div>";
                        $html .= "</td>";
                    }
                    else
                    {
                        if ($pubholdays[$cday]=='pm' OR $pubholdays[$cday]=='day') $html .= "<td class='expired'>PH</td>";
                        else
                        {
                            $html .= "<td class='shade2'>";
                            if ($user->id == $sit[2]) $html .= appointment_popup('book', $year, $month, $cday, 'pm', $group, $user->id);
                            $html .= '&nbsp;';
                            // This plugin function takes an optional param with an associative array containing the day
                            $pluginparams = array('plugin_calendar' => $plugin_calendar,
                                              'year'=> $year,
                                              'month'=> $month,
                                              'day'=> $cday,
                                              'useremail' => $user->email);
                            $html .= plugin_do('holiday_chart_day_pm',$pluginparams);
                            if ($user->id == $sit[2])  $html .= "</div>";
                            $html .= "</td>";
                        }
                    }
                }
            }
            $html .= "</tr>\n";
            $html .= "<tr><td colspan='0'></td></tr>\n";
        }
    }
    else
    {
        if ($numgroups < 1) $html .= "<p class='info'>Nothing to display</p>";
        else $html .= "<p class='info'>Nothing to display, check user group membership.</p>";
    }
    $html .= "</table>\n\n";

    // Legend
    $html .= "<table align='center'><tr><td><strong>Legend</strong>:</td>";
    foreach ($holidaytype AS $htype)
    {
        $html .= "<td>".substr($htype,0,1)." = {$htype}</td>";
    }
    $html .= "<td>PH = {$GLOBALS['strPublicHoliday']}</td>";
    $html .= "</tr>";
    // FIXME holiday approval status
    $html .= "<tr><td></td><td class='urgent'>declined</td><td class='review'>not approved</td><td class='idle'>approved</td><td class='notice'>approved free</td></tr>";
    $html .= "</table>\n\n";

    return $html;
}

function month_select($month, $year)
{
    $cyear=$year;
    $cmonth = $month - 3;
    if ($cmonth < 1) { $cmonth +=12; $cyear--; }
    $html = "<p align='center'>";
    $pmonth=$cmonth-5;
    $pyear=$cyear-1;
    $nyear=$cyear+1;
    $html .= "<a href='{$SERVER['PHP_SELF']}?display=month&amp;month={$month}&amp;year={$pyear}' title='Back one year'>&lt;&lt;</a> ";
    for ($c=1;$c <= 12;$c++)
    {
        if (mktime(0,0,0,$cmonth,1,$cyear)==mktime(0,0,0,date('m'),1,date('Y'))) $html .= "<span style='background: #FF0;'>";
        if (mktime(0,0,0,$cmonth,1,$cyear)==mktime(0,0,0,$month,1,$year)) $html .= "<span style='font-size: 160%'>";
        $html .= "<a href='{$SERVER['PHP_SELF']}?display=month&amp;month=$cmonth&amp;year=$cyear'>".date('M y',mktime(0,0,0,$cmonth,1,$cyear))."</a>";
        if (mktime(0,0,0,$cmonth,1,$cyear)==mktime(0,0,0,$month,1,$year)) $html .= "</span>";
        if (mktime(0,0,0,$cmonth,1,$cyear)==mktime(0,0,0,date('m'),1,date('Y'))) $html .= "</span>";
        if ($c < 12) $html .= " <span style='color: #666;'>|</span> ";
        $cmonth++;
        if ($cmonth > 12) { $cmonth -= 12; $cyear++; }
    }
    $html .= " <a href='{$SERVER['PHP_SELF']}?month=display=month&amp;{$month}&amp;year={$nyear}' title='Forward one year'>&gt;&gt;</a>";
    $html .= "</p>";
    return $html;
}


// Defaults
if (empty($_REQUEST['year'])) $year=date('Y');
else $year=$_REQUEST['year'];
if (empty($_REQUEST['month'])) $month=date('m');
else $month=$_REQUEST['month'];
if (empty($_REQUEST['day'])) $day=date('d');
else $day=$_REQUEST['day'];



// Navigation
echo "<p>{$strDisplay}: ";
echo "<a href='{$_SERVER['PHP_SELF']}?display=list&amp;year={$year}&amp;month={$month}&amp;day={$day}&amp;type={$type}'>";
if ($display=='list') echo "<em>{$strList}</em>";
else echo "{$strList}";
echo "</a> |";
echo " <a href='{$_SERVER['PHP_SELF']}?display=year&amp;year={$year}&amp;month={$month}&amp;day={$day}&amp;type={$type}'>";
if ($display=='year') echo "<em>{$strYear}</em>";
else echo "{$strYear}";
echo "</a> |";
echo " <a href='{$_SERVER['PHP_SELF']}?display=month&amp;year={$year}&amp;month={$month}&amp;day={$day}&amp;type={$type}'>";
if ($display=='month') echo "<em>{$strMonth}</em>";
else echo "{$strMonth}";
echo "</a> |";
echo " <a href='{$_SERVER['PHP_SELF']}?display=week&amp;year={$year}&amp;month={$month}&amp;day={$day}&amp;type={$type}'>";
if ($display=='week') echo "<em>{$strWeek}</em>";
else echo "{$strWeek}";
echo "</a> |";
echo " <a href='{$_SERVER['PHP_SELF']}?display=day&amp;year={$year}&amp;month={$month}&amp;day={$day}&amp;type={$type}'>";
if ($display=='day') echo "<em>{$strDay}</em>";
else echo "{$strDay}";
echo "</a>";
echo "</p>";


if ($display=='chart' OR $display=='month')
{
    // Display planner chart
    echo "<h2>{$strMonthView}</h2>";

    $nextyear=$year;
    if ($month < 12) $nextmonth = $month +1;
    else { $nextmonth = 1; $nextyear = $year+1; }

    $prevyear=$year;
    if ($month > 1) $prevmonth = $month -1;
    else { $prevmonth = 12; $prevyear = $year-1; }

    $plugin_calendar = plugin_do('holiday_chart_cal');

    echo month_select($month, $year);
    echo "<p align='center'><a href='{$_SERVER['PHP_SELF']}?month={$prevmonth}&amp;year={$prevyear}' title='Previous Month'>&lt;</a> ".date('F Y',mktime(0,0,0,$month,1,$year))." <a href='{$_SERVER['PHP_SELF']}?month={$nextmonth}&amp;year={$nextyear}' title='Next Month'>&gt;</a></p>";

    echo draw_chart('month', $year, $month, $day, '', $user);
}
elseif ($display=='list')
{
    echo "<h2>{$strHolidayList}</h2>";

    // Get list of holiday types
    $holidaytype[1] = $GLOBALS['strHoliday'];
    $holidaytype[2] = $GLOBALS['strAbsentSick'];
    $holidaytype[3] = $GLOBALS['strWorkingAway'];
    $holidaytype[4] = $GLOBALS['strTraining'];
    $holidaytype[5] = $GLOBALS['strCompassionateLeave'];

    echo "<form action='{$_SERVER['PHP_SELF']}' style='text-align: center;'>";
    echo "List: <select class='dropdown' name='type' onchange='window.location.href=this.options[this.selectedIndex].value'>\n";
    foreach ($holidaytype AS $htypeid => $htype)
    {
        echo "<option value='{$_SERVER['PHP_SELF']}?display=list&amp;type={$htypeid}'";
        if ($type == $htypeid) echo " selected='selected'";
        echo ">{$htype}</option>\n";
    }
    echo "</select></form>";
    echo "<h3>Descending date order</h3>"; // FIXME i18n decending date
    if (empty($type)) $type=1;
    $sql = "SELECT *, h.id AS holidayid FROM `{$dbHolidays}` AS h, `{$dbUsers}` AS u WHERE ";
    $sql .= "h.userid = u.id AND h.type=$type ";
    if (!empty($user) AND $user!='all') $sql .= "AND u.id='{$user}' ";
    $sql .= "ORDER BY startdate DESC";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    if (mysql_num_rows($result))
    {
        echo "<table align='center'>";
        echo "<tr><th>{$GLOBALS['strType']}</th><th>{$GLOBALS['strUser']}</th><th>{$GLOBALS['strDate']}</th><th>{$GLOBALS['strStatus']}</th><th>{$GLOBALS['strOperation']}</th></tr>\n";
        $shade='shade1';
        while ($dates = mysql_fetch_array($result))
        {
            echo "<tr class='$shade'><td>".holiday_type($dates['type'])."</td>";
            echo "<td>{$dates['realname']}</td>";
            echo "<td>".date('l jS F Y', $dates['startdate']);
            if ($dates['length']=='am') echo " {$strMorning}";
            if ($dates['length']=='pm') echo " {$strAfternoon}";
            echo "</td>";
            echo "<td>";
            if (empty($dates['approvedby'])) echo " <em>not requested yet</em>";
            else echo "<strong>".holiday_approval_status($dates['approved'])."</strong>";
            if ($dates['approvedby'] > 0 AND $dates['approved'] >= 1) echo " by ".user_realname($dates['approvedby']);
            elseif ($dates['approvedby'] > 0 AND empty($dates['approved'])) echo " of ".user_realname($dates['approvedby']);
            echo "</td>";
            echo "<td>";
            if ($approver==TRUE) echo "<a href='add_holiday.php?hid={$dates['holidayid']}&amp;year=".date('Y',$dates['startdate'])."&amp;month=".date('m',$dates['startdate'])."&amp;day=".date('d',$dates['startdate'])."&amp;user={$dates['userid']}&amp;type={$dates['type']}&amp;length=0&amp;return=list' onclick=\"return window.confirm('{$dates['realname']}: ".date('l jS F Y', $dates['startdate']).": Are you sure you want to delete this?');\">Delete</a>";
            echo "</td></tr>\n";
            if ($shade=='shade1') $shade='shade2';
            else $shade='shade1';
        }
        echo "</table>";
        if ($approver) echo "<p align='center'><a href='{$_SERVER['PHP_SELF']}?display=list&amp;type={$type}&amp;user=all'>{$GLOBALS['strShowAll']}</a></p>";
    }
    else echo "<p>{$GLOBALS['strNoResults']}</p>";
    mysql_free_result($result);
}
elseif ($display=='week')
{
    echo "<h2>Week View</h2>";
    // Force the week view to the start first day of the week (ie. the monday)
    switch (date('D',mktime(0,0,0,$month,$day,$year)))
    {
        case 'Tue': $day-=1; break;
        case 'Wed': $day-=2; break;
        case 'Thu': $day-=3; break;
        case 'Fri': $day-=4; break;
        case 'Sat': $day-=5; break;
        case 'Sun': $day-=6; break;
        case 'Mon':
        default:
            $day=$day; break;
    }
    echo "<p align='center'>";
    $pdate=mktime(0,0,0,$month,$day-7,$year);
    $ndate=mktime(0,0,0,$month,$day+7,$year);
    echo "<a href='{$_SERVER['PHP_SELF']}?display=week&amp;year=".date('Y',$pdate)."&amp;month=".date('m',$pdate)."&amp;day=".date('d',$pdate)."'>&lt;</a> ";
    echo date('dS F Y',mktime(0,0,0,$month,$day,$year))." &ndash; ".date('dS F Y',mktime(0,0,0,$month,$day+7,$year));
    echo " <a href='{$_SERVER['PHP_SELF']}?display=week&amp;year=".date('Y',$ndate)."&amp;month=".date('m',$ndate)."&amp;day=".date('d',$ndate)."'>&gt;</a>";
    echo "</p>";
    echo draw_chart('week', $year, $month, $day, '', $user);
}
elseif ($display=='day')
{
    // skip over weekends in day view
    if (date('D',mktime(0,0,0,$month,$day,$year))=='Sat') $day-=1;
    if (date('D',mktime(0,0,0,$month,$day,$year))=='Sun') $day+=1;
    if (date('D',mktime(0,0,0,$month,$day,$year))=='Mon') $pdate=mktime(0,0,0,$month,$day-3,$year);
    else $pdate=mktime(0,0,0,$month,$day-1,$year);
    if (date('D',mktime(0,0,0,$month,$day,$year))=='Fri') $ndate=mktime(0,0,0,$month,$day+3,$year);
    else $ndate=mktime(0,0,0,$month,$day+1,$year);
    echo "<h2>Day View</h2>";
    echo "<p align='center'>";
    echo "<a href='{$_SERVER['PHP_SELF']}?display=day&amp;year=".date('Y',$pdate)."&amp;month=".date('m',$pdate)."&amp;day=".date('d',$pdate)."'>&lt;</a> ";
    echo date('l dS F Y',mktime(0,0,0,$month,$day,$year));
    echo " <a href='{$_SERVER['PHP_SELF']}?display=day&amp;year=".date('Y',$ndate)."&amp;month=".date('m',$ndate)."&amp;day=".date('d',$ndate)."'>&gt;</a>";
    echo "</p>";
    echo draw_chart('day', $year, $month, $day, '', $user);
}
else
{
    // Display year calendar
    if ($type < 10)
    {
        echo "<h2>";
        if ($user=='all' && $approver==TRUE) echo "Everybody";
        else echo user_realname($user,TRUE);
        echo "'s Calendar</h2>";
        if ($type==1) echo "<p align='center'>Used ".user_count_holidays($user, $type)." of ".user_holiday_entitlement($user)." days entitlement.<br />";

        // Get list of holiday types
        $holidaytype[1] = $GLOBALS['strHoliday'];
        $holidaytype[2] = $GLOBALS['strAbsentSick'];
        $holidaytype[3] = $GLOBALS['strWorkingAway'];
        $holidaytype[4] = $GLOBALS['strTraining'];
        $holidaytype[5] = $GLOBALS['strCompassionateLeave'];

        echo "<form action='{$_SERVER['PHP_SELF']}' style='text-align: center;'>";
        echo "Calendar: <select class='dropdown' name='type' onchange='window.location.href=this.options[this.selectedIndex].value'>\n";
        foreach ($holidaytype AS $htypeid => $htype)
        {
            echo "<option value='{$_SERVER['PHP_SELF']}?user=$user&amp;type={$htypeid}'";
            if ($type == $htypeid) echo " selected='selected'";
            echo ">{$htype}</option>\n";
        }
        echo "</select></form>";

        $sql = "SELECT * from `{$dbHolidays}` WHERE userid='{$user}' AND approved=0 AND type='$type'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        if (mysql_num_rows($result))
        {
            echo "<table align='center'>";
            echo "<tr class='shade2'><td><strong>Dates waiting for approval</strong>:</td></tr>"; // FIXME i18n waiting
            echo "<tr class='shade1'><td>";
            while ($dates = mysql_fetch_array($result))
            {
                echo date('l ', $dates['startdate']);
                if ($dates['length']=='am') echo "{$strMorning} ";
                if ($dates['length']=='pm') echo "{$strAfternoon} ";
                echo date('jS F Y', $dates['startdate']);
                echo "<br/>\n";
            }
            echo "</td></tr>\n";
            // FIXME i18n send holiday request
            echo "<tr class='shade1'><td><a href='holiday_request.php?type=$type'>Send holiday request</a></td></tr>";
            echo "</table>";
        }
        mysql_free_result($result);

    }
    else
    {
        // Public Holidays are a special type = 10
        echo "<h2>{$strSetPublicHolidays}</h2>";
    }

    echo "<p align='center'>";
    if (!empty($selectedday))
    {
        echo "$selectedday/$selectedmonth/$selectedyear is ";
        switch ($length)
        {
            case 'am':
            echo "selected for the <strong>morning";
            break;

            case 'pm':
            echo "selected for the <strong>afternoon";
            break;

            case 'day':
            echo "selected for the <strong>full day";
            break;

            default:
            echo "<strong>not selected";
        }
        echo "</strong> ";
        echo " as ".holiday_type($type).".  ";

        if ($approved==0)
        {
            switch ($length)
            {
                case 'am':
                    echo "You can make it <a href='add_holiday.php?type=$type&amp;user=$user&amp;year=$selectedyear&amp;month=$selectedmonth&amp;day=$selectedday&amp;length=pm'>the afternoon instead</a>, or select the <a href='add_holiday.php?type=$type&amp;user=$user&amp;year=$selectedyear&amp;month=$selectedmonth&amp;day=$selectedday&amp;length=day'>full day</a>. ";
                break;

                case 'pm':
                    echo "You can make it <a href='add_holiday.php?type=$type&amp;user=$user&amp;year=$selectedyear&amp;month=$selectedmonth&amp;day=$selectedday&amp;length=am'>the morning</a> instead, or select the <a href='add_holiday.php?type=$type&amp;user=$user&amp;year=$selectedyear&amp;month=$selectedmonth&amp;day=$selectedday&amp;length=day'>full day</a>. ";
                break;

                case 'day':
                    echo "You can make it <a href='add_holiday.php?type=$type&amp;user=$user&amp;year=$selectedyear&amp;month=$selectedmonth&amp;day=$selectedday&amp;length=am'>the morning</a>, or <a href='add_holiday.php?type=$type&amp;user=$user&amp;year=$selectedyear&amp;month=$selectedmonth&amp;day=$selectedday&amp;length=pm'>the afternoon</a> instead. ";
            }
            if ($length!='0')
            {
                echo "Or you can <a href='add_holiday.php?type=$type&amp;user=$user&amp;year=$selectedyear&amp;month=$selectedmonth&amp;day=$selectedday&amp;length=0'>deselect</a> it. ";
                echo "<a href='holiday_calendar.php?type=$type&amp;user=$user' title='Clear this message'>Okay</a>.";
            }
        }
        elseif ($approved==1)
        {
            list($xtype, $xlength, $xapproved, $xapprovedby)=user_holiday($user, $type, $selectedyear, $selectedmonth, $selectedday, FALSE);
            echo "Approved by ".user_realname($xapprovedby).".";
            if ($length!='0' && $approver==TRUE && $sit[2]==$xapprovedby) echo "&nbsp;As approver for this holiday you can <a href='add_holiday.php?type=$type&amp;user=$user&amp;year=$selectedyear&amp;month=$selectedmonth&amp;day=$selectedday&amp;length=0'>deselect</a> it.";
        }
        else
        {
            echo "<span class='error'>Declined</span>.  You should <a href='add_holiday.php?type=$type&amp;user=$user&amp;year=$selectedyear&amp;month=$selectedmonth&amp;day=$selectedday&amp;length=0'>deselect</a> it.";
        }
    }
    else
    {
        echo "Click on a day to select it"; // FIXME i18n click on a day
    }
    echo "</p>\n";


    echo "<h2>{$strYear} View</h2>"; // FIXME i18n Year View
    $pdate=mktime(0,0,0,$month,$day,$year-1);
    $ndate=mktime(0,0,0,$month,$day,$year+1);
    echo "<p align='center'>";
    echo "<a href='{$_SERVER['PHP_SELF']}?display=year&amp;year=".date('Y',$pdate)."&amp;month=".date('m',$pdate)."&amp;day=".date('d',$pdate)."&amp;type={$type}'>&lt;</a> ";
    echo date('Y',mktime(0,0,0,$month,$day,$year));
    echo " <a href='{$_SERVER['PHP_SELF']}?display=year&amp;year=".date('Y',$ndate)."&amp;month=".date('m',$ndate)."&amp;day=".date('d',$ndate)."&amp;type={$type}'>&gt;</a>";
    echo "</p>";


    echo "<table align='center' border='1' cellpadding='0' cellspacing='0' style='border-collapse:collapse; border-color: #AAA; width: 80%;'>";
    $displaymonth=1;
    $displayyear=$year;
    for ($r==1;$r<3;$r++)
    {
        echo "<tr>";
        for ($c=1;$c<=4;$c++)
        {
            echo "<td valign='top' align='center' class='shade1'>";
            draw_calendar($displaymonth,$displayyear);
            echo "</td>";
            if ($displaymonth==12) { $displayyear++; $displaymonth=0; }
            $displaymonth++;
        }
        echo "</tr>";
    }
    echo "</table>";
}
include ('htmlfooter.inc.php');
?>