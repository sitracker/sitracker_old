<?php
// review_incoming_updates.php - Review/Delete Incident Updates
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Authors: Tom Gerrard, Ivan Lucas <ivanlucas[at]users.sourceforge.net>
//                       Paul Heaney <paulheaney[at]users.sourceforge.net>

// This Page Is Valid XHTML 1.0 Transitional! 31Oct05

@include ('set_include_path.inc.php');
$permission=42;
require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');


/**
    * @author Tom Gerrard
*/
function generate_row($update)
{
    global $CONFIG, $sit;
    if (strlen($update['bodytext']) > 1003) $updatebodytext = substr($update['bodytext'],0,1000).'...';
    else $updatebodytext=$update['bodytext'];

    $search = array( '<b>',  '</b>',  '<i>',  '</i>',  '<u>',  '</u>',  '&lt;',  '&gt;');
    $replace = '';
    $updatebodytext=htmlspecialchars(str_replace($search, $replace, $updatebodytext));
    if ($updatebodytext=='') $updatebodytext='&nbsp;';

    $shade='shade1';
    if (!empty($update['fromaddr']))
    {
        // Have a look if we've got a customer or user with this email address
        $sql = "SELECT COUNT(id) FROM `{$dbContacts}` WHERE email LIKE '%{$update['fromaddr']}%'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        list($contactmatches) = mysql_fetch_row($result);
        if ($contactmatches > 0) $shade='idle';
    }
    $pluginshade = plugin_do('holdingqueue_rowshade',$update);
    $shade = $pluginshade ? $pluginshade : $shade;
    $html_row="<tr class='$shade'>";
    $html_row.="<td style='text-align: center'><input type='checkbox' name='selected[]' value='".$update['id']."' /></td>";
    $html_row.="<td align='center' width='20%'>".date($CONFIG['dateformat_datetime'],$update['timestamp']).'</td>';
    $html_row.="<td width='20%'>";
    if (!empty($update['contactid']) AND strtolower($update['fromaddr'])==strtolower(contact_email($update['contactid'])))
    {
        $contact_realname = contact_realname($update['contactid']);
        $html_row .= $contact_realname;
        $html_row .= " of ".contact_site($update['contactid']);
        if ($update['emailfrom'] != $contact_realname)
        {
            $html_row .= "<br />\n";
            $html_row.= htmlentities($update['emailfrom'],ENT_QUOTES, $GLOBALS['i18ncharset']);
        }
    }
    else
    {
        $html_row .= "{$update['fromaddr']}<br />\n";
        $html_row.= htmlentities($update['emailfrom'],ENT_QUOTES, $GLOBALS['i18ncharset']);
    }
    $html_row .= "</td>";

    $html_row.="<td width='20%'><a href=\"javascript:incident_details_window('{$update['tempid']}','incomingview');\" id='update{$update['id']}' class='info'>";
//     $html_row.="<td width='20%'><a href=\"javascript:void();\" id='update{$update['id']}' class='info' style='cursor:help;'>";
    if (empty($update['subject'])) $update['subject'] = $strUntitled;
    $html_row.=htmlentities($update['subject'],ENT_QUOTES, $GLOBALS['i18ncharset']);
    $html_row.='<span>'.parse_updatebody($updatebodytext).'</span></a></td>';
    $html_row.="<td align='center' width='20%'>{$update['reason']}</td>";
    $html_row.="<td align='center' width='20%'>";
    if (($update['locked'] != $sit[2]) && ($update['locked']>0))
    $html_row.= "Locked by ".user_realname($update['locked'],TRUE);
    else
    {
        if ($update['locked'] == $sit[2])
        {
            $html_row.="<a href='{$_SERVER['PHP_SELF']}?unlock={$update['tempid']}' title='Unlock this update so it can be modified by someone else'> {$GLOBALS['strUnlock']}</a> | ";
        }
        else $html_row.= "<a href=\"javascript:incident_details_window('{$update['tempid']}','incomingview');\" id='update{$update['id']}' class='info' title='View and lock this held e-mail'>{$GLOBALS['strView']}</a> | ";
        $html_row.= "<a href='delete_update.php?updateid=".$update['id']."&amp;tempid=".$update['tempid']."&amp;timestamp=".$update['timestamp']."' title='Remove this item permanently' onclick='return confirm_delete();'> {$GLOBALS['strDelete']}</a>";
    }
    $html_row.="</td></tr>\n";
    return $html_row;
}

