<?php

include ('db_connect.inc.php');
include ('functions.inc.php');
include ('triggers.inc.php');

include ('htmlheader.inc.php');
trigger(TRIGGER_INCIDENT_CREATED, array('incidentid' => 1, 'incidenttitle' => "test incident"));
include ('htmlfooter.inc.php');
?>
