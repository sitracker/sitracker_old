<?php
// forgotpwd.php - Forgotten password page
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>

$permission=0; // not required
require('db_connect.inc.php');
require('functions.inc.php');
include('mime.inc.php');

$email = cleanvar($_REQUEST['emailaddress']);

if(empty($email))
{
    include('htmlheader.inc.php');
    echo "<form action='{$_SERVER['PHP_SELF']}' method='post'>";
    ?>

    <table class='vertical'>
    <tr><td colspan='2'><h2>Forgotten your details?</h2></td></tr>
    <tr><th>EMail address</th><td><input name="emailaddress" size="20" type="text" /></td></tr>
    <tr><td colspan='2'><input type="submit" value='EMail password' /></td></tr>
    </table>
    </form>
    <?php
    include('htmlfooter.inc.php');
}
else
{
    $sql = "SELECT username,password FROM contacts WHERE email = '{$email}'";

    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

    include('htmlheader.inc.php');

    $count = mysql_num_rows($result);
    if($count == 1)
    {
        while($row = mysql_fetch_object($result))
        {
            $extra_headers = "Reply-To: {$CONFIG['support_email']}\n";
            $extra_headers .= "X-Mailer: {$CONFIG['application_shortname']} {$application_version_string}/PHP " . phpversion() . "\n";
            $extra_headers .= "\n"; // add an extra crlf to create a null line to separate headers from body


            $bodytext = "Username: $row->username\nPassword: $row->password";
            mail($email, "Forgotten password details", stripslashes($bodytext), $extra_headers);
            confirmation_page("8", "index.php", "<h2>Details sent</h2><p align='center'>Please wait while you are redirected...</p>");
        }
    }
    else
    {
        echo "<h3>Invalid email address</h3>";
        echo "<p>If you feel that you should have access to this portal please contact {$COFNIG['support_email']} for assistance</p>";
    }

    include('htmlfooter.inc.php');
}

?>