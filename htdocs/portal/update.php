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
    echo "<div id='update' align='center'><form action='{$_SERVER[PHP_SELF]}?page=update&amp;id=$id' method='post' enctype='multipart/form-data'>";
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
    print_r($_FILES);
    $id = intval($_REQUEST['id']);
    $usersql = "SELECT forenames, surname FROM `{$dbContacts}` WHERE id={$_SESSION['contactid']}";
    $result = mysql_query($usersql);
    $user = mysql_fetch_object($result);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    // attach file
    $att_max_filesize = return_bytes($CONFIG['upload_max_filesize']);
    $incident_attachment_fspath = $CONFIG['attachment_fspath'] . $id;
    if ($_FILES['attachment']['name'] != "")
    {
        // try to figure out what delimeter is being used (for windows or unix)...
        //.... // $delim = (strstr($filesarray[$c],"/")) ? "/" : "\\";
        $delim = (strstr($_FILES['attachment']['tmp_name'],"/")) ? "/" : "\\";

        // make incident attachment dir if it doesn't exist
        $umask = umask(0000);
        echo $CONFIG['attachment_fspath'] . "$id";
        if (!file_exists($CONFIG['attachment_fspath'] . "$id"))
        {
            $mk = @mkdir($CONFIG['attachment_fspath'] ."$id", 0770);
            if (!$mk) throw_error('Failed creating incident attachment directory: ',$incident_attachment_fspath .$id);
        }
        $mk = @mkdir($CONFIG['attachment_fspath'] .$id . "{$delim}{$now}", 0770);
        if (!$mk) throw_error('Failed creating incident attachment (timestamp) directory: ',$incident_attachment_fspath .$id . "{$delim}{$now}");
        umask($umask);
        $newfilename = $incident_attachment_fspath.$delim.$now.$delim.$_FILES['attachment']['name'];

        // Move the uploaded file from the temp directory into the incidents attachment dir
        $mv = move_uploaded_file($_FILES['attachment']['tmp_name'], $newfilename);
        if (!$mv) trigger_error('!Error: Problem moving attachment from temp directory to: '.$newfilename, E_USER_WARNING);

        //$mv=move_uploaded_file($attachment, "$filename");
        //if (!mv) throw_error('!Error: Problem moving attachment from temp directory:',$filename);

        // Check file size before attaching
        if ($_FILES['attachment']['size'] > $att_max_filesize)
        {
            throw_error('User Error: Attachment too large or file upload error - size:',$_FILES['attachment']['size']);
            // throwing an error isn't the nicest thing to do for the user but there seems to be no guaranteed
            // way of checking file sizes at the client end before the attachment is uploaded. - INL
        }
    }

    //add the update
    $update = "Updated via the portal by <b>{$user->forenames} {$user->surname}</b>\n\n";
    $update .= $_REQUEST['update'];
    $update .= "\n\n<hr>Attachment: [[att]]{$_FILES['attachment']['name']}[[/att]]";
    $sql = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, currentstatus, bodytext, timestamp, customervisibility) ";
    $sql .= "VALUES('{$id}', '0', 'webupdate', '1', '{$update}', '{$now}', 'show')";
    mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    //set incident back to active
    $id = intval($_REQUEST['id']);
    $sql = "UPDATE `{$dbIncidents}` SET status=1, lastupdated='$now' WHERE id='{$id}'";
    mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);


    html_redirect("index.php");
}


?>