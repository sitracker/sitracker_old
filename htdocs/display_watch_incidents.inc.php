<?php
// display_watch_incidents.inc.php - Page to render the watch incidents so it can be done in a different thread
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>

$permission=0; // not required
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');


$sql = "SELECT type, id FROM dashboard_watch_incidents WHERE userid = {$sit[2]}";
$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);


if (mysql_num_rows($result) > 0)
{
    while($obj = mysql_fetch_object($result))
    {
        echo "<table align='center' style='width: 100%'>";
        switch($obj->type)
        {
            case '0': //Site
                $sql = "SELECT incidents.id, incidents.title, incidents.status, incidents.servicelevel, incidents.maintenanceid, incidents.priority, contacts.forenames, contacts.surname, contacts.siteid ";
                $sql .= "FROM incidents, contacts ";
                $sql .= "WHERE incidents.contact = contacts.id AND contacts.siteid = {$obj->id} ";
                $sql .= "AND incidents.status != 2 AND incidents.status != 7";

                $lsql = "SELECT name FROM sites WHERE id = {$obj->id}";
                $lresult = mysql_query($lsql);
                if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                $lobj = mysql_fetch_object($lresult);
                echo "<tr><th colspan='3'>Incidents for ".stripslashes($lobj->name)." (site)</th></tr>";
                break;
            case '1': //contact
                $sql = "SELECT incidents.id, incidents.title, incidents.status, incidents.servicelevel, incidents.maintenanceid, incidents.priority, contacts.forenames, contacts.surname, contacts.siteid ";
                $sql .= "FROM incidents, contacts ";
                $sql .= "WHERE incidents.contact = contacts.id AND incidents.contact = {$obj->id} ";
                $sql .= "AND incidents.status != 2 AND incidents.status != 7";

                $lsql = "SELECT forenames, surname FROM contacts WHERE id = {$obj->id}";
                $lresult = mysql_query($lsql);
                if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                $lobj = mysql_fetch_object($lresult);
                echo "<tr><th colspan='3'>Incidents for ".stripslashes($lobj->forenames)." ".stripslashes($lobj->surname)." (contact)</th></tr>";
                break;
            case '2': //engineer
                $sql = "SELECT incidents.id, incidents.title, incidents.status, incidents.servicelevel, incidents.maintenanceid, incidents.priority, contacts.forenames, contacts.surname, contacts.siteid ";
                $sql .= "FROM incidents, contacts ";
                $sql .= "WHERE incidents.contact = contacts.id AND (incidents.owner = {$obj->id} OR incidents.towner = {$obj->id}) ";
                $sql .= "AND incidents.status != 2 AND incidents.status != 7";

                $lsql = "SELECT realname FROM users WHERE id = {$obj->id}";
                $lresult = mysql_query($lsql);
                if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                $lobj = mysql_fetch_object($lresult);
                echo "<tr><th colspan='3'>";
                printf($GLOBALS['strIncidentsForEngineer'], stripslashes($lobj->realname));
                echo "</th></tr>";

                break;

            default:
                $sql = '';
        }

        if (!empty($sql))
        {
            $iresult = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
            if(mysql_num_rows($iresult) > 0)
            {
                echo "<tr>";
                echo colheader('id', $GLOBALS['strID']);
                echo colheader('title', $GLOBALS['strTitle']);
                //echo colheader('customer', $GLOBALS['strCustomer']);
                echo colheader('status', $GLOBALS['strStatus']);
                echo "</tr>\n";
                $shade='shade1';
                while ($incident = mysql_fetch_object($iresult))
                {
                    echo "<tr class='$shade'>";
                    echo "<td>{$incident->id}</td>";
                    echo "<td><a href='javascript:incident_details_window({$incident->id}) '  class='info'>".stripslashes($incident->title);
                    echo "<span><strong>{$GLOBALS['strCustomer']}:</strong> ".stripslashes($incident->forenames.' '.$incident->surname)." of ".site_name($incident->siteid);
                    list($update_userid, $update_type, $update_currentowner, $update_currentstatus, $update_body, $update_timestamp, $update_nextaction, $update_id)=incident_lastupdate($incident->id);
                    $update_body = parse_updatebody($update_body);
                    if (!empty($update_body) AND $update_body!='...') echo "<br />{$update_body}";
                    echo "</span></a></td>";
                    echo "<td>".incidentstatus_name($incident->status)."</td>";
                    echo "</tr>\n";
                    if ($shade=='shade1') $shade='shade2';
                    else $shade='shade1';
                }
            }
            else echo "<tr><td colspan='3'>{$GLOBALS['strNoOpenIncidents']}</td></tr>\n";
        }
        echo "</table>\n";
    }
}
else
{
    echo "<p align='center'>{$GLOBALS['strNone']}</p>";;
}
?>