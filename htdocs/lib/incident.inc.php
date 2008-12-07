<?php
// incident.inc.php - functions relating to incidents
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.

require_once('base.inc.php');
require_once('contract.inc.php');
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
    global $now, $dbIncidents;
    
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
        $incident = mysql_insert_id();
        return $incident;
    }
}


/**
 * Creates an incident based on an 'tempincoming' table entry
 * @author Kieran Hogg
 * @param int $incomingid the ID of the tempincoming entry
 * @return int|bool returns either the ID of the contract or FALSE if none
 */
function create_incident_from_incoming($incomingid)
{
    global $dbTempIncoming, $dbMaintenance, $dbServiceLevels, 
        $dbSoftwareProducts;
    $rtn = TRUE;
    
    $incomingid = intval($incomingid);
    $sql = "SELECT * FROM `{$dbTempIncoming}` ";
    $sql .= "WHERE id = '{$incomingid}'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    
    $row = mysql_fetch_object($result);
    $contact = $row->contactid;
    $contract = guess_contract_id($contact);
    if (!$contract)
    {
        // we have no contract to log against, update stays in incoming
        return TRUE;
    }
    $subject = $row->subject;
    $update = $row->updateid;
    
    $sql = "SELECT servicelevelid, tag, product, softwareid ";
    $sql .= "FROM `{$dbMaintenance}` AS m, `{$dbServiceLevels}` AS s, ";
    $sql .= "`{$dbSoftwareProducts}` AS sp ";
    $sql .= "WHERE m.id = '{$contract}' ";
    $sql .= "AND m.servicelevelid = s.id ";
    $sql .= "AND m.product = sp.productid LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_error())
    {
        trigger_error(mysql_error(),E_USER_ERROR);
        $rtn = FALSE;
    }    
    
    $row = mysql_fetch_object($result);
    $sla = $row->tag;
    $product = $row->product;
    $software = $row->softwareid;
    $incident = create_incident($subject, $contact, $row->tag, $contract, 
                                $product, $software);
    
    if(!move_update_to_incident($update, $incident))
    {
        $rtn = FALSE;
    }
    
    return $rtn;
}


/**
 * Move an update to an incident
 * @author Kieran Hogg
 * @param int $update the ID of the update
 * @param int $incident the ID of the incident
 * @return bool returns TRUE on success, FALSE on failure
 */
function move_update_to_incident($update, $incident)
{
    global $dbUpdates;
    $update = intval($update);
    $incident = intval($incident);
    
    $sql = "UPDATE `{$dbUpdates}` SET incidentid = '{$incident}' ";
    $sql .= "WHERE id = '{$update}'";
    mysql_query($sql);
    if (mysql_error())
    {
        trigger_error(mysql_error(),E_USER_ERROR);
        return FALSE;
    }
    else
    {
        return TRUE;
    }
}

?>
