<?php

include ('db_connect.inc.php');
include ('functions.inc.php');
include ('triggers.inc.php');

include ('htmlheader.inc.php');
trigger(TRIGGER_INCIDENT_CREATED, array('incidentid' => 1, 'incidenttitle' => "test incident"));
trigger(TRIGGER_INCIDENT_OWNED_CLOSED_BY_USER, array('incidentid' => 1, 'incidenttitle' => "test incident", 'engineerclosedname' => "Joe Blogs"));
include ('htmlfooter.inc.php');
?>
