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

@include ('set_include_path.inc.php');
$permission = 0; // not required
require ($lib_path.'db_connect.inc.php');
require ($lib_path.'functions.inc.php');

// This page requires authentication
require ($lib_path.'auth.inc.php');

$userid = cleanvar($_REQUEST['userid']);
$incidentid = cleanvar($_REQUEST['incidentid']);
$type = cleanvar($_REQUEST['type']);
$draftid = cleanvar($_REQUEST['draftid']);
$meta = cleanvar($_REQUEST['meta']);
$content = cleanvar($_REQUEST['content']);

if ($userid == $_SESSION['userid'])
{
    if ($draftid == -1)
    {
        $sql = "INSERT INTO `{$dbDrafts}` (userid,incidentid,type,meta,content,lastupdate) VALUES ('{$userid}','{$incidentid}','{$type}','{$meta}','{$content}','{$now}')";
    }
    else
    {
        $sql = "UPDATE `{$dbDrafts}` SET content = '{$content}', meta = '{$meta}', lastupdate = '{$now}' WHERE id = {$draftid}";
    }
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    echo mysql_insert_id();
}

?>
