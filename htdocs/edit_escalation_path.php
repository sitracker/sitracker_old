<?php
// edit_escalation_path - Ability to edit escalation path
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>

//// This Page Is Valid XHTML 1.0 Transitional!  (7 Oct 2006)

$permission=64; // Manage escalation paths
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

if(empty($_REQUEST['mode']))
{
    $title = "Edit escalation path";
    //show page
    $id = $_REQUEST['id'];
    $sql = "SELECT * FROM escalationpaths WHERE id = {$id}";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

    include('htmlheader.inc.php');
    ?>
    <script type='text/javascript'>
    function confirm_submit()
    {
        return window.confirm('Are you sure you want to edit this escalation path?');
    }
    </script>
    <?php

    echo "<h2>{$title}</h2>";

    while($details = mysql_fetch_object($result))
    {
        echo "<form action='".$_SERVER['PHP_SELF']."' method='post' onsubmit='return confirm_submit()'>";
        echo "<table class='vertical'>";

        echo "<tr><td class='shade2' align='right'><b>Name:</b></td><td><input name='name' value='{$details->name}'/></td></tr>";
        echo "<tr><td class='shade2' align='right'><b>Track URL:</b><br />Note: insert '%externalid%' for automatic incident number insertion</td><td><input name='trackurl' value='{$details->track_url}' /></td></tr>";
        echo "<tr><td class='shade2' align='right'><b>Home URL:</b></td><td><input name='homeurl' value='{$details->home_url}' /></td></tr>";
        echo "<tr><td class='shade2' align='right'><b>Title:</b></td><td><input name='title' value='{$details->url_title}' /></td></tr>";
        echo "<tr><td class='shade2' align='right'><b>Email domain:</b></td><td><input name='emaildomain' value='{$details->email_domain}' /></td></tr>";

        echo "</table>";
        echo "<input type='hidden' value='{$id}' name='id' />";
        echo "<input type='hidden' value='edit' name='mode' />";
        echo "<p align='center'><input type='submit' name='submit' value='Edit' /></p>";

        echo "</form>";
    }
    include('htmlfooter.inc.php');
}
else
{
    //make changes
    $id = cleanvar($_REQUEST['id']);
    $name = cleanvar($_REQUEST['name']);
    $trackurl = cleanvar($_REQUEST['trackurl']);
    $homeurl = cleanvar($_REQUEST['homeurl']);
    $title = cleanvar($_REQUEST['title']);
    $emaildomain = cleanvar($_REQUEST['emaildomain']);

    $errors = 0;
    if(empty($name))
    {
        $errors++;
        echo "<p class='error'>You must enter a name for the escalation path</p>\n";
    }

    if($errors == 0)
    {
        $sql = "UPDATE escalationpaths SET name = '{$name}', track_url = '{$trackurl}', ";
        $sql .= " home_url = '{$homeurl}', url_title = '{$title}', email_domain = '{$emaildomain}' ";
        $sql .= " WHERE id = '{$id}'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        if(!$result) echo "<p class='error'>Edit of escalation path failed</p>";
        else
        {
            confirmation_page("2", "escalation_paths.php", "<h2>Escalation path modified</h2><h5>Please wait while you are redirected...</h5>");
        }
    }
}

?>
