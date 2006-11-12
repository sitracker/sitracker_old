<?php
// update.inc.php - Update incident
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}

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



    //-->
    </script>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $id ?>" method="post" name="updateform" id="updateform" enctype="multipart/form-data">
    <table class='vertical'>
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
    <td class="shade1"><textarea name="bodytext" rows="13" cols="50"></textarea></td>
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

    // FIXME fix maximum priority
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
    <input type='file' name="attachment" size="40" maxfilesize="<?php echo $CONFIG['upload_max_filesize'] ?>" /></td>
    </tr>
    </table>
    <p class='center'>
    <input type="hidden" name='action' value="update" />
    <input type="submit" name="submit" value="Update Incident" /></p>
    </form>
<?php


?>