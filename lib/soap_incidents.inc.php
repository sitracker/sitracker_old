<?php
// soap_incidents.inc.php - SOAP functions for incidents
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Paul Heaney <paul[at]sitracker.org>

$server->register('list_incidents',
        array('sessionid' => 'xsd:string', 'owner' => 'xsd:int', 'type' => 'xsd:int'), // Input
        array('return'  => 'tns:incident_list_response'), // return
        $soap_namespace);

/**
 * Lists a set of incidents
 * @author Paul Heaney
 * @param string $sessionid - the sessionid
 * @param int $owner - List incidents of this owner (0 for all)
 * @param int $status - 0 for ALL, 1 for all Active, 2 for all open
 * @return Array - array of incidents[] Status
 */
function list_incidents($sessionid, $owner=0, $status=1)
{
    $status = new SoapStatus();

    $incidents = array();

    if (!empty($sessionid) AND validate_session($sessionid))
    {
        $sql = "SELECT * FROM `{$GLOBALS['dbIncidents']}` WHERE 1 = 1 ";
        if ($owner > 0) $sql .= "AND (owner = {$owner} OR $towner = {$owner}) ";
        switch ($status)
        {
            case 1: $sql .= "AND status = ".STATUS_ACTIVE." ";
                break;
            case 2: $sql .= "AND (status != ".STATUS_CLOSED." AND status !=  ".STATUS_UNASSIGNED.") ";
                break;
        }

        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        if (mysql_num_rows($result) > 0)
        {
        	while ($obj = mysql_fetch_object($result))
            {
            	$incident = new Incident();
                $incident->incidentid = $obj->id;
                $incident->title = $obj->title;
                $incident->ownerid= $obj->owner;
                $incident->townerid = $obj->towner;
                $incident->priority = $obj->priority;
                $incident->currentstatusid = $obj->status;
                $incident->skillid = $obj->softwareid;
                $incident->maintenanceid = $obj->maintenanceid;
                $incident->servicelevel = $obj->servicelevel;

                $incidents[] = $incident;
            }
        }
    }
    else
    {
    	$status->set_error('session_not_valid');
    }

    return array('incidents' => $incidents, 'status' => $status->get_array());
}

?>
