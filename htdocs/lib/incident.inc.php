<?php
// incident.inc.php - functions relating to incidents
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.

require_once('base.inc.php');

function create_incident($title, $contact, $servicelevel, $contract,
                             $product, $software, $priority = 1, $owner = 0,
                             $status = 1, $productversion = '',
                             $productservicepacks = '', $opened = '',
                             $lastupdated = '')
{
    global $now;
    if (empty($opened))
    {
        $opened = $now;
    }

    if (empty($lastupdated))
    {
        $lastupdated = $now;
    }
    $sql  = "INSERT INTO `{$dbIncidents}` (title, owner, contact, priority, ";
    $sql .= "servicelevel, status, maintenanceid, product, softwareid, ";
    $sql .= "productversion, productservicepacks, opened, lastupdated) ";
    $sql .= "VALUES ('{$title}', '{$owner}', '{$contact}', '{$priority}', ";
    $sql .= "'{$servicelevel}', '{$status}', '{$contract}', ";
    $sql .= "'{$product}', '{$software}', '{$productversion}', ";
    $sql .= "'{$productservicepacks}', '{$opened}', '{$lastupdated}')";
    $result = mysql_query($sql);
    if (mysql_error())
    {
        trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        return FALSE;
    }
    else
    {
        return mysql_insert_id();
    }
}

?>
