<?php
// incident.php - Main Incident Display page with tabs for working with incidents
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.


// incident framework
$permission=61; // View Incident Details
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

// External Variables
$incidentid = cleanvar($_REQUEST['id']);
$selectedtab = cleanvar($_REQUEST['tab']);
$selectedaction = cleanvar($_REQUEST['action']);


// Actions
switch($_REQUEST['action'])
{
    case 'save-edit':
        // External Variables
        $id = cleanvar($_POST['id']);
        $contact = cleanvar($_POST['contact']);
        $externalid = cleanvar($_POST['externalid']);
        $externalemail = cleanvar($_POST['externalemail']);
        $title = cleanvar($_POST['title']);
        $product = cleanvar($_POST['product']);
        $software = cleanvar($_POST['software']);
        $productversion = cleanvar($_POST['productversion']);
        $productservicepacks = cleanvar($_POST['productservicepacks']);
        $oldtitle = cleanvar($_POST['oldtitle']);
        $oldcontact = cleanvar($_POST['oldcontact']);
        $oldexternalid = cleanvar($_POST['oldexternalid']);
        $oldexternalengineer = cleanvar($_POST['oldexternalengineer']);
        $oldexternalemail = cleanvar($_POST['oldexternalemail']);
        $oldproduct = cleanvar($_POST['oldproduct']);
        $oldproductversion = cleanvar($_POST['oldproductvesion']);
        $oldproductservicepacks = cleanvar($_POST['oldproductservicepacks']);

        // check form input
        $errors = 0;
        // check for blank contact
        if ($contact == 0)
        {
            $errors = 1;
            $error_string .= "<p class='error'>You must select a contact</p>\n";
        }
        // check for blank title
        if ($title == "")
        {
            $errors = 1;
            $error_string .= "<p class='error'>You must enter a title</p>\n";
        }
        if ($errors == 0)
        {
            $addition_errors = 0;
            // update support incident
            $sql = "UPDATE incidents SET externalid='$externalid', externalengineer='$externalengineer', ";
            $sql .= "externalemail='$externalemail', title='$title', contact='$contact', product='$product', ";
            $sql .= "softwareid='$software', productversion='$productversion', productservicepacks='$productservicepacks' ";
            $sql .= "WHERE id='$id'";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            if (!$result)
            {
                $addition_errors = 1;
                $addition_errors_string .= "<p class='error'>Update of incident failed</p>\n";
            }
            if ($addition_errors == 0)
            {
                // dump details to incident update
                if ($oldtitle != $title) $header .= "Title: $oldtitle -&gt; <b>$title</b>\n";
                if ($oldcontact != $contact) $header .= "Contact: " . contact_realname($oldcontact) . " -&gt; <b>" . contact_realname($contact) . "</b>\n";
                if ($oldexternalid != $externalid)
                {
                    $header .= "External ID: ";
                    if ($oldexternalid != "")
                        $header .= $oldexternalid;
                    else
                        $header .= "None";
                    $header .= " -&gt; <b>";
                    if ($externalid != "")
                        $header .= $externalid;
                    else
                        $header .= "None";
                    $header .= "</b>\n";
                }
                if ($oldexternalengineer != $externalengineer) $header .= "External Engineer: " . $oldexternalengineer . " -&gt; <b>" . $externalengineer . "</b>\n";
                if ($oldexternalemail != $externalemail) $header .= "External email: " . $oldexternalemail . " -&gt; <b>" . $externalemail . "</b>\n";
                if ($oldproduct != $product) $header .= "Product: " . product_name($oldproduct) . " -&gt; <b>" . product_name($product) . "</b>\n";
                if ($oldproductversion != $productversion) $header .= "Product Version: $oldproductversion -&gt; <b>$productversion</b>\n";
                if ($oldproductservicepacks != $productservicepacks) $header .= "Service Packs Applied: $oldproductservicepacks -&gt; <b>$productservicepacks</b>\n";

                if (!empty($header)) $header .= "<hr>";
                $bodytext = $header . $bodytext;
                $bodytext = mysql_escape_string($bodytext);
                $sql  = "INSERT INTO updates (incidentid, userid, type, bodytext, timestamp) ";
                $sql .= "VALUES ('$id', '$sit[2]', 'editing', '$bodytext', '$now')";
                $result = mysql_query($sql);
                if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

                if (!$result)
                {
                    $addition_errors = 1;
                    $addition_errors_string .= "<p class='error'>Addition of incident update failed</p>\n";
                }
            }

            if ($addition_errors == 0)
            {
                journal(CFG_LOGGING_NORMAL, 'Incident Edited', "Incident $id was edited", CFG_JOURNAL_INCIDENTS, $id);
                $msg .= "<p class='info'>Incident Edited Successfully</p>";
            }
            else
            {
                trigger_error($addition_errors_string, E_USER_ERROR);
            }
        }
    break;
}



if ($incidentid=='' OR $incidentid < 1) trigger_error("Blank or Zero Incident ID received", E_USER_ERROR);

// Retrieve incident
// extract incident details
$sql  = "SELECT *, incidents.id AS incidentid, ";
$sql .= "contacts.id AS contactid ";
$sql .= "FROM incidents, contacts ";
$sql .= "WHERE (incidents.id='{$incidentid}' AND incidents.contact=contacts.id) ";
$sql .= " OR incidents.contact=NULL ";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
$incident = mysql_fetch_object($result);
$site_name=site_name($incident->siteid);
$product_name=product_name($incident->product);
if ($incident->softwareid > 0) $software_name=software_name($incident->softwareid);
$servicelevel_id=maintenance_servicelevel($incident->maintenanceid);
$servicelevel_name=servicelevel_name($servicelevelid);
$opened_for=format_seconds(time() - $incident->opened);

