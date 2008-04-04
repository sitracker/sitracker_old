<?php
// templates.php - Manage email and notice templates
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

@include ('set_include_path.inc.php');
$permission = 17; // Edit Template

require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

// FIXME i18n Whole page

// External variables
$id = cleanvar($_REQUEST['id']);
$action = $_REQUEST['action'];
$templatetype = cleanvar($_REQUEST['template']);

if (empty($action) OR $action == 'showform' OR $action == 'list')
{
    // Show select email type form
    include ('htmlheader.inc.php');

    echo "<h2>Templates</h2>";
    echo "<p align='center'>Please be very careful when editing existing templates, {$CONFIG['application_shortname']} relies on some of these templates to
    send emails out automatically, if in doubt - seek advice.</p>";
    echo "<p align='center'>{$strTemplatesShouldNotBeginWith}</p>";
    echo "<p align='center'><a href='triggers.php'>{$strTriggers}</a> | <a href='add_emailtype.php?action=showform'>{$strAddEmailTemplate}</a> | ";
    echo "<a href='edit_global_signature.php'>{$strEditGlobalSignature}</a></p>";

    $sql = "SELECT * FROM `{$dbEmailTemplates}` ORDER BY id";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    while ($email = mysql_fetch_object($result))
    {
        $templates[$email->id] = array('id' => $email->id, 'template' => 'email', 'name'=> $email->name,'type' => $email->type, 'desc' => $email->description);
    }
    $sql = "SELECT * FROM `{$dbNoticeTemplates}` ORDER BY id";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    while ($notice = mysql_fetch_object($result))
    {
        $templates[$notice->name] = array('id' => $notice->id, 'template' => 'notice', 'name'=> $notice->name, 'type' => $notice->type, 'desc' => $notice->description);
    }
    ksort($templates);
    $shade='shade1';
    echo "<table align='center'>";
    echo "<tr><th>{$strType}</th><th>{$strID}</th><th>{$strTemplate}</th><th>{$strDescription}</th><th>{$strOperation}</th></tr>";
    foreach ($templates AS $template)
    {
        echo "<tr class='{$shade}'>";
        echo "<td>{$template['type']} {$template['template']}</td>";
        echo "<td>{$template['id']}</td>";
        echo "<td>{$template['name']}</td>";
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
    ?>
    <script type='text/javascript'>
    //<![CDATA[

    function recordFocusElement(element)
    {
        $('focuselement').value = element.identify();
        $('templatevariables').show();
    }

    function clearFocusElement()
    {
        $('focuselement').value = '';
        $('templatevariables').hide();
    }

    function insertTemplateVar(tvar)
    {
        var element = $('focuselement').value;
        if (element.length > 0)
        {
            var start = $(element).selectionStart;
            var end = $(element).selectionEnd;
//             alert('start:' + start + '  end: ' + end + 'len: ' + $(element).textLength);
            $(element).value = $(element).value.substring(0, start) + tvar + $(element).value.substring(end, $(element).textLength);
        }
        else
        {
            alert('Select a field that supports template variables, then click a variable to insert it');
        }
    }
//]]>
</script>
    <?php


    // Retrieve the template from the database, whether it's email or notice
    switch ($templatetype)
    {
        case 'email':
            $sql = "SELECT * FROM `{$dbEmailTemplates}` WHERE id='$id'";
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
        echo "<p align='center'>".sprintf($strMandatoryMarked,"<sup class='red'>*</sup>")."</p>";
        echo "<div style='width: 48%; float: left;'>";
        echo "<form name='edittemplate' action='{$_SERVER['PHP_SELF']}?action=update' method='post' onsubmit=\"return confirm_action('{$strAreYouSureMakeTheseChanges}')\">";
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

        // Set template type to the trigger type if no type is already specified
        if (empty($template->type)) $template->type = $triggerarray[$trigaction->triggerid]['type'];


        echo "<tr><th>{$strID}: <sup class='red'>*</sup></th><td>";
        echo "<input maxlength='50' name='name' size='5' value='{$template->id} 'readonly='readonly' disabled='disabled' /></td></tr>\n";
        echo "<tr><th>Template Type:</th><td>{$template->type}";  // FIXME Temporary, remove before release
        if ($template->type == 'user') $required = array('incidentid', 'userid');
        else $required = $triggerarray[$trigaction->triggerid]['required'];
        echo "<br />required: ".print_r($required, true)."<br />";
        echo "</td><tr>";

        echo "<tr><th>{$strTemplate}: <sup class='red'>*</sup></th><td><input maxlength='100' name='name' size='40' value=\"{$template->name}\" /></td></tr>\n";
        echo "<tr><th>{$strDescription}: <sup class='red'>*</sup></th><td><textarea name='description' cols='50' rows='5' onfocus=\"clearFocusElement(this);\">{$template->description}</textarea></td></tr>\n";
        switch ($templatetype)
        {
            case 'email':

                echo "<tr><th colspan='2'>{$strEmail}</th></tr>"; // FIXME i18n defaults
                echo "<tr><th>{$strTo}: <sup class='red'>*</sup></th>";
                echo "<td><input id='tofield' maxlength='100' name='tofield' size='40' value=\"{$template->tofield}\" onfocus=\"recordFocusElement(this);\" /></td></tr>\n";
                echo "<tr><th>{$strFrom}: <sup class='red'>*</sup></th>";
                echo "<td><input id='fromfield' maxlength='100' name='fromfield' size='40' value=\"{$template->fromfield}\" onfocus=\"recordFocusElement(this);\" /></td></tr>\n";
                echo "<tr><th>{$strReplyTo}: <sup class='red'>*</sup></th>";
                echo "<td><input id='replytofield' maxlength='100' name='replytofield' size='40' value=\"{$template->replytofield}\" onfocus=\"recordFocusElement(this);\" /></td></tr>\n";
                echo "<tr><th>{$strCC}:</th>";
                echo "<td><input id='ccfield' maxlength='100' name='ccfield' size='40' value=\"{$template->ccfield}\" onfocus=\"recordFocusElement(this);\" /></td></tr>\n";
                echo "<tr><th>{$strBCC}:</th>";
                echo "<td><input id='bccfield' maxlength='100' name='bccfield' size='40' value=\"{$template->bccfield}\" onfocus=\"recordFocusElement(this);\" /></td></tr>\n";
                echo "<tr><th>{$strSubject}:</th>";
                echo "<td><input id='subject' maxlength='255' name='subjectfield' size='60' value=\"{$template->subjectfield}\" onfocus=\"recordFocusElement(this);\" /></td></tr>\n";
                break;

            case 'notice':
                // FIXME i18n
                echo "<tr><th>Link Text</th>";
                echo "<td><input id='linktext' maxlength='50' name='linktext' size='50' value=\"{$template->linktext}\" onfocus=\"recordFocusElement(this);\" /></td></tr>\n";
                echo "<tr><th>Link</th>";
                echo "<td><input id='link' maxlength='100' name='link' size='50' value=\"{$template->link}\"  onfocus=\"recordFocusElement(this);\" /></td></tr>\n";
                echo "<tr><th>Durability</th>";
                echo "<td><input id='durability' maxlength='100' name='durability' size='10' value=\"{$template->durability}\" onfocus=\"recordFocusElement(this);\" /></td></tr>\n";

        }

        if ($trigaction AND $template->type != $triggerarray[$trigaction->triggerid]['type']) echo "<p class='warning'>Trigger type mismatch</p>";
        echo "</td></tr>\n";


        if ($templatetype=='email') $body = $template->body;
        else $body = $template->text;
        echo "<tr><th>{$strText}</th>";
        echo "<td><textarea id='bodytext' name='bodytext' rows='20' cols='50' onfocus=\"recordFocusElement(this);\">{$body}</textarea></td>";

        if ($template->type=='incident')
        {
            echo "<tr><th></th><td><label><input type='checkbox' name='storeinlog' value='Yes' ";
            if ($template->storeinlog == 'Yes')
            {
                echo "checked='checked'";
            }
            echo " /> {$strStoreInLog}</label>";
            echo " &nbsp; (<input type='checkbox' name='cust_vis' value='yes' ";
            if ($template->customervisibility == 'show')
            {
                echo "checked='checked'";
            }
            echo " /> {$strVisibleToCustomer})";
            echo "</td></tr>\n";
        }
        echo "</table>\n";

        echo "<p>";
        echo "<input name='type' type='hidden' value='{$template->type}' />";
        echo "<input name='template' type='hidden' value='{$templatetype}' />";
        echo "<input name='focuselement' id='focuselement' type='hidden' value='' />";
        echo "<input name='id' type='hidden' value='{$id}' />";
        echo "<input name='submit' type='submit' value=\"{$strSave}\" />";
        echo "</p>\n";
        // FIXME when to allow deletion?
        if ($template->type=='user') echo "<p align='center'><a href='{$_SERVER['PHP_SELF']}?action=delete&amp;id={$id}'>{$strDelete}</a></p>";
        echo "</form>";
        echo "</div>";


            // FIXME i18n email templates
        // Show a list of available template variables.  Only variables that have 'requires' matching the 'required'
        // that the trigger provides is shown
        echo "<div id='templatevariables' style='width: 48%; float: right; border: 1px solid #CCCCFF; padding: 10px; display:none;'>";
        echo "<h4>Template Variables</h4>"; // FIXME template variables
        echo "<p align='center'>{$strFollowingSpecialIdentifiers}</p>";
        if (!is_array($required)) echo "<p class='info'>Some of these identifiers might not be available once you add a trigger</p>";

        echo "<dl>";
        foreach ($ttvararray AS $identifier => $ttvar)
        {
            $showtvar = FALSE;
            if (empty($ttvar['requires'])) $showtvar = TRUE;
            else
            {
                if (!is_array($ttvar['requires'])) $ttvar['requires'] = array($ttvar['requires']);
                foreach ($ttvar['requires'] as $needle)
                {
                    if (!is_array($required) OR in_array($needle, $required)) $showtvar = TRUE;
                }
            }
            if ($showtvar)
            {
                echo "<dt><code><a href=\"javascript:insertTemplateVar('{$identifier}');\">{$identifier}</a></code></dt>";
                if (!empty($ttvar['description'])) echo "<dd>{$ttvar['description']}";
                echo "<br />";
            }
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
    $sql = "DELETE FROM `{$dbEmailTemplates}` WHERE id='$id' AND type='user' LIMIT 1";
    mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    header("Location: {$_SERVER['PHP_SELF']}?action=showform");
    exit;
}
elseif ($action == "update")
{
    // External variables
    $template = cleanvar($_POST['template']);
    $name = cleanvar($_POST['name']);
    $description = cleanvar($_POST['description']);

    $tofield = cleanvar($_POST['tofield']);
    $fromfield = cleanvar($_POST['fromfield']);
    $replytofield = cleanvar($_POST['replytofield']);
    $ccfield = cleanvar($_POST['ccfield']);
    $bccfield = cleanvar($_POST['bccfield']);
    $subjectfield = cleanvar($_POST['subjectfield']);
    $bodytext = cleanvar($_POST['bodytext']);

    $link = cleanvar($_POST['link']);
    $linktext = cleanvar($_POST['linktext']);
    $durability = cleanvar($_POST['durability']);

    $cust_vis = cleanvar($_POST['cust_vis']);
    $storeinlog = cleanvar($_POST['storeinlog']);
    $id = cleanvar($_POST['id']);
    $type = cleanvar($_POST['type']);

//     echo "<pre>".print_r($_POST,true)."</pre>";

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

    switch ($template)
    {
        case 'email':
            $sql  = "UPDATE `{$dbEmailTemplates}` SET name='$name', description='$description', tofield='$tofield', fromfield='$fromfield', ";
            $sql .= "replytofield='$replytofield', ccfield='$ccfield', bccfield='$bccfield', subjectfield='$subjectfield', ";
            $sql .= "body='$bodytext', customervisibility='$cust_vis', storeinlog='$storeinlog' ";
            $sql .= "WHERE id='$id' LIMIT 1";
        break;

        case 'notice':
            $sql  = "UPDATE `{$dbNoticeTemplates}` SET name='$name', description='$description', type='', ";
            $sql .= "linktext='{$linktext}', link='{$link}', durability='{$durability}', ";
            $sql .= "text='$bodytext' ";
            $sql .= "WHERE id='$id' LIMIT 1";
        break;

        default:
            trigger_error('Error: Invalid template type', E_USER_WARNING);
            html_redirect($_SERVER['PHP_SELF'], FALSE);
    }

//     echo $sql;
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    if ($result)
    {
        journal(CFG_LOGGING_NORMAL, 'Email Template Updated', "Email Template {$type} was modified", CFG_JOURNAL_ADMIN, $type);
        html_redirect($_SERVER['PHP_SELF']);
    }
    else
    {
        html_redirect($_SERVER['PHP_SELF'], FALSE);
    }
}
?>