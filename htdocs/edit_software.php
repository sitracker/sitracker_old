<?php
// edit_software.php - Form for editing software
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

$permission=56; // Add Software

require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

// External variables
$submit = $_REQUEST['submit'];
$id = cleanvar($_REQUEST['id']);

if (empty($submit))
{
    $title='Edit Software';
    // Show add product form
    include('htmlheader.inc.php');
    ?>
    <script type="text/javascript">
    function confirm_submit()
    {
        return window.confirm('Are you sure you want to edit this software?');
    }
    </script>
    <?php
    echo "<h2>$title</h2>";
    $sql = "SELECT * FROM software WHERE id='$id' LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    while ($software = mysql_fetch_object($result))
    {
        echo "<p align='center'>Mandatory fields are marked <sup class='red'>*</sup></p>";
        echo "<form action='{$_SERVER['PHP_SELF']}' method='post' onsubmit='return confirm_submit()'>";
        echo "<table class='vertical'>";
        echo "<tr><th>Software Name: <sup class='red'>*</sup></th><td><input maxlength='50' name='name' size='30' value='".stripslashes($software->name)."' /></td></tr>";
        echo "<tr><th>Lifetime:</th><td>";
        echo "<input type='text' name='lifetime_start' id='lifetime_start' size='10' value='";
        if ($software->lifetime_start > 1) echo date('Y-m-d',mysql2date($software->lifetime_start));
        echo "' />";
        echo " To: ";
        echo "<input type='text' name='lifetime_end' id='lifetime_end' size='10' value='";
        if ($software->lifetime_end > 1) echo date('Y-m-d',mysql2date($software->lifetime_end));
        echo "' />";
        echo "</td></tr>";
        echo "</table>";
    }
    echo "<input type='hidden' name='id' value='$id' />";
    echo "<p align='center'><input name='submit' type='submit' value='Save' /></p>";
    echo "</form>\n";
    include('htmlfooter.inc.php');
}
else
{
    // External variables
    $name = cleanvar($_REQUEST['name']);
    if (!empty($_REQUEST['lifetime_start'])) $lifetime_start = date('Y-m-d',strtotime($_REQUEST['lifetime_start']));
    else $lifetime_start = '';
    if (!empty($_REQUEST['lifetime_end'])) $lifetime_end = date('Y-m-d',strtotime($_REQUEST['lifetime_end']));
    else $lifetime_end = '';

    // Add new
    $errors = 0;

    // check for blank name
    if ($name == "")
    {
        $errors = 1;
        $errors_string .= "<p class='error'>You must enter a name</p>\n";
    }
    // add product if no errors
    if ($errors == 0)
    {
        $sql = "UPDATE software SET ";
        $sql .= "name='$name', lifetime_start='$lifetime_start', lifetime_end='$lifetime_end' ";
        $sql .= "WHERE id = '$id'";
        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        else
        {
            $id=mysql_insert_id();
            journal(CFG_LOGGING_DEBUG, 'Software Edited', "Software $id was edited", CFG_JOURNAL_DEBUG, $id);
            confirmation_page("2", "products.php", "<h2>Software Edit Successful</h2><p align='center'>Please wait while you are redirected...</p>");
        }
    }
    else
    {
        include('htmlheader.inc.php');
        echo $errors_string;
        include('htmlfooter.inc.php');
    }
}
?>