<?php
// control_panel.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

@include ('set_include_path.inc.php');
$permission = 4; // Edit your profile

require ('db_connect.inc.php');
require ('functions.inc.php');
// This page requires authentication
require ('auth.inc.php');

include ('htmlheader.inc.php');
echo "<h2>{$CONFIG['application_shortname']} {$strControlPanel}</h2>";
echo "<table align='center'>";
echo "<thead>";
echo "<tr><th>USER SETTINGS</th></tr>";
echo "</thead>";
echo "<tbody>";
echo "<tr><td><a href='edit_profile.php'>{$strMyProfile}</a></td></tr>";
echo "<tr><td><a href='edit_user_skills.php'>{$strMySkills}</a></td></tr>";
echo "<tr><td><a href='edit_backup_users.php'>{$strMySubstitutes}</a></td></tr>";
echo "<tr><td><a href='holidays.php'>{$strMyHolidays}</a></td></tr>";
echo "</tbody>\n";
if (user_permission($sit[2],42)) // Review/Delete Incident Updates
{
    echo "<thead><tr><th>TECHNICAL SUPPORT ADMIN</th></tr></thead>";
    echo "<tbody><tr><td><a href='review_incoming_updates.php'>{$strHoldingQueue}</a></td></tr></tbody>";
}

if (user_permission($sit[2],44)) // FTP Publishing
{
    echo "<thead><tr><th>{$strFiles}</th></tr></thead>";
    echo "<tbody><tr><td><a href='ftp_list_files.php'>Manage FTP Files</a></td></tr></tbody>";// FIXME i18n manage ftp files
}
if (user_permission($sit[2],50)) // Approve holidays
{
    echo "<thead><tr><th>{$strManageUsers}</th></tr></thead>";
    echo "<tbody><tr><td><a href='holiday_request.php?user=all&mode=approval'>{$strApproveHolidays}</a></td></tr></tbody>";
}
if (user_permission($sit[2],22)) // Administrate
{
    // FIXME i18n
    echo "<thead><tr><th>ADMINISTRATORS ONLY</th></tr></thead>";
    echo "<tbody>";
    echo "<tr><td><a href='manage_users.php'>{$strManageUsers}</a></td></tr>";
    echo "<tr><td><a href='edit_emailtype.php?action=showform'>Manage Email Templates</a></td></tr>";
    echo "<tr><td><a href='browse_journal.php'>Browse {$CONFIG['application_shortname']} Journal</a></td></tr>";
    echo "<tr><td><a href='service_levels.php'>{$strServiceLevels}</a></td></tr>";
    echo "<tr><td><a href='add_productinfo.php?action=showform'>Add Product Information</a></td></tr>";
    echo "<tr><td><a href='holiday_calendar.php?type=10'>{$strSetPublicHolidays}</a></td></tr>";
    echo "<tr><td><a href='site_details.php?id=0&action=show'>Show orphaned contacts (no site)</a></td></tr>";
    echo "</tbody>";
}

plugin_do('cp_menu');
echo "</table>\n";
include ('htmlfooter.inc.php');
?>