<?php
// move_update.php - Moves an incident from the pending/holding queue
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

@include ('set_include_path.inc.php');
$permission = 8; // Update Incidents
require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');
// External variables
$incidentid = cleanvar($_REQUEST['incidentid']);
$updateid = cleanvar($_REQUEST['updateid']);
$error = cleanvar($_REQUEST['error']);
$send_email = cleanvar($_REQUEST['send_email']);

if ($incidentid=='')
{
    $title = "Move Update $updateid";
    include 'incident_html_top.inc.php';
    echo "<h2>$title</h2>";
    if ($error=='1')
    {
        echo "<p class='error'>Error assigning that incident update. Probable cause is ";
        echo "that no incident exists with that ID or it has been closed.</p>";
    }
    ?>
    <div align='center'>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method='post'>
    To Incident ID: <input type="text" name="incidentid" value="<?php echo $incidentid; ?>" />
    <input type="submit" value="Move" /><br />
    <input type="hidden" name="updateid" value="<?php echo $updateid; ?>" />
    </form>
    </div>
    <?php

    $sql  = "SELECT * FROM `{$dbUpdates}` WHERE id='$updateid' ";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    while ($updates = mysql_fetch_array($result))
    {
        $update_timestamp_string = ldate($CONFIG['dateformat_datetime'], $updates["timestamp"]);
        ?>
        <br />
        <table align='center' width="95%">
        <tr><th>
        <?php
        // Header bar for each update
        switch ($updates['type'])
        {
            case 'opening':
                echo "Opened by <strong>".user_realname($updates['userid'],TRUE)."</strong>";
                if ($updates['customervisibility'] == 'show') echo " (Customer Visible)";
            break;

            case 'reassigning':
                echo "Reassigned by <strong>".user_realname($updates['userid'],TRUE)."</strong>";
                if ($updates['currentowner']!=0)  // only say who it was assigned to if the currentowner field is filled in
                {
                    echo " To <strong>".user_realname($updates['currentowner'],TRUE)."</strong>";
                }
            break;

            case 'email':
                echo "Email Sent by <strong>".user_realname($updates['userid'],TRUE)."</strong>";
                if ($updates['customervisibility'] == 'show') echo " (Customer Visible)";
            break;

            case 'closing':
                echo "Closed by <strong>".user_realname($updates['userid'],TRUE)."</strong>";
                if ($updates['customervisibility'] == 'show') echo " (Customer Visible)";
            break;

            case 'reopening':
                echo "Reopened by <strong>".user_realname($updates['userid'],TRUE)."</strong>";
                if ($updates['customervisibility'] == 'show') echo " (Customer Visible)";
            break;

            case 'phonecallout':
                echo "Call made by <strong>".user_realname($updates['userid'],TRUE)."</strong>";
            break;

            case 'phonecallin':
                echo "Call taken by <strong>".user_realname($updates['userid'],TRUE)."</strong>";
            break;

            case 'research':
                echo "Researched by <strong>".user_realname($updates['userid'],TRUE)."</strong>";
            break;

            case 'webupdate':
                echo "Web Update by <strong>".user_realname($updates['userid'],TRUE)."</strong>";
            break;

            case 'emailout':
                echo "Email sent by <strong>".user_realname($updates['userid'],TRUE)."</strong>";
            break;

            case 'emailin':
                echo "Email received by <strong>".user_realname($updates['userid'],TRUE)."</strong>";
            break;

            case 'externalinfo':
                echo "External info added by <strong>".user_realname($updates['userid'],TRUE)."</strong>";
            break;

            case 'probdef':
                echo "Problem Definition by <strong>".user_realname($updates['userid'],TRUE)."</strong>";
            break;

            case 'solution':
                echo "Final Solution by <strong>".user_realname($updates['userid'],TRUE)."</strong>";
            break;

            default:
                echo "Updated by <strong>".user_realname($updates['userid'],TRUE)."</strong>";
                if ($updates['customervisibility'] == 'show') echo " (Customer Visible)";
            break;
        }
        if ($updates['nextaction']!='') echo " Next Action: <strong>".$updates['nextaction'].'</strong>';

        echo " - ".$update_timestamp_string."</th></tr>";
        echo "<tr><td class='shade2' width='100%'>";
        $updatecounter++;
        echo parse_updatebody($updates['bodytext']);
        ?>
        </td></tr>
        </table>
        <?php
        include ('htmlfooter.inc.php');
    }
}
else
{
    // check that the incident is still open.  i.e. status not = closed
    if (incident_open($incidentid) == $GLOBALS['strYes'])
    {
        // retrieve the update body so that we can insert time headers
        $sql = "SELECT incidentid, bodytext, timestamp FROM `{$dbUpdates}` WHERE id='$updateid'";
        $uresult=mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        list($oldincidentid, $bodytext, $timestamp)=mysql_fetch_row($uresult);
        if ($oldincidentid==0) $oldincidentid='Inbox';
        $prettydate = ldate('r', $timestamp);
        // prepend 'moved' header to bodytext
        $body ="Moved from <b>$oldincidentid</b> -> <b>$incidentid</b> by: <b>".user_realname($sit[2])."</b>\n";
        $body .="Original Message Received at: <b>$prettydate</b>\n";
        $body .= "Status: -&gt; <b>Active</b>\n";
        $bodytext = $body . $bodytext;
        $bodytext = mysql_real_escape_string($bodytext);
        // move the update.
        $sql = "UPDATE updates SET incidentid='$incidentid', userid='$sit[2]', bodytext='$bodytext', timestamp='$now' WHERE id='$updateid'";
        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        // update the incident record, change the incident status to active
        $sql = "UPDATE `{$dbIncidents}` SET status='1', lastupdated='$now', timeofnextaction='0' WHERE id='$incidentid'";
        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        $new_update_id = mysql_insert_id();
        
        //move attachments from updates to incident
        if (!file_exists($CONFIG['attachment_fspath'] ."$incidentid"))
        {
            $umask=umask(0000);
            @mkdir($CONFIG['attachment_fspath'] ."$incidentid", 0770);
            umask($umask);
            //     if (!$mk) throw_error('Failed creating incident directory: ',$incident_attachment_fspath ."$incidentid");
        }
        
        $new_path = $CONFIG['attachment_fspath'] ."$incidentid".$fsdelim."u$new_update_id";
        $update_path=$CONFIG['attachment_fspath'].'updates'.$fsdelim.$updateid;
        if (file_exists($update_path))
        {
            $dh = opendir($update_path);
            while (false !== ($file = readdir($dh)))
            {
                //Don't list subdirectories
                if (!is_dir("$update_path/$file"))
                {
                    if (!rename("$update_path/$file", "$new_path/$file"))
                    {
                        throw_error("Couldn't move file $file");
                    }
                }
            }
        }
        rmdir($update_path);

        //remove from tempincoming to prevent build up
        $sql = "DELETE FROM `{$dbTempIncoming}` WHERE updateid='$updateid'";
        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        journal(CFG_LOGGING_NORMAL, 'Incident Update Moved', "Incident update $update moved to incident $incidentid", CFG_JOURNAL_INCIDENTS, $incidentid);

        html_redirect("review_incoming_updates.php");
    }
    else
    {
        // no open incident with this number.  Return to form.
        header("Location: {$_SERVER['PHP_SELF']}?updateid=$updateid&error=1");
        exit;
    }
}
?>
