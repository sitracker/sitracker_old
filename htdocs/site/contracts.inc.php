<?php


    echo "<h3>Related Contracts<a id='contracts'></a></h3>";

    // Display contracts
    $sql  = "SELECT maintenance.id AS maintid, maintenance.term AS term, products.name AS product, resellers.name AS reseller, licence_quantity, licencetypes.name AS licence_type, expirydate, admincontact, contacts.forenames AS admincontactsforenames, contacts.surname AS admincontactssurname, maintenance.notes AS maintnotes ";
    $sql .= "FROM maintenance, contacts, products, licencetypes, resellers ";
    $sql .= "WHERE maintenance.product=products.id AND maintenance.reseller=resellers.id AND licence_type=licencetypes.id AND admincontact=contacts.id ";
    $sql .= "AND maintenance.site = '$id' ";
    $sql .= "ORDER BY expirydate DESC";

    // connect to database and execute query
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
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
    <p align='center'>
    <?php echo mysql_num_rows($result) ?> Contract(s)</p>
    <table align='center'>
    <tr>
        <th>Contract ID</th>
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
        if ($results['term']=='yes' || $results['expirydate']<$now) $class = "expired";
        ?>
        <tr>
            <td class='<?php echo $class ?>'><a href="maintenance_details.php?id=<?php echo $results['maintid'] ?>">Contract <?php echo $results['maintid'] ?></a></td>
            <td class='<?php echo $class ?>'><?php echo $results["product"] ?></td>
            <td class='<?php echo $class ?>'><?php echo $results["reseller"] ?></td>
            <td class='<?php echo $class ?>'><?php echo $results["licence_quantity"] ?> <?php echo $results["licence_type"] ?></td>
            <td class='<?php echo $class ?>'><?php echo date($CONFIG['dateformat_date'], $results["expirydate"]); ?></td>
            <td class='<?php echo $class ?>'><?php echo $results['admincontactsforenames'].' '.$results['admincontactssurname'] ?></td>
            <td class='<?php echo $class ?>'><?php if ($results['maintnotes'] == '') echo '&nbsp;'; else echo nl2br($results['maintnotes']); ?></td>
        </tr>
        <?php
        // invert shade
        if ($shade == 1) $shade = 0;
        else $shade = 1;
    }
    echo "</table>\n";
    echo "<p align='center'><a href='add_maintenance.php?action=showform&amp;siteid=$id'>Add Contract</a></p>";

?>