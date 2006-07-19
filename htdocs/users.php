<?php
// users.php - List users
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// This Page Is Valid XHTML 1.0 Transitional! 31Oct05

$permission=14; // View Users
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

// External variables
$sort = cleanvar($_REQUEST['sort']);
$groupid = cleanvar($_REQUEST['gid']);

// By default show users in home group
if ($groupid=='all') $filtergroup = 'all';
elseif ($groupid=='') $filtergroup = $_SESSION['groupid'];
else $filtergroup = $groupid;

include('htmlheader.inc.php');

$sql  = "SELECT * FROM users WHERE status!=0 ";  // status=0 means left company
if ($filtergroup=='0') $sql .= "AND (groupid='{$filtergroup}' OR groupid='')";
elseif ($filtergroup=='all') {} // Do nothing
else $sql .= "AND groupid='{$filtergroup}'";

// sort users by realname by default
if ($sort == "realname")
{
    $sql .= " ORDER BY realname ASC";
}
// sort incidents by job title
elseif ($sort == "jobtitle")
{
    $sql .= " ORDER BY title ASC";
}
// sort incidents by email
elseif ($sort == "email")
{
    $sql .= " ORDER BY email ASC";
}
// sort incidents by phone
elseif ($sort == "phone")
{
    $sql .= " ORDER BY phone ASC";
}
// sort incidents by fax
elseif ($sort == "fax")
{
    $sql .= " ORDER BY fax ASC";
}
// sort incidents by status
elseif ($sort == "status")
{
    $sql .= " ORDER BY status ASC";
}
// sort incidents by accepting calls
elseif ($sort == "accepting")
{
    $sql .= " ORDER BY accepting ASC";
}
else $sql .= " ORDER BY realname ASC";

$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);


