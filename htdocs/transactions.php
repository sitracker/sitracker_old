<?php
// transactions.php - List of transactions
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Authors: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
//          Paul Heaney <paulheaney[at]users.sourceforge.net>

@include('set_include_path.inc.php');
$permission = 76; // View Transactions

require_once('db_connect.inc.php');
require_once('functions.inc.php');
include_once ($lib_path . 'billing.inc.php');
// This page requires authentication
require_once('auth.inc.php');

$title = $strTransactions;

// External variables
$serviceid = cleanvar($_REQUEST['serviceid']);
$startdate = cleanvar($_REQUEST['startdate']);
$enddate = cleanvar($_REQUEST['enddate']);

$mode = cleanvar($_REQUEST['mode']); // FIXME this parameter is never used
$site = cleanvar($_REQUEST['site']);
$sites = $_REQUEST['sites'];
$display = cleanvar($_REQUEST['display']);
if (empty($display)) $display = 'html';
$showfoc = cleanvar($_REQUEST['foc']);
$focaszero = cleanvar($_REQUEST['focaszero']);

if (empty($showfoc) OR $showfoc != 'show') $showfoc = FALSE;
else $showfoc = TRUE;

if (!empty($site) AND empty($sites)) $sites = array($site);

$sitebreakdown = $_REQUEST['sitebreakdown'];

if (!empty($enddate))
{
    $a = explode("-", $enddateorig);

    $m = mktime(0, 0, 0, $a[1], $a[2]+1, $a[0]);
    $enddate = date("Y-m-d", $m);
}

if ($sitebreakdown == 'on') $sitebreakdown = TRUE;
else $sitebreakdown = FALSE;

$text = transactions_report($serviceid, $startdate, $enddate, $sites, $display, $sitebreakdown, $showfoc, $focaszero);

if ($display == 'html')
{
    include ('htmlheader.inc.php');

    echo "<h3>{$strTransactions}</h3>";

    echo $text;
    echo "<p align='center'><a href='{$_SERVER['HTTP_REFERER']}'>{$strReturnToPreviousPage}</a></p>";

    include('htmlfooter.inc.php');
}
elseif ($display == 'csv')
{
    header("Content-type: text/csv\r\n");
    header("Content-disposition-type: attachment\r\n");
    header("Content-disposition: filename=transactions.csv");
    echo $text;
}

?>