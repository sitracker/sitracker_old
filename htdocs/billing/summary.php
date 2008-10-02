<?php
// billing/summary.php - Summary page - to show
// Summary of all sites and their balances and expiry date.(sf 1931092)
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author:  Paul Heaney Paul Heaney <paulheaney[at]users.sourceforge.net>

@include ('set_include_path.inc.php');
$permission =  37; // Run Reports  // FIXME might need its own

require_once('db_connect.inc.php');
require_once('functions.inc.php');
// This page requires authentication
require_once('auth.inc.php');

$display = cleanvar($_REQUEST['display']);
$showfoc = cleanvar($_REQUEST['foc']);
$focaszero = cleanvar($_REQUEST['focaszero']);

if (empty($display)) $display = 'html';

$sql = "SELECT DISTINCT(CONCAT(m.id,sl.id)), m.site, m.product, s.* ";
$sql .= "FROM `{$dbMaintenance}` AS m, `{$dbServiceLevels}` AS sl, `{$dbService}` AS s, `{$dbSites}` AS site ";
$sql .= "WHERE m.servicelevelid = sl.id AND sl.timed = 'yes' AND m.id = s.contractid AND m.site = site.id ";

if (empty($showfoc) OR $showfoc != 'show')
{
	$sql .= "AND s.foc = 'no' ";
}

$sitestr = '';

$csv_currency = html_entity_decode($CONFIG['currency_symbol'], ENT_NOQUOTES, "ISO-8859-15"); // Note using -15 as -1 doesnt support euro

if (!empty($sites))
{
    foreach ($sites AS $s)
    {
        if (empty($sitestr)) $sitestr .= "m.site = {$s} ";
        else $sitestr .= "OR m.site = {$s} ";
    }

    $sql .= "AND {$sitestr} ";
}

$sql .= "ORDER BY site.name, s.enddate";

$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

if (mysql_numrows($result) > 0)
{
    if ($display == 'html')
    {
        $str .= "<table align='center' class='vertical'><tr><th>{$strSiteName}</th><th>{$strProduct}</th>";
        $str .= "<th>{$strStartDate}</th><th>{$strEndDate}</th>";
        $str .= "<th>{$strCreditAmount}</th><th>{$strBalance}</th>";
        $str .= "<th>{$strUnitRate}</th><th>Units remaining @1x</th></tr>";
    }
    elseif ($display == 'csv')
    {
        $str .= "\"{$strSiteName}\",\"{$strProduct}\",\"{$strStartDate}\",\"{$strEndDate}\",\"{$strCreditAmount}\",\"{$strBalance}\",\"{$strUnitRate}\",\"Units remaining @1 x\"\n"; // FIXME i18n
    }

    $lastsite = '';
    $lastproduct = '';

    $shade = 'shade1';
    while ($obj = mysql_fetch_object($result))
    {
        if ($obj->unitrate != 0) $unitsat1times = round(($obj->balance/$obj->unitrate), 2);
        else $unitsat1times = 0;
        
        if ($obj->foc == 'yes' AND !empty($focaszero))
        {
			$obj->creditamount = 0;
			$obj->balance = 0;
        }
        
        $totalcredit += $obj->creditamount;
        $totalbalance += $obj->balance;
        $remainingunits += $unitsat1times;

        if ($display == 'html')
        {
            if ($obj->site != $lastsite OR $obj->product != $lastproduct)
            {
                if ($shade == 'shade1') $shade = 'shade2';
                else $shade = 'shade1';
            }

            $str .= "<tr class='{$shade}'>";
            if ($obj->site != $lastsite)
            {
                $str .= "<td>".site_name($obj->site)."</td>";
                $str .= "<td>".product_name($obj->product)."</td>";
            }
            else
            {
                $str .= "<td></td>";
                if ($obj->product != $lastproduct)
                {
                    $str .= "<td>".product_name($obj->product)."</td>";
                }
                else
                {
                    $str .= "<td></td>";
                }
            }

            $str .= "<td>{$obj->startdate}</td><td>{$obj->enddate}</td>";
            $str .= "<td>{$CONFIG['currency_symbol']}".number_format($obj->creditamount,2)."</td>";
            $str .= "<td>{$CONFIG['currency_symbol']}".number_format($obj->balance,2)."</td>";
            $str .= "<td>{$CONFIG['currency_symbol']}{$obj->unitrate}</td>";
            $str .= "<td>{$unitsat1times}</td></tr>";

            $lastsite = $obj->site;
            $lastproduct = $obj->product;
        }
        elseif ($display == 'csv')
        {
            if ($obj->site != $lastsite)
            {
                $str .= "\"".site_name($obj->site)."\",";
                $str .= "\"".product_name($obj->product)."\",";
            }
            else
            {
                $str .= ",";
                if ($obj->product != $lastproduct)
                {
                    $str .= product_name($obj->product).",";
                }
                else
                {
                    $str .= ",";
                }
            }

            $str .= "\"{$obj->startdate}\",\"{$obj->enddate}\",";
            $str .= "\"{$csv_currency}{$obj->creditamount}\",\"{$csv_currency}{$obj->balance}\",";
            $str .= "\"{$csv_currency}{$obj->unitrate}\",";
            $str .= "\"{$unitsat1times}\"\n";
        }
    }

    if ($display == 'html')
    {
        $str .= "<tr><td colspan='4' align='right'>{$strTOTALS}</td><td>{$CONFIG['currency_symbol']}".number_format($totalcredit, 2)."</td>";
        $str .= "<td>{$CONFIG['currency_symbol']}".number_format($totalbalance, 2)."</td><td></td><td>{$remainingunits}</td></tr>";
        $str .= "</table>";
        $str .= "<p align='center'><a href='{$_SERVER['HTTP_REFERER']}'>{$strReturnToPreviousPage}</a></p>";
    }
    elseif ($display == 'csv')
    {
        $str .= ",,,\"{$strTOTALS}\",\"{$csv_currency}{$totalcredit}\",";
        $str .= "\"{$csv_currency}{$totalbalance}\",,\"{$remainingunits}\"\n";
    }
}
else
{
    $str = $strNone;
}

if ($display == 'html')
{
    include ('htmlheader.inc.php');
    echo "<h2>{$strBillingSummary}</h2>";
    echo $str;
    include ('htmlfooter.inc.php');
}
elseif ($display == 'csv')
{
    header("Content-type: text/csv\r\n");
    header("Content-disposition-type: attachment\r\n");
    header("Content-disposition: filename=billing_summary.csv");
    echo $str;
}

?>