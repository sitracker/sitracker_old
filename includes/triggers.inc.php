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
    $i; $j;
}
                        
//define all the actions
$i = 0;
$j = 1;
foreach($actionarray as $action)
{
    define($actionarray[$i], $j);
    $i; $j;
}

function trigger($triggertype, $paramarray='')
{
    global $sit, $CONFIG, $dbg;

    //quick sanity check
    if (!is_numeric($triggertype) OR $triggertype < 1 OR $triggertype > 16) return;
    foreach (array_keys($paramarray) as $key)
    {
        //parse parameter array
        //TODO define the keys to look for other than 'user'
        if ($CONFIG['debug']) $dbg .= "\$paramarray[$key] = " .$paramarray[$key]."<br />";
        
        //get user to apply trigger to
        if ($key == 'user')
        {
            $user = $paramarray['user'];
        }
    }
    
    //find relevant triggers
    //FIXME use db var
    $sql = "SELECT * FROM triggers WHERE triggerid={$triggertype} ";
    if($user) $sql .= "AND user={$user}";
    $query = mysql_query($sql);
    while($result = mysql_fetch_object($query))
    {
        trigger_action($result->userid, $result->action, $result->parameters);
    }
}


function trigger_action($userid, $action, $parameters)
{

    switch($action)
    {
        case ACTION_EMAIL:
            echo "sendemail()";
            break;
            
        case ACTION_NOTICE:
            echo "sendnotice()";
            break;
                
        case ACTION_NONE:
        //fallthrough
        default:
            break;
    }
}




?>
