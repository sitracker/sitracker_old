<?php
// search_renewals.php - Show contracts due for renewal
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>


$permission = 19; // View Maintenance Contracts
require ('core.php');
require (APPLICATION_LIBPATH . 'functions.inc.php');
$title = $strShowRenewals;

// This page requires authentication
require (APPLICATION_LIBPATH . 'auth.inc.php');

// External variables
$expire = cleanvar($_REQUEST['expire']);

// show search renewal form
if (empty($expire))
{
    include (APPLICATION_INCPATH . 'htmlheader.inc.php');
    ?>
    <h2><?php echo $title; ?></h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
 
    <?php
    printf("<p>{$strContractsExpiringWithinXdays}</p>", "<input maxlength='3' name='expire' size='3' type='text' />");
    echo "<p><input name='submit' type='submit' value=\"{$strSearch}\" /></p>";
    echo "</form>\n";
    include (APPLICATION_INCPATH . 'htmlfooter.inc.php');
}
else
{
    // perform search
    include (APPLICATION_INCPATH . 'htmlheader.inc.php');
    // check input
    if ($expire == '')
    {
        $errors = 1;
        echo "<p class='error'>You must enter a number of days</p>\n";
    }
    elseif (!is_numeric($expire))
    {
        $errors = 1;
        echo "<p class='error'>You must enter a numeric value</p>\n";
    }
    if ($errors == 0)
    {
        // convert number of days into a timestamp
        $now = time();
        $max_expiry = $now + ($expire * 86400);
        // build SQL
        $sql  = "SELECT m.id AS maintid, s.name AS site, p.name AS product, r.name AS reseller, ";
        $sql .= "licence_quantity, l.name AS licence_type, expirydate, admincontact, ";
        $sql .= "c.forenames AS admincontactforenames, c.surname AS admincontactsurname, m.notes ";
        $sql .= "FROM `{$dbMaintenance}` AS m, `{$dbSites}` AS s, `{$dbContacts}` AS c, `{$dbProducts}` AS p, `{$dbLicenceTypes}` AS l, `{$dbResellers}` AS r ";
        $sql .= "WHERE (m.site = s.id AND product = p.id AND reseller = r.id AND licence_type = l.id AND admincontact = c.id) AND ";
        $sql .= "expirydate <= $max_expiry AND expirydate >= $now ORDER BY expirydate ASC";

        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);

        if (mysql_num_rows($result) == 0)
        {
            printf("<h2>{$strContractsExpiringWithinXdays}</h2>", $expire);
            echo "<h5 class='warning'>Sorry, your search yielded no results</h5>\n";
        }
        else
        {
            ?>
            <script type="text/javascript">
            //<![CDATA[
            function support_contacts_window(maintenanceid)
            {
                URL = "support_contacts.php?maintid=" + maintenanceid;
                window.open(URL, "support_contacts_window", "toolbar=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=450,height=240");
            }
            function contact_details_window(contactid)
            {
                URL = "contact_details.php?id=" + contactid;
                window.open(URL, "contact_details_window", "toolbar=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=550,height=640");
            }
            //]]>
            </script>
            <?php
            printf("<h2>{$strContractsExpiringWithinXdays}</h2>", $expire);
            printf("<h5>{$strResultsNum}</h5>", mysql_num_rows($result));
            echo "
            <table align='center'>
            <tr>
            <th>{$strID}</th>
            <th>{$strSite}</th>
            <th>{$strProduct}</th>
            <th>{$strReseller}</th>
            <th>{$strLicense}</th>
            <th>{$strExpiryDate}</th>
            <th>{$strAdminContact}</th>
            <th>{$strNotes}</th>
            </tr>";
            $shade = 0;
            while ($results = mysql_fetch_array($result))
            {
                // define class for table row shading
                if ($shade) $class = "shade1";
                else $class = "shade2";
                ?>
                <tr>
                <td align='center' class='<?php echo $class ?>' width='50'><a href="contract_edit.php?action=edit&amp;maintid=<?php echo $results["maintid"] ?>" ><?php echo $results["maintid"] ?></a></td>
                <td align='center' class='<?php echo $class ?>' width='100'><?php echo $results["site"] ?></td>
                <td align='center' class='<?php echo $class ?>' width='100'><?php echo $results["product"] ?></td>
                <td align='center' class='<?php echo $class ?>' width='100'><?php echo $results["reseller"] ?></td>
                <td align='center' class='<?php echo $class ?>' width='75'><?php echo $results["licence_quantity"] ?> <?php echo $results["licence_type"] ?></td>
                <td align='center' class='<?php echo $class ?>' width='100'><?php echo ldate($CONFIG['dateformat_date'], $results["expirydate"]); ?></td>
                <td align='center' class='<?php echo $class ?>' width='100'><a href="javascript: contact_details_window(<?php echo $results["admincontact"]?>)"><?php echo $results['admincontactforenames'].' '.$results['admincontactsurname'] ?></a></td>
                <td align='center' class='<?php echo $class ?>' width='150'><?php if ($results["notes"] == '') echo "&nbsp;"; else echo nl2br($results["notes"]); ?></td>
                </tr>
                <?php
                // invert shade
                if ($shade == 1) $shade = 0;
                else $shade = 1;
            }
            ?>
            </table>
            <?php
        }
    }
    include (APPLICATION_INCPATH . 'htmlfooter.inc.php');
}
?>
