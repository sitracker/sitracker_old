<?php
// holiday_approve.php -
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

$permission=50; // Approve Holiday
require('db_connect.inc.php');
require('functions.inc.php');
$title="Holiday Approval";

// This page requires authentication
require('auth.inc.php');

// External variables
$approve = $_REQUEST['approve'];
$startdate = cleanvar($_REQUEST['startdate']);
$type = cleanvar($_REQUEST['type']);
$user = cleanvar($_REQUEST['user']);
$length = cleanvar($_REQUEST['length']);
$view = cleanvar($_REQUEST['view']);

// there is an existing booking so alter it
if ($approve=='TRUE') $sql = "UPDATE holidays SET approved='1', approvedby='$sit[2]' ";
elseif ($approve=='FALSE') $sql = "UPDATE holidays SET approved='2', approvedby='$sit[2]' "; //decline
else $sql = "UPDATE holidays SET approved='1', approvedby='$sit[2]', type='5' "; // free
$sql .= "WHERE userid='$user' ";
if ($startdate!='all') $sql.="AND startdate='$startdate' AND type='$type' AND length='$length' ";
$sql .= "AND approved='0'";
$result = mysql_query($sql);

$bodytext = "Message from {$CONFIG['application_shortname']}: ".$sit[2]." has ";
if ($approve=='FALSE') $bodytext.="rejected";
else $bodytext.="approved";
echo " the following holidays:\n\n";
    
    $bodytext .= "\n";
}
$email_from = user_email($sit[2]);
$email_to = user_email($user);
$email_subject = "Re: {$CONFIG['application_shortname']}: Holiday Approval Request";
$extra_headers  = "From: $email_from\nReply-To: $email_from\nErrors-To: {$CONFIG['support_email']}\n";
$extra_headers .= "X-Mailer: {$CONFIG['application_shortname']} {$application_version_string}/PHP " . phpversion()."\n";
$rtnvalue = mail($email_to, stripslashes($email_subject), stripslashes($bodytext), $extra_headers);

if ($rtnvalue===TRUE) echo "<p align='center'>".user_realname($user)." has been notified of your decision</p>";
else echo "<p class='error'>There was a problem sending your notification</p>";

plugin_do('holiday_ack');

if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
header("Location: holiday_request.php?user=$view&mode=approval");
exit;
?>