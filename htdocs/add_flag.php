<?php
// add_flag.php - Adds a flag to a record
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>

$permission=0; // not required
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

$flagname = $_REQUEST['flagname'];
$recordid = $_REQUEST['recordid'];
$flagtype = $_REQUEST['flagtype'];

if(empty($flagname))
{
    include('htmlheader.inc.php');
    echo "<h2>Add flag</h2>";
    echo "<form name='flagform' action='".$_SERVER['PHP_SELF']."' method='post'>";
    echo "<table align='center' class='vertical'>";
    echo "<tr><th>Flag:</th><td><input maxlength='255' name='flagname' size='40' /></td></tr>";
    echo "</table>";
    echo "<p align='center'><input name='submit' type='submit' value='Add' /></p>";
    echo "<input name='recordid' type='hidden' value='$recordid' />";
    echo "<input name='flagtype' type='hidden' value='$flagtype' />";
    echo "</form>";
    include('htmlfooter.inc.php');
}
else
{
    $success = add_flag($recordid, $flagtype, $flagname);
    if($success)
    {
        include('htmlheader.inc.php');
        confirmation_page("2", "main.php", "<h2>Flag added Successfully</h2><p align='center'>Please wait while you are redirected...</p>");
        include('htmlfooter.inc.php');
    }
    else
    {
        include('htmlheader.inc.php');
        echo "<h2>Error occured adding flag</h2>";
        include('htmlfooter.inc.php');
    }
}

?>