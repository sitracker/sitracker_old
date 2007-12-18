<?php
// search_maintenance.php - Search contracts
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// This Page Is Valid XHTML 1.0 Transitional!  29Nov05

// FIXME i18n whole page

@include ('set_include_path.inc.php');
$permission=19; // View Contracts
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
    if ($search_string == "")
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
            $sql  = "SELECT maintenance.id AS maintid, sites.name AS site, products.name AS product, resellers.name AS reseller, licence_quantity, licencetypes.name AS licence_type, expirydate, admincontact, contacts.forenames AS admincontactforenames, contacts.surname AS admincontactsurnname, maintenance.notes, maintenance.term FROM maintenance, sites, contacts, products, licencetypes, resellers WHERE ";
            $sql .= "(maintenance.site=sites.id AND product=products.id AND reseller=resellers.id AND licence_type=licencetypes.id AND admincontact=contacts.id) AND ";
            $sql .= "(maintenance.id LIKE ('%$search_string%') OR ";
            $sql .= "sites.name LIKE ('%$search_string%') OR ";
            $sql .= "products.name LIKE ('%$search_string%') OR ";
            $sql .= "resellers.name LIKE ('%$search_string%') OR ";
            $sql .= "licencetypes.name LIKE ('%$search_string%') OR ";
            $sql .= "contacts.surname LIKE ('%$search_string%'))";
            if ($hideterminated=='yes') $sql .= " AND NOT maintenance.term = 'yes'";
            if ($hideexpired=='yes') $sql .= " AND maintenance.expirydate > {$now}";
        }
        elseif ($fields == "id")
        {
            $sql  = "SELECT maintenance.id AS maintid, sites.name AS site, products.name AS product, resellers.name AS reseller, licence_quantity, licencetypes.name AS licence_type, expirydate, admincontact, contacts.forenames AS admincontactforenames, contacts.surname AS admincontactsurnname, maintenance.notes, maintenance.term FROM maintenance, sites, contacts, products, licencetypes, resellers WHERE ";
            $sql .= "(maintenance.site=sites.id AND product=products.id AND reseller=resellers.id AND licence_type=licencetypes.id AND admincontact=contacts.id) AND ";
            $sql .= "(maintenance.id LIKE ('%$search_string%'))";
            if ($hideterminated=='yes') $sql .= " AND NOT maintenance.term ='yes'";
            if ($hideexpired=='yes') $sql .= " AND maintenance.expirydate > {$now}";
        }
        elseif ($fields == "site")
        {
            $sql  = "SELECT maintenance.id AS maintid, sites.name AS site, products.name AS product, resellers.name AS reseller, licence_quantity, licencetypes.name AS licence_type, expirydate, admincontact, contacts.forenames AS admincontactforenames, contacts.surname AS admincontactsurnname, maintenance.notes, maintenance.term FROM maintenance, sites, contacts, products, licencetypes, resellers WHERE ";
            $sql .= "(maintenance.site=sites.id AND product=products.id AND reseller=resellers.id AND licence_type=licencetypes.id AND admincontact=contacts.id) AND ";
            $sql .= "(sites.name LIKE ('%$search_string%'))";
            if ($hideterminated=='yes') $sql .= " AND maintenance.term!='yes'";
            if ($hideexpired=='yes') $sql .= " AND maintenance.expirydate > {$now}";
        }
        elseif ($fields == "product")
        {
            $sql  = "SELECT maintenance.id AS maintid, sites.name AS site, products.name AS product, resellers.name AS reseller, licence_quantity, licencetypes.name AS licence_type, expirydate, admincontact, contacts.forenames AS admincontactforenames, contacts.surname AS admincontactsurnname, maintenance.notes, maintenance.term FROM maintenance, sites, contacts, products, licencetypes, resellers WHERE ";
            $sql .= "(maintenance.site=sites.id AND product=products.id AND reseller=resellers.id AND licence_type=licencetypes.id AND admincontact=contacts.id) AND ";
            $sql .= "(products.name LIKE ('%$search_string%'))";
            if ($hideterminated=='yes') $sql .= " AND maintenance.term!='yes'";
            if ($hideexpired=='yes') $sql .= " AND maintenance.expirydate > {$now}";
        }
        elseif ($fields == "admincontact")
        {
            $sql  = "SELECT maintenance.id AS maintid, sites.name AS site, products.name AS product, resellers.name AS reseller, licence_quantity, licencetypes.name AS licence_type, expirydate, admincontact, contacts.forenames AS admincontactforenames, contacts.surname AS admincontactsurnname, maintenance.notes, maintenance.term FROM maintenance, sites, contacts, products, licencetypes, resellers WHERE ";
            $sql .= "(maintenance.site=sites.id AND product=products.id AND reseller=resellers.id AND licence_type=licencetypes.id AND admincontact=contacts.id) AND ";
            $sql .= "(contacts.surname LIKE ('%$search_string%'))";
            if ($hideterminated=='yes') $sql .= " AND maintenance.term!='yes'";
            if ($hideexpired=='yes') $sql .= " AND maintenance.expirydate > {$now}";
        }
        elseif ($fields == "reseller")
        {
            $sql  = "SELECT maintenance.id AS maintid, sites.name AS site, products.name AS product, resellers.name AS reseller, licence_quantity, licencetypes.name AS licence_type, expirydate, admincontact, contacts.forenames AS admincontactforenames, contacts.surname AS admincontactsurnname, maintenance.notes, maintenance.term FROM maintenance, sites, contacts, products, licencetypes, resellers WHERE ";
            $sql .= "(maintenance.site=sites.id AND product=products.id AND reseller=resellers.id AND licence_type=licencetypes.id AND admincontact=contacts.id) AND ";
            $sql .= "(resellers.name LIKE ('%$search_string%'))";
            if ($hideterminated=='yes') $sql .= " AND maintenance.term!='yes'";
            if ($hideexpired=='yes') $sql .= " AND maintenance.expirydate > {$now}";
        }
        elseif ($fields == "licence_type")
        {
            $sql  = "SELECT maintenance.id AS maintid, sites.name AS site, products.name AS product, resellers.name AS reseller, licence_quantity, licencetypes.name AS licence_type, expirydate, admincontact, contacts.forenames AS admincontactforenames, contacts.surname AS admincontactsurnname, maintenance.notes, maintenance.term FROM maintenance, sites, contacts, products, licencetypes, resellers WHERE ";
            $sql .= "(maintenance.site=sites.id AND product=products.id AND reseller=resellers.id AND licence_type=licencetypes.id AND admincontact=contacts.id) AND ";
            $sql .= "(licencetypes.name LIKE ('%$search_string%'))";
            if ($hideterminated=='yes') $sql .= " AND maintenance.term!='yes'";
            if ($hideexpired=='yes') $sql .= " AND maintenance.expirydate > {$now}";
        }

        $sql .= " ORDER BY site ASC";

        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error".mysql_error(), E_USER_ERROR);

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
                URL = "support_contacts.php?maintid=" + maintenanceid;
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
                ?>
                <td><a href="contract_details.php?id=<?php echo $results['maintid'] ?>"><?php echo $results["maintid"] ?></a></td>
                <td><?php echo $results["site"]; ?></td>
                <td><?php echo $results["product"] ?></td>
                <td><?php echo $results["reseller"] ?></td>
                <td><?php echo $results["licence_quantity"] ?> <?php echo $results["licence_type"] ?></td>
                <td><?php echo date($CONFIG['dateformat_date'], $results["expirydate"]); ?>
                <?php
                if ($results["term"]=='yes') echo "<br />Terminated";
                ?>
                </td>
                <td><a href="contact_details.php?id=<?php echo $results['admincontact']?>" ><?php echo $results['admincontactforenames'].' '.$results['admincontactsurnname']; ?></a></td>                 <td align='center' width='150'><?php if ($results["notes"] == "") echo "&nbsp;"; else echo nl2br($results["notes"]); ?></td>
                </tr>
                <?php
                // invert shade
                if ($shade == 1) $shade = 0;
                else $shade = 1;
            }
            ?>
            </table>
            <p align='center'><a href="search.php?query=<?php echo $search_string; ?>&amp;context=maintenance">Search Again</a></p>
            <?php
        }
    }
    include ('htmlfooter.inc.php');
}
?>
