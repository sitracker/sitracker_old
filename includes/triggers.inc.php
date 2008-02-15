<?php
// triggers.inc.php - Handle triggers
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.

// Author: Kieran Hogg <kieran_hogg[at]users.sourceforge.net>
//         Ivan Lucas <ivanlucas[at]users.sourceforge.net>

include ('mime.inc.php');


/**
 * Define a list of available triggers, trigger() will need to be called in the appropriate
 * place in the code for each of these
 *
 * id - trigger name
 *   description - when the trigger is fired
 *   required - parameters the triggers needs to fire
 *   optional - Rules the trigger can check, mimics 'subscription'-type events
 *   type - Trigger type (eg. incident, contact etc)
 */
$triggerarray['TRIGGER_INCIDENT_CREATED'] =
array('name' => 'Incident Created',
      'description' => 'Occurs when a new incident has been created',
      'required' => array('incidentid'),
      'optional' => array('contactid', 'siteid', 'priority'),
      'type' => 'incident');

$triggerarray['TRIGGER_INCIDENT_ASSIGNED'] =
array('name' => 'Incident Assigned',
      'description' => 'Occurs when a new incident is assigned to you',
      'required' => array('incidentid', 'userid'),
      'optional' => array(),
      'type' => 'incident'
      );

$triggerarray['TRIGGER_INCIDENT_ASSIGNED_WHILE_AWAY'] =
array('name' => 'Incident Assigned While Away',
      'description' => 'Occurs when a new incident is assigned to you and you are set to not accepting',
      'required' => array('incidentid', 'userid'),
      'optional' => array(),
      'type' => 'incident'
     );

$triggerarray['TRIGGER_INCIDENT_ASSIGNED_WHILE_OFFLINE'] =
array('name' => 'Incident Assigned While Offline',
      'description' => 'Occurs when a new incident is assigned to you and your status is offline',
      'required' => array('incidentid', 'userid'),
      'optional' => array(),
      'type' => 'incident'
      );

$triggerarray['TRIGGER_INCIDENT_NEARING_SLA'] =
array('name' => 'Incident Nearing SLA',
      'description' => 'Occurs when an incidents nears an SLA',
      'required' => array('incidentid'),
      'optional' => array('ownerid'),
      'type' => 'incident'
      );

$triggerarray['TRIGGER_INCIDENT_REVIEW_DUE'] =
array('name' => 'Incident Review Due',
      'description' => 'Occurs when an incident is due a review',
      'required' => array('revieweruserid'),
      'optional' => array(),
      'type' => 'incident'
      );

$triggerarray['TRIGGER_KB_CREATED'] =
array('name' => 'Knowledgebase Article Created',
      'description' => 'Occurs when a new Knowledgebase article is created',
      'required' => array('kbid'),
      'optional' => array(),
      'type' => 'kb'
      );

$triggerarray['TRIGGER_NEW_HELD_EMAIL'] =
array('name' => 'New Held Email',
      'description' => 'Occurs when there is a new email in the holding queue',
      'required' => array(),
      'optional' => array(),
      'type' => 'incident'
      );

$triggerarray['TRIGGER_WAITING_HELD_EMAIL'] =
array('name' => 'Waiting Held Email',
      'description' => 'Occurs when there is a new email in the holding queue for x minutes',
      'required' => array('minswaiting'),
      'optional' => array(),
      'type' => 'system'
      );

$triggerarray['TRIGGER_USER_SET_TO_AWAY'] =
array('name' => 'User Set To Away',
      'description' => 'Occurs when one of your watched engineer goes away',
      'required' => array('engineerid'),
      'optional' => array(),
      'type' => 'incident',
      'type' => 'incident'
      );
$triggerarray['TRIGGER_USER_RETURNS'] =
array('name' => 'User Returns',
      'description' => 'Occurs when one of your watched engineers returns',
      'required' => array(),
      'optional' => array(),
      'type' => 'user'
      );

$triggerarray['TRIGGER_SIT_UPGRADED'] =
array('name' => 'SiT! Upgraded',
      'description' => 'Occurs when the system is upgraded',
      'required' => array('sitversion'),
      'optional' => array(),
      'type' => 'system'
      );

