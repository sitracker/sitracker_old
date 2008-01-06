<?php
// triggers.inc.php - Handle triggers
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.

//set up all the trigger types
$triggerarray = array(TRIGGER_INCIDENT_CREATED,
                        TRIGGER_INCIDENT_ASSIGNED,
                        TRIGGER_INCIDENT_ASSIGNED_WHILE_AWAY,
                        TRIGGER_INCIDENT_ASSIGNED_WHILE_OFFLINE,
                        TRIGGER_INCIDENT_NEARING_SLA,
                        TRIGGER_USERS_INCIDENT_NEARING_SLA,
                        TRIGGER_INCIDENT_EXCEEDED_SLA,
                        TRIGGER_INCIDENT_REVIEW_DUE,
                        TRIGGER_CRITICAL_INCIDENT_LOGGED,
                        TRIGGER_KB_CREATED,
                        TRIGGER_NEW_HELD_EMAIL,
                        TRIGGER_WAITING_HELP_EMAIL,
                        TRIGGER_USER_SET_TO_AWAY,
                        TRIGGER_SIT_UPGRADED,
                        TRIGGER_USER_RETURNS,
                        TRIGGER_INCIDENT_OWNED_CLOSED_BY_USER);

//set up all the action types
$actionarray = array(ACTION_NONE,
                        ACTION_NOTICE,
                        ACTION_EMAIL);

//TODO fix to use one var (minor issue)
//define all the triggers
$i = 0;
$j = 1;
foreach($triggerarray as $trigger)
{
    define($triggerarray[$i], $j);
    $i++; 
    $j++;
}

//define all the actions
$i = 0;
$j = 1;
foreach($actionarray as $action)
{
    define($actionarray[$i], $j);
    $i++; 
    $j++;
}

/**
    * Master trigger function, creates a new trigger
    * @author Kieran Hogg
    * @param $triggertype interger. The id of the trigger type
    * @param $paramarray array. Extra parameters to pass the trigger; foo=bar,bar=foo
    * @return boolean. TRUE if the trigger created successfully, FALSE if not
*/
function trigger($triggertype, $paramarray='')
{
    global $sit, $CONFIG, $dbg, $dbTriggers;
    if ($CONFIG['debug'])
    {
        foreach (array_keys($paramarray) as $key)
        {
            //parse parameter array
            $dbg .= "\$paramarray[$key] = " .$paramarray[$key]."\n";
            
            //TODO do we need to check for any 'special' keys here?
        }
    }

    //find relevant triggers
    $sql = "SELECT * FROM `{$dbTriggers}` WHERE triggerid={$triggertype} ";    
    $query = mysql_query($sql);
    while($result = mysql_fetch_object($query))
    {
        //if we have any params from the actual trigger, append to user params
        if(!empty($result->parameters))
        {
            $resultparams = explode(",", $result->parameters);
            foreach($resultparams as $assigns)
            {
                $values = explode("=", $assigns);
                $paramarray[$values[0]] = $values[1];
                if($CONFIG['debug']) $dbg .= "\$paramarray[{$values[0]}] = {$values[1]}\n";
            }
        }
        
        if($CONFIG['debug'])
        {
            $dbg .= "TRIGGER: trigger_action({$result->userid}, {$triggertype}, {$result->action}, {$paramarray}) called \n";
        }
        trigger_action($result->userid, $triggertype, $result->action, $paramarray);
    }
}

/**
    * Do the specific action for the specific user for a trigger
    * @author Kieran Hogg
    * @param $userid
    * @param $triggertypeif ($CONFIG['debug']) 
    * @return boolean. TRUE if the user has the permission, otherwise FALSE
*/
function trigger_action($userid, $triggertype, $action, $paramarray)
{
    global $CONFIG, $dbg;
    if($CONFIG['debug']) 
    {
        $dbg .= "TRIGGER: trigger_action($userid, $triggertype, $action, $paramarray) received\n";
    }

    switch($action)
    {
        case ACTION_EMAIL:
            send_trigger_email($userid, $triggertype, $paramarray);
            break;

        case ACTION_NOTICE:
            if($CONFIG['debug']) 
            {
                $dbg .= "TRIGGER: create_notice($userid, '', $triggertype, $paramarray) called";
            }
            create_notice($userid, '', $triggertype, $paramarray);
            break;

        case ACTION_NONE:
        //fallthrough
        default:
            break;
    }
}

/**
    * Returns TRUE or FALSE to indicate whether a given user has a given permission
    * @author Ivan Lucas
    * @param $userid integer. The userid to check
    * @param $permission integer. The permission id to check
    * @return boolean. TRUE if the user has the permission, otherwise FALSE
*/
function trigger_replace_specials($string, $paramarray)
{
    global $CONFIG, $application_version, $application_version_string, $dbg;
    global $dbIncidents;
    if($CONFIG['debug'])
    {
        $dbg .= "TRIGGER: notice string before - $string\n";
        $dbg .= "TRIGGER: param array: ".print_r($paramarray);
    }
    
    $url = parse_url($_SERVER['HTTP_REFERER']);
    $baseurl = "{$url['scheme']}://{$url['host']}{$CONFIG['application_webpath']}";

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
function send_trigger_email($userid, $triggertype, $paramarray)
{
    if($CONFIG['debug']) $dbg .= "send_trigger_email({$userid}, {$triggertype}, {$paramarray})";
    //if we have an incidentid, get it to pass to emailtype_replace_specials()
    if (!empty($paramarray['incidentid']))
    {
        $incidentid = $paramarray['incidentid'];
    }
    
    $sql = "SELECT * FROM emailtypes WHERE triggerid={$triggertype}";
    $query = mysql_query($sql);
    if ($query)
    {
        $result = mysql_fetch_object($query);
    }
    $string = $result->body;
    $email = emailtype_replace_specials($string, $incidentid, $userid);
    if($CONFIG['debug']) $dbg .= $email;

}


?>
