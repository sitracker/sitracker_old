<?php
/*
incident/details.inc.php - Performs incident tasks, included by ../incident.php

SiT (Support Incident Tracker) - Support call tracking system
Copyright (C) 2000-2006 Salford Software Ltd.

This software may be used and distributed according to the terms
of the GNU General Public License, incorporated herein by reference.
*/

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}

// Lookup the service level times
$slsql = "SELECT * FROM servicelevels WHERE tag='{$servicelevel_tag}' AND priority='{$incident->priority}' ";
$slresult = mysql_query($slsql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
$servicelevel = mysql_fetch_object($slresult);

// Get next target
$target = incident_get_next_target($incidentid);
// Calculate time remaining in SLA
$working_day_mins = ($CONFIG['end_working_day'] - $CONFIG['start_working_day']) / 60;
switch ($target->type)
{
    case 'initialresponse': $slatarget=$servicelevel->initial_response_mins; break;
    case 'probdef': $slatarget=$servicelevel->prob_determ_mins; break;
    case 'actionplan': $slatarget=$servicelevel->action_plan_mins; break;
    case 'solution': $slatarget=($servicelevel->resolution_days * $working_day_mins); break;
    default: $slaremain=0; $slatarget=0;
}

if ($slatarget >0) $slaremain=($slatarget - $target->since);
else $slaremain=0;
$targettype = target_type_name($target->type);

// Get next review time
$reviewsince = incident_get_next_review($incidents['id']);  // time since last review in minutes
$reviewtarget = ($servicelevel->review_days * $working_day_mins);          // how often reviews should happen in minutes
if ($reviewtarget > 0) $reviewremain=($reviewtarget - $reviewsince);
else $reviewremain = 0;

// Color the title bar according to the SLA and priority
$class='';
if ($slaremain <> 0)
{
    if (($slaremain - ($slatarget * 0.15 )) < 0 ) $class='notice';
    if (($slaremain - ($slatarget * 0.10 )) < 0 ) $class='urgent';
    if (($slaremain - ($slatarget * 0.05 )) < 0 ) $class='critical';
    if ($incident->priority==4) $class='critical';  // Force critical incidents to be critical always
}


// Print a table showing summary details of the incident
echo "<div id='detailsummary'>";
// Tempory hack, don't show this for old incident details page
if (strpos($_SERVER['PHP_SELF'], 'incident_details.php')===FALSE)
{
    echo "<h1 class='$class'>";  // Unknown
    echo "{$title}";
    echo "</h1>";
}
// Two column table
echo "<table>";
echo "<tr><td>";
// First column: Contact Details
echo "{$incident->forenames} {$incident->surname} of {$site_name}<br />\n";
echo "<a href='mailto:{$incident->email}'>{$incident->email}</a><br />\n";
if ($incident->ccemail != '') echo "CC: <a href='mailto:{$incident->ccemail}'>{$incident->ccemail}</a><br />\n";
if ($incident->phone!='' OR $incident->phone!='')
{
    if ($incident->phone!='') echo "Tel: {$incident->phone}";
    if ($incident->mobile!='') echo " Mob: {$incident->mobile}";
    echo "<br />\n";
}
if ($incident->externalid != '')
{
    echo "External ID: ";
    echo format_external_id($incident->externalid)."<br />\n";
}
if ($incident->externalengineer != '')
{
    echo "{$incident->externalengineer}";
    if ($incident->externalemail != '') echo ", <a href='mailto:{$incident->externalemail}'>{$incident->externalemail}</a>";
    echo "<br />\n";
}
echo "</td>";

echo "<td>";
// Second column, Product and Incident details
if ($incident->owner != $sit[2] OR ($incident->towner > 0 AND $incident->towner != $incident->owner))
{
    echo "Owner: <strong>".user_realname($incident->owner)."</strong> ";
    $incidentowner_phone = user_phone($incident->owner);
    if ($incidentowner_phone != '') echo "(Tel: {$incidentowner_phone}) ";
    if ($incident->towner > 0 AND $incident->towner != $incident->owner)
    {
       echo "(Temp: ".user_realname($incident->towner).")";
    }
    echo "<br />";
}
if ($software_name!='' OR $incident->productversion != '' OR $incident->productservicepacks!='')
{
    echo "{$software_name}";
    if ($incident->productversion != '' OR $incident->productservicepacks!='')
    {
        echo " ({$incident->productversion}";
        if ($incident->productservicepacks!='') echo "{$incident->productservicepacks}";
        echo ")";
    }
    echo "<br />\n";
}
if ($incident->priority==1) echo "<img src='{$CONFIG['application_webpath']}images/low_priority.gif' width='10' height='16' alt='Low Priority' title='Low Priority' /> ";
elseif ($incident->priority==2) echo "<img src='{$CONFIG['application_webpath']}images/med_priority.gif' width='10' height='16' alt='Medium Priority' title='Medium Priority' /> ";  // Medium
elseif ($incident->priority==3) echo "<img src='{$CONFIG['application_webpath']}images/high_priority.gif' width='10' height='16' alt='High Priority' title='High Priority' /> ";  // High
elseif ($incident->priority==4) echo "<img src='{$CONFIG['application_webpath']}images/crit_priority.gif' width='16' height='16' alt='Critical Priority' title='Critical Priority' />  ";  // Critical
if ($product_name!='') echo "{$product_name} / ";
echo "{$servicelevel_tag}<br />\n";
echo "Open for {$opened_for}, ";
echo incidentstatus_name($incident->status);
if ($incident->status == 2) echo " (" . closingstatus_name($incident->closingstatus) . ")";
echo "<br />\n";

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
echo "</td>";
echo "</tr>\n";


$rsql = "SELECT * FROM relatedincidents WHERE incidentid='$id' OR relatedid='$id'";
$rresult = mysql_query($rsql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
if (mysql_num_rows($rresult) >= 1)
{
    echo "<tr><td colspan='2'>Related incidents: ";
    while ($related = mysql_fetch_object($rresult))
    {
        if ($related->relatedid==$id)
        {
            echo "<a href='incident_details.php?id={$related->incidentid}' title='??'>{$related->incidentid}</a> ";
        }
        else echo "<a href='incident_details.php?id={$related->relatedid}' title='$relationship'>{$related->relatedid}</a> ";
        echo " &nbsp;";
    }
    echo "</td></tr>";

}

echo "</table>";
echo "</div>\n\n";



?>