function deldir($location)
{
    if (substr($location,-1) <> "/")
    $location = $location."/";
    $all=opendir($location);
    while ($file=readdir($all))
    {
        if (is_dir($location.$file) && $file <> ".." && $file <> ".")
        {
            deldir($location.$file);
            rmdir($location.$file);
            unset($file);
        }
        elseif (!is_dir($location.$file))
        {
            unlink($location.$file);
            unset($file);
        }
    }
    rmdir($location);
}

$title = 'Review Held Updates';
$refresh = $_SESSION['incident_refresh'];
$selected = $_POST['selected'];
include ('htmlheader.inc.php');

if ($lock=$_REQUEST['lock'])
{
    $lockeduntil=date('Y-m-d H:i:s',$now+$CONFIG['record_lock_delay']);
    $sql = "UPDATE tempincoming SET locked='{$sit[2]}', lockeduntil='{$lockeduntil}' ";
    $sql .= "WHERE tempincoming.id='{$lock}' AND (locked = 0 OR locked IS NULL)";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
}
elseif ($unlock=$_REQUEST['unlock'])
{
    $sql = "UPDATE tempincoming SET locked=NULL, lockeduntil=NULL ";
    $sql .= "WHERE tempincoming.id='{$unlock}' AND locked = '{$sit[2]}'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
}
else
{
    // Unlock any expired locks
    $nowdatel=date('Y-m-d H:i:s');
    $sql = "UPDATE tempincoming SET locked=NULL, lockeduntil=NULL ";
    $sql .= "WHERE UNIX_TIMESTAMP(lockeduntil) < '$now' ";
    mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
}

if ($spam_string=$_REQUEST['delete_all_spam'])
{
    $spam_array=explode(',',$spam_string);
    foreach ($spam_array as $spam)
    {
        $ids=explode('_',$spam);

        $sql = "DELETE FROM tempincoming WHERE id='".$ids[1]."' AND SUBJECT LIKE '%SPAMASSASSIN%' AND updateid='".$ids[0]."' LIMIT 1";
        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        if (mysql_affected_rows()==1)
        {
            $sql = "DELETE FROM updates WHERE id='".$ids[0]."'";
            mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            $path=$CONFIG['attachment_fspath'].'updates/'.$ids[0];
            if (file_exists($path)) deldir($path);
        }
    }
    unset($spam_array);
}

if (!empty($selected))
{
    foreach ($selected as $updateid)
    {
        $sql = "DELETE FROM updates WHERE id='$updateid'";
        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        $sql = "DELETE FROM tempincoming WHERE updateid='$updateid'";
        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        $path=$incident_attachment_fspath.'updates/'.$updateid;
        if (file_exists($path)) deldir($path);

        journal(CFG_LOGGING_NORMAL, 'Incident Log Entry Deleted', "Incident Log Entry $updateid was deleted", CFG_JOURNAL_INCIDENTS, $updateid);
    }
}


    ?>
    <script type="text/javascript">
    <!--
        function confirm_delete()
        {
            return window.confirm("This item will be permanently deleted.  Are you sure you want to continue?");
        }

        function submitform()
        {
            document.held_emails.submit();
        }

        function checkAll(checkStatus)
        {
            var frm = document.held_emails.elements;
            for(i = 0; i < frm.length; i++)
            {
                if (frm[i].type == 'checkbox')
                {
                    if (checkStatus)
                    {
                        frm[i].checked = true;
                    }
                    else
                    {
                        frm[i].checked = false;
                    }
                }
            }
        }
        -->
    </script>

<?php

