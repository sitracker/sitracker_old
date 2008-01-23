<?php
// triggers.inc.php - Handle triggers
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.

// Author: Kieran Hogg <kieran_hogg[at]users.sourceforge.net>

include ('mime.inc.php');


/**
 * id - trigger name
 * description - when the trigger is fired
 * required - parameters the triggers needs to fire
 * optional - parameters the trigger can check on, mimics 'subscription'-type events
 */
$triggerarray[] = array('id' => TRIGGER_INCIDENT_CREATED,
                        'description' => 'Occurs when a new incident has been created',
                        'requires' => array('incidentid'),
                        'optional' => array('contactid', 'siteid'));
$triggerarray[] = array('id' => TRIGGER_INCIDENT_ASSIGNED,
                        'description' => 'Occurs when a new incident is assigned to you',
                        'requires' => array('incidentid', 'userid'),
                        'optional' => array(),
                        );
$triggerarray[] = array('id' => TRIGGER_INCIDENT_ASSIGNED_WHILE_AWAY,
                        'description' => 'Occurs when a new incident is assigned to you and you are set to not accepting');
$triggerarray[] = array('id' => TRIGGER_INCIDENT_ASSIGNED_WHILE_OFFLINE,
                        'description' => 'Occurs when a new incident is assigned to you and your status is offline');
$triggerarray[] = array('id' => TRIGGER_INCIDENT_NEARING_SLA,
                        'description' => 'Occurs when one of your incidents nears an SLA');
$triggerarray[] = array('id' => TRIGGER_USERS_INCIDENT_NEARING_SLA,
                        'description' => 'Occurs when a user\'s incident you are watching is assigned to you',
                        'requires' => array('incidentid'),
                        'optional' => array('userid'));
$triggerarray[] = array('id' => TRIGGER_INCIDENT_EXCEEDED_SLA,
                        'description' => 'Occurs when one of your incidents exceeds an SLA');
$triggerarray[] = array('id' => TRIGGER_INCIDENT_REVIEW_DUE,
                        'description' => 'Occurs when an incident is due a review');
$triggerarray[] = array('id' => TRIGGER_CRITICAL_INCIDENT_CREATED,
                        'description' => 'Occurs when a priority A incident is logged');
$triggerarray[] = array('id' => TRIGGER_KB_CREATED,
                        'description' => 'Occurs when a new Knowledgebase article is created');
$triggerarray[] = array('id' => TRIGGER_NEW_HELD_EMAIL,
                        'description' => 'Occurs when there is a new email in the holding queue');
$triggerarray[] = array('id' => TRIGGER_WAITING_HELD_EMAIL,
                        'description' => 'Occurs when there is a new email in the holding queue for x minutes');
$triggerarray[] = array('id' => TRIGGER_USER_SET_TO_AWAY,
                        'description' => 'Occurs when one of your watched engineer goes away');
$triggerarray[] = array('id' => TRIGGER_SIT_UPGRADED,
                        'description' => 'Occurs when the system is upgraded');
$triggerarray[] = array('id' => TRIGGER_USER_RETURNS,
                        'description' => 'Occurs when one of your watched engineers returns');
$triggerarray[] = array('id' => TRIGGER_INCIDENT_OWNED_CLOSED_BY_USER,
                        'description' => 'Occurs when one of your incidents is closed by another engineer');

//set up all the action types
define(ACTION_NONE, 1);
define(ACTION_NOTICE, 2);
define(ACTION_EMAIL, 3);
define(ACTION_JOURNAL, 4);
$actionarray = array(ACTION_NONE,
                ACTION_NOTICE,
                ACTION_EMAIL,
                ACTION_JOURNAL);

