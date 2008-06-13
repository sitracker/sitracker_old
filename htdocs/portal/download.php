<?php
// download.php - Pass a file to the browser for download
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas, <ivanlucas[at]users.sourceforge.net

@include ('set_include_path.inc.php');
$permission = 0; // no permission required

require ('db_connect.inc.php');
require ('functions.inc.php');

$accesslevel = 'any';

require ('portalauth.inc.php');
// External variables
$id = cleanvar(intval($_GET['id']));

$sql = "SELECT *, u.id AS updateid
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

$file_fspath = "{$CONFIG['attachment_fspath']}{$incidentid}{$fsdelim}u{$updateid}{$fsdelim}{$filename}";

if (!file_exists($file_fspath))
{
    header('HTTP/1.1 404 Not Found');
    header('Status: 403 Not Found',1,403);
    echo "<h3>404 File Not Found</h3>";
    echo "<p>{$file}</p>";
        echo $file_fspath;
    exit;
}
elseif ($access == TRUE)
{
    $file_size = filesize($file_fspath);
    $fp = fopen($file_fspath, 'r');
    $file_ext = substr($file_fspath, ((strlen($file_fspath)-1 - strrpos($file_fspath, '.')) * -1 ));

    $display_mimetypes['jpg'] = 'image/jpeg';
    $display_mimetypes['txt'] = 'text/plain';
    if ($fp && ($file_size !=-1))
    {
        if (array_key_exists($file_ext, $display_mimetypes))
        {
            header("Content-Type: {$display_mimetypes[$file_ext]}\r\n");
            header("Content-Length: {$file_size}\r\n");
            header("Content-Disposition: filename={$filename}\r\n");
        }
        else
        {
            header("Content-Type: application/octet-stream\r\n");
            header("Content-Length: {$file_size}\r\n");
            header("Content-Disposition-Type: attachment\r\n");
            header("Content-Disposition: filename={$filename}\r\n");
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