$triggerarray['TRIGGER_INCIDENT_OWNED_CLOSED_BY_USER'] =
array('name' => 'Own Incident Closed By User',
      'description' => 'Occurs when one of your incidents is closed by another engineer',
      'required' => array('engineerid'),
      'optional' => array(),
      'type' => 'incident'
      );

$triggerarray['TRIGGER_LANGUAGE_DIFFERS'] =
array('name' => 'Current Language Differs',
      'description' => 'Occurs when your current language setting is different to your profile setting',
      'required' => array('currentlang', 'profilelang'),
      'optional' => array(),
      'type' => 'system'
     );


//set up all the action types
define(ACTION_NONE, 1);
define(ACTION_NOTICE, 2);
define(ACTION_EMAIL, 3);
define(ACTION_JOURNAL, 4);

$actionarray['ACTION_NONE'] =
array('name' => $strNone,
      'description' => 'Do nothing'
      );

$actionarray['ACTION_NOTICE'] =
array('name' => 'Notice',
      'description' => 'Create a notice based on %s'
      );

$actionarray['ACTION_EMAIL'] =
array('name' => 'Email',
      'description' => 'Send an email based on a %s template'
      );

$actionarray['ACTION_JOURNAL'] =
array('name' => 'Journal',
      'description' => 'Log the trigger in the system journal'
      );

/**
    * Template variables (Alphabetical order)
    * description - Friendly label
    * replacement - Quoted PHP code to be run to perform the template var replacement
    * requires -Optional field. single string or array. Specifies the 'required' params from the trigger that is needed for this replacement
    * action - Optional field, when set the var will only be available for that action
*/
$ttvararray['{applicationname}'] = array('description' => $CONFIG['application_name'],
                                     'replacement' => '$CONFIG[\'application_name\'];');

$ttvararray['{applicationshortname}'] = array('description' => $CONFIG['application_shortname'],
                                     'replacement' => '$CONFIG[\'application_shortname\'];');

$ttvararray['{applicationversion}'] = array('description' => $application_version_string,
                                     'replacement' => '$application_version_string;');

$ttvararray['{contactemail}'] = array('description' => $strIncidentsContactEmail,
                                      'requires' => 'contactid',
                                     'replacement' => 'contact_email($contactid);',
                                     'action' => 'ACTION_EMAIL');

$ttvararray['{contactfirstname}'] = array('description' => 'First Name of contact',
                                     'requires' => 'contactid',
                                     'replacement' => "strtok(contact_realname(\$contactid),' ');");

$ttvararray['{contactname}'] = array('description' => 'Full Name of contact',
                                     'requires' => 'contactid',
                                     'replacement' => 'contact_realname($contactid);');

$ttvararray['{contactnotify}'] = array('description' => 'The Notify Contact email address (if set)',
                                      'requires' => 'contactid',
                                     'replacement' => 'contact_notify_email($contactid);');

$ttvararray['{contactphone}'] = array('description' => 'Contact phone number',
                                     'requires' => 'contactid',
                                     'replacement' => 'contact_site($contactid);');

$ttvararray['{contactsite}'] = array('description' => 'Site name',
                                     'requires' => 'siteid',
                                     'replacement' => 'contact_site($contactid);');

$ttvararray['{feedbackurl}'] = array('description' => '',
                                     'requires' => 'incidentid',
                                     'replacement' => '$baseurl.\'feedback.php?ax=\'.urlencode(trim(base64_encode(gzcompress(str_rot13(urlencode($CONFIG[\'feedback_form\']).\'&&\'.urlencode($contactid).\'&&\'.urlencode($incidentid))))));');

$ttvararray['{globalsignature}'] = array('description' => $strGlobalSignature,
                                     'replacement' => 'global_signature();');

$ttvararray['{incidentccemail}'] = array('description' => $strIncidentCCList,
                                     'requires' => 'incidentid',
                                     'replacement' => 'incident_ccemail($paramarray[incidentid]);');

$ttvararray['{incidentexternalemail}'] = array('description' => $strExternalEngineerEmail,
                                     'requires' => 'incidentid',
                                     'replacement' => 'incident_externalemail($paramarray[incidentid]);');

