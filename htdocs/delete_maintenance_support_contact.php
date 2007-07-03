<?php
// delete_maintenance_support_contact.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//


// Removes an Association between a contact and a maintenance contract

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

// This Page Is Valid XHTML 1.0 Transitional!   31Oct05

$permission=32;  // Edit Supported Products
require('db_connect.inc.php');
require('functions.inc.php');
$title="Remove a Supported Contact";

// This page requires authentication
require('auth.inc.php');

// External variables
$action = $_REQUEST['action'];
$context = cleanvar($_REQUEST['context']);
$maintid =cleanvar($_REQUEST['maintid']);
$contactid = cleanvar($_REQUEST['contactid']);


if (empty($action) OR $action == "showform")
{
    include('htmlheader.inc.php');
    ?>
    <script type='text/javascript'>
    function confirm_submit()
    {
        return window.confirm('This will remove the ability to log incidents for this contact regarding the product which this contract is for. Are you sure you want to delete this maintenance support contact?');
    }
    </script>
    <h2>Remove the link between a maintenance contract and a support contact</h2>
    <p align='center'>This will mean that the contact will not be able to log any further support incidents for the related product</p>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>?action=delete" method="post" onsubmit="return confirm_submit()">
    <input type="hidden" name="context" value="<?php echo $context ?>" />
    <table>
    <?php
    if (empty($maintid))
    {
        ?>
        <tr><th>Maintenance Contract:</th><td><?php echo maintenance_drop_down("maintid", 0); ?></td></tr>
        <?php
    }
    else
    {
        echo "<tr><th>Maintenance Contract:</th><td>$maintid";
        echo "<input name=\"maintid\" type=\"hidden\" value=\"$maintid\" /></td></tr>";
    }

    if (empty($contactid))
    {
        ?>
        <tr><th>Support Contact:</th><td width='400'><?php echo contact_drop_down("contactid", 0); ?></td></tr>
        <?php
    }
    else
    {
        echo "<tr><th>Contact:</th><td>$contactid";
        echo "<input name=\"contactid\" type=\"hidden\" value=\"$contactid\" /></td></tr>";
    }
    ?>
    </table>
    <p align='center'><input name="submit" type="submit" value="Continue" /></p>
    </form>
    <?php
    include('htmlfooter.inc.php');
}
elseif ($action == "delete")
{
    // Delete the chosen support contact
    $errors = 0;
    // check for blank contact
    if ($contactid == 0)
    {
        $errors = 1;
        $errors_string .= "<p class='error'>You must select a support contact</p>\n";
    }
    // check for blank maintenance id
    if ($maintid == 0)
    {
        $errors = 1;
        $errors_string .= "<p class='error'>You must select a maintenance contract</p>\n";
    }
    // delete maintenance support contact if no errors
    if ($errors == 0)
    {
        $sql  = "DELETE FROM supportcontacts WHERE maintenanceid='$maintid' AND contactid='$contactid'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        // show error message if deletion failed
        if (!$result)
        {
            include('htmlheader.inc.php');
            throw_error('Deletion of maintenance support conact failed:','$sql');
            include('htmlfooter.inc.php');
        }
        // update db and show success message
        else
        {
            journal(CFG_LOGGING_NORMAL, 'Supported Contact Removed', "Contact $contactid removed from maintenance contract $maintid", CFG_JOURNAL_MAINTENANCED, $maintid);

            if ($context=='maintenance')
            {
                confirmation_page("3", "maintenance_details.php?id=$maintid", "<h2>Maintenance Support Contact Deletion Successful</h2><p align='center'>Please wait while you are redirected...</p>");
            }
            else
            {
                confirmation_page("3", "contact_details.php?id=$contactid", "<h2>Maintenance Support Contact Deletion Successful</h2><p align='center'>Please wait while you are redirected...</p>");
            }
        }
    }
    else
    {
        // show error message if errors
        include('htmlheader.inc.php');
        echo $errors_string;
        include('htmlfooter.inc.php');
    }
}
?>
