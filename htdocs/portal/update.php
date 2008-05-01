<?php
// portal/update.php - Update incidents in the portal

// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors

// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.

include 'portalheader.inc.php';


if (empty($_POST['update']) AND empty($_FILES))
{
    $id = $_REQUEST['id'];
    echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/note.png' alt='{$strUpdateIncident}' /> {$strUpdateIncident} {$_REQUEST['id']}</h2>";
    echo "<div id='update' align='center'><form action='{$_SERVER[PHP_SELF]}?page=update&amp;id={$id}' method='post' enctype='multipart/form-data'>";
    echo "<p>{$strUpdate}:</p><textarea cols='60' rows='10' name='update'></textarea><br />";
    echo "<p><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/attach.png' alt='{$strAttachment}' />";
    // calculate upload filesize
    $j = 0;
    $ext = array($strBytes, $strKBytes, $strMBytes, $strGBytes, $strTBytes);
    $att_file_size = $CONFIG['upload_max_filesize'];
    while ($att_file_size >= pow(1024,$j))
    {
        ++$j;
    }

    $att_file_size = round($att_file_size / pow(1024,$j-1) * 100) / 100 . ' ' . $ext[$j-1];

    echo "{$strAttachment} ";

    echo "(&lt;{$att_file_size}): ";
    echo "<input type='hidden' name='MAX_FILE_SIZE' value='{$CONFIG['upload_max_filesize']}' />";
    echo "<input type='file' name='attachment' size='40' maxfilesize='{$CONFIG['upload_max_filesize']}' /></p>";
    echo "<input type='submit' value=\"{$strSave}\"/></form></div>";
    
    include 'htmlfooter.inc.php';
}
else
{
    $id = intval($_REQUEST['id']);
    $usersql = "SELECT forenames, surname FROM `{$dbContacts}` WHERE id={$_SESSION['contactid']}";
    $result = mysql_query($usersql);
    $user = mysql_fetch_object($result);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    //add the update
    $update = "Updated via the portal by <b>{$user->forenames} {$user->surname}</b>\n\n";
    $update .= $_REQUEST['update'];
    
    if($filename = upload_file($_FILES['attachment'], $id, 'incident'))
    {
        $update .= "\n\n<hr>Attachment: [[att]]{$filename}[[/att]]";
    }
    $sql = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, currentstatus, bodytext, timestamp, customervisibility) ";
    $sql .= "VALUES('{$id}', '0', 'webupdate', '1', '{$update}', '{$now}', 'show')";
    mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    //set incident back to active
    $id = intval($_REQUEST['id']);
    $sql = "UPDATE `{$dbIncidents}` SET status=1, lastupdated='$now' WHERE id='{$id}'";
    mysql_query($sql);
    if (mysql_error())
    {
        trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        html_redirect($_SERVER['PHP_SELF']."?id={$id}", FALSE);
    }
    else
    {
        html_redirect("index.php");
    }
}


?>