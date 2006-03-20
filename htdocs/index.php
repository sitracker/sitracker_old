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

require('db_connect.inc.php');
require('functions.inc.php');

session_start();

if ($_SESSION['auth'] != TRUE)
{
    // External variables
    $id = cleanvar($_REQUEST['id']);

    // Invalid user, show log in form
    include('htmlheader.inc.php');
    ?>

    <h2>Login to <?php echo $CONFIG['application_shortname']; ?></h2>
    <form action="login.php" method="post">
    <div style='width: 40%; margin-left: 40%;'>
    <?php
    if ($id==1) echo "<p class='error'>Enter your credentials to login to {$CONFIG['application_shortname']}</p><br />";
    if ($id==2) echo "<p class='error'>You must login before accessing {$CONFIG['application_shortname']} functions</p><br />";
    if ($id==3) echo "<p class='error'>Invalid username/password combination</p><br />";
    ?>
    <br />
    <label>Username:<br /><input name="username" size="20" type="text" /></label><br />
    <label>Password:<br /><input name="password" size="20" type="password" /></label><br />
    <input type="submit" value="Log In" />
    <?php
    echo "</div>";
    echo "</form>";

    include('htmlfooter.inc.php');
}
else
{
    // User is validated, jump to main
    header("Location: main.php");
    exit;
}
?>