<?php
// main.php - Front page
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
// This Page Is Valid XHTML 1.0 Transitional! 27Oct05

$permission=0; // not required
require('db_connect.inc.php');
require('functions.inc.php');

session_name($CONFIG['session_name']);
session_start();

// Check session is authenticated, if not redirect to login page
if (!isset($_SESSION['portalauth']) OR $_SESSION['portalauth'] == FALSE)
{
    $_SESSION['portalauth'] = FALSE;
    // Invalid user
    $page = $_SERVER['PHP_SELF'];
    if (!empty($_SERVER['QUERY_STRING'])) $page .= '?'.$_SERVER['QUERY_STRING'];
    $page = urlencode($page);
    header("Location: {$CONFIG['application_webpath']}index.php?id=2&page=$page");
    exit;
}
else
{
    // Attempt to prevent session fixation attacks
    session_regenerate_id();
}

// External variables
$page = cleanvar($_REQUEST['page']);

$filter=array('page' => $page);

include('htmlheader.inc.php');

echo "<div id='menu'>\n";
echo "<ul id='menuList'>\n";
echo "<li><a href='logout.php'>Logout</a></li>";
echo "<li><a href='portal.php?page=entitlement'>Entitlement</a></li>";
echo "<li><a href='portal.php?page=incidents'>Incidents</a></li>";
echo "</ul>";
echo "</div>";

switch ($page)
{
    case 'entitlement':
        echo "<h2>Your support entitlement</h2>";
        $sql = "SELECT maintenance.*, products.* FROM supportcontacts, maintenance, products ";
        $sql .= "WHERE supportcontacts.maintenanceid=maintenance.id ";
        $sql .= "AND maintenance.product=products.id ";
        $sql .= "AND supportcontacts.contactid='{$_SESSION['contactid']}'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        $numcontracts = mysql_num_rows($result);
        if ($numcontracts >= 1)
        {
            echo "<table align='center'>";
            echo "<tr><th>Contract ID</th><th>Product</th><th>Expires</th>";
            $shade='shade1';
            while ($contract = mysql_fetch_object($result))
            {
                echo "<tr class='$shade'><td>{$contract->id}</td><td>{$contract->name}</td>";
                echo "<td>".date($CONFIG['dateformat_date'],$contract->expirydate)."</td></tr>\n";
                if ($shade=='shade1') $shade='shade2';
                else $shade='shade1';
            }
            echo "</table>";
        }
        else
        {
            echo "<p class='info'>No contracts</p>";
        }
    break;

    case 'incidents':
        echo "<h2>Your Incidents</h2>";
        $sql = "SELECT * FROM incidents WHERE status!=2 AND contact = '{$_SESSION['contactid']}'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        $numincidents = mysql_num_rows($result);
        if ($numincidents >= 1)
        {
            $shade='shade1';
            echo "<table align='center'>";
            echo "<tr>";

            echo colheader('id', 'ID', $sort, $order, $filter);
            echo "<th>Title</th></tr>";
            while ($incident = mysql_fetch_object($result))
            {
                echo "<tr class='$shade'><td>{$incident->id}</td>";
                echo "<td>Product<br /><a href=''>{$incident->title}</a></td>";
                echo "</tr>";
                if ($shade=='shade1') $shade='shade2';
                else $shade='shade1';
            }
            echo "</table>";
        }
        else echo "<p class='info'>No incidents</p>";
    break;

    case '':
    default:
        echo "<p align='center'>Welcome ".contact_realname($_SESSION['contactid'])."</p>";
}

include('htmlfooter.inc.php');

?>