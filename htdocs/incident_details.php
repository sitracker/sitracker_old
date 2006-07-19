<?php
// incident_details.php - Show incident details
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// This file will soon be superceded by incident.php - 20Oct05 INL

$permission=61; // View Incident Details
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

// External variables
$incidentid = cleanvar($_REQUEST['id']);
$id = $incidentid;

$title = $id . " - " . incident_title($id);
include('incident_html_top.inc.php');

// Retrieve incident
// extract incident details
$sql  = "SELECT *, incidents.id AS incidentid, ";
$sql .= "contacts.id AS contactid ";
$sql .= "FROM incidents, contacts ";
$sql .= "WHERE (incidents.id='{$incidentid}' AND incidents.contact=contacts.id) ";
$sql .= " OR incidents.contact=NULL ";
$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(), E_USER_ERROR);
$incident = mysql_fetch_object($result);
$site_name=site_name($incident->siteid);
$product_name=product_name($incident->product);
if ($incident->softwareid > 0) $software_name=software_name($incident->softwareid);
$servicelevel_id=maintenance_servicelevel($incident->maintenanceid);
$servicelevel_tag = $incident->servicelevel;
if ($servicelevel_tag=='') $servicelevel_tag = servicelevel_id2tag(maintenance_servicelevel($incident->maintenanceid));
$servicelevel_name=servicelevel_name($servicelevelid);
$opened_for=format_seconds(time() - $incident->opened);

include('incident/details.inc.php');
echo "<br />";



