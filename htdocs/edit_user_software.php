<?php
// edit_user_software.php - Form to set users skills
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

if (empty($_REQUEST['user'])
    OR $_REQUEST['user']=='current'
    OR $_REQUEST['userid']==$_SESSION['userid']) $permission=58; // Edit your software skills
else $permission=59; // Manage users software skills

require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

// External Variables
$submit=$_REQUEST['submit'];
if (empty($_REQUEST['user']) || $_REQUEST['user']=='current') $user=$sit[2];
else $user = cleanvar($_REQUEST['user']);

if (empty($submit))
{
    include('htmlheader.inc.php');
    // This Javascript code placed in the public domain at http://www.irt.org/script/1265.htm
    // "Code examples on irt.org can be freely copied and used."
    ?>
    <script type="text/javascript">
    <!--
    function deleteOption(object,index)
    {
        object.options[index] = null;
    }

    function addOption(object,text,value)
    {
        var defaultSelected = true;
        var selected = true;
        var optionName = new Option(text, value, defaultSelected, selected)
        object.options[object.length] = optionName;
    }

    function copySelected(fromObject,toObject)
    {
        for (var i=0, l=fromObject.options.length;i<l;i++)
        {
            if (fromObject.options[i].selected)
                addOption(toObject,fromObject.options[i].text,fromObject.options[i].value);
        }
        for (var i=fromObject.options.length-1;i>-1;i--) {
            if (fromObject.options[i].selected)
                deleteOption(fromObject,i);
        }
    }

    function copyAll(fromObject,toObject) {
        for (var i=0, l=fromObject.options.length;i<l;i++) {
            addOption(toObject,fromObject.options[i].text,fromObject.options[i].value);
        }
        for (var i=fromObject.options.length-1;i>-1;i--) {
            deleteOption(fromObject,i);
        }
    }

    function populateHidden(fromObject,toObject) {
        var output = '';
        for (var i=0, l=fromObject.options.length;i<l;i++) {
                output += escape(fromObject.name) + '=' + escape(fromObject.options[i].value) + '&';
        }
        // alert(output);
        toObject.value = output;
    }
    //--></script>
    <?php

    $sql = "SELECT * FROM usersoftware, software WHERE usersoftware.softwareid=software.id AND userid='$user' ORDER BY name";
    $result = mysql_query($sql);
    if (mysql_num_rows($result) >= 1)
    {
        while ($software = mysql_fetch_object($result))
        {
            $expertise[]=$software->id;
        }
    }
    echo "<h2>Software Skills for ".user_realname($user)."</h2>";
    echo "<p align='center'>Select the software that you have the skills to support</p>";
    echo "<form name='softwareform' action='{$_SERVER['PHP_SELF']}' method='post' onsubmit=\"populateHidden(document.softwareform.elements['expertise[]'],document.softwareform.choices)\">";
    echo "<table align='center'>";
    echo "<tr><th>NO Skills</th><th>&nbsp;</th><th>HAVE Skills</th></tr>";
    echo "<tr><td align='center' width='300' class='shade1'>";
    $sql = "SELECT * FROM software ORDER BY name";
    $result = mysql_query($sql);
    if (mysql_num_rows($result) >= 1)
    {
        echo "<select name='noskills[]' multiple='multiple' size='20'>";
        while ($software = mysql_fetch_object($result))
        {
            if (is_array($expertise)) { if (!in_array($software->id,$expertise)) echo "<option value='{$software->id}'>$software->name</option>\n";  }
            else  echo "<option value='{$software->id}'>$software->name</option>\n";
        }
        echo "</select>";
    }
    else echo "<p class='error'>No software defined</p>";
    echo "</td>";
    echo "<td class='shade2'>";
    echo "<input type='button' value='&gt;' title='Add Selected' onclick=\"copySelected(this.form.elements['noskills[]'],this.form.elements['expertise[]'])\" /><br />";
    echo "<input type='button' value='&lt;' title='Remove Selected' onclick=\"copySelected(this.form.elements['expertise[]'],this.form.elements['noskills[]'])\" /><br />";
    echo "<input type='button' value='&gt;&gt;' title='Add All' onclick=\"copyAll(this.form.elements['noskills[]'],this.form.elements['expertise[]'])\" /><br />";
    echo "<input type='button' value='&lt;&lt;' title='Remove All' onclick=\"copyAll(this.form.elements['expertise[]'],this.form.elements['noskills[]'])\" /><br />";
    echo "</td>";
    echo "<td class='shade1'>";
    $sql = "SELECT * FROM usersoftware, software WHERE usersoftware.softwareid=software.id AND userid='{$user}' ORDER BY name";
    $result = mysql_query($sql);
    echo "<select name='expertise[]' multiple='multiple' size='20'>";
    while ($software = mysql_fetch_object($result))
    {
        echo "<option value='{$software->id}'>$software->name</option>\n";
    }
    // echo "<option value='0'>---</option>\n";
    echo "</select>";
    echo "<input type='hidden' name='userid' value='{$user}' />";
    echo "</td></tr>\n";
    ?>
    </table>
    <input type="hidden" name="choices" />
    <p align='center'><input name="submit" type="submit" value="Save Changes" /></p>
    </form>
    <?php
    include('htmlfooter.inc.php');
}
else
{
    // Update user profile
    $selections=urldecode($_POST['choices']);
    parse_str($selections);

    $expertise = $_POST['expertise'];
    $noskills = $_POST['noskills'];

    // remove existing selections first

    // FIXME: This is going to wipe the backup engineer settings, need to fix this
    // INL 21Dec04

    // FIXME: whatabout cases where the user is a backup for one of the products
    // he removes? or if the backup user leaves the company?

    //$sql = "DELETE FROM usersoftware WHERE userid='{$_POST['userid']}'";
    //mysql_query($sql);
    //if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    if (is_array($expertise))
    {
        $expertise=array_unique($expertise);
        foreach ($expertise AS $value)
        {
            $checksql = "SELECT userid FROM usersoftware WHERE userid='{$_POST['userid']}' AND softwareid='$value' LIMIT 1";
            $checkresult=mysql_query($checksql);
            if (mysql_num_rows($checkresult)< 1)
            {
                $sql = "INSERT DELAYED INTO usersoftware (userid, softwareid) VALUES ('{$_POST['userid']}', '$value')";
                // echo "$sql <br />";
                mysql_query($sql);
                if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            }
            $softlist[]=$value;
        }

        // Make sure we're not being backup support for all the software we have no skills in.
        if (is_array($noskills))
        {
            $noskills=array_unique($noskills);
            foreach ($noskills AS $value)
            {
                // Remove the software listed that we don't support
                $sql = "DELETE FROM usersoftware WHERE userid='{$_POST['userid']}' AND softwareid='$value' LIMIT 1";
                mysql_query($sql);
                if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

                // If we are providing backup for a skill we don't have - reset that back to nobody providing backup
                $sql = "UPDATE usersoftware SET backupid='0' WHERE backupid='{$_POST['userid']}' AND softwareid='$value' LIMIT 1";
                mysql_query($sql);
                if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            }
        }

        journal(CFG_LOGGING_MAX,'Skillset Updated',"Users Skillset was Changed",CFG_JOURNAL_USER,0);
    }
    // Have a look to see if any of the software we support is lacking a backup engineer
    $sql = "SELECT userid FROM usersoftware WHERE userid='{$_POST['userid']}' AND backupid='0' LIMIT 1";
    $result=mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $lacking=mysql_num_rows($result);
    if ($lacking >= 1)
    {
        confirmation_page("3", "edit_backup_users.php?user={$_POST['userid']}", "<h2>Changes Saved</h2><h3>You should now define a backup engineer for each piece of software.</h3><h5>Please wait while you are redirected...</h5>");
    }
    else
    {
        if ($_POST['userid']==$_COOKIE['sit'][2])
            confirmation_page("2", "control_panel.php", "<h2>Update Successful</h2><p align='center'>Please wait while you are redirected...</p>");
        else
            confirmation_page("2", "manage_users.php", "<h2>Update Successful</h2><p align='center'>Please wait while you are redirected...</p>");
    }
}
?>
