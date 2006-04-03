<?php
// htmlheader.inc.php - Header html to be included at the top of pages
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// This Page Is Valid XHTML 1.0 Transitional! 27Oct05

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<!-- SiT (Support Incident Tracker) - Support call tracking system
     Copyright (C) 2000-2006 Salford Software Ltd.

     This software may be used and distributed according to the terms
     of the GNU General Public License, incorporated herein by reference. -->
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
<?php
echo "<meta name=\"GENERATOR\" content=\"{$CONFIG['application_name']} {$application_version_string}\" />\n";
echo "<title>";
if (isset($title)) { echo "$title - {$CONFIG['application_shortname']}"; } else { echo "{$CONFIG['application_name']}{$extratitlestring}"; }
echo "</title>\n";
echo "<link rel='SHORTCUT ICON' href='{$CONFIG['application_webpath']}images/sit_favicon.png' />\n";
echo "<style type='text/css'>@import url('{$CONFIG['application_webpath']}styles/webtrack.css');</style>\n";
if ($_SESSION['auth'] == TRUE)
{
    $style = interface_style($_SESSION['style']);
    echo "<link rel='stylesheet' href='{$CONFIG['application_webpath']}styles/{$style['cssurl']}' />\n";
}
else
{
    echo "<link rel='stylesheet' href='{$CONFIG['application_webpath']}styles/webtrack1.css' />\n";
}

if (isset($refresh) && $refresh != 0)
{
   echo "<meta http-equiv=\"refresh\" content=\"$refresh\" />\n";
}
echo "<script src='{$CONFIG['application_webpath']}webtrack.js' type='text/javascript'></script>\n";
echo "</head>\n";
echo "<body>\n";
echo "<h1 id='apptitle'>{$CONFIG['application_name']}</h1>\n";
// Show menu if logged in
if ($sit[0]!='') build_htopmenu(0);
    /*
        echo "<div id='navmenu'>";
        build_topmenu($permission);
        echo "</div>\n";
    */
?>
