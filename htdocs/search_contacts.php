<?php
// search_contacts.php - Search contacts
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// DEPRECATED will be removed in 3.32, replaced by search.php

@include('set_include_path.inc.php');
$permission=array(60,12); // Perform Searches, View Contacts
require('db_connect.inc.php');
require('functions.inc.php');
$title="Search Contacts";

// This page requires authentication
require('auth.inc.php');


// External variables
$search_string = cleanvar($_REQUEST['search_string']);
$fields = cleanvar($_REQUEST['fields']);

include('htmlheader.inc.php');
?>
<script type="text/javascript">
function contact_products_window(contactid)
{
    URL = "contact_products.php?id=" + contactid;
    window.open(URL, "contact_products_window", "toolbar=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=520,height=240");
}
</script>
<?php

// show add incident form
if (empty($search_string))
{
    echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/search.png' width='32' height='32' alt='' /> ";
    echo "Search Contacts</h2>";

    echo "<form action='{$_SERVER['PHP_SELF']}' method='post'>";
?>
    <table align='center' class='vertical'>
    <tr><th>Search String:</th><td><input maxlength='100' name="search_string" size=30 type="text" /></td></tr>
    <tr><th>Search Fields:</th><td>
    <select name="fields">
    <option value="all">All Fields
    <option value="surname">Contact Surname
    <option value="site">Site Name
    <option value="email">Email
    <option value="phone">Phone
    <option value="fax">Fax
    </select>
    </td></tr>
    </table>
    <p><input name="submit" type="submit" value="Search" /></p>
    </form>
    <?php
}
else
{
    // perform search
    // check input
    if ($search_string == "")
    {
        $errors = 1;
        echo "<p class='error'>You must enter a search string</p>\n";
    }

    // search for criteria
    if ($errors == 0)
    {
        // build SQL
        if ($fields == "all")
        {
            $sql  = "SELECT * FROM contacts WHERE ";
            $sql .= "surname LIKE ('%$search_string%') OR ";
            $sql .= "forenames LIKE ('%$search_string%') OR ";
            $sql .= "email LIKE ('%$search_string%') OR ";
            $sql .= "phone LIKE ('%$search_string%') OR ";
            $sql .= "fax LIKE ('%$search_string%')";
        }
        elseif ($fields == "surname")
        {
            $sql  = "SELECT * FROM contacts WHERE surname LIKE ('%$search_string%')";
        }
        elseif ($fields == "email")
        {
            $sql  = "SELECT * FROM contacts WHERE email LIKE ('%$search_string%')";
        }
        elseif ($fields == "phone")
        {
            $sql  = "SELECT * FROM contacts WHERE phone LIKE ('%$search_string%')";
        }
        elseif ($fields == "fax")
        {
            $sql  = "SELECT * FROM contacts WHERE fax LIKE ('%$search_string%')";
        }

        // execute query
        $result = mysql_query($sql);

        echo $sql;

        if (mysql_num_rows($result) == 0)
        {
            echo "<p class='error'>Sorry, your search yielded no results</p>\n";
        }
        else
        {
            ?>
            <h2>Search yielded <?php echo mysql_num_rows($result) ?> result(s)</h2>
            <?php
            echo "<table align='center'>
            <tr>
            <th>Contact Name</th>
            <th>Site</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Fax</th>
            <th>{$strAddIncident}</th>
            </tr>";
            $shade = 0;
            while ($results = mysql_fetch_array($result))
            {
                // define class for table row shading
                if ($shade) $class = "shade1";
                else $class = "shade2";
                ?>
                <tr>
                <td align='center' class='<?php echo $class ?>' width='150'><a href="contact_details.php?id=<?php echo $results["id"] ?>" target="_new"><?php echo $results['forenames'].' '.$results['surname'] ?></a></td>
                <td align='center' class='<?php echo $class ?>' width='200'><?php echo site_name($results['siteid']) ?></td>
                <td align='center' class='<?php echo $class ?>' width='150'><?php echo $results["email"] ?></td>
                <td align='center' class='<?php echo $class ?>' width='100'><?php if ($results["phone"] == "") { ?>None<?php } else { echo $results["phone"]; } ?></td>
                <td align='center' class='<?php echo $class ?>' width='100'><?php if ($results["fax"] == "") { ?>None<?php } else { echo $results["fax"]; } ?></td>
                <td align='center' class='<?php echo $class ?>' width='100'><a href="add_incident.php?action=findcontact&contactid=<?php echo $results["id"] ?>">Add Incident</a></td>
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
}
include('htmlfooter.inc.php');
?>
