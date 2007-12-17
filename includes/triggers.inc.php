<?php
// triggers.inc.php - Handle triggers
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.

//set up all the trigger types
define('INCIDENT_CREATED_TRIGGER', 1);
define('INCIDENT_ASSIGNED_TRIGGER', 2);
define('INCIDENT_ASSIGNED_WHILE_AWAY_TRIGGER', 3);
define('INCIDENT_ASSIGNED_WHILE_OFFLINE_TRIGGER', 4);
define('INCIDENT_NEARING_SLA_TRIGGER', 5);
define('USERS_INCIDENT_NEARING_SLA_TRIGGER', 6);
define('INCIDENT_EXCEEDED_SLA_TRIGGER', 7);
define('INCIDENT_REVIEW_DUE', 8);
define('CRITICAL_INCIDENT_LOGGED', 9);
define('KB_CREATED_TRIGGER', 10);
define('NEW_HELD_EMAIL_TRIGGER', 11);
define('WAITING_HELP_EMAIL', 12);
define('USER_SET_TO_AWAY_TRIGGER', 13);
define('SIT_UPGRADED_TRIGGER', 14);
define('USER_RETURNS_TRIGGER', 15);
define('INCIDENT_OWNED_CLOSED_BY_USER_TRIGGER', 16);

function trigger($triggertype, $paramarray='')
{
    global $sit, $CONFIG, $dbg;
    
    //quick sanity check
    if(!is_numeric($triggertype) OR $triggertype < 1 OR $triggertype > 16) return;

    switch($triggertype)
    {
        case INCIDENT_CREATED_TRIGGER:
        {
            if($CONFIG['debug']) $dbg .= "INCIDENT_CREATED_TRIGGER<br />";
            if($paramarray != '')
            {
                if($CONFIG['debug']) $dbg .= "Params passed:<br />";
                foreach(array_keys($paramarray) as $key)
                {
                    //parse parameter array
                    //TODO define the keys to look for other than 'user'
                    if($CONFIG['debug']) $dbg .= "\$paramarray[$key] = " .$paramarray[$key]."<br />";
                    
                    //get user to apply trigger to
                    if($key == 'user')
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

        case INCIDENT_ASSIGNED_TRIGGER:
            echo "INCIDENT_ASSIGNED_TRIGGER";
            break;
        case INCIDENT_ASSIGNED_WHILE_AWAY_TRIGGER:
            echo "INCIDENT_ASSIGNED_WHILE_AWAY_TRIGGER";
            break;
        case INCIDENT_ASSIGNED_WHILE_OFFLINE_TRIGGER:
            echo "INCIDENT_ASSIGNED_WHILE_OFFLINE_TRIGGER";
            break;
        case INCIDENT_NEARING_SLA_TRIGGER:
            echo "INCIDENT_NEARING_SLA_TRIGGER";
            break;
        case USERS_INCIDENT_NEARING_SLA_TRIGGER:
            echo "USERS_INCIDENT_NEARING_SLA_TRIGGER";
            break;
        case INCIDENT_EXCEEDED_SLA_TRIGGER:
            echo "INCIDENT_EXCEEDED_SLA_TRIGGER";
            break;
        case INCIDENT_REVIEW_DUE:
            echo "INCIDENT_REVIEW_DUE";
            break;
        case CRITICAL_INCIDENT_LOGGED:
            echo "CRITICAL_INCIDENT_LOGGED";
            break;
        case KB_CREATED_TRIGGER:
            echo "KB_CREATED_TRIGGER";
            break;
        case NEW_HELD_EMAIL_TRIGGER:
            echo "NEW_HELD_EMAIL_TRIGGER";
            break;
        case WAITING_HELP_EMAIL:
            echo "WAITING_HELP_EMAIL";
            break;
        case USER_SET_TO_AWAY_TRIGGER:
            echo "USER_SET_TO_AWAY_TRIGGER";
            break;
        case SIT_UPGRADED_TRIGGER:
            echo "SIT_UPGRADED_TRIGGER";
            break;
        case USER_RETURNS_TRIGGER:
            echo "USER_RETURNS_TRIGGER";
            break;
        case INCIDENT_OWNED_CLOSED_BY_USER_TRIGGER:
            echo "INCIDENT_OWNED_CLOSED_BY_USER_TRIGGER";
            break;
        default:
            echo "hit default!";
            break;
    } 

}


?>
