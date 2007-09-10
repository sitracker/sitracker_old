<?php
/*
incident/details.inc.php - Performs incident tasks, included by ../incident.php

SiT (Support Incident Tracker) - Support call tracking system
Copyright (C) 2000-2007 Salford Software Ltd. and Contributors

This software may be used and distributed according to the terms
of the GNU General Public License, incorporated herein by reference.
*/

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}


$incident->forenames = stripslashes($incident->forenames);
$incident->surname = stripslashes($incident->surname);

echo "<div id='detailsummary'>";

// Two column table
echo "<table>";
echo "<tr><td>";
// First column: Contact Details
echo "<a href='contact_details.php?id={$incident->contactid}' title='Contact Details' target='top.opener'>{$incident->forenames} {$incident->surname}</a> ";
echo "of <a href='site_details.php?id={$incident->siteid}' title='Site Details' target='top.opener'>{$site_name}</a> ";
echo list_tag_icons($incident->siteid, 3); // site tag icons
echo "<br />\n";
echo "<a href='mailto:{$incident->email}'>{$incident->email}</a><br />\n";
if ($incident->ccemail != '') echo "CC: <a href='mailto:{$incident->ccemail}'>{$incident->ccemail}</a><br />\n";
if ($incident->phone!='' OR $incident->phone!='')
{
    if ($incident->phone!='') echo "Tel: {$incident->phone}";
    if ($incident->mobile!='') echo " Mob: {$incident->mobile}";
    echo "<br />\n";
}
if ($incident->externalid != '' OR $incident->escalationpath > 0)
{
    echo "Escalated: ";
    echo format_external_id($incident->externalid,$incident->escalationpath)."<br />\n";
}
if ($incident->externalengineer != '')
{
    echo "{$incident->externalengineer}";
    if ($incident->externalemail != '') echo ", <a href='mailto:{$incident->externalemail}'>{$incident->externalemail}</a>";
    echo "<br />\n";
}
$tags = list_tags($id, 2, TRUE);
if (!empty($tags)) echo "{$tags}\n";
echo "</td>";

echo "<td>";
// Second column, Product and Incident details
if ($incident->owner != $sit[2] OR ($incident->towner > 0 AND $incident->towner != $incident->owner))
{
    echo "Owner: <strong>".user_realname($incident->owner,TRUE)."</strong> ";
    $incidentowner_phone = user_phone($incident->owner);
    if ($incidentowner_phone != '') echo "(Tel: {$incidentowner_phone}) ";
    if ($incident->towner > 0 AND $incident->towner != $incident->owner)
    {
       echo "(Temp: ".user_realname($incident->towner,TRUE).")";
    }
    echo "<br />";
}
if ($software_name!='' OR $incident->productversion != '' OR $incident->productservicepacks!='')
{
    echo stripslashes($software_name);
    if ($incident->productversion != '' OR $incident->productservicepacks!='')
    {
        echo " ({$incident->productversion}";
        if ($incident->productservicepacks!='') echo "{$incident->productservicepacks}";
        echo ")";
    }
    echo "<br />\n";
}
echo priority_icon($incident->priority)." ";
echo "<a href='maintenance_details.php?id={$incident->maintenanceid}' title='Contract {$incident->maintenanceid} Details' target='top.opener'>";
if ($product_name!='') echo "{$product_name}";
else echo "Contract {$incident->maintenanceid}";
echo "</a> / ";

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
        echo "Review in ".format_workday_minutes($reviewremain);
    }
    elseif ($reviewremain <= 0)
    {
        if ($slaremain <> 0) echo "<br />"; // only need a line sometimes
        echo "Review Due Now!";
    }
}
echo "</td>";
echo "</tr>\n";

// Incident relationships
$rsql = "SELECT * FROM relatedincidents WHERE incidentid='$id' OR relatedid='$id'";
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
