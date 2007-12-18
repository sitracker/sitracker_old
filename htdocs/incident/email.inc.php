<?php
// email.inc.php - Displays a page for email from an incident
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Included by ../incident.php

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}

//if (empty($step)) $step=1;

if (empty($step))
{
    $action = $_REQUEST['action'];

    if ($action == "deletedraft")
    {
        if ($draftid != -1)
        {
            $sql = "DELETE FROM drafts WHERE id = {$draftid}";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        }
        html_redirect("email_incident.php?id={$id}");
        exit;
    }

    $sql = "SELECT * FROM drafts WHERE type = 'email' AND userid = '{$sit[2]}' AND incidentid = '{$id}'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

    if (mysql_num_rows($result) > 0)
    {
        include ('incident_html_top.inc.php');

        echo "<h2>{$title}</h2>";

        display_drafts('email', $result);

        echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=1&amp;id={$id}'>{$strNewEmail}</a></p>";

        include ('incident_html_bottom.inc.php');

        exit;
    }
    else
    {
        $step = 1;
    }
}

switch ($step)
{
    case 1:
        // show form 1
        include ('incident_html_top.inc.php');
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
        echo "<option value='none' onclick='notarget(this.form)'>{$strNo}</option>\n";
        switch ($target->type)
        {
            case 'initialresponse':
                echo "<option value='initialresponse' style='text-indent: 15px; height: 17px; background-image: url({$CONFIG['application_webpath']}/images/icons/{$iconset}/16x16/initialresponse.png); background-repeat: no-repeat;' onclick='initialresponse(this.form)' >{$strInitialResponse}</option>\n";
                echo "<option value='probdef' style='text-indent: 15px; height: 17px; background-image: url({$CONFIG['application_webpath']}/images/icons/{$iconset}/16x16/probdef.png); background-repeat: no-repeat;' onclick='probdef(this.form)'>{$strProblemDefinition}</option>\n";
                echo "<option value='actionplan' style='text-indent: 15px; height: 17px; background-image: url({$CONFIG['application_webpath']}/images/icons/{$iconset}/16x16/actionplan.png); background-repeat: no-repeat;' onclick='actionplan(this.form)'>{$strActionPlan}</option>\n";
                echo "<option value='solution' style='text-indent: 15px; height: 17px; background-image: url({$CONFIG['application_webpath']}/images/icons/{$iconset}/16x16/solution.png); background-repeat: no-repeat;' onclick='reprioritise(this.form)'>{$strResolutionReprioritisation}</option>\n";
            break;
            case 'probdef':
                echo "<option value='probdef' style='text-indent: 15px; height: 17px; background-image: url({$CONFIG['application_webpath']}/images/icons/{$iconset}/16x16/probdef.png); background-repeat: no-repeat;' onclick='probdef(this.form)'>{$strProblemDefinition}</option>\n";
                echo "<option value='actionplan' style='text-indent: 15px; height: 17px; background-image: url({$CONFIG['application_webpath']}/images/icons/{$iconset}/16x16/actionplan.png); background-repeat: no-repeat;' onclick='actionplan(this.form)'>{$strActionPlan}</option>\n";
                echo "<option value='solution' style='text-indent: 15px; height: 17px; background-image: url({$CONFIG['application_webpath']}/images/icons/{$iconset}/16x16/solution.png); background-repeat: no-repeat;' onclick='reprioritise(this.form)'>{$strResolutionReprioritisation}</option>\n";
            break;
            case 'actionplan':
                echo "<option value='actionplan' style='text-indent: 15px; height: 17px; background-image: url({$CONFIG['application_webpath']}/images/icons/{$iconset}/16x16/actionplan.png); background-repeat: no-repeat;' onclick='actionplan(this.form)'>{$strActionPlan}</option>\n";
                echo "<option value='solution' style='text-indent: 15px; height: 17px; background-image: url({$CONFIG['application_webpath']}/images/icons/{$iconset}/16x16/solution.png); background-repeat: no-repeat;' onclick='reprioritise(this.form)'>{$strResolutionReprioritisation}</option>\n";
            break;
                case 'solution':
                echo "<option value='solution' style='text-indent: 15px; height: 17px; background-image: url({$CONFIG['application_webpath']}/images/icons/{$iconset}/16x16/solution.png); background-repeat: no-repeat;' onclick='reprioritise(this.form)'>{$strResolutionReprioritisation}</option>\n";
            break;
        }
        echo "</select>\n</td></tr>";

        if ($CONFIG['auto_chase'] == TRUE)
        {
            $sql = "SELECT * FROM updates WHERE incidentid = {$id} ORDER BY timestamp DESC LIMIT 1";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

            $obj = mysql_fetch_object($result);

            if ($obj->type == 'auto_chase_phone')
            {
                echo "<tr><th>{$strCustomerChaseUpdate}</th><td>";
                echo "<input type='radio' name='chase_customer' value='no' checked='yes' />{$strNo} ";
                echo "<input type='radio' name='chase_customer' value='yes' />{$strYes}";
                echo "</td></tr>";
            }

            if ($obj->type == 'auto_chase_manager')
            {
                echo "<tr><th>{$strManagerChaseUpdate}</th>";
                echo "<input type='radio' name='chase_manager' value='no' checked='yes' />{$strNo} ";
                echo "<input type='radio' name='chase_manager' value='yes' />{$strYes}";
                echo "</td></tr>";
            }
        }

        echo "<tr><th>{$strNewIncidentStatus}:</th><td>";
        echo incidentstatus_drop_down("newincidentstatus", incident_status($id));
        echo "</td></tr>\n";
        echo "<tr><th>{$strTimeToNextAction}:</th>";
        echo "<td>";
        echo "<input type='radio' name='timetonextaction_none' value='none' checked='checked' />None<br />";
        echo "<input type='radio' name='timetonextaction_none' value='time' />In <em>x</em> days, hours, minutes<br />&nbsp;&nbsp;&nbsp;";
        echo "<input maxlength='3' name='timetonextaction_days' onclick='window.document.updateform.timetonextaction_none[1].checked = true;' size='3' /> Days&nbsp;";
        echo "<input maxlength='2' name='timetonextaction_hours' onclick='window.document.updateform.timetonextaction_none[1].checked = true;' size='3' /> Hours&nbsp;";
        echo "<input maxlength='2' name='timetonextaction_minutes' onclick='window.document.updateform.timetonextaction_none[1].checked = true;' size='3' /> Minutes<br />";
        echo "<input type='radio' name='timetonextaction_none' value='date' />At specific date and time<br />&nbsp;&nbsp;&nbsp;";
        echo "<input name='date' size='10' value='{$date}' onclick=\"window.document.updateform.timetonextaction_none[2].checked = true;\"/> ";
        echo date_picker('updateform.date');
        echo " <select name='timeoffset' onchange='window.document.updateform.timetonextaction_none[2].checked = true;'>";
        echo "<option value=''></option>";
        echo "<option value='0'>8:00 AM</option>";
        echo "<option value='1'>9:00 AM</option>";
        echo "<option value='2'>10:00 AM</option>";
        echo "<option value='3'>11:00 AM</option>";
        echo "<option value='4'>12:00 PM</option>";
        echo "<option value='5'>1:00 PM</option>";
        echo "<option value='6'>2:00 PM</option>";
        echo "<option value='7'>3:00 PM</option>";
        echo "<option value='8'>4:00 PM</option>";
        echo "<option value='9'>5:00 PM</option>";
        echo "</select>";
        echo "<br />";
        echo "</td></tr>";
        echo "</table>";
        echo "<p align='center'>";
        echo "<input type='hidden' name='step' value='2' />";
        echo "<input type='hidden' name='menu' value='$menu' />";
        echo "<input name='submit1' type='submit' value='{$strContinue}' /></p>";
        echo "</form>\n";
        include ('incident_html_bottom.inc.php');
    break;

    case 2:
        // show form 2
        if ($draftid != -1)
        {
            $draftsql = "SELECT * FROM drafts WHERE id = {$draftid}";
            $draftresult = mysql_query($draftsql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
            $draftobj = mysql_fetch_object($draftresult);

            $metadata = explode("|",$draftobj->meta);
        }

        include ('incident_html_top.inc.php');
        ?>
        <script type='text/javascript'>
        function confirm_send_mail()
        {
            return window.confirm('Are you sure you want to send this email?');
        }

        function urlencode(str) {
            str = escape(str);
            str = str.replace('+', '%2B');
            str = str.replace('%20', '+');
            str = str.replace('*', '%2A');
            str = str.replace('/', '%2F');
            str = str.replace('@', '%40');
            return str;
        }

        <?php
            echo "var draftid = {$draftid};";
        ?>

        // Auto save
        function save_content(){
            var xmlhttp=false;

            if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
                try {
                    xmlhttp = new XMLHttpRequest();
                } catch (e) {
                    xmlhttp=false;
                }
            }
            if (!xmlhttp && window.createRequest) {
                try {
                    xmlhttp = window.createRequest();
                } catch (e) {
                    xmlhttp=false;
                }
            }

            var toPass = byId('bodytext').value;
            //alert(toPass.value);

/*
Format of meta data
$emailtype|$newincidentstatus|$timetonextaction_none|$timetonextaction_days|$timetonextaction_hours|$timetonextaction_minutes|$day|$month|$year|$target|$chase_customer|$chase_manager|$from|$replyTo|$ccemail|$bccemail|$toemail|$subject|$body
*/

            var meta = byId('emailtype').value+"|"+byId('newincidentstatus').value+"|"+byId('timetonextaction_none').value+"|";
            meta = meta+byId('timetonextaction_days').value+"|"+byId('timetonextaction_hours').value+"|";
            meta = meta+byId('timetonextaction_minutes').value+"|"+byId('day').value+"|"+byId('month').value+"|";
            meta = meta+byId('year').value+"|"+byId('target').value+"|"+byId('chase_customer').value+"|";
            meta = meta+byId('chase_manager').value+"|"+byId('fromfield').value+"|"+byId('replytofield').value+"|";
            meta = meta+byId('ccfield').value+"|"+byId('bccfield').value+"|"+byId('tofield').value+"|";
            meta = meta+urlencode(byId('subjectfield').value)+"|"+urlencode(byId('bodytext').value)+"|"
            meta = meta+byId('date').value+"|"+byid('timeoffset').value;

            if (toPass != "")
            {
                xmlhttp.open("GET", "auto_save.php?userid="+<?php echo $_SESSION['userid']; ?>+"&type=email&incidentid="+<?php echo $id; ?>+"&draftid="+draftid+"&meta="+meta+"&content="+escape(toPass), true);

                xmlhttp.onreadystatechange=function() {
                    //remove this in the future after testing
                    if (xmlhttp.readyState==4) {
                        if (xmlhttp.responseText != ""){
                            //alert(xmlhttp.responseText);
                            if (draftid == -1)
                            {
                                draftid = xmlhttp.responseText;
                            }
                            var currentTime = new Date();
                            var hours = currentTime.getHours();
                            var minutes = currentTime.getMinutes();
                            if (minutes < 10)
                            {
                                minutes = "0"+minutes;
                            }
                            var seconds = currentTime.getSeconds();
                            if (seconds < 10)
                            {
                                seconds = "0"+seconds;
                            }
                            byId('updatestr').innerHTML = "<?php echo "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/info.png' /> ".$GLOBALS['strDraftLastSaved'] ?>: "+hours+":"+minutes+":"+seconds;
                            $('draftid').value = draftid;
                        }
                    }
                }
                xmlhttp.send(null);
            }
        }

        setInterval("save_content()", 10000); //every 10 seconds

        </script>
        <?php
        // External vars
        if ($draftid == -1)
        {
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
            $chase_customer = cleanvar($_REQUEST['chase_customer']);
            $chase_manager = cleanvar($_REQUEST['chase_manager']);
            $date = cleanvar($_REQUEST['date']);
            $timeoffset = cleanvar($_REQUEST['timeoffset']);
        }
        else
        {
            $emailtype = $metadata[0];
            $newincidentstatus = $metadata[1];
            $timetonextaction_none = $metadata[2];
            $timetonextaction_days = $metadata[3];
            $timetonextaction_hours = $metadata[4];
            $timetonextaction_minutes = $metadata[5];
            $day = $metadata[6];
            $month = $metadata[7];
            $year = $metadata[8];
            $target = $metadata[9];
            $chase_customer = $metadata[10];
            $chase_manager = $metadata[11];
            $date = $metadata[12];
            $timeoffset = $metadata[13];
        }

        if ($emailtype == 0 AND $draftid == -1)
        {
            echo "<p class='error'>You must select an email type</p>\n";
        }
        else
        {
            if ($draftid == -1)
            {
                $from = emailtype_replace_specials(emailtype_from($emailtype), $id, $sit[2]);
                $replyTo = emailtype_replace_specials(emailtype_replyto($emailtype), $id, $sit[2]);
                $ccemail = emailtype_replace_specials(emailtype_cc($emailtype), $id, $sit[2]);
                $bccemail = emailtype_replace_specials(emailtype_bcc($emailtype), $id, $sit[2]);
                $toemail = emailtype_replace_specials(emailtype_to($emailtype), $id, $sit[2]);
                $subject = emailtype_replace_specials(emailtype_subject($emailtype), $id, $sit[2]);
                $body = emailtype_replace_specials(emailtype_body($emailtype), $id, $sit[2]);
            }
            else
            {
                $from = $metadata[12];
                $replyTo = $metadata[13];
                $ccemail = $metadata[14];
                $bccemail = $metadata[15];
                $toemail = $metadata[16];
//                 $subject = stripslashes($metadata[17]);
//                 $body = stripslashes($metadata[18]);
                $subject = $metadata[17];
                $body = $metadata[18];
            }

            // NOTE \" used rather than ' so email address can contain a ' (as permitted by RFC) PH 28/10/2007

            // encoding is multipart/form-data again as it no longer works without (why was this disabled?) - TPG 13/08/2002
            echo "<form action='{$_SERVER['PHP_SELF']}?id={$id}' method='post' enctype='multipart/form-data' onsubmit='return confirm_send_mail();' >";
            echo "<table align='center' class='vertical' width='95%'>";
            echo "<tr><th width='30%'>{$strFrom}:</th><td><input maxlength='100' name='fromfield' id='fromfield' size='40' value=\"{$from}\" /></td></tr>\n";
            echo "<tr><th>{$strReplyTo}:</th><td><input maxlength='100' name='replytofield' id='replytofield' size='40' value=\"{$replyTo}\" /></td></tr>\n";
            if (trim($ccemail) == ",") $ccemail = "";
            if (substr($ccemail, 0, 1) == ",") $ccfield = substr($ccemail, 1, strlen($ccemail));
            echo "<tr><th>CC:</th><td><input maxlength='100' name='ccfield' id='ccfield' size='40' value=\"{$ccemail}\" /></td></tr>\n";
            echo "<tr><th>BCC:</th><td><input maxlength='100' name='bccfield' id='bccfield' size='40' value=\"{$bccemail}\" /></td></tr>\n";
            echo "<tr><th>{$strTo}:</th><td><input maxlength='100' name='tofield' id='tofield' size='40' value=\"{$toemail}\" /></td></tr>\n";
            echo "<tr><th>{$strSubject}:</th><td><input maxlength='255' name='subjectfield' id='subjectfield' size='40' value=\"{$subject}\" /></td></tr>\n";
            echo "<tr><th>{$strAttachment}";
            // calculate filesize
            $j = 0;
            $ext =
            array("Bytes","KBytes","MBytes","GBytes","TBytes");
            $file_size = $CONFIG['upload_max_filesize'];
            while ($file_size >= pow(1024,$j)) ++$j;
            $file_size = round($file_size / pow(1024,$j-1) * 100) / 100 . ' ' . $ext[$j-1];
            echo "(&lt; $file_size)";
            echo ":</th><td>";
            echo "<input type='hidden' name='MAX_FILE_SIZE' value='{$CONFIG['upload_max_filesize']}' />";
            echo "<input type='file' name='attachment' size='40' maxfilesize='{$CONFIG['upload_max_filesize']}' />";
            echo "</td></tr>";
            echo "<tr><th>{$strMessage}:</th><td>";
            echo "<textarea name='bodytext' id='bodytext' rows='20' cols='65'>";
            // Attempt to restore email body from session in case there was an error submitting previously
            if (!empty($_SESSION['temp-emailbody']))
            {
                echo $_SESSION['temp-emailbody'];
            }
            else
            {
                echo $body;
            }
            echo "</textarea>";
            echo "<div id='updatestr'></div>";
            echo "</td></tr>";
            if ($CONFIG['enable_spellchecker'] == TRUE)
            {
                echo "<tr><th>&nbsp;</th><td><input type='checkbox' name='spellcheck' value='yes' /> Check Spelling before sending</td></tr>\n";
            }
            echo "</table>";
            echo "<p align='center'>";
            echo "<input name='newincidentstatus' id='newincidentstatus' type='hidden' value='{$newincidentstatus}' />";
            echo "<input name='timetonextaction_none' id='timetonextaction_none' type='hidden' value='{$timetonextaction_none}' />";
            echo "<input name='timetonextaction_days' id='timetonextaction_days' type='hidden' value='{$timetonextaction_days}' />";
            echo "<input name='timetonextaction_hours' id='timetonextaction_hours' type='hidden' value='{$timetonextaction_hours}' />";
            echo "<input name='timetonextaction_minutes' id='timetonextaction_minutes' type='hidden' value='{$timetonextaction_minutes}' />";
            echo "<input name='chase_customer' id='chase_customer' type='hidden' value='{$chase_customer}' />";
            echo "<input name='chase_manager' id='chase_manager' type='hidden' value='{$chase_manager}' />";
            echo "<input name='date' id='date' type='hidden' value='{$date}' />";
            echo "<input name='timeoffset' id='timeoffset' type='hidden' value='{$timeoffset}' />";
//             echo "<input name='day' id='day' type='hidden' value='{$day}' />";
//             echo "<input name='month' id='month' type='hidden' value='{$month}' />";
//             echo "<input name='year' id='year' type='hidden' value='{$year}' />";
            echo "<input name='target' id='target' type='hidden' value='{$target}' />";
            echo "<input type='hidden' id='step' name='step' value='3' />";
            echo "<input type='hidden' id='emailtype' name='emailtype' value='{$emailtype}' />";
            echo "<input type='hidden' id='draftid' name='draftid' value='{$draftid}' />";
            echo "<input name='submit2' type='submit' value='{$strSendEmail}' />";
            echo "</p>\n</form>\n";
        }
        include ('incident_html_bottom.inc.php');
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
        $subjectfield = cleanvar($_REQUEST['subjectfield'],FALSE,TRUE);
        $emailtype = cleanvar($_REQUEST['emailtype']);
        $newincidentstatus = cleanvar($_REQUEST['newincidentstatus']);
        $timetonextaction_none = cleanvar($_REQUEST['timetonextaction_none']);
        $timetonextaction_days = cleanvar($_REQUEST['timetonextaction_days']);
        $timetonextaction_hours = cleanvar($_REQUEST['timetonextaction_hours']);
        $timetonextaction_minutes = cleanvar($_REQUEST['timetonextaction_minutes']);
        $date = cleanvar($_REQUEST['date']);
        $timeoffset = cleanvar($_REQUEST['timeoffset']);
        /*$day = cleanvar($_REQUEST['day']);
        $month = cleanvar($_REQUEST['month']);
        */$year = cleanvar($_REQUEST['year']);
        $target = cleanvar($_REQUEST['target']);
        $chase_customer = cleanvar($_REQUEST['chase_customer']);
        $chase_manager = cleanvar($_REQUEST['chase_manager']);

        ?>
        <script type="text/javascript">
        function confirm_close_window()
        {
            if (window.confirm('The email was sent successfully, click OK to close this window'))
            window.opener.location='incident_details.php?id=<?php echo $id; ?>';
            window.close();
        }
        </script>
        <?php

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
            include ('spellcheck_email.php');
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
            $extra_headers .= "X-Originating-IP: {$_SERVER['REMOTE_ADDR']}\n";
            if ($ccfield != '')  $extra_headers .= "cc: $ccfield\n";
            if ($bccfield != '') $extra_headers .= "Bcc: $bccfield\n";

            $extra_headers .= "\n"; // add an extra crlf to create a null line to separate headers from body
                                // this appears to be required by some email clients - INL

            $mime = new MIME_mail($fromfield, $tofield, $subjectfield, $bodytext, $extra_headers, $mailerror);

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
                        // kh: parse date from calendar picker, format: 200-12-31
                        $date=explode("-", $date);
                        $timeofnextaction=mktime(8 + $timeoffset,0,0,$date[1],$date[2],$date[0]);
                        $now = time();
                        if ($timeofnextaction<0) $timeofnextaction=0;
                    break;

                    default:
                        $timeofnextaction = 0;
                    break;
                }

                $oldtimeofnextaction=incident_timeofnextaction($id);

                if ($newincidentstatus != incident_status($id))
                {
                    $sql = "UPDATE `{$dbIncidents}` SET status='$newincidentstatus', lastupdated='$now', timeofnextaction='$timeofnextaction' WHERE id='$id'";
                    mysql_query($sql);
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
                    $updateheader = "New Status: <b>" . incidentstatus_name($newincidentstatus) . "</b>\n\n";
                }
                else
                {
                    mysql_query("UPDATE `{$dbIncidents}` SET lastupdated='$now', timeofnextaction='$timeofnextaction' WHERE id='$id'");
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
                }

                $timetext = "";

                if ($timeofnextaction != 0)
                {
                    $timetext = "Next Action Time: ";
                    if (($oldtimeofnextaction-$now)<1)
                    {
                        $timetext .= "None";
                    }
                    else
                    {
                        $timetext .= date("D jS M Y @ g:i A", $oldtimeofnextaction);
                    }
                    $timetext .= " -&gt; <b>";
                    if ($timeofnextaction<1)
                    {
                        $timetext .= "None";
                    }
                    else
                    {
                        $timetext.=date("D jS M Y @ g:i A", $timeofnextaction);
                    }
                    $timetext .= "</b>\n\n";
                    //$bodytext = $timetext.$bodytext;
                }

                // add update
                $bodytext=htmlentities($bodytext, ENT_COMPAT, 'UTF-8');
                $updateheader .= "To: <b>$tofield</b>\nFrom: <b>$fromfield</b>\nReply-To: <b>$replytofield</b>\n";
                if ($ccfield!="" AND $ccfield!=",") $updateheader .=   "CC: <b>$ccfield</b>\n";
                if ($bccfield!="") $updateheader .= "BCC: <b>$bccfield</b>\n";
                if ($filename!="") $updateheader .= "Attachment: <b>".$filename_end_part."</b>\n";
                $updateheader .= "Subject: <b>$subjectfield</b>\n";

                if (!empty($updateheader)) $updateheader .= "<hr>";
                $updatebody = $timetext . $updateheader . $bodytext;
                $updatebody=mysql_real_escape_string($updatebody);

                $sql  = "INSERT INTO `{$dbUpdates}` (incidentid, userid, bodytext, type, timestamp, currentstatus,customervisibility) ";
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
                        $sql  = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, timestamp, currentowner, currentstatus, customervisibility, sla, bodytext) ";
                        $sql .= "VALUES ('$id', '".$sit[2]."', 'slamet', '$now', '".$sit[2]."', '$newincidentstatus', 'show', 'initialresponse','The Initial Response has been made.')";
                    break;

                    case 'probdef':
                        $sql  = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, timestamp, currentowner, currentstatus, customervisibility, sla, bodytext) ";
                        $sql .= "VALUES ('$id', '".$sit[2]."', 'slamet', '$now', '".$sit[2]."', '$newincidentstatus', 'show', 'probdef','The problem has been defined.')";
                    break;

                    case 'actionplan':
                        $sql  = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, timestamp, currentowner, currentstatus, customervisibility, sla, bodytext) ";
                        $sql .= "VALUES ('$id', '".$sit[2]."', 'slamet', '$now', '".$sit[2]."', '$newincidentstatus', 'show', 'actionplan','An action plan has been made.')";
                    break;

                    case 'solution':
                        $sql  = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, timestamp, currentowner, currentstatus, customervisibility, sla, bodytext) ";
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
                    $sql = "UPDATE `{$dbIncidents}` SET slaemail='0', slanotice='0' WHERE id='$id' LIMIT 1";
                    mysql_query($sql);
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
                }

                if (!empty($chase_customer))
                {
                    $sql_insert = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, bodytext, timestamp, customervisibility) VALUES ('{$id}','{$sit['2']}','auto_chased_phone','Customer has been called to chase','{$now}','hide')";
                    mysql_query($sql_insert);
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

                    $sql_update = "UPDATE `{$dbIncidents}` SET lastupdated = '{$now}' WHERE id = {$id}";
                    mysql_query($sql_update);
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
                }

                if (!empty($chase_manager))
                {
                    $sql_insert = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, bodytext, timestamp, customervisibility) VALUES ('{$id}','{$sit['2']}','auto_chased_manager','Manager has been called to chase','{$now}','hide')";
                    mysql_query($sql_insert);
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

                    $sql_update = "UPDATE `{$dbIncidents}` SET lastupdated = '{$now}' WHERE id = {$id}";
                    mysql_query($sql_update);
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
                }

                if ($draftid != -1)
                {
                    $sql = "DELETE FROM drafts WHERE id = {$draftid}";
                    mysql_query($sql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                }

                journal(CFG_LOGGING_FULL, 'Email Sent', "Email sent subject: $subjectfield, regarding incident $id", CFG_JOURNAL_INCIDENTS, $id);
                echo "<html>";
                echo "<head></head>";
                echo "<body onload=\"confirm_close_window();\">";
                echo "</body>";
                echo "</html>";
            }
            else
            {
                include ('incident_html_top.inc.php');
                echo "<p class='error'>Error sending email: $mailerror</p>\n";
                include ('incident_html_bottom.inc.php');
            }
        }
        else
        {
            // there were errors
            include ('incident_html_top.inc.php');
            echo $error_string;
            include ('incident_html_bottom.inc.php');
        }
    break;

    default:
        throw_error('Fatal error', "Invalid Step $step");
    break;
} // end switch step

?>