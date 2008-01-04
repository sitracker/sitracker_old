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

function trigger($triggertype, $paramarray='')
{
    global $sit, $CONFIG, $dbg, $dbTriggers;
    //quick sanity check
    if (!is_numeric($triggertype) OR $triggertype < 1 OR $triggertype > 16) return;
    
    foreach (array_keys($paramarray) as $key)
    {
        //parse parameter array
        if ($CONFIG['debug']) $dbg .= "\$paramarray[$key] = " .$paramarray[$key]."\n";
    }

    //find relevant triggers
    //FIXME use db var
    $sql = "SELECT * FROM `{$dbTriggers}` WHERE triggerid={$triggertype} ";
    if($CONFIG['debug']) $dbg .= $sql."\n";
    if($user) $sql .= "AND user={$user}";
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
                if($CONFIG['debug']) $dbg .= "\$paramarray[{$values[0]}] = {$values[1]}";
            }
        }
        
        if($CONFIG['debug'])
        {
            $dbg .= "TRIGGER: trigger_action({$result->userid}, {$triggertype}, {$result->action}, {$paramarray}) called \n";
        }
        trigger_action($result->userid, $triggertype, $result->action, $paramarray);
    }
}


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
            echo "sendemail()";
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


?>
