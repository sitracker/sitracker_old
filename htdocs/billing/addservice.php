<?php
// services/add.php - Adds a new service record
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

@include ('../set_include_path.inc.php');
$permission = 21; // FIXME need a permission for add service

require ('db_connect.inc.php');
require ('functions.inc.php');
// This page requires authentication
require ('auth.inc.php');

// External variables
$contractid = mysql_real_escape_string($_REQUEST['contractid']);
$submit = $_REQUEST['submit'];


// Contract ID must not be blank
if (empty($contractid))
{
    html_redirect('../main.php', FALSE);
    exit;
}

// Find the latest end date so we can suggest a start date
$sql = "SELECT enddate FROM `{$dbService}` WHERE contractid = {$contractid} ORDER BY enddate DESC LIMIT 1";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);

if (mysql_num_rows($result) > 0)
{
    list($prev_enddate) = mysql_fetch_row($result);
    $suggested_startdate = mysql2date($prev_enddate) + 86400; // the next day
}
else
{
    $suggested_startdate = $now; // Today
}

if (empty($submit) OR !empty($_SESSION['formerrors']['add_service']))
{
    include ('htmlheader.inc.php');
    echo show_form_errors('add_service');
    clear_form_errors('add_service');
    echo "<h2> ";
    echo "{$strNewService}</h2>";

    echo "<h5>".sprintf($strMandatoryMarked, "<sup class='red'>*</sup>")."</h5>";
    echo "<form id='serviceform' name='serviceform' action='{$_SERVER['PHP_SELF']}' method='post' onsubmit='return confirm_submit(\"{$strAreYouSureMakeTheseChanges}\");'>";
    echo "<table align='center' class='vertical'>";

    echo "<tr><th>{$strStartDate}</th>";
    echo "<td><input type='text' name='startdate' id='startdate' size='10'";
    if ($_SESSION['formdata']['add_service']['startdate'] != '')
    {
        echo "value='{$_SESSION['formdata']['add_service']['startdate']}'";
    }
    else
    {
        echo "value='".date('Y-m-d', $suggested_startdate)."'";
    }
    echo "/> ";
    echo date_picker('serviceform.startdate');
    echo "</td></tr>";

    echo "<tr><th>{$strEndDate}<sup class='red'>*</sup></th>";
    echo "<td><input type='text' name='enddate' id='enddate' size='10'";
    if ($_SESSION['formdata']['add_service']['enddate'] != '')
    {
        echo "value='{$_SESSION['formdata']['add_service']['enddate']}'";
    }
    echo "/> ";
    echo date_picker('serviceform.enddate');
    echo " <input type='checkbox' name='noexpiry' ";
    if ($_SESSION['formdata']['add_contract']['noexpiry'] == "on")
    {
        echo "checked='checked' ";
    }
    echo "onclick=\"$('enddate').value='';\" /> {$strUnlimited}</td></tr>\n";

    echo "<tr><th>{$strNotes}</th><td>";
    echo "<textarea rows='5' cols='20' name='notes'></textarea></td></tr>";

    echo "<tr><th>{$strBilling}</th>";
    echo "<td>";
    echo "<label>";
    echo "<input type='radio' name='billtype' value='billperunit' onchange=\"addservice_showbilling();\" checked /> ";
    echo "{$strPerUnit}</label>";
    echo "<label>";
    echo "<input type='radio' name='billtype' value='billperincident' onchange=\"addservice_showbilling();\" /> ";
    echo "{$strPerIncident}</label>";
    echo "</td></tr>\n";

    echo "<tbody id='billingsection'>"; //FIXME not XHTML

    echo "<tr><th>{$strCreditAmount}</th>";
    echo "<td>{$CONFIG['currency_symbol']} <input type='text' name='amount' size='5' />";
    echo "</td></tr>";

    echo "<tr id='unitratesection'><th>{$strUnitRate}</th>";
    echo "<td>{$CONFIG['currency_symbol']} <input type='text' name='unitrate' size='5' />";
    echo "</td></tr>";

    echo "<tr id='incidentratesection' style='display:none'><th>{$strIncidentRate}</th>";
    echo "<td>{$CONFIG['currency_symbol']} <input type='text' name='incidentrate' size='5' />";
    echo "</td></tr>";

    echo "</tbody>"; //FIXME not XHTML

//  Not sure how applicable daily rate is, INL 4Apr08
//     echo "<tr><th>{$strDailyRate}</th>";
//     echo "<td>{$CONFIG['currency_symbol']} <input type='text' name='dailyrate' size='5' />";
//     echo "</td></tr>";

    echo "</table>\n\n";
    echo "<input type='hidden' name='contractid' value='{$contractid}' />";
    echo "<p><input name='submit' type='submit' value=\"{$strAdd}\" /></p>";
    echo "</form>\n";

    echo "<p align='center'><a href='../contract_details.php?id={$contractid}'>{$strReturnWithoutSaving}</a></p>";

    //cleanup form vars
    clear_form_data('add_service');
    include ('htmlfooter.inc.php');
}
else
{
    // External variables
    $contractid = cleanvar($_POST['contractid']);
    $startdate = strtotime($_REQUEST['startdate']);
    if ($startdate > 0) $startdate = date('Y-m-d',$startdate);
    else $startdate = date('Y-m-d',$now);
    $enddate = strtotime($_REQUEST['enddate']);
    if ($enddate > 0) $enddate = date('Y-m-d',$enddate);
    else $enddate = date('Y-m-d',$now);
    $amount =  cleanvar($_POST['amount']);
    if ($amount == '') $amount = 0;
    $unitrate =  cleanvar($_POST['unitrate']);
    if ($unitrate == '') $unitrate = 0;
    $incidentrate =  cleanvar($_POST['incidentrate']);
    if ($incidentrate == '') $incidentrate = 0;

    $billtype = cleanvar($_REQUEST['billtype']);
    $notes = cleanvar($_REQUEST['notes']);
    
    if ($billtype == 'billperunit') $incidentrate = 0;
    elseif ($billtype == 'billperincident') $unitrate = 0;

    $sql = "INSERT INTO `{$dbService}` (contractid, startdate, enddate, creditamount, unitrate, incidentrate, notes) ";
    $sql .= "VALUES ('{$contractid}', '{$startdate}', '{$enddate}', '{$amount}', '{$unitrate}', '{$incidentrate}', '{$notes}')";

    mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    if (mysql_affected_rows() < 1) trigger_error("Insert failed",E_USER_ERROR);

    $serviceid = mysql_insert_id();

    if ($amount != 0)
    {
        update_contract_balance($contractid, "New service", $amount, $serviceid);
    }

    $sql = "SELECT expirydate FROM `{$dbMaintenance}` WHERE id = {$contractid}";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

    if (mysql_num_rows($result) > 0)
    {
        $obj = mysql_fetch_object($result);
        if ($obj->expirydate < strtotime($enddate))
        {
            $update = "UPDATE `$dbMaintenance` ";
            $update .= "SET expirydate = '".strtotime($enddate)."' ";
            $update .= "WHERE id = {$contractid}";
            mysql_query($update);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
            if (mysql_affected_rows() < 1) trigger_error("Expiry of contract update failed",E_USER_ERROR);
        }
    }

    html_redirect("../contract_details.php?id={$contractid}", TRUE);
}
?>