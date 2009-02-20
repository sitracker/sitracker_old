<?php
// export.inc.php - functions relating to exporting data in various formats
//                  e.g. RSS/vcard/XML etc.
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}


class feed
{
    var $title = '';
    var $feedurl = '';
    var $description = '';
    var $pubdate = '';


}



/**
  * Return XML for an RSS feed

*/
function rss_feed($feedobject)
{
    print_r($feedobject);
    global $CONFIG, $application_version_string;

    if (!empty($_SESSION['lang'])) $lang = $_SESSION['lang'];
    else $lang = $CONFIG['default_i18n'];

    $xml = '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
    $xml .= "<channel><title>{$feedobject->title}</title>\n";
    $xml .= '<link>'.application_url()."</link>\n";
    $xml .= '<atom:link href="'.application_url()."incidents_rss.php?c={$c}\" rel=\"self\" type=\"application/rss+xml\" />\n";
    $xml .= "<description>{$feedobject->description}</description>\n";
    $xml .= "<language>{$lang}</language>\n";
    $xml .= "<pubDate>{$feedobject->pubdate}</pubDate>\n";
    $xml .= "<lastBuildDate>{$feedobject->pubdate}</lastBuildDate>\n";
    $xml .= '<docs>http://blogs.law.harvard.edu/tech/rss</docs>';
    $xml .= "<generator>{$CONFIG['application_name']} {$application_version_string}</generator>\n";
    $xml .= "<webMaster>".user_email($CONFIG['support_manager'])." (Support Manager)</webMaster>\n";

    $xml .= $itemxml;
    if (is_array($feedobject->items))
    {
        foreach ($feedobject->items AS $item)
        {
            print_r($item);

        }


    }

    $xml .= "</channel></rss>\n";
    return $xml;
}

?>