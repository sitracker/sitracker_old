<?php
// login.php - processes the login
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

require('db_connect.inc.php');

session_name($CONFIG['session_name']);
session_start();
session_regenerate_id(TRUE);

$language = $_POST['lang'];

require('functions.inc.php');

// External vars
$password = md5($_REQUEST['password']);
$username = cleanvar($_REQUEST['username']);
$public_browser = cleanvar($_REQUEST['public_browser']);
$page = strip_tags(str_replace('..','',str_replace('//','',str_replace(':','',urldecode($_REQUEST['page'])))));

if(empty($_REQUEST['username']) AND empty($_REQUEST['password']) AND $language != $_SESSION['lang'])
{
    $_SESSION['lang'] = $language;
    header ("Location: index.php");
}
elseif (authenticate($username, $password) == 1)
{
    // Valid user
    $_SESSION['auth'] = TRUE;

    // Retrieve users profile
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
    $_SESSION['num_update_view'] = $user->var_num_updates_view;
    $_SESSION['collapse'] = $user->var_collapse;
    $_SESSION['groupid'] = is_null($user->groupid) ? 0 : $user->groupid;
    if (!empty($user->var_i18n)) $_SESSION['lang'] = $user->var_i18n;

    // Dismiss any old session user notices
    $sql = "UPDATE usernotices SET dismissed=2 WHERE durability='session' AND userid={$_SESSION['userid']}";
    mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(), E_USER_ERROR);

    //check if the session lang is different the their profiles
    if($_SESSION['lang'] != $user->var_lang)
    {
        $sql = "INSERT INTO usernotices VALUES(3, $user->id, '0', 'session') ";
        $sql .= "ON DUPLICATE KEY UPDATE noticeid=3";
        mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(), E_USER_ERROR);
    }

    // Make an array full of users permissions
    // The zero permission is added to all users, zero means everybody can access
    $userpermissions[]=0;
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

    // redirect
    if (empty($page))
    {
        header ("Location: main.php?pg=welcome");
        exit;
    }
    else
    {
        header("Location: {$page}");
        exit;
    }
}
elseif($CONFIG['portal'] == TRUE)
{
    // Invalid user and portal enabled
    // Have a look if this is a contact trying to login
    $portalpassword=cleanvar($_REQUEST['password']);
    $sql = "SELECT * FROM contacts WHERE username='$username' AND password='$portalpassword' LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    if (mysql_num_rows($result) >= 1)
    {
        $contact = mysql_fetch_object($result);

        // Customer session
        // Valid user
        $_SESSION['portalauth'] = TRUE;

        $_SESSION['contactid'] = $contact->id;
        header("Location: portal.php");
        exit;
    }

    // Login failure
    $_SESSION['auth'] = FALSE;
    $_SESSION['portalauth'] = FALSE;
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
else
{
    //invalid user and portal disabled
    header ("Location: index.php?id=3");
    exit;
}
?>
