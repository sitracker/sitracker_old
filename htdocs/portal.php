<?php
// portal.php - Simple customer interface
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Authors: Ivan Lucas <ivanlucas[at]users.sourceforge.net, Kieran Hogg <kieran_hogg[at]users.sourceforge.net>
// XHTML 1.0 Transitional valid 12/11/07 - KMH

@include ('set_include_path.inc.php');
$permission = 0; // not required
require ('db_connect.inc.php');
require ('functions.inc.php');
session_name($CONFIG['session_name']);
session_start();
// Load session language if it is set and different to the default language
if (!empty($_SESSION['lang']) AND $_SESSION['lang'] != $CONFIG['default_i18n'])
{
    include("i18n/{$_SESSION['lang']}.inc.php");
}
require ('strings.inc.php');

if ($CONFIG['portal'] == FALSE)
{
    // portal disabled
    $_SESSION['portalauth'] = FALSE;
    $page = $_SERVER['PHP_SELF'];
    if (!empty($_SERVER['QUERY_STRING'])) $page .= '?'.$_SERVER['QUERY_STRING'];
    $page = urlencode($page);
    header("Location: {$CONFIG['application_webpath']}index.php?id=2&page=$page");
    exit;
}

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
    if (function_exists('session_regenerate_id'))
    {
        session_regenerate_id();
    }

    if (!version_compare(phpversion(),"4.3.3",">="))
    {
        setcookie(session_name(), session_id(),ini_get("session.cookie_lifetime"), "/");
    }
}

// External variables
$page = cleanvar($_REQUEST['page']);
$contractid = cleanvar($_REQUEST['contractid']);

$filter = array('page' => $page);

include ('htmlheader.inc.php');

//find contracts
$sql = "SELECT m.*, p.name, ";
$sql .= "(m.incident_quantity - m.incidents_used) AS availableincidents ";
$sql .= "FROM `{$dbSupportContacts}` AS sc, `{$dbMaintenance}` AS m, `{$dbProducts}` AS p ";
$sql .= "WHERE sc.maintenanceid=m.id ";
$sql .= "AND m.product=p.id ";
$sql .= "AND sc.contactid='{$_SESSION['contactid']}' ";
$sql .= "ORDER BY expirydate DESC";

$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
$numcontracts = mysql_num_rows($result);

echo "<div id='menu'>\n";
echo "<ul id='menuList'>\n";
echo "<li><a href='portal.php?page=incidents'>{$strIncidents}</a></li>";
if($numcontracts == 1)
{
    //only one contract
    $contractobj = mysql_fetch_object($result);
    $contractid = $contractobj->id;
    $productid  = $contractobj->product;
    echo "<li><a href='portal.php?page=add&amp;contractid={$contractid}&amp;product={$productid}'>{$strAddIncident}</a></li>";
}
else
{
    echo "<li><a href='portal.php?page=entitlement'>{$strEntitlement}</a></li>";
}
echo "<li><a href='portal.php?page=details'>{$strDetails}</a></li>";
echo "<li><a href='logout.php'>{$strLogout}</a></li>";

echo "</ul>";
echo "</div>";

switch ($page)
{
    //show the user's contracts
    case 'entitlement':
        include ('portal/entitlement.inc.php');
        if ($contract->expirydate == -1)
            echo $strUnlimited;
        else
                    echo ldate($CONFIG['dateformat_date'],$contract->expirydate);
        echo "</td>";
        if ($contract->expirydate > $now)
        {
            echo "<a href='$_SERVER[PHP_SELF]?page=add&amp;contractid={$contract->id}'>{$strAddIncident}</a>";
        }
        echo "</td></tr>\n";
        include 'htmlfooter.inc.php';
        break;
    //update an open incident
    case 'update':
        include ('portal/update.inc.php');
        break;
    //close an open incident
    case 'close':
        include ('portal/close.inc.php');
        break;
    //add a new incident
    case 'add':
        include ('portal/add.inc.php');

        //stop people changing the contractid

        $sql = "SELECT products.*, maintenance.*, maintenance.id AS id, ";
        $sql .= "(maintenance.incident_quantity - maintenance.incidents_used) AS availableincidents ";
        $sql .= "FROM supportcontacts, maintenance, products ";
        $sql .= "WHERE supportcontacts.maintenanceid=maintenance.id ";
        $sql .= "AND maintenance.product=products.id ";
        $sql .= "AND supportcontacts.contactid='{$_SESSION['contactid']}'";
        $sql .= "AND maintenance.id='{$contractid}'";
        $checkcontract = mysql_query($sql);

        //FIXME i18n; right function?
        if(mysql_num_rows($checkcontract) == 0)
        {
        user_error("You do not have access to that contract");
        die();
        }

        break;
    //show user's details
    case 'details':
        include ('portal/details.inc.php');
        break;
    //show specified incident
    case 'showincident':
        include ('portal/showincident.inc.php');
        break;
    //show their open incidents
    case 'incidents':
        //fallthrough
    default:
        include ('portal/incidents.inc.php');
        break;
}

?>