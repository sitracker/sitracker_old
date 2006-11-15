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

$title='Forgotten Password';

// External variables
$email = cleanvar($_REQUEST['emailaddress']);
$username = cleanvar($_REQUEST['username']);
$userid = cleanvar($_REQUEST['userid']);
$userhash = cleanvar($_REQUEST['hash']);

switch($_REQUEST['action'])
{
    case 'forgotpwd':
        include('htmlheader.inc.php');
        // First look to see if this is a SiT user
        $sql = "SELECT username, password FROM users WHERE email = '{$email}' LIMIT 1";
        $userresult = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        $usercount = mysql_num_rows($userresult);
        if($usercount == 1)
        {
            $extra_headers = "Reply-To: {$CONFIG['support_email']}\n";
            $extra_headers .= "X-Mailer: {$CONFIG['application_shortname']} {$application_version_string}/PHP " . phpversion() . "\n";
            $extra_headers .= "X-Originating-IP: {$_SERVER['REMOTE_ADDR']}\n";
            $extra_headers .= "\n"; // add an extra crlf to create a null line to separate headers from body
            $bodytext = "To reset your password please visit:\n";
            $url = parse_url($_SERVER['HTTP_REFERER']);
            $hash = md5($row->username.'.'.$row->password);
            $reseturl = "{$url['scheme']}://{$url['host']}{$url['path']}?action=confirmreset&amp;userid={$row->id}&amp;hash={$hash}";
            $bodytext .= "";
            mail($email, "Information for resetting your password", stripslashes($bodytext), $extra_headers);
            echo "<h3>Information sent</h3>";
            echo "<p>We have sent instructions how to reset your password to the email address you provided.</p>";
            echo "<p><a href='index.php'>Back to login page</a></p>";
        }
        else
        {
            $sql = "SELECT username,password FROM contacts WHERE email = '{$email}' LIMIT 1";
            $contactresult = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

            $contactcount = mysql_num_rows($contactresult);
            if($contactcount == 1)
            {
                while($row = mysql_fetch_object($contactresult))
                {
                    $extra_headers = "Reply-To: {$CONFIG['support_email']}\n";
                    $extra_headers .= "X-Mailer: {$CONFIG['application_shortname']} {$application_version_string}/PHP " . phpversion() . "\n";
                    $extra_headers .= "X-Originating-IP: {$_SERVER['REMOTE_ADDR']}\n";
                    $extra_headers .= "\n"; // add an extra crlf to create a null line to separate headers from body


                    $bodytext = "Username: {$row->username}\nPassword: {$row->password}";
                    mail($email, "Forgotten password details", stripslashes($bodytext), $extra_headers);
                    confirmation_page("8", "index.php", "<h2>Details sent</h2><p align='center'>Please wait while you are redirected...</p>");
                }
            }
            else
            {
                echo "<h3>Invalid email address</h3>";
                echo "<p>If you feel that you should have access to this portal please contact {$CONFIG['support_email']} for assistance</p>";
                echo "<p><a href='index.php'>Back to login page</a></p>";
            }
        }
        include('htmlfooter.inc.php');
    break;

    case 'confirmreset':
        include('htmlheader.inc.php');
        echo "<h2>Reset user password</h2>";
        echo "<p align='center'>Please confirm your username</p>";
        echo "<form action='{$_SERVER['PHP_SELF']}' method='post'>";
        ?>
        <table class='vertical'>
        <tr><th>Username</th><td><input name="username" size="30" type="text" /></td></tr>
        </table>
        <p><input type="submit" value='Continue' /></p>
        <?php
        echo "<input type='hidden' name='userid' value='{$userid}' />";
        echo "<input type='hidden' name='hash' value='{$userhash}' />";
        echo "<input type='hidden' name='action' value='resetuserpassword' />";
        echo "</form>";
        include('htmlfooter.inc.php');
    break;

    case 'resetuserpassword':
        // TODO password reset needs completing, and some parts need disabling if customer portal is not enabled INL 19Nov06
        echo "<h3>Sorry</h3>";
        echo "<p>Password reset feature not yet available.</p>";
        echo "<p><a href='index.php'>Back to login page</a></p>";
    break;

    case 'form':
    default:
        include('htmlheader.inc.php');
        echo "<h2>$title</h2>";
        echo "<form action='{$_SERVER['PHP_SELF']}' method='post'>";
        ?>
        <table class='vertical'>
        <tr><th>Email address</th><td><input name="emailaddress" size="30" type="text" /></td></tr>
        </table>
        <p><input type="submit" value='Continue' /></p>
        <input type='hidden' name='action' value='forgotpwd' />
        </form>
        <?php
        include('htmlfooter.inc.php');
    break;
}

?>