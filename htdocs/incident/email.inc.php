<?php
// email.inc.php - Choose email type tab (new style)
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Authors: Paul Heaney <paulheaney[at]users.sourceforge.net>
//          Ivan Lucas <ivanlucas[at]users.sourceforge.net>

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
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $id ?>" name="emailform" method="post">
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
        <input type="radio" name="timetonextaction_none" value="date" />On specified Date<br />&nbsp;&nbsp;&nbsp;
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
        <input type='hidden' name='action' value='email' />
        <input name="submit1" type="submit" value="Continue" /></p>
        </form>
        <?php


?>