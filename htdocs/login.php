<?php
// login.php - processes the login
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Ivan Lucas

require('db_connect.inc.php');
require('functions.inc.php');

session_start();

// External vars
$password=md5($_REQUEST['password']);
$username = cleanvar($_REQUEST['username']);
$public_browser = cleanvar($_REQUEST['public_browser']);

if (authenticate($username, $password) == 1)
{
    // Valid user
    $_SESSION['auth']==TRUE;
    if (!$public_browser=='yes')
    {
        // set the cookie (expires in 30 days)
        setcookie("sit[0]", $username, time()+2592000);
        setcookie("sit[1]", $password, time()+2592000);
        setcookie("sit[2]", user_id($username, $password), time()+2592000);
    }
    else
    {
        // set the cookie for a public machine (expires in 30 mins)
        setcookie("sit[0]", $username, time()+1800);
        setcookie("sit[1]", $password, time()+1800);
        setcookie("sit[2]", user_id($username, $password), time()+1800);
        setcookie("sit[3]", 'public', time()+1800);
    }
    // redirect
    header ("Location: main.php?pg=welcome");
    exit;
}
else
{
    // Invalid user
    // TODO target v3.25 Have a look if this is a contact trying to login

    $_SESSION['auth']==FALSE;
    // log the failure
    if ($username!='')
    {
        $errdate=date('M j H:i');
        $errmsg="$errdate Failed login for user '$username' from IP: {$_SERVER['REMOTE_ADDR']}";
        $errmsg.="\n";
        $errlog=@error_log($errmsg, 3, $CONFIG['access_logfile']);
        ## if (!$errlog) echo "Fatal error logging this problem<br />";
        unset($errdate);
        unset($errmsg);
        unset($errlog);
    }
    // redirect

    header ("Location: index.php?id=3");
    exit;
}
?>