$ttvararray['{incidentexternalengineer}'] = array('description' => $strExternalEngineer,
                                     'requires' => 'incidentid',
                                     'replacement' => 'incident_externalengineer($paramarray[incidentid]);');


$ttvararray['{incidentexternalengineerfirstname}'] = array('description' => $strExternalEngineersFirstName,
                                     'requires' => 'incidentid',
                                     'replacement' => 'strtok(incident_externalengineer($paramarray[incidentid]),\' \');');

$ttvararray['{incidentexternalid}'] = array('description' => "{$GLOBALS['strExternalID']}",
                                     'requires' => 'incidentid',
                                     'replacement' => '$incident->externalid;');

$ttvararray['{incidentfirstupdate}'] = array('description' => $strFirstCustomerVisibleUpdate,
                                     'replacement' => '');

$ttvararray['{incidentid}'] = array('description' => $GLOBALS['strIncidentID'],
                                     'requires' => 'incidentid',
                                     'replacement' => '$paramarray[incidentid];');

$ttvararray['{incidentowner}'] = array('description' => $strIncidentOwnersFullName,
                                     'requires' => 'incidentid',
                                     'replacement' => 'user_realname(incident_owner($paramarray[incidentid]));');

$ttvararray['{incidentowneremail}'] = array('description' => 'Incident Owners Email Address',
                                     'requires' => 'incidentid',
                                     'replacement' => 'user_email(incident_owner($paramarray[incidentid]));');

$ttvararray['{incidentpriority}'] = array('description' => $strIncidentPriority,
                                     'requires' => 'incidentid',
                                     'replacement' => 'priority_name(incident_priority($paramarray[incidentid]));');

$ttvararray['{incidentsoftware}'] = array('description' => $strSkillAssignedToIncident,
                                     'requires' => 'incidentid',
                                     'replacement' => 'software_name(db_read_column("softwareid", $GLOBALS["dbIncidents"], $paramarray[incidentid]));');

$ttvararray['{incidenttitle}'] = array('description' => $strIncidentTitle,
                                     'requires' => 'incidentid',
                                     'replacement' => 'incident_title($paramarray[incidentid]);');

$ttvararray['{salesperson}'] = array('description' => 'Salesperson',
                                     'requires' => 'siteid',
                                     'replacement' => 'user_realname(db_read_column(\'owner\', $GLOBALS[\'dbSites\'], $siteid));');

$ttvararray['{salespersonemail}'] = array('description' => $strSalespersonAssignedToContactsSiteEmail,
                                     'requires' => 'siteid',
                                     'replacement' => 'user_email(db_read_column(\'owner\', $GLOBALS[\'dbSites\'], $siteid));');

$ttvararray['{signature}'] = array('description' => $strCurrentUsersSignature,
                                     'replacement' => 'user_signature($_SESSION[\'userid\']);');

$ttvararray['{supportemail}'] = array('description' => $strSupportEmailAddress,
                                     'replacement' => '$CONFIG[\'support_email\'];');

$ttvararray['{supportmanageremail}'] = array('description' => $strSupportManagersEmailAddress,
                                     'replacement' => '$CONFIG[\'support_manager_email\'];');

$ttvararray['{todaysdate}'] = array('description' => $strCurrentDate,
                                     'replacement' => 'ldate("jS F Y");');

$ttvararray['{useremail}'] = array('description' => $strCurrentUserEmailAddress,
                                     'replacement' => 'user_email($_SESSION[\'userid\']);');

$ttvararray['{userrealname}'] = array('description' => $strFullNameCurrentUser,
                                     'replacement' => 'user_realname($_SESSION[\'userid\']);');


