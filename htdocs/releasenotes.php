<?php
// releasenotes.php - Release notes summary
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.

@include ('set_include_path.inc.php');
$permission = 0;
require ('db_connect.inc.php');
require ('functions.inc.php');
$version = cleanvar($_GET['v']);
//as passed by triggers
$version = str_replace("v", "", $version);
if (!empty($version))
{
    header("Location: {$_SERVER['PHP_SELF']}#{$version}");
}

// This page requires authentication
require ('auth.inc.php');
include_once('htmlheader.inc.php');
echo "<h2>Release Notes</h2>";

echo "<div id='help'>";
echo "<h4>This is a summary of the full release notes showing only the most important changes, for more detailed notes and the latest information on this release please <a href='http://sitracker.sourceforge.net/ReleaseNotes'>see the SiT website</a>:</h4>";

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

echo "<h3><a name='3.35'>v3.35</a></h3>";
echo "<div>
<p><strong>Better Searching</strong><br />
This release introduces much speedier searching, and the search feature now searches the whole of SiT in a single operation, saving you time.</p>
<div class='img-dec'><img src='images/changelog/335search.png' alt='Search' /></div>
<p>Search is also easier to access with a new search box on the right of the menu bar.
You can also enter an incident number in the search bar to open that incident.</p>
<div class='img-dec'><img src='images/changelog/335searchbar.png' alt='Search' /></div>

<p><strong>Portal</strong><br />
The built-in portal now has been extended and now contains all the features you would expect, including the ability for contacts to view all the incidents logged for their site, and for admin contacts to be able to manage all their contracts and supported contacts.</p>
<div class='img-dec'><img src='images/changelog/335portal.png' alt='portal' /></div><br />
<div class='img-dec'><img src='images/changelog/335portallog.png' alt='portal log' /></div><br />

<p><strong>Dashboard</strong><br />
The dashboard has been improved and now dashlets can be editing in-line and can refreshed, (automatically too) if needed.</p>
<div class='img-dec'><img src='images/changelog/335dashlets.png' alt='dashlets' /></div>
<p>We've also added two new dashlets, one for monitoring the holding queue, another for seeing who's away today.
These are available to add from the dashboard configure icon, if they are not present, please ask your administrator to install them.</p>
<div class='img-dec'><img src='images/changelog/335holdingqueue.png' alt='Search' /></div><br />
<div class='img-dec'><img src='images/changelog/335holidays.png' alt='Search' /></div>

<p><strong>More strings internationalised</strong><br />
Many more strings that were previously fixed as English strings can be translated, including status strings that are now stored in the database as i18n keys.</p>

<p><strong>Oxygen Icons</strong><br />
Icons from the oxygen icon set are included in this release.</p>

<p><strong>Contracts</strong><br />
In addition the existing ability to restrict the number of supported contacts, you can now specify \"All site contacts\", which will mean that any contact associated with a particular site will be able to log incidents under the contract.</p>

<p><strong>Knowledge Base</strong><br />
The knowledge base editor has been completely re-written, to the delight of everybody that has struggled with the old editor I'm sure. The new knowledge base editor is much much easier to use and supports BBcode.</p>

