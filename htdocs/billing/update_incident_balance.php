<?php
// billable_incidents.php - Report for billing incidents
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author:  Paul Heaney Paul Heaney <paulheaney[at]users.sourceforge.net>

@include ('../set_include_path.inc.php');
$permission = 80; //Set -ve balances

require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

$mode = cleanvar($_REQUEST['mode']);
$incidentid = cleanvar($_REQUEST['incidentid']);

if (empty($mode))
{
    include ('htmlheader.inc.php');

    echo "<h2>".sprintf($strUpdateIncidentXsBalance, $incidentid)."</h2>";

    echo "<form action='{$_SERVER['PHP_SELF']}' method='post' id='modifyincidentbalance'>";

    echo "<table class='vertical'><tr><td>{$strAmount0}<br />{$strForRefundsThisShouldBeNegative}</td><td>";
    echo "<input type='text' name='amount' id='amount' size='10' /> {$strMinutes}</td></tr>";

    echo "<tr><td>{$strDescription}</td><td>";
    echo "<textarea cols='40' name='description' rows='5'></textarea>";
    echo "</tr>";

    echo "</table>";

    echo "<input type='hidden' id='incidentid' name='incidentid' value='{$incidentid}' />";
    echo "<input type='hidden' id='mode' name='mode' value='update' />";

    echo "<p align='center'><input type='submit' name='Sumbit' value='{$strUpdate}'  /></p>";

    echo "</form>";

    include ('htmlfooter.inc.php');
}
elseif ($mode == 'update')
{
    $amount = cleanvar($_REQUEST['amount']);
    $description = cleanvar($_REQUEST['description']);

    $sql = "SELECT closed, status, owner FROM `{$dbIncidents}` WHERE id = {$incidentid}";

    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);

    if (mysql_num_rows($result) > 0)
    {
        $obj = mysql_fetch_object($result);

        $description = "[b]Amount[/b]: {$amount} minutes\n\n{$description}";

        $amount *= 60; // to seconds
        $sqlInsert = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, currentowner, currentstatus, bodytext, timestamp, duration) VALUES ";
        $sqlInsert .= "('{$incidentid}', '{$sit[2]}', 'editing', '{$obj->owner}', '{$obj->status}', '{$description}', '{$now}', '{$amount}')";
        $resultInsert = mysql_query($sqlInsert);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        if (mysql_affected_rows() > 0) html_redirect('../billable_incidents.php', TRUE, $strUpdateSuccessful);
        else  html_redirect('../billable_incidents.php', FALSE, $strUpdateFailed);
    }
    else
    {
        html_redirect('../billable_incidents.php', FALSE, "Failed to find date incident closed");
    }
}

?>
