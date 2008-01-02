<?php
// search_expired.php - Search expired contracts
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
// FIXME i18n whole page

@include('set_include_path.inc.php');
$permission=19; // View Contracts

require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

// External variables
$expired = cleanvar($_REQUEST['expired']);
$output = cleanvar($_REQUEST['output']);

// show search expired maintenance form
if (empty($expired))
{
    include('htmlheader.inc.php');
    ?>
    <h2>Search Expired Contracts</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
    <p>Show Contracts Expired Within <input maxlength='4' name="expired" size='3' type="text" /> Days</p>
    <p><input checked='checked' name="show" type='radio' value="terminated"> Terminated <input name="show" type='radio' value="nonterminated" /> Non-terminated</p>
    <?php
    echo "<p align='center'>{$strOutput}: ";
    echo "<select name='output'>";
    echo "<option value='screen'>{$strScreen}</option>";
    // echo "<option value='printer'>Printer</option>";
    echo "<option value='csv'>{$strCSVfile}</option>";
    echo "</select>";
    echo "</p>";
    echo "<p><input name='submit' type='submit' value=\"{$strSearch}\" /></p>\n";
    echo "</form>\n";
    include('htmlfooter.inc.php');
}
else
{
    // perform search
    // check input
    if ($expired == "")
    {
        $errors = 1;
        echo "<p class='error'>You must enter a number of days</p>\n";
    }
    elseif (!is_numeric($expired))
    {
        $errors = 1;
        echo "<p class='error'>You must enter a numeric value</p>\n";
    }
    if ($errors == 0)
    {
        // convert number of days into a timestamp
        $now = time();
        $min_expiry = $now - ($expired * 86400);

        // build SQL
        $sql  = "SELECT maintenance.id AS maintid, sites.name AS site, products.name AS product, resellers.name AS reseller, licence_quantity, licencetypes.name AS licence_type, expirydate, admincontact, contacts.forenames AS admincontactforenames, contacts.surname AS admincontactsurname, contacts.email AS admincontactemail, contacts.phone AS admincontactphone, maintenance.notes FROM maintenance, sites, contacts, products, licencetypes, resellers WHERE ";
        $sql .= "(siteid=sites.id AND product=products.id AND reseller=resellers.id AND licence_type=licencetypes.id AND admincontact=contacts.id) AND ";
        $sql .= "expirydate >= $min_expiry AND expirydate <= $now ";
        if ($show == "terminated") $sql .= "AND term='yes'";
        else if ($show == "nonterminated") $sql .= "AND term='no'";
        $sql .= "ORDER BY expirydate ASC";

        // connect to database
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        if ($show == "nonterminated") $pagetitle = "<h2>Non-Terminated Contracts Expired Within The Last $expired Days</h2>\n";
        else if ($show == "terminated") $pagetitle = "<h2>Terminated Contracts Expired Within The Last $expired Days</h2>\n";

        if (mysql_num_rows($result) == 0)
        {
            include('htmlheader.inc.php');
            echo $pagetitle;
            // FIXME i18n
            echo "<p class='error'>Sorry, your search yielded no results</p>\n";
            include('htmlfooter.inc.php');
        }
        else
        {
            if ($_REQUEST['output']=='screen')
            {
                include('htmlheader.inc.php');
                ?>
                <script type="text/javascript">
                function support_contacts_window(maintenanceid)
                {
                    URL = "support_contacts.php?maintid=" + maintenanceid;
                    window.open(URL, "support_contacts_window", "toolbar=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=450,height=240");
                }
                function contact_details_window(contactid)
                {
                    URL = "contact_details.php?contactid=" + contactid;
                    window.open(URL, "contact_details_window", "toolbar=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=450,height=240");
                }
                </script>
                <?php
                echo "<h2>$pagetitle</h2>";
                ?>
                <h3>Search yielded <?php echo mysql_num_rows($result) ?> result(s)</h3>
                <?php
                echo "<table align='center'>
                <tr>
                <th>{$strContract}</th>
                <th>{$strSite}</th>
                <th>{$strProduct}</th>
                <th>{$strReseller}</th>
                <th>{$strLicense}</th>
                <th>{$strExpiryDate}</th>
                <th>{$strAdminContact}</th>
                <th>{$strTelephone}</th>
                <th>{$strEmail}</th>
                <th>{$strNotes}</th>
                </tr>\n";
                // FIXME check data protection fields for email and telephone
                $shade = 0;
                while ($results = mysql_fetch_array($result))
                {
                    // define class for table row shading
                    if ($shade) $class = "shade1";
                    else $class = "shade2";
                    ?>
                    <tr>
                    <td align='center' class='<?php echo $class ?>' width='50'><a href="contract_details.php?id=<?php echo $results["maintid"] ?>"><?php echo $results["maintid"] ?></a></td>
                    <td align='center' class='<?php echo $class ?>' width='100'><?php echo $results["site"] ?></td>
                    <td align='center' class='<?php echo $class ?>' width='100'><?php echo $results["product"] ?></td>
                    <td align='center' class='<?php echo $class ?>' width='100'><?php echo $results["reseller"] ?></td>
                    <td align='center' class='<?php echo $class ?>' width='75'><?php echo $results["licence_quantity"] ?> <?php echo $results["licence_type"] ?></td>
                    <td align='center' class='<?php echo $class ?>' width='100'><?php echo date($CONFIG['dateformat_date'], $results["expirydate"]); ?></td>
                    <td align='center' class='<?php echo $class ?>' width='100'><a href="javascript: contact_details_window(<?php echo $results['admincontact']?>)"><?php echo $results['admincontactforenames'].' '.$results['admincontactsurname'] ?></a></td>
                    <?php
                    echo "<td class='{$class}'>{$results['admincontactphone']}</td>";
                    echo "<td class='{$class}'>{$results['admincontactemail']}</td>";
                    ?>
                    <td align='center' class='<?php echo $class; ?>' width='150'><?php if ($results["notes"] == "") echo "&nbsp;"; else echo nl2br($results["notes"]); ?></td>
                    </tr>
                    <?php
                    // invert shade
                    if ($shade == 1) $shade = 0;
                    else $shade = 1;
                }
                echo "</table>\n";
                echo "<p align='center'><a href='search.php?query={$search_string}&amp;context=maintenance'>{$strSearchAgain}</a></p>\n";
                include('htmlfooter.inc.php');
            }
            else
            {
                $csvfieldheaders="{$strContract},{$strSite},{$strProduct},{$strReseller},{$strLicense},{$strExpiryDate},{$strAdminContact},{$strTelephone},{$strEmail},{$strNotes}\n";
                while ($row = mysql_fetch_object($result))
                {
                    $csv.= "{$row->maintid},{$row->site},{$row->product},{$row->reseller},{$row->license_quantity} {$row->licence_type},";
                    $csv.= date($CONFIG['dateformat_date'], $row->expirydate);
                    $csv.= ",{$row->admincontactforenames} {$row->admincontactsurname},{$row->admincontactphone},{$row->admincontactemail},";
                    $notes=nl2br($row->notes);
                    $notes=str_replace(","," ",$notes);
                    $notes=str_replace("\n"," ",$notes);
                    $notes=str_replace("\r"," ",$notes);
                    $notes=str_replace("<br />"," ",$notes);
                    $csv.= "{$notes}\n";
                }
                // --- CSV File HTTP Header
                header("Content-type: text/csv\r\n");
                header("Content-disposition-type: attachment\r\n");
                header("Content-disposition: filename=expired_report.csv");
                echo $csvfieldheaders;
                echo $csv;
            }
        }
    }
}
?>
