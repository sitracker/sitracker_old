<?php
// add_tag.php - Adds a tag to a record
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

$tagname = $_REQUEST['tagname'];
$recordid = $_REQUEST['recordid'];
$tagtype = $_REQUEST['tagtype'];

if(empty($tagname))
{
    include('htmlheader.inc.php');
    echo "<h2>Add tag</h2>";
    echo "<form name='tagform' action='".$_SERVER['PHP_SELF']."' method='post'>";
    echo "<table align='center' class='vertical'>";
    echo "<tr><th>Tag:</th><td><input maxlength='255' name='tagname' size='40' /></td></tr>";
    echo "</table>";
    echo "<p align='center'><input name='submit' type='submit' value='Add' /></p>";
    echo "<input name='recordid' type='hidden' value='$recordid' />";
    echo "<input name='tagtype' type='hidden' value='$tagtype' />";
    echo "</form>";
    include('htmlfooter.inc.php');
}
else
{
    $success = add_tag($recordid, $tagtype, $tagname);
    if($success)
    {
        include('htmlheader.inc.php');
        confirmation_page("2", "main.php", "<h2>Tag added Successfully</h2><p align='center'>Please wait while you are redirected...</p>");
        include('htmlfooter.inc.php');
    }
    else
    {
        include('htmlheader.inc.php');
        echo "<h2>Error occured adding tag</h2>";
        include('htmlfooter.inc.php');
    }
}

?>