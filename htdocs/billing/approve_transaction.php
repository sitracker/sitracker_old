<?php
// approve_transaction.php - Page which does the approval of a transaction
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Authors: Paul Heaney <paulheaney[at]users.sourceforge.net>

$lib_path = dirname( __FILE__ ).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR;
$permission = 73; // Approve billable incidents

require_once($lib_path.'db_connect.inc.php');
require_once($lib_path.'functions.inc.php');
include_once ($lib_path . 'billing.inc.php');
// This page requires authentication
require_once($lib_path.'auth.inc.php');

$transactiond = cleanvar($_REQUEST['transactionid']);

include ('../inc/htmlheader.inc.php');

$sql = "SELECT * FROM `{$GLOBALS['dbTransactions']}` WHERE transactionid = {$transactiond}";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("Error getting transaction ".mysql_error());

if (mysql_num_rows($result) > 0)
{
	$obj = mysql_fetch_object($result);
    if ($obj->transactionstatus == AWAITINGAPPROVAL)
    {
    	// function update_contract_balance($contractid, $description, $amount, $serviceid='', $transactionid='', $totalunits=0, $totalbillableunits=0, $totalrefunds=0)
        $r = update_contract_balance('', '', $obj->amount, $obj->serviceid, $obj->transactionid);

        if ($r) html_redirect("../billable_incidents.php", TRUE, "Transaction approved");
        else html_redirect("../billable_incidents.php", FALSE, "Failed to approve transaction ID {$transactiond}");
    }
    else
    {
    	html_redirect("../billable_incidents.php", FALSE, "Transaction{$transactiond} is not awaiting approval");
    }
}
else
{
	html_redirect("../billable_incidents.php", FALSE, "No transaction found with ID {$transactiond}");
}

include ('../inc/htmlfooter.inc.php');

?>
