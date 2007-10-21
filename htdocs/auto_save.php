<?php
// auto_save.php - Page to auto save content
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>

$permission=0; // not required
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

$userid = cleanvar($_REQUEST['userid']);
$incidentid = cleanvar($_REQUEST['incidentid']);
$type = cleanvar($_REQUEST['type']);
$draftid = cleanvar($_REQUEST['draftid']);
$metacontent = cleanvar($_REQUEST['meta']);
$content = cleanvar($_REQUEST['content']);
$now = time();

if($userid == $_SESSION['userid'])
{
    if($draftid == -1)
    {
    //check your changing your own
        $sql = "INSERT INTO drafts (userid,incidentid,type,meta,content,lastupdate) VALUES ('{$userid}','{$incidentid}','{$type}','{$meta}','{$content}','{$now}')";
    }
    else
    {
        $sql = "UPDATE drafts SET content = '{$content}', meta = '{$meta}', lastupdate = '{$now}' WHERE id = {$draftid}";
    }
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    echo mysql_insert_id();
}

?>