/**
    * Master trigger function, creates a new trigger
    * @author Kieran Hogg
    * @param $triggerid integer. The id of the trigger to fire
    * @param $paramarray array. Extra parameters to pass the trigger
    * @return boolean. TRUE if the trigger created successfully, FALSE if not
*/
function trigger($triggerid, $paramarray='')
{
    global $sit, $CONFIG, $dbg, $dbTriggers, $triggerarray;
    global $dbTriggers;

    // Check that this is a defined trigger
    if (!array_key_exists($triggerid, $triggerarray))
    {
        trigger_error("Trigger '{$triggerid}' not defined", E_USER_WARNING);
        return;
    }

    if ($CONFIG['debug'] && $paramarray != '')
    {
        foreach (array_keys($paramarray) as $key)
        {
            //parse parameter array
            $dbg .= "\$paramarray[$key] = " .$paramarray[$key]."\n";
            if($key == "user")
            {
                $userid = $paramarray[$key];
            }
            //TODO do we need to check for any 'special' keys here?
        }
    }

    //find relevant triggers
    $sql = "SELECT * FROM `{$dbTriggers}` WHERE triggerid='{$triggerid}'";
    if ($userid)
    {
        $sql .= "AND userid={$userid}";
    }
    $result = mysql_query($sql);
//     echo $sql;
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
    while ($triggerobj = mysql_fetch_object($result))
    {
        //see if we have any checks first
        if(!empty($triggerobj->checks))
        {
            if (!trigger_checks($triggerobj->checks, $paramarray))
            {
                return;
            }
        }

        //if we have any params from the actual trigger, append to user params
        if (!empty($triggerobj->parameters))
        {
            $resultparams = explode(",", $triggerobj->parameters);
            foreach ($resultparams as $assigns)
            {
                $values = explode("=", $assigns);
                $paramarray[$values[0]] = $values[1];
                if ($CONFIG['debug'])
                {
                    $dbg .= "\$paramarray[{$values[0]}] = {$values[1]}\n";
                }
            }
        }

        if ($CONFIG['debug'])
        {
            $dbg .= "TRIGGER: trigger_action({$triggerobj->userid}, {$triggerid},
                    {$triggerobj->action}, {$paramarray}) called \n";
        }
        trigger_action($triggerobj->userid, $triggerid, $triggerobj->action,
                       $paramarray, $triggerobj->template);
    }
    return;
}


/**
    * Do the specific action for the specific user for a trigger
    * @author Kieran Hogg
    * @param $userid integer. The user to apply the trigger action to
    * @param $triggerid string. The id of the trigger to apply
    * @param $action string. The type of action to perform
    * @param $paramarray array. The array of extra parameters to apply to the
    * trigger
    * @return boolean. TRUE if the user has the permission, otherwise FALSE
*/
function trigger_action($userid, $triggerid, $action, $paramarray, $template)
{
    global $CONFIG, $dbg;
    global $dbTriggers;
    if ($CONFIG['debug'])
    {
        $dbg .= "TRIGGER: trigger_action($userid, $triggerid, $action,
                $paramarray, $template) received\n";
    }

    switch ($action)
    {
        case "ACTION_EMAIL":
            if ($CONFIG['debug'])
            {
                $dbg .= "TRIGGER: send_trigger_email($userid, $triggerid, $template, $paramarray)\n";
            }
            $rtnvalue = send_trigger_email($userid, $triggerid, $template, $paramarray);
            break;

        case "ACTION_NOTICE":
            if ($CONFIG['debug'])
            {
                $dbg .= "TRIGGER: create_trigger_notice($userid, '',
                        $triggerid, $template, $paramarray) called";
            }
            $rtnvalue = create_trigger_notice($userid, '', $triggerid, $template,
                                  $paramarray);
            break;

        case "ACTION_JOURNAL":
            if (is_array($paramarray)) $journalbody = implode($paramarray);
            else $journalbody = '';
            $rtnvalue = journal(CFG_LOGGING_NORMAL, $triggerid, "Trigger Fired ({$journalbody})", 0, $userid);

        case "ACTION_NONE":
        //fallthrough
        default:
            break;
    }

    return $rtnvalue;
}


