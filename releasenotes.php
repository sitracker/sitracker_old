<?php
// releasenotes.php - Release notes summary
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.


$permission = 0;
require ('core.php');
require (APPLICATION_LIBPATH . 'functions.inc.php');
$version = cleanvar($_GET['v']);
//as passed by triggers
$version = str_replace("v", "", $version);
if (!empty($version))
{
    header("Location: {$_SERVER['PHP_SELF']}#{$version}");
}

// This page requires authentication
require (APPLICATION_LIBPATH . 'auth.inc.php');
include_once (APPLICATION_INCPATH . 'htmlheader.inc.php');
echo "<h2>Release Notes</h2>";

echo "<div id='help'>";
echo "<h4>This is a summary of the full release notes showing only the most important changes, for more detailed notes and the latest information on this release please <a href='http://sitracker.org/wiki/ReleaseNotes'>see the SiT website</a>:</h4>";

echo "<h3><a name='3.50'>v3.50</a></h3>";
echo "<div>";
echo "<ul><li>Not yet released</li></ul>";
echo "</div>";

echo "<h3><a name='3.45'>v3.45</a></h3>";
echo "<div>";
echo "<ul>
<li>A long awaited configuration/settings interface</li>
<li>Much improved Inbound Email parsing and connection/error handling</li>
<li>Allow choice of 'inbox' and archive folder for inbound email</li>
<li>End of htdocs! We've rearranged the file layout of SiT; the 'htdocs' folder is no more, making it easier to deploy and to give you a nicer URL</li>
<li>Easier setup - no need to set the application path or include path any more and setup guides you through creating a directory to store attachments</li>
<li>Added ability for portal users to create emails in the holding queue rather than incidents</li>
<li>Added stub translations for: Catalan and Slovenian</li>
<li>Added partial translations for Russian and Mexican Spanish</li>
<li>Updated and improved German and Italian translations</li>
<li>Improved i18n</li>
<li>Improvements to billing</li>
<li>Debug Logging</li>
<li>When emails are received for closed incidents, there is now an option to reopen and add it straight from the holding queue</li>
<li>The list of available languages is now configurable and new languages can be added on-the-fly</li>";
echo "</div>";

echo "<h3><a name='3.41'>v3.41</a></h3>";
echo "<div>";
echo "<ul><li>This was bugfix release and does not contain any new features over v3.40.</li></ul>";
echo "</div>";


echo "<h3><a name='3.40'>v3.40</a></h3>";
echo "<div>";

echo "<ul>
<li>New Danish (da-DK) Translation by Carsten Jensen</li>
<li>Portuguese (pt-PT) Translation by José Tomás</li>
<li>Updated Spanish (es-ES) Translation by Carlos Perez</li>
<li>Ability to receive incoming mail from a POP or IMAP email account</li>
<li>Gravatar support</li>
<li>Billing - highly customisable framework for charging based on incidents and time worked on incidents</li>
<li>Inventory - a cataloguing system for collecting information on remote access and/or assets (servers, PCs etc)</li>
<li>The help menu now has a link to 'Get Help Online' which takes the user to the Documentation page of the wiki, this was done to make it easier for users to find the latest help and also to make it easier for contributors to expand the documentation and translate it into other languages.</li>
</ul>";
echo "</div>";

include_once (APPLICATION_INCPATH . 'htmlfooter.inc.php');

?>