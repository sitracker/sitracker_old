<?php
// holiday_chart.php - Calendar showing holidays
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas

$permission=37; // Run Reports
require('db_connect.inc.php');
require('functions.inc.php');
$title="Holiday Chart";

// This page requires authentication
require('auth.inc.php');

// Valid user, check permissions
if (!user_permission($sit[2],$permission))
{
    header("Location: noaccess.php?id=$permission");
    exit;
}
include('htmlheader.inc.php');

$letter='';

$sql  = "SELECT * FROM users WHERE status!=0 ORDER BY realname ASC";  // status=0 means left company
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

if (!isset($month)) $month=date('m');
if (!isset($year)) $year=date('Y');

?>
<h2><?php echo $title; ?></h2>

<form action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post' name='dateform'>
<p align='center'>
<?php
month_drop_down('month', $month);
year_drop_down('year', $year);
?>
<a href="javascript:document.dateform.submit();">Go</a>
</p>
</form>

<table>
<tr>
<td align='left' class='shade2'><strong>Name</strong></td>
<?php
$daysinmonth=date('t',mktime(0,0,0,$month,1,$year));
for($day=1;$day<=$daysinmonth;$day++)
{
    $shade='shade1';
    if (date('D',mktime(0,0,0,$month,$day,$year))=='Sat' || date('D',mktime(0,0,0,$month,$day,$year))=='Sun') $shade='expired';
    echo "<td align='center' class=\"$shade\">";
    echo date('D',mktime(0,0,0,$month,$day,$year))."<br>".date('jS',mktime(0,0,0,$month,$day,$year)) ;
    echo "</td>";
}
?>
</tr>
<?php

// show results
$shade = 0;
while ($users = mysql_fetch_array($result))
{
    // define class for table row shading
    if ($shade) $class = "shade1";
    else $class = "shade2";

    ?>
    <tr>
    <td align=left class='shade1'><a href="mailto:<?php  echo $users['email'] ?>" title="<?php $users['title']  ?>"><?php  echo $users['realname'] ?></a></td>

    <?php
    for($day=1;$day<=$daysinmonth;$day++)
    {
        $shade='shade2';
        $halfday="";
        $letter='';
        $style='';
        list($dtype,$dlength,$dapproved)=user_holiday($users['id'], 0, $year, $month, $day, FALSE);
        switch ($dtype)
        {
            case 1:
                $shade= "mainshade";
                if ($dapproved==1) $shade='idle';
                if ($dapproved==2) $shade='shade2';
                $letter="H";
                $daytitle="Holiday";
            break;

            case 2:
                $shade= "mainshade";
                if ($dapproved==1) $shade='urgent';
                if ($dapproved==2) $shade='shade2';
                $letter="S";
                $daytitle="Sickness";
            break;

            case 3:
                $shade= "mainshade";
                if ($dapproved==1) $shade='idle';
                if ($dapproved==2) $shade='shade2';
                $letter="W";
                $daytitle="Working Away";
            break;

            case 4:
                $shade= "mainshade";
                if ($dapproved==1) $shade='idle';
                if ($dapproved==2) $shade='shade2';
                $letter="T";
                $daytitle="Training";
            break;

            case 5:
                $shade= "mainshade";
                if ($dapproved==1) $shade='idle';
                if ($dapproved==2) $shade='shade2';
                $letter="L";
                $daytitle="Other Leave";
            break;


            case 10: // bank holidays
                $shade="expired";
                $style="border-left: 1px dotted blue; border-right: 1px dotted blue;";
            break;

            default:
                $shade="shade2";
            break;
        }
        if ($dlength=='pm')
        {
            $halfday = "style=\"background-image: url({$CONFIG['application_webpath']}images/halfday-pm.gif)\" ";
            $style="background-image: url({$CONFIG['application_webpath']}images/halfday-pm.gif);";
        }
        if ($dlength=='am')
        {
            $halfday = "style=\"background-image: url({$CONFIG['application_webpath']}images/halfday-am.gif)\" ";
            $style="background-image: url({$CONFIG['application_webpath']}images/halfday-am.gif);";
        }
        if (date('D',mktime(0,0,0,$month,$day,$year))=='Sat' || date('D',mktime(0,0,0,$month,$day,$year))=='Sun') $shade='expired';

        echo "<td align='center' class=\"$shade\" style=\"$style\" title=\"$daytitle\">";

        if ($length=='day') echo "x";
        else  echo "&nbsp;$letter";

        echo "</td>";
        $daytitle='';
    }
    echo "</tr>";
    // invert shade
    if ($shade == 1) $shade = 0;
    else $shade = 1;
}
echo "</table>\n";
echo "<p align='center'><strong>Legend:</strong> H - Holiday, S - Sickness, W - Working Away, T - Training, L - Other Leave</p>";

include('htmlfooter.inc.php');
include('db_disconnect.inc.php');
?>