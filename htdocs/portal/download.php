<?php
// download.php - Pass a file to the browser for download
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas, <ivanlucas[at]users.sourceforge.net

@include ('../set_include_path.inc.php');
$permission = 0; // no permission required

require ($lib_path.'db_connect.inc.php');
require ($lib_path.'functions.inc.php');

$accesslevel = 'any';

require ('portalauth.inc.php');
// External variables
$id = cleanvar(intval($_GET['id']));

$sql = "SELECT *, u.id AS updateid, f.id AS fileid
        FROM `{$dbFiles}` AS f, `{$dbLinks}` AS l, `{$dbUpdates}` AS u
        WHERE l.linktype='5'
        AND l.origcolref=u.id
        AND l.linkcolref='{$id}'
        AND l.direction='left'
        AND l.linkcolref=f.id
        ORDER BY f.filedate DESC";

$result = mysql_query($sql);
$fileobj = mysql_fetch_object($result);
$incidentid = cleanvar(intval($fileobj->incidentid));
$updateid = cleanvar(intval($fileobj->updateid));
$filename = cleanvar($fileobj->filename);
$fileid = $fileobj->fileid;
$visibility = $fileobj->category;

$access = FALSE;
if ($visibility == 'public' AND isset($_SESSION['contactid']))
{
    $access = TRUE;
}
else
{
    $access = FALSE;
}

$file_fspath = "{$CONFIG['attachment_fspath']}{$incidentid}{$fsdelim}{$fileid}-{$filename}";

if ($incidentid == 0 OR empty($incidentid))
{
    $file_fspath = "{$CONFIG['attachment_fspath']}updates{$fsdelim}{$fileid}-{$filename}";
    $old_style = "{$CONFIG['attachment_fspath']}updates{$fsdelim}{$filename}";
}
else
{
    $file_fspath = "{$CONFIG['attachment_fspath']}{$incidentid}{$fsdelim}{$fileid}-{$filename}";
    $old_style = "{$CONFIG['attachment_fspath']}{$incidentid}{$fsdelim}u{$updateid}{$fsdelim}{$filename}";
}

if (!file_exists($file_fspath) AND !file_exists($old_style))
{
    header('HTTP/1.1 404 Not Found');
    header('Status: 403 Not Found',1,403);
    echo "<h3>404 File Not Found</h3>";
    echo "<p>{$file}</p>";
    exit;
}
elseif ($access == TRUE)
{
    $file_size = filesize($file_fspath);
    $fp = fopen($file_fspath, 'r');
    
    if ($fp && ($file_size !=-1))
    {
        if (file_exists($file_fspath))
        {
            header("Content-Type: ".mime_content_type($filename)."\r\n");
            header("Content-Length: {$file_size}\r\n");
            header("Content-Disposition-Type: attachment\r\n");
            header("Content-Disposition: filename={$filename}\r\n");
        }
        elseif(file_exists($old_style))
        {
            header("Content-Type: ".mime_content_type($old_style)."\r\n");
            header("Content-Length: {$file_size}\r\n");
            header("Content-Disposition-Type: attachment\r\n");
            header("Content-Disposition: filename={$old_style}\r\n");
        }
        
        $buffer = '';
        while (!feof($fp))
        {
            $buffer = fread($fp, 1024*1024);
            print $buffer;
        }
        fclose($fp);
        exit;
    }
}
else
{
    // Access Denied
    header('HTTP/1.1 403 Forbidden');
    header('Status: 403 Forbidden',1,403);
    echo "<h3>403 Forbidden</h3>";
    exit;
}


?>