/*
// set a variable showing whether user has permission to view hidden updates
$hidden_view_permision=user_permission($sit[2],52);

// extract incident details
$sql  = "SELECT incidents.id AS incidentid, owner, towner, title, contact, externalid, externalengineer, externalemail, priority, status, type, maintenanceid, product, softwareid, productversion, productservicepacks, closingstatus, closed, contacts.id AS contactid, contacts.siteid, surname, email, phone, fax, address1, opened, lastupdated, timeofnextaction FROM incidents, contacts ";
$sql .= "WHERE (incidents.id=$id AND incidents.contact=contacts.id) ";
$sql.=" OR incidents.contact=NULL ";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
$incident = mysql_fetch_array($result);
$opened_string = date("l jS F Y @ g:i a", $incident["opened"]);
if ($incident['closed']==0) { $closed_string =  'Not yet closed';  }
else { $closed_string =  date("l jS F Y @ g:i a", $incident['closed']);  }
if ($showauto=='') $showauto='true';

$lastupdated_string = date("l jS F Y @ g:i a", $incident["lastupdated"]);
$now = time();
if ($incident["timeofnextaction"] == 0) $timetonextaction_string = "None";
else
{
    if (($incident["timeofnextaction"] - $now) > 0)
    {
        $timetonextaction_string = format_seconds($incident["timeofnextaction"] - $now);
        $timetonextaction_date = date("l jS F Y @ g:i a", $incident["timeofnextaction"]);
        $timetonextaction_string .= "($timetonextaction_date)";
    }
    else
    {
        $timetonextaction_string = "<span class=\"expired\">Now ";
        $timetonextaction_date = date("l jS F Y @ g:i a", $incident["timeofnextaction"]);
        $timetonextaction_string .= "($timetonextaction_date)</span>";
    }
}

// Display link to expand or collapse entries
$expand=true;
$view='expand';

if (!isset($expand))
{
    if (user_collapse($sit[2])==FALSE) { $expand='true'; $view='expand'; }
    else { $expand='false'; $view='collapse'; }
}
echo "<div align='center'>";
echo "<form>";
echo "View: <select class='dropdown' name='view' onchange='window.location.href=this.options[this.selectedIndex].value'>";
echo "<option value='$PHP_SELF?id=$id&view=expand&expand=true&showauto=true' ";
if ($view == 'expand')  echo "selected ";
echo ">Display All</option>";
echo "<option value='$PHP_SELF?id=$id&view=visible&expand=true&showauto=true' ";
if ($view == 'visible')  echo "selected ";
echo ">Visible Only (What the customer can see)</option>";
echo "<option value='$PHP_SELF?id=$id&view=hidden&expand=true&showauto=true' ";
if ($view == 'hidden')  echo "selected ";
echo ">Hidden Only (What the customer can't see)</option>";
echo "<option value='$PHP_SELF?id=$id&view=auto&expand=true&showauto=false' ";
if ($view == 'auto')  echo "selected ";
echo ">All except auto updates</option>";
echo "</select></form></div>\n";

?>
<table align='center' class='vertical' width='85%'>
<tr><th>Contact:</th><td><a href="contact_details.php?id=<?php echo $incident["contactid"] ?>" target="_new"><?php echo contact_realname($incident['contact']);
echo "</a>";
echo " of ".site_name($incident["siteid"]);
?>
</td></tr>
<tr><th>Email:</th><td><?php echo $incident["email"]; ?></td></tr>
<tr><th>Phone:</th><td><?php if ($incident["phone"] == "") echo "None"; else echo $incident["phone"]; ?></td></tr>
</table>

<div id="header" <?php if ($expand!="true") echo "style=\"Display: none\""; ?>>
<p align='center'><strong><?php echo $incident["type"];?> Incident Details</strong></p>
<table align='center' class='vertical' width="85%">
<tr><th>Owner:</th><td>
<?php
echo user_realname($incident['owner']);
echo " (".userstatus_name(user_status($incident["owner"])).")";
$user_message = user_message($incident['owner']);
if (!empty($user_message)) echo " - $user_message";
$userphone=user_phone($incident["owner"]);
if (!empty($userphone)) echo ", Telephone: {$userphone}";
echo "</td></tr>";
if ($incident['towner'] > 0)
{
    echo "<tr><th>Temporary Owner:</th><td>";
    echo user_realname($incident['towner']);
    echo "</td></tr>\n";
}
if ($incident['softwareid'] > 0)
{
    echo "<tr><th>Backup Engineer:</th><td>";
    if ($incident['towner'] > 0) $backupid=software_backup_userid($incident['towner'], $incident['softwareid']);
    else $backupid=software_backup_userid($incident['owner'], $incident['softwareid']);
    if ($backupid > 0)
    {
        echo user_realname($backupid);
        $backupphone=user_phone($backupid);
        if (!empty($backupphone)) echo ", Telephone: {$backupphone}";
    }
    else echo "None available";
    echo "</td></tr>\n";
}
echo "</table>\n";

echo "<table align='center' class='vertical' width='85%'>";

if (!empty($incident['externalid']))
    echo "<tr><th>External ID:</th><td>{$incident['externalid']}</td></tr>\n";
if (!empty($incident['externalengineer']))
    echo "<tr><th>External Engineer:</th><td><a href=\"mailto:{$incident['externalemail']}\">{$incident['externalengineer']}</a>, {$incident['externalemail']}</td></tr>\n";

?>
<tr><th>Service Level/Priority:</th><td>
<?php
    echo servicelevel_name(maintenance_servicelevel($incident['maintenanceid']));
    echo ", ";
    echo priority_name($incident['priority'])." priority";
?>
</td></tr>
<tr><th>Status:</th><td><?php echo incidentstatus_name($incident["status"]) ?><?php if ($incident["status"] == 2) { echo " (" . closingstatus_name($incident["closingstatus"]) . ")"; } ?></td></tr>
<tr><th>Contract:</th><td><?php
if (!empty($incident['maintenanceid'])) echo "<strong>{$incident['maintenanceid']}</strong> - ";
if (product_name($incident["product"]) == "") echo "None"; else echo product_name($incident["product"]); ?></td></tr>
<tr><th>Software:</th><td><?php if ($incident["softwareid"] == 0 || empty($incident["softwareid"])) echo "See Contract"; else echo software_name($incident["softwareid"]); ?></td></tr>
<tr><th>Software Version:</th><td><?php if ($incident["productversion"] == "") echo "None"; else echo $incident["productversion"]; ?></td></tr>
<tr><th>Service Packs Applied:</th><td><?php if ($incident["productservicepacks"] == "") echo "None"; else echo $incident["productservicepacks"]; ?></td></tr>
<?php incident_productinfo_html($incident["incidentid"]) ?>
</table>

<table align='center' class='vertical' width="85%">
<tr><th>Date Opened:</th><td><?php echo $opened_string ?></td></tr>
<?php
if ($incident['closingstatus']!=0 || $incident['status']==2)
{
    echo "<tr><th>Date Closed:</th><td>{$closed_string}</td></tr>\n";
    echo "<tr><th>Closing Status:</th><td>".closingstatus_name($incident['closingstatus'])."</td></tr>\n";
}
?>
<tr><th>Last Updated:</th><td><?php echo $lastupdated_string ?></td></tr>
<tr><th>Next Action Time:</th><td><?php echo $timetonextaction_string ?></td></tr>
</table>
</div>
<br />
<?php
*/



