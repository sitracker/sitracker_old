<?php
// incident_calendar.php -
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

// FIXME this isn't on the menu, is it still required?

@include ('set_include_path.inc.php');
$permission = 27; // View your calendar
require ('db_connect.inc.php');
require ('functions.inc.php');
$title="Incident Calendar";

// This page requires authentication
require ('auth.inc.php');

// External variables


include ('htmlheader.inc.php');

/**
    * @author Ivan Lucas
*/
function draw_calendar($nmonth, $nyear)
{

    /* Get the current date/time for the users timezone */
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
       # *Sigh* recursion would have been more fun here.
       $monthOrig = date('m',mktime(0,0,0,$nmonth,1,$nyear));
       $monthTest = date('m',mktime(0,0,0,$nmonth,$lastday,$nyear));
       if ($monthTest != $monthOrig) { $lastday -= 1; }
    }
    while ($monthTest != $monthOrig);
       $monthName = date('F',mktime(0,0,0,$nmonth,1,$nyear));

       if ($CONFIG['debug'])
       {
         print("<p>first day of the first week of $nmonth $nyear is $firstday (from 0 to 6) <p>\n");
         print("The last day of $nmonth $nyear is $lastday\n<p>");
       }
       $days[0] = 'Sun';
       $days[1] = 'Mon';
       $days[2] = 'Tue';
       $days[3] = 'Wed';
       $days[4] = 'Thu';
       $days[5] = 'Fri';
       $days[6] = 'Sat';

       $dayRow = 0;
       ?> <table> <?php

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
       if ($nmonth < 12)
       {
         // $nextmonth=nmonth+1;
         $nextyear=$nyear;
       }
       if ($nmonth == 12)
       {
         $nextmonth = 1;
         $nextyear = $nyear + 1;
       }
       echo "<caption valign=\"center\">";
       /* Print Current Month */
       echo "&nbsp;<b><span class=\"calendartitle\">$monthName $nyear</span></b>";
       echo "&nbsp;";
       echo "</caption>";
       print("<tr>\n");
       for($i=0; $i<=6; $i++)
       {
         print("<td width=\"10%\" ");
         if ($i==0 || $i==6)
         { print("class=\"shade1\""); }
         else
         { print("class=\"shade2\""); }
         print(">$days[$i]</td>\n");
       }
       print("</tr>\n");

       print("<tr>\n");
       while ($dayRow < $firstday)
       {
         print("<td><!-- This day in last month --></td>");
         $dayRow += 1;
       }
       $day = 0;
       if ($frametarget)
       {
         $targetString = 'target = '.$frametarget;
       }
       else
       {
         $targetString = '';
       }
       while ($day < $lastday)
       {
         if (($dayRow % 7) == 0)
         {
           print("</tr>\n<tr>\n");
         }
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
           print("<td id=\"id$calday\" class=\"calendar\"><a href=\"daymessages.php?month=$nmonth&day=$calday&year=$nyear&sid=$sid\" title=\"$rowcount messages\"
           $targetString target=\"mainscreen\" onmouseover=\"window.over('id$calday')\" onMouseOut=\"window.out('id$calday')\">$bold$adjusted_day$notbold</a></td>");
         }
         else
         {
           /*
                     if ($dayRow % 7 == 0 || $dayRow % 7 == 6)
                     {
                       print("<td class=\"shade1\">");
                     }
                     else
                     {
                      print("<td class=\"shade2\">");
                     }
           */
           $countdayincidents=countdayincidents($calday, $nmonth, $nyear);
           $maxdayincidents=30;
           $bgcol=($countdayincidents/$maxdayincidents)*255;
           $bgcol2=255-$bgcol;
           $bgcol2=dechex($bgcol2);
           if (strlen($bgcol2)<2) $bgcol2='0'.$bgcol2;
           if (strlen($bgcol2)>2) $bgcol2='FF';
           $bgcolor=$bgcol2.$bgcol2.$bgcol2;
           if ($countdayincidents<1) $bgcolor='FFFFFF';
           echo "<td bgcolor=\"#$bgcolor\">";

                     print("<a href=\"#\" title=\"$countdayincidents Incidents\">$bold$adjusted_day$notbold</a></td>");
         }

         $day += 1;
         $dayRow += 1;
       }
       print("\n</tr>\n</table>\n");
       #  print("$nmonth");

}

      echo "<h2>Number of Incidents Logged each day</h2>";

      echo "<table summary='calendar' align='center'>";
      $month=1;
      for ($r==1;$r<3;$r++)
      {
        echo "<tr>";
        for ($c=1;$c<=4;$c++)
        {
          echo "<td valign='top' align='center' class='shade1'>";
          draw_calendar($month,2001);
          echo "</td>";
          $month++;
        }
        echo "</tr>\n";
      }
      echo "</table>\n";

      include ('htmlfooter.inc.php');
?>