/**
    * Replaces template variables with their values
    * @author Kieran Hogg
    * @param $triggerid string. The id/name of the trigger being used
    * @param $string string. The string containing the variables
    * @param $paramarray array. An array containing values to be substitute
    * into the string
    * @return string. The string with variables replaced
*/
function trigger_replace_specials($triggerid, $string, $paramarray)
{
    global $CONFIG, $application_version, $application_version_string, $dbg;
    global $dbIncidents;
    global $triggerarray, $ttvararray;

    if ($CONFIG['debug'])
    {
        $dbg .= "\nTRIGGER: notice string before - $string\n";
        $dbg .= "TRIGGER: param array: ".print_r($paramarray);
    }

    $url = parse_url($_SERVER['HTTP_REFERER']);
    $baseurl = "{$url['scheme']}://{$url['host']}";
    $baseurl .= "{$CONFIG['application_webpath']}";

    foreach ($ttvararray AS $identifier => $ttvar)
    {
        $usetvar = FALSE;
        if (empty($ttvar['requires'])) $usetvar = TRUE;
        else
        {
            if (!is_array($ttvar['requires'])) $ttvar['requires'] = array($ttvar['requires']);
            foreach ($ttvar['requires'] as $needle)
            {
                if (in_array($needle, $triggerarray[$triggerid]['required'])) $usetvar = TRUE;
            }
        }
        if ($usetvar)
        {
            $trigger_regex[] = "/{$identifier}/s";
            if (!empty($ttvar['replacement']))
            {
                eval("\$res = {$ttvar[replacement]};");
            }
            $trigger_replace[] = $res;
            unset($res);
        }
    }

    return preg_replace($trigger_regex, $trigger_replace, $string);
}


/**
    * Sends an email for a trigger
    * @author Kieran Hogg
    * @param $userid integer. The user to send the email to
    * @param $triggerid string. The triggerid/name of the trigger
    * @param $template string. The name of the email template to use
    * @param $paramarray array. The array of extra parameters to apply to the
    * trigger
*/
function send_trigger_email($userid, $triggerid, $template, $paramarray)
{
    global $CONFIG, $dbg, $dbEmailTemplates;
    if ($CONFIG['debug'])
    {
        $dbg .= "TRIGGER: send_trigger_email({$userid},{$triggertype}, {$paramarray})\n";
    }
    // $triggerarray[$triggerid]['type'])

    //if we have an incidentid, get it to pass to emailtype_replace_specials()
    if (!empty($paramarray['incidentid']))
    {
        $incidentid = $paramarray['incidentid'];
    }

    $sql = "SELECT * FROM `{$dbEmailTemplates}` WHERE id='{$template}'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
    if ($result)
    {
        $template = mysql_fetch_object($result);
    }

    $from = trigger_replace_specials($triggerid, $template->fromfield, $paramarray);
    $toemail = trigger_replace_specials($triggerid, $template->tofield, $paramarray);
    $subject = trigger_replace_specials($triggerid, $template->subjectfield, $paramarray);
    $body .= trigger_replace_specials($triggerid, $template->body, $paramarray);


    $mailok = send_email($toemail, $from, $subject, $body);

    if ($mailok==FALSE) trigger_error('Internal error sending email: '. $mailerror.' send_mail() failed', E_USER_ERROR);

    if ($CONFIG['debug'])
    {
        $dbg .= "TRIGGER: emailtype_replace_specials($string, $incidentid, $userid)\n";
    }
    $email = emailtype_replace_specials($string, $incidentid, $userid);
    if ($CONFIG['debug'])
    {
        $dbg .= $email;
    }
}


/**
    * Creates a trigger notice
    * @author Kieran Hogg
    * @param $userid integer. The user to apply the trigger action to
    * @param $noticetext string. The text of the notice; only used for manual
    * notices
    * @param $triggertype string. The type of trigger to apply
    * @param $template string. The name of the email template to use
    * @param $paramarray array. The array of extra parametes to apply to the
    * trigger
*/
function create_trigger_notice($userid, $noticetext='', $triggertype='',
                               $template, $paramarray='')
{
    global $CONFIG, $dbg;
    /*if ($CONFIG['debug'])
    {
        $dbg .= print_r($paramarray)."\n";
    }*/

    if (!empty($template))
    {
        //this is a trigger notice, get notice template
        $sql = "SELECT * from noticetemplates WHERE id='{$template}'";
        $query = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
        if ($query)
        {
            $notice = mysql_fetch_object($query);
            $noticetext = trigger_replace_specials($triggertype, $notice->text, $paramarray);
            echo "<h1>trigger_replace_specials($triggertype, $notice->text, $paramarray)</h1>";
            $noticelinktext = trigger_replace_specials($triggertype, $notice->linktext, $paramarray);
            $noticelink = trigger_replace_specials($triggertype, $notice->link, $paramarray);
            if ($CONFIG['debug']) $dbg .= $noticetext."\n";

            $sql = "INSERT into notices(userid, type, text, linktext, link,
                                        referenceid, timestamp) ";
            $sql .= "VALUES ({$userid}, '{$notice->type}', '{$noticetext}',
                            '{$noticelinktext}', '{$noticelink}', '', NOW())";
                            //echo $sql;
            mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
        }
        else
        {
            throw_error("No such trigger type");
        }
    }
}


