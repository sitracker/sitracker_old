<?php
// email_incident.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

$permission=33; // Send Emails
require('db_connect.inc.php');
require('functions.inc.php');
include('mime.inc.php');

// This page requires authentication
require('auth.inc.php');

// External variables
$step = cleanvar($_REQUEST['step']);
$id = cleanvar($_REQUEST['id']);
$incidentid=$id;

$title = 'Email';

if (empty($step)) $step=1;

switch ($step)
{
    case 1:
        // show form 1
        include('incident_html_top.inc.php');
        ?>
        <script type="text/javascript">
        <!--
        function deleteOption(object) {
            var Current = object.updatetype.selectedIndex;
            object.updatetype.options[Current] = null;
        }

        function notarget(object)
        {
            // remove last option
            var length = object.updatetype.length;
            if (length > 6)
            {
                object.updatetype.selectedIndex=6;
                var Current = object.updatetype.selectedIndex;
                object.updatetype.options[Current] = null;
            }
            object.priority.value=object.storepriority.value;
            object.priority.disabled=true;
            object.updatetype.selectedIndex=0;
            object.updatetype.disabled=false;
        }


        function initialresponse(object)
        {
            // remove last option
            var length = object.updatetype.length;
            if (length > 6)
            {
                object.updatetype.selectedIndex=6;
                var Current = object.updatetype.selectedIndex;
                object.updatetype.options[Current] = null;
            }
            object.priority.value=object.storepriority.value;
            object.priority.disabled=true;
            object.updatetype.selectedIndex=0;
            object.updatetype.disabled=false;
        }


        function actionplan(object)
        {
            // remove last option
            var length = object.updatetype.length;
            if (length > 6)
            {
                object.updatetype.selectedIndex=6;
                var Current = object.updatetype.selectedIndex;
                object.updatetype.options[Current] = null;
            }

            var defaultSelected = true;
            var selected = true;
            var optionName = new Option('Action Plan', 'actionplan', defaultSelected, selected)
            var length = object.updatetype.length;
            object.updatetype.options[length] = optionName;
            object.priority.value=object.storepriority.value;
            object.priority.disabled=true;
            object.updatetype.disabled=true;
        }

        function reprioritise(object)
        {
            // remove last option
            var length = object.updatetype.length;
            if (length > 6)
            {
                object.updatetype.selectedIndex=6;
                var Current = object.updatetype.selectedIndex;
                object.updatetype.options[Current] = null;
            }
            // add new option
            var defaultSelected = true;
            var selected = true;
            var optionName = new Option('Reprioritise', 'solution', defaultSelected, selected)
            var length = object.updatetype.length;
            object.updatetype.options[length] = optionName;
            object.priority.disabled=false;
            object.updatetype.disabled=true;
        }

        function probdef(object)
        {
            // remove last option
            var length = object.updatetype.length;
            if (length > 6)
            {
                object.updatetype.selectedIndex=6;
                var Current = object.updatetype.selectedIndex;
                object.updatetype.options[Current] = null;
            }

            var defaultSelected = true;
            var selected = true;
            var optionName = new Option('Problem Definition', 'probdef', defaultSelected, selected)
            var length = object.updatetype.length;
            object.updatetype.options[length] = optionName;
            object.priority.value=object.storepriority.value;
            object.priority.disabled=true;
            object.updatetype.disabled=true;
        }

        function replaceOption(object) {
            var Current = object.updatetype.selectedIndex;
            object.updatetype.options[Current].text = object.currentText.value;
            object.updatetype.options[Current].value = object.currentText.value;
        }
        //-->
        </script>
        <h2>Send Incident Email</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $id ?>" name="updateform" method="post">
        <table align='center' class='vertical'>
        <tr><th>Use Template:</th><td><?php echo emailtype_drop_down("emailtype", 1); ?></td></tr>
        <tr><th>Does this email meet an SLA target?:</th><td>
        <?php
        $target = incident_get_next_target($id);
        echo "<select name='target' class='dropdown'>\n";
        echo "<option value='none' onclick='notarget(this.form)'>No</option>\n";
        switch ($target->type)
        {
            case 'initialresponse':
                echo "<option value='initialresponse' onclick='initialresponse(this.form)' >Initial Response</option>\n";
                echo "<option value='probdef' onclick='probdef(this.form)'>Problem Definition</option>\n";
                echo "<option value='actionplan' onclick='actionplan(this.form)'>Action Plan</option>\n";
                echo "<option value='solution' onclick='reprioritise(this.form)'>Resolution/Reprioritisation</option>\n";
            break;
            case 'probdef':
                echo "<option value='probdef' onclick='probdef(this.form)'>Problem Definition</option>\n";
                echo "<option value='actionplan' onclick='actionplan(this.form)'>Action Plan</option>\n";
                echo "<option value='solution' onclick='reprioritise(this.form)'>Resolution/Reprioritisation</option>\n";
            break;
            case 'actionplan':
                echo "<option value='actionplan' onclick='actionplan(this.form)'>Action Plan</option>\n";
                echo "<option value='solution' onclick='reprioritise(this.form)'>Resolution/Reprioritisation</option>\n";
            break;
                case 'solution':
                echo "<option value='solution' onclick='reprioritise(this.form)'>Resolution/Reprioritisation</option>\n";
            break;
        }
        echo "</select>\n";
        ?>
        </td></tr>
        <tr><th>New Incident Status:</th><td><?php echo incidentstatus_drop_down("newincidentstatus", incident_status($id)); ?></td></tr>
        <tr><th>Time To Next Action:<br />Or date:</th>
        <td>
        <input type="radio" name="timetonextaction_none" value="none" checked='checked' />None<br />
        <input type="radio" name="timetonextaction_none" value="time" />In <em>x</em> days, hours, minutes<br />&nbsp;&nbsp;&nbsp;
        <input maxlength="3" name="timetonextaction_days" onclick="window.document.updateform.timetonextaction_none[1].checked = true;" size="3" /> Days&nbsp;
        <input maxlength="2" name="timetonextaction_hours" onclick="window.document.updateform.timetonextaction_none[1].checked = true;" size="3" /> Hours&nbsp;
        <input maxlength="2" name="timetonextaction_minutes" onclick="window.document.updateform.timetonextaction_none[1].checked = true;" size="3" /> Minutes<br />
        <input type="radio" name="timetonextaction_none" value="date">On specified Date<br />&nbsp;&nbsp;&nbsp;
        <?php
        // Print Listboxes for a date selection
        ?><select name='day' onclick="window.document.updateform.timetonextaction_none[2].checked = true;"><?php
        for ($t_day=1;$t_day<=31;$t_day++)
        {
            echo "<option value=\"$t_day\" ";
            if ($t_day==date("j"))
            {
                echo "selected='selected'";
            }
            echo ">$t_day</option>\n";
        }
        ?></select><select name='month' onclick="window.document.updateform.timetonextaction_none[2].checked = true;"><?php
        for ($t_month=1;$t_month<=12;$t_month++)
        {
            echo "<option value=\"$t_month\"";
            if ($t_month==date("n"))
            {
                echo " selected='selected'";
            }
            echo ">". date ("F", mktime(0,0,0,$t_month,1,2000)) ."</option>\n";
        }
        ?></select>
        <select name='year' onclick="window.document.updateform.timetonextaction_none[2].checked = true;"><?php
        for ($t_year=(date("Y")-1);$t_year<=(date("Y")+5);$t_year++)
        {
            echo "<option value=\"$t_year\" ";
            if ($t_year==date("Y"))
            {
                echo " selected='selected'";
            }
            echo ">$t_year</option>\n";
        }
        ?></select>
        </td></tr>
        </table>
        <p align='center'>
        <input type='hidden' name='step' value='2' />
        <?php echo "<input type='hidden' name='menu' value='$menu' />"; ?>
        <input name="submit1" type="submit" value="Continue" /></p>
        </form>
        <?php
        include('incident_html_bottom.inc.php');
    break;

    case 2:
        // show form 2
        include('incident_html_top.inc.php');
        ?>
        <script type='text/javascript'>
        function confirm_send_mail()
        {
            return window.confirm('Are you sure you want to send this email?');
        }
        </script>
        <?php
        // External vars
        $emailtype = cleanvar($_REQUEST['emailtype']);
        $newincidentstatus = cleanvar($_REQUEST['newincidentstatus']);
        $timetonextaction_none = cleanvar($_REQUEST['timetonextaction_none']);
        $timetonextaction_days = cleanvar($_REQUEST['timetonextaction_days']);
        $timetonextaction_hours = cleanvar($_REQUEST['timetonextaction_hours']);
        $timetonextaction_minutes = cleanvar($_REQUEST['timetonextaction_minutes']);
        $day = cleanvar($_REQUEST['day']);
        $month = cleanvar($_REQUEST['month']);
        $year = cleanvar($_REQUEST['year']);
        $target = cleanvar($_REQUEST['target']);

        if ($emailtype == 0)
        {
            echo "<p class='error'>You must select an email type</p>\n";
        }
        else
        {
            // encoding is multipart/form-data again as it no longer works without (why was this disabled?) - TPG 13/08/2002
            ?>
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>?id=<?php echo $id ?>" method="post" enctype="multipart/form-data" onsubmit="return confirm_send_mail()" >
            <table align='center' class='vertical' width='95%'>
            <tr><th width='30%'>From:</th><td><input maxlength='100' name="fromfield" size='40' value="<?php echo emailtype_replace_specials(emailtype_from($emailtype), $id, $sit[2]) ?>" /></td></tr>
            <tr><th>Reply To:</th><td><input maxlength='100' name="replytofield" size='40' value="<?php echo emailtype_replace_specials(emailtype_replyto($emailtype), $id, $sit[2]) ?>" /></td></tr>
            <tr><th>CC:</th><td><input maxlength='100' name="ccfield" size='40' value="<?php echo emailtype_replace_specials(emailtype_cc($emailtype), $id, $sit[2]) ?>" /></td></tr>
            <tr><th>BCC:</th><td><input maxlength='100' name="bccfield" size='40' value="<?php echo emailtype_replace_specials(emailtype_bcc($emailtype), $id, $sit[2]) ?>" /></td></tr>
            <tr><th>To:</th><td><input maxlength='100' name="tofield" size='40' value="<?php echo emailtype_replace_specials(emailtype_to($emailtype), $id, $sit[2]) ?>" /></td></tr>
            <tr><th>Subject:</th><td><input maxlength='255' name="subjectfield" size='40' value="<?php echo emailtype_replace_specials(emailtype_subject($emailtype), $id, $sit[2]) ?>" /></td></tr>
            <tr><th>Attachment
            <?php
            // calculate filesize
            $j = 0;
            $ext =
            array("Bytes","KBytes","MBytes","GBytes","TBytes");
            $file_size = $CONFIG['upload_max_filesize'];
            while ($file_size >= pow(1024,$j)) ++$j;
            $file_size = round($file_size / pow(1024,$j-1) * 100) / 100 . ' ' . $ext[$j-1];
            echo "(&lt; $file_size)";
            ?>
            :</th><td>
            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $CONFIG['upload_max_filesize'] ?>" />
            <input type="file" name="attachment" size="40" maxfilesize="<?php echo $CONFIG['upload_max_filesize'] ?>" />
            </td></tr>
            <tr><th>Message:</th><td>
            <textarea name="bodytext" rows="20" cols="65"><?php
            // Attempt to restore email body from session in case there was an error submitting previously
            if (!empty($_SESSION['temp-emailbody'])) echo $_SESSION['temp-emailbody'];
            else echo emailtype_replace_specials(emailtype_body($emailtype), $id, $sit[2])
            ?></textarea>
            </td></tr>
            <?php
            if ($CONFIG['enable_spellchecker']==TRUE) echo "<tr><th>&nbsp;</th><td><input type='checkbox' name='spellcheck' value='yes' /> Check Spelling before sending</td></tr>\n";
            ?>
            </table>
            <p align='center'>
            <input name="newincidentstatus" type="hidden" value="<?php echo $newincidentstatus; ?>" />
            <input name="timetonextaction_none" type="hidden" value="<?php echo $timetonextaction_none; ?>" />
            <input name="timetonextaction_days" type="hidden" value="<?php echo $timetonextaction_days; ?>" />
            <input name="timetonextaction_hours" type="hidden" value="<?php echo $timetonextaction_hours; ?>" />
            <input name="timetonextaction_minutes" type="hidden" value="<?php echo $timetonextaction_minutes; ?>" />
            <input name="day" type="hidden" value="<?php echo $day; ?>" />
            <input name="month" type="hidden" value="<?php echo $month; ?>" />
            <input name="year" type="hidden" value="<?php echo $year; ?>" />
            <input name="target" type="hidden" value="<?php echo $target; ?>" />
            <input type="hidden" name="step" value="3" />
            <input type="hidden" name="emailtype" value="<?php echo $emailtype; ?>" />
            <input name="submit2" type="submit" value="Send Email" />
            </p>
            </form>
            <?php
        }
        include('incident_html_bottom.inc.php');
    break;

    case 3:
        // show form 3 (spellcheck) or send email and update incident

        // External variables
        $bodytext = $_REQUEST['bodytext'];
        $tofield = cleanvar($_REQUEST['tofield']);
        $fromfield = cleanvar($_REQUEST['fromfield']);
        $replytofield = cleanvar($_REQUEST['replytofield']);
        $ccfield = cleanvar($_REQUEST['ccfield']);
        $bccfield = cleanvar($_REQUEST['bccfield']);
        $subjectfield = cleanvar($_REQUEST['subjectfield']);
        $emailtype = cleanvar($_REQUEST['emailtype']);
        $newincidentstatus = cleanvar($_REQUEST['newincidentstatus']);
        $timetonextaction_none = cleanvar($_REQUEST['timetonextaction_none']);
        $timetonextaction_days = cleanvar($_REQUEST['timetonextaction_days']);
        $timetonextaction_hours = cleanvar($_REQUEST['timetonextaction_hours']);
        $timetonextaction_minutes = cleanvar($_REQUEST['timetonextaction_minutes']);
        $day = cleanvar($_REQUEST['day']);
        $month = cleanvar($_REQUEST['month']);
        $year = cleanvar($_REQUEST['year']);
        $target = cleanvar($_REQUEST['target']);

        // move attachment to a safe place for processing later
        if ($_FILES['attachment']['name']!='')       // Should be using this format throughout TPG 13/08/2002
        {
            if (!isset($filename)) $filename = $CONFIG['attachment_fspath'].$_FILES['attachment']['name'];
            $mv=move_uploaded_file($_FILES['attachment']['tmp_name'], "$filename");    // Added tmp_name TPG 13/08/2002
            if (!mv) throw_error('!Error: Problem moving attachment from temp directory:',$filename);
            $attachmenttype = $_FILES['attachment']['type'];
        }
        // spellcheck email if required
        if ($spellcheck == 'yes')
        {
            include('spellcheck_email.php');
            exit;
        }
        if ($encoded=='yes')



        $errors = 0;
        // check to field
        if ($tofield == '')
        {
            $errors = 1;
            $error_string .= "<p class='error'>You must enter a recipient in the 'To' field</p>\n";
        }
        // check from field
        if ($fromfield == "")
        {
            $errors = 1;
            $error_string .= "<p class='error'>You must complete the 'From' field</p>\n";
        }
        // check reply to field
        if ($replytofield == "")
        {
            $errors = 1;
            $error_string .= "<p class='error'>You must complete the 'Reply To' field</p>\n";
        }
        // Store email body in session if theres been an error
        if ($errors > 0) $_SESSION['temp-emailbody'] = $bodytext;
        else unset($_SESSION['temp-emailbody']);

        // send email if no errors
        if ($errors == 0)
        {
            $extra_headers = "Reply-To: $replytofield\nErrors-To: ".user_email($sit[2])."\n";
            $extra_headers .= "X-Mailer: {$CONFIG['application_shortname']} {$application_version_string}/PHP " . phpversion() . "\n";
            if ($ccfield != '')  $extra_headers .= "cc: $ccfield\n";
            if ($bccfield != '') $extra_headers .= "Bcc: $bccfield\n";

            $extra_headers .= "\n"; // add an extra crlf to create a null line to separate headers from body
                                // this appears to be required by some email clients - INL

            $mime = new MIME_mail($fromfield, $tofield, stripslashes($subjectfield), stripslashes($bodytext), $extra_headers, $mailerror);

            // check for attachment
            //        if ($_FILES['attachment']['name']!='' || strlen($filename) > 3)
            if ($filename!='' && strlen($filename) > 3)
            {
                //          if (!isset($filename)) $filename = $attachment_fspath.$_FILES['attachment']['name'];   ??? TPG 13/08/2002
                ## bugbug move was here
                if (!file_exists($filename)) throw_error('Error: File did not exist upon processing attachment', $filename);
                if ($filename=='') throw_error('Error: Filename was blank upon processing attachment', $filename);

                // Check file size before sending
                if (filesize($filename) > $CONFIG['upload_max_filesize'] || filesize($filename)==FALSE)
                {
                    throw_error("User Error: Attachment too large or file upload error, filename: $filename,  perms: ".fileperms($filename).", size:",filesize($filename));
                    // throwing an error isn't the nicest thing to do for the user but there seems to be no way of
                    // checking file sizes at the client end before the attachment is uploaded. - INL
                }

                if (preg_match("!/x\-.+!i", $attachmenttype))  $type = OCTET;
                else $type = str_replace("\n","",$attachmenttype);
                $mime -> fattach($filename, "Attachment for incident $id", $type);
            }

            // Lookup the email template (we need this to find out if the update should be visible or not)
            $sql = "SELECT * FROM emailtype WHERE id='$emailtype' ";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            if (mysql_num_rows($result) < 1) trigger_error("Email template '{$meailtype}' not found",E_USER_ERROR);
            $emailtype = mysql_fetch_object($result);

            // actually send the email
            $mailok=$mime -> send_mail();

            // after mail is sent, move the attachment to the incident file attachment directory / timestamp
            if ($filename!="" && file_exists($filename) && $mailok==TRUE)
            {
                // make incident attachment dir if it doesn't exist
                $umask=umask(0000);
                if (!file_exists($CONFIG['attachment_fspath'] . "$id"))
                {
                    $mk=mkdir($CONFIG['attachment_fspath'] ."$id", 0770);
                    if (!$mk) throw_error('Failed creating incident attachment directory after sending mail: ',$CONFIG['attachment_fspath'] .$id);
                }
                $mk=mkdir($CONFIG['attachment_fspath'] .$id . "/$now", 0770);
                if (!$mk) throw_error('Failed creating incident attachment (timestamp) directory after sending mail: ',$CONFIG['attachment_fspath'] .$id . "/$now");
                umask($umask);
                // failes coz renaming file to a directory
                $filename_parts_array=explode('/', $filename);
                $filename_parts_count=count($filename_parts_array)-1;
                $filename_end_part=$filename_parts_array[$filename_parts_count]; // end part of filename (actual name)
                $rn=rename($filename, $CONFIG['attachment_fspath'] . $id . "/$now/" . $filename_end_part);
                if (!rn) throw_error('Failed moving attachment after sending mail: ',$CONFIG['attachment_fspath'] .$id . "/$now");
                // unlink ($filename);  // used to delete the file - don't any more INL 6Nov01
            }

            if ($mailok==FALSE) throw_error('Internal error sending email:','send_mail() failed');

            if ($mailok==TRUE)
            {
                // update incident status if necessary
                switch ($timetonextaction_none)
                {
                    case 'none':
                        $timeofnextaction = 0;
                    break;

                    case 'time':
                        $timeofnextaction = calculate_time_of_next_action($timetonextaction_days, $timetonextaction_hours, $timetonextaction_minutes);
                    break;

                    case 'date':
                        // $now + ($days * 86400) + ($hours * 3600) + ($minutes * 60);
                        $unixdate=mktime(9,0,0,$month,$day,$year);
                        $timeofnextaction = $unixdate;
                        if ($timeofnextaction<0) $timeofnextaction=0;
                    break;

                    default:
                        $timeofnextaction = 0;
                    break;
                }
                if ($newincidentstatus != incident_status($id))
                {
                    $sql = "UPDATE incidents SET status='$newincidentstatus', lastupdated='$now', timeofnextaction='$timeofnextaction' WHERE id='$id'";
                    mysql_query($sql);
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
                    $updateheader = "New Status: <b>" . incidentstatus_name($newincidentstatus) . "</b>\n\n";
                }
                else
                {
                    mysql_query("UPDATE incidents SET lastupdated='$now', timeofnextaction='$timeofnextaction' WHERE id='$id'");
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
                }
                // add update
                $updateheader .= "To: <b>$tofield</b>\nFrom: <b>$fromfield</b>\nReply-To: <b>$replytofield</b>\n";
                if ($ccfield!="") $updateheader .=   "CC: <b>$ccfield</b>\n";
                if ($bccfield!="") $updateheader .= "BCC: <b>$bccfield</b>\n";
                if ($filename!="") $updateheader .= "Attachment: <b>".$filename_end_part."</b>\n";
                $updateheader .= "Subject: <b>$subjectfield</b>\n";

                if (!empty($updateheader)) $updateheader .= "<hr>";
                $updatebody = $updateheader . $bodytext;
                $updatebody=mysql_escape_string($updatebody);
                $sql  = "INSERT INTO updates (incidentid, userid, bodytext, type, timestamp, currentstatus,customervisibility) ";
                $sql .= "VALUES ($id, $sit[2], '$updatebody', 'email', '$now', '$newincidentstatus', '{$emailtype->customervisibility}')";
                mysql_query($sql);
                if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

                // Handle meeting of service level targets
                switch ($target)
                {
                    case 'none':
                        // do nothing
                        $sql = '';
                    break;

                    case 'initialresponse':
                        $sql  = "INSERT INTO updates (incidentid, userid, type, timestamp, currentowner, currentstatus, customervisibility, sla, bodytext) ";
                        $sql .= "VALUES ('$id', '".$sit[2]."', 'slamet', '$now', '".$sit[2]."', '$newincidentstatus', 'show', 'initialresponse','The Initial Response has been made.')";
                    break;

                    case 'probdef':
                        $sql  = "INSERT INTO updates (incidentid, userid, type, timestamp, currentowner, currentstatus, customervisibility, sla, bodytext) ";
                        $sql .= "VALUES ('$id', '".$sit[2]."', 'slamet', '$now', '".$sit[2]."', '$newincidentstatus', 'show', 'probdef','The problem has been defined.')";
                    break;

                    case 'actionplan':
                        $sql  = "INSERT INTO updates (incidentid, userid, type, timestamp, currentowner, currentstatus, customervisibility, sla, bodytext) ";
                        $sql .= "VALUES ('$id', '".$sit[2]."', 'slamet', '$now', '".$sit[2]."', '$newincidentstatus', 'show', 'actionplan','An action plan has been made.')";
                    break;

                    case 'solution':
                        $sql  = "INSERT INTO updates (incidentid, userid, type, timestamp, currentowner, currentstatus, customervisibility, sla, bodytext) ";
                        $sql .= "VALUES ('$id', '".$sit[2]."', 'slamet', '$now', '".$sit[2]."', '$newincidentstatus', 'show', 'solution','The incident has been resolved or reprioritised.\nThe issue should now be brought to a close or a new problem definition created within the service level.')";
                    break;
                }
                if (!empty($sql))
                {
                    mysql_query($sql);
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
                }
                if ($target!='none')
                {
                    // Reset the slaemail sent column, so that email reminders can be sent if the new sla target goes out
                    $sql = "UPDATE incidents SET slaemail='0' WHERE id='$id' LIMIT 1";
                    mysql_query($sql);
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
                }

                journal(CFG_LOGGING_FULL, 'Email Sent', "Email sent subject: $subjectfield, regarding incident $id", CFG_JOURNAL_INCIDENTS, $id);
                ?>
                <html>
                <head></head>
                <script type="text/javascript">
                function confirm_close_window()
                {
                    if (window.confirm('The email was sent successfully, click OK to close this window'))
                    window.close();
                }
                </script>
                <body onLoad="confirm_close_window();">
                </body>
                </html>
                <?php
            }
            else
            {
                include('incident_html_top.inc.php');
                echo "<p class='error'>Error sending email: $mailerror</p>\n";
                include('incident_html_bottom.inc.php');
            }
        }
        else
        {
            // there were errors
            include('incident_html_top.inc.php');
            echo $error_string;
            include('incident_html_bottom.inc.php');
        }
    break;

    default:
        throw_error('Fatal error', "Invalid Step $step");
    break;
} // end switch step
?>