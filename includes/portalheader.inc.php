<?php
// portalheader.inc.php - Header for inclusion in the portal
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
    session_regenerate();

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
$sql .= "AND expirydate > (UNIX_TIMESTAMP(NOW()) - 15778463) ";
$sql .= "ORDER BY expirydate DESC";

$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
$numcontracts = mysql_num_rows($result);

echo "<div id='menu'>\n";
echo "<ul id='menuList'>\n";
echo "<li><a href='index.php'>{$strIncidents}</a></li>";
if($numcontracts == 1)
{
    //only one contract
    $contractobj = mysql_fetch_object($result);
    $contractid = $contractobj->id;
    echo "<li><a href='add.php?contractid={$contractid}'>{$strAddIncident}</a></li>";
}
else
{
    echo "<li><a href='entitlement.php'>{$strEntitlement}</a></li>";
}

if($CONFIG['kb_enabled'] AND $CONFIG['portal_kb_enabled'])
{
    echo "<li><a href='kb.php'>{$strKnowledgeBase}</a></li>";
}

echo "<li><a href='details.php'>{$strDetails}</a></li>";
if($_SESSION['usertype'] == 'admin')
    echo "<li><a href='admin.php'>{$strAdmin}</a></li>";
echo "<li><a href='../logout.php'>{$strLogout}</a></li>";

echo "</ul>";

echo "<div align='right'>";
echo contact_realname($_SESSION['contactid']);
echo ", ".contact_site($_SESSION['siteid']);
echo "</div>";
echo "</div>";



?>