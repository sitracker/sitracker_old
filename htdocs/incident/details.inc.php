<?php
/*
incident/details.inc.php - Performs incident tasks, included by ../incident.php

SiT (Support Incident Tracker) - Support call tracking system
Copyright (C) 2000-2008 Salford Software Ltd. and Contributors

This software may be used and distributed according to the terms
of the GNU General Public License, incorporated herein by reference.
*/

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}

echo "<div id='detailsummary'>";

// Two column table FIXME can be divs
echo "<table>";
echo "<tr><td>";
// First column: Contact Details
$contact = "<a href='contact_details.php?id={$incident->contactid}' title=\"{$strContact}\" target='top.opener' class='info'>{$incident->forenames} {$incident->surname}";
if (!empty($contact_notes)) $contact .= "<span>{$contact_notes}</span>";
$contact .= "</a> ";
$site = "<a href='site_details.php?id={$incident->siteid}' title='{$strSite}' target='top.opener' class='info'>{$site_name}";
if (!empty($site_notes)) $site .= "<span>{$site_notes}</span>";
$site .= "</a> ";
$site .= list_tag_icons($incident->siteid, TAG_SITE); // site tag icons
$site .= "<br />\n";
echo sprintf($strContactofSite, $contact, $site)." ";
echo "<a href=\"mailto:{$incident->email}\">{$incident->email}</a><br />\n";
if ($incident->ccemail != '') echo "CC: <a href=\"mailto:{$incident->ccemail}\">{$incident->ccemail}</a><br />\n";
if ($incident->phone!='' OR $incident->phone!='')
{
    if ($incident->phone != '') echo "{$strTel}: {$incident->phone}";
    if ($incident->mobile != '') echo " {$strMob}: {$incident->mobile}";
    echo "<br />\n";
}
if ($incident->externalid != '' OR $incident->escalationpath > 0)
{
    echo "{$strEscalated}: ";
    echo format_external_id($incident->externalid,$incident->escalationpath)."<br />\n";
}
if ($incident->externalengineer != '')
{
    echo $incident->externalengineer;
    if ($incident->externalemail != '') echo ", <a href=\"mailto:{$incident->externalemail}\">{$incident->externalemail}</a>";
    echo "<br />\n";
}

$sql = "SELECT * FROM {$dbLinks} AS l, {$dbInventory} AS i ";
$sql .= "WHERE linktype = 7 ";
$sql .= "AND origcolref = {$incidentid} ";
$sql .= "AND i.id = linkcolref ";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
if (mysql_num_rows($result) > 0)
{
    $inventory = mysql_fetch_object($result);
    echo "<a href='inventory.php?view={$inventory->id}'>";
    echo "$inventory->name";
    if (!empty($inventory->identifier))
    {
        echo " ({$inventory->identifier})";
    }
    elseif (!empty($inventory->address))
    {
        echo " ({$inventory->address})";
    }
}

$tags = list_tags($id, TAG_INCIDENT, TRUE);
if (!empty($tags)) echo "{$tags}\n";
echo "</td>";

echo "<td>";
// Second column, Product and Incident details
if ($incident->owner != $sit[2] OR ($incident->towner > 0 AND $incident->towner != $incident->owner))
{
    echo "{$strOwner}: <strong>".user_realname($incident->owner,TRUE)."</strong> ";
    $incidentowner_phone = user_phone($incident->owner);
    if ($incidentowner_phone != '') echo "({$strTel}: {$incidentowner_phone}) ";
    if ($incident->towner > 0 AND $incident->towner != $incident->owner)
    {
       echo "({$strTemp}: ".user_realname($incident->towner,TRUE).")";
    }
    echo "<br />";
}
if ($software_name!='' OR $incident->productversion != '' OR $incident->productservicepacks!='')
{
    echo $software_name;
    if ($incident->productversion != '' OR $incident->productservicepacks!='')
    {
        echo " (".$incident->productversion;
        if ($incident->productservicepacks!='') echo $incident->productservicepacks;
        echo ")";
    }
    echo "<br />\n";
}
echo priority_icon($incident->priority)." ".priority_name($incident->priority);
if ($product_name!='')
{
    echo " <a href='contract_details.php?id={$incident->maintenanceid}' title='{$strContactDetails}' target='top.opener'>";
    echo "{$product_name}";
    echo "</a>";
}
elseif ($incident->maintenanceid > 0)
{
    echo "<a href='contract_details.php?id={$incident->maintenanceid}' title='{$strContactDetails}' target='top.opener'>";
    echo "{$strContract} {$incident->maintenanceid}";
    echo "</a>";
}
else echo "<strong>{$strSiteSupport}</strong>";
echo " / ";

echo "{$servicelevel_tag}<br />\n ";

