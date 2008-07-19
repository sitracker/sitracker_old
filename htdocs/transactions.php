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
// This page requires authentication
require_once('auth.inc.php');

$title = $strTransactions;

// External variables
$serviceid = cleanvar($_REQUEST['serviceid']);
$startdateorig = cleanvar($_REQUEST['startdate']);
$enddateorig = cleanvar($_REQUEST['enddate']);

$startdate = strtotime(cleanvar($_REQUEST['startdate']));
$enddate = strtotime(cleanvar($_REQUEST['enddate']));
$mode = cleanvar($_REQUEST['mode']); // FIXME this parameter is never used
$site = cleanvar($_REQUEST['site']);
$sites = $_REQUEST['sites'];
$display = cleanvar($_REQUEST['display']);
if (empty($display)) $display = 'html';

$sitebreakdown = $_REQUEST['sitebreakdown'];

if (!empty($enddate))
{
    $a = explode("-", $enddateorig);

    $m = mktime(0, 0, 0, $a[1], $a[2]+1, $a[0]);
    $enddate = date("Y-m-d", $m);
}


$sql = "SELECT DISTINCT t.*, m.site FROM `{$dbTransactions}` AS t, `{$dbService}` AS p, `{$dbMaintenance}` AS m, `{$dbServiceLevels}` AS sl, `{$dbSites}` AS s ";
$sql .= "WHERE t.serviceid = p.serviceid AND p.contractid = m.id "; // AND t.date <= '{$enddateorig}' ";
$sql .= "AND m.servicelevelid = sl.id AND sl.timed = 'yes' AND m.site = s.id ";
//// $sql .= "AND t.date > p.lastbilled AND m.site = {$objsite->site} ";
if ($serviceid > 0) $sql .= "AND t.serviceid = {$serviceid} ";
if (!empty($startdate)) $sql .= "AND t.date >= '{$startdateorig}' ";
if (!empty($enddate)) $sql .= "AND t.date <= '{$enddate}' ";

if (!empty($sites))
{
    $sitestr = '';

    foreach ($sites AS $s)
    {
        if (empty($sitestr)) $sitestr .= "m.site = {$s} ";
        else $sitestr .= "OR m.site = {$s} ";
    }

    $sql .= "AND {$sitestr} ";
}

if (!empty($site)) $sql .= "AND m.site = {$site} ";

$sql .= "ORDER BY s.name ";

$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);

