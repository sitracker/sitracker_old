<?php
// triggers.inc.php - Handle triggers
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.

//set up all the trigger types
define('TRIGGER_INCIDENT_CREATED', 1);
define('TRIGGER_INCIDENT_ASSIGNED', 2);
define('TRIGGER_INCIDENT_ASSIGNED_WHILE_AWAY', 3);
define('TRIGGER_INCIDENT_ASSIGNED_WHILE_OFFLINE', 4);
define('TRIGGER_INCIDENT_NEARING_SLA', 5);
define('TRIGGER_USERS_INCIDENT_NEARING_SLA', 6);
define('TRIGGER_INCIDENT_EXCEEDED_SLA', 7);
define('TRIGGER_INCIDENT_REVIEW_DUE', 8);
define('TRIGGER_CRITICAL_INCIDENT_LOGGED', 9);
define('TRIGGER_KB_CREATED', 10);
define('TRIGGER_NEW_HELD_EMAIL', 11);
define('TRIGGER_WAITING_HELP_EMAIL', 12);
define('TRIGGER_USER_SET_TO_AWAY', 13);
define('TRIGGER_SIT_UPGRADED', 14);
define('TRIGGER_USER_RETURNS', 15);
define('TRIGGER_INCIDENT_OWNED_CLOSED_BY_USER', 16);

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

function trigger($triggertype, $paramarray='')
{
    global $sit, $CONFIG, $dbg;
    
    //quick sanity check
    if (!is_numeric($triggertype) OR $triggertype < 1 OR $triggertype > 16) return;

    switch($triggertype)
    {
        case TRIGGER_INCIDENT_CREATED:
        {
            if ($CONFIG['debug']) $dbg .= "TRIGGER_INCIDENT_CREATED<br />";
            if ($paramarray != '')
            {
                if ($CONFIG['debug']) $dbg .= "Params passed:<br />";
                foreach(array_keys($paramarray) as $key)
                {
                    //parse parameter array
                    //TODO define the keys to look for other than 'user'
                    if ($CONFIG['debug']) $dbg .= "\$paramarray[$key] = " .$paramarray[$key]."<br />";
                    
                    //get user to apply trigger to
                    if ($key == 'user')
                    {
                        $user = $key;
                    }
                    else
                    {
                        $user = $sit[2];
                    }
                }
            }
        }
            break;

        case TRIGGER_INCIDENT_ASSIGNED:
            echo "TRIGGER_INCIDENT_ASSIGNED";
            break;
        case TRIGGER_INCIDENT_ASSIGNED_WHILE_AWAY:
            echo "TRIGGER_INCIDENT_ASSIGNED_WHILE_AWAY";
            break;
        case TRIGGER_INCIDENT_ASSIGNED_WHILE_OFFLINE:
            echo "TRIGGER_INCIDENT_ASSIGNED_WHILE_OFFLINE";
            break;
        case TRIGGER_INCIDENT_NEARING_SLA:
            echo "TRIGGER_INCIDENT_NEARING_SLA";
            break;
        case TRIGGER_USERS_INCIDENT_NEARING_SLA:
            echo "TRIGGER_USERS_INCIDENT_NEARING_SLA";
            break;
        case TRIGGER_INCIDENT_EXCEEDED_SLA:
            echo "TRIGGER_INCIDENT_EXCEEDED_SLA";
            break;
        case TRIGGER_INCIDENT_REVIEW_DUE:
            echo "TRIGGER_INCIDENT_REVIEW_DUE";
            break;
        case TRIGGER_CRITICAL_INCIDENT_LOGGED:
            echo "TRIGGER_CRITICAL_INCIDENT_LOGGED";
            break;
        case TRIGGER_KB_CREATED:
            echo "TRIGGER_KB_CREATED";
            break;
        case TRIGGER_NEW_HELD_EMAIL:
            echo "TRIGGER_NEW_HELD_EMAIL";
            break;
        case TRIGGER_WAITING_HELP_EMAIL:
            echo "TRIGGER_WAITING_HELP_EMAIL";
            break;
        case TRIGGER_USER_SET_TO_AWAY:
            echo "TRIGGER_USER_SET_TO_AWAY";
            break;
        case TRIGGER_SIT_UPGRADED:
            echo "TRIGGER_SIT_UPGRADED";
            break;
        case TRIGGER_USER_RETURNS:
            echo "TRIGGER_USER_RETURNS";
            break;
        case TRIGGER_INCIDENT_OWNED_CLOSED_BY_USER:
            echo "TRIGGER_INCIDENT_OWNED_CLOSED_BY_USER";
            break;
        default:
            echo "hit default!";
            break;
    } 

}


?>
