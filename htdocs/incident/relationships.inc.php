<?php
// relationships.inc.php - Displays and allows editing of incident relationships
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}

// External variables
$action = $_REQUEST['action'];
$relatedid = cleanvar($_POST['relatedid']);
$relation = cleanvar($_POST['relation']);
$rid = cleanvar($_REQUEST['rid']);

switch ($action)
{
    case 'add':
        // First check there isn't already a relationship to that incident
        $sql = "SELECT id FROM relatedincidents WHERE (incidentid='$relatedid' AND relatedid='$id') OR (relatedid='$relatedid' AND incidentid='$id')";
        $result = mysql_query($sql);
        if (mysql_num_rows($result) < 1 AND $relatedid!=$id)
        {
            echo "<p align='center'>Adding a relation</p>";
            switch ($relation)
            {
                case 'sibling':
                    $sql = "INSERT INTO `{$dbRelatedIncidents}` (incidentid, relation, relatedid, owner) ";
                    $sql .= "VALUES ('$id', 'sibling', '$relatedid', '{$_SESSION['userid']}')";
                    mysql_query($sql);
                    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

                    // Insert an entry into the update log for this incident
                    $sql  = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, timestamp, currentowner, customervisibility, sla, bodytext) ";
                    $sql .= "VALUES ('$id', '".$sit[2]."', 'editing', '$now', '".$sit[2]."', 'hide', '','Added relationship with Incident $relatedid')";
                    mysql_query($sql);
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

                    // Insert an entry into the update log for the related incident
                    $sql  = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, timestamp, currentowner, customervisibility, sla, bodytext) ";
                    $sql .= "VALUES ('$relatedid', '".$sit[2]."', 'editing', '$now', '".$sit[2]."', 'hide', '','Added relationship with Incident $id')";
                    mysql_query($sql);
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
                break;
            }
            // TODO v3.2x Child/Parent incident relationships
        }
        else echo "<br /><p class='error' align='center'>A relationship already exists with that incident</p>";
    break;

    case 'delete':
        // Retreive details of the relationship
        $sql = "SELECT * FROM relatedincidents WHERE id='$rid'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        $relation = mysql_fetch_object($result);

        $sql = "DELETE FROM relatedincidents WHERE id='$rid'";
        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        // Insert an entry into the update log for this incident
        $sql  = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, timestamp, currentowner, customervisibility, sla, bodytext) ";
        $sql .= "VALUES ('{$relation->incidentid}', '".$sit[2]."', 'editing', '$now', '".$sit[2]."', 'hide', '','Removed relationship with Incident {$relation->relatedid}')";
        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        // Insert an entry into the update log for the related incident
        $sql  = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, timestamp, currentowner, customervisibility, sla, bodytext) ";
        $sql .= "VALUES ('{$relation->relatedid}', '".$sit[2]."', 'editing', '$now', '".$sit[2]."', 'hide', '','Removed relationship with Incident {$relation->incidentid}')";
        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        echo "<br /><p class='info' align='center'>Relationship removed</p>";
    break;

    default:
        // do nothing
}


// Incident relationships
$rsql = "SELECT * FROM relatedincidents WHERE incidentid='$id' OR relatedid='$id'";
$rresult = mysql_query($rsql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
if (mysql_num_rows($rresult) >= 1)
{
    echo "<h2>Related Incidents</h2>";
    echo "<table summary='Related Incidents' align='center'>";
    echo "<tr><th>Incident ID</th><th>Title</th><th>Relationship</th><th>Created by</th><th>Action</th></tr>\n";
    while ($related = mysql_fetch_object($rresult))
    {
        echo "<tr>";
        $incidenttitle = incident_title($related->incidentid);
        if ($related->relatedid==$id)
        {
            if ($related->relation=='child') $relationship='Child';
            else $relationship='Sibling';
            echo "<td><a href='incident_details.php?id={$related->incidentid}'>{$related->incidentid}</a></td>";
        }
        else
        {
            if ($related->relation=='child') $relationship='Parent';
            else $relationship='Sibling';
            echo "<td><a href='incident_details.php?id={$related->relatedid}'>{$related->relatedid}</a></td>";

        }
        echo "<td>$incidenttitle</td>";
        echo "<td>$relationship</td>";
        echo "<td>".user_realname($related->owner,TRUE)."</td>";
        echo "<td><a href='incident_relationships.php?id={$id}&amp;rid={$related->id}&amp;action=delete'>Remove</a></td>";
        echo "</tr>\n";
    }
    echo "</table>";
}
else echo "<p align='center'>There are no related incidents</p>";

echo "<form action='incident_relationships.php' method='post'>";
echo "<h2>Add a relation</h2>";
echo "<table summary='Add a relationship' class='vertical'>";
echo "<tr><th>Incident ID</th><td><input type='text' name='relatedid' size='10' /></td></tr>\n";
// TODO v3.24 Child/Parent incident relationships
//echo "<tr><th>Relationship to this incident</th><td>";
//echo "<select name='relation'>";
//echo "<option value='child'>Child</option>";
//echo "<option value='parent'>Parent</option>";
//echo "<option value='sibling'>Sibling</option>";
//echo "</select>";
//echo "</td></tr>\n";
echo "</table>\n";
echo "<input type='hidden' name='action' value='add' />";
echo "<input type='hidden' name='id' value='{$id}' />";
echo "<input type='hidden' name='relation' value='sibling' />";
echo "<p><input type='submit' value='{$strAdd}' /></p>";
echo "</form>";


?>