/**
    * Master trigger function, creates a new trigger
    * @author Kieran Hogg
    * @param $triggerid integer. The id of the trigger to fire
    * @param $paramarray array. Extra parameters to pass the trigger
    * @return boolean. TRUE if the trigger created successfully, FALSE if not
*/
function trigger($triggerid, $paramarray='')
{
    global $sit, $CONFIG, $dbg, $dbTriggers;
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
    $query = mysql_query($sql);
    while ($result = mysql_fetch_object($query))
    {
        //see if we have any checks first
        if(!empty($result->checks))
        {
            if (!trigger_checks($result->checks, $paramarray))
            {
                return;
            }
        }
        
        //if we have any params from the actual trigger, append to user params
        if (!empty($result->parameters))
        {
            $resultparams = explode(",", $result->parameters);
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
            $dbg .= "TRIGGER: trigger_action({$result->userid}, {$triggerid},
                    {$result->action}, {$paramarray}) called \n";
        }
        trigger_action($result->userid, $triggerid, $result->action,
                       $paramarray);
    }
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
function trigger_action($userid, $triggerid, $action, $paramarray)
{
    global $CONFIG, $dbg;
    if ($CONFIG['debug'])
    {
        $dbg .= "TRIGGER: trigger_action($userid, $triggerid, $action,
                $paramarray) received\n";
    }

    //get the template type
    $sql = "SELECT template FROM triggers WHERE userid='{$userid}' AND
            triggerid='{$triggerid}'";
    $query = mysql_query($sql);
    $template = mysql_fetch_object($query);
    $template = $template->template;

    switch ($action)
    {
        case "ACTION_EMAIL":
            if ($CONFIG['debug'])
            {
                $dbg .= "TRIGGER: send_trigger_email($userid, $triggerid,
                        $template, $paramarray)\n";
            }
            send_trigger_email($userid, $triggerid, $template, $paramarray);
            break;

        case "ACTION_NOTICE":
            if ($CONFIG['debug'])
            {
                $dbg .= "TRIGGER: create_trigger_notice($userid, '',
                        $triggerid, $template, $paramarray) called";
            }
            create_trigger_notice($userid, '', $triggerid, $template,
                                  $paramarray);
            break;
        case "ACTION_JOURNAL":
            $journalbody = implode($paramarray);
            journal(CFG_LOGGING_NORMAL, $triggerid, "Trigger Fired ({$journalbody})", 0, $userid);
        case "ACTION_NONE":
        //fallthrough
        default:
            break;
    }
}

/**
    * Replaces template variables with their values
    * @author Kieran Hogg
    * @param $string string. The string containing the variables
    * @param $paramarray array. An array containing values to be substitute
    * into the string
    * @return string. The string with variables replaced
*/
function trigger_replace_specials($string, $paramarray)
{
    global $CONFIG, $application_version, $application_version_string, $dbg;
    global $dbIncidents;
    if ($CONFIG['debug'])
    {
        $dbg .= "TRIGGER: notice string before - $string\n";
        $dbg .= "TRIGGER: param array: ".print_r($paramarray);
    }

    $url = parse_url($_SERVER['HTTP_REFERER']);
    $baseurl = "{$url['scheme']}://{$url['host']}";
    $baseurl .= "{$CONFIG['application_webpath']}";

    $trigger_regex = array(0 => '/<incidentid>/s',
                            1 => '/<incidenttitle>/s',
                            2 => '/<incidentowner>/s',
                            3 => '/<KBname>/s',
                            4 => '/<sitpath>/s',
                            5 => '/<sitversion>/s',
                            6 => '/<engineerclosedname>/s',
                            );

    $trigger_replace = array(0 => $paramarray['incidentid'],
                                1 => $paramarray['incidenttitle'],
                                2 => $paramarray['incidentowner'],
                                3 => $paramarray['KBname'],
                                4 => $baseurl,
                                5 => $application_version,
                                6 => $paramarray['engineerclosedname']
                            );

    return preg_replace($trigger_regex,$trigger_replace,$string);
}

