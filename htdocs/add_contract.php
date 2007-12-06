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
$siteid = cleanvar($_REQUEST['siteid']);

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
    echo "<tr><th>{$strContacts}:<sup class='red'>*</sup></th><td>";
    // TODO all supportedcontacts disabled for 3.31 release
    // echo "<input value='amount' type='radio' name='contacts' checked='checked' />";
    echo "<input type='hidden' name ='contacts' value='amount' />";
    echo "{$strLimitTo} <input size='2' value='0' name='amount' /> {$strSupportedContacts} ({$str0MeansUnlimited})<br />";
    // echo "<input type='radio' value='all' name='contacts' />";
    // echo "{$strAllSiteContactsSupported}";
    echo "</td></tr>";
    echo "<tr><th>{$strProduct}: <sup class='red'>*</sup></th><td>".product_drop_down("product", 0)."</td></tr>\n";
    echo "<tr><th>{$strExpiryDate}: <sup class='red'>*</sup></th>";
    echo "<td><input name='expiry' size='10' /> ".date_picker('addcontract.expiry')." <input type='checkbox' name='noexpiry' /> {$strUnlimited}</td></tr>\n";
    echo "<tr><th>{$strServiceLevel}:</th><td>".servicelevel_drop_down('servicelevelid', 1, TRUE)."</td></tr>\n";

    echo "<tr><th>{$strAdminContact}: <sup class='red'>*</sup></th><td>".contact_drop_down("admincontact", 0, true)."</td></tr>\n";
    echo "<tr><th>{$strNotes}:</th><td><textarea cols='40' name='notes' rows='5'></textarea></td></tr>\n";

    echo "<tr><th></th><td><a href=\"javascript:toggleDiv('hidden');\">{$strAdvanced}</a></td></tr>\n";

    echo "<tbody id='hidden' style='display:none'>";

    echo "<tr><th>{$strReseller}:</th><td>";
    reseller_drop_down("reseller", 0);
    echo "</td></tr>\n";

    echo "<tr><th>{$strLicenseQuantity}:</th><td><input value='0' maxlength='7' name='licence_quantity' size='5' />";
    echo " ({$str0MeansUnlimited})</td></tr>\n";

    echo "<tr><th>{$strLicenseType}:</th><td>";
    licence_type_drop_down("licence_type", 0);
    echo "</td></tr>\n";

    echo "<tr><th>{$strIncidentPool}:</th>";
    $incident_pools = explode(',', "Unlimited,{$CONFIG['incident_pools']}");
    echo "<td>".array_drop_down($incident_pools,'incident_poolid',$maint['incident_quantity'])."</td></tr>";

    echo "<tr><th>{$strProductOnly}:</th><td><input name='productonly' type='checkbox' value='yes' /></td></tr></tbody>\n";

    echo "</table>\n";
    echo "<p align='center'><input name='submit' type='submit' value=\"{$strAddContract}\" /></p>";
    echo "</form>";
    include('htmlfooter.inc.php');
}
elseif ($action == "add")
{
    // External Variables
    $site = cleanvar($_REQUEST['site']);
    $product = cleanvar($_REQUEST['product']);
    $reseller = cleanvar($_REQUEST['reseller']);
    $licence_quantity = cleanvar($_REQUEST['licence_quantity']);
    $licence_type = cleanvar($_REQUEST['licence_type']);
    $admincontact = cleanvar($_REQUEST['admincontact']);
    $expirydate = strtotime($_REQUEST['expiry']);
    $notes = cleanvar($_REQUEST['notes']);
    $servicelevelid = cleanvar($_REQUEST['servicelevelid']);
    $incidentpoolid = cleanvar($_REQUEST['incidentpoolid']);
    $productonly = cleanvar($_REQUEST['productonly']);
    $term = cleanvar($_REQUEST['term']);
    $contacts = cleanvar($_REQUEST['contacts']);
    if($_REQUEST['noexpiry'] == 'on') $expirydate = '-1';

    $allcontacts = 'No';
    if($contacts == 'amount') $amount = cleanvar($_REQUEST['amount']);
    elseif($contacts == 'all') $allcontacts = 'Yes';

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
    if ($expirydate < $now AND $expirydate != -1)
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
        $sql .= "admincontact, servicelevelid, incidentpoolid, incident_quantity, productonly, term, supportedcontacts, allcontactssupported) ";
        $sql .= "VALUES ('$site', '$product', '$reseller', '$expirydate', '$licence_quantity', '$licence_type', '$notes', ";
        $sql .= "'$admincontact', '$servicelevelid', '$incidentpoolid', '$incident_quantity', '$productonly', '$term', '$amount', '$allcontacts')";

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

            html_redirect("contract_details.php?id=$maintid");
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