<?php
// forgotpwd.php - Forgotten password page
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Authors: Paul Heaney <paulheaney[at]users.sourceforge.net>
//          Ivan Lucas <ivanlucas[at]users.sourceforge.net>

@include ('set_include_path.inc.php');
$permission=0; // not required
require ('db_connect.inc.php');
require ('functions.inc.php');
include ('mime.inc.php');

$title='Forgotten Password';

// External variables
$email = cleanvar($_REQUEST['emailaddress']);
$username = cleanvar($_REQUEST['username']);
$userid = cleanvar($_REQUEST['userid']);
$userhash = cleanvar($_REQUEST['hash']);

switch ($_REQUEST['action'])
{
    case 'forgotpwd':
        include ('htmlheader.inc.php');
        // First look to see if this is a SiT user
        $sql = "SELECT id, username, password FROM users WHERE email = '{$email}' LIMIT 1";
        $userresult = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        $usercount = mysql_num_rows($userresult);
        $userdetails = mysql_fetch_object($userresult);
        if ($usercount == 1)
        {
            $extra_headers = "Reply-To: {$CONFIG['support_email']}\n";
            $extra_headers .= "X-Mailer: {$CONFIG['application_shortname']} {$application_version_string}/PHP " . phpversion() . "\n";
            $extra_headers .= "X-Originating-IP: {$_SERVER['REMOTE_ADDR']}\n";
            $extra_headers .= "\n"; // add an extra crlf to create a null line to separate headers from body
            $bodytext = "To reset your password please visit:\n";
            $url = parse_url($_SERVER['HTTP_REFERER']);
            $hash = md5($userdetails->username.'.'.$userdetails->password);
            $reseturl = "{$url['scheme']}://{$url['host']}{$url['path']}?action=confirmreset&amp;userid={$userdetails->id}&amp;hash={$hash}";
            $bodytext .= "{$reseturl}";
            mail($email, "Information for resetting your password", $bodytext, $extra_headers);
            echo "<h3>Information sent</h3>";
            echo "<p>We have sent instructions how to reset your password to the email address you provided.</p>";
            echo "<p><a href='index.php'>Back to login page</a></p>";
        }
        else
        {
            // This is a SiT contact, not a user
            $sql = "SELECT username, password FROM `{$dbContacts}` WHERE email = '{$email}' LIMIT 1";
            $contactresult = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

            $contactcount = mysql_num_rows($contactresult);
            if ($contactcount == 1)
            {
                while ($row = mysql_fetch_object($contactresult))
                {
                    $extra_headers = "Reply-To: {$CONFIG['support_email']}\n";
                    $extra_headers .= "X-Mailer: {$CONFIG['application_shortname']} {$application_version_string}/PHP " . phpversion() . "\n";
                    $extra_headers .= "X-Originating-IP: {$_SERVER['REMOTE_ADDR']}\n";
                    $extra_headers .= "\n"; // add an extra crlf to create a null line to separate headers from body

                    $bodytext = "Username: {$row->username}\nPassword: {$row->password}";
                    mail($email, "Forgotten password details", $bodytext, $extra_headers);
                    html_redirect("index.php", TRUE, "Details sent"); // FIXME i18n
                }
            }
            else
            {
                echo "<h3>Invalid email address</h3>";
                echo "<p>If you feel that you should have access to this portal please contact {$CONFIG['support_email']} for assistance</p>";
                echo "<p><a href='index.php'>Back to login page</a></p>";
            }
        }
        include ('htmlfooter.inc.php');
    break;

    case 'confirmreset':
        include ('htmlheader.inc.php');
        $sql = "SELECT id, username, password FROM users WHERE id = '{$userid}' LIMIT 1";
        $userresult = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        $usercount = mysql_num_rows($userresult);
        if ($usercount > 0)
        {
            $userdetails = mysql_fetch_object($userresult);
            $hash = md5($userdetails->username.'.'.$userdetails->password);
            if ($hash == $userhash)
            {
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
                echo "<input type='hidden' name='action' value='resetpasswordform' />";
                echo "</form>";
            }
            else
            {
                echo "<h3>Error</h3>";
                echo "<p>Did you paste the full URL you received in the email?</p>";
                echo "<p><a href='index.php'>Back to login page</a></p>";
            }
        }
        else
        {
            echo "<h3>Error</h3>";
            echo "<p>Did you paste the full URL you received in the email?</p>";
            echo "<p><a href='index.php'>Back to login page</a></p>";
        }
        include ('htmlfooter.inc.php');
    break;

    case 'resetpasswordform':
        include ('htmlheader.inc.php');
        $sql = "SELECT id, username, password FROM users WHERE id = '{$userid}' LIMIT 1";
        $userresult = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        $usercount = mysql_num_rows($userresult);
        if ($usercount > 0)
        {
            $userdetails = mysql_fetch_object($userresult);
            $hash = md5($userdetails->username.'.'.$userdetails->password);
            if ($hash == $userhash AND $username==$userdetails->username)
            {
                $newhash = md5($userdetails->username.'.ok.'.$userdetails->password);
                echo "<h2>Set your new password</h2>";
                echo "<form action='{$_SERVER['PHP_SELF']}' method='post'>";
                echo "<table align='center' class='vertical'>";
                echo "<tr class='password'><th>New Password:</th><td><input maxlength='50' name='newpassword1' size='30' type='password' /></td></tr>";
                echo "<tr class='password'><th>Confirm New Password:</th><td><input maxlength='50' name='newpassword2' size='30' type='password' /></td></tr>";
                echo "</table>";
                echo "<input type='hidden' name='userid' value='{$userid}' />";
                echo "<input type='hidden' name='hash' value='{$newhash}' />";
                echo "<input type='hidden' name='action' value='savepassword' />";
                echo "<p><input type='submit' value='Set Password' />";
                echo "</form>";
                echo "<p><a href='index.php'>Back to login page</a></p>";
            }
            else
            {
                echo "<h3>Error</h3>";
                echo "<p>Have you forgotten your username?  If so you should contact an administrator.</p>";
                echo "<p><a href='index.php'>Back to login page</a></p>";
            }
        }
        else
        {
            echo "<h3>Error</h3>";
            echo "<p>Invalid user ID</p>";
            echo "<p><a href='index.php'>Back to login page</a></p>";
        }
        include ('htmlfooter.inc.php');
    break;

    case 'savepassword':
        $newpassword1 = cleanvar($_REQUEST['newpassword1']);
        $newpassword2 = cleanvar($_REQUEST['newpassword2']);
        include ('htmlheader.inc.php');
        $sql = "SELECT id, username, password FROM users WHERE id = '{$userid}' LIMIT 1";
        $userresult = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        $usercount = mysql_num_rows($userresult);
        if ($usercount > 0)
        {
            $userdetails = mysql_fetch_object($userresult);
            $newhash = md5($userdetails->username.'.ok.'.$userdetails->password);
            if ($newhash == $userhash)
            {
                if ($newpassword1==$newpassword2)
                {
                    $usql = "UPDATE users SET password=MD5({$newpassword1}) WHERE id={$userid} LIMIT 1";
                    mysql_query($usql);
                    echo "<h3>Password reset</h3>";
                    echo "<p>Your password has been reset, you can now login using the new details.</p>";
                    echo "<p><a href='index.php'>Back to login page</a></p>";
                }
                else
                {
                    echo "<h3>Error</h3>";
                    echo "<p>The new password you entered was not confirmed correctly.</p>";
                    echo "<p><a href='index.php'>Back to login page</a></p>";
                }
            }
            else
            {
                echo "<h3>Error</h3>";
                echo "<p>Invalid details</p>";
                echo "<p><a href='index.php'>Back to login page</a></p>";
            }
        }
        else
        {
            echo "<h3>Error</h3>";
            echo "<p>Invalid user ID</p>";
            echo "<p><a href='index.php'>Back to login page</a></p>";
        }
        include ('htmlfooter.inc.php');
    break;

    case 'form':
    default:
        include ('htmlheader.inc.php');
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
        include ('htmlfooter.inc.php');
    break;
}

?>