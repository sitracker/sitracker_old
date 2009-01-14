<?php
// search_sites.php - Form for searching sites
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

@include ('set_include_path.inc.php');
$permission = 11; // View Sites

require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

// External variables
$search_string = cleanvar($_REQUEST['search_string']);
$user = cleanvar($_REQUEST['user']);

// show search sites form
if (empty($search_string))
{
    include ('htmlheader.inc.php');
    ?>
    <h2>Search Sites</h2>
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
    <p>
    <table align='center'>
    <tr><td align='right' width='200'><strong>Search Fields</strong>:</td><td width='300'>
    <select name="fields">
    <option value="all">All Fields</option>
    <option value="id">ID</option>
    <option value="realname">Name</option>
    <option value="address1">address1</option>
    </select>
    </td></tr>
    <tr><td align='right' width='200'><strong>Search String</strong>:</td><td width='300'><input maxlength='100' name="search_string" size=30 type="text" /></td></tr>
    </table>
    </p>
    <input name="submit" type="submit" value="Search" />
    </form>
    <?php
    include ('htmlfooter.inc.php');
}
else
{
    // perform search
    include ('htmlheader.inc.php');

    // check input
    if ($search_string == '' AND $user== '')
    {
        $errors = 1;
        echo "<p class='error'>You must enter a search string</p>\n";
    }

    if ($errors == 0)
    {
        // search for criteria
        // build SQL
        if ($fields == "all")
        {
            $sql  = "SELECT id, name, address1 FROM `{$dbSites}` WHERE ";
            $sql .= "id LIKE ('%$search_string%') OR ";
            $sql .= "name LIKE ('%$search_string%') OR ";
            $sql .= "address1 LIKE ('%$search_string%')";
        }
        else if ($fields == "id")
        {
            $sql  = "SELECT id, name, address1 FROM `{$dbSites}` WHERE ";
            $sql .= "id LIKE ('%$search_string%')";
        }
        else if ($fields == "name")
        {
            $sql  = "SELECT id, name, address1 FROM `{$dbSites}` WHERE ";
            $sql .= "name LIKE ('%$search_string%')";
        }
        else if ($fields == "address1")
        {
            $sql  = "SELECT id, name, address1 FROM `{$dbSites}` WHERE ";
            $sql .= "address1 LIKE ('%$search_string%')";
        }

        if ($user=='current') $user=$sit[2];
        if (!empty($user)) $sql .= " AND owner='$user' ";

        $sql .= " ORDER BY name ASC";

        // execute query
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);

        if (mysql_num_rows($result) == 0)
        {
            echo "<p class='error'>Sorry, your search for '$search_string' in 'sites' yielded no results</p>\n";
            echo "<p align='center'><a href=\"search.php?query=$search_string\">Search Again</a></p>";
        }
        else
        {
            // border=1 bordercolor=#FFFFFF cellpadding=1 cellspacing=0 width=550
            ?>
            <h3>Search yielded <?php echo mysql_num_rows($result) ?> result(s)</h3>
            <table align='center'>
            <tr>
            <td class='shade1' width=50><b>ID</b></td>
            <td class='shade1' width='300'><b>Name</b></td>
            <td class='shade1' width='200'><b>address1</b></td>
            </tr>
            <?php
            $shade = 0;
            while ($results = mysql_fetch_array($result))
            {
                // define class for table row shading
                if ($shade) $class = "shade1";
                else $class = "shade2";
                ?>
                <tr>
                <td class='<?php echo $class; ?>' width=50><?php echo $results["id"] ?></a></td>
                <td class='<?php echo $class; ?>' width='300'><a href="site_details.php?id=<?php echo $results['id']; ?>&action=show"><?php echo $results['name'] ?></a></td>
                <td class='<?php echo $class; ?>' width='200'><?php echo nl2br($results["address1"]) ?></td>
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
    include ('htmlfooter.inc.php');
}
?>
