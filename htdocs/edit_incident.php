<?php
// edit_incident.php - Form for editing incident title and other fields
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Soon to be replaced
// See incident/edit.inc.php

$permission=7; // Edit Incidents

require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

// External variables
$submit = $_REQUEST['submit'];
$id = cleanvar($_REQUEST['id']);

// No submit detected show edit form
if (empty($submit))
{
    $title = 'Edit: '.$id . " - " . incident_title($id);
    include('incident_html_top.inc.php');

    // extract incident details
    $sql  = "SELECT * FROM incidents WHERE id='$id'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $incident = mysql_fetch_array($result);

    // SUPPORT INCIDENT
    if ($incident["type"] == "Support")
    {
        ?>
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" name="editform">
        <table class='vertical'>
        <tr><th>Edit Incident:</th><td><h3><?php echo $id; ?></h3></td></tr>
        <tr><th>Title:</th><td><input maxlength='150' name="title" size='40' type="text" value="<?php echo stripslashes($incident['title']) ?>" /></td></tr>
        <tr><th>Important:</th>
        <td>Changing the contact or software will not reassign this incident to another contract.
        <?php
        if ($incident['maintenanceid'] >= 1) echo "This incident is logged under contract {$incident['maintenanceid']}. ";
        else echo "This incident is not logged under contract. ";
        echo "To change the contract log a new incident and close this one.";
        ?>
        </td></tr>
        <tr><th>Contact:</th><td><?php echo contact_drop_down("contact", $incident["contact"], TRUE); ?></td></tr>
        <?php
        flush();
        $maintid=maintenance_siteid($incident['maintenanceid']);
        echo "<tr><th>Site:</th><td>".site_name($maintid)."</td></tr>";
        ?>
        <tr><th>Software:</th>
        <td><?php echo software_drop_down("software", $incident["softwareid"]); flush(); ?></td></tr>
        <tr><th>Software Version:</th>
        <td><input maxlength='50' name="productversion" size='30' type="text" value="<?php echo $incident["productversion"] ?>" /></td></tr>
        <tr><th>Service Packs Applied:</th>
        <td><input maxlength='100' name="productservicepacks" size='30' type="text" value="<?php echo $incident["productservicepacks"] ?>" /></td></tr>
        <tr><th>CC Email:</th>
        <td><input maxlength='255' name="ccemail" size='30' type="text" value="<?php echo $incident["ccemail"] ?>" /></td></tr>
        <tr><th>External ID:</th>
        <td><input maxlength='50' name="externalid" size='30' type="text" value="<?php echo $incident["externalid"] ?>" /></td></tr>
        <tr><th>External Engineers Name:</th>
        <td><input maxlength='80' name="externalengineer" size='30' type="text" value="<?php echo $incident["externalengineer"] ?>" /></td></tr>
        <tr><th>External Email:</th>
        <td><input maxlength='255' name="externalemail" size='30' type="text" value="<?php echo $incident["externalemail"] ?>" /></td></tr>
        </table>
        <p align='center'>
        <input name="type" type="hidden" value="Support" />
        <input name="id" type="hidden" value="<?php echo $id; ?>" />
        <input name="oldtitle" type="hidden" value="<?php echo $incident["title"] ?>" />
        <input name="oldcontact" type="hidden" value="<?php echo $incident["contact"] ?>" />
        <input name="oldccemail" type="hidden" value="<?php echo $incident["ccemail"] ?>" />
        <input name="oldexternalid" type="hidden" value="<?php echo $incident["externalid"] ?>" />
        <input name="oldexternalengineer" type="hidden" value="<?php echo $incident["externalengineer"] ?>" />
        <input name="oldexternalemail" type="hidden" value="<?php echo $incident["externalemail"] ?>" />
        <input name="oldpriority" type="hidden" value="<?php echo $incident["priority"] ?>" />
        <input name="oldstatus" type="hidden" value="<?php echo $incident["status"] ?>" />
        <input name="oldproductversion" type="hidden" value="<?php echo $incident["productversion"] ?>" />
        <input name="oldproductservicepacks" type="hidden" value="<?php echo $incident["productservicepacks"] ?>" />
        <input name="oldsoftware" type="hidden" value="<?php echo $incident["softwareid"] ?>" />
        <input name="submit" type="submit" value="Save" /></p>
        </form>
        <?php
    }
    include('incident_html_bottom.inc.php');
}
else
{
    // External variables
    $externalid = cleanvar($_POST['externalid']);
    $type = cleanvar($_POST['type']);
    $ccemail = cleanvar($_POST['ccemail']);
    $externalengineer = cleanvar($_POST['externalengineer']);
    $externalemail = cleanvar($_POST['externalemail']);
    $title = cleanvar($_POST['title']);
    $contact = cleanvar($_POST['contact']);
    $software = cleanvar($_POST['software']);
    $productversion = cleanvar($_POST['productversion']);
    $productservicepacks = cleanvar($_POST['productservicepacks']);
    $id = cleanvar($_POST['id']);
    $oldtitle = cleanvar($_POST['oldtitle']);
    $oldcontact = cleanvar($_POST['oldcontact']);
    $maintid = cleanvar($_POST['maintid']);
    $oldexternalid = cleanvar($_POST['oldexternalid']);
    $oldexternalemail = cleanvar($_POST['oldexternalemail']);
    $oldproduct = cleanvar($_POST['oldproduct']);
    $oldproductversion = cleanvar($_POST['oldproductversion']);
    $oldproductservicepacks = cleanvar($_POST['oldproductservicepacks']);
    $oldccemail = cleanvar($_POST['oldccemail']);
    $oldexternalengineer = cleanvar($_POST['oldexternalengineer']);
    $oldsoftware = cleanvar($_POST['oldsoftware']);

    // Edit the incident
    if ($type == "Support")  // FIXME: This IF might not be needed since sales incidents are obsolete INL 29Apr03
    {
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

        if ($errors > 0)
        {
            echo "<div>$bodytext</div>";
        }

        if ($errors == 0)
        {
            $addition_errors = 0;

            // update support incident
            $sql = "UPDATE incidents SET externalid='$externalid', ccemail='$ccemail', ";
            $sql .= "externalengineer='$externalengineer', externalemail='$externalemail', title='$title', ";
            $sql .= "contact='$contact', softwareid='$software', productversion='$productversion', ";
            $sql .= "productservicepacks='$productservicepacks' WHERE id='$id'";
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
                if ($oldcontact != $contact)
                {
                    $contactname = contact_realname($contact);
                    $contactsite = contact_site($contact);
                    $header .= "Contact: " . contact_realname($oldcontact) . " -&gt; <b>{$contactname}</b>\n";
                    $maintsiteid = maintenance_siteid(incident_maintid($id));
                    if ($maintsiteid > 0 AND contact_siteid($contact) != $maintsiteid)
                    {
                        $maintcontactsite = site_name($maintsiteid);
                        $header .= "Assigned to <b>{$contactname} of {$contactsite}</b> on behalf of {$maintcontactsite} (The contract holder)\n";
                    }
                }
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
                if ($oldccemail != $ccemail) $header .= "CC Email: " . $oldccemail . " -&gt; <b>" . $ccemail . "</b>\n";
                if ($oldexternalengineer != $externalengineer) $header .= "External Engineer: " . $oldexternalengineer . " -&gt; <b>" . $externalengineer . "</b>\n";
                if ($oldexternalemail != $externalemail) $header .= "External email: " . $oldexternalemail . " -&gt; <b>" . $externalemail . "</b>\n";
                if ($oldsoftware != $software) $header .= "Software: ".software_name($oldsoftware)." -&gt; <b>".software_name($software)."</b>\n";
                if ($oldproductversion != $productversion) $header .= "Software Version: $oldproductversion -&gt; <b>$productversion</b>\n";
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
                confirmation_page("2", "incident_details.php?id=" . $id, "<h2>Update Successful</h2><p align='center'>Please wait while you are redirected...</p>");
            }
            else
            {
                include('incident_html_top.inc.php');
                echo $addition_errors_string;
                include('incident_html_bottom.inc.php');
            }
        }
        else
        {
            include('incident_html_top.inc.php');
            echo $error_string;
            include('incident_html_bottom.inc.php');
        }
    }
}
?>