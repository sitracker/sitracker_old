<?php
// incoming.inc.php - Displays tempincoming data
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

// Included by ../incident.php

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}

$incomingid = cleanvar($_REQUEST['id']);

$sql = "SELECT * FROM tempincoming WHERE id='{$incomingid}'";
$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
$action = cleanvar($_REQUEST['action']);

while ($incoming = mysql_fetch_object($result))
{
    if(!$incoming->locked)
    {
        //it's not locked, lock for this user
        $lockeduntil=date('Y-m-d H:i:s',$now+$CONFIG['record_lock_delay']);
        $sql = "UPDATE tempincoming SET locked='{$sit[2]}', lockeduntil='{$lockeduntil}' WHERE tempincoming.id='{$incomingid}' AND (locked = 0 OR locked IS NULL)";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        $lockedby = "you";
    }
    elseif($incoming->locked != $sit[2])
    {
        $lockedby = $row->locked;
    }
    else
        $lockedby = "you";

    echo "<div class='detailinfo'>";
    if (!empty($incoming->reason)) echo "<div class='detaildate'>{$incoming->reason}</div>";
    echo "Locked by: {$lockedby}</div>";

    //echo "<pre>".print_r($incoming,true)."</pre>";
    $usql = "SELECT * FROM updates WHERE id='{$incoming->updateid}'";
    $uresult = mysql_query($usql);
    while ($update = mysql_fetch_object($uresult))
    {
        $updatetime = readable_date($update->timestamp);
        echo "<div class='detailhead'><div class='detaildate'>{$updatetime}</div>From <strong>".stripslashes($incoming->emailfrom)."</strong></div>";
        echo "<div class='detailentry'>";
        echo parse_updatebody($update->bodytext);
        echo "</div>";
    }
}

?>
