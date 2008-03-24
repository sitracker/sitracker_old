<?php
/*
portal/close.inc.php - Request incident closure in the portal included by ../portal.php

SiT (Support Incident Tracker) - Support call tracking system
Copyright (C) 2000-2008 Salford Software Ltd. and Contributors

This software may be used and distributed according to the terms
of the GNU General Public License, incorporated herein by reference.
*/

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}


if (empty($_REQUEST['reason']))
{
    $id = $_REQUEST['id'];
    echo "<h2>{$strClosureRequestForIncident} {$_REQUEST['id']}</h2>";
    echo "<div id='update' align='center'><form action='{$_SERVER[PHP_SELF]}?page=close&amp;id={$id}' method='POST'>";
    echo "<p>{$strReason}:</p><textarea name='reason' cols='50' rows='10'></textarea><br />"; 
    echo "<input type='submit'></form></div>";
}
else
{
    $usersql = "SELECT forenames, surname FROM `{$dbContacts}` WHERE id={$_SESSION['contactid']}";
    $result = mysql_query($usersql);
    $user = mysql_fetch_object($result);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    // FIXME i18n ? In db ?
    $reason = "Incident closure requested via the portal by <b>{$user->forenames} {$user->surname}</b>\n\n";
    $reason .= "<b>Reason:</b> {$_REQUEST['reason']}";
    $sql = "INSERT into `{$dbUpdates}` (incidentid, userid, type, currentstatus, bodytext, timestamp, customervisibility) ";
    $sql .= "VALUES('{$_REQUEST['id']}', '0', 'customerclosurerequest',  '1', '{$reason}',
    '{$now}', 'show')";
    mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    //set incident back to active
    $sql = "UPDATE `{$dbIncidents}` SET status=1, lastupdated={$now} WHERE id={$_REQUEST['id']}";
    mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);


    html_redirect("portal.php?page=incidents");
}
?>