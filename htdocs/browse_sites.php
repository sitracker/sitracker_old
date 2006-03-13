<?php
// browse_sites.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//


// Author: Ivan Lucas
// This Page Is Valid XHTML 1.0 Transitional! 27Oct05

$permission=11; // View Sites
require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

// Valid user, check permissions
if (!user_permission($sit[2],$permission))
{
    header("Location: noaccess.php?id=$permission");
    exit;
}

// External variables
$search_string = cleanvar($_REQUEST['search_string']);

include('htmlheader.inc.php');
?>
<table summary="alphamenu" align="center">
<tr>
<td align="center">
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
    <p>Browse sites: <input type="text" name="search_string" /><input type="submit" value="go" /></p>
    </form>
</td>
</tr>
<tr>
<td valign="middle">
    <a href="add_site.php">Add Site</a> |
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
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=0">#</a> |
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=*">All</a>
    </td>
</tr>
</table>

<script type="text/javascript">
    function site_details_window(siteid)
    {
        URL = "site_details.php?action=edit&amp;site=" + siteid;
        window.open(URL, "site_details_window", "toolbar=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=450,height=240");
    }
</script>
<?php
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
    $sql  = "SELECT id, name, department FROM sites ";

    if ($search_string != '*')
    {
        $sql .= "WHERE ";
        if (strlen($search_string)==1)
        {
            if ($search_string=='0') $sql .= "(SUBSTRING(name,1,1)=('0')
                                            OR SUBSTRING(name,1,1)=('1')
                                            OR SUBSTRING(name,1,1)=('2')
                                            OR SUBSTRING(name,1,1)=('3')
                                            OR SUBSTRING(name,1,1)=('4')
                                            OR SUBSTRING(name,1,1)=('5')
                                            OR SUBSTRING(name,1,1)=('6')
                                            OR SUBSTRING(name,1,1)=('7')
                                            OR SUBSTRING(name,1,1)=('8')
                                            OR SUBSTRING(name,1,1)=('9'))";
            else $sql .= "SUBSTRING(name,1,1)=('$search_string') ";
        }
        else
        {
            $sql .= "name LIKE '%$search_string%' ";
        }
    }
    $sql .= " ORDER BY name ASC";

    // execute query
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    if (mysql_num_rows($result) == 0)
    {
        echo "<p align='center'>Sorry, unable to find any sites matching <strong>'$search_string</strong>'</p>\n";
    }
    else
    {
        ?>
        <p align='center'>Displaying <?php echo mysql_num_rows($result) ?> site(s) matching <strong>'<?php echo $search_string; ?>'</strong></p>

        <table align='center'>
        <tr>
            <th>ID</th>
            <th>Site Name</th>
            <th>Department</th>
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
                <td align='center'><?php echo $results['id'] ?></td>
                <td><a href="site_details.php?id=<?php echo $results['id']; ?>&amp;action=show"><?php echo htmlspecialchars($results['name']) ?></a></td>
                <td><?php echo nl2br(htmlspecialchars($results["department"])); ?></td>
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

include('htmlfooter.inc.php');
include('db_disconnect.inc.php');
?>