<?php
// delete_update.php - Deletes incident updates (log entries) from the database
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

$permission=42; // Delete Incident Updates
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

// External variables
$updateid = cleanvar($_REQUEST['updateid']);
$timestamp = cleanvar($_REQUEST['timestamp']);
$tempid = cleanvar($_REQUEST['tempid']);

if (empty($updateid)) throw_error('!Error: Update ID was not set, not deleting!', $updateid);

// We delete using ID and timestamp to make sure we dont' delete the wrong update by accident
$sql = "DELETE FROM updates WHERE id='$updateid' AND timestamp='$timestamp'";  // We might in theory have more than one ...
mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

$sql = "DELETE FROM tempincoming WHERE id='$tempid'";
mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

function deldir($location)
{
    if (substr($location,-1) <> "/")
        $location = $location."/";
    $all=opendir($location);
    while ($file=readdir($all))
    {
        if (is_dir($location.$file) && $file <> ".." && $file <> ".")
        {
            deldir($location.$file);
            rmdir($location.$file);
            unset($file);
        }
        elseif (!is_dir($location.$file))
        {
            unlink($location.$file);
            unset($file);
        }
    }
    rmdir($location);
}

$path=$incident_attachment_fspath.'updates/'.$updateid;
if (file_exists($path)) deldir($path);

journal(CFG_LOGGING_NORMAL, 'Incident Log Entry Deleted', "Incident Log Entry $updateid was deleted from Incident $incidentid", CFG_JOURNAL_INCIDENTS, $incidentid);
confirmation_page("0", "incident_details.php", "<h2>Delete Successful</p><p align='center'>Please wait while you are redirected...</h2>");
?>