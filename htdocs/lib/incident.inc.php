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
 * Gets incident details
 *
 * This function emulates a SQL query to the incident table while abstracting
 * SQL details
 * @param int $incident ID of the incident
 * @return object an object containing all parameters contained in the table
 * @author Kieran Hogg
 */
function incident($incident)
{
    global $dbIncidents;
    
    $incident = intval($incident);
    $sql = "SELECT * FROM `{$dbIncidents}` WHERE id = '$incident'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $row = mysql_fetch_object($result);
    return $row;
}

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

    if (!move_update_to_incident($update, $incident))
    {
        $rtn = FALSE;
    }
    
    if ($CONFIG['auto_assign_incidents'])
    {
        $user = suggest_reassign_userid($incident);
        if (!reassign_incident($incident, $user))
        {
            $rtn = FALSE;
        }
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


/**
 * Gets update details
 *
 * This function emulates a SQL query to the update table while abstracting
 * SQL details
 * @param int $update ID of the update
 * @return object an object containing all parameters contained in the table
 * @author Kieran Hogg
 */
function update($update)
{
    global $dbUpdates;
    
    $update = intval($update);
    $sql = "SELECT * FROM `{$dbUpdates}` WHERE id = '{$update}'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $row = mysql_fetch_object($result);
    return $row;
}


/**
    * Suggest the userid of a suitable person to handle the given incident
    * @author Ivan Lucas
    * @param $incidentid integer. An incident ID to suggest a new owner for
    * @param $exceptuserid integer. This user ID will not be suggested (e.g. the existing owner)
    * @returns A user ID of the suggested new owner
    * @retval FALSE failure.
    * @retval integer The user ID of the suggested new owner
    * @note Users are chosen randomly in a weighted lottery depending on their
    * avilability and queue status
*/
function suggest_reassign_userid($incidentid, $exceptuserid = 0)
{
    global $now, $dbUsers, $dbIncidents, $dbUserSoftware;
    $sql = "SELECT product, softwareid, priority, contact, owner FROM `{$dbIncidents}` WHERE id={$incidentid} LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

    if (!$result)
    {
        $userid = FALSE;
    }
    else
    {
        $incident = mysql_fetch_object($result);
        // If this is a critical incident the user we're assigning to must be online
        if ($incident->priority >= 4) $req_online = TRUE;
        else $req_online = FALSE;

        // Find the users with this skill (or all users)
        if (!empty($incident->softwareid))
        {
            $sql = "SELECT us.userid, u.status, u.lastseen FROM `{$dbUserSoftware}` AS us, `{$dbUsers}` AS u ";
            $sql .= "WHERE u.id = us.userid AND u.status > 0 AND u.accepting='Yes' ";
            if ($exceptuserid > 0) $sql .= "AND u.id != '$exceptuserid' ";
            $sql .= "AND softwareid = {$incident->softwareid}";
        }
        else
        {
            $sql = "SELECT id AS userid, status, lastseen FROM `{$dbUsers}` AS u WHERE status > 0 AND u.accepting='Yes' ";
            if ($exceptuserid > 0) $sql .= "AND id != '$exceptuserid' ";
        }
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

        // Fallback to all users if we have no results from above
        if (mysql_num_rows($result) < 1)
        {
            $sql = "SELECT id AS userid, status, lastseen FROM `{$dbUsers}` WHERE status > 0 ";
            if ($exceptuserid > 0) $sql .= "AND id != '$exceptuserid' ";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        }

        while ($user = mysql_fetch_object($result))
        {
            // Get a ticket for being skilled
            // Or in the case we don't know the skill, just get a ticket for accepting
            $ticket[] = $user->userid;

            // Get a ticket for being seen in the past 30 minutes
            if (mysql2date($user->lastseen) > $now - 1800) $ticket[] = $user->userid;

            // Get two tickets for being marked in-office or working at home
            if ($user->status == 1 OR $user->status == 6)
            {
                $ticket[] = $user->userid;
                $ticket[] = $user->userid;
            }

            // Get one ticket for being marked at lunch or in meeting
            // BUT ONLY if the incident isn't critical
            if ($incident->priority < 4 AND ($user->status == 3 OR $user->status == 4))
            {
                $ticket[] = $user->userid;
            }

            // Have a look at the users incident queue (owned)
            $qsql = "SELECT id, priority, lastupdated, status, softwareid FROM `{$dbIncidents}` WHERE owner={$user->userid}";
            $qresult = mysql_query($qsql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
            $queue_size = mysql_num_rows($qresult);
            if ($queue_size > 0)
            {
                $queued_critical = 0;
                $queued_high = 0;
                $queue_lastupdated = 0;
                $queue_samecontact = FALSE;
                while ($queue = mysql_fetch_object($qresult))
                {
                    if ($queue->priority == 3) $queued_high++;
                    if ($queue->priority >= 4) $queued_critical++;
                    if ($queue->lastupdated > $queue_lastupdated) $queue_lastupdated = $queue->lastupdated;
                    if ($queue->contact == $incident->contact) $queue_samecontact = TRUE;
                }
                // Get one ticket for your queue being updated in the past 4 hours
                if ($queue_lastupdated > ($now - 14400)) $user->userid;

                // Get two tickets for dealing with the same contact in your queue
                if ($queue_samecontact == TRUE)
                {
                    $ticket[] = $user->userid;
                    $ticket[] = $user->userid;
                }

                // Get one ticket for having five or less incidents
                if ($queued_size <=5) $ticket[] = $user->userid;

                // Get up to three tickets, one less ticket for each critical incident in queue
                for ($c=1;$c < (3 - $queued_critical);$c++) $ticket[] = $user->userid;

                // Get up to three tickets, one less ticket for each high priority incident in queue
                for ($c=1;$c < (3 - $queued_high);$c++) $ticket[] = $user->userid;
            }
            else
            {
                // Get one ticket for having an empty queue
                $ticket[] = $user->userid;
            }
        }

        // Do the lottery - "Release the balls"
        $numtickets = count($ticket)-1;
        $rand = mt_rand(0, $numtickets);
        $userid = $ticket[$rand];
    }
    if (empty($userid)) $userid = FALSE;
    return $userid;
}


/**
 * Reassigns an incident
 * @param int $incident incident ID to reassign
 * @param int $user user to reassign the incident to
 * @param string $type 'full' to do a full reassign, 'temp' for a temp
 * @return bool TRUE on success, FALSE on failure
 * @author Kieran Hogg
 */
function reassign_incident($incident, $user, $type = 'full')
{
    global $dbUpdates;
    if ($type == 'temp')
    {
        $sql = "UPDATE `{$dbUpdates} SET towner = '{$user}'";
    }
    else
    {
        $sql = "UPDATE `{$dbUpdates}` SET owner = '{$user}'";
    }
    mysql_query($sql);
    if (mysql_error())
    {
        trigger_error(mysql_error(),E_USER_WARNING);
        return FALSE;
    }
    else
    {
        return FALSE;
    }

}

?>