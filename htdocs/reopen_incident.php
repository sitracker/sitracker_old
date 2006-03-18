<?php
// reopen_incident.php - Form for re-opening a closed incident
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

$permission=34; // Reopen Incidents

require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

// External variables
$submit = cleanvar($_REQUEST['submit']);
$id = cleanvar($_REQUEST['id']);

if (empty($submit))
{
    // No submit detected show update form
    $incident_title=incident_title($id);
    $title = 'Reopen: '.$id . " - " . $incident_title;
    include('incident_html_top.inc.php');
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
    include('incident_html_bottom.inc.php');
}
else
{
    // Reopen the incident
    // update incident
    $time = time();
    $sql = "UPDATE incidents SET status=$newstatus, lastupdated=$time, closed=0 WHERE id=$id";
    mysql_query($sql);

    // add update
    $sql  = "INSERT INTO updates (incidentid, userid, type, bodytext, timestamp) ";
    $sql .= "VALUES ($id, $sit[2], 'reopening', '$bodytext', $time)";
    $result = mysql_query($sql);

    if (!$result)
    {
        include('includes/incident_html_top.inc');
        echo "<p class='error'>Update Failed</p>\n";
        include('incident_htmlfooter.inc.php');
    }
    else
    {
        confirmation_page("2", "incident_details.php?id=" . $id, "<h2>Incident Reopened</h2><p align='center'>Please wait while you are redirected...</h2>");
    }
}
?>