switch (does_contact_have_billable_contract($incident->contactid))
{
    case CONTACT_HAS_BILLABLE_CONTRACT:
        echo "{$strContactHasBillableContract} (&cong;".contract_unit_balance(get_billable_contract_id($incident->contactid))." units)<br />";
        break;
    case SITE_HAS_BILLABLE_CONTRACT:
        echo "{$strSiteHasBillableContract} (&cong;".contract_unit_balance(get_billable_contract_id($incident->contactid))." units)<br />";
        break;
}

$num_open_activities = open_activities_for_incident($incidentid);
if (count($num_open_activities) > 0)
{
    echo "<a href='tasks.php?incident={$incidentid}' class='info'>";
    echo icon('timer', 16, $strOpenActivities);
    echo "</a> ";
}

// Product Info
if (!empty($incident->product))
{
    $pisql = "SELECT pi.information AS label, ipi.information AS information ";
    $pisql .= "FROM `{$dbIncidentProductInfo}` AS ipi, `{$dbProductInfo}` AS pi ";
    $pisql .= "WHERE pi.id = ipi.productinfoid AND ipi.incidentid = {$incidentid}";
    $piresult = mysql_query($pisql);
    if (mysql_num_rows($piresult) > 0)
    {
        while ($pi = mysql_fetch_object($piresult))
        {
            echo "{$pi->label}: {$pi->information} <br />\n";
        }
    }
}

echo sprintf($strOpenForX, $opened_for)." ";
echo incidentstatus_name($incident->status);
if ($incident->status == 2) echo " (" . closingstatus_name($incident->closingstatus) . ")";
echo "<br />\n";

// Show sla target/review target if incident is still open
if ($incident->status != 2 AND $incident->status!=7)
{
    if ($targettype != '')
    {
        if ($slaremain > 0)
        {
            echo sprintf($strSLAInX, $targettype, format_workday_minutes($slaremain));
        }
        elseif ($slaremain < 0)
        {
            echo " ".sprintf($strSLAXLate, $targettype, format_workday_minutes((0-$slaremain)));
        }
        else
        {
        	echo " ".sprintf($strSLAXDueNow , $targettype);
        }
    }

    if ($reviewremain > 0 && $reviewremain <= 2400)
    {
        // Only display if review is due in the next five days
        if ($slaremain<>0) echo "<br />"; // only need a line sometimes
        printf($strReviewIn,format_workday_minutes($reviewremain));
    }
    elseif ($reviewremain <= 0)
    {
        if ($slaremain <> 0) echo "<br />"; // only need a line sometimes
        echo $strReviewDueNow;
    }
    
    if ($servicelevel->timed == 'yes')
    {
        echo "<br />";
        switch (count($num_open_activities))
        {
        	case 0: //start
                echo "<a href='add_task.php?incident={$id}'>{$strStartNewActivity}</a>";
                break;
            case 1: //stop
                echo "<a href='view_task.php?id={$num_open_activities[0]}&amp;mode=incident&amp;incident={$id}'>{$strViewActivity}</a> | ";
                $sql = "SELECT * FROM `{$dbNotes}` WHERE link='10' AND refid='{$id}'";
                $result = mysql_query($sql);
                if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
                if (mysql_num_rows($result) >= 1)
                {
                    echo "<a href='edit_task.php?id={$num_open_activities[0]}&amp;action=markcomplete&amp;incident={$id}'>{$strStopActivity}</a>";
                }
                else
                {
                    // Notes needed before closure
                	echo $strActivityContainsNoNotes;
                }
                break;
            default:  //greyed out
                echo "<a href='tasks.php?incident={$id}'>{$strMultipleActivitiesRunning}</a>";
        }
    }
}
echo "</td>";
echo "</tr>\n";

// Incident relationships
$rsql = "SELECT * FROM `{$dbRelatedIncidents}` WHERE incidentid='$id' OR relatedid='$id'";
$rresult = mysql_query($rsql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
if (mysql_num_rows($rresult) >= 1)
{
    echo "<tr><td colspan='2'>{$strRelations}: ";
    while ($related = mysql_fetch_object($rresult))
    {
        if ($related->relatedid == $id)
        {
            if ($related->relation == 'child') $linktitle = 'Child';
            else $linktitle = 'Sibling';
            $linktitle .= ": ".incident_title($related->incidentid);
            echo "<a href='incident_details.php?id={$related->incidentid}' title='$linktitle'>{$related->incidentid}</a> ";
        }
        else
        {
            if ($related->relation == 'child') $linktitle = 'Parent';
            else $linktitle = 'Sibling';
            $linktitle .= ": ".incident_title($related->relatedid);
            echo "<a href='incident_details.php?id={$related->relatedid}' title='$linktitle'>{$related->relatedid}</a> ";
        }
        echo " &nbsp;";
    }
    echo "</td></tr>";

}

echo "</table>";

plugin_do('incident_details');

echo "</div>\n\n";

?>
