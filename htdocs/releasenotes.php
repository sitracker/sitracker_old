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

// This page requires authentication
require ('auth.inc.php');
include_once('htmlheader.inc.php');
echo "<h2>Release Notes</h2>";

echo "<div id='help'>";
echo "<p>For the latest notes on this release please <a href='http://sitracker.sourceforge.net/ReleaseNotes'>see the SiT website</a>, a summary is shown below:</p>";
echo "<h3>v3.32</h3>";
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

echo "<h3>v3.31</h3>";
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