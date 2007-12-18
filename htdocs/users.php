<?php
// users.php - List users
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// This Page Is Valid XHTML 1.0 Transitional! 31Oct05
// This page seems to sometimes generate a warning
// Warning: Unknown: Your script possibly relies on a session side-effect which existed until PHP 4.2.3. Please be advised that the session extension does not consider global variables as a source of data, unless register_globals is enabled. You can disable this functionality and this warning by setting session.bug_compat_42 or session.bug_compat_warn to off, respectively. in Unknown on line 0
// Not sure why - Ivan 6Sep06

@include ('set_include_path.inc.php');
$permission=14; // View Users
require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

// External variables
$sort = cleanvar($_REQUEST['sort']);
$order = cleanvar($_REQUEST['order']);
$groupid = cleanvar($_REQUEST['gid']);
$changeuser = cleanvar($_REQUEST['user']);
$newstatus = cleanvar($_REQUEST['status']);

//TODO: maybe put this in another file?
if ($changeuser AND $newstatus)
{
    $sql = "UPDATE `{$dbUsers}` SET accepting='{$newstatus}' WHERE id={$changeuser}";
    @mysql_query($sql);
}


// By default show users in home group
if ($groupid=='all') $filtergroup = 'all';
elseif ($groupid=='') $filtergroup = $_SESSION['groupid'];
else $filtergroup = $groupid;

include ('htmlheader.inc.php');

