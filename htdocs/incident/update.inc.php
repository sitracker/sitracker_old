<?php
// update.inc.php - Displays a page for updating the incident log
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
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

$title = $strUpdate;

/**
    * Update page
*/
function display_update_page($draftid=-1)
{
    global $id;
    global $incidentid;
    global $action;
    global $CONFIG;
    global $iconset;
    global $now;
    global $dbDrafts;

    if ($draftid != -1)
    {
        $draftsql = "SELECT * FROM `{$dbDrafts}` WHERE id = {$draftid}";
        $draftresult = mysql_query($draftsql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        $draftobj = mysql_fetch_object($draftresult);

        $metadata = explode("|",$draftobj->meta);
    }

    // No update body text detected show update form

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
        //object.priority.disabled=true;
        object.priority.disabled=false;
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
        document.updateform.priority.disabled=false;
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

    // Display/Hide the time to next action fields
    // Author: Ivan Lucas
    function update_ttna() {
         if ($('ttna_time').checked)
         {
            $('ttnacountdown').show();
            $('ttnadate').hide();
         }
         if ($('ttna_date').checked)
         {
            $('ttnacountdown').hide();
            $('ttnadate').show();
         }
         if ($('ttna_none').checked)
         {
            $('ttnacountdown').hide();
            $('ttnadate').hide();
         }
    }

    <?php
        echo "var draftid = {$draftid}";
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

        var toPass = byId('updatelog').value;
        //alert(toPass.value);

        var meta = byId('target').value+"|"+byId('updatetype').value+"|"+byId('cust_vis').checked+"|";
        meta += byId('priority').value+"|"+byId('newstatus').value+"|"+byId('nextaction').value+"|";

        if (toPass != "")
        {
            xmlhttp.open("GET", "auto_save.php?userid="+<?php echo $_SESSION['userid']; ?>+"&type=update&incidentid="+<?php echo $id; ?>+"&draftid="+draftid+"&meta="+meta+"&content="+escape(toPass), true);

            xmlhttp.onreadystatechange=function() {
                //remove this in the future after testing
                if (xmlhttp.readyState==4) {
                    if (xmlhttp.responseText != ""){
                        //alert(xmlhttp.responseText);
                        if (draftid == -1)
                        {
                            draftid = xmlhttp.responseText;
                            byId('draftid').value = draftid;
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
                    }
                }
            }
            xmlhttp.send(null);
        }
    }

    setInterval("save_content()", 10000); //every 10 seconds

    //-->
    </script>
    <?php
    //echo "<form action='".$_SERVER['PHP_SELF']."?id={$id}&amp;draftid={$draftid}' method='post' name='updateform' id='updateform' enctype='multipart/form-data'>";
    echo "<form action='".$_SERVER['PHP_SELF']."?id={$id}' method='post' name='updateform' id='updateform' enctype='multipart/form-data'>";
    echo "<table class='vertical'>";
    echo "<tr>";
    echo "<th align='right' valign='top' width='30%;'>{$GLOBALS['strDoesThisUpdateMeetSLA']}:";
    echo "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/sla.png' width='16' height='16' alt='' /></th>";
    echo "<td class='shade2'>";
    $target = incident_get_next_target($id);

    $targetNone = "";
    $targetInitialresponse = "";
    $targetProbdef = "";
    $targetActionplan = "";
    $targetSolution = "";

    $typeResearch = "";
    $typeEmailin = "";
    $typeEmailout = "";
    $typePhonecallin = "";
    $typePhonecallout = "";
    $typeExternalinfo = "";
    $typeReviewmet = "";


    if (!empty($metadata))
    {
        switch ($metadata[0])
        {
            case 'none': $targetNone = " SELECTED ";
                break;
            case 'initialresponse': $targetInitialresponse = " SELECTED ";
                break;
            case 'probdef': $targetProbdef = " SELECTED ";
                break;
            case 'actionplan': $targetActionplan = " SELECTED ";
                break;
            case 'solution': $targetSolution = " SELECTED ";
                break;
        }

        switch ($metadata[1])
        {
            case 'research': $typeResearch = " SELECTED ";
                break;
            case 'emailin': $typeEmailin = " SELECTED ";
                break;
            case 'emailout': $typeEmailout = " SELECTED ";
                break;
            case 'phonecallin': $typePhonecallin = " SELECTED ";
                break;
            case 'phonecallout': $typePhonecallout = " SELECTED ";
                break;
            case 'externalinfo': $typeExternalinfo = " SELECTED ";
                break;
            case 'reviewmet': $typeReviewmet = " SELECTED ";
                break;
        }
    }


    echo "<select name='target' id='target' class='dropdown'>\n";
    echo "<option value='none' {$targetNone} onclick='notarget(this.form)'>{$GLOBALS['strNo']}</option>\n";
    switch ($target->type)
    {
        case 'initialresponse':
            echo "<option value='initialresponse' {$targetInitialresponse} style='text-indent: 15px; height: 17px; background-image: url({$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/initialresponse.png); background-repeat: no-repeat;' onclick='initialresponse(this.form)' >{$GLOBALS['strInitialResponse']}</option>\n";
            echo "<option value='probdef' {$targetProbdef} style='text-indent: 15px; height: 17px; background-image: url({$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/probdef.png); background-repeat: no-repeat;' onclick='probdef(this.form)'>{$GLOBALS['strProblemDefinition']}</option>\n";
            echo "<option value='actionplan' {$targetActionplan} style='text-indent: 15px; height: 17px; background-image: url({$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/actionplan.png); background-repeat: no-repeat;' onclick='actionplan(this.form)'>{$GLOBALS['strActionPlan']}</option>\n";
            echo "<option value='solution' {$targetSolution} style='text-indent: 15px; height: 17px; background-image: url({$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/solution.png); background-repeat: no-repeat;' onclick='reprioritise(this.form)'>{$GLOBALS['strResolutionReprioritisation']}</option>\n";
        break;
        case 'probdef':
            echo "<option value='probdef' {$targetProbdef} style='text-indent: 15px; height: 17px; background-image: url({$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/probdef.png); background-repeat: no-repeat;' onclick='probdef(this.form)'>{$GLOBALS['strProblemDefinition']}</option>\n";
            echo "<option value='actionplan' {$targetActionplan} style='text-indent: 15px; height: 17px; background-image: url({$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/actionplan.png); background-repeat: no-repeat;' onclick='actionplan(this.form)'>{$GLOBALS['strActionPlan']}</option>\n";
            echo "<option value='solution' {$targetSolution} style='text-indent: 15px; height: 17px; background-image: url({$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/solution.png); background-repeat: no-repeat;' onclick='reprioritise(this.form)'>{$GLOBALS['strResolutionReprioritisation']}</option>\n";
        break;
        case 'actionplan':
            echo "<option value='actionplan' {$targetActionplan} style='text-indent: 15px; height: 17px; background-image: url({$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/actionplan.png); background-repeat: no-repeat;' onclick='actionplan(this.form)'>{$GLOBALS['strActionPlan']}</option>\n";
            echo "<option value='solution' {$targetSolution} style='text-indent: 15px; height: 17px; background-image: url({$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/solution.png); background-repeat: no-repeat;' onclick='reprioritise(this.form)'>{$GLOBALS['strResolutionReprioritisation']}</option>\n";
        break;
            case 'solution':
            echo "<option value='solution' {$targetSolution} style='text-indent: 15px; height: 17px; background-image: url({$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/solution.png); background-repeat: no-repeat;' onclick='reprioritise(this.form)'>{$GLOBALS['strResolutionReprioritisation']}</option>\n";
        break;
    }
    echo "</select>\n";
    echo "</td></tr>\n";
    echo "<tr><th align='right' valign='top'>{$GLOBALS['strUpdateType']}:</th>";
    echo "<td class='shade1'>";
    echo "<select name='updatetype' id='updatetype' class='dropdown'>";
    /*
    if ($target->type!='actionplan' && $target->type!='solution')
        echo "<option value='probdef'>Problem Definition</option>\n";
    if ($target->type!='solution')
        echo "<option value='actionplan'>Action Plan</option>\n";
    */
    echo "<option value='research' {$typeResearch} selected='selected' style='text-indent: 15px; height: 17px; background-image: url({$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/research.png); background-repeat: no-repeat;'>{$GLOBALS['strResearchNotes']}</option>\n";
    echo "<option value='emailin' {$typeEmailin} style='text-indent: 15px; height: 17px; background-image: url({$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/emailin.png); background-repeat: no-repeat;'>{$GLOBALS['strEmailFromCustomer']}</option>\n";
    echo "<option value='emailout' {$typeEmailout} style='text-indent: 15px; height: 17px; background-image: url({$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/emailout.png); background-repeat: no-repeat;'>{$GLOBALS['strEmailToCustomer']}</option>\n";
    echo "<option value='phonecallin' {$typePhonecallin} style='text-indent: 15px; height: 17px; background-image: url({$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/callin.png); background-repeat: no-repeat;'>{$GLOBALS['strCallFromCustomer']}</option>\n";
    echo "<option value='phonecallout' {$typePhonecallout} style='text-indent: 15px; height: 17px; background-image: url({$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/callout.png); background-repeat: no-repeat;'>{$GLOBALS['strCallToCustomer']}</option>\n";
    echo "<option value='externalinfo' {$typeExternalinfo} style='text-indent: 15px; height: 17px; background-image: url({$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/externalinfo.png); background-repeat: no-repeat;'>{$GLOBALS['strExternalInfo']}</option>\n";
    echo "<option value='reviewmet' {$typeReviewmet} style='text-indent: 15px; height: 17px; background-image: url({$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/review.png); background-repeat: no-repeat;'>{$GLOBALS['strReview']}</option>\n";

    echo "</select>";
    echo "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<th align='right' valign='top'>Update Log:</th>";
    echo "<td class='shade1'>";
    echo "New information, relevent to the incident.  Please be as detailed as possible and include full descriptions of any work you have performed.<br />";
    echo "<br />";
    $checkbox = "";
    if (!empty($metadata))
    {
        if ($metadata[2] == "true") $checkbox = "checked='checked'";
    }
    else
    {
        $checkbox = "checked='checked'";
    }
    echo "<label><input type='checkbox' name='cust_vis' id='cust_vis' {$checkbox} value='yes' /> Make this update visible to the incident reporter<label><br />"; //FIXME i18n Make this update visible to the incident reporter
    echo "<textarea name='bodytext' id='updatelog' rows='13' cols='50'>";
    if ($draftid != -1) echo $draftobj->content;
    echo "</textarea>";
    echo "<div id='updatestr'></div>";
    echo "</td></tr>";

    if ($target->type=='initialresponse')
    {
        $disable_priority=TRUE;
    }
    else $disable_priority=FALSE;
    echo "<tr><th align='right' valign='top'>{$GLOBALS['strNewPriority']}:</th>";
    echo "<td class='shade1'>";

    // FIXME fix maximum priority
    $servicelevel=maintenance_servicelevel(incident_maintid($id));
    if ($servicelevel==2 || $servicelevel==5) $maxpriority=4;
    else $maxpriority=3;

    $setPriorityTo = incident_priority($id);

    if (!empty($metadata))
    {
        $setPriorityTo = $metadata[3];
    }

    echo priority_drop_down("newpriority", $setPriorityTo, $maxpriority, $disable_priority); //id='priority
    echo "</td></tr>\n";

    echo "<tr>";
    echo "<th align='right' valign='top'>{$GLOBALS['strNewStatus']}:</th>";

    $setStatusTo = incident_status($id);

    if (!empty($metadata))
    {
        $setStatusTo = $metadata[4];
    }

    echo "<td class='shade1'>".incidentstatus_drop_down("newstatus", $setStatusTo)."</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<th align='right' valign='top'>{$GLOBALS['strNextAction']}:</th>";

    $nextAction = "";

    if (!empty($metadata))
    {
        $nextAction = $metadata[5];
    }

    echo "<td class='shade2'><input type='text' name='nextaction' id='nextaction' maxlength='50' size='30' value='{$nextAction}' /></td></tr>";
    echo "<tr>";
    echo "<th align='right'>";
    // FIXME i18n will be placed in the waiting queue
    echo "<strong>{$GLOBALS['strTimeToNextAction']}</strong>:</th>";
    echo "<td class='shade2'>";
    echo "Place the incident in the waiting queue?<br />";

    $oldtimeofnextaction=incident_timeofnextaction($id);
    if ($oldtimeofnextaction<1) $oldtimeofnextaction=$now;
    $wait_time=($oldtimeofnextaction-$now);

    $na_days=floor($wait_time / 86400);
    $na_remainder=$wait_time % 86400;
    $na_hours=floor($na_remainder / 3600);
    $na_remainder=$wait_time % 3600;
    $na_minutes=floor($na_remainder / 60);
    if ($na_days<0) $na_days=0;
    if ($na_hours<0) $na_hours=0;
    if ($na_minutes<0) $na_minutes=0;

    echo "<label><input type='radio' name='timetonextaction_none' id='ttna_time' value='time' onchange=\"update_ttna();\" />";
    echo "For <em>x</em> days, hours, minutes</label><br />"; // FIXME i18n for x days,. hours, minutes
    echo "<span id='ttnacountdown'";
    if (empty($na_days) AND empty($na_hours) AND empty($na_minutes)) echo " style='display: none;'";
    echo ">";
    echo "&nbsp;&nbsp;&nbsp;<input maxlength='3' name='timetonextaction_days' id='timetonextaction_days' value='{$na_days}' onclick='window.document.updateform.timetonextaction_none[0].checked = true;' size='3' /> {$GLOBALS['strDays']}&nbsp;";
    echo "<input maxlength='2' name='timetonextaction_hours' id='timetonextaction_hours' value='{$na_hours}' onclick='window.document.updateform.timetonextaction_none[0].checked = true;' size='3' /> {$GLOBALS['strHours']}&nbsp;";
    echo "<input maxlength='2' name='timetonextaction_minutes' id='timetonextaction_minutes' value='{$na_minutes}' onclick='window.document.updateform.timetonextaction_none[0].checked = true;' size='3' /> {$GLOBALS['strMinutes']}";
    echo "<br /></span>";

    echo "<input type='radio' name='timetonextaction_none' id='ttna_date' value='date' onchange=\"update_ttna();\" />Until specific date and time<br />"; //FIXME i18n Until specific date and time
    echo "<span id='ttnadate' style='display: none;'>";
    echo "<input name='date' id='date' size='10' value='{$date}' onclick=\"window.document.updateform.timetonextaction_none[1].checked = true;\"/> ";
    echo date_picker('updateform.date');
    echo " <select name='timeoffset' id='timeoffset' onchange='window.document.updateform.timetonextaction_none[1].checked = true;'>";
    echo "<option value='0'></option>";
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
    echo "<br /></span>";

    echo "<label><input checked='checked' type='radio' name='timetonextaction_none' id='ttna_none' onchange=\"update_ttna();\" onclick=\"window.document.updateform.timetonextaction_days.value = ''; window.document.updateform.timetonextaction_hours.value = ''; window.document.updateform.timetonextaction_minutes.value = '';\" value='None' />Unspecified</label>";
    echo "</td></tr>";
    echo "<tr>";
    // calculate upload filesize
    $j = 0;
    $ext = array($strBytes, $strKBytes, $strMBytes, $strGBytes, $strTBytes);
    $att_file_size = $CONFIG['upload_max_filesize'];
    while ($att_file_size >= pow(1024,$j)) ++$j;
    $att_file_size = round($att_file_size / pow(1024,$j-1) * 100) / 100 . ' ' . $ext[$j-1];

    echo "<th align='right' valign='top'>{$GLOBALS['strAttachFile']}";
    echo "(&lt;{$att_file_size}):</th>";

    echo "<td class='shade1'><input type='hidden' name='MAX_FILE_SIZE' value='{$CONFIG['upload_max_filesize']}' />";
    echo "<input type='file' name='attachment' size='40' maxfilesize='{$CONFIG['upload_max_filesize']}' /></td>";
    echo "</tr>";
    echo "</table>";
    echo "<p class='center'>";
    echo "<input type='hidden' name='action' value='update' />";
    if ($draftid == -1) $localdraft = "";
    else $localdraft = $draftid;
    echo "<input type='hidden' name='draftid' id='draftid' value='{$localdraft}' />";
    echo "<input type='hidden' name='storepriority' value='".incident_priority($id)."' />";
    echo "<input type='submit' name='submit' value='{$GLOBALS['strUpdateIncident']}' /></p>";
    echo "</form>";
}


if (empty($action))
{
    $sql = "SELECT * FROM `{$dbDrafts}` WHERE type = 'update' AND userid = '{$sit[2]}' AND incidentid = '{$id}'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

    include ('incident_html_top.inc.php');

    if (mysql_num_rows($result) > 0)
    {
        echo "<h2>{$title}</h2>";

        display_drafts('update', $result);

        echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?action=newupdate&amp;id={$id}'>{$strUpdateNewUpdate}</a></p>";
    }
    else
    {
        //No previous updates - just display the page
        display_update_page();
    }
}
else if ($action == "editdraft")
{
    include ('incident_html_top.inc.php');
    $draftid = cleanvar($_REQUEST['draftid']);
    display_update_page($draftid);
}
else if ($action == "deletedraft")
{
    $draftid = cleanvar($_REQUEST['draftid']);
    if ($draftid != -1)
    {
        $sql = "DELETE FROM `{$dbDrafts}` WHERE id = {$draftid}";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    }
    html_redirect("update_incident.php?id={$id}");
}
else if ($action == "newupdate")
{
    include ('incident_html_top.inc.php');
    display_update_page();
}
else
{
    // Update the incident

    // External variables
    $target = cleanvar($_POST['target']);
    $updatetype = cleanvar($_POST['updatetype']);
    $newstatus = cleanvar($_POST['newstatus']);
    $nextaction = cleanvar($_POST['nextaction']);
    $newpriority = cleanvar($_POST['newpriority']);
    $cust_vis = cleanvar($_POST['cust_vis']);
    $timetonextaction_none = cleanvar($_POST['timetonextaction_none']);
    $date = cleanvar($_POST['date']);
    $timeoffset = cleanvar($_POST['timeoffset']);
    $timetonextaction_days = cleanvar($_POST['timetonextaction_days']);
    $timetonextaction_hours = cleanvar($_POST['timetonextaction_hours']);
    $timetonextaction_minutes = cleanvar($_POST['timetonextaction_minutes']);
    $draftid = cleanvar($_POST['draftid']);

    if (empty($newpriority)) $newpriority  = incident_priority($id);
    // update incident
    switch ($timetonextaction_none)
    {
        case 'none':
            $timeofnextaction = 0;
        break;

        case 'time':
            if ($timetonextaction_days<1 && $timetonextaction_hours<1 && $timetonextaction_minutes<1)
            {
                $timeofnextaction = 0;
            }
            else
            {
                $timeofnextaction = calculate_time_of_next_action($timetonextaction_days, $timetonextaction_hours, $timetonextaction_minutes);
            }
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

    // Put text into body of update for field changes (reverse order)
    // delim first
    $bodytext = "<hr>" . $bodytext;
    $oldstatus=incident_status($id);
    $oldtimeofnextaction=incident_timeofnextaction($id);
    if ($newstatus != $oldstatus)
    {
        $bodytext = "Status: ".incidentstatus_name($oldstatus)." -&gt; <b>" . incidentstatus_name($newstatus) . "</b>\n\n" . $bodytext;
    }
    if ($newpriority != incident_priority($id))
    {
        $bodytext = "New Priority: <b>" . priority_name($newpriority) . "</b>\n\n" . $bodytext;
    }
    if ($timeofnextaction > ($oldtimeofnextaction+60))
    {
        $timetext = "Next Action Time: ";
        if (($oldtimeofnextaction-$now)<1) $timetext.="None";
        else $timetext.=date("D jS M Y @ g:i A", $oldtimeofnextaction);
        $timetext.=" -&gt; <b>";
        if ($timeofnextaction<1) $timetext.="None";
        else $timetext.=date("D jS M Y @ g:i A", $timeofnextaction);
            $timetext.="</b>\n\n";
        $bodytext=$timetext.$bodytext;
    }
    // was '$attachment'
    if ($_FILES['attachment']['name']!='' && isset($_FILES['attachment']['name'])==TRUE)
    {
        $bodytext = "Attachment: [[att]]{$_FILES['attachment']['name']}[[/att]]\n".$bodytext;
    }
    // Debug
    ## if ($target!='') $bodytext = "Target: $target\n".$bodytext;

    // Check the updatetype field, if it's blank look at the target
    if (empty($updatetype))
    {
        switch ($target)
        {
            case 'actionplan': $updatetype='actionplan';  break;
            case 'probdef': $updatetype='probdef';  break;
            case 'solution': $updatetype='solution';  break;
            default: $updatetype='research';  break;
        }
    }

    // Force reviewmet to be visible
    if ($updatetype=='reviewmet') $cust_vis='yes';

    // visible update
    if ($cust_vis == "yes")
    {
        $sql  = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, bodytext, timestamp, currentstatus, customervisibility, nextaction) ";
        $sql .= "VALUES ('$id', '$sit[2]', '$updatetype', '$bodytext', '$now', '$newstatus', 'show' , '$nextaction')";
    }
    // invisible update
    else
    {
        $sql  = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, bodytext, timestamp, currentstatus, nextaction) ";
        $sql .= "VALUES ($id, $sit[2], '$updatetype', '$bodytext', '$now', '$newstatus', '$nextaction')";
    }
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    $sql = "UPDATE `{$dbIncidents}` SET status='$newstatus', priority='$newpriority', lastupdated='$now', timeofnextaction='$timeofnextaction' WHERE id='$id'";
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
            $sql .= "VALUES ('$id', '".$sit[2]."', 'slamet', '$now', '".$sit[2]."', '$newstatus', 'show', 'initialresponse','The Initial Response has been made.')";
        break;

        case 'probdef':
            $sql  = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, timestamp, currentowner, currentstatus, customervisibility, sla, bodytext) ";
            $sql .= "VALUES ('$id', '".$sit[2]."', 'slamet', '$now', '".$sit[2]."', '$newstatus', 'show', 'probdef','The problem has been defined.')";
        break;

        case 'actionplan':
            $sql  = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, timestamp, currentowner, currentstatus, customervisibility, sla, bodytext) ";
            $sql .= "VALUES ('$id', '".$sit[2]."', 'slamet', '$now', '".$sit[2]."', '$newstatus', 'show', 'actionplan','An action plan has been made.')";
        break;

        case 'solution':
            $sql  = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, timestamp, currentowner, currentstatus, customervisibility, sla, bodytext) ";
            $sql .= "VALUES ('$id', '".$sit[2]."', 'slamet', '$now', '".$sit[2]."', '$newstatus', 'show', 'solution','The incident has been resolved or reprioritised.\nThe issue should now be brought to a close or a new problem definition created within the service level.')";
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
        //remove any SLA notices - KMH
        $sql = "DELETE from notices WHERE userid={$sit[2]} AND referenceid={$id}";
        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    }

    // attach file
    $att_max_filesize = return_bytes($CONFIG['upload_max_filesize']);
    $incident_attachment_fspath = $CONFIG['attachment_fspath'] . $id;
    if ($_FILES['attachment']['name'] != "")
    {
        // make incident attachment dir if it doesn't exist
        $umask=umask(0000);
        if (!file_exists($CONFIG['attachment_fspath'] . "$id"))
        {
            $mk=@mkdir($CONFIG['attachment_fspath'] ."$id", 0770);
            if (!$mk) throw_error('Failed creating incident attachment directory: ',$incident_attachment_fspath .$id);
        }
        $mk=@mkdir($CONFIG['attachment_fspath'] .$id . "/$now", 0770);
        if (!$mk) throw_error('Failed creating incident attachment (timestamp) directory: ',$incident_attachment_fspath .$id . "/$now");
        umask($umask);
        $newfilename = $incident_attachment_fspath.'/'.$now.'/'.$_FILES['attachment']['name'];

        // Move the uploaded file from the temp directory into the incidents attachment dir
        $mv=move_uploaded_file($_FILES['attachment']['tmp_name'], $newfilename);
        if (!$mv) trigger_error('!Error: Problem moving attachment from temp directory to: '.$newfilename, E_USER_WARNING);

        //$mv=move_uploaded_file($attachment, "$filename");
        //if (!mv) throw_error('!Error: Problem moving attachment from temp directory:',$filename);

        // Check file size before attaching
        if ($_FILES['attachment']['size'] > $att_max_filesize)
        {
            throw_error('User Error: Attachment too large or file upload error - size:',$_FILES['attachment']['size']);
            // throwing an error isn't the nicest thing to do for the user but there seems to be no guaranteed
            // way of checking file sizes at the client end before the attachment is uploaded. - INL
        }
    }
    if (!$result)
    {
        include ('includes/incident_html_top.inc');
        echo "<p class='error'>{$strUpdateIncidentFailed}</p>\n";
        include ('includes/incident_html_bottom.inc');
    }
    else
    {
        if ($draftid != -1 AND !empty($draftid))
        {
            $sql = "DELETE FROM `{$dbDrafts}` WHERE id = {$draftid}";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        }
        journal(4,'Incident Updated', "Incident $id Updated", 2, $id);
        html_redirect("incident_details.php?id={$id}");
    }
}

?>
