<?php
// holiday_approve.php -
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas

$permission=50; // Approve Holiday
require('db_connect.inc.php');
require('functions.inc.php');
$title="Holiday Approval";

// This page requires authentication
require('auth.inc.php');

// Valid user, check permissions
if (!user_permission($sit[2],$permission))
{
    header("Location: noaccess.php?id=$permission");
    exit;
}
// External variables
$approve = $_REQUEST['approve'];
$startdate = cleanvar($_REQUEST['startdate']);
$type = cleanvar($_REQUEST['type']);
$length = cleanvar($_REQUEST['length']);
$view = cleanvar($_REQUEST['view']);

// there is an existing booking so alter it
if ($approve=='TRUE') $sql = "UPDATE holidays SET approved='1', approvedby='$sit[2]' ";
elseif ($approve=='FALSE') $sql = "UPDATE holidays SET approved='2', approvedby='$sit[2]' "; //decline
else $sql = "UPDATE holidays SET approved='1', approvedby='$sit[2]', type='5' "; // free
$sql .= "WHERE userid='$user' AND startdate='$startdate' AND type='$type' AND length='$length' ";
$result = mysql_query($sql);
## echo $sql;
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
header("Location: holiday_request.php?user=$view&mode=approval");
exit;
?>