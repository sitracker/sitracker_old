<?php
// search_incident.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// This Page Is Valid XHTML 1.0 Transitional!  10Jan06

@include('set_include_path.inc.php');
$permission=6; // View Incidents

require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

// External variables
$search_string = cleanvar($_REQUEST['search_string']);

include('htmlheader.inc.php');

// show search incidents form
if (empty($search_string))
{
    ?>
    <h2>Search Incidents</h2>
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get">
    <table summary="Form to Search Incidents" class='vertical'>

    <tr><th>Search String:</th><td><input maxlength='100' name="search_string" size="30" type="text" />
    </td></tr>
    <tr><th>Search Fields:</th><td>
    <select name="fields">
        <option value="all">All Fields (Except Site)</option>
        <option value="title">Title</option>
        <option value="id">ID</option>
        <option value="externalid">External ID</option>
        <option value="contact">Contact</option>
        <option value="site">Site</option>
    </select>
    </td></tr>
    </table>
    <p><input name="submit" type="submit" value="Search" /></p>

    <p><a href="advanced_search_incidents.php">Advanced Search</a></p>
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
        if ($fields == "all" || $fields=='')
        {
            $sql  = "SELECT incidents.id, externalid, title, priority, siteid, owner, type, surname, forenames, lastupdated, status FROM incidents, contacts, priority WHERE contact=contacts.id ";
            $sql .= "AND incidents.priority=priority.id AND ";
            $sql .= "(title LIKE ('%$search_string%') OR ";
            $sql .= "incidents.id LIKE ('%$search_string%') OR ";
            $sql .= "externalid LIKE ('%$search_string%') OR ";
            $sql .= "surname LIKE ('%$search_string%') OR ";
            $sql .= "forenames LIKE ('%$search_string%') OR ";
            $sql .= "CONCAT(forenames, ' ', surname) LIKE ('%$search_string%')) ";
        }
        elseif ($fields == "title")
        {
            $sql  = "SELECT incidents.id, externalid, title, priority, siteid, owner, type, surname, forenames, lastupdated, status, contacts.siteid AS siteid FROM incidents, contacts WHERE contact=contacts.id AND title LIKE ('%$search_string%')";
        }
        elseif ($fields == "id")
        {
            $sql  = "SELECT incidents.id, externalid, title, priority, siteid, owner, type, surname, forenames, lastupdated, status, contacts.siteid AS siteid FROM incidents, contacts WHERE contact=contacts.id AND incidents.id LIKE ('%$search_string%')";
        }
        elseif ($fields == "externalid")
        {
            $sql  = "SELECT incidents.id, externalid, title, priority, siteid, owner, type, surname, forenames, lastupdated, status, contacts.siteid AS siteid FROM incidents, contacts WHERE contact=contacts.id AND externalid LIKE ('%$search_string%')";
        }
        elseif ($fields == "contact")
        {
            $sql  = "SELECT incidents.id, externalid, title, priority, siteid, owner, type, surname, forenames, lastupdated, status, contacts.siteid AS siteid FROM incidents, contacts WHERE contact=contacts.id AND surname LIKE ('%$search_string%')";
        }
        elseif ($fields == "site")
        {
            $sql  = "SELECT incidents.id, externalid, title, priority, siteid, owner, type, surname, forenames, lastupdated, status, contacts.siteid AS siteid FROM incidents, contacts, sites WHERE contact=contacts.id AND contacts.siteid=sites.id AND sites.name LIKE ('%$search_string%') ORDER BY incidents.id";
        }

        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        if (mysql_num_rows($result) == 0)
        {
            echo "<h2>Sorry, your search yielded no results</h2>\n";
            echo "<p align='center'><a href=\"advanced_search_incidents.php\">Try an Advanced Search</a></p>";
        }
        else
        {
            ?>
            <h2>Search yielded <?php echo mysql_num_rows($result) ?> result(s)</h2>
            <table align="center" summary="Search results">
            <tr>
            <th>ID (Ext ID)</th>
            <th>Title</th>
            <th>Contact</th>
            <th>Site</th>
            <th>Priority</th>
            <th>Owner</th>
            <th>Last Updated</th>
            <th>Type</th>
            <th>Status</th>
            </tr>
            <?php
            $shade = 0;
            $now = time();
            while ($results = mysql_fetch_array($result))
            {
                // define class for table row shading
                if ($results["timeofnextaction"] < $now && $results["timeofnextaction"] != 0)
                    $class = "urgent";
                else
                {
                    if ($shade) $class = "shade1";
                    else $class = "shade2";
                }
                if ($results['status']==2) $class="expired";
                ?>
                <tr>
                <td align='center' class='<?php echo $class; ?>' width='100'><?php echo $results["id"] ?> (<?php if ($results["externalid"] == "") echo "None"; else echo $results["externalid"] ?>)</td>
                <td class='<?php echo $class; ?>' width='150'><a href="javascript:incident_details_window('<?php echo $results["id"] ?>')"><?php echo $results["title"] ?></a></td>
                <td align='center' class='<?php echo $class; ?>' width='100'><?php echo $results['forenames'].' '.$results['surname']; ?></td>
                <td align='center' class='<?php echo $class; ?>' width='100'><?php echo site_name($results["siteid"]) ?></td>
                <td align='center' class='<?php echo $class; ?>' width='50'><?php echo priority_name($results["priority"]) ?></td>
                <td align='center' class='<?php echo $class; ?>' width='100'><?php echo user_realname($results["owner"],TRUE) ?></td>
                <td align='center' class='<?php echo $class; ?>' width='150'><?php echo date($CONFIG['dateformat_datetime'], $results["lastupdated"]); ?></td>
                <td align='center' class='<?php echo $class; ?>' width='50'><?php echo $results["type"] ?></td>
                <td align='center' class='<?php echo $class; ?>' width='50'><?php echo incidentstatus_name($results["status"]); ?></td>
                </tr>
                <?php
                // invert shade
                if ($shade == 1) $shade = 0;
                else $shade = 1;
            }
        }
        echo "</table>";
        echo "<br />";
        echo "<p align='center'><a href=\"search_incidents.php\">Search Again</a></p>";
    }
}
include('htmlfooter.inc.php');
?>
