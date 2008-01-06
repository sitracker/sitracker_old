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
include ('htmlfooter.inc.php');
?>
