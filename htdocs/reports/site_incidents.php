<?php
// site_incidents.php - csv file showing how many incidents each site logged
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

$permission=37; // Run Reports
$title='Number of incidents per site';
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

$sql = "SELECT DISTINCT sites.id, sites.name as name, resellers.name as resel FROM sites, maintenance, resellers ";
$sql.= "WHERE sites.id=maintenance.site AND resellers.id=maintenance.reseller AND maintenance.term<>'yes' ORDER BY name";
$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
while ($site = mysql_fetch_object($result))
{
    $sql = "SELECT count(incidents.id) AS incidentz, sites.name as site FROM contacts, sites, incidents ";
    $sql.= "WHERE contacts.siteid=sites.id AND sites.id={$site->id} AND incidents.opened > ($now-60*60*24*365.25) AND incidents.contact=contacts.id ";
    $sql.= "GROUP BY site";
    $sresult = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    $details=mysql_fetch_object($sresult);
    $count=1*($details->incidentz);
    $csv .="$count,'{$site->name},'{$site->resel}'\n";
}
header("Content-type: text/csv\r\n");
header("Content-disposition-type: attachment\r\n");
header("Content-disposition: filename=yearly_incidents.csv");
echo "incidents, site, reseller\n";
echo $csv;
?>
