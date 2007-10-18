<?php
// add_escalation_path.php - Display a form for adding an escalation path
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>

//// This Page Is Valid XHTML 1.0 Transitional!  (1 Oct 2006)

$permission=64; // Manage escalation paths

require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

$submit = $_REQUEST['submit'];

$title = $strNewEscalationPath;



if(empty($submit))
{
    include('htmlheader.inc.php');
    ?>
    <script type='text/javascript'>
    function confirm_submit()
    {
        return window.confirm('Are you sure you want to add this escalation path?');
    }
    </script>
    <?php

    echo "<h2>{$title}</h2>";

    echo "<form action='".$_SERVER['PHP_SELF']."' method='post' onsubmit='return confirm_submit()'>";
    echo "<table class='vertical'>";

    echo "<tr><th>{$strName}:</th><td><input name='name' /></td></tr>";
    echo "<tr><th>Track URL:<br />Note: insert '%externalid%' for automatic incident number insertion</th><td><input name='trackurl' /></td></tr>";
    echo "<tr><th>Home URL:</th><td><input name='homeurl' /></td></tr>";
    echo "<tr><th>{$strTitle}:</th><td><input name='title' /></td></tr>";
    echo "<tr><th>Email domain:</th><td><input name='emaildomain' /></td></tr>";

    echo "</table>";

    echo "<p align='center'><input type='submit' name='submit' value='{$strAdd}' /></p>";

    echo "</form>";

    include('htmlfooter.inc.php');
}
else
{
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
        $sql = "INSERT INTO escalationpaths (name,track_url,home_url,url_title,email_domain) VALUES ";
        $sql .= " ('{$name}','{$trackurl}','{$homeurl}','{$title}','{$emaildomain}')";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        if(!$result) echo "<p class='error'>Addition of escalation path failed</p>";
        else
        {
            confirmation_page("2", "escalation_paths.php", "<h2>Escalation path added</h2><h5>{$strPleaseWaitRedirect}...</h5>");
        }
    }
}


?>