// extract updates
$sql  = 'SELECT updates.id as id, updates.bodytext as bodytext, tempincoming.emailfrom as emailfrom, tempincoming.subject as subject, ';
$sql .= 'updates.timestamp as timestamp, tempincoming.incidentid as incidentid, tempincoming.id as tempid, tempincoming.locked as locked, ';
$sql .= 'tempincoming.reason as reason, tempincoming.contactid as contactid, tempincoming.`from` as fromaddr ';
$sql .= 'FROM updates, tempincoming WHERE updates.incidentid=0 AND tempincoming.updateid=updates.id ';
$sql .= 'ORDER BY timestamp ASC, id ASC';
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
$countresults=mysql_num_rows($result);

$spamcount=0;
if ($countresults > 0)
{
    if ($countresults) mysql_data_seek($result, 0);

    while ($updates = mysql_fetch_array($result))
    {
        if (!stristr($updates['subject'],$CONFIG['spam_email_subject']))
        {
            $queuerows[$updates['id']] = generate_row($updates);
        }
        else
        {
            $spamcount++;
        }
    }
}

$sql = "SELECT * FROM `{$dbIncidents}` WHERE owner='0' AND status!='2'";
$resultnew = mysql_query($sql);
if (mysql_num_rows($resultnew) >= 1)
{
    while ($new = mysql_fetch_object($resultnew))
    {
        // Get Last Update
        list($update_userid, $update_type, $update_currentowner, $update_currentstatus, $update_body, $update_timestamp, $update_nextaction, $update_id)=incident_lastupdate($new->id);
        $update_body = parse_updatebody($update_body);
        $html = "<tr class='shade1'><td />";
        $html .= "<td align='center'>".date($CONFIG['dateformat_datetime'], $new->opened)."</td>";
        $html .= "<td>".contact_realname($new->contact)."</td>";
        $html .= "<td>".product_name($new->product)." / ".software_name($new->softwareid)."<br />";
        $html .= "[{$new->id}] <a href=\"javascript:incident_details_window('{$new->id}','holdingview');\" class='info'>{$new->title}<span>{$update_body}</span></a></td>";
        $html .= "<td style='text-align:center;'>Unassigned</td>";
        $html .= "<td style='text-align:center;'>";
        $html .= "<a href= \"javascript:incident_details_window('{$new->id}','holdingview');\" title='View this incident'>View</a> | ";
        $html .= "<a href= \"javascript:wt_winpopup('reassign_incident.php?id={$new->id}&amp;reason=Initial%20assignment%20to%20engineer&amp;popup=yes','mini');\" title='Assign this incident'>Assign</a></td>";
        $html .= "</tr>";
        $queuerows[$update_timestamp] = $html;
    }
}

$realemails = $countresults-$spamcount;

if ((mysql_num_rows($resultnew) > 0) OR ($realemails > 0))
{
    $totalheld = $countresults + mysql_num_rows($resultnew) - $spamcount;
    echo "<h2>".sprintf($strHeldEmailsNum, $realemails)."</h2>"; // was $countresults
    echo "<p align='center'>{$strIncomingEmailText}</p>";
    echo "<form action='{$_SERVER['PHP_SELF']}' name='held_emails'  method='post'>";
    echo "<table align='center' style='width: 95%'>";
    echo "<tr>";
    echo "<th>";
    if ($realemails > 0)
    {
        echo "<input type='checkbox' name='selectAll' value='CheckAll' onclick=\"checkAll(this.checked);\" />";
    }

    echo "</th>
    <th>{$strDate}</th>
    <th>{$strFrom}</th>
    <th>{$strSubject}</th>
    <th>{$strMessage}</th>
    <th>{$strOperation}</th>
    </tr>";
    sort($queuerows);
    foreach ($queuerows AS $row)
    {
        echo $row;
    }
    if ($realemails > 0)
    {
        echo "<tr><td>";
        echo "<a href=\"javascript: submitform()\" onclick='return confirm_delete();'>{$strDelete}</a>";
        echo "</td></tr>";
    }
    echo "</table>\n";
    echo "</form>";
}
else if ($spamcount == 0)
{
    echo "<h2>{$strNoRecords}</h2>";
}

