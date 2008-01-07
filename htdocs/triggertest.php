<?php
@include ('set_include_path.inc.php');
$permission = 0;
require ('db_connect.inc.php');
require ('functions.inc.php');
require ('triggers.inc.php');
// This page requires authentication
require ('auth.inc.php');

include ('htmlheader.inc.php');
trigger(TRIGGER_INCIDENT_CREATED, array('incidentid' => 1, 'incidenttitle' => "test incident"));
trigger(TRIGGER_INCIDENT_OWNED_CLOSED_BY_USER, array('incidentid' => 1, 'incidenttitle' => "test incident", 'engineerclosedname' => "Joe Blogs"));
trigger(TRIGGER_INCIDENT_CREATED, array('incidentid' => 1, 'incidenttitle' => "test incident"));
trigger(TRIGGER_INCIDENT_ASSIGNED, array('incidentid' => 1, 'incidenttitle' => "test incident", 'user' => 2));
trigger(TRIGGER_INCIDENT_ASSIGNED_WHILE_AWAY, array('incidentid' => 1, 'incidenttitle' => "test incident", 'user' => 2));
trigger(TRIGGER_INCIDENT_ASSIGNED_WHILE_OFFLINE, array('incidentid' => 1, 'incidenttitle' => "test incident", 'user' => 2));
trigger(TRIGGER_INCIDENT_NEARING_SLA, array('incidentid' => 1, 'incidenttitle' => "test incident"));
trigger(TRIGGER_USERS_INCIDENT_NEARING_SLA, array('incidentid' => 1, 'incidenttitle' => "test incident"));
trigger(TRIGGER_INCIDENT_EXCEEDED_SLA, array('incidentid' => 1, 'incidenttitle' => "test incident"));
trigger(TRIGGER_INCIDENT_REVIEW_DUE, array('incidentid' => 1, 'incidenttitle' => "test incident"));
trigger(TRIGGER_CRITICAL_INCIDENT_LOGGED, array('incidentid' => 1, 'incidenttitle' => "test incident"));
trigger(TRIGGER_KB_CREATED, array('KBname' => 'KB Article'));
trigger(TRIGGER_NEW_HELD_EMAIL, '');
trigger(TRIGGER_WAITING_HELP_EMAIL, array('holdingmins' => 30));
trigger(TRIGGER_USER_SET_TO_AWAY, array('user' => 1));
trigger(TRIGGER_SIT_UPGRADED, array('version' => 3.40));
trigger(TRIGGER_USER_RETURNS, array('user' => 1));
trigger(TRIGGER_INCIDENT_OWNED_CLOSED_BY_USER, array('incidentid' => 1, 'incidenttitle' => "test incident"));
        
include ('htmlfooter.inc.php');
?>
