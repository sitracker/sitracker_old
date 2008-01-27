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
      'type' => 'incident'
      );

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
    * Template variables
    * description - Friendly label
    * replacement - Quoted PHP code to be run to perform the template var replacement
    * action - Optional field, when set the var will only be available for that action
*/
$ttvararray['{contactemail}'] = array('description' => 'Email address of contact',
                                     'replacement' => 'contact_email($contactid)',
                                     'action' => 'ACTION_EMAIL');

$ttvararray['{contactname}'] = array('description' => 'Full Name of contact',
                                     'replacement' => 'contact_realname($contactid)');

$ttvararray['{contactfirstname}'] = array('description' => 'First Name of contact',
                                     'replacement' => "strtok(contact_realname(\$contactid),' ')");

$ttvararray['{contactsite}'] = array('description' => 'Site name',
                                     'replacement' => 'contact_site($contactid)');

$ttvararray['{contactphone}'] = array('description' => 'Contact phone number',
                                     'replacement' => 'contact_site($contactid)');

$ttvararray['{contactnotify}'] = array('description' => 'The Notify Contact email address (if set)',
                                     'replacement' => 'contact_notify_email($contactid)');

$ttvararray['{incidentid}'] = array('description' => 'Incident ID',
                                     'replacement' => '$incidentid');



// Array of template variables available for each trigger type
$triggertypevars['incident'] = array('{contactemail}', '{contactname}', '{contactfirstname}',
                                     '{contactsite}', '{contactphone}', '{contactnotify}',
                                     '{incidentid}');
/*
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
*/

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
    $query = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
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
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
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
            if (is_array($paramarray)) $journalbody = implode($paramarray);
            else $journalbody = '';
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
        /*$dbg .= "TRIGGER: notice string before - $string\n";
        $dbg .= "TRIGGER: param array: ".print_r($paramarray);*/
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
                            7 => '/<realname>/s'
                            );

    $trigger_replace = array(0 => $paramarray['incidentid'],
                                1 => incident_title($paramarray['incidentid']),
                                2 => $paramarray['incidentowner'],
                                3 => $paramarray['KBname'],
                                4 => $baseurl,
                                5 => $application_version,
                                6 => $paramarray['engineerclosedname'],
                                7 => user_realname($paramarray['userid']),
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
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
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
            $noticetext = trigger_replace_specials($notice->text, $paramarray);
            $noticelinktext = trigger_replace_specials($notice->linktext, $paramarray);
            $noticelink = trigger_replace_specials($notice->link, $paramarray);
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
    * Checks array of parameters against list of parameters
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
                $sql = "SELECT contacts.id AS contactid ";
                $sql .= "FROM incidents, contacts ";
                $sql .= "WHERE incidents.id={$paramarray[incidentid]} ";
                $sql .= "AND incidents.contact=contacts.id ";
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
                $sql = "SELECT incidents.owner AS userid ";
                $sql .= "FROM incidents ";
                $sql .= "WHERE incidents.id={$paramarray[incidentid]} ";
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
                $sql = "SELECT incidents.servicelevel AS sla ";
                $sql .= "FROM incidents ";
                $sql .= "WHERE incidents.id={$paramarray[incidentid]} ";
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
    global $CONFIG, $iconset, $actionarray;
    $html = "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/triggeraction.png' width='16' height='16' alt='' /> ";
    if (!empty($trigaction->checks)) $html .= "When {$trigaction->checks} ";
    if (!empty($trigaction->template))
    {
        if ($trigaction->action == 'ACTION_EMAIL')
        {
            $templatename = $trigaction->template;
            if ($editlink) $template = "<a href='edit_emailtype.php?id={$trigaction->template}&amp;action=edit&amp;template=email'>";
            $template .= "{$templatename}";
            if ($editlink) $template .= "</a>";
        }
        elseif  ($trigaction->action == 'ACTION_NOTICE')
        {
            $templatename = db_read_column('name', 'noticetemplates', $trigaction->template);
            if ($editlink) $template = "<a href='edit_emailtype.php?id={$templatename}&amp;action=edit&amp;template=notice'>";
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

?>