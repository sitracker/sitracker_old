<?php
// portal/update.php - Update incidents in the portal

// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors

// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.

include 'portalheader.inc.php';


if (empty($_REQUEST['update']))
{
    $id = $_REQUEST['id'];
    echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/note.png' alt='{$strUpdateIncident}' /> {$strUpdateIncident} {$_REQUEST['id']}</h2>";
    echo "<div id='update' align='center'><form action='{$_SERVER[PHP_SELF]}?page=update&amp;id=$id' method='post'>";
    echo "<p>{$strUpdate}:</p><textarea cols='50' rows='10' name='update'></textarea><br />";
    echo "<input type='submit' value=\"{$strSave}\"/></form></div>";
}
else
{
    $usersql = "SELECT forenames, surname FROM `{$dbContacts}` WHERE id={$_SESSION['contactid']}";
    $result = mysql_query($usersql);
    $user = mysql_fetch_object($result);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    //add the update
    $update = "Updated via the portal by <b>{$user->forenames} {$user->surname}</b>\n\n";
    $update .= $_REQUEST['update'];
    $sql = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, currentstatus, bodytext, timestamp, customervisibility) ";
    $sql .= "VALUES('{$_REQUEST['id']}', '0', 'webupdate', '1', '{$update}', '{$now}', 'show')";
    mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    //set incident back to active
    $sql = "UPDATE `{$dbIncidents}` SET status=1, lastupdated=$now WHERE id={$_REQUEST['id']}";
    mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);


    html_redirect("portal.php?page=incidents");
}

include 'htmlfooter.inc.php';

?>