<?php
// search_maintenance.php - Search contracts
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// This Page Is Valid XHTML 1.0 Transitional!  29Nov05

// FIXME i18n whole page

@include ('set_include_path.inc.php');
$permission = 19; // View Contracts
require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

// External variables
$search_string = cleanvar($_REQUEST['search_string']);
$fields = cleanvar($_REQUEST['fields']);
$hideterminated = cleanvar($_REQUEST['hideterminated']);
$hideexpired = cleanvar($_REQUEST['hideexpired']);

// show search maintenance form
if (empty($search_string))
{
    include ('htmlheader.inc.php');
    ?>
    <h2>Search Contracts</h2>
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get">
    <table align='center'>
    <tr><th>Search Fields:</th><td>
    <select name="fields">
    <option value="all">All Fields</option>
    <option value="id">ID</option>
    <option value="site">Site</option>
    <option value="product">Product</option>
    <option value="admincontact">Admin Contact</option>
    <option value="reseller">Reseller</option>
    <option value="licence_type">Licence Type</option>
    </select>
    </td></tr>
    <tr><th>Search String:</th><td><input maxlength='100' name="search_string" size='30' type='text' /></td></tr>
    <tr><th>Terminated</th><td><label><input type='checkbox' name='hideterminated' value='yes' />Hide terminated contracts</label></td></tr>
    <tr><th>Expired</th><td><input type='checkbox' name='hideexpired' value='yes' />Hide expired contracts</td></tr>
    </table>
    <p><input name="submit" type="submit" value="Search" /></p>
    </form>
    <?php
    include ('htmlfooter.inc.php');
}
else
{
    // perform search
    include ('htmlheader.inc.php');
    // check input
    if ($search_string == '')
    {
        $errors = 1;
        echo "<p class='error'>You must enter a search string</p>\n";
    }
    if ($errors == 0)
    {
        // search for criteria
        // build SQL
        if ($fields=='' OR $fields == "all")
        {
            $sql  = "SELECT m.id AS maintid, s.name AS site, p.name AS product, r.name AS reseller, ";
            $sql .= "licence_quantity, lt.name AS licence_type, expirydate, admincontact, c.forenames AS admincontactforenames, ";
            $sql .= "c.surname AS admincontactsurnname, m.notes, m.term ";
            $sql .= "FROM `{$dbMaintenance}` AS m, `{$dbSites}` AS s, `{$dbContacts}` AS c, `{$dbProducts}` AS p, `{$dbLicenceTypes}` AS lt, `{$dbResellers}` AS r ";
            $sql .= "WHERE ";
            $sql .= "(m.site = s.id AND product = p.id AND reseller = r.id AND licence_type = lt.id AND admincontact = c.id) AND ";
            $sql .= "(m.id LIKE ('%$search_string%') OR ";
            $sql .= "s.name LIKE ('%$search_string%') OR ";
            $sql .= "p.name LIKE ('%$search_string%') OR ";
            $sql .= "r.name LIKE ('%$search_string%') OR ";
            $sql .= "licencetypes.name LIKE ('%$search_string%') OR ";
            $sql .= "c.surname LIKE ('%$search_string%'))";
            if ($hideterminated=='yes') $sql .= " AND NOT m.term = 'yes'";
            if ($hideexpired=='yes') $sql .= " AND m.expirydate > {$now}";
        }
        elseif ($fields == "id")
        {
            $sql  = "SELECT m.id AS maintid, s.name AS site, p.name AS product, r.name AS reseller, ";
            $sql .= "licence_quantity, lt.name AS licence_type, expirydate, admincontact, c.forenames AS admincontactforenames, ";
            $sql .= "c.surname AS admincontactsurnname, m.notes, m.term ";
            $sql .= "FROM `{$dbMaintenance}` AS m, `{$dbSites}` AS s, `{$dbContacts}` AS c, `{$dbProducts}` AS p, `{$dbLicenceTypes}` AS lt, `{$dbResellers}` AS r ";
            $sql .= "WHERE ";
            $sql .= "(m.site = s.id AND product = p.id AND reseller = r.id AND licence_type = lt.id AND admincontact = c.id) AND ";
            $sql .= "(m.id LIKE ('%$search_string%'))";
            if ($hideterminated=='yes') $sql .= " AND NOT m.term ='yes'";
            if ($hideexpired=='yes') $sql .= " AND m.expirydate > {$now}";
        }
        elseif ($fields == "site")
        {
            $sql  = "SELECT m.id AS maintid, s.name AS site, p.name AS product, r.name AS reseller, ";
            $sql .= "licence_quantity, lt.name AS licence_type, expirydate, admincontact, c.forenames AS admincontactforenames, ";
            $sql .= "c.surname AS admincontactsurnname, m.notes, m.term ";
            $sql .= "FROM `{$dbMaintenance}` AS m, `{$dbSites}` AS s, `{$dbContacts}` AS c, `{$dbProducts}` AS p, `{$dbLicenceTypes}` AS lt, `{$dbResellers}` AS r ";
            $sql .= "WHERE ";
            $sql .= "(m.site = s.id AND product = p.id AND reseller = r.id AND licence_type = lt.id AND admincontact = c.id) AND ";
            $sql .= "(s.name LIKE ('%$search_string%'))";
            if ($hideterminated=='yes') $sql .= " AND m.term!='yes'";
            if ($hideexpired=='yes') $sql .= " AND m.expirydate > {$now}";
        }
        elseif ($fields == "product")
        {
            $sql  = "SELECT m.id AS maintid, s.name AS site, p.name AS product, r.name AS reseller, ";
            $sql .= "licence_quantity, lt.name AS licence_type, expirydate, admincontact, c.forenames AS admincontactforenames, ";
            $sql .= "c.surname AS admincontactsurnname, m.notes, m.term ";
            $sql .= "FROM `{$dbMaintenance}` AS m, `{$dbSites}` AS s, `{$dbContacts}` AS c, `{$dbProducts}` AS p, `{$dbLicenceTypes}` AS lt, `{$dbResellers}` AS r ";
            $sql .= "WHERE ";
            $sql .= "(m.site = s.id AND product = p.id AND reseller = r.id AND licence_type = lt.id AND admincontact = c.id) AND ";
            $sql .= "(p.name LIKE ('%$search_string%'))";
            if ($hideterminated=='yes') $sql .= " AND m.term!='yes'";
            if ($hideexpired=='yes') $sql .= " AND m.expirydate > {$now}";
        }
        elseif ($fields == "admincontact")
        {
            $sql  = "SELECT m.id AS maintid, s.name AS site, p.name AS product, r.name AS reseller, licence_quantity, lt.name AS licence_type, expirydate, admincontact, c.forenames AS admincontactforenames, c.surname AS admincontactsurnname, m.notes, m.term ";
            $sql .= "FROM `{$dbMaintenance}` AS m, `{$dbSites}` AS s, `{$dbContacts}` AS c, `{$dbProducts}` AS p, `{$dbLicenceTypes}` AS lt, `{$dbResellers}` AS r ";
            $sql .= "WHERE ";
            $sql .= "(m.site = s.id AND product = p.id AND reseller = r.id AND licence_type = lt.id AND admincontact = c.id) AND ";
            $sql .= "(c.surname LIKE ('%$search_string%'))";
            if ($hideterminated=='yes') $sql .= " AND m.term!='yes'";
            if ($hideexpired=='yes') $sql .= " AND m.expirydate > {$now}";
        }
        elseif ($fields == "reseller")
        {
            $sql  = "SELECT m.id AS maintid, s.name AS site, p.name AS product, resellers.name AS reseller, licence_quantity, licencetypes.name AS licence_type, expirydate, admincontact, c.forenames AS admincontactforenames, c.surname AS admincontactsurnname, m.notes, m.term ";
            $sql .= "FROM `{$dbMaintenance}` AS m, `{$dbSites}` AS s, `{$dbContacts}` AS c, `{$dbProducts}` AS p, `{$dbLicenceTypes}` AS lt, `{$dbResellers}` AS r WHERE ";
            $sql .= "(m.site = s.id AND product = p.id AND reseller = r.id AND licence_type = lt.id AND admincontact = c.id) AND ";
            $sql .= "(r.name LIKE ('%$search_string%'))";
            if ($hideterminated=='yes') $sql .= " AND m.term!='yes'";
            if ($hideexpired=='yes') $sql .= " AND m.expirydate > {$now}";
        }
        elseif ($fields == "licence_type")
        {
            $sql  = "SELECT m.id AS maintid, s.name AS site, products.name AS product, resellers.name AS reseller, licence_quantity, licencetypes.name AS licence_type, expirydate, admincontact, c.forenames AS admincontactforenames, c.surname AS admincontactsurnname, m.notes, m.term ";
            $sql .= "FROM `{$dbMaintenance}` AS m, `{$dbSites}` AS s, `{$dbContacts}` AS c, `{$dbProducts}` AS p, `{$dbLicenceTypes}` AS lt, `{$dbResellers}` AS r WHERE ";
            $sql .= "(m.site = s.id AND product = p.id AND reseller = r.id AND licence_type = lt.id AND admincontact = c.id) AND ";
            $sql .= "(lt.name LIKE ('%$search_string%'))";
            if ($hideterminated=='yes') $sql .= " AND m.term!='yes'";
            if ($hideexpired=='yes') $sql .= " AND m.expirydate > {$now}";
        }
        $sql .= " ORDER BY site ASC";

        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error".mysql_error(), E_USER_WARNING);

        if (mysql_num_rows($result) == 0)
        {
            echo "<p class='error'>Sorry, your search for '$search_string' in 'maintenance' yielded no results</p>\n";
            echo "<p align='center'><a href=\"search.php?query=$search_string\">Search Again</a></p>";
        }
        else
        {
            ?>
            <script type="text/javascript">
            function support_contacts_window(maintenanceid)
            {
                URL = "support_c.php?maintid=" + maintenanceid;
                window.open(URL, "support_contacts_window", "toolbar=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=450,height=240");
            }
            function contact_details_window(contactid)
            {
                URL = "contact_details.php?contactid=" + contactid;
                window.open(URL, "contact_details_window", "toolbar=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=450,height=240");
            }
            </script>
            <h2>Search yielded <?php echo mysql_num_rows($result) ?> result(s)</h2>
            <table align='center'>
            <tr>
            <th>ID</th>
            <th>Site</th>
            <th>Product</th>
            <th>Reseller</th>
            <th>Licence</th>
            <th>Expiry Date</th>
            <th>Admin Contact</th>
            <th>Notes</th>
            </tr>
            <?php
            $shade = 0;
            while ($results = mysql_fetch_array($result))
            {
                // define class for table row shading
                if ($shade) $class = "shade1";
                else $class = "shade2";
                echo "<tr class='$class'>";
                echo "<td><a href='contract_details.php?id=";
                echo "{$results['maintid']}'>{$results["maintid"]}</a></td>";
                echo "<td>{$results["site"]}</td>";
                echo "<td>{$results["product"]}</td>";
                echo "<td>{$results["reseller"]}</td>";
                echo "<td>{$results["licence_quantity"]} ";
                echo "{$results["licence_type"]}</td>";
                echo "<td>";
                echo ldate($CONFIG['dateformat_date'], $results["expirydate"]);
                if ($results["term"] == 'yes')
                {
                	echo "<br />Terminated";
                }
                echo "</td>";
                echo "<td><a href='contact_details.php?id=";
                echo "{$results['admincontact']}'>";
                echo "{$results['admincontactforenames']} ";
                echo "{$results['admincontactsurnname']}</a></td>";
                echo "<td align='center' width='150'>";
                if ($results["notes"] == ''){
                	echo "&nbsp;";
                }
                else
                {
                	echo nl2br($results["notes"]);
                }
                echo "</td></tr>";
                // invert shade
                if ($shade == 1) $shade = 0;
                else $shade = 1;
            }
            echo "</table>";
            echo "<p align='center'>";
            echo "<a href='search.php?query={$search_string}&amp;context=";
            echo "maintenance'>{$strSearchAgain}</a></p>";
        }
    }
    include ('htmlfooter.inc.php');
}
?>