if ($spamcount > 0)
{
    // FIXME i18n
    echo "<h2>Spam Email";
    if ($spamcount > 1) echo "s";
    echo " ({$spamcount} total)</h2>\n";
    echo "<p align='center'>Incoming email that is suspected to be spam</p>";

    // Reset back for 'nasty' emails
    if ($countresults) mysql_data_seek($result, 0);

    echo "<table align='center' style='width: 95%;'>";
    echo "<tr><th /><th>{$strDate}</th><th>{$strFrom}</th>";
    echo "<th>{$strSubject}</th><th>{$strMessage}</th>";
    echo "<th>{$strOperation}</th></tr>\n";

    while ($updates = mysql_fetch_array($result))
    {
        if (stristr($updates['subject'],$CONFIG['spam_email_subject']))
        {
            echo generate_row($updates);
            $spam_array[]=$updates['id'].'_'.$updates['tempid'];
        }
    }
    echo "</table>";
    // FIXME i18n
    if (is_array($spam_array)) echo "<p align='center'><a href={$_SERVER['PHP_SELF']}?delete_all_spam=".implode(',',$spam_array).'>Delete all mail from spam queue</a></p>';

    echo "<br /><br />"; //gap
}


$sql = "SELECT i.id, i.title, contacts.forenames, contacts.surname, sites.name ";
$sql .= "FROM `{$dbIncidents}` AS i,contacts,sites ";
$sql .= "WHERE i.status = 8 AND i.contact = contacts.id AND contacts.siteid = sites.id ";
$sql .= "ORDER BY sites.id, i.contact"; //awaiting customer action
$resultchase = mysql_query($sql);
if (mysql_num_rows($resultchase) >= 1)
{
    $shade='shade1';
    while ($chase = mysql_fetch_object($resultchase))
    {
        $sql_update = "SELECT * FROM updates WHERE incidentid = {$chase	->id} ORDER BY timestamp DESC LIMIT 1";
        $result_update = mysql_query($sql_update);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        $obj_update = mysql_fetch_object($result_update);

        if ($obj_update ->type == 'auto_chase_phone' OR $obj_update ->type == 'auto_chase_manager')
        {
            if (empty($html_chase))
            {
                $html_chase .= "<br />";
                $html_chase .= "<h2>Incidents requiring chasing by phone</h2>"; // FIXME i18n Incidents requiring chasing
                $html_chase .= "<table align='center' style='width: 95%'>";
                $html_chase .= "<tr><th>{$strIncident} {$strID}</th>";
                $html_chase .= "<th>{$strIncidentTitle}</th><th>{$strContact}</th><th>{$strSite}</th><th>{$strType}</th></tr>";
            }

            if ($obj_update->type == "auto_chase_phone")
            {
                $type = "Chase phone";
            }
            else
            {
                $type = "Chase manager";
            }

            // show
            $html_chase .= "<tr class='{$shade}'><td><a href=\"javascript:incident_details_window('{$obj_update->incidentid}','incident{$obj_update->incidentid}')\" class='info'>{$obj_update->incidentid}</a></td><td>{$chase->title}</td><td>{$chase->forenames} {$chase->surname}</td><td>{$chase->name}</td><td>{$type}</td></tr>";

            if ($shade=='shade1') $shade='shade2';
            else $shade='shade1';
        }
    }
}

if (!empty($html_chase))
{
    echo $html_chase;
    echo "</table>";
}

$sql = "SELECT * FROM tempassigns,incidents WHERE tempassigns.incidentid=incidents.id AND assigned='no' ";
$result = mysql_query($sql);

