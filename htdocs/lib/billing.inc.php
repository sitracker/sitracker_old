<?php
// billing.inc.php - functions relating to billing
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}

/**
 * Adds a closed incident to the transactions table awaiting approval
 * @author Paul Heaney
 * @param  int $incidentid The incident ID to add
 */
function add_billable_incident($incidentid)
{

}

/**
* Returns if the contact has a timed contract or if the site does in the case of the contact not.
* @author Paul Heaney
* @return either NO_BILLABLE_CONTRACT, CONTACT_HAS_BILLABLE_CONTRACT or SITE_HAS_BILLABLE_CONTRACT the latter is if the site has a billable contract by the contact isn't a named contact
*/
function does_contact_have_billable_contract($contactid)
{
    global $now;
    $return = NO_BILLABLE_CONTRACT;

    $siteid = contact_siteid($contactid);
    $sql = "SELECT DISTINCT m.id FROM `{$GLOBALS['dbMaintenance']}` AS m, `{$GLOBALS['dbServiceLevels']}` AS sl ";
    $sql .= "WHERE m.servicelevelid = sl.id AND sl.timed = 'yes' AND m.site = {$siteid} ";
    $sql .= "AND m.expirydate > {$now} AND m.term != 'yes'";
    $result = mysql_query($sql);

    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);

    if (mysql_num_rows($result) > 0)
    {
        // We have some billable/timed contracts
        $return = SITE_HAS_BILLABLE_CONTRACT;

        // check if the contact is listed on one of these

        while ($obj = mysql_fetch_object($result))
        {
            $sqlcontact = "SELECT * FROM `{$GLOBALS['dbSupportContacts']}` ";
            $sqlcontact .= "WHERE maintenanceid = {$obj->id} AND contactid = {$contactid}";

            $resultcontact = mysql_query($sqlcontact);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

            if (mysql_num_rows($resultcontact) > 0)
            {
                $return = CONTACT_HAS_BILLABLE_CONTRACT;
                break;
            }
        }
    }

    return $return;
}


/**
* Gets the billable contract ID for a contact, if multiple exist then the first one is choosen
* @author Paul Heaney
* @param int $contactid - The contact ID you want to find the contract for
* @return int the ID of the contract, -1 if not found
*/
function get_billable_contract_id($contactid)
{
    global $now;

    $return = -1;

    $siteid = contact_siteid($contactid);
    $sql = "SELECT DISTINCT m.id FROM `{$GLOBALS['dbMaintenance']}` AS m, `{$GLOBALS['dbServiceLevels']}` AS sl ";
    $sql .= "WHERE m.servicelevelid = sl.id AND sl.timed = 'yes' AND m.site = {$siteid} ";
    $sql .= "AND m.expirydate > {$now} AND m.term != 'yes'";

    $result = mysql_query($sql);

    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);

    if (mysql_num_rows($result) > 0)
    {
        $return = mysql_fetch_object($result)->id;
    }

    return $return;
}


/**
* Returns the percentage remaining for ALL services on a contract
* @author Kieran Hogg
* @param string $mainid - contract ID
* @return mixed - percentage between 0 and 1 if services, FALSE if not
*/
function get_service_percentage($maintid)
{
    global $dbService;
    if (does_contact_have_billable_contract(maintenance_siteid($maintid)) != NO_BILLABLE_CONTRACT)
    {
        $sql = "SELECT * FROM `{$dbService}` ";
        $sql .= "WHERE contractid = '{$maintid}'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
        
        $num = 0;
        while ($service = mysql_fetch_object($result))
        {
            $total += (float) $service->balance / (float) $service->creditamount;
            $num++;
        }
        $return = (float) $total / (float) $num;
    }
    else
    {
        $return = FALSE;
    }

    return $return;
}


/**
 * Set the last billing time on a service
 * @param int $serviceid - service ID
 * @param string $date -  Date (in format YYYY-MM-DD) to set the last billing time to
 * @return boolean - TRUE if sucessfully updated, false otherwise
 */
