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
include ('triggertypes.inc.php');

//set up all the action types
define(ACTION_NONE, 1);
define(ACTION_NOTICE, 2);
define(ACTION_EMAIL, 3);
define(ACTION_JOURNAL, 4);
plugin_do('trigger_actions');

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
    plugin_do($triggerid);
    if ($CONFIG['debug'] && $paramarray != '')
    {
        foreach (array_keys($paramarray) as $key)
        {
            //parse parameter array
            $dbg .= "\$paramarray[$key] = " .$paramarray[$key]."\n";
            if ($key == "user")
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
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
    while ($triggerobj = mysql_fetch_object($result))
    {
        //see if we have any checks first
        if (!empty($triggerobj->checks))
        {
            if (!trigger_checks($triggerobj->checks, $paramarray))
            {
                $checks = trigger_replace_specials($triggerid, $triggerobj->checks, $paramarray);
                eval("return \$value = $checks;");
                if($value === FALSE)
                {
                    return;
                }
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
            if (is_array($paramarray))
            {
                foreach (array_keys($paramarray) AS $param)
                {
                    $journalbody .= "$param: {$paramarray[$param]}; ";
                }
            }
            else
            {
                $journalbody = '';
            }
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
    * @author Kieran Hogg, Ivan Lucas
    * @param $triggerid string. The id/name of the trigger being used
    * @param $string string. The string containing the variables
    * @param $paramarray array. An array containing values to be substituted
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
        $dbg .= "TRIGGER: param array: ".print_r($paramarray,true);
    }
    
    //this loops through each variable and creates an array of useable varaibles' regexs
    foreach ($ttvararray AS $identifier => $ttvar)
    {
        $multiple = FALSE;
        foreach ($ttvar AS $key => $value)
        {
            //this checks if it's a multiply-defined variable
            if (is_numeric($key))
            {
                $trigger_replaces = replace_vars(&$ttvar[$key], &$triggerid, &$identifier, &$paramarray);
                if(!empty($trigger_replaces))
                {
                    $trigger_regex[] = $trigger_replaces['trigger_regex'];
                    $trigger_replace[] = $trigger_replaces['trigger_replace'];
                }
                $multiple = TRUE;
            }
        }
        if ($multiple == FALSE)
        {
            $trigger_replaces = replace_vars(&$ttvar, &$triggerid, &$identifier, &$paramarray);
            if(!empty($trigger_replaces))
            {
                $trigger_regex[] = $trigger_replaces['trigger_regex'];
                $trigger_replace[] = $trigger_replaces['trigger_replace'];
            }
        }
    }
    return  preg_replace($trigger_regex, $trigger_replace, $string);
}


/**
    * Actually do the replacement, used so we can define variables more than once
    * @author Kieran Hogg
    * @param &$ttvar array the array of the variable to replace
    * @param &$triggerid string The name of the trigger
    * @param &$identifier string the {variable} name
    * @param &$paramarray the array of trigger parameters 
    * @return mixed array if replacement found, NULL if not
*/
function replace_vars($ttvar, $triggerid, $identifier, $paramarray, $required='')
{
    global $triggerarray, $ttvararray, $CONFIG;
    
    $usetvar = FALSE;
    
    //if we don't have any requires, we can already use this var
    if (empty($ttvar['requires']))
    {
        $usetvar = TRUE;
    }
    else
    {
        //otherwise we need to check all the requires
        if (!is_array($ttvar['requires']))
        {
            $ttvar['requires'] = array($ttvar['requires']);
        }
        //compare the trigger 'provides' with the var 'requires'
        foreach ($ttvar['requires'] as $needle)
        {
            if ($required != '')
            {
                if (in_array($needle, $required))
                {
                    $usetvar = TRUE;
                }
            }
            else
            {
                if (in_array($needle, $triggerarray[$triggerid]['required']))
                {
                    $usetvar = TRUE;
                }
            }
        }
    }
    
    //if we're able to use this variable
    if ($usetvar)
    {
        $trigger_regex = "/{$identifier}/s";
        if (!empty($ttvar['replacement']))
        {
            eval("\$res = {$ttvar[replacement]};");
        }
        $trigger_replace = $res;
        unset($res);
        return array('trigger_replace' => $trigger_replace,
                     'trigger_regex' => $trigger_regex);
    }
}

/**
    * Replaces template variables with their values
    * @author Ivan Lucas
    * @param $string string. The string containing the variables
    * @param $paramarray array. An array containing values to be substituted
    * @return string. The string with variables replaced
*/
function replace_specials($string, $paramarray)
{
    global $CONFIG, $dbg, $dbIncidents, $ttvararray;

    //manual variables
    $required = array('incidentid');

    //this loops through each variable and creates an array of useable varaibles' regexs
    foreach ($ttvararray AS $identifier => $ttvar)
    {
        $multiple = FALSE;
        foreach ($ttvar AS $key => $value)
        {
            //this checks if it's a multiply-defined variable
            if (is_numeric($key))
            {
                $trigger_replaces = replace_vars(&$ttvar[$key], &$triggerid, &$identifier, &$paramarray, $required);
                if(!empty($trigger_replaces))
                {
                    $trigger_regex[] = $trigger_replaces['trigger_regex'];
                    $trigger_replace[] = $trigger_replaces['trigger_replace'];
                }
                $multiple = TRUE;
            }
        }
        if ($multiple == FALSE)
        {
            $trigger_replaces = replace_vars(&$ttvar, &$triggerid, &$identifier, &$paramarray, $required);
            if(!empty($trigger_replaces))
            {
                $trigger_regex[] = $trigger_replaces['trigger_regex'];
                $trigger_replace[] = $trigger_replaces['trigger_replace'];
            }
        }
    }
    return  preg_replace($trigger_regex, $trigger_replace, $string);
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

    $sql = "SELECT * FROM `{$dbEmailTemplates}` WHERE name='{$template}'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
    if ($result)
    {
        $template = mysql_fetch_object($result);
    }

    //add this in manually, this is who we're sending the email to
    $paramarray['triggeruserid'] = $userid;

    $from = trigger_replace_specials($triggerid, $template->fromfield, $paramarray);
    $toemail = trigger_replace_specials($triggerid, $template->tofield, $paramarray);
    $subject = cleanvar(trigger_replace_specials($triggerid, $template->subjectfield, $paramarray));
    $body .= trigger_replace_specials($triggerid, $template->body, $paramarray);


    $mailok = send_email($toemail, $from, $subject, $body);
    $dbg .= "send_email($toemail, $from, $subject, $body)"; // FIXME BUGBUG remove this debugging
    if ($mailok==FALSE) trigger_error('Internal error sending email: '. $mailerror.' send_mail() failed', E_USER_ERROR);
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
    global $CONFIG, $dbg, $dbNotices, $dbNoticeTemplates;
    /*if ($CONFIG['debug'])
    {
        $dbg .= print_r($paramarray)."\n";
    }*/

    if (!empty($template))
    {
        //this is a trigger notice, get notice template
        $sql = "SELECT * FROM `{$dbNoticeTemplates}` WHERE name='{$template}'";
        $query = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
        if ($query)
        {
            $notice = mysql_fetch_object($query);
            $noticetext = mysql_escape_string(trigger_replace_specials($triggertype, $notice->text, $paramarray));
            $noticelinktext = cleanvar(trigger_replace_specials($triggertype, $notice->linktext, $paramarray));
            $noticelink = cleanvar(trigger_replace_specials($triggertype, $notice->link, $paramarray));
            $refid = cleanvar(trigger_replace_specials($triggertype, $notice->refid, $paramarray));
            $durability = $notice->durability;
            if ($CONFIG['debug']) $dbg .= $noticetext."\n";

            if ($userid == 0 AND $paramarray['userid'] > 0) $userid = $paramarray['userid'];

            $sql = "INSERT INTO `{$dbNotices}` (userid, type, text, linktext, link,
                                         durability, referenceid, timestamp) ";
            $sql .= "VALUES ({$userid}, '{$notice->type}', '{$noticetext}',
                            '{$noticelinktext}', '{$noticelink}', '{$durability}', '{$refid}', NOW())";
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
    * @param $triggertype string. The type of trigger (incident, system...)
    * @param $name string. The name for the select
    * @param $selected string. The name of the selected item
    * @returns string. HTML snippet
*/
function email_templates($name, $triggertype='system', $selected = '')
{
    global $dbEmailTemplates, $dbTriggers;;
    $html .= "<select id='{$name}' name='{$name}'>";
    $sql = "SELECT id, name, description FROM `{$dbEmailTemplates}` ";
    $sql .= "WHERE type='{$triggertype}' ORDER BY name";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
    while ($template = mysql_fetch_object($result))
    {
        //$name = strpos()
        //$name = str_replace("_", " ", $name);
        $name = strtolower($name);
        $html .= "<option value='{$template->id}' title=\"{$template->description}\">{$name}</option>\n";
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
    $sql = "SELECT * FROM `{$dbNoticeTemplates}` ORDER BY name ASC";
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
    $html = "".icon('trigger', 16)." ";
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
    $html = "".icon('triggeraction', 16)." ";
    if (!empty($trigaction->template))
    {
        if ($trigaction->action == 'ACTION_EMAIL')
        {
            $html .= icon('email', 16)." ";
            $templatename = db_read_column('name', $dbEmailTemplates, $trigaction->template);
            if ($editlink) $template = "<a href='templates.php?id={$trigaction->template}&amp;action=edit&amp;template=email'>";
            $template .= "{$templatename}";
            if ($editlink) $template .= "</a>";
        }
        elseif  ($trigaction->action == 'ACTION_NOTICE')
        {
            $html .= icon('info', 16)." ";
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
    }
    if (!empty($trigaction->userid)) $html .= " for ".user_realname($trigaction->userid);
    if (!empty($trigaction->parameters)) $html .= ", using {$trigaction->parameters}";
    if (!empty($trigaction->checks)) $html .= ", when {$trigaction->checks} ";
    
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