<?php
// delete_user.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Authors: Valdemaras Pipiras <info[at]ambernet.lt>
//          Ivan Lucas <ivanlucas[at]users.sourceforge.net>

@include ('set_include_path.inc.php');
$permission=20;  // Manage users
require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

// External variables
$userid = cleanvar($_REQUEST['userid']);

if (!empty($userid))
{
    $errors=0;
    // Check there are no files linked to this user
    $sql = "SELECT userid FROM files WHERE userid=$userid LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_num_rows($result)>=1) $errors++;

    // check there are no links linked to this product
    $sql = "SELECT userid FROM links WHERE userid=$userid LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_num_rows($result)>=1) $errors++;

    // check there are no notes linked to this product
    $sql = "SELECT userid FROM notes WHERE userid=$userid LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_num_rows($result)>=1) $errors++;

    // Check there is no software linked to this user
    $sql = "SELECT softwareid FROM usersoftware WHERE userid=$userid LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_num_rows($result)>=1) $errors++;

    // Check there are no incidents linked to this user
    $sql = "SELECT id FROM `{$dbIncidents}` WHERE owner=$userid OR towner=$userid LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_num_rows($result)>=1) $errors++;

    // Check there are no updates by this user
    $sql = "SELECT id FROM updates WHERE userid=$userid LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_num_rows($result)>=1) $errors++;

    // FIXME need to check more tables for data possibly linked to userid
    // We break data integrity if we delete the user and there are things
    // related to him/her

    if ($errors==0)
    {
        $sql = Array();
        $sql[] = "DELETE FROM users WHERE id = $userid LIMIT 1";
        $sql[] = "DELETE FROM holidays WHERE userid = $userid";
        $sql[] = "DELETE FROM usergroups WHERE userid = $userid";
        $sql[] = "DELETE FROM userpermissions WHERE userid = $userid";

        foreach ($sql as $query)
        {
            $result = mysql_query($query);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        }

        journal(CFG_LOGGING_NORMAL, 'User Removed', "User $userid was removed", CFG_JOURNAL_USERS, $userid);
        html_redirect("users.php");
    }
    else
    {
        include ('htmlheader.inc.php');
        // FIXME i18n error
        echo "<p class='error'>Sorry, this user cannot be deleted because it has been associated with one or more files, links, notes or skills/software</p>";
        echo "<p align='center'><a href='users.php#{$userid}'>Return to users list</a></p>";
        include ('htmlfooter.inc.php');
    }
}
else
{
    throw_error("Could not delete user", "Parameter(s) missing");
}
?>