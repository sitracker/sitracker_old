<?php
// login.php - processes the login
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

@include ('set_include_path.inc.php');
require ('db_connect.inc.php');

session_name($CONFIG['session_name']);
session_start();

if (function_exists('session_regenerate_id'))
{
    if (!version_compare(phpversion(),"5.1.0",">=")) session_regenerate_id(TRUE);
    else session_regenerate_id();
}

if (!version_compare(phpversion(),"4.3.3",">=")) setcookie(session_name(), session_id(),ini_get("session.cookie_lifetime"), "/");

$language = $_POST['lang'];

require ('functions.inc.php');

// External vars
$password = md5($_REQUEST['password']);
$username = cleanvar($_REQUEST['username']);
$public_browser = cleanvar($_REQUEST['public_browser']);
$page = strip_tags(str_replace('..','',str_replace('//','',str_replace(':','',urldecode($_REQUEST['page'])))));

if (empty($_REQUEST['username']) AND empty($_REQUEST['password']) AND $language != $_SESSION['lang'])
{
    if ($language!='default')
    {
        $_SESSION['lang'] = $language;
    }
    else
    {
        $_SESSION['lang'] = '';
    }
    header ("Location: index.php");
}
elseif (authenticate($username, $password) == 1)
{
    // Valid user
    $_SESSION['auth'] = TRUE;

    // Retrieve users profile
    $sql = "SELECT * FROM `{$dbUsers}` WHERE username='$username' AND password='$password' LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(), E_USER_ERROR);
    if (mysql_num_rows($result) < 1)
    {
        $_SESSION['auth'] = FALSE;
        trigger_error("No such user", E_USER_ERROR);
    }
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
    $_SESSION['utcoffset'] = $user->var_utc_offset;

    // Delete any old session user notices
    $sql = "DELETE FROM `{$dbNotices}` WHERE durability='session' AND userid={$_SESSION['userid']}";
    mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(), E_USER_ERROR);

    //check if the session lang is different the their profiles
    if ($_SESSION['lang'] != '' AND $_SESSION['lang'] != $user->var_i18n)
    {
        //FIXME need to implement this trigger
        trigger("TRIGGER_LANGUAGE_DIFFERS", array('currentlang' => $user->var_i18n, 'sessionlang' => $_SESSION['lang']));
    }

    if ($user->var_i18n != $CONFIG['default_i18n'] AND $_SESSION['lang']=='')
    {
        $_SESSION['lang'] = $user->var_i18n;
    }

    // Make an array full of users permissions
    // The zero permission is added to all users, zero means everybody can access
    $userpermissions[] = 0;
    // First lookup the role permissions
    $sql = "SELECT * FROM `{$dbUsers}` AS u, `{$dbRolePermissions}` AS rp WHERE u.roleid = rp.roleid ";
    $sql .= "AND u.id = '{$_SESSION['userid']}' AND granted='true'";
    $result = mysql_query($sql);
    if (mysql_error())
    {
        $_SESSION['auth'] = FALSE;
        trigger_error(mysql_error(),E_USER_ERROR);
    }
    if (mysql_num_rows($result) >= 1)
    {
        while ($perm = mysql_fetch_object($result))
        {
            $userpermissions[]=$perm->permissionid;
        }
    }

    // Next lookup the individual users permissions
    $sql = "SELECT * FROM `{$dbUserPermissions}` WHERE userid = '{$_SESSION['userid']}' AND granted='true' ";
    $result = mysql_query($sql);
    if (mysql_error())
    {
        $_SESSION['auth'] = FALSE;
        trigger_error(mysql_error(),E_USER_ERROR);
    }
    if (mysql_num_rows($result) >= 1)
    {
        while ($perm = mysql_fetch_object($result))
        {
            $userpermissions[]=$perm->permissionid;
        }
    }
    else
    {
        $_SESSION['auth'] = FALSE;
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
elseif ($CONFIG['portal'] == TRUE)
{
    // Invalid user and portal enabled
    // Have a look if this is a contact trying to login
    $portalpassword = cleanvar($_REQUEST['password']);
    //we need plaintext and md5 as contacts created pre 3.35 will be in plaintext
    $sql = "SELECT * FROM `{$dbContacts}` WHERE username='{$username}' AND (password='{$portalpassword}' OR password=MD5('{$portalpassword}')) LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    if (mysql_num_rows($result) >= 1)
    {
        $contact = mysql_fetch_object($result);

        // Customer session
        // Valid user
        $_SESSION['portalauth'] = TRUE;
        $_SESSION['contactid'] = $contact->id;
        $_SESSION['siteid'] = $contact->siteid;
        
        //if we're an admin contact
        if(admin_contact_contracts($_SESSION['contactid'], $_SESSION['siteid']) != NULL)
        {
            $_SESSION['contracts'] = admin_contact_contracts($_SESSION['contactid'], $_SESSION['siteid']);
            $_SESSION['usertype'] = 'admin';
        }
        //if we're a named contact
        elseif(contact_contracts($_SESSION['contactid'], $_SESSION['siteid']) != NULL)
        {
            $_SESSION['contracts'] = contact_contracts($_SESSION['contactid'], $_SESSION['siteid']);
            $_SESSION['usertype'] = 'contact';
        }
        //we're a contact(we logged in) but not on any contracts
        elseif(all_contact_contracts($_SESSION['contactid'], $_SESSION['siteid']) != NULL)
        {
            $_SESSION['contracts'] = all_contact_contracts($_SESSION['contactid'], $_SESSION['siteid']);
            $_SESSION['usertype'] = 'user';
        }

        
        
        header("Location: portal/");
        exit;
    }

    // Login failure
    $_SESSION['auth'] = FALSE;
    $_SESSION['portalauth'] = FALSE;
    // log the failure
    if ($username != '')
    {
        $errdate = date('M j H:i');
        $errmsg = "$errdate Failed login for user '{$username}' from IP: {$_SERVER['REMOTE_ADDR']}";
        $errmsg .= "\n";
        $errlog = @error_log($errmsg, 3, $CONFIG['access_logfile']);
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
