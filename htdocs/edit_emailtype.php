<?php
// edit_emailtype.php - Form for editing email templates
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

@include ('set_include_path.inc.php');
$permission = 17; // Edit Email Template

require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

// FIXME i18n Whole page

// External variables
$id = cleanvar($_REQUEST['id']);
$action = $_REQUEST['action'];
$templatetype = cleanvar($_REQUEST['template']);

if (empty($action) OR $action == "showform")
{
    // Show select email type form
    include ('htmlheader.inc.php');

    echo "<h2>Templates</h2>";
    echo "<p align='center'>Please be very careful when editing existing templates, {$CONFIG['application_shortname']} relies on some of these templates to
    send emails out automatically, if in doubt - seek advice.</p>";
    echo "<p align='center'>{$strTemplatesShouldNotBeginWith}</p>";
    echo "<p align='center'><a href='triggers.php'>{$strTriggers}</a> | <a href='add_emailtype.php?action=showform'>{$strAddEmailTemplate}</a> | ";
    echo "<a href='edit_global_signature.php'>{$strEditGlobalSignature}</a></p>";

    $sql = "SELECT * FROM `{$dbEmailType}` ORDER BY id";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    while ($email = mysql_fetch_object($result))
    {
        $templates[$email->id] = array('id' => $email->id, 'template' => 'email', 'type' => $email->type, 'desc' => $email->description);
    }
    $sql = "SELECT * FROM `{$dbNoticeTemplates}` ORDER BY id";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    while ($notice = mysql_fetch_object($result))
    {
        $templates[$notice->name] = array('id' => $notice->name, 'template' => 'notice', 'type' => $notice->type, 'desc' => $notice->description);
    }
    ksort($templates);
    $shade='shade1';
    echo "<table align='center'>";
    echo "<tr><th>{$strType}</th><th>{$strID}</th><th>{$strDescription}</th><th>{$strOperation}</th></tr>";
    foreach ($templates AS $template)
    {
        echo "<tr class='{$shade}'>";
        echo "<td>{$template['type']} {$template['template']}</td>";
        echo "<td>{$template['id']}</td>";
        echo "<td>{$template['desc']}</td>";
        echo "<td><a href='{$_SERVER['PHP_SELF']}?id={$template['id']}&amp;action=edit&amp;template={$template['template']}'>{$strEdit}</a></td>";
        echo "</tr>\n";
        if ($shade=='shade1') $shade='shade2';
        else $shade='shade1';
    }
    echo "</table>";
//     echo "<pre>".print_r($template,true)."</pre>";
    include ('htmlfooter.inc.php');
}
elseif ($action == "edit")
{
    // Retrieve the template from the database, whether it's email or notice
    switch ($templatetype)
    {
        case 'email':
            $sql = "SELECT * FROM `{$dbEmailType}` WHERE id='$id'";
            $title = "{$strEdit}: $strEmailTemplate";
            $action = 'ACTION_EMAIL';
            break;

        case 'notice':
        default:
            $sql = "SELECT * FROM `{$dbNoticeTemplates}` WHERE id='$id' LIMIT 1";
            $title = "{$strEdit}: Notice Template"; // FIXME i18n edit notice template
            $action = 'ACTION_NOTICE';
    }
    $result = mysql_query($sql);
    $template = mysql_fetch_object($result);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    include ('htmlheader.inc.php');

    if (mysql_num_rows($result) > 0)
    {
        echo "<h2>{$title}</h2>";
        echo "<h5>".sprintf($strMandatoryMarked,"<sup class='red'>*</sup>")."</h5>";
        echo "<p align='center'>{$strListOfSpecialIdentifiersEmail}.</p>";
        echo "<div style='width: 48%; float: left;'>";
        echo "<form name='edittemplate' action='{$_SERVER['PHP_SELF']}?action=update' method='post' onsubmit='return confirm_submit(\"{$strAreYouSureEditEmailTemplate}\")'>";
        echo "<table class='vertical' width='100%'>";

        $tsql = "SELECT * FROM `{$dbTriggers}` WHERE action = '{$action}' AND template = '$id' LIMIT 1";
        $tresult = mysql_query($tsql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        if (mysql_num_rows($tresult) >= 1)
        {
            $trigaction = mysql_fetch_object($tresult);
            echo "<tr><th>{$strTrigger}</th><td>".trigger_description($triggerarray[$trigaction->triggerid])."<br /><br />";
            echo triggeraction_description($trigaction)."</td></tr>";
        }
        else
        {
            echo "<tr><th>{$strTrigger}</th><td>{$strNone}</td></tr>\n";
        }
        echo "<tr><th>{$strID}: <sup class='red'>*</sup></th><td>";
        echo "<input maxlength='50' name='name' size='35' value='{$template->id} 'readonly='readonly' disabled='disabled' /></td></tr>\n";
        echo "<tr><th>Template Type:</th><td>{$template->type}";  // FIXME Temporary, remove before release
        echo "<tr><th>{$strDescription}: <sup class='red'>*</sup></th><td><textarea name='description' cols='50' rows='5'>{$template->description}</textarea></td></tr>\n";
        switch ($templatetype)
        {
            case 'email':

                echo "<tr><th colspan='2'>{$strEmail}</th></tr>"; // FIXME i18n defaults
                echo "<tr><th>{$strTo}: <sup class='red'>*</sup></th><td><input maxlength='100' name='tofield' size='40' value=\"{$template->tofield}\" /></td></tr>\n";
                echo "<tr><th>{$strFrom}: <sup class='red'>*</sup></th><td><input maxlength='100' name='fromfield' size='40' value=\"{$template->fromfield}\" /></td></tr>\n";
                echo "<tr><th>{$strReplyTo}: <sup class='red'>*</sup></th><td><input maxlength='100' name='replytofield' size='40' value=\"{$template->replytofield}\" /></td></tr>\n";
                echo "<tr><th>{$strCC}:</th><td><input maxlength='100' name='ccfield' size='40' value=\"{$template->ccfield}\" /></td></tr>\n";
                echo "<tr><th>{$strBCC}:</th><td><input maxlength='100' name='bccfield' size='40' value=\"{$template->bccfield}\" /></td></tr>\n";
                echo "<tr><th>{$strSubject}:</th><td><input maxlength='255' name='subjectfield' size='60' value=\"{$template->subjectfield}\" /></td></tr>\n";
                break;

            case 'notice':

                echo "<tr><th>{$strNotice}</th><td>TODO</td></tr>\n";

        }

        // Set template type to the trigger type if no type is already specified
        if (empty($template->type)) $template->type = $triggerarray[$trigaction->triggerid]['type'];

        if ($trigaction AND $template->type != $triggerarray[$trigaction->triggerid]['type']) echo "<p class='warning'>Trigger type mismatch</p>";
        echo "</td></tr>\n";

        echo "</td></tr>\n";
        if ($template->type=='incident')
        {
            echo "<tr><th></th><td><label><input type='checkbox' name='storeinlog' value='Yes' ";
            if ($template->storeinlog == 'Yes')
            {
                echo "checked='checked'";
            }
            echo " /> {$strStoreInLog}</label>";
            echo " &nbsp; (<input type='checkbox' name='cust_vis' value='yes' ";
            if ($emailtype['customervisibility'] == 'show')
            {
                echo "checked='checked'";
            }
            echo " /> {$strVisibleToCustomer})";
            echo "</td></tr>\n";
        }
        if ($templatetype=='email') $body = $template->body;
        else $body = $template->text;
        echo "<tr><th>{$strText}</th><td><textarea name='bodytext' rows='20' cols='50'>{$body}</textarea></td>";
        echo "</table>\n";

        echo "<p>";
        echo "<input name='type' type='hidden' value='{$emailtype['type']}' />";
        echo "<input name='id' type='hidden' value='{$id}' />";
        echo "<input name='submit' type='submit' value=\"{$strSave}\" />";
        echo "</p>\n";
        if ($emailtype['type']=='user') echo "<p align='center'><a href='{$_SERVER['PHP_SELF']}?action=delete&amp;id={$id}'>{$strDelete}</a></p>";
        echo "</form>";
        echo "</div>";


            // FIXME i18n email templates
        echo "<div style='width: 48%; float: right; border: 1px solid #CCCCFF; padding: 10px;'>";
        echo "<h4>Template Variables</h4>"; // FIXME template variables
        echo "<p align='center'>{$strFollowingSpecialIdentifiers}</p>";
        echo "<dl>";
        foreach ($triggertypevars[$template->type] AS $triggertypevar => $identifier)
        {
            echo "<dt><code>{$identifier}</code></dt>";
            if (!empty($ttvararray[$identifier]['description'])) echo "<dd>{$ttvararray[$identifier]['description']}";
            echo "<br />";
        }
        echo "</dl>";
        plugin_do('emailtemplate_list');
        echo "</table>\n";
        echo "</div>";

        echo "<p style='clear:both; margin-top: 2em;' align='center'><a href='{$_SERVER['PHP_SELF']}'>{$strBackToList}</a></p>";

        include ('htmlfooter.inc.php');
    }
    else
    {
        echo "<p class='error'>{$strMustSelectEmailTemplate}</p>\n";
    }
}
elseif ($action == "delete")
{
    if (empty($id) OR is_numeric($id)==FALSE)
    {
        // id must be filled and be a number
        header("Location: {$_SERVER['PHP_SELF']}?action=showform");
        exit;
    }
    // We only allow user templates to be deleted
    $sql = "DELETE FROM `{$dbEmailType}` WHERE id='$id' AND type='user' LIMIT 1";
    mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    header("Location: {$_SERVER['PHP_SELF']}?action=showform");
    exit;
}
elseif ($action == "update")
{
    // Add new email type

    // External variables
    $name = cleanvar($_POST['name']);
    $description = cleanvar($_POST['description']);
    // We don't strip tags because that would also strip our special tags
    $tofield = mysql_real_escape_string($_POST['tofield']);
    $fromfield = mysql_real_escape_string($_POST['fromfield']);
    $replytofield = mysql_real_escape_string($_POST['replytofield']);
    $ccfield = mysql_real_escape_string($_POST['ccfield']);
    $bccfield = mysql_real_escape_string($_POST['bccfield']);
    $subjectfield = mysql_real_escape_string($_POST['subjectfield']);
    $bodytext = mysql_real_escape_string($_POST['bodytext']);
    $cust_vis = cleanvar($_POST['cust_vis']);
    $storeinlog = cleanvar($_POST['storeinlog']);
    $id = cleanvar($_POST['id']);
    $type = cleanvar($_POST['type']);

    // check form input
    $errors = 0;
    // check for blank name
    if ($name == "")
    {
        $errors = 1;
        echo "<p class='error'>You must enter a name for the email type</p>\n";
    }
    // check for blank to field
    if ($tofield == "")
    {
        $errors = 1;
        echo "<p class='error'>You must enter a 'To' field</p>\n";
    }
    // check for blank from field
    if ($fromfield == "")
    {
        $errors = 1;
        echo "<p class='error'>You must enter a 'From' field</p>\n";
    }
    // check for blank reply to field
    if ($replytofield == "")
    {
        $errors = 1;
        echo "<p class='error'>You must enter a 'Reply To' field</p>\n";
    }
    // check for blank type
    if ($type == "")
    {
        $errors = 1;
        trigger_error("Invalid input, blank type",E_USER_ERROR);
    }
    if ($type == 'system' AND is_numeric($name))
    {
        $errors++;
        echo "<p class='error'>System email templates cannot have a name that consists soley of numbers</p>\n";
    }

    // User templates may not have _ (underscore) in their names, we replace with spaces
    // in contrast system templates must have _ (underscore) instead of spaces, so we do a replace
    // the other way around for those
    // We do this to help prevent user templates having names that clash with system templates
    if ($type == 'user') $name = str_replace('_', ' ', $name);
    else $name = str_replace(' ', '_', strtoupper(trim($name)));

    if ($cust_vis=='yes') $cust_vis='show';
    else $cust_vis='hide';

    if ($storeinlog=='Yes') $storeinlog='Yes';
    else $storeinlog='No';


    if ($errors == 0)
    {
        $sql  = "UPDATE emailtype SET name='$name', description='$description', tofield='$tofield', fromfield='$fromfield', ";
        $sql .= "replytofield='$replytofield', ccfield='$ccfield', bccfield='$bccfield', subjectfield='$subjectfield', ";
        $sql .= "body='$bodytext', customervisibility='$cust_vis', storeinlog='$storeinlog' ";
        $sql .= "WHERE id='$id' LIMIT 1";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        if (!$result) echo "<p class='error'>Update of Email Type Failed\n";
        else
        {
            journal(CFG_LOGGING_NORMAL, 'Email Template Updated', "Email Template {$type} was modified", CFG_JOURNAL_ADMIN, $type);
            html_redirect("edit_emailtype.php");
        }
    }
}
?>