$gsql = "SELECT * FROM groups ORDER BY name";
$gresult = mysql_query($gsql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
while ($group = mysql_fetch_object($gresult))
{
    $grouparr[$group->id]=$group->name;
}
$numgroups = count($grouparr);
echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/user.png' width='32' height='32' alt='' /> ";
echo "{$strUsers}</h2>";
if ($numgroups >= 1)
{
    echo "<form action='{$_SERVER['PHP_SELF']}' style='text-align: center;' method='get'>";
    echo "{$strGroup}: <select name='choosegroup' onchange='window.location.href=this.options[this.selectedIndex].value'>";
    echo "<option value='{$_SERVER['PHP_SELF']}?gid=all'";
    if ($filtergroup=='all') echo " selected='selected'";
    echo ">All</option>\n";
    foreach ($grouparr AS $groupid => $groupname)
{
        echo "<option value='{$_SERVER['PHP_SELF']}?gid={$groupid}'";
        if ($groupid == $filtergroup) echo " selected='selected'";
        echo ">$groupname</option>\n";
}
    echo "<option value='{$_SERVER['PHP_SELF']}?gid=0'";
    if ($filtergroup=='0') echo " selected='selected'";
    echo ">{$strUsersNoGroup}</option>\n";
    echo "</select>\n";
    echo "</form>\n<br />";
}

$sql  = "SELECT * FROM `{$dbUsers}` WHERE status!=0 ";  // status=0 means account disabled
if ($numgroups >= 1 AND $filtergroup=='0') $sql .= "AND (groupid='0' OR groupid='' OR groupid IS NULL) ";
elseif ($numgroups < 1 OR $filtergroup=='all') { $sql .= "AND 1=1 "; }
else $sql .= "AND groupid='{$filtergroup}'";

// Sorting
if (!empty($sort))
{
    if ($sort == "realname") $sql .= " ORDER BY realname ";
    elseif ($sort == "jobtitle") $sql .= " ORDER BY title ";
    elseif ($sort == "email") $sql .= " ORDER BY email ";
    elseif ($sort == "phone") $sql .= " ORDER BY phone ";
    elseif ($sort == "fax") $sql .= " ORDER BY fax ";
    elseif ($sort == "status") $sql .= " ORDER BY status ";
    elseif ($sort == "accepting") $sql .= " ORDER BY accepting ";
    else $sql .= " ORDER BY realname ";

    if ($order=='a' OR $order=='ASC' OR $order='') $sql .= "ASC";
    else $sql .= "DESC";
}
else $sql .= "ORDER BY realname ASC ";

$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

echo "<table align='center' style='width: 95%;'>";
echo "<tr>";
$filter=array('gid' => $filtergroup);
echo colheader('realname', $strName, $sort, $order, $filter);
echo "<th colspan='5'>{$strIncidentsinQueue}</th>";
echo colheader('phone',$strTelephone,$sort, $order, $filter);
echo colheader('mobile',$strMobile,$sort, $order, $filter);
echo colheader('status',$strStatus,$sort, $order, $filter);
echo colheader('accepting',$strAccepting,$sort, $order, $filter);
echo "<th>{$strJumpTo}</th>";
echo "</tr><tr>";
echo "<th></th>";
echo "<th align='center'>{$strActionNeeded} / {$strWaiting}</th>";
echo "<th align='center'>".priority_icon(4)."</th>";
echo "<th align='center'>".priority_icon(3)."</th>";
echo "<th align='center'>".priority_icon(2)."</th>";
echo "<th align='center'>".priority_icon(1)."</th>";
echo "<th colspan='8'></th>";
echo "</tr>\n";

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
    echo "<a href='mailto:{$users['email']}' title='{$strEmail} {$users['realname']}'><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/email.png' width='16' height='16' alt='{$strEmail}' style='border:none;' /></a> ";
    echo "<a href='incidents.php?user={$users['id']}&amp;queue=1&amp;type=support' class='info'>";
    if (!empty($users['message'])) echo "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/messageflag.png' width='16' height='16' title='{$users['message']}' alt='{$strMessage}' /> ";
    echo "{$users['realname']}";
    echo "<span>";
    if (!empty($users['title'])) echo "<strong>{$users['title']}</strong><br />";
    if ($users['groupid'] > 0) echo "{$strGroup}: {$grouparr[$users['groupid']]}<br />";
    if (strlen($users['aim']) > 3) echo "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/aim.png' width='16' height='16' alt='{$users['aim']}' /> <strong>AIM</strong>: {$users['aim']}<br />";
    if (strlen($users['icq']) > 3) echo "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/icq.png' width='16' height='16' alt='{$users['icq']}' /> <strong>ICQ</strong>: {$users['icq']}<br />";
    if (strlen($users['msn']) > 3) echo "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/msn.png' width='16' height='16' alt='{$users['msn']}' /> <strong>MSN</strong>: {$users['msn']}<br />";
    if (!empty($users['message'])) echo "<br /><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/messageflag.png' width='16' height='16' alt='' /> <strong>{$strMessage}</strong>: {$users['message']}";
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
    <td align='center'>
    <?php
        if ($users["phone"] == "") echo $strNone;
        else echo $users["phone"];

        echo "</td>";
        echo "<td align='center'>";

        if ($users["mobile"] == "") echo $strNone;
        else echo $users["mobile"];
    ?>
    </td>
    <td align='left'>
    <?php
    //see if the users has been active in the last 30mins
    echo user_online($users[id]);
    echo userstatus_name($users["status"]);
    echo "</td><td align='center'>";
    echo $users["accepting"]=='Yes' ? $strYes : "<span class='error'>{$strNo}</span>";
    echo "</td><td>";
    echo "<a href='holidays.php?user={$users['id']}' title='{$strHolidays}'><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/holiday.png' width='16' height='16' alt='{$strHolidays}' style='border:none;' /></a> ";
    echo "<a href='tasks.php?user={$users['id']}' title='{$strTasks}'><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/task.png' width='16' height='16' alt='Todo icon' style='border:none;' /></a> ";
    $sitesql = "SELECT COUNT(id) FROM `{$dbSites}` WHERE owner='{$users['id']}'";
    $siteresult = mysql_query($sitesql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    list($ownedsites) = mysql_fetch_row($siteresult);
    if ($ownedsites > 0) echo "<a href='browse_sites.php?owner={$users['id']}' title='{$strSites}'><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/site.png' width='16' height='16' alt='Sites icon' style='border:none;' /></a> ";
    echo "</td>";
    echo "</tr>";

    // invert shade
    if ($shade == 1) $shade = 0;
    else $shade = 1;
}
echo "</table>\n";

mysql_free_result($result);

include ('htmlfooter.inc.php');
?>