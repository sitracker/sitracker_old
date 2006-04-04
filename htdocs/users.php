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


include('htmlheader.inc.php');

$sql  = "SELECT * FROM users WHERE status!=0";  // status=0 means left company

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

?>
<h2>User Listing</h2>
<table align='center' style='width: 95%;'>
<tr>
    <th align='left'><a href="<?php echo $_SERVER['PHP_SELF'] ?>?sort=realname">Name</a> (Click name to send email)</th>
    <th align='center'>View Incidents</th>
    <?php if (strlen(user_aim($sit[2])) > 3) { echo "<th>AIM</th>"; } ?>
    <?php if (strlen(user_icq($sit[2])) > 3) { echo "<th>ICQ</th>"; } ?>
    <?php if (strlen(user_msn($sit[2])) > 3) { echo "<th>MSN</th>"; } ?>
    <th><a href="<?php echo $_SERVER['PHP_SELF'] ?>?sort=phone">Phone</a></th>
    <th>Mobile</th>
    <th><a href="<?php echo $_SERVER['PHP_SELF'] ?>?sort=status">Status</a></th>
    <th><a href="<?php echo $_SERVER['PHP_SELF'] ?>?sort=accepting">Accepting</a></th>
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
    ?>
    <tr class='<?php echo $class ?>'>
    <td><a href="mailto:<?php  echo $users['email'] ?>" title="<?php $users['title']  ?>"><?php  echo $users['realname'] ?></a></td>
    <td align='center'><a href="incidents.php?user=<?php echo $users["id"] ?>&amp;queue=1&amp;type=support">
    <?php
    $countincidents = user_countincidents($users['id']);
    if ($countincidents >= 1) $countactive=user_activeincidents($users['id']);
    else $countactive=0;

    $countdiff=$countincidents-$countactive;

    if ($countincidents==0) echo "None</a>";
    elseif ($countactive==$countincidents) echo "{$countincidents} Action Needed</a>";
    elseif ($countactive >= 1) echo "{$countactive} Action Needed + {$countdiff} Other</a>";
    elseif ($countactive < 1) echo "{$countincidents} Incidents</a>";
    else echo "{$countactive} Action Needed + {$countdiff} Other</a>";
    echo "</td>";
    if (strlen(user_aim($sit[2])) > 3) { ?> <td align='center'><?php if ($users['aim'] !='') echo "<a href=\"javascript:alert('{$users['aim']}');\" title=\"".$users['aim']."\"><img src=\"images/icons/16x16/apps/ksmiletris.png\" border=\"0\" width=\"16\" height=\"16\" alt=\"".$users['aim']."\" /></a>"; else echo '&nbsp;';  ?></td> <?php } ?>
    <?php if (strlen(user_icq($sit[2])) > 3) { ?> <td align='center'><?php if ($users['icq'] !='') echo "<a href=\"javascript:alert('{$users['icq']}');\" title=\"{$users['icq']}\"><img src=\"images/icons/16x16/apps/licq.png\" border=\"0\" width=\"16\" height=\"16\" alt=\"".$users['icq']."\" /></a>"; else echo '&nbsp;'; ?></td> <?php } ?>
    <?php if (strlen(user_msn($sit[2])) > 3) { ?> <td align='center'><?php if ($users['msn'] !='') echo "<a href=\"javascript:alert('{$users['msn']}');\"><img src=\"images/icons/16x16/apps/personal.png\" width=\"16\" height=\"16\" border=\"0\" title=\"{$users['msn']}\" alt=\"".$users['msn']."\" /></a>"; else echo '&nbsp;'; ?></td> <?php } ?>

    <td align='center'><?php if ($users["phone"] == "") { ?>None<?php } else { echo $users["phone"]; } ?></td>
    <td align='center'><?php if ($users["phone"] == "") { ?>None<?php } else { if ($users['mobile']!='') echo $users["mobile"]; else echo '&nbsp;'; } ?></td>
    <td align='center'><?php echo userstatus_name($users["status"]) ?></td>
    <td align='center'><?php echo $users["accepting"]=='Yes' ? 'Yes' : "<span class='error'>No</span>"; ?></td>
    </tr>
    <?php
    if ($users['message'] != '')
    {
        ?>
        <tr class='<?php echo $class ?>'><td align='right'><strong>Message</strong>:</td><td colspan='10'><?php echo strip_tags($users['message']); ?></td></tr>
        <?php
    }

    // invert shade
    if ($shade == 1) $shade = 0;
    else $shade = 1;
}
echo "</table>\n";

mysql_free_result($result);

include('htmlfooter.inc.php');
?>