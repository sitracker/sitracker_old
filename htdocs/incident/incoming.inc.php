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

if($_REQUEST['action'] == "updatereason")
{
    $newreason = cleanvar($_REQUEST['newreason']);
    $update = "UPDATE tempincoming SET reason='{$newreason}' WHERE id={$incomingid}";
    $result = mysql_query($update);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

}


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
        $lockedbyname = "you";
    }
    elseif($incoming->locked != $sit[2])
    {
        $lockedby = $incoming->locked;
        $lockedbysql = "SELECT realname FROM users WHERE id={$lockedby}";
        $lockedbyresult = mysql_query($lockedbysql);
        //if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        while($row = mysql_fetch_object($lockedbyresult))
            $lockedbyname = $row->realname;
    }
    else
        $lockedbyname = "you";

    echo "<div class='detailinfo'>";
    if (!empty($incoming->reason))
    {
        if($lockedbyname == "you")
        {
            echo "<div class='detaildate'>
                    <form method='POST' action='{$_SERVER['PHP_SELF']}?id={$incomingid}&win=incomingview&action=updatereason'>
                    <input name='newreason' value='{$incoming->reason}'>
                    <input type='submit' value='Save'>
                    </form>
                </div>";
        }
        else
        {
            echo "<div class='detaildate'>{$incoming->reason}</div>";
        }
    }
    
    echo "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/locked.png' alt='Locked' /> Locked by {$lockedbyname}</div>";

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
