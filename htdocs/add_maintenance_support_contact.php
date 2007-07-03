<?php
// add_maintenance_support_contract.php - Associates a contact with a contract
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

$permission=32;  // Edit Supported Products
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

// External Variables
$maintid = cleanvar($_REQUEST['maintid']);
$contactid = cleanvar($_REQUEST['contactid']);
$context = cleanvar($_REQUEST['context']);
$action = $_REQUEST['action'];

// Valid user, check permissions
if (empty($action) || $action == "showform")
{
    $title="Associate person with Contract";
    include('htmlheader.inc.php');
    ?>
    <h2>Link a contract with a support contact</h2>
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>?action=add" method="post">
    <input type="hidden" name="context" value="<?php echo $context ?>" />
    <table>
    <?php
    if (empty($maintid))
    {
        ?>
        <tr><th>Contract:</th><td width=400><?php echo maintenance_drop_down("maintid", 0) ?></td></tr>
        <?php
    }
    else
    {
        $sql = "SELECT sites.name, products.name FROM maintenance, sites, products WHERE maintenance.site=sites.id ";
        $sql .= "AND maintenance.product=products.id AND maintenance.id='$maintid'";
        $result=mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        list($sitename, $product)=mysql_fetch_row($result);

        echo "<tr><th>Contract:</th><td>$maintid - $sitename, $product</td></tr>";
        echo "<input name=\"maintid\" type=\"hidden\" value=\"$maintid\" />";
    }

    if (empty($contactid))
    {
        ?>
        <tr><th>Support Contact:</th><td><?php echo contact_drop_down("contactid", 0, TRUE); ?></td></tr>
        <?php
    }
    else
    {
        echo "<tr><th>Contact:</th><td>$contactid - ".contact_realname($contactid).", ".site_name(contact_site($contactid))."</td></tr>";
        echo "<input name=\"contactid\" type=\"hidden\" value=\"$contactid\" />";
    }
    ?>
    </table>
    <p align='center'><input name="submit" type="submit" value="Continue" /></p>
    </form>
    <?php
    include('htmlfooter.inc.php');
}
else if ($action == "add")
{
    // Add support contact
    $errors = 0;
    // check for blank contact
    if ($contactid == 0)
    {
        $errors = 1;
        $errors_string .= "<p class='error'>You must select a contact</p>\n";
    }
    // check for blank maintenance id
    if ($maintid == 0)
    {
        $errors = 1;
        $errors_string .= "<p class='error'>Something weird has happened, better call technical support</p>\n";
    }

    // add maintenance support contact if no errors
    if ($errors == 0)
    {
        $sql  = "INSERT INTO supportcontacts (maintenanceid, contactid) VALUES ($maintid, $contactid)";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        // show error message if addition failed
        if (!$result)
        {
            include('htmlheader.inc.php');
            echo "<p class='error'>Addition of support contact failed\n";
            include('htmlfooter.inc.php');
        }
        // update database and show success message
        else
        {
            if ($context=='contact')
            {
                confirmation_page("3", "contact_details.php?id=$contactid", "<h2>Contact Successfully linked to contract</h2><p align='center'>Please wait while you are redirected...</p>");
            }
            else
            {
                confirmation_page("3", "maintenance_details.php?id=$maintid", "<h2>Contact Successfully linked to contract</h2><p align='center'>Please wait while you are redirected...</p>");
            }
        }
    }

    // show error message if errors
    else
    {
        include('htmlheader.inc.php');
        echo $errors_string;
        include('htmlfooter.inc.php');
    }
}
?>
