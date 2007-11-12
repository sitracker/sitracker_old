<?php

$permission=0;
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');
include_once('htmlheader.inc.php');
$latestversion = '331';
$version = cleanvar($_REQUEST['v']);

echo "<h2>{$strReleaseNotes}</h2>";

if($version == '331' OR $latestversion == '331' OR $version == 'all')
{
    echo "<h3>v3.31</h3>";

    echo '<div>            
            <p><strong>Customer Portal</strong></p>
            <div class="img-dec"><img src="images/changelog/331portal.png" /></div></li>
            <p>Improved customer portal, customers can now read, open, update and request to close their incidents, view their contracts and view and update their details.</p><br /><br />

            
            <p><strong>Internationalisation</strong></p>
            <div class="img-dec"><img src="images/changelog/331translation.png" /></div>
            <ul><li>Enabled i18n to allow translation</li>
            <li>Added translate page for users to translate strings</li>            
            <li>Changed the login page to allow for session-based language choice</li></ul>
            <div class="img-dec"><img src="images/changelog/331language.png" /></div><br /><br />
            
            
            <p><strong>Jump To Incident</strong></p>
            <div class="img-dec"><img src="images/changelog/331jumpto.png" /></div>
            <p>Added a \'jump to incident\' link in the support menu</p><br /><br />

            <p><strong>Notices</strong></p>           
            <div class="img-dec"><img src="images/changelog/331notices.png" /></div>
            <p>New notice system, allows global notices as well as for informing usersof errors or information such as upgrades</p><br /><br />
            
            <p><strong>User Status</strong></p>            
            <div class="img-dec"><img src="images/changelog/331status.png" /></div>
            <p>The users page now contains an icon dispaying a user\'s online status</p><br /><br />
    
            <p><strong>Other Updates</strong></p>           
            <ul><li>Added <a href="">external engineer report</a> which shows incidents that have been escalated</li>
            <li>Software now has a vendor (allows more accurate stats)</li></ul>
        </ul></div>';
}
echo "<p align='center'><a href='{$_SERVER['PHP_SELF']}?v=all'>{$strOlderVersions}</a></p>";
include_once('htmlfooter.inc.php');

?>