$title = "{$id} - {$incident->title}";
include('incident_html_top.inc.php');

// Print info message if set
if (!empty($msg)) echo $msg;


include('incident/details.inc.php');


// Incident locking
//
// Three states of locking
// - unlocked
// - locked by somebody else
// - locked by user
$lockexpires=$incident->locktime + $CONFIG['record_lock_delay'];
if ($lockexpires > time() AND $incident->locked >0 AND $incident->locked != $sit[2])
{
    // The incident is locked by somebody else
    echo "<p><img src='images/lock.png' width='16' height='16' alt='Locked' style='border:0px;' /> ";
    echo "This incident is locked by ".user_realname($incident->locked)." until ".date('H:i',$lockexpires)." (".format_seconds($lockexpires).")</p>";
    $locked=TRUE;
}
elseif ($incident->locked == 0 OR $lockexpires <= time())
{
    // Incident unlocked
    // Check to see if we've requested to lock it
    if ($_REQUEST['action']=='lock')
    {
        // Yep, the user would like this locked, so lets lock it
        $lock_sql = "UPDATE incidents SET locktime='".time()."', locked='{$sit[2]}' WHERE id='{$incidentid}' LIMIT 1";
        mysql_query($lock_sql);
        if (mysql_error()) trigger_error(mysql_error(), E_USER_ERROR);
        if (mysql_affected_rows() < 1) trigger_error("Failed to lock incident (id: {$incidentid})", E_USER_WARNING);
        // Incident locked by this user, display form
        include('incident/action.inc.php');
    }
    else
    {
        // Offer option to lock the incident
        echo "<a href='{$_SERVER['PHP_SELF']}?id={$incidentid}&amp;action=lock'><img src='images/action.png' width='17' height='17' alt='Action Button' style='border:0px;' />";
    }
}
else
{
    // Incident already locked by this user
    // Check to see if we've requested to unlock it
    if ($_REQUEST['action']=='unlock')
    {
        $lock_sql = "UPDATE incidents SET locktime='0', locked='0' WHERE id='{$incidentid}' LIMIT 1";
        mysql_query($lock_sql);
        if (mysql_error()) trigger_error(mysql_error(), E_USER_ERROR);
        if (mysql_affected_rows() < 1) trigger_error("Failed to update lock on incident (id: {$incidentid})", E_USER_WARNING);
        echo "<a href='{$_SERVER['PHP_SELF']}?id={$incidentid}&amp;action=lock'><img src='images/action.png' width='17' height='17' alt='Action Button' style='border:0px;' />";
    }
    else
    {
        // Refresh the lock time, keep the incident locked longer while we're using it
        $lock_sql = "UPDATE incidents SET locktime='".time()."' WHERE id='{$incidentid}' LIMIT 1";
        mysql_query($lock_sql);
        if (mysql_error()) trigger_error(mysql_error(), E_USER_ERROR);
        if (mysql_affected_rows() < 1) trigger_error("Failed to update lock on incident (id: {$incidentid})", E_USER_WARNING);

        include('incident/action.inc.php');
        echo "<p><img src='images/lock.png' width='16' height='16' alt='Locked' style='border:0px;' /> ";
        echo "You have a lock on this incident, <a href='{$_SERVER['PHP_SELF']}?id={$incidentid}&amp;action=unlock'>unlock it</a> to allow others to make changes.</p>";
        //, display form

    }
}

// debug
// echo "<div><br /><br /><p>Selected tab: {$selectedtab} / Action: {$selectedaction}</p><br /><br /></div>";
// /debug

$tabsarray=array('Log' => "{$_SERVER['PHP_SELF']}?id={$incidentid}&tab=log&action={$selectedaction}",
                'Files' => "{$_SERVER['PHP_SELF']}?id={$incidentid}&tab=files&action={$selectedaction}",
                'Update' => "update_incident.php?id={$incidentid}",
                'Edit' => "{$_SERVER['PHP_SELF']}?id={$incidentid}&tab=edit&action={$selectedaction}",
                'Reassign' => "reassign_incident.php?id={$incidentid}",
                'Send Email' => "{$_SERVER['PHP_SELF']}?id={$incidentid}&tab=edit&action={$selectedaction}",
                'Customer_View' => "{$_SERVER['PHP_SELF']}?id={$incidentid}&tab=customer_view&action={$selectedaction}",
                'SLA' => "{$_SERVER['PHP_SELF']}?id={$incidentid}&tab=SLA&action={$selectedaction}",
                'Close' => "close_incident.php?id={$incidentid}");
// FIXME these tabs need converting to submit tags or they will lose the info currently entered in the form
echo draw_tabs($tabsarray, $selectedtab);



// Tabs
switch($_REQUEST['tab'])
{
    case 'files': include('incident/files.inc.php'); break;
    case 'SLA': include('incident/sla.inc.php'); break;
    case 'edit': include('incident/edit.inc.php'); break;
    case 'relationships': include('incident/relationships.inc.php'); break;
    case 'log':
    default:
        include('incident/log.inc.php');
}

include('incident_html_bottom.inc.php');
?>