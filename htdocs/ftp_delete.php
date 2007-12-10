<?php
// ftp_delete.php -
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

@include('set_include_path.inc.php');
$permission=44; // Publish Files to FTP site

require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

// External variables
$id = cleanvar($_REQUEST['id']);

$sql = "SELECT * FROM files WHERE id='$id'";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
$frow=mysql_fetch_array($result);

// set up basic connection
$conn_id = ftp_connect($CONFIG['ftp_hostname']);

// login with username and password
$login_result = ftp_login($conn_id, $CONFIG['ftp_username'], $CONFIG['ftp_user_pass']);

// check connection
if ((!$conn_id) || (!$login_result))
{
    throw_error("FTP Connection failed, connecting to {$CONFIG['ftp_hostname']} for user {$CONFIG['ftp_username']}",'');
}
if ($frow['path']!='')
{
    // delete private file
    $filewithpath=$CONFIG['ftp_path'] . $frow['path'] . $frow['filename'];
    $filepath=$CONFIG['ftp_path'] . $frow['path'];
    $dele=ftp_delete($conn_id, $filewithpath);
    if (!$dele) throw_error('Error deleting FTP file:', $filewithpath);
    // remove the directory if it's not a public one
    if ($filepath!=$CONFIG['ftp_path'])
    {
        $dele=ftp_delete($conn_id, $filepath);
        if (!$dele) throw_error('Error deleting FTP folder:', $filepath);
    }
}
else
{
    // delete public file
    $filewithpath=$CONFIG['ftp_path'] . $frow['filename'];
    $filepath=$CONFIG['ftp_path'] . $frow['path'];
    $dele=ftp_delete($conn_id, $filewithpath);
    if (!$dele) throw_error('Error deleting FTP file:', $filewithpath);
    // remove the directory if it's not a public one
    if ($filepath!=$CONFIG['ftp_path'])
    {
        $dele=ftp_delete($conn_id, $filepath);
        if (!$dele) throw_error('Error deleting FTP folder:', $filepath);
    }
}
// close the FTP stream
ftp_quit($conn_id);

// remove file from database
$sql = "DELETE FROM files WHERE id='$id'";
mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
journal(CFG_JOURNAL_OTHER, 'FTP File Deleted', "File {$frow['filename']} was deleted from FTP", CFG_JOURNAL_PRODUCTS, 0);

html_redirect("ftp_list_files.php");
?>
