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

// Two column table
echo "<table>";
echo "<tr><td>";
// First column: Contact Details
echo "<a href='contact_details.php?id={$incident->contactid}' title=\"{$strContact}\" target='top.opener' class='info'>{$incident->forenames} {$incident->surname}";
if (!empty($contact_notes)) echo "<span>{$contact_notes}</span>";
echo "</a> ";
echo "of <a href='site_details.php?id={$incident->siteid}' title='{$strSite}' target='top.opener' class='info'>{$site_name}";
if (!empty($site_notes)) echo "<span>{$site_notes}</span>";
echo "</a> ";
echo list_tag_icons($incident->siteid, TAG_SITE); // site tag icons
echo "<br />\n";
echo "<a href=\"mailto:{$incident->email}\">{$incident->email}</a><br />\n";
if ($incident->ccemail != '') echo "CC: <a href=\"mailto:{$incident->ccemail}\">{$incident->ccemail}</a><br />\n";
if ($incident->phone!='' OR $incident->phone!='')
{
    if ($incident->phone!='') echo "Tel: {$incident->phone}";
    if ($incident->mobile!='') echo " Mob: {$incident->mobile}";
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

if (open_activities_for_incident($incidentid))
{
    echo "<a href='tasks.php?incident={$incidentid}' class='info'>";
    echo "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/timer.png' width='16' height='16' alt='{$strOpenActivities}' />";
    echo " {$strOpenActivities}</a><br />";
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
    if ($incidentowner_phone != '') echo "(Tel: {$incidentowner_phone}) ";
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
echo priority_icon($incident->priority)." ";
if ($product_name!='')
{
    echo "<a href='contract_details.php?id={$incident->maintenanceid}' title='Contract {$incident->maintenanceid} Details' target='top.opener'>";
    echo "{$product_name}";
    echo "</a>";
}
elseif ($incident->maintenanceid > 0)
{
    echo "<a href='contract_details.php?id={$incident->maintenanceid}' title='Contract {$incident->maintenanceid} Details' target='top.opener'>";
    echo "{$strContract} {$incident->maintenanceid}";
    echo "</a>";
}
else echo "<strong>{$strSiteSupport}</strong>";
echo " / ";

echo "{$servicelevel_tag}<br />\n";
echo "Open for {$opened_for}, ";
echo incidentstatus_name($incident->status);
if ($incident->status == 2) echo " (" . closingstatus_name($incident->closingstatus) . ")";
echo "<br />\n";

// Show sla target/review target if incident is still open
if ($incident->status != 2 AND $incident->status!=7)
{
    if ($slaremain<>0)
    {
        echo $targettype;
        if ($slaremain > 0) echo " in ".format_workday_minutes($slaremain);  //  ." left"
        elseif ($slaremain < 0) echo " ".format_workday_minutes((0-$slaremain))." late";  //  ." left"
    }
    if ($reviewremain>0 && $reviewremain<=2400)
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
}
echo "</td>";
echo "</tr>\n";

// Incident relationships
$rsql = "SELECT * FROM `{$dbRelatedIncidents}` WHERE incidentid='$id' OR relatedid='$id'";
$rresult = mysql_query($rsql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
if (mysql_num_rows($rresult) >= 1)
{
    echo "<tr><td colspan='2'>Relations: ";
    while ($related = mysql_fetch_object($rresult))
    {
        if ($related->relatedid==$id)
        {
            if ($related->relation=='child') $linktitle='Child';
            else $linktitle='Sibling';
            $linktitle .= ": ".incident_title($related->incidentid);
            echo "<a href='incident_details.php?id={$related->incidentid}' title='$linktitle'>{$related->incidentid}</a> ";
        }
        else
        {
            if ($related->relation=='child') $linktitle='Parent';
            else $linktitle='Sibling';
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
