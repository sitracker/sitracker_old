<?php
// main.php - Front page
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
// This Page Is Valid XHTML 1.0 Transitional! 27Oct05

$permission=0; // not required
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

// Valid user
include('htmlheader.inc.php');

// Count incidents logged today
$sql = "SELECT id FROM incidents WHERE opened > '$todayrecent'";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
$todaysincidents=mysql_num_rows($result);
mysql_free_result($result);

// Count incidents updated today
$sql = "SELECT id FROM incidents WHERE lastupdated > '$todayrecent'";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
$todaysupdated=mysql_num_rows($result);
mysql_free_result($result);

// Count incidents closed today
$sql = "SELECT id FROM incidents WHERE closed > '$todayrecent'";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
$todaysclosed=mysql_num_rows($result);
mysql_free_result($result);

// count total number of SUPPORT incidents that are open at this time (not closed)
$sql = "SELECT id FROM incidents WHERE status!=2 AND status!=9 AND status!=7 AND type='support'";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
$supportopen=mysql_num_rows($result);
mysql_free_result($result);

    // Count kb articles published today
$sql = "SELECT docid FROM kbarticles WHERE published > '".date('Y-m-d')."'";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
$kbpublished=mysql_num_rows($result);
mysql_free_result($result);

echo "<div style='height: 400px;'>";
echo "<div class='windowbox' style='width: 50%'>";
echo "<div class='windowtitle'>Random Tip</div>";
echo "<div class='window'>";
echo random_tip();
echo "</div>";
echo "</div>";

echo "<div class='windowbox' style='width: 50%'>";
echo "<div class='windowtitle'>Todays Statistics</div>";
echo "<div class='window'>";
if ($todaysincidents == 0) echo "No Incidents";
elseif ($todaysincidents == 1) echo "{$todaysincidents} Incident";
elseif ($todaysincidents > 1) echo "{$todaysincidents} Incidents";
echo " logged<br />";

if ($todaysupdated == 0) echo "No Incidents";
elseif ($todaysupdated == 1) echo "{$todaysupdated} Incident";
elseif ($todaysupdated > 1) echo "{$todaysupdated} Incidents";
echo " updated<br />";

if ($todaysclosed == 0) echo "No Incidents";
elseif ($todaysclosed == 1) echo "{$todaysclosed} Incident";
elseif ($todaysclosed > 1) echo "{$todaysclosed} Incidents";
echo " closed<br />";

if ($supportopen == 0) echo "No Incidents";
elseif ($supportopen == 1) echo "{$supportopen} Incident";
elseif ($supportopen > 1) echo "{$supportopen} Incidents";
echo " currently open<br />";

if ($kbpublished == 0) echo "No KB Articles";
elseif ($kbpublished == 1) echo "{$kbpublished} KB Article";
elseif ($kbpublished > 1) echo "{$kbpublished} KB Articles";
echo " published<br />";

echo "</div>";
echo "</div>";
echo "</div>";


// May use these commented out bits, or similar bits before v4.  INL 26Oct05
// Count incidents logged today
// Count incidents updated today
// Count incidents closed today
// count total number of SUPPORT incidents that are open at this time (not closed)



//  Users Login Details
echo "<div id='userbar'>Logged in as: <strong>{$sit[0]}</strong>, ";
echo "currently <strong>".userstatus_name(user_status($sit[2]))."</strong> and ";

if (user_accepting($sit[2])!='Yes')
{
    echo "<span class=\"error\">Not Accepting</span>";
}
else
{
    echo "<strong>Accepting</strong>";
}
echo " calls";
if ($sit[3]=='public')
{
    echo "- Public/Shared Computer (Increased Security)";
}
?>
</div>
<div id='footerbar'>
<?php
echo "<form style='margin: 0px;' action='{$_SERVER['PHP_SELF']}'>";
?>
Set your Status: <?php if(isset($sit[2])) userstatus_bardrop_down("status", user_status($sit[2])); ?></form>
</div>
<?php
include('htmlfooter.inc.php');
?>