if (mysql_num_rows($result) >= 1)
{
    echo "<br />\n";

    echo "<h2>Pending Re-Assignments</h2>";
    echo "<p align='center'>Automatic reassignments that could not be made because users were set to 'not accepting'</p>";
    echo "<table align='center' style='width: 95%;'>";
    echo "<tr><th title='Last Updated'>{$strDate}</th><th title='Current Owner'>{$strFrom}</th>";
    echo "<th title='Incident Title'>{$strSubject}</th><th>{$strMessage}</th>";
    echo "<th>{$strOperation}</th></tr>\n";

    while ($assign = mysql_fetch_object($result))
    {
        // $originalownerstatus=user_status($assign->originalowner);
        $useraccepting=strtolower(user_accepting($assign->originalowner));
        if (($assign->owner == $assign->originalowner || $assign->towner == $assign->originalowner) AND $useraccepting=='no')
        {
            echo "<tr class='shade1'>";
            echo "<td align='center'>".date($CONFIG['dateformat_datetime'], $assign->lastupdated)."</td>";
            echo "<td>".user_realname($assign->originalowner,TRUE)."</td>";
            echo "<td>".software_name($assign->softwareid)."<br />[<a href=\"javascript:wt_winpopup('incident_details.php?id={$assign->id}&amp;popup=yes', 'mini')\">{$assign->id}</a>] ".$assign->title."</td>";
            $userstatus=userstatus_name($assign->userstatus);
            $usermessage=user_message($assign->originalowner);
            $username=user_realname($assign->originalowner,TRUE);
            echo "<td>Owner {$userstatus} &amp; not accepting<br />{$usermessage}</td>";
            $backupid=software_backup_userid($assign->originalowner, $assign->softwareid);
            $backupname=user_realname($backupid,TRUE);
            $reason = urlencode(trim("Previous Incident Owner ($username) {$userstatus}  {$usermessage}"));
            echo "<td>";
            if ($backupid >= 1) echo "<a href=\"javascript:wt_winpopup('reassign_incident.php?id={$assign->id}&amp;reason={$reason}&amp;backupid={$backupid}&amp;asktemp=temporary&amp;popup=yes','mini');\" title='Re-assign this incident to {$backupname}'>Assign to Backup</a> | ";

            echo "<a href=\"javascript:wt_winpopup('reassign_incident.php?id={$assign->id}&amp;reason={$reason}&amp;asktemp=temporary&amp;popup=yes','mini');\" title='Re-assign this incident to another engineer'>Assign to other</a> | <a href='set_user_status.php?mode=deleteassign&amp;incidentid={$assign->incidentid}&amp;originalowner={$assign->originalowner}' title='Ignore this reassignment and delete this notice'>Ignore</a></td>";
            echo "</tr>\n";
        }
        elseif ($assign->owner != $assign->originalowner AND $useraccepting=='yes')
        {
            // display a row to assign the incident back to the original owner
            echo "<tr class='shade2'>";
            echo "<td>".date($CONFIG['dateformat_datetime'], $assign->lastupdated)."</td>";
            echo "<td>".user_realname($assign->owner,TRUE)."</td>";
            echo "<td>[<a href=\"javascript:wt_winpopup('incident_details.php?id={$assign->id}&amp;popup=yes', 'mini')\">{$assign->id}</a>] {$assign->title}</td>";
            $userstatus=user_status($assign->originalowner);
            $userstatusname=userstatus_name($userstatus);
            $origstatus=userstatus_name($assign->userstatus);
            $usermessage=user_message($assign->originalowner);
            $username=user_realname($assign->owner,TRUE);
            echo "<td>Owner {$userstatusname} &amp; accepting again<br />{$usermessage}</td>";
            $originalname=user_realname($assign->originalowner,TRUE);
            $reason = urlencode(trim("{$originalname} is now accepting incidents again. Previous status {$origstatus} and not accepting."));
            echo "<td>";
            echo "<a href=\"javascript:wt_winpopup('reassign_incident.php?id={$assign->id}&amp;reason={$reason}&amp;originalid={$assign->originalowner}&amp;popup=yes','mini');\" title='Re-assign this incident to {$originalname}'>Return to original owner</a> | ";

            echo "<a href=\"javascript:wt_winpopup('reassign_incident.php?id={$assign->id}&amp;reason={$reason}&amp;asktemp=temporary&amp;popup=yes','mini');\" title='Re-assign this incident to another engineer'>Assign to other</a> | <a href='set_user_status.php?mode=deleteassign&amp;incidentid={$assign->incidentid}&amp;originalowner={$assign->originalowner}' title='Ignore this reassignment and delete this notice'>Ignore</a></td>";
            echo "</tr>\n";
        }
    }
    echo "</table>\n";
}


// TODO v3.2x Merge the sections into a single queue using an array

include ('htmlfooter.inc.php');
?>
