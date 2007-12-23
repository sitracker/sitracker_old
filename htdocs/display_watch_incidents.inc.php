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

@include ('set_include_path.inc.php');
$permission = 0; // not required
require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

?>
<script type="text/javascript">
function statusform_submit(user)
{
    URL = "incidents.php?status=" + window.document.statusform.status.options[window.document.statusform.status.selectedIndex].value + "&amp;user=" + user;
    window.confirm(URL);
    window.location.href = URL;
}
</script>
<?php

$queue = $_REQUEST['queue'];

// Removed by INL 26Nov07 in prep for 3.31 release
// echo "<form action='{$_SERVER['PHP_SELF']}' style='text-align: center;'>";
// echo "{$strQueue}: <select class='dropdown' name='queue' onchange='window.location.href=this.options[this.selectedIndex].value'>\n";
// echo "<option ";
// if ($queue == 1) echo "selected='selected' ";
// echo "value=\"javascript:get_and_display('display_watch_incidents.inc.php?queue=1','watch_incidents_windows');\">{$strActionNeeded}</option>\n";
// echo "<option ";
// if ($queue == 3) echo "selected='selected' ";
// echo "value=\"javascript:get_and_display('display_watch_incidents.inc.php?queue=3','watch_incidents_windows');\">{$strAllOpen}</option>\n";
// echo "</select>\n";
// echo "</form>";


$sql = "SELECT type, id FROM `{$dbDashboardWatchIncidents}` WHERE userid = {$sit[2]} ORDER BY type";
$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);


if (mysql_num_rows($result) > 0)
{
    $header_printed = FALSE;
    $previous = 0;
    while ($obj = mysql_fetch_object($result))
    {
        if ($obj->type !=3 AND $previous == 3)
        {
            echo "</table>";
        }

        if ($obj->type == 3 AND !$header_printed)
        {
            echo "<table align='center' style='width: 100%'>";
        }
        else if ($obj->type != 3)
        {
            echo "<table align='center' style='width: 100%'>";
        }

        switch ($obj->type)
        {
            case '0': //Site
                $sql = "SELECT i.id, i.title, i.status, i.servicelevel, i.maintenanceid, i.priority, c.forenames, c.surname, c.siteid ";
                $sql .= "FROM `{$dbIncidents}` AS i, `{$dbContacts}` AS c ";
                $sql .= "WHERE i.contact = c.id AND c.siteid = {$obj->id} ";
                $sql .= "AND i.status != 2 AND i.status != 7";

                $lsql = "SELECT name FROM `{$dbSites}` WHERE id = {$obj->id}";
                $lresult = mysql_query($lsql);
                if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                $lobj = mysql_fetch_object($lresult);
                echo "<tr><th colspan='3'>{$lobj->name} ({$strSite})</th></tr>";
                break;
            case '1': //contact
                $sql = "SELECT i.id, i.title, i.status, i.servicelevel, i.maintenanceid, i.priority, c.forenames, c.surname, c.siteid ";
                $sql .= "FROM `{$dbIncidents}` AS i, `{$dbContacts}` AS c ";
                $sql .= "WHERE i.contact = c.id AND i.contact = {$obj->id} ";
                $sql .= "AND i.status != 2 AND i.status != 7";

                $lsql = "SELECT forenames, surname FROM `{$dbContacts}` WHERE id = {$obj->id}";
                $lresult = mysql_query($lsql);
                if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                $lobj = mysql_fetch_object($lresult);
                echo "<tr><th colspan='3'>{$lobj->forenames} {$lobj->surname} ({$strContact})</th></tr>";
                break;
            case '2': //engineer
                $sql = "SELECT i.id, i.title, i.status, i.servicelevel, i.maintenanceid, i.priority, c.forenames, c.surname, c.siteid ";
                $sql .= "FROM `{$dbIncidents}` AS i, `{$dbContacts}` AS c ";
                $sql .= "WHERE i.contact = c.id AND (i.owner = {$obj->id} OR i.towner = {$obj->id}) ";
                $sql .= "AND i.status != 2 AND i.status != 7";

                $lsql = "SELECT realname FROM `{$dbUsers}` WHERE id = {$obj->id}";
                $lresult = mysql_query($lsql);
                if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
                $lobj = mysql_fetch_object($lresult);
                echo "<tr><th colspan='3'>";
                printf($GLOBALS['strIncidentsForEngineer'], $lobj->realname);
                echo "</th></tr>";

                break;
            case '3': //incident
                $sql = "SELECT i.id, i.title, i.status, i.servicelevel, i.maintenanceid, i.priority ";
                $sql .= "FROM `{$dbIncidents}` AS i ";
                $sql .= "WHERE i.id = {$obj->id} ";
                //$sql .= "AND incidents.status != 2 AND incidents.status != 7";
                break;
            default:
                $sql = '';
        }

        if (!empty($sql))
        {
            $iresult = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
            if (mysql_num_rows($iresult) > 0)
            {
                if ($obj->type == 3 AND !$header_printed)
                {
                    echo "<tr>";
                    echo colheader('id', $GLOBALS['strID']);
                    echo colheader('title', $GLOBALS['strTitle']);
                    //echo colheader('customer', $GLOBALS['strCustomer']);
                    echo colheader('status', $GLOBALS['strStatus']);
                    echo "</tr>\n";
                    $header_printed = TRUE;
                }
                else if ($obj->type != 3)
                {
                    echo "<tr>";
                    echo colheader('id', $GLOBALS['strID']);
                    echo colheader('title', $GLOBALS['strTitle']);
                    //echo colheader('customer', $GLOBALS['strCustomer']);
                    echo colheader('status', $GLOBALS['strStatus']);
                    echo "</tr>\n";
                }

                $shade='shade1';
                while ($incident = mysql_fetch_object($iresult))
                {
                    echo "<tr class='$shade'>";
                    echo "<td>{$incident->id}</td>";
                    echo "<td><a href='javascript:incident_details_window({$incident->id}) '  class='info'>".$incident->title;
                    echo "<span><strong>{$GLOBALS['strCustomer']}:</strong> ".$incident->forenames.' '.$incident->surname." of ".site_name($incident->siteid); // FIXME i18n 'of'
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
        if ($obj->type == 3 AND !$header_printed)
        {
            echo "</table>\n";
        }

        $previous = $obj->type;
    }
}
else
{
    echo "<p align='center'>{$GLOBALS['strNoRecords']}</p>";
}
?>