$gsql = "SELECT * FROM groups ORDER BY name";
$gresult = mysql_query($gsql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
while ($group = mysql_fetch_object($gresult))
{
    $grouparr[$group->id]=$group->name;
}
$numgroups = count($grouparr);
echo "<h2>User Listing</h2>";
if ($numgroups >= 1)
{
    echo "<form action='{$_SERVER['PHP_SELF']}' style='text-align: center;' method='get'>";
    echo "Group: <select name='choosegroup' onchange='window.location.href=this.options[this.selectedIndex].value'>";
    echo "<option value='{$_SERVER['PHP_SELF']}?gid=all'";
    if ($filtergroup=='all') echo " selected='selected'";
    echo ">All</option>\n";
    foreach($grouparr AS $groupid => $groupname)
    {
        echo "<option value='{$_SERVER['PHP_SELF']}?gid={$groupid}'";
        if ($groupid == $filtergroup) echo " selected='selected'";
        echo ">$groupname</option>\n";
    }
    echo "<option value='{$_SERVER['PHP_SELF']}?gid=0'";
    if ($filtergroup=='0') echo " selected='selected'";
    echo ">Users with no group</option>\n";
    echo "</select>\n";
    echo "</form>\n<br />";
}



?>

<table align='center' style='width: 95%;'>
<tr>
    <th align='left'><a href="<?php echo $_SERVER['PHP_SELF'] ?>?sort=realname">Name</a></th>
    <th colspan='5'>Incidents in Queue</th>
    <th><a href="<?php echo $_SERVER['PHP_SELF'] ?>?sort=phone">Phone</a></th>
    <th>Mobile</th>
    <th><a href="<?php echo $_SERVER['PHP_SELF'] ?>?sort=status">Status</a></th>
    <th><a href="<?php echo $_SERVER['PHP_SELF'] ?>?sort=accepting">Accepting</a></th>
</tr>
<tr>
    <th></th>
    <th align='center'>Action Needed / Waiting</th>
    <?php
    echo "<th align='center'>".priority_icon(4)."</th>";
    echo "<th align='center'>".priority_icon(3)."</th>";
    echo "<th align='center'>".priority_icon(2)."</th>";
    echo "<th align='center'>".priority_icon(1)."</th>";
    ?>
    <th colspan='7'></th>
</tr>
<?php

// show results
$shade = 0;
while ($users = mysql_fetch_array($result))
{
    // define class for table row shading
    if ($shade) $class = "shade1";
    else $class = "shade2";

    // print HTML for rows
    echo "<tr class='$class'>";
    echo "<td>";
    echo "<a href='mailto:{$users['email']}' title='Email {$users['realname']}'><img src='{$CONFIG['application_webpath']}images/icons/16x16/actions/mail_generic.png' width='16' height='16' alt='Email icon' style='border:none;' /></a> ";
    echo "<a href='incidents.php?user={$users['id']}&amp;queue=1&amp;type=support' class='info'>";
    if (!empty($users['message'])) echo "<img src='{$CONFIG['application_webpath']}images/icons/16x16/actions/flag.png' width='16' height='16' title='{$users['message']}' alt='Flag' />";
    echo "{$users['realname']}";
    echo "<span>";
    if (!empty($users['title'])) echo "<strong>{$users['title']}</strong><br />";
    if ($users['groupid'] > 0) echo "Group: {$grouparr[$users['groupid']]}<br />";
    if (strlen($users['aim']) > 3) echo "<img src='{$CONFIG['application_webpath']}images/icons/16x16/apps/ksmiletris.png' width='16' height='16' alt='{$users['aim']}' /> <strong>AIM</strong>: {$users['aim']}<br />";
    if (strlen($users['icq']) > 3) echo "<img src='{$CONFIG['application_webpath']}images/icons/16x16/apps/licq.png' width='16' height='16' alt='{$users['icq']}' /> <strong>ICQ</strong>: {$users['icq']}<br />";
    if (strlen($users['msn']) > 3) echo "<img src='{$CONFIG['application_webpath']}images/icons/16x16/apps/personal.png' width='16' height='16' alt='{$users['msn']}' /> <strong>MSN</strong>: {$users['msn']}<br />";
    if (!empty($users['message'])) echo "<br /><strong>Message</strong>: {$users['message']}";
    echo "</span>";
    echo "</a>";
    echo "</td>";
    echo "<td align='center'><a href='incidents.php?user={$users['id']}&amp;queue=1&amp;type=support'>";
    $incpriority = user_incidents($users['id']);
    $countincidents = ($incpriority['1']+$incpriority['2']+$incpriority['3']+$incpriority['4']);
    if ($countincidents >= 1) $countactive=user_activeincidents($users['id']);
    else $countactive=0;

    $countdiff=$countincidents-$countactive;

    echo $countactive;
    echo "</a> / <a href='incidents.php?user={$users['id']}&amp;queue=2&amp;type=support'>{$countdiff}</a></td>";
    echo "<td align='center'>".$incpriority['4']."</td>";
    echo "<td align='center'>".$incpriority['3']."</td>";
    echo "<td align='center'>".$incpriority['2']."</td>";
    echo "<td align='center'>".$incpriority['1']."</td>";
    ?>
    <td align='center'><?php if ($users["phone"] == "") { ?>None<?php } else { echo $users["phone"]; } ?></td>
    <td align='center'><?php if ($users["phone"] == "") { ?>None<?php } else { if ($users['mobile']!='') echo $users["mobile"]; else echo '&nbsp;'; } ?></td>
    <td>
    <?php echo "<a href='holiday_calendar.php?type=1&amp;user={$users['id']}' title='Holiday Calendar'><img src='{$CONFIG['application_webpath']}images/icons/16x16/apps/date.png' width='16' height='16' alt='Calendar icon' style='border:none;' /></a> ";
    echo userstatus_name($users["status"]) ?></td>
    <td align='center'><?php echo $users["accepting"]=='Yes' ? 'Yes' : "<span class='error'>No</span>"; ?></td>
    </tr>
    <?php
    // invert shade
    if ($shade == 1) $shade = 0;
    else $shade = 1;
}
echo "</table>\n";

mysql_free_result($result);

include('htmlfooter.inc.php');
?>