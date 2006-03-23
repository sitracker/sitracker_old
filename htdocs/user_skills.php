<?php
// user_skills.php - Display a list of users skills
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
// This Page Is Valid XHTML 1.0 Transitional!  31Oct05

$permission=14; // View Users
$title = "User Skills";
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

// External Variables
$sort = cleanvar($_REQUEST['sort']);

include('htmlheader.inc.php');

$sql  = "SELECT * FROM users WHERE status!=0";  // status=0 means account disabled

// sort users by realname by default
if (empty($sort) || $sort == "realname")  $sql .= " ORDER BY realname ASC";

$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

?>
<h2>User Skills Listing</h2>
<table align="center" style="width:95%;">
<tr>
    <th><a href="<?php echo $_SERVER['PHP_SELF'] ?>?sort=realname">Name</a></th>
    <th>Qualifications / Skills</th>
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
    <tr>
        <td rowspan='2' class='<?php echo $class ?>'><a href="mailto:<?php  echo $users['email'] ?>" title="<?php $users['title']  ?>"><?php  echo $users['realname'] ?></a></td>
        <td class='<?php echo $class ?>'><?php if ($users["qualifications"] == "") { ?>-<?php } else { echo "<strong>".$users["qualifications"]."</strong>"; } ?></td>
    </tr>
    <tr>
    <?php
    echo "<td class='$class'>";
    $ssql = "SELECT * FROM usersoftware, software WHERE usersoftware.softwareid = software.id AND usersoftware.userid='{$users['id']}' ORDER BY software.name ";
    $sresult = mysql_query($ssql);
    $countskills=mysql_num_rows($sresult);
    $nobackup=0;
    if ($countskills >= 1)
    {
        $c=1;
        while ($software = mysql_fetch_object($sresult))
        {
            if ($software->backupid==0) echo "<u class='info' title='No backup engineer'>{$software->name}</u>";
            else echo "<span class='info' title='Backup: ".user_realname($software->backupid)."'>{$software->name}</span>";
            if ($software->backupid==0) $nobackup++;
            if ($c < $countskills) echo ", ";
            else
            {
                echo "<br />&bull; $countskills Software skills";
                if (($nobackup+1) >= $countskills) echo ", <strong>No backup engineers defined</strong>.";
                elseif ($nobackup > 0) echo ", <strong>{$nobackup} need backup engineers to be defined</strong>.";
            }
            $c++;
        }
    }
    else echo "-";

    if ($users['id']==$sit[2]) echo " <a href='edit_user_software.php'>Define your skills</a>";

    echo "</td>";
    ?>
    </tr>
    <?php
    // invert shade
    if ($shade == 1) $shade = 0;
    else $shade = 1;
}
echo "</table>\n";

// free result and disconnect
mysql_free_result($result);

include('htmlfooter.inc.php');
?>