<?php
// edit_contact_flags.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// DEPRECATED as of v3.30

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

@include ('set_include_path.inc.php');
$permission=36; // Set Contact Flags
$title='Set Contact Flags';

require ('db_connect.inc.php');
require ('functions.inc.php');
// This page requires authentication
require ('auth.inc.php');

// External variables
$mode = $_REQUEST['mode'];
$contactid=mysql_real_escape_string($_REQUEST['id']);
$flag=cleanvar($_REQUEST['flag']);

switch ($mode)
{
    case 'addflag':
        $sql = "INSERT INTO contactflags (contactid, flag) VALUES ('$contactid', '$flag')";
        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error: ".mysql_error(), E_USER_ERROR);
        header("Location: edit_contact_flags.php?id={$contactid}");
        exit;
    break;

    case 'removeflag':
        $sql = "DELETE FROM contactflags WHERE contactid='$contactid' AND flag='$flag' LIMIT 1";
        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error: ".mysql_error(), E_USER_ERROR);
        header("Location: edit_contact_flags.php?id={$contactid}");
        exit;
    break;
}

include ('htmlheader.inc.php');
?>
<script type="text/javascript">
function confirm_submit()
{
    return window.confirm('Are you sure you want to make these changes?');
}
</script>
<?php
//
// Display current Contact Flags
//
$sql="SELECT * FROM contacts WHERE id='$contactid' ";
$contactresult = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
while ($contactrow=mysql_fetch_array($contactresult))
{
    echo "<table align='center'>";
    echo "<tr><th>Name:</th><td><h3><a href='edit_contact.php?action=edit&amp;contact={$contactid}'>".$contactrow['forenames'].' '.$contactrow['surname']."</a></h3></td></tr>";
    echo "<tr><th>Current Flags:</th><td>";
    print_contact_flags($contactid, TRUE);
    echo "</td></tr>\n";
}
echo "</table>";
// list available flags
// have a look what are set already
$cflags=array();
$csql = "SELECT flag FROM contactflags WHERE contactid='$contactid'";
$cresult = mysql_query($csql);
while ($cflag = mysql_fetch_object($cresult))
{
    $cflags[] = $cflag->flag;
}

echo "<h2>Available Flags</h2>";
echo "<p class='warning'>Please note that 'contact flags' are a deprecated feature as of SiT v3.30, we recommend to use 'tags' instead.</p>";
echo "<table align='center'><tr>";

$sql="SELECT * FROM flags ORDER BY flag";
$flagresult = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
$col=1;
$shade=1;

while ($flagrow=mysql_fetch_array($flagresult))
{
    if (!in_array($flagrow['flag'], $cflags))
    {
        echo "<td class=\"shade2\" align=\"center\"><a href=\"{$_SERVER['PHP_SELF']}?mode=addflag&amp;id=$contactid&amp;flag=".strtoupper($flagrow['flag'])."\" title=\"".$flagrow['name']."\">".strtoupper($flagrow['flag'])."</a><br />{$flagrow['name']}</td>\n";
        if ($col>4) { echo "</tr>\n<tr>"; $col=0;}
        $col++;
    }
}
while ($col<=5)
{
    echo "<td class=\"shade2\">&nbsp;</td>";
    $col++;
}
echo "</tr></table>";
include ('htmlfooter.inc.php');
?>
