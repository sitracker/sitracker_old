<?php
// add_notice_templates.php - Form for adding notice templates
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

@include ('set_include_path.inc.php');
$permission = 0;

require ('db_connect.inc.php');
require ('functions.inc.php');
// This page requires authentication
require ('auth.inc.php');

include ('htmlheader.inc.php');

// External variables
$submit=$_REQUEST['submit'];

?>
<script type='text/javascript'>
function confirm_submit()
{
    return window.confirm('<?php echo $strAddNoticeConfirm ?>');
}
</script>
<?php

// Show add notice type form
if (empty($submit))
{
    echo "<h2>{$strNewTemplate}</h2>";
    echo "<h5>".sprintf($strMandatoryMarked,"<sup class='red'>*</sup>")."</h5>";
    ?>
    <p align='center'>The following special identifiers can be used in these fields:</p>
    <table align='center' class='vertical'>
    <tr><th>&lt;contactemail&gt;</th><td>Email address of incident contact</td></tr>
    <tr><th>&lt;contactname&gt;</th><td>Full Name of incident contact</td></tr>
    <tr><th>&lt;contactfirstname&gt;</th><td>First Name of incident contact</td></tr>
    <tr><th>&lt;contactsite&gt;</th><td>Site name of incident contact</td></tr>
    <tr><th>&lt;contactphone&gt;</th><td>Phone number of incident contact</td></tr>
    <tr><th>&lt;contactnotify&gt;</th><td>The 'Notify Contact' email address (if set)</td></tr>
    <tr><th>&lt;contactnotify2&gt;</th><td>(or 3 or 4) The 'Notify Contact' email address (if set) of the notify contact recursively</td></tr>
    <tr><th>&lt;incidentid&gt;</th><td>ID number of incident</td></tr>
    <tr><th>&lt;incidentexternalid&gt;</th><td>External ID number of incident</td></tr>
    <tr><th>&lt;incidentexternalengineer&gt;</th><td>Name of External engineer dealing with incident</td></tr>
    <tr><th>&lt;incidentexternalengineerfirstname&gt;</th><td>Name of External engineer dealing with incident</td></tr>
    <tr><th>&lt;incidentexternalemail&gt;</th><td>Email address of External engineer dealing with incident</td></tr>
    <tr><th>&lt;incidentccemail&gt;</th><td>Extra email addresses to CC regarding incidents</td></tr>
    <tr><th>&lt;incidenttitle&gt;</th><td>Title of incident</td></tr>
    <tr><th>&lt;incidentpriority&gt;</th><td>Priority of incident</td></tr>
    <tr><th>&lt;incidentsoftware&gt;</th><td>Skill assigned to an incident</td></tr>
    <tr><th>&lt;incidentowner&gt;</th><td>The full name of the person who owns the incident</td></tr>
    <tr><th>&lt;incidentreassignemailaddress&gt;</th><td>The email address of the person a call has been reassigned to</td></tr>
    <tr><th>&lt;incidentfirstupdate&gt;</th><td>The first customer visible update in the incident log</td></tr>
    <tr><th>&lt;useremail&gt;</th><td>Email address of current user</td></tr>
    <tr><th>&lt;userrealname&gt;</th><td>Real name of current user</td></tr>
    <tr><th>&lt;applicationname&gt;</th><td>Name of this application</td></tr>
    <tr><th>&lt;applicationshortname&gt;</th><td>Short name of this application</td></tr>
    <tr><th>&lt;applicationversion&gt;</th><td>Version number of this application</td></tr>
    <tr><th>&lt;supportemail&gt;</th><td>Technical Support email address</td></tr>
    <tr><th>&lt;supportmanageremail&gt;</th><td>Technical Support mangers email address</td></tr>
    <tr><th>&lt;todaysdate&gt;</th><td>Current Date</td></tr>
    <tr><th>&lt;info1&gt;</th><td>Additional Info #1 (template dependent)</td></tr>
    <tr><th>&lt;info2&gt;</th><td>Additional Info #2 (template dependent)</td></tr>
    <?php
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

    plugin_do('noticetemplate_list');
    ?>
    </table>

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return confirm_submit()">
    <table align='center' class='vertical'>
    <?php
    echo "<tr><th>{$strNoticeTemplate}: <sup class='red'>*</sup></th><td>";
    echo "<input maxlength='50' name='name' size='35' value='{$noticetype['name']}' ";
    // if ($noticetype['type']=='system') echo "readonly='readonly' ";
    echo "/>";
    echo "</td></tr>\n";
    echo "<tr><th>{$strDescription}: <sup class='red'>*</sup></th><td><input name='description' size='50' value=\"{$noticetype["description"]}\" /></td></tr>\n";
    echo "<tr><th>{$strType} <sup class='red'>*</sup></th><td><input name='type' size='50' value=\"{$noticetype["type"]}\" /></td></tr>\n";
    echo "<tr><th>{$strText} <sup class='red'>*</sup></th><td>";
    echo "<textarea>{$noticetype["text"]}</textarea></td></tr>\n";
    echo "<tr><th>{$strLinkText}</th><td><input maxlength='100' name='linktext' size='30' value=\"{$noticetype["linktext"]}\" /></td></tr>\n";
    echo "<tr><th>{$strLink}</th><td><input maxlength='100' name='link' size='30' value=\"{$noticetype["link"]}\" /></td></tr>\n";
    echo "</td></tr>";
    echo "</table>";?>
    <p align='center'><input name="submit" type="submit" value="Add It" /></p>
    </form>
    <?php
    include ('htmlfooter.inc.php');
}
else
{
    // Add new email template

    // External Variables
    // These variables may contain templates so don't strip tags
    $name = mysql_real_escape_string($_POST['name']);
    $description = mysql_real_escape_string($_POST['description']);
    $type = cleanvar($_POST['type']);
    $text = cleanvar($_POST['text']);
    $linktext = cleanvar($_POST['linktext']);
    $link = cleanvar($_POST['link']);
    // check form input
    $errors = 0;
    //TODO form checking
    if ($errors == 0)
    {
        $sql  = "INSERT INTO `{$dbNoticeTemplates}` (name, description, type, text, linktext, link) ";
        $sql .= "VALUES ('$name', '$description', '$type', '$text', '$linktext', '$link')";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        if (!$result) echo "<p class='error'>Addition of Notice Template Failed\n";
        else
        {
            $id=mysql_insert_id();
            journal(CFG_LOGGING_FULL, 'Administration', 'Notice template $id was added', CFG_JOURNAL_ADMIN, $id);
            html_redirect("edit_notice_templates.php?action=showform");
        }
    }
}
?>
