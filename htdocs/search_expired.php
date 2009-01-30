<?php
// search_expired.php - Search expired contracts
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
// FIXME i18n whole page

@include ('set_include_path.inc.php');
$permission = 19; // View Contracts

require ($lib_path.'db_connect.inc.php');
require ($lib_path.'functions.inc.php');

// This page requires authentication
require ($lib_path.'auth.inc.php');

// External variables
$expired = cleanvar($_REQUEST['expired']);
$output = cleanvar($_REQUEST['output']);

// show search expired maintenance form
if (empty($expired))
{
    include ('./inc/htmlheader.inc.php');
    
    echo "<h2>Search Expired Contracts</h2>";
    echo "<form action='{$_SERVER['PHP_SELF']}' method='get' >";
    echo "<p>Show Contracts Expired Within <input maxlength='4' name='expired' size='3' type='text'' /> Days</p>";
    echo "<p><input checked='checked' name='show' type='radio' value='terminated> Terminated <input name='show' type='radio' value='nonterminated'' /> Non-terminated</p>";
    
    echo "<p align='center'>{$strOutput}: ";
    echo "<select name='output'>";
    echo "<option value='screen'>{$strScreen}</option>";
    // echo "<option value='printer'>Printer</option>";
    echo "<option value='csv'>{$strCSVfile}</option>";
    echo "</select>";
    echo "</p>";
    echo "<p><input name='submit' type='submit' value=\"{$strSearch}\" /></p>\n";
    echo "</form>\n";
    include ('./inc/htmlfooter.inc.php');
}
else
{
    // perform search
    // check input
    if ($expired == '')
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
        $sql  = "SELECT m.id AS maintid, s.name AS site, p.name AS product, r.name AS reseller, ";
        $sql .= "licence_quantity, l.name AS licence_type, expirydate, admincontact, ";
        $sql .= "c.forenames AS admincontactforenames, c.surname AS admincontactsurname, ";
        $sql .= "c.email AS admincontactemail, c.phone AS admincontactphone, m.notes ";
        $sql .= "FROM `{$dbMaintenance}` AS m, `{$dbSites}` AS s, `{$dbContacts}` AS c, ";
        $sql .= "`{$dbProducts}` AS p, `{$dbLicenceTypes}` AS l, `{$dbResellers}` AS r WHERE ";
        $sql .= "(siteid = s.id AND product = p.id AND reseller = r.id AND licence_type = l.id AND admincontact = c.id) AND ";
        $sql .= "expirydate >= $min_expiry AND expirydate <= $now ";
        if ($show == "terminated") $sql .= "AND term='yes'";
        else if ($show == "nonterminated") $sql .= "AND term='no'";
        $sql .= "ORDER BY expirydate ASC";

        // connect to database
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);

        if ($show == "nonterminated") $pagetitle = "<h2>Non-Terminated Contracts Expired Within The Last $expired Days</h2>\n";
        else if ($show == "terminated") $pagetitle = "<h2>Terminated Contracts Expired Within The Last $expired Days</h2>\n";

        if (mysql_num_rows($result) == 0)
        {
            include ('./inc/htmlheader.inc.php');
            echo $pagetitle;
            // FIXME i18n
            echo "<p class='error'>Sorry, your search yielded no results</p>\n";
            include ('./inc/htmlfooter.inc.php');
        }
        else
        {
            if ($_REQUEST['output'] == 'screen')
            {
                include ('./inc/htmlheader.inc.php');
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

                echo "<h3>Search yielded ".mysql_num_rows($result)." result(s)</h3>";

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
                $shade = 'shade1';
                while ($results = mysql_fetch_object($result))
                {
                    echo "<tr>";
                    echo "<td align='center' class='{$shade}' width='50'><a href='contract_details.php?id={$results->maintid}'>{$results->maintid}</a></td>";
                    echo "<td align='center' class='{$shade}' width='100'>{$results->site}</td>";
                    echo "<td align='center' class='{$shade}' width='100'>{$results->product}</td>";
                    echo "<td align='center' class='{$shade}' width='100'>{$results->reseller}</td>";

                    echo "<td align='center' class='{$shade}' width='75'>{$results->licence_quantity} {$results->licence_type}</td>";
                    echo "<td align='center' class='{$shade}' width='100'>".ldate($CONFIG['dateformat_date'], $results->expirydate)."</td>";
                    echo "<td align='center' class='{$shade}' width='100'><a href=\"javascript: contact_details_window({$results->admincontact})\">{$results->admincontactforenames} {$results->admincontactsurname}</a></td>";

                    echo "<td class='{$shade}'>{$results->admincontactphone}</td>";
                    echo "<td class='{$shade}'>{$results->admincontactemail}</td>";
                    
                    echo "<td align='center' class='{$shade}' width='150'>";
                    if ($results->notes == '')
                    {
                        echo "&nbsp;";
                    }
                    else
                    {
                        echo nl2br($results->notes);
                    }
                    
                    echo "</td></tr>";
                    // invert shade
                    if ($shade == 'shade1;') $shade = 'shade2';
                    else $shade = 'shade1';
                }
                echo "</table>\n";
                echo "<p align='center'><a href='search.php?query={$search_string}&amp;context=maintenance'>{$strSearchAgain}</a></p>\n";
                include ('./inc/htmlfooter.inc.php');
            }
            else
            {
                $csvfieldheaders="{$strContract},{$strSite},{$strProduct},{$strReseller},{$strLicense},{$strExpiryDate},{$strAdminContact},{$strTelephone},{$strEmail},{$strNotes}\n";
                while ($row = mysql_fetch_object($result))
                {
                    $csv .= "{$row->maintid},{$row->site},{$row->product},{$row->reseller},{$row->license_quantity} {$row->licence_type},";
                    $csv .= date($CONFIG['dateformat_date'], $row->expirydate);
                    $csv .= ",{$row->admincontactforenames} {$row->admincontactsurname},{$row->admincontactphone},{$row->admincontactemail},";
                    $notes = nl2br($row->notes);
                    $notes = str_replace(","," ",$notes);
                    $notes = str_replace("\n"," ",$notes);
                    $notes = str_replace("\r"," ",$notes);
                    $notes = str_replace("<br />"," ",$notes);
                    $csv .= "{$notes}\n";
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
