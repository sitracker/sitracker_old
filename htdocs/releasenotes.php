<?php

$permission=0;
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');
include_once('htmlheader.inc.php');
$version = cleanvar($_REQUEST['v']);

echo "<h2>{$strReleaseNotes}</h2>";

if($version == '331' OR empty($version))
{
    echo "<h3>v3.31</h3>";
    
    echo '<div><ul>
            <li>Software now has a vendor (allows more accurate stats)</li>
            
            <li>Improved customer portal, customers can now read, open, update and request
            to close their incidents, view their contracts and view and update their 
                    details</li>
            <div class="img-dec"><img src="images/changelog/331portal.png" /></div>
    
            
            <li>Enabled i18n to allow translation</li>
            
            <li>Added translate page for users to translate strings</li>
            <div class="img-dec"><img src="images/changelog/331translation.png" /></div>
            
            <li>Added a \'jump to incident\' link in the support menu</li>
            <div class="img-dec"><img src="images/changelog/331jumpto.png" /></div>
            
            <li>Added <a href="">external engineer report</a> which shows incidents that have been escalated</li>
            
            <li>New notice system, allows global notices as well as for informing users</li>
            <div class="img-dec"><img src="images/changelog/331notices.png" /></div>
            
    of errors or information such as upgrades
            <li>The users page now contains an icon dispaying a user\'s online status</li>
            <div class="img-dec"><img src="images/changelog/331status.png" /></div>
            
            <li>Changed the login page to allow for session-based language choice</li>
            <div class="img-dec"><img src="images/changelog/331language.png" /></div>
        </ul></div>';
}

include_once('htmlfooter.inc.php');

?>