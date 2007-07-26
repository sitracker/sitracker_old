<?php
// holiday_request.php - Search contracts
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
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
$action = cleanvar($_REQUEST['action']);
$type = cleanvar($_REQUEST['type']);
$memo = cleanvar($_REQUEST['memo']);
$approvaluser = cleanvar($_REQUEST['approvaluser']);

include('htmlheader.inc.php');
if (empty($user)) $user=$sit[2];
if (!$sent)
{
    // check to see if this user has approve permission
    $approver=user_permission($sit[2], 50);

    $waiting=FALSE;
    echo "<h2>";
    if ($user=='all') echo "All";
    else echo user_realname($user,TRUE);
    echo " - Holiday Requests</h2>";

    if ($approver==TRUE AND $mode!='approval' AND $user==$sit[2]) echo "<p align='center'><a href='holiday_request.php?user=all&amp;mode=approval'>Approve holiday requests</a></p>";
    if ($approver==TRUE AND $mode=='approval' AND $user!='all') echo "<p align='center'><a href='holiday_request.php?user=all&amp;mode=approval'>Show all requests</a></p>";

    $sql = "SELECT * FROM holidays, holidaytypes WHERE holidays.type=holidaytypes.id AND approved=0 ";
    if (!empty($type)) $sql .= "AND type='$type' ";
    if ($mode!='approval' || $user!='all') $sql.="AND userid='$user' ";
    if ($approver==TRUE && $mode=='approval') $sql .= "AND approvedby={$sit[2]} ";
    $sql .= "ORDER BY startdate, length";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    if (mysql_num_rows($result)>0)
    {
        echo "<table align='center'>";
        echo "<tr>";
        if ($user=='all' && $approver==TRUE) echo "<th>Name</th>";
        echo "<th>Date</th><th>Length</th><th>Type</th>";
        if ($approver AND $mode=='approval') echo "<th>Approval</th><th>Group Members Away</th>";
        else echo "<th>Status</th>";

        echo "</tr>";
        while ($holiday=mysql_fetch_object($result))
        {
            echo "<tr class='shade2'>";
            if ($user=='all' && $approver==TRUE)
            {
                echo "<td><a href='{$_SERVER['PHP_SELF']}?user={$holiday->userid}&amp;mode=approval'>";
                echo user_realname($holiday->userid,TRUE);
                echo "</a></td>";
            }
            echo "<td>".date('l j F Y', $holiday->startdate)."</td>";
            echo "<td>";
            if ($holiday->length=='am') echo "Morning";
            if ($holiday->length=='pm') echo "Afternoon";
            if ($holiday->length=='day') echo "Full Day";
            echo "</td>";
            echo "<td>".$holiday->name."</td>";
            if ($approver==TRUE)
            {
                if ($sit[2]!=$holiday->userid AND $mode=='approval')
                {
                    echo "<td>";
                    $approvetext='Approve';
                    if ($holiday->type==2) $approvetext='Acknowledge';
                    echo "<a href=\"holiday_approve.php?approve=TRUE&amp;user={$holiday->userid}&amp;view={$user}&amp;startdate={$holiday->startdate}&amp;type={$holiday->type}&amp;length={$holiday->length}\">{$approvetext}</a> | ";
                    echo "<a href=\"holiday_approve.php?approve=FALSE&amp;user={$holiday->userid}&amp;view={$user}&amp;startdate={$holiday->startdate}&amp;type={$holiday->type}&amp;length={$holiday->length}\">Decline</a>";
                    if ($holiday->type==1) echo " | <a href=\"holiday_approve.php?approve=FREE&amp;user={$holiday->userid}&amp;view={$user}&amp;startdate={$holiday->startdate}&amp;type={$holiday->type}&amp;length={$holiday->length}\">Free Leave</a>";
                    echo "</td>";
                }
                else
                {
                    echo "<td>";
                    if ($holiday->approvedby > 0) echo "Request sent to ".user_realname($holiday->approvedby,TRUE);
                    else
                    {
                        echo "Request not sent";
                        $waiting=TRUE;
                    }
                    echo "</td>";
                }
                if ($approver==TRUE AND $mode=='approval')
                {
                    echo "<td>";
                    echo check_group_holiday($holiday->userid, $holiday->startdate, $holiday->length);
                    echo "</td>";
                }
            }
            echo "</tr>";
        }
        echo "</table>";
        if ($mode=='approval') echo "<p align='center'><a href='holiday_approve.php?approve=TRUE&amp;user=$user&amp;view=$user&amp;startdate=all&amp;type=all'>Approve all</a></p>";
        else
        {
            // extract users (only show users with permission to approve that are not disabled accounts)
            $sql  = "SELECT DISTINCT id, realname, accepting FROM users, userpermissions, rolepermissions ";
            $sql .= "WHERE users.id=userpermissions.userid AND users.roleid=rolepermissions.roleid ";
            $sql .= "AND (userpermissions.permissionid=50 AND userpermissions.granted='true' OR ";
            $sql .= "rolepermissions.permissionid=50 AND rolepermissions.granted='true') ";
            $sql .= "AND users.id != {$sit[2]} AND users.status > 0 ORDER BY realname ASC";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
            $numapprovers = mysql_num_rows($result);
            if ($numapprovers > 0)
            {
                echo "<form action='{$_SERVER['PHP_SELF']}' method='post'>";
                echo "<p align='center'>";
                echo "Send the request(s) to: ";
                echo "<select name='approvaluser'>";
                echo "<option selected='selected' value='0'>Select A User</option>\n";
                while ($users = mysql_fetch_array($result))
                {
                    echo "<option";
                    if ($users['id'] == $id) echo " selected='selected'";
                    echo " value='{$users['id']}'";
                    echo ">{$users['realname']}</option>\n";
                }
                echo "</select>";
                echo "</p>";

                // Force resend if there are no new additions to be requested
                if ($waiting==FALSE AND $action!='resend') $action='resend';
                echo "<input type='hidden' name='action' value='$action' />";
                echo "<p align='center'>Send comments with your request: (or leave blank)<br />";
                echo "<textarea name='memo' rows='3' cols='40'></textarea>";
                echo "<input type='hidden' name='user' value='$user' />";
                echo "<input type='hidden' name='sent' value='true' /><br /><br />";
                echo "<input type='submit' name='submit' value='Send Request' />";
                echo "</p>";
                echo "</form>";
            }
            else echo "<p class='error'>There are no users that can approve your request, only users with appropiate permissions can approve holiday requests and you cannot approve your own requests.</p>";
        }
    }
    else
    {
        echo "<p class='info'>There are currently no holidays waiting for your approval</p>";
    }
}
else
{
    if (empty($approvaluser)) echo "<p class='error'>Error: You did not select a user to send the request to</p>";
    else
    {
        $sql = "SELECT * FROM holidays, holidaytypes WHERE holidays.type=holidaytypes.id AND approved=0 ";
        if ($action!='resend') $sql .= "AND approvedby=0 ";
        if ($user!='all' || $approver==FALSE) $sql .= "AND userid='{$user}' ";
        $sql .= "ORDER BY startdate, length";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
        if (mysql_num_rows($result)>0)
        {
            // FIXME this email should probably use the email template system
            $bodytext = "Message from {$CONFIG['application_shortname']}: ".user_realname($user)." has requested that you approve the following holidays:\n\n";
            while ($holiday=mysql_fetch_object($result))
            {
                $holidaylist .= date('l j F Y', $holiday->startdate).", ";
                if ($holiday->length=='am') $holidaylist .= "Morning";
                if ($holiday->length=='pm') $holidaylist .= "Afternoon";
                if ($holiday->length=='day') $holidaylist .= "Full Day";
                $holidaylist .= ", ";
                $holidaylist .= $holiday->name."\n";
            }
            $bodytext .= "$holidaylist\n";
            if (strlen($memo)>3)
            {
                $bodytext .= "The following comments were sent with the request:\n\n";
                $bodytext .= "---\n$memo\n---\n\n";
            }
            $url = parse_url($_SERVER['HTTP_REFERER']);
            $approveurl = "{$url['scheme']}://{$url['host']}{$url['path']}";
            $bodytext .= "Please point your browser to\n<{$approveurl}?user={$user}&mode=approval>\n ";
            $bodytext .= "to approve or decline these requests.";
        }
        // Mark the userid of the person who will approve the request so that they can see them
        $sql = "UPDATE holidays SET approvedby='{$approvaluser}' WHERE userid='{$user}' AND approved=0";
        mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        $email_from = user_email($user);
        $email_to = user_email($approvaluser);
        $email_subject = "{$CONFIG['application_shortname']}: Holiday Approval Request";
        $extra_headers  = "From: $email_from\nReply-To: $email_from\nErrors-To: {$CONFIG['support_email']}\n";
        $extra_headers .= "X-Mailer: {$CONFIG['application_shortname']} {$application_version_string}/PHP " . phpversion()."\n";
        $extra_headers .= "X-Originating-IP: {$_SERVER['REMOTE_ADDR']}\n";
        $rtnvalue = mail($email_to, stripslashes($email_subject), stripslashes($bodytext), $extra_headers);

        if ($rtnvalue===TRUE)
        {
            echo "<p align='center'>Your request has been sent</p>";
            echo "<p align='center'>".nl2br($holidaylist)."</p>";
        }
        else echo "<p class='error'>There was a problem sending your request</p>";
    }
    echo "<p align='center'><a href='holidays.php?user={$user}'>Back to holidays page</p></p>";
}
include('htmlfooter.inc.php');
?>
