<?php
// timecalc.php - Calculate SLA times
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>

// Prevent script from being run directly (ie. it must always be included by auto.php)
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}

define("STATUS_CUSTOMER",8);

/*

*/

/*

    Loop around all awaiting customer action
        if necessary send changse

    Loop around all sent chase
        if over 3 days set to chase_phone

    Loop around all chased_phone  (NOTE: not doing chase_phone as this wouldn't be fair)
        if over 2 days set to chase_manager
*/

function not_auto_type($type)
{
    if($type != 'auto_chase_email' AND $type != 'auto_chase_phone' AND $type != 'auto_chase_manager')
    {
        return TRUE;
    }

    return FALSE;
}

if($CONFIG['auto_chase'] == TRUE)
{

    // if 'awaiting customer action' for more than $CONFIG['chase_email_minutes'] and NOT in an auto state, send auto email

    //$sql = "SELECT incidents.id, contacts.forenames,contacts.surname,contacts.id AS managerid FROM incidents,contacts WHERE status = ".STATUS_CUSTOMER." AND contacts.notify_contactid = contacts.id";
    $sql = "SELECT incidents.id, incidents.timeofnextaction FROM incidents WHERE status = ".STATUS_CUSTOMER;

    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    while($obj = mysql_fetch_object($result))
    {
        if(!in_array($obj->maintenanceid, $CONFIG['dont_chase_maintids']))
        {
            // only annoy these people
            $sql_update = "SELECT * FROM updates WHERE incidentid = {$obj->id} ORDER BY timestamp DESC LIMIT 1";
            $result_update = mysql_query($sql_update);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

            $obj_update = mysql_fetch_object($result_update);

            if($CONFIG['chase_email_minutes'] != 0)
            {
                //if(not_auto_type($obj_update->type) AND $obj_update->timestamp <= ($now-$CONFIG['chase_email_minutes']*60))
                if(not_auto_type($obj_update->type) AND (($obj->timeofnextaction == 0 AND calculate_working_time($obj_update->timestamp, $now) >= $CONFIG['chase_email_minutes']) OR ($obj->timeofnextaction != 0 AND calculate_working_time($obj->timeofnextupdate, $now) >= $CONFIG['chase_email_minutes'])))
                {
                    send_template_email($CONFIG['chase_email_template'],$obj->id);
                    $sql_insert = "INSERT INTO updates (incidentid, userid, type, bodytext, timestamp, customervisibility) VALUES ('{$obj_update->incidentid}','{$sit['2']}','auto_chase_email','Sent auto chase email to customer','{$now}','show')";
                    mysql_query($sql_insert);
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

                    $sql_update = "UPDATE incidents SET lastupdated = '{$now}', nextactiontime = 0 WHERE id = {$obj->id}";
                    mysql_query($sql_update);
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
                }
            }

            if($CONFIG['chase_phone_minutes'] != 0)
            {
                //if($obj_update->type == 'auto_chase_email' AND $obj_update->timestamp <= ($now-$CONFIG['chase_phone_minutes']*60))
                if($obj_update->type == 'auto_chase_email' AND  (($obj->timeofnextaction == 0 AND calculate_working_time($obj_update->timestamp, $now) >= $CONFIG['chase_phone_minutes']) OR ($obj->timeofnextaction != 0 AND calculate_working_time($obj->timeofnextupdate, $now) >= $CONFIG['chase_phone_minutes'])))

                {
                    $sql_insert = "INSERT INTO updates (incidentid, userid, type, bodytext, timestamp, customervisibility) VALUES ('{$obj_update->incidentid}','{$sit['2']}','auto_chase_phone','Status: Awaiting Customer Action -&gt; <b>Active</b><hr>Please phone the customer to get an update on this call as {$CONFIG['chase_phone_minutes']} have passed since the auto chase email was sent. Once you have done this please use the update type \"Chased customer - phone\"','{$now}','hide')";
                    mysql_query($sql_insert);
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

                    $sql_update = "UPDATE incidents SET lastupdated = '{$now}', nextactiontime = 0, status = 1 WHERE id = {$obj->id}";
                    mysql_query($sql_update);
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
                }
            }

            if($CONFIG['chase_manager_minutes'] != 0)
            {
                //if($obj_update->type == 'auto_chased_phone' AND $obj_update->timestamp <= ($now-$CONFIG['chase_manager_minutes']*60))
                if($obj_update->type == 'auto_chased_phone' AND (($obj->timeofnextaction == 0 AND calculate_working_time($obj_update->timestamp, $now) >= $CONFIG['chase_manager_minutes']) OR ($obj->timeofnextaction != 0 AND calculate_working_time($obj->timeofnextupdate, $now) >= $CONFIG['chase_manager_minutes'])))
                {
                    $update = "Status: Awaiting Customer Action -&gt; <b>Active</b><hr>";
                    $update .= "Please phone the customers MANAGER to get an update on this call as ".$CONFIG['chase_manager_minutes']." have passed since the auto chase email was sent.<br />";
                    $update .= "The manager is <a href='contact_details.php?id={$obj->managerid}'>{$obj->forenames} {$obj->surname}</a><br />";
                    $update .= " Once you have done this please email the actions to the customer and select the \"Was this a customer chase?\"'";

                    $sql_insert = "INSERT INTO updates (incidentid, userid, type, bodytext, timestamp, customervisibility) VALUES ('{$obj_update->incidentid}','{$sit['2']}','auto_chase_manager',$update,'{$now}','hide')";
                    mysql_query($sql_insert);
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

                    $sql_update = "UPDATE incidents SET lastupdated = '{$now}', nextactiontime = 0, status = 1 WHERE id = {$obj->id}";
                    mysql_query($sql_update);
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
                }
            }

            if($CONFIG['chase_managers_manager_minutes'] != 0)
            {
                //if($obj_update->type == 'auto_chased_manager' AND $obj_update->timestamp <= ($now-$CONFIG['chase_managers_manager_minutes']*60))
                if($obj_update->type == 'auto_chased_manager' AND (($obj->timeofnextaction == 0 AND calculate_working_time($obj_update->timestamp, $now) >= $CONFIG['chase_amanager_manager_minutes']) OR ($obj->timeofnextaction != 0 AND calculate_working_time($obj->timeofnextupdate, $now) >= $CONFIG['chase_amanager_manager_minutes'])))
                {
                    $sql_insert = "INSERT INTO updates (incidentid, userid, type, bodytext, timestamp, customervisibility) VALUES ('{$obj_update->incidentid}','{$sit['2']}','auto_chase_managers_manager','Status: Awaiting Customer Action -&gt; <b>Active</b><hr>Please phone the customers managers manager to get an update on this call as {$CONFIG['chase_manager_minutes']} have passed since the auto chase email was sent. Once you have done this please email the actions to the customer and manager and select the \"Was this a manager chase?\"','{$now}','hide')";
                    mysql_query($sql_insert);
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

                    $sql_update = "UPDATE incidents SET lastupdated = '{$now}', nextactiontime = 0, status = 1 WHERE id = {$obj->id}";
                    mysql_query($sql_update);
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
                }
            }
        }
    }


}

?>