if (mysql_num_rows($result) > 0)
{
    $shade = 'shade1';

    $total = 0;
    $totalcredit = 0;
    $totaldebit = 0;

    while ($transaction = mysql_fetch_object($result))
    {
        if ($display == 'html')
        {
            $str = "<tr class='$shade'>";
            $str .= "<td>{$transaction->date}</td>";
            $str .= "<td>{$transaction->transactionid}</td>";
            $str .= "<td>{$transaction->serviceid}</td>";
            $str .= "<td>".site_name($transaction->site)."</td>";
            $str .= "<td>{$transaction->description}</td>";
        }
        elseif ($display == 'csv')
        {
            $str = "\"{$transaction->date}\",";
            $str .= "\"{$transaction->transactionid}\",";
            $str .= "\"{$transaction->serviceid}\",\"";
            $str .= site_name($transaction->site)."\",";
            $str .= "\"{$transaction->description}\",";
        }

        $total += $transaction->amount;
        if ($transaction->amount < 0)
        {
            $totaldebit += $transaction->amount;
            if ($display == 'html')
            {
                $str .= "<td></td><td>{$CONFIG['currency_symbol']}".number_format($transaction->amount, 2)."</td>";
            }
            elseif ($display == 'csv')
            {
                $str .= ",\"{$CONFIG['currency_symbol']}".number_format($transaction->amount, 2)."\",";
            }
        }
        else
        {
            $totalcredit += $transaction->amount;
            if ($display == 'html')
            {
                $str .= "<td>{$CONFIG['currency_symbol']}".number_format($transaction->amount, 2)."</td><td></td>";
            }
            elseif ($display == 'csv')
            {
                $str .= "\"{$CONFIG['currency_symbol']}".number_format($transaction->amount, 2)."\",,";
            }
        }

        if ($display == 'html') $str .= "</tr>";
        elseif ($display == 'csv') $str .= "\n";

        if ($sitebreakdown == 'on')
        {
            $table[$transaction->site]['site'] = site_name($transaction->site);
            $table[$transaction->site]['str'] .= $str;
            if ($transaction->amount < 0)
            {
                $table[$transaction->site]['debit'] += $transaction->amount;
            }
            else
            {
                $table[$transaction->site]['credit'] += $transaction->amount;
            }
        }
        else
        {
            $table .= $str;
        }
    }

    if ($sitebreakdown == 'on')
    {
        foreach ($table AS $e)
        {
            if ($display == 'html')
            {
                $text .= "<h3>{$e['site']}</h3>";
                $text .= "<table align='center'  width='60%'>";
                //echo "<tr><th colspan='7'>{$e['site']}</th></tr>";
                $text .= "<tr><th>{$strDate}</th><th>{$strID}</th><th>{$strServiceID}</th>";
                $text .= "<th>{$strSite}</th><th>{$strDescription}</th><th>{$strCredit}</th><th>{$strDebit}</th></tr>";
                $text .= $e['str'];
                $text .= "<tr><td colspan='5' align='right'>{$strTotal}</td>";
                $text .= "<td>{$CONFIG['currency_symbol']}".number_format($e['credit'], 2)."</td>";
                $text .= "<td>{$CONFIG['currency_symbol']}".number_format($e['debit'], 2)."</td></tr>";
                $text .= "</table>";
            }
            elseif ($display == 'csv')
            {
                $text .= "\"{$e['site']}\"\n\n";
                $text .= "\"{$strDate}\",\"{$strID}\",\"{$strServiceID}\",";
                $text .= "\"{$strSite}\",\"{$strDescription}\",\"{$strCredit}\",\"{$strDebit}\"\n";
                $text .= $e['str'];
                $text .= ",,,,{$strTotal},";
                $text .= "\"{$CONFIG['currency_symbol']}".number_format($e['credit'], 2)."\",\"";
                $text .="{$CONFIG['currency_symbol']}".number_format($e['debit'], 2)."\"\n";
            }
        }
    }
    else
    {
        if ($display == 'html')
        {
            $text .= "<table align='center'>";
            $text .= "<tr><th>{$strDate}</th><th>{$strID}</th><th>{$strServiceID}</th>";
            $text .= "<th>{$strSite}</th>";
            $text .= "<th>{$strDescription}</th><th>{$strCredit}</th><th>{$strDebit}</th></tr>";
            $text .= $table;
            $text .= "<tr><td colspan='5' align='right'>{$strTOTALS}</td>";
            $text .= "<td>{$CONFIG['currency_symbol']}".number_format($totalcredit, 2)."</td>";
            $text .= "<td>{$CONFIG['currency_symbol']}".number_format($totaldebit, 2)."</td></tr>";
            $text .= "</table>";
        }
        elseif ($display == 'csv')
        {
            $text .= "\"{$strDate}\",\"{$strID}\",\"{$strServiceID}\",";
            $text .= "\"{$strSite}\",";
            $text .= "\"{$strDescription}\",\"{$strCredit}\",\"{$strDebit}\"\n";
            $text .= $table;
            $text .= ",,,,{$strTOTALS},";
            $text .= "\"{$CONFIG['currency_symbol']}".number_format($totalcredit, 2)."\",\"";
            $text .= "{$CONFIG['currency_symbol']}".number_format($totaldebit, 2)."\"\n";
        }
    }


    if ($shade == 'shade1') $shade = 'shade2';
    else $shade = 'shade1';
}
else
{
    if ($display == 'html')
    {
        $text = "<p align='center'>{$strNoTransactionsMatchYourSearch}</p>";
    }
    elseif ($display == 'csv')
    {
        $text = $strNoTransactionsMatchYourSearch."\n";
    }
}

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
