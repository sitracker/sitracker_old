<?php
// incident.inc.php - functions relating to incidents
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.

require_once('base.inc.php');

/**
 * Creates a new incident
 * @param string $title The title of the incident
 * @param int $contact The ID of the incident contact
 * @param int $servicelevel The ID of the servicelevel to log the incident under
 * @param int $contract The ID of the contract to log the incident under
 * @param int $product The ID of the product the incident refers to
 * @param int $skill The ID of the skill the incident refers to
 * @param int $priority (Optional) Priority of the incident (Default: 1 = Low)
 * @param int $owner (Optional) Owner of the incident (Default: 0 = SiT)
 * @param int $status (Optional) Incident status (Default: 1 = Active)
 * @param string $productversion (Optional) Product version field
 * @param string $productservicepacks (Optional) Product service packs field
 * @param int $opened (Optional) Timestamp when incident was opened (Default: now)
 * @param int $lastupdated (Optional) Timestamp when incident was updated (Default: now)
 * @return int|bool Returns FALSE on failure, an incident ID on success
 * @author Kieran Hogg
 */
function create_incident($title, $contact, $servicelevel, $contract, $product,
                         $software, $priority = 1, $owner = 0, $status = 1, 
                         $productversion = '', $productservicepacks = '', 
                         $opened = '', $lastupdated = '')
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