/**
    * Replaces email template variables with their values
    * @author Kieran Hogg
    * @param $string string. The string containing the variables
    * @param $paramarray array. An array containing values to be substitute
    * into the string
    * @return string. The string with variables replaced
    * @notes Temporary function, should be intergrated into
    * trigger_email_specials()
*/
function trigger_replace_email_specials($string, $paramarray)
{
    global $CONFIG, $application_version, $application_version_string, $dbg;
    global $dbIncidents;
    if ($CONFIG['debug'])
    {
        $dbg .= "TRIGGER: notice string before - $string\n";
        $dbg .= "TRIGGER: param array: ".print_r($paramarray);
    }

    $url = parse_url($_SERVER['HTTP_REFERER']);
    $baseurl = "{$url['scheme']}://{$url['host']}";
    $baseurl.= "{$CONFIG['application_webpath']}";

    $email_regex = array(0 => '/<contactemail>/s',
                         1 => '/<contactname>/s',
                         2 => '/<contactfirstname>/s',
                         3 => '/<contactsite>/s',
                         4 => '/<contactphone>/s',
                         5 => '/<contactmanager>/s',
                         6 => '/<contactnotify>/s',
                         7 => '/<incidentid>/s',
                         8 => '/<incidentexternalid>/s',
                         9 => '/<incidentccemail>/s',
                         10 => '/<incidentexternalengineer>/s',
                         11 => '/<incidentexternalengineerfirstname>/s',
                         12 => '/<incidentexternalemail>/s',
                         13 => '/<incidenttitle>/s',
                         14 => '/<incidentpriority>/s',
                         15 => '/<incidentsoftware>/s',
                         16 => '/<incidentowner>/s',
                         17 => '/<useremail>/s',
                         18 => '/<userrealname>/s',
                         19 => "/<applicationname>/s",
                         20 => '/<applicationshortname>/s',
                         21 => '/<applicationversion>/s',
                         22 => '/<supportemail>/s',
                         23 => '/<salesemail>/s',
                         24 => '/<supportmanageremail>/s',
                         25 => '/<signature>/s',
                         26 => '/<globalsignature>/s',
                         27 => '/<todaysdate>/s',
                         28 => '/<salespersonemail>/s',
                         29 => '/<incidentfirstupdate>/s',
                         30 => '/<contactnotify2>/s',
                         31 => '/<contactnotify3>/s',
                         32 => '/<contactnotify4>/s',
                         33 => '/<feedbackurl>/s'
                        );

    $email_replace = array(0 => contact_email($contactid),
        1 => contact_realname($contactid),
        2 => strtok(contact_realname($contactid),' '),
        3 => contact_site($contactid),
        4 => contact_phone($contactid),
        5 => contact_notify_email($contactid),
        6 => contact_notify_email($contactid),
        7 => $incidentid,
        8 => $incident->externalid,
        9 => incident_ccemail($incidentid),
        10 => incident_externalengineer($incidentid),
        11 => strtok(incident_externalengineer($incidentid),' '),
        12 => incident_externalemail($incidentid),
        13 => incident_title($incidentid),
        14 => priority_name(incident_priority($incidentid)),
        15 => software_name($incident->softwareid),
        16 => user_realname($incident->owner),
        17 => user_email($userid),
        18 => user_realname($userid),
        19 => $CONFIG['application_name'],
        20 => $CONFIG['application_shortname'],
        21 => $application_version_string,
        22 => $CONFIG['support_email'],
        23 => $CONFIG['sales_email'],
        24 => $CONFIG['support_manager_email'],
        25 => user_signature($userid),
        26 => global_signature(),
        27 => date("jS F Y"),
        28 => user_email(db_read_column('owner', 'sites',
                                        db_read_column('siteid','contacts',
                                                       $contactid))),
        29 => incident_firstupdate($incidentid),
        30 => contact_email(contact_notify($contactid, 2)),
        31 => contact_email(contact_notify($contactid, 3)),
        32 => contact_email(contact_notify($contactid, 4)),
        33 => $baseurl.'feedback.php?ax='.urlencode(trim(base64_encode(
                gzcompress(str_rot13(urlencode($CONFIG['feedback_form']).'&&'.
                urlencode($contactid).'&&'.urlencode($incidentid))))))
        );

    return preg_replace($email_regex,$email_replace,$string);
}
/**
    * Sends an email for a trigger
    * @author Kieran Hogg
    * @param $userid integer. The user to send the email to
    * @param $triggertype string. The type of trigger to apply
    * @param $template string. The name of the email template to use
    * @param $paramarray array. The array of extra parameters to apply to the
    * trigger
*/
function send_trigger_email($userid, $triggertype, $template, $paramarray)
{
    global $CONFIG, $dbg;
    if ($CONFIG['debug'])
    {
        $dbg .= "TRIGGER: send_trigger_email({$userid},{$triggertype},
                {$paramarray})";
    }

    //if we have an incidentid, get it to pass to emailtype_replace_specials()
    if (!empty($paramarray['incidentid']))
    {
        $incidentid = $paramarray['incidentid'];
    }

    $sql = "SELECT * FROM emailtype WHERE id='{$triggertype}'";
    $query = mysql_query($sql);
    if ($query)
    {
        $result = mysql_fetch_object($query);
    }
    $emailtype = $result->id;
    $from = emailtype_replace_specials(emailtype_from($emailtype), $incidentid,
                                       $userid);
    $toemail = emailtype_replace_specials(emailtype_to($emailtype), $incidentid,
                                          $userid);
    $subject = emailtype_replace_specials(emailtype_subject($emailtype),
                                          $incidentid, $userid);
    $body = emailtype_replace_specials(emailtype_body($emailtype), $incidentid,
                                       $userid);

    $mime = new MIME_mail($from, $toemail, $subject, $body, '', $mailerror);

    $mailok=$mime->send_mail();
    if ($mailok==FALSE) trigger_error('Internal error sending email: '.
                                      $mailerror.'','send_mail() failed');

    if ($CONFIG['debug'])
    {
        $dbg .= "TRIGGER: emailtype_replace_specials($string, $incidentid,
                $userid)";
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
    if ($CONFIG['debug'])
    {
        $dbg .= print_r($paramarray)."\n";
    }

    if (!empty($template))
    {
        //this is a trigger notice, get notice template
        $sql = "SELECT * from noticetemplates WHERE id='{$template}'";
        $query = mysql_query($sql);
        if ($query)
        {
            $notice = mysql_fetch_object($query);
            $noticetext = trigger_replace_specials($notice->text, $paramarray);
            $noticelinktext = trigger_replace_specials($notice->linktext, $paramarray);
            $noticelink = trigger_replace_specials($notice->link, $paramarray);
            if ($CONFIG['debug']) $dbg .= $noticetext."\n";
    
            $sql = "INSERT into notices(userid, type, text, linktext, link,
                                        referenceid, timestamp) ";
            $sql .= "VALUES ({$userid}, '{$notice->type}', '{$noticetext}',
                            '{$noticelinktext}', '{$noticelink}', '', NOW())";
            mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        }
        else
        {
            throw_error("No such trigger type");
        }
    }
}

