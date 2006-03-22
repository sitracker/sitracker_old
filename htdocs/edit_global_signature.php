<?php
// edit_global_signature.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//


// This Page Is Valid XHTML 1.0 Transitional!   4Nov05

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

$permission=43; // Edit global signature

$title='Edit Global Signature';
require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

// External variables
$signature = cleanvar($_REQUEST['signature']);

if (empty($signature))
{
    // show form
    include('htmlheader.inc.php');
    echo "<h2>$title</h2>";
    ?>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <table class='vertical' width='50%'>
    <tr>
    <td align="right" valign="top" class="shade1"><strong>Global Signature</strong>:<br />
    A signature to insert at the bottom of outgoing emails.  It's recommended that you begin this signature with two dashes, a space
    and a line feed.<br /><br />
    Remember that any changes here will be effective immediately and outgoing emails will carry the new signature.
    </td>
    <td class="shade1"><textarea name="signature" rows="15" cols="65"><?php echo stripslashes(global_signature()); ?></textarea></td>
    </tr>
    </table>
    <p align='center'><input name="submit" type="submit" value="Update Signature" /></p>
    </form>
    <?php
    include('htmlfooter.inc.php');
}
else
{
    $sql = "UPDATE emailsig SET signature='$signature' ";
    mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    journal(CFG_LOGGING_NORMAL, 'Global Signature Edited', "The global signature was modified", CFG_JOURNAL_ADMIN, 0);
    confirmation_page("2", "main.php" . $id, "<h2>Edit Successful</h2><p align='center'>Please wait while you are redirected...</p>");
}
?>
