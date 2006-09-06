<?php
// add_software.php - Form for adding software
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

if (empty($submit))
{
    // Show add product form
    include('htmlheader.inc.php');
    ?>
    <script type="text/javascript">
    function confirm_submit()
    {
        return window.confirm('Are you sure you want to add this software?');
    }
    </script>

    <h2>Add New Software</h2>
    <p align='center'>Mandatory fields are marked <sup class='red'>*</sup></p>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return confirm_submit()">
    <table class='vertical'>
    <tr><th>Software Name: <sup class='red'>*</sup></th><td><input maxlength="50" name="name" size="30" /></td></tr>
    <?php
        echo "<tr><th>Lifetime:</th><td>";
    echo "<input type='text' name='lifetime_start' id='lifetime_start' size='10' value='".date('Y-m-d')."' />";
    echo " To: ";
    echo "<input type='text' name='lifetime_end' id='lifetime_end' size='10' value='".date('Y-m-d',strtotime('Next Year'))."' />";
    ?>
    </td></tr>
    </table>
    <p align='center'><input name="submit" type="submit" value="Add Software" /></p>
    <p class='warning'>Please check that the software does not already exist <em>before</em> adding it</p>
    </form>
    <?php
    include('htmlfooter.inc.php');
}
else
{
    // External variables
    $name = cleanvar($_REQUEST['name']);
    if (!empty($_REQUEST['lifetime_start'])) $lifetime_start = strtotime($_REQUEST['lifetime_start']);
    else $lifetime_start = '';
    if (!empty($_REQUEST['lifetime_end'])) $lifetime_end = strtotime($_REQUEST['lifetime_end']);
    else $lifetime_end = '';

    // Add new
    $errors = 0;

    // check for blank name
    if ($name == "")
    {
        $errors = 1;
        $errors_string .= "<p class='error'>You must enter a name</p>\n";
    }
    // Check this is not a duplicate
    $sql = "SELECT id FROM software WHERE LCASE(name)=LCASE('$name') LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_num_rows($result) >= 1)
    {
        $errors++;
        $errors_string .= "<p class='error'>A record already exists with that name</p>";
    }

    // add product if no errors
    if ($errors == 0)
    {
        $sql = "INSERT INTO software (name, lifetime_start, lifetime_end) VALUES ('$name','$lifetime_start','$lifetime_end')";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        if (!$result) echo "<p class='error'>Addition of Software Failed\n";
        else
        {
            $id=mysql_insert_id();
            journal(CFG_LOGGING_DEBUG, 'Software Added', "Software $id was added", CFG_JOURNAL_DEBUG, $id);
            confirmation_page("2", "products.php", "<h2>Software Addition Successful</h2><p align='center'>Please wait while you are redirected...</p>");
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