function update_last_billed_time($serviceid, $date)
{
    global $dbService;

    $rtnvalue = FALSE;

    if (!empty($serviceid) AND !empty($date))
    {
        $rtnvalue = TRUE;
        $sql .= "UPDATE `{$dbService}` SET lastbilled = '{$date}' WHERE serviceid = {$serviceid}";
        mysql_query($sql);
        if (mysql_error())
        {
            trigger_error(mysql_error(),E_USER_ERROR);
            $rtnvalue = FALSE;
        }

        if (mysql_affected_rows() < 1)
        {
            trigger_error("Approval failed",E_USER_ERROR);
            $rtnvalue = FALSE;
        }
    }

    return $rtnvalue;
}

/**
 * Find the billing multiple that should be applied given the day, time and matrix in use 
 * @author Paul Heaney
 * @param string $dayofweek 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun' or 'holiday'
 * @return float - The applicable multiplier for the time of day and billing matrix being used 
*/
function get_billable_multiplier($dayofweek, $hour, $billingmatrix = 1)
{
    $sql = "SELECT `{$dayofweek}` AS rate FROM {$GLOBALS['dbBillingMatrix']} WHERE hour = {$hour} AND id = {$billingmatrix}";

    $result = mysql_query($sql);
    if (mysql_error())
    {
        trigger_error(mysql_error(),E_USER_WARNING);
        return FALSE;
    }

    $rate = 1;

    if (mysql_num_rows($result) > 0)
    {
        $obj = mysql_fetch_object($result);
        $rate = $obj->rate;
    }

    return $rate;
}


/**
* Function to get an array of all billing multipliers for a billing matrix
* @author Paul Heaney
*/
function get_all_available_multipliers($matrixid=1)
{
    $days = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun', 'holiday');

    foreach ($days AS $d)
    {
        $sql = "SELECT DISTINCT({$d}) AS day FROM `{$GLOBALS['dbBillingMatrix']}` WHERE id = {$matrixid}";
        $result = mysql_query($sql);
        if (mysql_error())
        {
            trigger_error(mysql_error(),E_USER_WARNING);
            return FALSE;
        }

        while ($obj = mysql_fetch_object($result))
        {
            $a[$obj->day] = $obj->day;
        }
    }

    ksort($a);

    return $a;
}


/**
* Function to find the most applicable unit rate for a particular contract
* @author Paul Heaney
* @param $contractid - The contract id
* @param $date UNIX timestamp. The function will look for service that is current as of this timestamp
* @return int the unit rate, -1 if non found
*/
function get_unit_rate($contractid, $date='')
{
    $serviceid = get_serviceid($contractid, $date);

    if ($serviceid != -1)
    {
        $sql = "SELECT unitrate FROM `{$GLOBALS['dbService']}` AS p WHERE serviceid = {$serviceid}";

        $result = mysql_query($sql);
        if (mysql_error())
        {
            trigger_error(mysql_error(),E_USER_WARNING);
            return FALSE;
        }

        $unitrate = -1;

        if (mysql_num_rows($result) > 0)
        {
            $obj = mysql_fetch_object($result);
            $unitrate = $obj->unitrate;
        }
    }
    else
    {
        $unitrate = -1;
    }

    return $unitrate;
}


/**
* @author Paul Heaney
* @param $contractid  The Contract ID
* @param $date  UNIX timestamp. The function will look for service that is current as of this timestamp
* @return mixed.     Service ID, or -1 if not found, or FALSE on error
*/
function get_serviceid($contractid, $date = '')
{
    global $now, $CONFIG;
    if (empty($date)) $date = $now;

    $sql = "SELECT serviceid FROM `{$GLOBALS['dbService']}` AS p ";
    $sql .= "WHERE contractid = {$contractid} AND UNIX_TIMESTAMP(startdate) <= {$date} ";
    $sql .= "AND UNIX_TIMESTAMP(enddate) > {$date} ";
    $sql .= "AND (balance > 0 OR (select count(1) from service where contractid = s.contractid and balance > 0) = 0) ";

    if (!$CONFIG['billing_allow_incident_approval_against_overdrawn_service'])
    {
        $sql .= "AND balance > 0 ";
    }

    $sql .= "ORDER BY priority DESC, enddate ASC, balance DESC LIMIT 1";

    $result = mysql_query($sql);
    if (mysql_error())
    {
        trigger_error(mysql_error(),E_USER_WARNING);
        return FALSE;
    }

    $serviceid = -1;

    if (mysql_num_rows($result) > 0)
    {
        list($serviceid) = mysql_fetch_row($result);
    }

    return $serviceid;
}

?>
