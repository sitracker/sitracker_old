<?php
// holiday_request.php - Search contracts
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//


$permission=27; /* View your calendar */
require('db_connect.inc.php');
require('functions.inc.php');
$title="Holiday Request";

// This page requires authentication
require('auth.inc.php');

// External variables
$user = cleanvar($_REQUEST['user']);
$sent = cleanvar($_REQUEST['sent']);
$mode = cleanvar($_REQUEST['mode']);
$approvaluser = cleanvar($_REQUEST['approvaluser']);

include('htmlheader.inc.php');
if (empty($user) || $user=='0') $user=$sit[2];
if (!$sent)
{
    // check to see if this user has approve permission
    $approver=user_permission($sit[2], 50);

    echo "<h2>";
    if ($user=='all') echo "All";
    else echo user_realname($user);
    echo " - Holiday Requests</h2>";

    if ($approver==TRUE AND $mode!='approval') echo "<p align='center'><a href='holiday_request.php?user=all&amp;mode=approval'>Approve holiday requests</a></p>";

    $sql = "SELECT * FROM holidays, holidaytypes WHERE holidays.type=holidaytypes.id AND approved=0 ";
    if ($mode!='approval') $sql.="AND userid='$user' ";
    if ($user!='all' && $approver==TRUE) $sql .= "AND userid='".$user."' ";
    $sql .= "ORDER BY startdate, length";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    if (mysql_num_rows($result)>0)
    {
        echo "<table align='center'>";
        echo "<tr>";
        if ($user=='all' && $approver==TRUE) echo "<th>Name</th>";
        echo "<th>Date</th><th>Length</th><th>Type</th>";
        if ($approver) echo "<th>Approval</th><th>Group Members Away</th>";

        echo "</tr>";
        while ($holiday=mysql_fetch_object($result))
        {
            echo "<tr class='shade2'>";
            if ($user=='all' && $approver==TRUE) echo "<td>".user_realname($holiday->userid)."</td>";
            echo "<td>".date('l j F Y', $holiday->startdate)."</td>";
            echo "<td>";
            if ($holiday->length=='am') echo "Morning Only";
            if ($holiday->length=='pm') echo "Afternoon Only";
            if ($holiday->length=='day') echo "Full Day";
            echo "</td>";
            echo "<td>".$holiday->name."</td>";
            if ($approver==TRUE)
            {
                echo "<td>";
                if ($sit[2]!=$holiday->userid)
                {
                    $approvetext='Approve';
                    if ($holiday->type==2) $approvetext='Acknowledge';
                    echo "<a href=\"holiday_approve.php?approve=TRUE&amp;user={$holiday->userid}&amp;view={$user}&amp;startdate={$holiday->startdate}&amp;type={$holiday->type}&amp;length={$holiday->length}\">{$approvetext}</a> | ";
                    echo "<a href=\"holiday_approve.php?approve=FALSE&amp;user={$holiday->userid}&amp;view={$user}&amp;startdate={$holiday->startdate}&amp;type={$holiday->type}&amp;length={$holiday->length}\">Decline</a>";
                    if ($holiday->type==1) echo "| <a href=\"holiday_approve.php?approve=FREE&amp;user={$holiday->userid}&amp;view={$user}&amp;startdate={$holiday->startdate}&amp;type={$holiday->type}&amp;length={$holiday->length}\">Free Leave</a>";
                }
                else echo "Cannot approve yourself";
                if ($approver==TRUE)
                {
                    echo "<td>";
                    echo check_group_holiday($holiday->userid, $holiday->startdate, $holiday->length);
                    echo "</td>";
                }
                echo "</td>";
            }
            echo "</tr>";

        }
        echo "</table>";
        if (!$mode=='approval')
        {
            echo "<form action='{$_SERVER['PHP_SELF']}' method='post'>";
            echo "<p align='center'>";
            echo "Send the request(s) to: ";
            // extract users
            $sql  = "SELECT id, realname, accepting FROM users, userpermissions ";
            $sql .= "WHERE users.id=userpermissions.userid AND permissionid=50 ORDER BY realname ASC";
            $result = mysql_query($sql);

            echo "<select class='dropdown' name='approvaluser'>";
            if ($id == 0)
            echo "<option selected value='0'>Select A User\n";
            while ($users = mysql_fetch_array($result))
            {
                if($users['id'] != $sit[2])
                {
                    ?><option <?php if ($users["id"] == $id) { ?>selected='selected' <?php } ?>value='<?php echo $users["id"] ?>'>
                    <?php echo $users["realname"]; ?><?php
                }
                echo "</option\n";
            }
            echo "</select>";
            echo "</p>";
            echo "<p align='center'>Send comments with your request: (or leave blank)<br />";
            echo "<textarea name='memo' rows='3' cols='40'></textarea>";
            echo "<input type='hidden' name='user' value='$user'>";
            echo "<input type='hidden' name='sent' value='true'><br /><br />";
            echo "<input type='submit' name='submit' value='submit'>";
            echo "</p>";
            echo "</form>";
        }
    }
    else
    {
        echo "<p class='info'>You have no holidays that are booked but not yet approved</p>";
    }
}
else
{
    if (empty($approvaluser)) echo "<p class='error'>Error: You did not select a user to send the request to</p>";
    else
    {
    $sql = "SELECT * FROM holidays, holidaytypes WHERE holidays.type=holidaytypes.id AND approved=0 ";
    if ($user!='all' || $approver==FALSE) $sql .= "AND userid='".$sit[2]."' ";
    $sql .= "ORDER BY startdate, length";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    if (mysql_num_rows($result)>0)
    {
        // FIXME this email should probably use the email template system
        $bodytext = "Message from {$CONFIG['application_shortname']}: ".user_realname($user)." has requested that you approve the following holidays:\n\n";
        while ($holiday=mysql_fetch_object($result))
        {
        $bodytext .= date('l j F Y', $holiday->startdate).", ";
        if ($holiday->length=='am') $bodytext .= "Morning Only";
        if ($holiday->length=='pm') $bodytext .= "Afternoon Only";
        if ($holiday->length=='day') $bodytext .= "Full Day";
        $bodytext .= ", ";
        $bodytext .= $holiday->name."\n";
        }
        $bodytext .= "\n";
        if (strlen($memo)>3)
        {
        $bodytext .= "The following comments were sent with the request:\n\n";
        $bodytext .= "---\n$memo\n---\n";
        }
        $bodytext .= "Please point your browser to\n<https://{$_SERVER['HOSTNAME']}/holiday_request.php?user={$user}&mode=approval>\n ";
        $bodytext .= "to approve or decline these requests.";
    }
    echo "<p align='center'>Your request has been sent</p>";
    }
$email_from = user_email($user);
$email_to = user_email($approvaluser);
$email_subject = "{$CONFIG['application_shortname']}: Holiday Approval Request";
$extra_headers  = "From: $email_from\nReply-To: $email_replyto\nErrors-To: {$CONFIG['support_email']}\n";
$extra_headers .= "X-Mailer: {$CONFIG['application_shortname']} {$application_version_string}/PHP " . phpversion()."\n";

$rtnvalue = mail($email_to, stripslashes($email_subject), stripslashes($bodytext), $extra_headers);
    echo "<p align='center'><a href='holiday_calendar.php?type=1&user=$user'>Back to your calendar</p></p>";
}
include('htmlfooter.inc.php');
?>