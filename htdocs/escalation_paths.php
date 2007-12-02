<?php
// escalation_paths.php - List escalation paths
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

//// This Page Is Valid XHTML 1.0 Transitional!  (7 Oct 2006)

$permission=64; // Manage escalation paths

require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

$title = $strEscalationPaths;

include('htmlheader.inc.php');
echo "<h2>$title</h2>";

$sql = "SELECT * FROM escalationpaths ORDER BY name";
$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
if (mysql_num_rows($result) >= 1)
{
    echo "<table align='center'>";
    echo "<tr>";
    echo colheader('name',$strName);
    echo colheader('track_url','Track URL'); // FIXME i18n, Track URL, Home URL, URL TItle
    echo colheader('home_url','Home URL');
    echo colheader('url_title',"URL {$strTitle}");
    echo colheader('email_domain','Email Domain');
    echo colheader('edit',$strOperation);
    echo "</tr>";
    while ($path = mysql_fetch_object($result))
    {
        echo "<tr>";
        echo "<td>{$path->name}</td>";
        echo "<td>{$path->track_url}</td>";
        echo "<td>{$path->home_url}</td>";
        echo "<td>{$path->url_title}</td>";
        echo "<td>{$path->email_domain}</td>";
        echo "<td><a href='edit_escalation_path.php?id={$path->id}'>{$strEdit}</a></td>";
        echo "</tr>";
    }
    echo "</table>";
}
else echo "<p align='center'>{$strNoRecords}</p>";

echo "<p align='center'><a href='add_escalation_path.php'>{$strAdd}</a></p>";

include('htmlfooter.inc.php');

?>