/**
    * Displays a <select> with the list of email templates
    * @author Kieran Hogg, Ivan Lucas
    * @param $triggertype string. The type of trigger (incident, contact...)
    * @param $name string. The name for the select
    * @param $selected string. The name of the selected item
*/
function email_templates($triggertype, $name, $selected = '')
{
    global $dbEmailTemplates, $dbTriggers;;
    $html .= "<select id='{$name}' name='{$name}'>";
    $sql = "SELECT * FROM `{$dbEmailTemplates}` WHERE id NOT IN (SELECT template FROM `{$dbTriggers}`) AND type='{$triggertype}' ORDER BY id";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
    while ($template = mysql_fetch_object($result))
    {
        $html .= "<option value='{$template->id}'>{$template->name}</option>\n";
    }
    $html .= "</select>\n";
    return $html;
}


/**
    * Displays a <select> with the list of notice templates
    * @author Kieran Hogg
    * @param $name string. The name for the select
    * @param $selected string. The name of the selected item
*/
function notice_templates($name, $selected = '')
{
    global $dbNoticeTemplates;
    $html .= "<select id='{$name}' name='{$name}'>";
    $sql = "SELECT * FROM `{$dbNoticeTemplates}`";
    $query = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
    while ($template = mysql_fetch_object($query))
    {
        $html .= "<option value='{$template->id}'>{$template->name}</option>\n";
    }
    $html .= "</select>\n";
    return $html;
}


/**
    * Checks array of parameters against list of parameters
    * @author Kieran Hogg
    * @param $checkstrings string. The list of required parameters
    * @param $paramarray array. The array to compare the strings to
    * @returns TRUE if the string parameter is in the array, FALSE if not
*/
function trigger_checks($checkstrings, $paramarray)
{
    global $dbSites, $dbIncidents, $dbContacts;
    $passed = FALSE;

    $checks = explode(",", $checkstrings);
    foreach ($checks as $check)
    {
        $values = explode("=", $check);
        switch($values[0])
        {
            case 'siteid':
                $sql = "SELECT s.id AS siteid ";
                $sql .= "FROM `{$dbSites}` AS s, `{$dbIncidents}` AS i, `{$dbContacts}` ";
                $sql .= "WHERE i.id={$paramarray[incidentid]} ";
                $sql .= "AND i.contact=c.id ";
                $sql .= "AND s.id=c.siteid";
                $query = mysql_query($sql);
                if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
                if($query)
                {
                    $result = mysql_fetch_object($query);
                    $siteid = $result->siteid;
                    if ($siteid == $values[1])
                    {
                        $passed = TRUE;
                    }
                }
            break;

            case 'contactid':
                $sql = "SELECT c.id AS contactid ";
                $sql .= "FROM `{$dbIncidents}` AS i, `{$dbContacts}` AS c ";
                $sql .= "WHERE i.id={$paramarray[incidentid]} ";
                $sql .= "AND i.contact=c.id ";
                $query = mysql_query($sql);
                if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
                if($query)
                {
                    $result = mysql_fetch_object($query);
                    $contactid = $result->contactid;
                    if ($contactid == $values[1])
                    {
                        $passed = TRUE;
                    }
                }
            break;

            case 'userid':
                $sql = "SELECT i.owner AS userid ";
                $sql .= "FROM `{$dbIncidents}` AS i ";
                $sql .= "WHERE i.id='{$paramarray[incidentid]}' ";
                $query = mysql_query($sql);
                if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
                if($query)
                {
                    $result = mysql_fetch_object($query);
                    $userid = $result->userid;
                    if ($userid == $values[1])
                    {
                        $passed = TRUE;
                    }
                }
            break;

            case 'sla':
                $sql = "SELECT i.servicelevel AS sla ";
                $sql .= "FROM `{$dbIncidents}` AS i ";
                $sql .= "WHERE i.id={$paramarray[incidentid]} ";
                $query = mysql_query($sql);
                if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
                if($query)
                {
                    $result = mysql_fetch_object($query);
                    $sla = $result->sla;
                    if ($sla == $values[1])
                    {
                        $passed = TRUE;
                    }
                }
            break;

            default:
                //blank
            break;
        }
    }
    return $passed;
}


