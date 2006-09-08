<?php
// index.php - Welcome screen and login form
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// This Page Is Valid XHTML 1.0 Transitional! 31Oct05

// Since this is the first page people will visit check if the include path is correct
if (($fp = @fopen($filename, 'r', 1)) and fclose($fp) == FALSE)
{
    header("Location: setup.php");
    exit;
}

require('db_connect.inc.php');
require('functions.inc.php');

session_name($CONFIG['session_name']);
session_start();

if ($_SESSION['auth'] != TRUE)
{
    // External variables
    $id = cleanvar($_REQUEST['id']);
    $page = cleanvar(str_replace('..','',str_replace('//','',str_replace(':','',urldecode($_REQUEST['page'])))));

    // Invalid user, show log in form
    include('htmlheader.inc.php');
    if ($id==1) echo "<p class='error'>Enter your credentials to login to {$CONFIG['application_shortname']}</p><br />";
    if ($id==2) echo "<p class='error'>Your session has expired or you have not yet logged in</p><br />";
    if ($id==3) throw_user_error("Invalid username/password combination");
    echo "<div style='width: 25%; margin-left: auto; margin-right: auto; margin-top: 1em; padding: 2em;'>";
    ?>
    <h2>Login to <?php echo $CONFIG['application_shortname']; ?></h2>
    <form action="login.php" method="post">

    <br />
    <label>Username:<br /><input name="username" size="20" type="text" /></label><br />
    <label>Password:<br /><input name="password" size="20" type="password" /></label><br />
    <?php
    echo "<input type='hidden' name='page' value='$page' />";
    ?>
    <input type="submit" value="Log In" />
    <?php
    echo "</form>";
    echo "</div>";
    include('htmlfooter.inc.php');
}
else
{
    // User is validated, jump to main
    header("Location: main.php");
    exit;
}
?>