/**
    * Displays a <select> with the list of triggers
    * @author Kieran Hogg
    * @param $name string. The name for the select
    * @param $selected string. The name of the selected item
*/
function triggers_drop_down($name, $selected = '')
{
    global $triggerarray;
    $html .= "<select id='{$name}' name='{$name}'>";
    foreach ($triggerarray as $trigger)
    {
        if ($trigger['id'] == $selected)
        {
            $html .= "<option selected='selected'>{$trigger['id']}</option>\n";
        }
        else
        {
            $html .= "<option>{$trigger['id']}</option>\n";

        }
    }
    $html .=  "</select>";
    return $html;
}

/**
    * Displays a <select> with the list of email templates
    * @author Kieran Hogg
    * @param $name string. The name for the select
    * @param $selected string. The name of the selected item
*/
function email_templates($name, $selected = '')
{
    $html .= "<select id='{$name}' name='{$name}'>";
    $sql = "SELECT * FROM emailtype";
    $query = mysql_query($sql);
    while ($template = mysql_fetch_object($query))
    {
        $html .= "<option value='{$template->id}'>{$template->id}</option>\n";
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
    $html .= "<select id='{$name}' name='{$name}'>";
    $sql = "SELECT * FROM noticetemplates";
    $query = mysql_query($sql);
    while ($template = mysql_fetch_object($query))
    {
        $html .= "<option value='{$template->id}'>{$template->id}</option>\n";
    }
    $html .= "</select>\n";
    return $html;
}

/**
    * Checks array of parameters against list of parametrs
    * @author Kieran Hogg
    * @param $checkstrings string. The list of required parameters
    * @param $paramarray array. The array to compare the strings to
    * @returns TRUE if the string parameter is in the array, FALSE if not
*/
function trigger_checks($checkstrings, $paramarray)
{
    $passed = FALSE;
    
    $checks = explode(",", $checkstrings);
    foreach ($checks as $check)
    {
        $values = explode("=", $check);
        switch($values[0])
        {
            case 'siteid':
                $sql = "SELECT sites.id AS siteid ";
                $sql .= "FROM sites, incidents, contacts ";
                $sql .= "WHERE incidents.id={$paramarray[incidentid]} ";
                $sql .= "AND incidents.contact=contacts.id ";
                $sql .= "AND sites.id=contacts.siteid";
                $query = mysql_query($sql);
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
                $sql = "SELECT contacts.id AS contactid ";
                $sql .= "FROM incidents, contacts ";
                $sql .= "WHERE incidents.id={$paramarray[incidentid]} ";
                $sql .= "AND incidents.contact=contacts.id ";
                $query = mysql_query($sql);
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
                $sql = "SELECT incidents.owner AS userid ";
                $sql .= "FROM incidents ";
                $sql .= "WHERE incidents.id={$paramarray[incidentid]} ";
                $query = mysql_query($sql);
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
                $sql = "SELECT incidents.servicelevel AS sla ";
                $sql .= "FROM incidents ";
                $sql .= "WHERE incidents.id={$paramarray[incidentid]} ";
                $query = mysql_query($sql);
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
?>
