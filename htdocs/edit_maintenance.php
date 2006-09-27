<?php
// edit_maintenance.php - Form for editing maintenance contracts
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//


$permission=21; // Edit Contracts

require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

// External variables
$action = $_REQUEST['action'];
$maintid = cleanvar($_REQUEST['maintid']);


if (empty($action) OR $action == "showform")
{
    include('htmlheader.inc.php');
    ?>
    <h2>Select Contract to Edit</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>?action=edit" method="post">
    <table align='center' class='vertical'>
    <tr><th>Contract:</th><td><?php echo maintenance_drop_down("maintid", 0); ?></td></tr>
    </table>
    <p align='center'><input name="submit" type="submit" value="Continue" /></p>
    </form>
    <?php
    include('htmlfooter.inc.php');
}


if ($action == "edit")
{
    // Show edit maintenance form
    include('htmlheader.inc.php');
    if ($maintid == 0) echo "<p class='error'>You must select a contract</p>\n";
    else
    {
        $sql = "SELECT * FROM maintenance WHERE id='$maintid'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Error", E_USER_ERROR);
        $maint = mysql_fetch_array($result);
        ?>
        <script type='text/javascript'>
        <!--
        function confirm_submit()
        {
            return window.confirm('Are you sure you want to make these changes?');
        }

        function set_terminated()
        {
            if (document.maintform.productonly.checked==true)
            {
                document.maintform.terminated.disabled=true;
                document.maintform.terminated.checked=true;
            }
            else
            {
                document.maintform.terminated.disabled=false;
                document.maintform.terminated.checked=false;
            }
        }
        //-->
        </script>
        <?php
        echo "<h2>Edit Contract {$maintid}</h2>";
        ?>
        <p align='center'>Mandatory fields are marked <sup class='red'>*</sup></p>
        <form id='maintform' name='maintform' action="<?php echo $_SERVER['PHP_SELF']; ?>?action=update" method="post" onsubmit="return confirm_submit()">
        <table align='center' class='vertical'>
        <tr><th>Site: <sup class='red'>*</sup></th><td><?php echo site_name($maint["site"]) ?></td></tr>
        <tr><th>Product: <sup class='red'>*</sup></th><td><?php echo product_name($maint["product"]) ?></td></tr>
        <tr><th>Reseller: <sup class='red'>*</sup></th><td><?php echo reseller_drop_down("reseller", $maint["reseller"]) ?></td></tr>
        <tr><th>Licence Quantity: <sup class='red'>*</sup></th><td><input maxlength="7" name="licence_quantity" size="5" value="<?php echo $maint["licence_quantity"] ?>" /></td></tr>
        <tr><th>Licence Type: <sup class='red'>*</sup></th><td><?php echo licence_type_drop_down("licence_type", $maint["licence_type"]) ?></td></tr>
        <tr><th>Expiry Date: <sup class='red'>*</sup></th>
        <?php
        echo "<td><input name='expirydate' size='10' value='";
        if ($maint['expirydate'] > 0) echo date('Y-m-d',$maint['expirydate']);
        echo "' /> ".date_picker('maintform.expirydate')."</td></tr>\n";
        ?>
        <?php
        //day_drop_down("expiry_day", maintenance_expiry_day($maintid));
        //month_drop_down("expiry_month", maintenance_expiry_month($maintid));
        //year_drop_down("expiry_year", maintenance_expiry_year($maintid))
        // </td></tr>
        ?>
        <tr><th>Service Level:</th><td><?php echo servicelevel_drop_down('servicelevelid',$maint['servicelevelid'], TRUE); ?></td></tr>
        <?php
        echo "<tr><th>Incident Pool:</th>";
        $incident_pools = explode(',', "Unlimited,{$CONFIG['incident_pools']}");
        echo "<td>".array_drop_down($incident_pools,'incident_poolid',$maint['incident_quantity'])."</td></tr>";
        ?>
        <tr><th>Admin Contact: <sup class='red'>*</sup></th><td><?php echo contact_drop_down("admincontact", $maint["admincontact"], true) ?></td></tr>
        <tr><th>Notes:</th><td><textarea cols="40" name="notes" rows="5"><?php echo $maint["notes"] ?></textarea></td></tr>
        <tr><th>Terminated:</th><td><input name="terminated" id="terminated" type="checkbox" value="yes"<?php if ($maint["term"] == "yes") echo " checked" ?> /></td></tr>
        <tr><th>Product Only:</th><td><input name="productonly" type="checkbox" value="yes" onclick="set_terminated();" <?php if ($maint["productonly"] == "yes") echo " checked" ?> /></td></tr>
        </table>
        <input name="maintid" type="hidden" value="<?php echo $maintid ?>" />
        <p align='center'><input name="submit" type="submit" value="Save" /></p>
        </form>
        <?php
        echo "<p align='center'><a href='maintenance_details.php?id={$maintid}'>View contract</a></p>";
        mysql_free_result($result);
    }
    include('htmlfooter.inc.php');
}
else if ($action == "update")
{
    // External variables
    $incident_pools = explode(',', "0,{$CONFIG['incident_pools']}");
    $incident_quantity = $incident_pools[$_POST['incident_poolid']];
    $reseller = cleanvar($_POST['reseller']);
    $licence_quantity = cleanvar($_POST['licence_quantity']);
    $licence_type = cleanvar($_POST['licence_type']);
    $notes = cleanvar($_POST['notes']);
    $admincontact = cleanvar($_POST['admincontact']);
    $terminated = cleanvar($_POST['terminated']);
    $servicelevelid = cleanvar($_POST['servicelevelid']);
    $incidentpoolid = cleanvar($_POST['incidentpoolid']);
    $expirydate = strtotime($_REQUEST['expirydate']);

    // Update maintenance
    $errors = 0;

    // check for blank reseller
    if ($reseller == 0)
    {
        $errors = 1;
        $errors_string .= "<p class='error'>You must select a reseller</p>\n";
    }
    // check for blank licence quantity
    if ($licence_quantity == "")
    {
        $errors = 1;
        $errors_string .= "<p class='error'>You must enter a licence quantity</p>\n";
    }
    // check for blank licence type
    if ($licence_type == 0)
    {
        $errors = 1;
        $errors_string .= "<p class='error'>You must select a licence type</p>\n";
    }
    // check for blank admin contact
    if ($admincontact == 0)
    {
        $errors = 1;
        $errors_string .= "<p class='error'>You must select an admin contact</p>\n";
    }
    // check for blank expiry day
    if ($expirydate == 0)
    {
        $errors = 1;
        $errors_string .= "<p class='error'>You must enter an expiry date</p>\n";
    }

    // update maintenance if no errors
    if ($errors == 0)
    {
        if (empty($productonly)) $productonly='no';
        if ($productonly=='yes') $terminated='yes';

        $sql  = "UPDATE maintenance SET reseller='$reseller', expirydate='$expirydate', licence_quantity='$licence_quantity', ";
        $sql .= "licence_type='$licence_type', notes='$notes', admincontact=$admincontact, term='$terminated', servicelevelid='$servicelevelid', ";
        $sql .= "incident_quantity='$incident_quantity', ";
        $sql .= "incidentpoolid='$incidentpoolid', productonly='$productonly' WHERE id='$maintid'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        // show error message if addition failed
        if (!$result)
        {
            include('htmlheader.inc.php');
            echo "<p class='error'>Maintenance update failed)\n";
            include('htmlfooter.inc.php');
        }
        // show success message
        else
        {
            journal(CFG_LOGGING_NORMAL, 'Contract Edited', "contract $maintid modified", CFG_JOURNAL_MAINTENANCE, $maintid);
            confirmation_page("2", "maintenance_details.php?id=$maintid", "<h2>Contract Update Successful</h2><p align='center'>Please wait while you are redirected...</p>");
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