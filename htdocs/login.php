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
session_regenerate_id(TRUE);

// External vars
$password = md5($_REQUEST['password']);
$username = cleanvar($_REQUEST['username']);
$public_browser = cleanvar($_REQUEST['public_browser']);

if (authenticate($username, $password) == 1)
{
    // Valid user
    $_SESSION['auth'] = TRUE;

    // Retreive users profile
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password' LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(), E_USER_ERROR);
    if (mysql_num_rows($result) < 1) trigger_error("No such user", E_USER_ERROR);
    $user = mysql_fetch_object($result);
    // Profile
    $_SESSION['userid'] = $user->id;
    $_SESSION['username'] = $user->username;
    $_SESSION['realname'] = $user->realname;
    $_SESSION['email'] = $user->email;
    $_SESSION['style'] = $user->var_style;
    $_SESSION['incident_refresh'] = $user->var_incident_refresh;
    $_SESSION['update_order'] = $user->var_update_order;
    $_SESSION['collapse'] = $user->var_collapse;

    // Get an array full of users permissions
    // First lookup the role permissions
    $sql = "SELECT * FROM users, rolepermissions WHERE users.roleid=rolepermissions.roleid ";
    $sql .= "AND users.id = '{$_SESSION['userid']}' AND granted='true'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    if (mysql_num_rows($result) >= 1)
    {
        while ($perm = mysql_fetch_object($result))
        {
            $userpermissions[]=$perm->permissionid;
        }
    }
    // Next lookup the individual users permissions
    $sql = "SELECT * FROM userpermissions WHERE userid = '{$_SESSION['userid']}' AND granted='true' ";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    if (mysql_num_rows($result) >= 1)
    {
        while ($perm = mysql_fetch_object($result))
        {
            $userpermissions[]=$perm->permissionid;
        }
    }
    $_SESSION['permissions'] = array_unique($userpermissions);

/*
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
        setcookie("sit[2]", $userid, time()+1800);
        setcookie("sit[3]", 'public', time()+1800);
    }
    */
    // redirect
    header ("Location: main.php?pg=welcome");
    exit;
}
else
{
    // Invalid user
    // TODO target v3.25 Have a look if this is a contact trying to login

    $_SESSION['auth'] = FALSE;
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