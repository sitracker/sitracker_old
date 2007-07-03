<?php
// browse_contacts.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//


// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
// This Page Is Valid XHTML 1.0 Transitional! 27Oct05

$permission=12; // View Contacts
$title="Browse Contacts";
require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

// External variables
$search_string = cleanvar($_REQUEST['search_string']);
$submit_value = cleanvar($_REQUEST['submit']);

if($submit_value == 'go')
{
        // build SQL
        $sql  = "SELECT * FROM contacts ";
        $search_string_len=strlen($search_string);
        if ($search_string != '*')
        {
            $sql .= "WHERE ";
            if ($search_string_len<=6) $sql .= "id=('$search_string') OR ";
            if ($search_string_len<=2)
            {
                $sql .= "SUBSTRING(surname,1,$search_string_len)=('$search_string') ";
            }
            else
            {
                $sql .= "surname LIKE '%$search_string%' OR forenames LIKE '%$search_string%' OR ";
                $sql .= "CONCAT(forenames,' ',surname) LIKE '%$search_string%'";
            }
        }
        $sql .= " ORDER BY surname ASC";

        // execute query
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        if(mysql_num_rows($result) == 1)
        {
            //go straight to the contact 
            $row = mysql_fetch_array($result);
            $url = "contact_details.php?id=".$row["id"];
            header("Location: $url");
        }
}

include('htmlheader.inc.php');

if (empty($search_string)) $search_string='a';
?>
<script type="text/javascript">
function contact_products_window(contactid)
{
URL = "contact_products.php?id=" + contactid;
window.open(URL, "contact_products_window", "toolbar=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=520,height=240");
}
</script>
<script type="text/javascript" src="scripts/dojo/dojo.js"></script>
<script type="text/javascript">
    dojo.require("dojo.widget.ComboBox");
</script>
<h2>Browse Contacts</h2>
<table summary="alphamenu" align="center">
<tr>
<td align="center">
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
    <!-- <input type="text" name="search_string" />-->
    <p>Browse contacts: <input dojoType='ComboBox' dataUrl='autocomplete.php?action=contact' style='width: 300px;' name='search_string' />
    <input name="submit" type="submit" value="go" /></p>
    </form>
</td>
</tr>
<tr>
<td valign="middle">
    <a href="add_contact.php">Add Contact</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=A">A</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=B">B</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=C">C</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=D">D</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=E">E</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=F">F</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=G">G</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=H">H</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=I">I</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=J">J</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=K">K</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=L">L</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=M">M</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=N">N</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=O">O</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=P">P</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=Q">Q</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=R">R</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=S">S</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=T">T</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=U">U</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=V">V</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=W">W</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=X">X</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=Y">Y</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=Z">Z</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=*">All</a>
    </td>
</tr>
</table>
<?php

if (empty($search_string))
{
    throw_error('No Search String','');
}
else
{
    // perform search
    // check input
    if ($search_string == '')
    {
        $errors = 1;
        echo "<p class='error'>You must enter a search string</p>\n";
    }
    // search for criteria
    if ($errors == 0)
    {
        if($submit_value != 'go')
        {
            // Don't  need to do this again, already done above, us the results of that
            // build SQL
            $sql  = "SELECT * FROM contacts ";
            $search_string_len=strlen($search_string);
            if ($search_string != '*')
            {
                $sql .= "WHERE ";
                if ($search_string_len<=6) $sql .= "id=('$search_string') OR ";
                if ($search_string_len<=2)
                {
                    $sql .= "SUBSTRING(surname,1,$search_string_len)=('$search_string') ";
                }
                else
                {
                    $sql .= "surname LIKE '%$search_string%' OR forenames LIKE '%$search_string%' OR ";
                    $sql .= "CONCAT(forenames,' ',surname) LIKE '%$search_string%'";
                }
            }
            $sql .= " ORDER BY surname ASC";
    
            // execute query
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        }

        if (mysql_num_rows($result) == 0)
            echo "<p align='center'>Sorry, unable to find any contacts matching <em>'$search_string</em>'</p>\n";
        else
        {
            ?>
            <p align='center'>Displaying <?php echo mysql_num_rows($result) ?> contact(s) matching <em>'<?php echo $search_string; ?>'</em></p>
            <table align='center'>
            <tr>
            <th>Contact Name</th>
            <th>Site</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Fax</th>
            <th>Add Incident</th>
            </tr>
            <?php
            $shade = 0;
            while ($results = mysql_fetch_array($result))
            {
                // define class for table row shading
                if ($shade) $class = "shade1";
                else $class = "shade2";
                ?>
                <tr class='<?php echo $class ?>'>
                    <td><a href="contact_details.php?id=<?php echo $results["id"] ?>" ><?php echo $results['surname'].', '.$results['forenames'] ?></a></td>
                    <td><a href="site_details.php?id=<?php echo $results['siteid'] ?>"><?php echo site_name($results['siteid']) ?></a></td>
                    <td><?php echo $results["email"] ?></td>
                    <td><?php if ($results["phone"] == "") { ?><em>None</em><?php } else { echo $results["phone"]; } ?></td>
                    <td><?php if ($results["fax"] == "") { ?><em>None</em><?php } else { echo $results["fax"]; } ?></td>
                    <td><a href="add_incident.php?action=findcontact&amp;contactid=<?php echo $results['id'] ?>">Add Incident</a></td>
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
        // free result
        mysql_free_result($result);
    }
}
include('htmlfooter.inc.php');
?>
