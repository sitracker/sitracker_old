<?php
// portal/contracts.inc.php - Shows contact details
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Kieran Hogg <kieran_hogg[at]users.sourceforge.net

include 'portalheader.inc.php';
//TODO check we're allowed
$id = intval($_GET['id']);
$contactid = intval($_GET['contactid']);
$action = cleanvar($_GET['action']);
if ($id != 0 AND $contactid != 0 AND $action == 'remove')
{
    $sql = "DELETE FROM `{$dbSupportContacts}`
            WHERE maintenanceid='{$id}'
            AND contactid='{$contactid}'
            LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    else
    {
        html_redirect($_SERVER['PHP_SELF']."?id={$id}");
        exit;
    }
}
elseif ($id != 0 AND $action == 'add' AND intval($_POST['contactid'] != 0))
{
    $contactid = intval($_POST['contactid']);
    $sql = "INSERT INTO `{$dbSupportContacts}`
            (maintenanceid, contactid)
            VALUES('{$id}', '{$contactid}')";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    else
    {
        html_redirect($_SERVER['PHP_SELF']."?id={$id}");
        exit;
    }
}


echo contract_details($id, 'external');

include 'htmlfooter.inc.php'

?>
