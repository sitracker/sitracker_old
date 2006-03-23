<?php
// update_incident.php - For for logging updates to an incident
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Note: This functionality will soon be merged into the new incident.php
// way of doing things, we won't need this seperate file then - INL Nov05

$permission=8; // Update Incident
require('db_connect.inc.php');
require('functions.inc.php');

$disable_priority=TRUE;

// 19 Nov 04 - Fixed bug where currentstatus wasn't updated or inserted - ilucas

// This page requires authentication
require('auth.inc.php');

// External Variables
$bodytext = cleanvar($_REQUEST['bodytext']);
$id = cleanvar($_REQUEST['id']);

if (empty($bodytext))
{
    // No update body text detected show update form
    $incident_title=incident_title($id);
    $title = "Update: {$id} - {$incident_title}";
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



    //-->
    </script>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $id ?>" method="post" name="updateform" id="updateform" enctype="multipart/form-data">
    <table align='center' width="600">
    <tr>
    <th align="right" valign="top"><h3>Update to Incident:</h3></td>
    <td class="shade2"><h3 style='text-align: left'><?php echo "$id - $incident_title"; ?></h3></td>
    </tr>
    <tr>
    <th align="right" valign="top">Does this update meet an SLA target?:</th>
    <td class="shade2">
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
    </td>
    </tr>
    <tr>
    <th align="right" valign="top">Update Type:</th>
    <td class="shade1">
    <select name="updatetype" class='dropdown'>
    <?php
    /*
    if ($target->type!='actionplan' && $target->type!='solution')
        echo "<option value='probdef'>Problem Definition</option>\n";
    if ($target->type!='solution')
        echo "<option value='actionplan'>Action Plan</option>\n";
    */
    ?>
    <option value="research" selected>Research Notes</option>
    <option value="emailin">Email from Customer</option>
    <option value="emailout">Email to Customer</option>
    <option value="phonecallin">Phone call from Customer</option>
    <option value="phonecallout">Phone call to Customer</option>
    <option value="externalinfo">External Escalation Info</option>
    <option value="reviewmet">Incident Review</option>
    </select>
    </td>
    </tr>
    <tr>
    <th align="right" valign="top">Update Log:<br />
    New information, relevent to the incident.  Please be as detailed as possible and include full descriptions of any work you have performed.<br />
    <br />
    Check here <input type="checkbox" name="cust_vis" checked='checked' value="yes" /> to make this update visible to the customer.
    </td>
    <td class="shade1"><textarea name="bodytext" rows="15" cols="50"></textarea></td>
    </tr>
    <?php
    echo "<input type='hidden' name='storepriority' value='".incident_priority($id)."'>";
    if ($target->type=='initialresponse')
    {
        $disable_priority=TRUE;
    }
    else $disable_priority=FALSE;
    echo "<tr><th align='right' valign='top'>New Priority:</td>";
    echo "<td class='shade1'>";

    // FIXME, fix maximum priority
    $servicelevel=maintenance_servicelevel(incident_maintid($id));
    if ($servicelevel==2 || $servicelevel==5) $maxpriority=4;
    else $maxpriority=3;
    echo priority_drop_down("newpriority", incident_priority($id), $maxpriority, $disable_priority);
    echo "</td></tr>\n";
    ?>

    <tr>
    <th align="right" valign="top">New Status:</th>
    <td class="shade1"><?php echo incidentstatus_drop_down("newstatus", incident_status($id)); ?></td>
    </tr>
    <tr>
    <th align='right' valign=top>Next Action:</th>
    <td class="shade2"><input type="text" name="nextaction" maxlength="50" size="30" value="" /></td></tr>
    <tr>
    <th align='right'>
    <strong>Time To Next Action</strong>:<br />The incident will be placed in the waiting queue until the time specified.</th>
    <td class="shade2">
    <?php
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
    ?>
    <input type="radio" name="timetonextaction_none" value="time" />In <em>x</em> days, hours, minutes<br />&nbsp;&nbsp;&nbsp;
    <input maxlength='3' name="timetonextaction_days" value="<?php echo $na_days ?>" onclick="window.document.updateform.timetonextaction_none[0].checked = true;" size='3' /> Days&nbsp;
    <input maxlength='2' name="timetonextaction_hours" value="<?php echo $na_hours ?>" onclick="window.document.updateform.timetonextaction_none[0].checked = true;" size='3' /> Hours&nbsp;
    <input maxlength='2' name="timetonextaction_minutes" value="<?php echo $na_minutes ?>" onclick="window.document.updateform.timetonextaction_none[0].checked = true;" size='3' /> Minutes<br />
    <input type="radio" name="timetonextaction_none" value="date">On specified Date<br />&nbsp;&nbsp;&nbsp;
    <?php
    // Print Listboxes for a date selection
    ?><select name='day' class='dropdown' onclick="window.document.updateform.timetonextaction_none[1].checked = true;"><?php
    for ($t_day=1;$t_day<=31;$t_day++)
    {
        echo "<option value=\"$t_day\" ";
        if ($t_day==date("j"))
        {
            echo "selected='selected'";
        }
        echo ">$t_day</option>\n";
    }
    ?></select><select name='month' class='dropdown' onclick="window.document.updateform.timetonextaction_none[1].checked = true;"><?php
    for ($t_month=1;$t_month<=12;$t_month++)
    {
        echo "<option value=\"$t_month\"";
        if ($t_month==date("n"))
        {
            echo " selected='selected'";
        }
        echo ">". date ("F", mktime(0,0,0,$t_month,1,2000)) ."</option>\n";
    }
    ?></select><select name='year' class='dropdown' onclick="window.document.updateform.timetonextaction_none[1].checked = true;"><?php
    for ($t_year=(date("Y")-1);$t_year<=(date("Y")+5);$t_year++)
    {
        echo "<option value=\"$t_year\"";
        if ($t_year==date("Y"))
        {
            echo " selected='selected'";
        }
        echo ">$t_year</option>\n";
    }
    ?></select>
    <br />
    <input checked type="radio" name="timetonextaction_none" onclick="window.document.updateform.timetonextaction_days.value = ''; window.document.updateform.timetonextaction_hours.value = ''; window.document.updateform.timetonextaction_minutes.value = '';" value="None" /> Unspecified
    </td></tr>
    <tr>
    <?php
    // calculate upload filesize
    $j = 0;
    $ext = array("Bytes","KBytes","MBytes","GBytes","TBytes");
    $att_file_size = $CONFIG['upload_max_filesize'];
    while ($att_file_size >= pow(1024,$j)) ++$j;
    $att_file_size = round($att_file_size / pow(1024,$j-1) * 100) / 100 . ' ' . $ext[$j-1];
    ?>
    <th align="right" valign="top">Attach File
    <?php echo "(&lt;{$att_file_size}):</th>";
    ?>
    <td class="shade1"><input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $CONFIG['upload_max_filesize'] ?>" />
    <input class='textbox' type='file' name="attachment" size=40 maxfilesize="<?php echo $CONFIG['upload_max_filesize'] ?>" /></td>
    </tr>
    </table>
    <p class='center'><input name="submit" type="submit" value="Update Incident" /></p>
    </form>
    <?php
    include('incident_html_bottom.inc.php');
}
else
{
    // Update the incident


    $time = time();
    // External variables
    $id = cleanvar($_REQUEST['id']);
    $bodytext = cleanvar($_POST['bodytext']);
    $target = cleanvar($_POST['target']);
    $updatetype = cleanvar($_POST['updatetype']);
    $newstatus = cleanvar($_POST['newstatus']);
    $nextaction = cleanvar($_POST['nxtaction']);
    $newpriority = cleanvar($_POST['newpriority']);
    $cust_vis = cleanvar($_POST['cust_vis']);
    $timetonextaction_none = cleanvar($_POST['timetonextaction_none']);
    $timetonextaction_days = cleanvar($_POST['timetonextaction_days']);
    $timetonextaction_hours = cleanvar($_POST['timetonextaction_hours']);
    $timetonextaction_minutes = cleanvar($_POST['timetonextaction_minutes']);
    $year = cleanvar($_POST['year']);
    $month = cleanvar($_POST['month']);
    $day = cleanvar($_POST['day']);

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
            // $now + ($days * 86400) + ($hours * 3600) + ($minutes * 60);
            $unixdate=mktime(9,0,0,$month,$day,$year);
            $now = time();
            $timeofnextaction = $unixdate;
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
        $bodytext = "Status: ".incidentstatus_name($oldstatus)." -&gt; <b>" . incidentstatus_name($newstatus) . "</b>\n\n" . $bodytext;
    if ($newpriority != incident_priority($id))
        $bodytext = "New Priority: <b>" . priority_name($newpriority) . "</b>\n\n" . $bodytext;
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
    if ($attachment_name!='' && isset($attachment_name)==TRUE)
    {
        $bodytext = "Attachment: <b>Yes</b>\n".$bodytext;
    }
    // Debug
    ## if ($target!='') $bodytext = "Target: $target\n".$bodytext;

    // Check the updatetype field, if it's blank look at the target
    if (empty($updatetype))
    {
        switch($target)
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
        $sql  = "INSERT INTO updates (incidentid, userid, type, bodytext, timestamp, currentstatus, customervisibility, nextaction) ";
        $sql .= "VALUES ('$id', '$sit[2]', '$updatetype', '$bodytext', '$time', '$newstatus', 'show' , '$nextaction')";
    }
    // invisible update
    else
    {
        $sql  = "INSERT INTO updates (incidentid, userid, type, bodytext, timestamp, currentstatus, nextaction) ";
        $sql .= "VALUES ($id, $sit[2], '$updatetype', '$bodytext', $time, '$newstatus', '$nextaction')";
    }
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    $sql = "UPDATE incidents SET status='$newstatus', priority='$newpriority', lastupdated='$time', timeofnextaction='$timeofnextaction' WHERE id='$id'";
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
            $sql  = "INSERT INTO updates (incidentid, userid, type, timestamp, currentowner, currentstatus, customervisibility, sla, bodytext, timesincesla) ";
            $sql .= "VALUES ('$id', '".$sit[2]."', 'slamet', '$now', '".$sit[2]."', '$newstatus', 'show', 'initialresponse','The Initial Response has been made.','0')";
        break;

        case 'probdef':
            $sql  = "INSERT INTO updates (incidentid, userid, type, timestamp, currentowner, currentstatus, customervisibility, sla, bodytext, timesincesla) ";
            $sql .= "VALUES ('$id', '".$sit[2]."', 'slamet', '$now', '".$sit[2]."', '$newstatus', 'show', 'probdef','The problem has been defined.','0')";
        break;

        case 'actionplan':
            $sql  = "INSERT INTO updates (incidentid, userid, type, timestamp, currentowner, currentstatus, customervisibility, sla, bodytext, timesincesla) ";
            $sql .= "VALUES ('$id', '".$sit[2]."', 'slamet', '$now', '".$sit[2]."', '$newstatus', 'show', 'actionplan','An action plan has been made.', '0')";
        break;

        case 'solution':
            $sql  = "INSERT INTO updates (incidentid, userid, type, timestamp, currentowner, currentstatus, customervisibility, sla, bodytext, timesincesla) ";
            $sql .= "VALUES ('$id', '".$sit[2]."', 'slamet', '$now', '".$sit[2]."', '$newstatus', 'show', 'solution','The incident has been resolved or reprioritised.\nThe issue should now be brought to a close or a new problem definition created within the service level.', '0')";
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

    // attach file
    if ($attachment_name!="")
    {
        // Is this the right path?  INL 2Nov05
        $filename = $CONFIG['attachment_fspath'].$attachment_name;

        $mv=move_uploaded_file($attachment, "$filename");
        if (!mv) throw_error('!Error: Problem moving attachment from temp directory:',$filename);

        // Check file size before attaching
        $filesize=filesize($filename);
        if (!$filesize) throw_error("!Error: Problem checking filesize of uploaded attachment:", $filename);
        if ($filesize > $CONFIG['upload_max_filesize'] || filesize($filename)==FALSE)
        {
            throw_error('User Error: Attachment too large or file upload error - size:',filesize($filename));
            // throwing an error isn't the nicest thing to do for the user but there seems to be no way of
            // checking file sizes at the client end before the attachment is uploaded. - INL
        }
        // after update, move the attachment to the incident file attachment directory / timestamp
        if ($filename!="" && file_exists($filename))
        {
            // make incident attachment dir if it doesn't exist

            $umask=umask(0000);
            if (!file_exists($CONFIG['attachment_fspath'] . "$id"))
            {
                $mk=@mkdir($CONFIG['attachment_fspath'] ."$id", 0770);
                //if (!$mk) throw_error('Failed creating incident attachment directory: ',$incident_attachment_fspath .$id);
            }
            $mk=@mkdir($CONFIG['attachment_fspath'] .$id . "/$now", 0770);
            //if (!$mk) throw_error('Failed creating incident attachment (timestamp) directory: ',$incident_attachment_fspath .$id . "/$now");
            umask($umask);

            $filename_parts_array=explode('/', $filename);
            $filename_parts_count=count($filename_parts_array)-1;
            $filename_end_part=$filename_parts_array[$filename_parts_count]; // end part of filename (actual name)
            $rn=rename($filename, $CONFIG['attachment_fspath'] . $id . "/$now/" . $filename_end_part);
            if (!rn) throw_error('Failed moving attachment: ',$CONFIG['attachment_fspath'] .$id . "/$now");
        }
    }
    if (!$result)
    {
        include('includes/incident_html_top.inc');
        echo "<p class='error'>Update Failed</p>\n";
        include('includes/incident_html_bottom.inc');
    }
    else
    {
        journal(4,'Incident Updated', "Incident $id Updated", 2, $id);
        confirmation_page("2", "incident_details.php?id=" . $id, "<h2>Update Successful</h2><p align='center'>Please wait while you are redirected...</p>");
    }
}
?>