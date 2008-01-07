<?php
// add_emailtype.php - Form for adding email templates
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

@include('set_include_path.inc.php');
$permission=16; // Add Email Template

require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

include('htmlheader.inc.php');

// External variables
$submit=$_REQUEST['submit'];

// Show add email type form
if (empty($submit))
{
    echo "<h2>{$strNewTemplate}</h2>";
    echo "<h5>".sprintf($strMandatoryMarked,"<sup class='red'>*</sup>")."</h5>";

    echo "<p align='center'>The following special identifiers can be used in these fields:</p>";
    echo "<table align='center' class='vertical'>";
    echo "<tr><th>&lt;contactemail&gt;</th><td>Email address of incident contact</td></tr>";
    echo "<tr><th>&lt;contactname&gt;</th><td>Full Name of incident contact</td></tr>";
    echo "<tr><th>&lt;contactfirstname&gt;</th><td>First Name of incident contact</td></tr>";
    echo "<tr><th>&lt;contactsite&gt;</th><td>Site name of incident contact</td></tr>";
    echo "<tr><th>&lt;contactphone&gt;</th><td>Phone number of incident contact</td></tr>";
    echo "<tr><th>&lt;contactnotify&gt;</th><td>The 'Notify Contact' email address (if set)</td></tr>";
    echo "<tr><th>&lt;contactnotify2&gt;</th><td>(or 3 or 4) The 'Notify Contact' email address (if set) of the notify contact recursively</td></tr>";
    echo "<tr><th>&lt;incidentid&gt;</th><td>ID number of incident</td></tr>";
    echo "<tr><th>&lt;incidentexternalid&gt;</th><td>External ID number of incident</td></tr>";
    echo "<tr><th>&lt;incidentexternalengineer&gt;</th><td>Name of External engineer dealing with incident</td></tr>";
    echo "<tr><th>&lt;incidentexternalengineerfirstname&gt;</th><td>Name of External engineer dealing with incident</td></tr>";
    echo "<tr><th>&lt;incidentexternalemail&gt;</th><td>Email address of External engineer dealing with incident</td></tr>";
    echo "<tr><th>&lt;incidentccemail&gt;</th><td>Extra email addresses to CC regarding incidents</td></tr>";
    echo "<tr><th>&lt;incidenttitle&gt;</th><td>Title of incident</td></tr>";
    echo "<tr><th>&lt;incidentpriority&gt;</th><td>Priority of incident</td></tr>";
    echo "<tr><th>&lt;incidentsoftware&gt;</th><td>Skill assigned to an incident</td></tr>";
    echo "<tr><th>&lt;incidentowner&gt;</th><td>The full name of the person who owns the incident</td></tr>";
    echo "<tr><th>&lt;incidentreassignemailaddress&gt;</th><td>The email address of the person a call has been reassigned to</td></tr>";
    echo "<tr><th>&lt;incidentfirstupdate&gt;</th><td>The first customer visible update in the incident log</td></tr>";
    echo "<tr><th>&lt;useremail&gt;</th><td>Email address of current user</td></tr>";
    echo "<tr><th>&lt;userrealname&gt;</th><td>Real name of current user</td></tr>";
    echo "<tr><th>&lt;signature&gt;</th><td>Signature of current user</td></tr>";
    echo "<tr><th>&lt;novellid&gt;</th><td>Novell ID of current user</td></tr>";
    echo "<tr><th>&lt;microsoftid&gt;</th><td>Microsoft ID of current user</td></tr>";
    echo "<tr><th>&lt;dseid&gt;</th><td>DSE ID of current user</td></tr>";
    echo "<tr><th>&lt;cheyenneid&gt;</th><td>Cheyenne ID of current user</td></tr>";
    echo "<tr><th>&lt;applicationname&gt;</th><td>Name of this application</td></tr>";
    echo "<tr><th>&lt;applicationshortname&gt;</th><td>Short name of this application</td></tr>";
    echo "<tr><th>&lt;applicationversion&gt;</th><td>Version number of this application</td></tr>";
    echo "<tr><th>&lt;supportemail&gt;</th><td>Technical Support email address</td></tr>";
    echo "<tr><th>&lt;supportmanageremail&gt;</th><td>Technical Support mangers email address</td></tr>";
    echo "<tr><th>&lt;globalsignature&gt;</th><td>Current Global Signature</td></tr>";
    echo "<tr><th>&lt;todaysdate&gt;</th><td>Current Date</td></tr>";
    echo "<tr><th>&lt;info1&gt;</th><td>Additional Info #1 (template dependent)</td></tr>";
    echo "<tr><th>&lt;info2&gt;</th><td>Additional Info #2 (template dependent)</td></tr>";

    echo "<tr><th>&lt;useremail&gt;</th><td>The current users email address</td></tr>";
    echo "<tr><th>&lt;userrealname&gt;</th><td>The full name of the current user</td></tr>";
    echo "<tr><th>&lt;salespersonemail&gt;</th><td>The email address of the salesperson attached to the contacts site</td></tr>";
    echo "<tr><th>&lt;applicationname&gt;</th><td>'{$CONFIG['application_name']}'</td></tr>";
    echo "<tr><th>&lt;applicationversion&gt;</th><td>'{$application_version_string}'</td></tr>";
    echo "<tr><th>&lt;applicationshortname&gt;</th><td>'{$CONFIG['application_shortname']}'</td></tr>";
    echo "<tr><th>&lt;supportemail&gt;</th><td>The support email address</td></tr>";
    echo "<tr><th>&lt;signature&gt;</th><td>The current users signature</td></tr>";
    echo "<tr><th>&lt;globalsignature&gt;</th><td>The global signature</td></tr>";
    echo "<tr><th>&lt;todaysdate&gt;</th><td>Todays date</td></tr>";

    plugin_do('emailtemplate_list');

    echo "</table>";

    echo "<form action='{$_SERVER['PHP_SELF']}' method='post' onsubmit='return confirm_submit(\"{$strAddEmailConfirm}\")'>";
    echo "<table class='vertical'>";
    echo "<tr><th>Name Of Email Template: <sup class='red'>*</sup></th><td><input maxlength='50' name='name' size='30' /></td></tr>";
    echo "<tr><th>Description: <sup class='red'>*</sup></th><td><input maxlength='50' name='description' size='30' /></td></tr>";
    echo "<tr><th>'To' Field: <sup class='red'>*</sup></th><td><input maxlength='100' name='tofield' size='30' /></td></tr>";
    echo "<tr><th>'From' Field: <sup class='red'>*</sup></th><td><input maxlength='100' name='fromfield' size='30' /></td></tr>";
    echo "<tr><th>'Reply To' Field: <sup class='red'>*</sup></th><td><input maxlength='100' name='replytofield' size='30' /></td></tr>";
    echo "<tr><th>'CC' Field:</th><td><input maxlength='100' name='ccfield' size='30' /></td></tr>";
    echo "<tr><th>'BCC' Field:</th><td><input maxlength='100' name='bccfield' size='30' /></td></tr>";
    echo "<tr><th>'Subject' Field:</th><td><input maxlength='255' name='subjectfield' size='30' /></td></tr>";
    echo "<tr><th>Body text:</th><td><textarea name='bodytext' rows='20' cols='60'></textarea></td></tr>";
    echo "<tr><th>Store in Log:</th><td><input type='checkbox' name='storeinlog' value='Yes' checked='checked' /> Store the email in the incident log</td></tr>";
    echo "<tr><th>Visibility:</th><td><input type='checkbox' name='cust_vis' checked='checked' value='yes' /> Make the update to the incident log visible to the customer</td></tr>";
    echo "</table>";
    echo "<p align='center'><input name='submit' type='submit' value='{$strAdd}' /></p>";
    echo "</form>";
    include('htmlfooter.inc.php');
}
else
{
    // Add new email template

    // External Variables
    // These variables may contain templates so don't strip tags
    $name = mysql_real_escape_string($_POST['name']);
    $description = mysql_real_escape_string($_POST['description']);
    $tofield = mysql_real_escape_string($_POST['tofield']);
    $fromfield = mysql_real_escape_string($_POST['fromfield']);
    $replytofield = mysql_real_escape_string($_POST['replytofield']);
    $ccfield = mysql_real_escape_string($_POST['ccfield']);
    $bccfield = mysql_real_escape_string($_POST['bccfield']);
    $subjectfield = mysql_real_escape_string($_POST['subjectfield']);
    $bodytext = mysql_real_escape_string($_POST['bodytext']);
    $storeinlog = mysql_real_escape_string($_POST['storeinlog']);
    $cust_vis = mysql_real_escape_string($_POST['cust_vis']);

    // check form input
    $errors = 0;
    // check for blank name
    if ($name == "")
    {
        $errors++;
        echo "<p class='error'>You must enter a name for the email type</p>\n";
    }
    // check for blank tofield
    if ($tofield == "")
    {
        $errors++;
        echo "<p class='error'>You must enter a 'To' field</p>\n";
    }
    // check for blank name
    if ($fromfield == "")
    {
        $errors++;
        echo "<p class='error'>You must enter a 'From' field</p>\n";
    }
    if ($errors == 0)
    {
        if ($_REQUEST['cust_vis']=='yes') $cust_vis='show';
        else $cust_vis='hide';
        if ($_REQUEST['storeinlog']=='yes') $storeinlog='Yes';
        else $storeinlog='No';

        $sql  = "INSERT INTO emailtype (name, description, tofield, fromfield, replytofield, ccfield, bccfield, subjectfield, body, customervisibility, storeinlog) ";
        $sql .= "VALUES ('$name', '$description', '$tofield', '$fromfield', '$replytofield', '$ccfield', ";
        $sql .= "'$bccfield', '$subjectfield', '$bodytext', '$cust_vis', '$storeinlog')";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        if (!$result) echo "<p class='error'>Addition of Email Type Failed\n";
        else
        {
            $id=mysql_insert_id();
            journal(CFG_LOGGING_FULL, 'Administration', 'Email template $id was added', CFG_JOURNAL_ADMIN, $id);
            html_redirect("edit_emailtype.php?action=showform");
        }
    }
}
?>
