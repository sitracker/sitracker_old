<?php
/*
incident/edit.inc.php - Form to edit an incident, included by ../incident.php

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

echo "<h2>Edit Incident</h2>";

echo "<form action='{$_SERVER['PHP_SELF']}' method='post' name='editform'>";
echo "<table align='center' class='vertical'>";
echo "<tr><th>Edit Incident:</th><td>{$id}</td></tr>";
echo "<tr><th>Title:</th><td><input maxlength='150' name='title' size='40' type='text' value='{$incident->title}' /></td></tr>";
echo "<tr><th>Important:</th><td>Changing the contact, product or software will not reassign this incident to another ";
echo "maintenance contract. This incident is currently logged under contract {$incident->maintenanceid}. ";
echo "To change the contract log a new incident and close this one.</td></tr>";

echo "<tr><th>Contact:</th><td>".contact_drop_down("contact", $incident->contact)."</td></tr>";
flush();
echo "<tr><th>Product:</th><td>".supported_product_drop_down("product", $incident->contact, $incident->product)."</td></tr>";
flush();
echo "<tr><th>Skill:</th><td>".software_drop_down("software", $incident->softwareid)."</td></tr>";
flush();
echo "<tr><th>Version:</th><td><input maxlength='50' name='productversion' size='30' type='text' value='{$incident->productversion}' /></td></tr>";
echo "<tr><th>Service Packs Applied:</th><td><input maxlength='100' name='productservicepacks' size='30' type='text' value='{$incident->productservicepacks}' /></td></tr>";
echo "<tr><th>External ID:</th><td><input maxlength='50' name='externalid' size='30' type='text' value='{$incident->externalid}' /></td></tr>";
echo "<tr><th>External Engineers Name:</th><td><input maxlength='80' name='externalengineer' size='30' type='text' value='{$incident->externalengineer}' /></td></tr>";
echo "<tr><th>External Email:</th><td><input maxlength='255' name='externalemail' size='30' type='text' value='{$incident->externalemail}' /></td></tr>";
echo "</table>\n";
echo "<input name='type' type='hidden' value='Support' />";
echo "<input name='oldtitle' type='hidden' value='{$incident->title}' />";
echo "<input name='oldcontact' type='hidden' value='{$incident->contact}' />";
echo "<input name='oldexternalid' type='hidden' value='{$incident->externalid}' />";
echo "<input name='oldexternalengineer' type='hidden' value='{$incident->externalengineer}' />";
echo "<input name='oldexternalemail' type='hidden' value='{$incident->externalemail}' />";
echo "<input name='oldpriority' type='hidden' value='{$incident->priority} />";
echo "<input name='oldstatus' type='hidden' value='{$incident->status}' />";
echo "<input name='oldproduct' type='hidden' value='{$incident->product} />";
echo "<input name='oldproductversion' type='hidden' value='{$incident->productversion}' />";
echo "<input name='oldproductservicepacks' type='hidden' value='{$incident->productservicepacks}' />";
echo "<input name='id' type='hidden' value='{$id}' />";
echo "<input name='action' type='hidden' value='save-edit' />";
echo "<p align='center'><input name='submit' type='submit' value='{$strSave}' /></p>";
echo "</form>\n";
?>