include('incident/log.inc.php');

/*
// Extract updates
$sql  = "SELECT * FROM updates WHERE incidentid='$id' ";
// Don't display automatic updates unless showauto var is set
if ($showauto!='true')
{
    $sql .= "AND type!='auto' ";
}
if ($view=='hidden') $sql .= "AND customervisibility='hide' ";
if ($view=='visible') $sql .= "AND customervisibility='show' ";
if (user_update_order($sit[2]) == "asc")
    $sql .= "ORDER BY timestamp ASC, id ASC";
else if (user_update_order($sit[2]) == "desc")
    $sql .= "ORDER BY timestamp DESC, id DESC";

$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

$updatecounter=0;
while ($updates = mysql_fetch_array($result))
{
    // Make a readble date
    if (date('dmy', $updates["timestamp"]) == date('dmy', time()))
    {
        $update_timestamp_string = "Today @ ".date('g:i A', $updates["timestamp"]);
    }
    elseif (date('dmy', $updates["timestamp"]) == date('dmy', (time()-86400)))
    {
        $update_timestamp_string = "Yesterday @ ".date('g:i A', $updates["timestamp"]);
    }
    else
    {
        $update_timestamp_string = date("D jS M y @ g:i A", $updates["timestamp"]);
    }
    // ondblclick="ShowHide('collapse<? echo $updates['id']; ','update

    echo "<table align=center border=0 cellpadding=2 cellspacing=0 width=\"95%\"><tr><td class='shade1' width=\"*\">";

    // echo "<a href=\"javascript:ShowHide('link{$updates['id']}','update{$updates['id']}');\" style='color: #444;' id='link{$updates['id']}'>[-]</a> ";

    if ($updates['customervisibility'] == 'show')
    {
        echo "<a href=\"incident_showhide_update.php?mode=hide&incidentid={$id}&updateid={$updates['id']}&view={$view}&expand={$expand}\" name='{$updates['id']}' class='info'>(Visible)<span>Visible to customer, click to hide</span></a> &nbsp;";
    }
    else
    {
        if ($hidden_view_permision == TRUE)
            echo "<a href=\"incident_showhide_update.php?mode=show&incidentid={$id}&updateid={$updates['id']}&view={$view}&expand={$expand}\" name='{$updates['id']}' class='info'>(Hidden)<span>Hidden, click to make visible to customer</span></a> &nbsp;";
        else
            echo "[Hidden] &nbsp;";
    }

    // Header bar for each update
    switch ($updates['type'])
    {
        case 'editing':
            echo "Edited by <strong>".user_realname($updates['userid'])."</strong>";
        break;

        case 'opening':
            echo "Opened by <strong>".user_realname($updates['userid'])."</strong>";
        break;

        case 'reassigning':
            echo "Reassigned by <strong>".user_realname($updates['userid'])."</strong>";
            if ($updates['currentowner']!=0)  // only say who it was assigned to if the currentowner field is filled in
            {
                echo " To <strong>".user_realname($updates['currentowner'])."</strong>";
            }
        break;

        case 'tempassigning':
            echo "Temporarily assigned by <strong>".user_realname($updates['userid'])."</strong>";
            // <strong>".user_realname($updates['userid'])."</strong>";
            if ($updates['currentowner']!=0)  // only say who it was assigned to if the currentowner field is filled in
            {
                echo " To <strong>".user_realname($updates['currentowner'])."</strong>";
            }
    break;

    case 'email':
        echo "Email Sent by <strong>".user_realname($updates['userid'])."</strong>";
    break;

    case 'closing':
        echo "Closed by <strong>".user_realname($updates['userid'])."</strong>";
    break;

    case 'reopening':
        echo "Reopened by <strong>".user_realname($updates['userid'])."</strong>";
    break;

    case 'phonecallout':
        echo "Call made by <strong>".user_realname($updates['userid'])."</strong>";
    break;

    case 'phonecallin':
        echo "Call taken by <strong>".user_realname($updates['userid'])."</strong>";
    break;

    case 'research':
        echo "Researched by <strong>".user_realname($updates['userid'])."</strong>";
    break;

    case 'webupdate':
        echo "Web Update by <strong>".user_realname($updates['userid'])."</strong>";
    break;

    case 'emailout':
        echo "Email sent by <strong>".user_realname($updates['userid'])."</strong>";
    break;

    case 'emailin':
        echo "<a href=/attachments/updates/".$updates['id']."/mail.eml onMouseOver=\"window.status='Open original message in email application'; return true;\">Email</a> received by <strong>".user_realname($updates['userid'])."</strong>";
    break;

    case 'externalinfo':
        echo "External info added by <strong>".user_realname($updates['userid'])."</strong>";
    break;

    case 'probdef':
        echo "Problem Definition by <strong>".user_realname($updates['userid'])."</strong>";
    break;

    case 'solution':
        echo "Resolution/Reprioritisation by <strong>".user_realname($updates['userid'])."</strong>";
    break;

    case 'auto':
        echo "Auto-Updated by <strong>".user_realname($updates['userid'])."</strong>";
    break;

    case 'actionplan':
        echo "Action Plan by <strong>".user_realname($updates['userid'])."</strong>";
    break;

    case 'slamet':
        echo "<strong style='background-color:#FFC850;'>SLA: ".target_type_name($updates['sla'])." by ".user_realname($updates['userid'])."</strong>";
    break;

    case 'reviewmet':
        echo "<strong style='background-color:#7AC5CD;'>REVIEW: ";
        if ($updates['userid']==0) echo "Review period started";
        else echo "Incident reviewed";
        echo " by ".user_realname($updates['userid'])."</strong>";
    break;

    default:
        echo "Updated by <strong>".user_realname($updates['userid'])."</strong>";

    break;
    }
    if ($updates['nextaction']!='') echo " Next Action: <strong>".$updates['nextaction'].'<strong>';

    echo "</td><td align=right class=shade1 width=200><strong>$update_timestamp_string</strong></td></tr></table>";

    //echo "<SPAN ID='update{$updates['id']}'";
    //if ($updatecounter>=2 && $expand!="true") echo "style=\"Display: none\"";
    //echo ">";
    // ondblclick=\"ShowHide('collapse{$updates['id']}','update{$updates['id']}')
    echo "<table id='update{$updates['id']}' align=center border=0 cellpadding=2 cellspacing=0 width='95%'";
    // if ($updatecounter>=2 && $expand!="true") echo "style=\"Display: none\"";
    echo "\">";
    ////////////////////////
    if (($hidden_view_permision == FALSE && $updates['customervisibility'] == 'hide') OR $updates['bodytext']=='')
    {
    echo "<!-- hidden -->";
    }
    else
    {
    if ($updates['customervisibility'] == 'show') $shade='visible';
    else $shade='shade2';
    echo "<tr><td class='$shade' width='100%'>";

    $updatecounter++;
    // strip tags from update body (convert to html entities)
    $updatebodytext = $updates['bodytext'];

    $updatebodytext = str_replace( "<b>", "[[b]]", $updatebodytext );
    $updatebodytext = str_replace( "</b>", "[[/b]]", $updatebodytext );
    $updatebodytext = str_replace( "<B>", "[[b]]", $updatebodytext );
    $updatebodytext = str_replace( "</B>", "[[/b]]", $updatebodytext );
    $updatebodytext = str_replace( "<i>", "[[i]]", $updatebodytext );
    $updatebodytext = str_replace( "</i>", "[[/i]]", $updatebodytext );
    $updatebodytext = str_replace( "<I>", "[[i]]", $updatebodytext );
    $updatebodytext = str_replace( "</I>", "[[/i]]", $updatebodytext );
    $updatebodytext = str_replace( "<u>", "[[u]]", $updatebodytext );
    $updatebodytext = str_replace( "</u>", "[[/u]]", $updatebodytext );
    $updatebodytext = str_replace( "<U>", "[[u]]", $updatebodytext );
    $updatebodytext = str_replace( "</U>", "[[/u]]", $updatebodytext );
    $updatebodytext = str_replace( "&lt;", "[[lt]]", $updatebodytext );
    $updatebodytext = str_replace( "&gt;", "[[gt]]", $updatebodytext );
    $updatebodytext = str_replace( "<hr>", "[[hr]]", $updatebodytext );

    $updatebodytext=htmlspecialchars($updatebodytext);
    // Bold, Italic, Underline

    $updatebodytext = str_replace( "[[b]]", "<b>", $updatebodytext );
    $updatebodytext = str_replace( "[[/b]]", "</b>", $updatebodytext );
    $updatebodytext = str_replace( "[[B]]", "<b>", $updatebodytext );
    $updatebodytext = str_replace( "[[/B]]", "</b>", $updatebodytext );
    $updatebodytext = str_replace( "[[i]]", "<i>", $updatebodytext );
    $updatebodytext = str_replace( "[[/i]]", "</i>", $updatebodytext );
    $updatebodytext = str_replace( "[[I]]", "<i>", $updatebodytext );
    $updatebodytext = str_replace( "[[/I]]", "</i>", $updatebodytext );
    $updatebodytext = str_replace( "[[u]]", "<u>", $updatebodytext );
    $updatebodytext = str_replace( "[[/u]]", "</u>", $updatebodytext );
    $updatebodytext = str_replace( "[[U]]", "<u>", $updatebodytext );
    $updatebodytext = str_replace( "[[/U]]", "</u>", $updatebodytext );
    $updatebodytext = str_replace( "[[lt]]", "&lt;", $updatebodytext );
    $updatebodytext = str_replace( "[[gt]]", "&gt;", $updatebodytext );
    $updatebodytext = str_replace( "[[hr]]", "<hr>", $updatebodytext );

    $updatebodytext = preg_replace(  // Insert path to attachments
        "/\[\[att\]\](.*?)\[\[\/att\]\]/",
        "<a href = '/attachments/updates/{$updates['id']}/$1'>$1</a>",
        $updatebodytext
    );

    if ($updatebodytext=='') $updatebodytext='&nbsp;';

    // added a strip-slashes here INL 30July02
    // this is because the send email is now handled differently

    // Put the header part (up to the <hr> in a seperate DIV)
    if (strpos($updatebodytext, '<hr>')!==FALSE)
    {
        $updatebodytext = "<div class='iheader'>".str_replace('<hr>','</div>',$updatebodytext);
    }

    $search = array("/(?<!quot;|[=\"]|:\/{2})\b((\w+:\/{2}|www\.).+?)"."(?=\W*([<>\s]|$))/i");  // , "/(([\w\.]+))(@)([\w\.]+)\b/i"
    $replace = array("<a href=\"$1\">$1</a>"); // , "<a href=\"mailto:$0\">$0</a>"
    $updatebodytext = preg_replace("/href=\"www/i", "href=\"http://www", preg_replace ($search, $replace, $updatebodytext));

    echo nl2br(stripslashes($updatebodytext));
    echo "</td></tr>\n";
    }
    /////////
    echo "</table>\n";
    //echo "</SPAN>";
    echo "<br />";
}
*/
include('incident_html_bottom.inc.php');
?>