/**
    * Formats a human readable description of a trigger
    * @author Ivan Lucas
    * @param $triggervar array. An individual trigger array
    * @returns HTML
*/
function trigger_description($triggervar)
{
    global $CONFIG, $iconset, $triggerarray;
    $html = "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/trigger.png' width='16' height='16' alt='' /> ";
    $html .= "<strong>";
    if (!empty($triggervar['name'])) $html .= "{$triggervar['name']}";
    else $html .= "{$trigger}";
    $html .= "</strong><br />\n";
    $html .= $triggervar['description'];
    return $html;
}


/**
    * Formats a human readable description of a trigger action
    * @author Ivan Lucas
    * @param $trigaction object. mysql fetch object of triggers db table
    * @param $editlink bool. Do a hyperlink to edit template when TRUE
    * @returns HTML
*/
function triggeraction_description($trigaction, $editlink=FALSE)
{
    global $CONFIG, $iconset, $actionarray, $dbEmailTemplates, $dbNoticeTemplates;
    $html = "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/triggeraction.png' width='16' height='16' alt='' /> ";
    if (!empty($trigaction->checks)) $html .= "When {$trigaction->checks} ";
    if (!empty($trigaction->template))
    {
        if ($trigaction->action == 'ACTION_EMAIL')
        {
            $templatename = db_read_column('name', $dbEmailTemplates, $trigaction->template);
            if ($editlink) $template = "<a href='templates.php?id={$trigaction->template}&amp;action=edit&amp;template=email'>";
            $template .= "{$templatename}";
            if ($editlink) $template .= "</a>";
        }
        elseif  ($trigaction->action == 'ACTION_NOTICE')
        {
            $templatename = db_read_column('name', $dbNoticeTemplates, $trigaction->template);
            if ($editlink) $template = "<a href='templates.php?id={$trigaction->template}&amp;action=edit&amp;template=notice'>";
            $template .= "{$templatename}";
            if ($editlink) $template .= "</a>";
        }
        else
        {
            $template = $trigaction->template;
        }
        $html .= sprintf($actionarray[$trigaction->action]['description'], $template);
        $html .= " ";
    }
    else
    {
        $html .= "{$actionarray[$trigaction->action]['description']} ";
        //                         echo "{$actionarray[$trigaction->action]['name']} ";
    }
    if (!empty($trigaction->userid)) $html .= " for ".user_realname($trigaction->userid).". ";
    if (!empty($trigaction->parameters)) $html .= " using {$trigaction->parameters}.";

    return $html;
}


/**
    * Revokes any triggers of that type/reference
    * @author Kieran Hogg
    * @param $triggerid string. Type of trigger
    * @param $userid integer. ID of the user to revoke from
    * @param $referenceid integer. Reference of the notice
*/
//TODO should this be limited to one delete, is there ever more than one?
//TODO make it fail quietly
function trigger_revoke($triggerid, $userid, $referenceid=0)
{
    global $GLOBALS;
    //find all triggers of this type and user
    $sql = "SELECT * FROM `{$GLOBALS['dbTriggers']}` WHERE triggerid='{$triggerid}' ";
    $sql .= "AND userid={$userid} AND action='ACTION_NOTICE' AND template!=0";
    $result = mysql_query($sql);
    while($triggerobj = @mysql_fetch_object($result))
    {
        $templatesql = "DELETE FROM {$GLOBALS['dbNotices']} ";
        $templatesql .= "WHERE template={$triggerobj->template} ";
        $templatesql .= "AND userid={$userid} ";

        if($referenceid != 0)
        {
            $templatesql .= "AND referenceid={$referenceid}";
        }
        $result = @mysql_query($templatesql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
    }
}
?>