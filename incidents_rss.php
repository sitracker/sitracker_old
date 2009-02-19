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


require ('core.php');
require (APPLICATION_LIBPATH . 'functions.inc.php');

// This script requires no authentication
// The information it reveals should not be sensitive

$c = cleanvar($_GET['c']);
$salt = md5($CONFIG['db_password']);
$usql = "SELECT id FROM `{$dbUsers}` WHERE MD5(CONCAT(`username`, '{$salt}')) = '$c' LIMIT 1";
$uresult = mysql_query($usql);

if ($uresult)
{
    list($userid) = mysql_fetch_row($uresult);
}

// $userid = cleanvar($_REQUEST['user']);

if (!is_numeric($userid))
{
    header("HTTP/1.1 403 Forbidden");
    echo "<html><head><title>403 Forbidden</title></head><body><h1>403 Forbidden</h1></body></html>\n";
    exit;
}

$sql = "SELECT * FROM `{$dbIncidents}` WHERE (owner='$userid' OR towner='$userid') ";
$sql .= "AND (status!='".STATUS_CLOSED."') ORDER BY lastupdated DESC LIMIT 5";  // not closed
$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

if (!empty($_SESSION['lang'])) $lang = $_SESSION['lang'];
else $lang = $CONFIG['default_i18n'];

$count = 0;
$pubdate = $now;
while ($incident = mysql_fetch_object($result))
{
    // Get Last Update
    list($update_userid, $update_type, $update_currentowner, $update_currentstatus, $update_body, $update_timestamp, $update_nextaction, $update_id)=incident_lastupdate($incident->id);

    if ($count == 0) $pubdate = date('r',$update_timestamp);

    $authorname = user_realname($update_userid);
    $author = user_email($update_userid)." (".$authorname. ")";

    $itemxml .= "<item>\n";
    $itemxml .= "<title>[{$incident->id}] - {$incident->title} ({$update_type})</title>\n";
    $itemxml .= "<author>{$author}</author>\n";
    $itemxml .= "<link>{$CONFIG['application_uriprefix']}{$CONFIG['application_webpath']}incident_details.php?id={$incident->id}</link>\n";
    $itemxml .= "<description>{$strUpdated} ".date($CONFIG['dateformat_datetime'],$update_timestamp) . ' ';
    $itemxml .= " {$strby} &lt;strong&gt;{$authorname}&lt;/strong&gt;. \n";
    $itemxml .= "{$strStatus}: ".incidentstatus_name($update_currentstatus).". &lt;br /&gt;\n\n";
    $itemxml .= strip_tags($update_body);
    $itemxml .= "</description>\n";
    $itemxml .= "<pubDate>".date('r',$update_timestamp)."</pubDate>\n";
    $itemxml .= "<guid isPermaLink=\"false\">{$CONFIG['application_uriprefix']}{$CONFIG['application_webpath']}incident_details.php?id={$incident->id}#{$update_id}</guid>\n";
    $itemxml .= "</item>\n";
    $count++;
}

$xml = '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
$xml .= "<channel><title>{$CONFIG['application_shortname']} {$strIncidents}</title>\n";
$xml .= '<link>'.application_url()."</link>\n";
$xml .= '<atom:link href="'.application_url()."incidents_rss.php?c={$c}\" rel=\"self\" type=\"application/rss+xml\" />\n";
$xml .= "<description>{$CONFIG['application_name']}: {$strIncidents} {$strFor} ".user_realname($userid)." ({$strActionNeeded}).</description>\n";
$xml .= "<language>{$lang}</language>\n";
$xml .= "<pubDate>{$pubdate}</pubDate>\n";
$xml .= "<lastBuildDate>{$pubdate}</lastBuildDate>\n";
$xml .= '<docs>http://blogs.law.harvard.edu/tech/rss</docs>';
$xml .= "<generator>{$CONFIG['application_name']} {$application_version_string}</generator>\n";
$xml .= "<webMaster>".user_email($CONFIG['support_manager'])." (Support Manager)</webMaster>\n";

$xml .= $itemxml;


$xml .= "</channel></rss>\n";

header("Content-Type: application/xml");
echo $xml;
?>
