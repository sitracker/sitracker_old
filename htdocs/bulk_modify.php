<?php
// bulk_modify.php - Modify items in bulk - mainly incidents
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

$permission=7; // Edit Incidents

require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

include('htmlheader.inc.php');

$action = cleanvar($_REQUEST['action']);

    switch($action)
    {
        case 'external_esc': //show external escalation modification page
            echo "<h2>Bulk modify external escalation details</h2>";
            $sql = "SELECT distinct(externalemail), externalengineer ";
            $sql .= "FROM `incidents` WHERE closed = '0' AND externalemail!=''";

            $result = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
            if (mysql_num_rows($result) >= 1)
            {
                echo "<form action='".$_SERVER['PHP_SELF']."?action=change_external_esc' method='post'>";
                echo "<p align='center'>This will change the external engineer details for all open incidents for the external engineer you select.</p>";
                echo "<table class='vertical'>";
                echo "<tr><th>External engineers email address (to change):</th>";
                echo "<td><select name='oldexternalemail'>";
                while($row = mysql_fetch_array($result))
                {
                    echo "<option value='".stripslashes($row['externalengineer']).",".$row['externalemail']."'>";
                    echo stripslashes($row['externalengineer'])." - ".$row['externalemail']."</option>\n";
                }
                echo "</select></td></tr>";
                echo "<tr><th>External Engineers Name:</th>";
                echo "<td><input maxlength='80' name='externalengineer' size='30' type='text' value='' /></td></tr>";
                echo "<tr><th>External Email:</th>";
                echo "<td><input maxlength='255' name='externalemail' size='30' type='text' value='' /></td></tr>";
                echo "</table><p align='center'><input name='submit' type='submit' value='Save' /></p></form>";
            }
            else echo "<p align='center'>There are currently no escalated incidents to modify</p>";
        break;
        case 'change_external_esc': //omdify the extenal escalation info
/*
External Engineer:  -&gt; <b>Foo</b>
External email:  -&gt; <b>foo@pheaney.co.uk</b>
<hr>
*/
            list($old_external_engineer, $old_email_address) = split(',',  cleanvar($_REQUEST['oldexternalemail']));
            //$old_email_address = cleanvar($_REQUEST['oldexternalemail']);
            $new_external_email = cleanvar($_REQUEST['externalemail']);
            $new_extenal_engineer = cleanvar($_REQUEST['externalengineer']);

            //list incidents with this old email address so we can update them

            $sql = "SELECT id FROM incidents WHERE closed = '0' AND externalemail = '".$old_email_address."'";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

            while($row = mysql_fetch_array($result))
            {
                $bodytext = "External Engineer: ".$old_external_engineer." -&gt; [b]". $new_extenal_engineer."[/b]\n";
                $bodytext .= "External email: ".$old_email_address." -&gt; [b]".$new_external_email."[/b]\n<hr>";
                $sql  = "INSERT INTO updates (incidentid, userid, type, bodytext, timestamp) ";
                $sql .= "VALUES ('".$row['id']."', '".$sit[2]."', 'editing', '$bodytext', '".time()."')";
                $result = mysql_query($sql);
                if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            }


            $sql = "UPDATE incidents SET externalengineer = '$new_extenal_engineer', externalemail = '$new_external_email' ";
	    $sql .= " WHERE externalemail = '$old_email_address' AND closed = '0'";


            $result = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
            confirmation_page("2", "main.php", "<h2>Update Successful</h2><p align='center'>Please wait while you are redirected...</p>");
            break;
        default:
            echo '<h1>No action specified</h1>';
            break;
    }


include('htmlfooter.inc.php');

?>
