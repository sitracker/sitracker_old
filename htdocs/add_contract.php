<?php
// add_contract.php - Add a new maintenance contract
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// This Page Is Valid XHTML 1.0 Transitional!  1Nov05

$permission=39; // Add Maintenance Contract

require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

$title = $strAddContract;

// External variables
$action = $_REQUEST['action'];
$siteid = mysql_escape_string($_REQUEST['siteid']);

// Show add maintenance form
if ($action == "showform" OR $action=='')
{
    include('htmlheader.inc.php');

    ?>
    <script type="text/javascript">
    function confirm_submit()
    {
        return window.confirm('Are you sure you want to add this contract?');
    }
    </script>
    <?php
    echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/contract.png' width='32' height='32' alt='' /> ";
    echo "{$strAddContract}</h2>";
    echo "<h5>".sprintf($strMandatoryMarked,"<sup class='red'>*</sup>")."</h5>";
    echo "<form name='addcontract' action='{$_SERVER['PHP_SELF']}?action=add' method='post' onsubmit='return confirm_submit();'>";
    echo "<table align='center' class='vertical'>";
    echo "<tr><th>{$strSite}: <sup class='red'>*</sup></th><td>".site_drop_down("site", $siteid)." </td></tr>\n";
    echo "<tr><th>{$strContacts}:</th><td><input type='radio'>Limit to <input size='2' value='0'> supported contacts (0 means unlimited)<br />";
    echo "<input type='radio'>All site contacts are to be supported</td></tr>";
    echo "<tr><th>{$strProduct}: <sup class='red'>*</sup></th><td>".product_drop_down("product", 0)."</td></tr>\n";
    echo "<tr><th>{$strReseller}: <sup class='red'>*</sup></th><td>";
    reseller_drop_down("reseller", 0);
    echo "</td></tr>\n";
    echo "<tr><th>{$strLicenseQuantity}: <sup class='red'>*</sup></th><td><input maxlength='7' name='licence_quantity' size='5' /></td></tr>\n";
    echo "<tr><th>{$strLicenseType}: <sup class='red'>*</sup></th><td>";
    licence_type_drop_down("licence_type", 0);
    echo "</td></tr>\n";
    echo "<tr><th>{$strExpiryDate}: <sup class='red'>*</sup></th>";
    echo "<td><input name='expiry' size='10' /> ".date_picker('addcontract.expiry')."</td></tr>\n";
    echo "<tr><th>{$strServiceLevel}:</th><td>".servicelevel_drop_down('servicelevelid', 1, TRUE)."</td></tr>\n";
    echo "<tr><th>{$strIncidentPool}:</th>";
    $incident_pools = explode(',', "Unlimited,{$CONFIG['incident_pools']}");
    echo "<td>".array_drop_down($incident_pools,'incident_poolid',$maint['incident_quantity'])."</td></tr>";
    echo "<tr><th>{$strAdminContact}: <sup class='red'>*</sup></th><td>".contact_drop_down("admincontact", 0, true)."</td></tr>\n";
    echo "<tr><th>{$strNotes}:</th><td><textarea cols='40' name='notes' rows='5'></textarea></td></tr>\n";
    echo "<tr><th>Product Only:</th><td><input name='productonly' type='checkbox' value='yes' /></td></tr>\n"; // FIXME i18n
    echo "</table>\n";
    echo "<p align='center'><input name='submit' type='submit' value=\"{$strAddContract}\" /></p>";
    echo "</form>";
    include('htmlfooter.inc.php');
}
elseif ($action == "add")
{
    // External Variables
    $site = mysql_escape_string($_REQUEST['site']);
    $product = mysql_escape_string($_REQUEST['product']);
    $reseller = mysql_escape_string($_REQUEST['reseller']);
    $licence_quantity = mysql_escape_string($_REQUEST['licence_quantity']);
    $licence_type = mysql_escape_string($_REQUEST['licence_type']);
    $admincontact = mysql_escape_string($_REQUEST['admincontact']);
    $expirydate = strtotime($_REQUEST['expiry']);
    $notes = mysql_escape_string($_REQUEST['notes']);
    $servicelevelid = mysql_escape_string($_REQUEST['servicelevelid']);
    $incidentpoolid = mysql_escape_string($_REQUEST['incidentpoolid']);
    $productonly = mysql_escape_string($_REQUEST['productonly']);
    $term = mysql_escape_string($_REQUEST['term']);

    $incident_pools = explode(',', "0,{$CONFIG['incident_pools']}");
    $incident_quantity = $incident_pools[$_POST['incident_poolid']];

    // Add maintenance to database
    $errors = 0;
    // check for blank site
    if ($site == 0)
    {
        $errors = 1;
        $errors_string .= "<p class='error'>You must select a site</p>\n";
    }
    // check for blank product
    if ($product == 0)
    {
        $errors = 1;
        $errors_string .= "<p class='error'>You must select a product</p>\n";
    }
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
    // check for blank service level
    if ($admincontact == 0)
    {
        $errors = 1;
        $errors_string .= "<p class='error'>You must select a service level</p>\n";
    }
    // check for blank expiry day
    if ($expirydate == 0)
    {
        $errors = 1;
        $errors_string .= "<p class='error'>You must enter an expiry date</p>\n";
    }
    if ($expirydate < $now)
    {
        $errors = 1;
        $errors_string .= "<p class='error'>Expiry date cannot be in the past</p>\n";
    }
    // add maintenance if no errors
    if ($errors == 0)
    {
        $addition_errors = 0;

        if (empty($productonly)) $productonly='no';
        if ($productonly=='yes') $term='yes';
        else $term='no';
        $sql  = "INSERT INTO maintenance (site, product, reseller, expirydate, licence_quantity, licence_type, notes, ";
        $sql .= "admincontact, servicelevelid, incidentpoolid, incident_quantity, productonly, term) ";
        $sql .= "VALUES ('$site', '$product', '$reseller', '$expirydate', '$licence_quantity', '$licence_type', '$notes', ";
        $sql .= "'$admincontact', '$servicelevelid', '$incidentpoolid', '$incident_quantity', '$productonly', '$term')";

        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        $maintid=mysql_insert_id();

        if (!$result)
        {
            $addition_errors = 1;
            $addition_errors_string .= "<p class='error'>Addition of contract failed</p>\n";
        }


        if ($addition_errors == 1)
        {
            // show addition error message
            include('htmlheader.inc.php');
            echo $addition_errors_string;
            include('htmlfooter.inc.php');
        }
        else
        {
            // show success message
            $id=mysql_insert_id();
            journal(CFG_LOGGING_NORMAL, 'Contract Added', "Contract $id Added", CFG_JOURNAL_MAINTENANCE, $id);

            confirmation_page("2", "maintenance_details.php?id=$maintid", "<h2>Contract Added Successfully</h2><h5>{$strPleaseWaitRedirect}...</h5>");
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