<?php
// portal/update.php - Update incidents in the portal
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author Kieran Hogg <kieran_hogg[at]users.sourceforge.net>

@include ('set_include_path.inc.php');
require 'db_connect.inc.php';
require 'functions.inc.php';

$accesslevel = 'any';

include 'portalauth.inc.php';
include 'portalheader.inc.php';

if (empty($_POST['update']) AND empty($_FILES))
{
    $id = $_REQUEST['id'];
    echo "<h2>".icon('note', 32, $strUpdateIncident);
    echo " {$strUpdateIncident} {$_REQUEST['id']}</h2>";
    echo "<div id='update' align='center'><form action='{$_SERVER[PHP_SELF]}?page=update&amp;id={$id}' method='post' enctype='multipart/form-data'>";
    echo "<p>{$strUpdate}:</p><textarea cols='60' rows='10' name='update'></textarea><br />";
    echo "<p>".icon('attach', 16, $strAttachment);
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
    echo "<input type='submit' value=\"{$strUpdate}\"/></form></div>";
    
    include 'htmlfooter.inc.php';
}
else
{
    $id = intval($_REQUEST['id']);
    $usersql = "SELECT forenames, surname FROM `{$dbContacts}` WHERE id={$_SESSION['contactid']}";
    $result = mysql_query($usersql);
    $user = mysql_fetch_object($result);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    $forenames = cleanvar($user->forenames);
    $surname = $user->surname;
    $update = cleanvar($_REQUEST['update']);
    
    if (!empty($forenames) AND !empty($surname))
    {
        //TODO change order for a name such as Chinese?
        $updatebody = "<hr>".sprintf($SYSLANG['strUpdatedViaThePortalBy'], "[b]{$forenames}", "{$surname}[/b]")."\n\n";
    }
    else
    {
        $updatebody = "<hr>".sprintf($SYSLANG['strUpdatedViaThePortalBy'], "[b]{$strCustomer}[/b]")."\n\n";
    }

    if (!empty($_FILES['attachment']['name']))
    {
        $filename = cleanvar($_FILES['attachment']['name']);
        $sql = "INSERT INTO `{$dbFiles}`(category, filename, size, userid, usertype, shortdescription, longdescription, filedate) ";
        $sql .= "VALUES ('public', '{$filename}', '{$_FILES['attachment']['size']}', '{$_SESSION['contactid']}', 'contact', '', '', NOW())";
        mysql_query($sql);
        if (mysql_error())
        {
            $errors++;
            trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        }
        else
        {
            $fileid = mysql_insert_id();
            $updatebody = "{$SYSLANG['strAttachment']}: [[att={$fileid}]]{$filename}[[/att]]".$updatebody;
        }
    }
    //add the update
    $updatebody .= $update;
    
    $sql = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, currentstatus, bodytext, timestamp, customervisibility) ";
    $sql .= "VALUES('{$id}', '0', 'webupdate', '1', '{$updatebody}', '{$now}', 'show')";
    mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    else
    {
        $updateid = mysql_insert_id();
    }
    
    //upload file, here because we need updateid
    if ($_FILES['attachment']['name'] != "")
    {
        // try to figure out what delimeter is being used (for windows or unix)...
        //.... // $delim = (strstr($filesarray[$c],"/")) ? "/" : "\\";
        $delim = (strstr($_FILES['attachment']['tmp_name'],"/")) ? "/" : "\\";

        // make incident attachment dir if it doesn't exist
        $umask = umask(0000);
        if (!file_exists("{$CONFIG['attachment_fspath']}{$id}{$fsdelim}u{$updateid}"))
        {
            $mk = @mkdir("{$CONFIG['attachment_fspath']}{$id}{$fsdelim}u{$updateid}", 0770, TRUE);
            if (!$mk)
            {
                $errors++;
                $sql = "DELETE FROM `{$dbUpdates}` WHERE id='{$updateid}'";
                mysql_query($sql);
                if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
                throw_error("Failed creating incident attachment directory: {$CONFIG['attachment_fspath']}{$id}{$fsdelim}u{$updateid}");
            }
        }
        umask($umask);
        $newfilename = "{$CONFIG['attachment_fspath']}{$id}{$delim}u{$updateid}{$fsdelim}{$_FILES['attachment']['name']}";

        // Move the uploaded file from the temp directory into the incidents attachment dir
        $mv = move_uploaded_file($_FILES['attachment']['tmp_name'], $newfilename);
        if (!$mv)
        {
            $errors++;
            $sql = "DELETE FROM `{$dbUpdates}` WHERE id='{$updateid}'";
            mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            trigger_error('!Error: Problem moving attachment from temp directory to: '.$newfilename, E_USER_WARNING);
        }

        // Check file size before attaching
        $att_max_filesize = return_bytes($CONFIG['upload_max_filesize']);
        if ($_FILES['attachment']['size'] > $att_max_filesize)
        {
            $errors++;
            $sql = "DELETE FROM `{$dbUpdates}` WHERE id='{$updateid}'";
            mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            throw_error('User Error: Attachment too large or file upload error - size:',$_FILES['attachment']['size']);
            // throwing an error isn't the nicest thing to do for the user but there seems to be no guaranteed
            // way of checking file sizes at the client end before the attachment is uploaded. - INL
        }
        $filename = cleanvar($_FILES['attachment']['name']);
    }
    
    //create link
    $sql = "INSERT INTO `{$dbLinks}`(linktype, origcolref, linkcolref, direction, userid) ";
    $sql .= "VALUES(5, '{$updateid}', '{$fileid}', 'left', '0')";
    mysql_query($sql);
    if (mysql_error())
    {
        $errors++;
        trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    }
    
    //set incident back to active
    $id = intval($_REQUEST['id']);
    $sql = "UPDATE `{$dbIncidents}` SET status=1, lastupdated='$now' WHERE id='{$id}'";
    mysql_query($sql);
    if (mysql_error())
    {
        $errors++;
        trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    }
    
    if ($errors > 0)
    {
        html_redirect($_SERVER['PHP_SELF']."?id={$id}", FALSE);
    }
    else
    {
        html_redirect("incident.php?id={$id}");
    }
}


?>