<p><strong>New Scheduler</strong><br />
SiT now has an easy to use built in scheduler, all you need to do is ensure that auto.php is called periodically, via a cron or other some method (e.g. http://www.webcron.org/) and SiT will do the rest. Administrators can alter the schedules of each task, see when they last run, and disable them individually if necessary.
The SetUserStatus action will automatically set users' status based on entries in their holiday calendar, so incidents can be reassigned automatically to substitute users when somebody goes on holiday.</p>

<p><strong>Triggers</strong><br />
New in this release is a very powerful triggers system. Instead of the old system where emails would always be sent if (say) an incident was logged, with triggers you can configure exactly what happens.</p>
<div class='img-dec'><img src='images/changelog/335triggerssettings.png' alt='Search' /></div>
<div class='img-dec'><img src='images/changelog/335triggernotice.png' alt='Search' /></div>
<p><strong>Consolidated Attachments</strong><br />
Previously, attachments weren't viewable until the incident was logged, and frequently clicking attachments from different locations resulted in 404 errors.
The way of storing attachments has no been improved so attachments can be viewed prior to creating an incident. (Requires the use of inboundmail.php)</p>

<p><strong>Help Tips</strong><br />
We've begun adding helpful hints in the form of [?] links that have a popup message that offers useful tips and documentation on less obvious features.</p>
<div class='img-dec'><img src='images/changelog/335help.png' alt='help' /></div>
</div>";

echo "<hr /><h3><a name='3.32'>v3.32</a></h3>";
echo '<div>
    <p><strong>Internationalisation</strong></p>
    <ul><li>More strings are internationalised</li>
    <li>New Spanish / Colombian (es-CO) translation</li>
    <li>New Japanese (ja-JP) translation</li>
    <li>Updated German (de-DE) translation</li>
    <li>Updated French (fr-FR) translation</li>
    </ul>

    <p><strong>Localisation</strong></p>
    <ul><li>Users can now set their own local timezone</li>
    <li>Dates and times are displayed in the users\' local timezone</li>
    </ul>

    <p><strong>Drafts</strong></p>
    <ul><li>Drafts are now saved automatically every few seconds while typing incident updates or emails. You can return to these drafts later and continue writing from where you left off</li>
    </ul>

    <p><strong>Hide old incidents</strong></p>
    <ul><li>A new config variable <code>$CONFIG[\'hide_closed_incidents_older_than\']</code> has been added, when set this hides old incidents from users\' closed incidents queues. This is useful if you want to archive incidents after a time, for example after 6 months. Of course you can still search these incidents.</li>
    </ul>

';

echo "<h3><a name='3.31'>v3.31</a></h3>";
echo '<div>

    <p><strong>Internationalisation</strong></p>
    <div class="img-dec"><img src="images/changelog/331translation.png" alt="Translation" /></div>
    <ul><li>Enabled i18n to allow translation</li>
    <li>Added translate page for users to translate strings</li>
    <li>Changed the login page to allow for session-based language choice</li></ul>
    <div class="img-dec"><img src="images/changelog/331language.png" alt="Language" /></div><br /><br />

    <p><strong>Customer Portal</strong></p>
    <div class="img-dec"><img src="images/changelog/331portal.png" alt="Portal" /></div>
    <p>Improved built-in customer portal, customers can now read, open, update and request to close their incidents, view their contracts and view and update their details.</p><br /><br />

    <p><strong>Improved Contract Flexibility</strong></p>
    <div class="img-dec"><img src="images/changelog/331addcontract.png" alt="Add Contract" /></div>
    <p>Now when adding a contract, you can specify a number of new things.</p>
    <ul>
        <li>You can limit a contract to a certain amount of supported contacts (or unlimited as it is now)</li>
        <li>You can say that every site contact is to be supported by the contact. This is particually useful for sites that have a large amount of users that all need to be supported, i.e. a University department and its students.</li>
        <li>Contracts can now have no expiry date - Unlimited.</li>
    </ul><br /><br />

    <p><strong>Notices</strong></p>
    <div class="img-dec"><img src="images/changelog/331notices.png" alt="Notices" /></div>
    <p>New notice system, allows global notices as well as for informing usersof errors or information such as upgrades</p><br /><br />

    <p><strong>SLA Notices</strong></p>
    <div class="img-dec"><img src="images/changelog/331slanotices.png" alt="SLA Notices" /></div>
    <p>Making use of the new notice system, you will now see notices when incidents are approaching the end of their SLA.</p><br /><br />

    <p><strong>Jump To Incident</strong></p>
    <div class="img-dec"><img src="images/changelog/331jumpto.png" alt="Jump to Incident" /></div>
    <p>Added a \'jump to incident\' link in the support menu</p><br /><br />



    <p><strong>User Status</strong></p>
    <div class="img-dec"><img src="images/changelog/331status.png" alt="User Status" /></div>
    <p>The users page now contains an icon dispaying a user\'s online status</p><br /><br />


    <p><strong>Activites</strong></p>
    <div class="img-dec"><img src="images/changelog/331activitiesmenu.png" alt="Activities Menu" /></div>
    <p>Incidents logged under SLAs that are set to \'Timed\' (in <a href="service_levels.php">service_levels.php</a>) have an \'Activities\' entry.</p>
    <div class="img-dec"><img src="images/changelog/331activitieslist.png" alt="Activities List" /></div>
    <p>List of all the activities related to an incident.</p>
    <div class="img-dec"><img src="images/changelog/331activitieslog.png" alt="Activities Log" /></div>
    <p>When an activity is marked as complete, an entry is entered into the update log.</p><br /><br />


    <p><strong>Other Updates</strong></p>
    <ul><li>Added <a href="'.$CONFIG['application_webpath'].'reports/external_engineers.php">external engineer report</a> which shows incidents that have been escalated</li>
    <li>Software now has a vendor (allows more accurate stats)</li></ul>
    </div>';
echo "</div>";

include_once('htmlfooter.inc.php');

?>