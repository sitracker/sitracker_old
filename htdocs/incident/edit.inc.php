<?php
/*
incident/edit.inc.php - Form to edit an incident, included by ../incident.php

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
    <?php
    echo "<tr><th>Escalation</th>";
    echo "<td>".escalation_path_drop_down('escalationpath', $incident['escalationpath'])."</td></tr>";
    ?>
    <tr><th>External ID:</th>
    <td><input maxlength='50' name="externalid" size='30' type="text" value="<?php echo $incident["externalid"] ?>" /></td></tr>
    <tr><th>External Engineers Name:</th>
    <td><input maxlength='80' name="externalengineer" size='30' type="text" value="<?php echo $incident["externalengineer"] ?>" /></td></tr>
    <tr><th>External Email:</th>
    <td><input maxlength='255' name="externalemail" size='30' type="text" value="<?php echo $incident["externalemail"] ?>" /></td></tr>
    <?php
        plugin_do('edit_incident_form');
    ?>
    </table>

    <p align='center'>
    <input name="type" type="hidden" value="Support" />
    <input name="id" type="hidden" value="<?php echo $id; ?>" />
    <input name="oldtitle" type="hidden" value="<?php echo stripslashes($incident["title"]) ?>" />
    <input name="oldcontact" type="hidden" value="<?php echo $incident["contact"] ?>" />
    <input name="oldccemail" type="hidden" value="<?php echo $incident["ccemail"] ?>" />
    <input name="oldescalationpath" type="hidden" value="<?php echo db_read_column('name', 'escalationpaths', $incident["escalationpath"]) ?>" />
    <input name="oldexternalid" type="hidden" value="<?php echo $incident["externalid"] ?>" />
    <input name="oldexternalengineer" type="hidden" value="<?php echo $incident["externalengineer"] ?>" />
    <input name="oldexternalemail" type="hidden" value="<?php echo $incident["externalemail"] ?>" />
    <input name="oldpriority" type="hidden" value="<?php echo $incident["priority"] ?>" />
    <input name="oldstatus" type="hidden" value="<?php echo $incident["status"] ?>" />
    <input name="oldproductversion" type="hidden" value="<?php echo $incident["productversion"] ?>" />
    <input name="oldproductservicepacks" type="hidden" value="<?php echo $incident["productservicepacks"] ?>" />
    <input name="oldsoftware" type="hidden" value="<?php echo $incident["softwareid"] ?>" />
    <input type="hidden" name='action' value="save-edit" />
    <input name="submit" type="submit" value="Save" /></p>
    </form>
    <?php
}
?>
