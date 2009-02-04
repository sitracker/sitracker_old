<?php
// incidents_rss.php - Output an RSS representation of a users incident queue
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2006-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// This feature is experimental as of 22Sep06

$lib_path = dirname( __FILE__ ).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR;
require ($lib_path.'db_connect.inc.php');
require ($lib_path.'functions.inc.php');

// This script requires no authentication
// The information it reveals should not be sensitive

$userid = cleanvar($_REQUEST['user']);

if (!is_numeric($userid)) trigger_error('Invalid userid', E_USER_ERROR);

$sql = "SELECT * FROM `{$dbIncidents}` WHERE (owner='$userid' OR towner='$userid') ";
$sql .= "AND (status!='2') LIMIT 5";  // not closed

$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

$xml = '<rss version="2.0">';
$xml .= '<channel><title>SiT Incidents</title>';
$xml .= '<link>http://localhost/sit/</link>';
$xml .= "<description>SiT incidents list for ".user_realname($userid)."</description>";
$xml .= '<language>en-us</language>';
$xml .= "<pubDate>".date('r',$now)."</pubDate>";
$xml .= "<lastBuildDate>".date('r',$now)."</lastBuildDate>";
$xml .= '<docs>http://blogs.law.harvard.edu/tech/rss</docs>';
$xml .= "<generator>{$CONFIG['application_name']} {$application_version_string}</generator>";
$xml .= "<webMaster>".user_email($CONFIG['support_manager'])."</webMaster>";

while ($incident = mysql_fetch_object($result))
{
    // Get Last Update
    list($update_userid, $update_type, $update_currentowner, $update_currentstatus, $update_body, $update_timestamp, $update_nextaction, $update_id)=incident_lastupdate($incident->id);

    $xml .= "<item>";
    $xml .= "<title>{$incident->id} {$update_type}: {$incident->title}</title>";
    $xml .= "<link>{$CONFIG['application_uriprefix']}{$CONFIG['application_webpath']}incident_details.php?id={$incident->id}</link>";
    $xml .= "<description>Incident updated ".date($CONFIG['dateformat_datetime'],$update_timestamp).". Status: ".incidentstatus_name($update_currentstatus)."</description>";
    $xml .= "<pubDate>".date('r',$update_timestamp)."</pubDate>";
    $xml .= "<guid>http://localhost/2003/06/03.html#item{$update_id}</guid>";
    $xml .= "</item>";
}

$xml .= "</channel></rss>";

header("Content-Type: application/xml");
echo $xml;
?>
