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
// This page requires authentication
require ('auth.inc.php');

// External variables
$file = cleanvar($_REQUEST['file']);
$incidentid = cleanvar($_REQUEST['incidentid']);
$incidentpath = cleanvar($_REQUEST['p']);

$file_fspath = "{$CONFIG['attachment_fspath']}{$incidentid}";
if (!empty($incidentpath)) $file_fspath .= "{$fsdelim}{$incidentpath}";
$file_fspath .= "{$fsdelim}{$file}";



if (!file_exists($file_fspath))
{
    header('HTTP/1.1 404 Not Found');
    header('Status: 403 Not Found',1,403);
    echo "<h3>404 File Not Found</h3>";
    echo "<p>{$file}</p>";
        echo $file_fspath;
    exit;
}
elseif (TRUE == TRUE) // FIXME we need some checking here, is the user allowed to download the file?
{
    $file_size = filesize($file_fspath);
    $fp = fopen($file_fspath, 'r');
    if ($fp && ($file_size !=-1))
    {
        header("Content-Type: application/octet-stream\r\n");
        header("Content-Length: {$file_size}\r\n");
        header("Content-Disposition-Type: attachment\r\n");
        header("Content-Disposition: filename={$file}\r\n");
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