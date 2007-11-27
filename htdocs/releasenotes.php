<?php

$permission=0;
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');
include_once('htmlheader.inc.php');
echo "<h2>Release Notes</h2>";

echo "<div id='help'>";
echo "<h3>v3.31</h3>";

echo '<div>

    <p><strong>Internationalisation</strong></p>
    <div class="img-dec"><img src="images/changelog/331translation.png" /></div>
    <ul><li>Enabled i18n to allow translation</li>
    <li>Added translate page for users to translate strings</li>
    <li>Changed the login page to allow for session-based language choice</li></ul>
    <div class="img-dec"><img src="images/changelog/331language.png" /></div><br /><br />

    <p><strong>Customer Portal</strong></p>
    <div class="img-dec"><img src="images/changelog/331portal.png" /></div></li>
    <p>Improved built-in customer portal, customers can now read, open, update and request to close their incidents, view their contracts and view and update their details.</p><br /><br />

    <p><strong>Improved Contract Flexibility</strong></p>
    <div class="img-dec"><img src="images/changelog/331addcontract.png" /></div></li>
    <p>Now when adding a contract, you can specify a number of new things.</p>
    <ul>
        <li>You can limit a contract to a certain amount of supported contacts (or unlimited as it is now)</li>
        <li>You can say that every site contact is to be supported by the contact. This is particually useful for sites that have a large amount of users that all need to be supported, i.e. a University department and its students.</li>
        <li>Contracts can now have no expiry date - Unlimited.</li>
    </ul><br /><br />

    <p><strong>Notices</strong></p>
    <div class="img-dec"><img src="images/changelog/331notices.png" /></div>
    <p>New notice system, allows global notices as well as for informing usersof errors or information such as upgrades</p><br /><br />

    <p><strong>SLA Notices</strong></p>
    <div class="img-dec"><img src="images/changelog/331slanotices.png" /></div>
    <p>Making use of the new notice system, you will now see notices when incidents are approaching the end of their SLA.</p><br /><br />

    <p><strong>Jump To Incident</strong></p>
    <div class="img-dec"><img src="images/changelog/331jumpto.png" /></div>
    <p>Added a \'jump to incident\' link in the support menu</p><br /><br />



    <p><strong>User Status</strong></p>
    <div class="img-dec"><img src="images/changelog/331status.png" /></div>
    <p>The users page now contains an icon dispaying a user\'s online status</p><br /><br />


    <p><strong>Activites</strong></p>
    <div class="img-dec"><img src="images/changelog/331activitiesmenu.png" /></div>
    <p>Incidents logged under SLAs that are set to \'Timed\' (in <a href="service_levels.php">service_levels.php</a>) have an \'Activities\' entry.</p>
    <div class="img-dec"><img src="images/changelog/331activitieslist.png" /></div>
    <p>List of all the activities related to an incident.</p>
    <div class="img-dec"><img src="images/changelog/331activitieslog.png" /></div>
    <p>When an activity is marked as complete, an entry is entered into the update log.</p><br /><br />


    <p><strong>Other Updates</strong></p>
    <ul><li>Added <a href="'.$CONFIG['application_webpath'].'reports/external_engineers.php">external engineer report</a> which shows incidents that have been escalated</li>
    <li>Software now has a vendor (allows more accurate stats)</li></ul>
</ul></div>';
echo "</div>";

include_once('htmlfooter.inc.php');

?>