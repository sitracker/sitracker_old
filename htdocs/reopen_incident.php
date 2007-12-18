<?php
// reopen_incident.php - Form for re-opening a closed incident
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

@include ('set_include_path.inc.php');
$permission=34; // Reopen Incidents

require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

// External variables
$submit = cleanvar($_REQUEST['submit']);
$id = cleanvar($_REQUEST['id']);
$newstatus = cleanvar($_REQUEST['newstatus']);
$bodytext = cleanvar($_REQUEST['bodytext']);

if (empty($submit))
{
    // No submit detected show update form
    $incident_title=incident_title($id);
    $title = 'Reopen: '.$id . " - " . $incident_title;
    include ('incident_html_top.inc.php');
    ?>
    <h2>Reopen Incident</h2>
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>?id=<?php echo $id ?>" method="post">
    <table class='vertical'>
    <tr><th>Update:</th><td><textarea name="bodytext" rows='20' cols='60'></textarea></td></tr>
    <tr><th>Status:</th><td><?php echo incidentstatus_drop_down("newstatus", 1); ?></td></tr>
    </table>
    <p><input name="submit" type="submit" value="Reopen Incident" /></p>
    </form>
    <?php
    include ('incident_html_bottom.inc.php');
}
else
{
    // Reopen the incident
    // update incident
    $time = time();
    $sql = "UPDATE incidents SET status='$newstatus', lastupdated='$time', closed='0' WHERE id='$id' LIMIT 1";
    mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

    // add update
    $sql  = "INSERT INTO updates (incidentid, userid, type, bodytext, timestamp) ";
    $sql .= "VALUES ($id, $sit[2], 'reopening', '$bodytext', $time)";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

    // Insert the first SLA update for the reopened incident, this indicates the start of an sla period
    // This insert could possibly be merged with another of the 'updates' records, but for now we keep it seperate for clarity
    $sql  = "INSERT INTO updates (incidentid, userid, type, timestamp, currentowner, currentstatus, customervisibility, sla, bodytext) ";
    $sql .= "VALUES ('$id', '".$sit[2]."', 'slamet', '$now', '".$sit[2]."', '1', 'show', 'opened','The incident is open and awaiting action.')";
    mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    // Insert the first Review update, this indicates the review period of an incident has restarted
    // This insert could possibly be merged with another of the 'updates' records, but for now we keep it seperate for clarity
    $sql  = "INSERT INTO updates (incidentid, userid, type, timestamp, currentowner, currentstatus, customervisibility, sla, bodytext) ";
    $sql .= "VALUES ('$id', '0', 'reviewmet', '$now', '".$sit[2]."', '1', 'hide', 'opened','')";
    mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    if (!$result)
    {
        include ('includes/incident_html_top.inc');
        echo "<p class='error'>Update Failed</p>\n";
        include ('incident_htmlfooter.inc.php');
    }
    else
    {
        html_redirect("incident_details.php?id={$id}");
    }
}
?>