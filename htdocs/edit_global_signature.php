<?php
// edit_global_signature.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//


// This Page Is Valid XHTML 1.0 Transitional!   4Nov05

// Authors: Ivan Lucas <ivanlucas[at]users.sourceforge.net> and Paul Heaney

function get_globalsignature($sig_id)
{
    $sql = "SELECT signature FROM emailsig WHERE id = $sig_id";
    $result=mysql_query($sql);
    list($signature)=mysql_fetch_row($result);
    mysql_free_result($result);
    $signature=stripslashes($signature);
    return $signature;
}

function delete_signature($sig_id)
{
    $sql = "DELETE FROM emailsig WHERE id = $sig_id";
    mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

    journal(CFG_LOGGING_NORMAL, 'Global Signature deleted', "A global signature was deleted", CFG_JOURNAL_ADMIN, 0);
    confirmation_page("2", "edit_global_signature.php" . $id, "<h2>Edit Successful</h2><p align='center'>Please wait while you are redirected...</p>");
}

$permission=43; // Edit global signature

//$title='Edit Global Signature';
$title = 'Global Signature';
require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

// External variables
$action = cleanvar($_REQUEST['action']);
$sig_id = cleanvar($_REQUEST['sig_id']);
$signature = cleanvar($_REQUEST['signature']);
$formaction = cleanvar($_REQUEST['formaction']);

if(!empty($signature))
{
    //we've been passed a signature - ie we must either be deleting or editing on actual signature
    switch($formaction)
    {
        case 'add':
            //then we're adding a new signature
            $sql = "INSERT INTO emailsig (signature) VALUES ('$signature') ";
            mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

            journal(CFG_LOGGING_NORMAL, 'Global Signature added', "A new global signature was added", CFG_JOURNAL_ADMIN, 0);
            include('htmlheader.inc.php');
            confirmation_page("2", "edit_global_signature.php" . $id, "<h2>Edit Successful</h2><p align='center'>Please wait while you are redirected...</p>");
            include('htmlfooter.inc.php');
        break;

        case 'edit':
            $sql = "UPDATE emailsig SET signature = '$signature' WHERE id = ".$sig_id;
            mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

            journal(CFG_LOGGING_NORMAL, 'Global Signature updated', "A global signature was updated", CFG_JOURNAL_ADMIN, 0);
            include('htmlheader.inc.php');
            confirmation_page("2", "edit_global_signature.php" . $id, "<h2>Edit Successful</h2><p align='center'>Please wait while you are redirected...</p>");
            include('htmlfooter.inc.php');
      break;
  }

}
elseif(empty($action))
{
    //The just view the global signatures
    include('htmlheader.inc.php');

    echo "<h2>$title</h2>";

    $sql = "SELECT id, signature FROM emailsig ORDER BY id ASC";
    $result = mysql_query($sql);
    if(mysql_error()) trigger_error(mysql_error(), E_USER_WARNING);

    echo "<p align='center'>One of the signatures blow will be chosen at random and inserted at the bottom of outgoing emails. It's recommended that you begin this signature with two dashes, a space and a line feed.<br /><br />";
    echo "Remember that any changes here will be effective immediately and outgoing emails will carry the new signature.</p>";

    echo "<p align='center'><a href='edit_global_signature.php?action=add'>Add New Global Signature</a></p>";

    echo "<table align='center' width='60%'>";
    echo "<tr><th>Signature</th><th>Options</th></tr>";
    while($signature = mysql_fetch_array($result))
    {
        $id = $signature['id'];
        echo "<tr>";
        echo "<td class='shade1' width='70%'>".ereg_replace("\n", "<br />", $signature['signature'])."</td>";
        echo "<td class='shade2' align='center'><a href='edit_global_signature.php?action=edit&amp;sig_id=$id'>edit</a> | ";
        echo "<a href='edit_global_signature.php?action=delete&amp;sig_id=$id'>delete</a></td>";
        echo "</tr>";
    }
    echo "</table>";

    include('htmlfooter.inc.php');
}
elseif(!empty($action))
{
    include('htmlheader.inc.php');
    switch($action)
    {
        case 'add':
            echo "<h2>$title</h2>";
            ?>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <input type="hidden" name="formaction" value="add" />
            <table class='vertical' width='50%'>
            <tr>
            <td align="right" valign="top" class="shade1"><strong>Global Signature</strong>:<br />
            A signature to insert at the bottom of outgoing emails.  It's recommended that you begin this signature with two dashes, a space and a line feed.<br /><br />
            Remember that any changes here will be effective immediately and outgoing emails will carry the new signature.
            </td>
            <td class="shade1"><textarea name="signature" rows="15" cols="65"></textarea></td>
            </tr>
            </table>
            <p align='center'><input name="submit" type="submit" value="Add Signature" /></p>
            </form>
            <?php
        break;

        case 'delete':
            delete_signature($sig_id);
        break;

        case 'edit':
            echo "<h2>Edit $title</h2>";
            ?>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <input type="hidden" name="formaction" value="edit" />
            <input type="hidden" name="sig_id" value="<?php echo $sig_id ?>" />
            <table class='vertical' width='50%'>
            <tr>
            <td align="right" valign="top" class="shade1"><strong>Global Signature</strong>:<br />
            A signature to insert at the bottom of outgoing emails.  It's recommended that you begin this signature with two dashes, a space and a line feed.<br /><br />
            Remember that any changes here will be effective immediately and outgoing emails will carry the new signature.
            </td>
            <td class="shade1"><textarea name="signature" rows="15" cols="65"><?php echo stripslashes(get_globalsignature($sig_id)); ?></textarea></td>
            </tr>
            </table>
            <p align='center'><input name="submit" type="submit" value="Edit Signature" /></p>
            </form>
            <?php
        break;
    }
    include('htmlfooter.inc.php');
}
?>
