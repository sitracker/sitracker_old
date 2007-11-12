<?php
// reassign_incident.php - Form for re-assigning an incident to another user
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
// FIXME i18n

$permission=13; // Reassign Incident
require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

$forcepermission = user_permission($sit[2],40);

// External variables
$bodytext = cleanvar($_REQUEST['bodytext']);
$id = cleanvar($_REQUEST['id']);
$incidentid=$id;
$backupid = cleanvar($_REQUEST['backupid']);
$originalid = cleanvar($_REQUEST['originalid']);
$reason = cleanvar($_REQUEST['reason']);

if (empty($bodytext))
{
    // No submit detected show reassign form
    $title = $strReassign;
    include('incident_html_top.inc.php');
    ?>

    <?php
    $suggested = suggest_reassign_userid($id);
    $sql = "SELECT * FROM incidents WHERE id='$id' LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $incident = mysql_fetch_object($result);

    echo "<form name='assignform' action='{$_SERVER['PHP_SELF']}?id={$id}' method='post'>";

    $sql = "SELECT * FROM users WHERE status!=0 AND NOT id=$sit[2] ";
    if ($suggested) $sql .= "AND NOT id='$suggested' ";
    if (!$forcepermission) $sql .= "AND accepting='Yes' ";
    $sql .= "ORDER BY realname";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        if ($incident->towner==$sit[2])
        {
            echo "<p>You are the temporary owner of this incident.";
            echo "<br />\n".user_realname($incident->owner,TRUE)." is the original owner.</p>";
        }
        elseif ($incident->owner==$sit[2])
        {
            echo "<p>You are the owner of this incident.";
            if ($incident->towner > 0) echo "<br />\n".user_realname($incident->towner,TRUE)." also has temporary ownership.";
            echo "</p>";
        }
        else
        {
            echo "<p>".user_realname($incident->owner,TRUE).".";
            if ($incident->towner > 0) echo "<br />\n".user_realname($incident->towner,TRUE)." also has temporary ownership.";
            echo "</p>";
        }


    echo "<table align='center'>";
    echo "<tr>
        <th colspan='2'>Reassign to</th>
                <th colspan='5'>{$strIncidentsinQueue}</th>
                <th>{$strAccepting}</th>
            </tr>";
    echo "<tr>

        <th>{$strName}</th>
                <th>{$strStatus}</th>
        <th align='center'>{$strActionNeeded} / {$strOther}</th>";
    echo "<th align='center'>".priority_icon(4)."</th>";
    echo "<th align='center'>".priority_icon(3)."</th>";
    echo "<th align='center'>".priority_icon(2)."</th>";
    echo "<th align='center'>".priority_icon(1)."</th>";
    echo "<th></th></tr>\n";

    if ($suggested)
    {
        // Suggested user is shown as the first row
        $sugsql = "SELECT * FROM users WHERE id='$suggested' LIMIT 1";
        $sugresult = mysql_query($sugsql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        $suguser = mysql_fetch_object($sugresult);
        echo "<tr class='idle'>";
        echo "<td><label><input type='radio' name='userid' checked='checked' value='{$suguser->id}' /> ";
        // Have a look if this user has skills with this software
        $ssql = "SELECT softwareid FROM usersoftware WHERE userid={$suguser->id} AND softwareid={$incident->softwareid} ";
        $sresult = mysql_query($ssql);
        if (mysql_error()) trigger_error("MySQL Query Error".mysql_error(), E_USER_ERROR);
        if (mysql_num_rows($sresult) >=1 ) echo "<strong>".stripslashes($suguser->realname)."</strong>";
        else echo stripslashes($users->realname);
        echo "</label></td>";
        echo "<td>".userstatus_name($suguser->status)."</td>";
        $incpriority = user_incidents($suguser->id);
        $countincidents = ($incpriority['1']+$incpriority['2']+$incpriority['3']+$incpriority['4']);

        if ($countincidents >= 1) $countactive=user_activeincidents($suguser->id);
        else $countactive=0;
        $countdiff=$countincidents-$countactive;
        echo "<td align='center'>$countactive / {$countdiff}</td>";
        echo "<td align='center'>".$incpriority['4']."</td>";
        echo "<td align='center'>".$incpriority['3']."</td>";
        echo "<td align='center'>".$incpriority['2']."</td>";
        echo "<td align='center'>".$incpriority['1']."</td>";
        echo "<td align='center'>";
        echo $suguser->accepting=='Yes' ? $strYes : "<span class='error'>{$strNo}</span>";
        echo "</td>";
        echo "</tr>\n";
    }
    $countusers = mysql_num_rows($result);
    if ($countusers >= 1)
    {
        // Other users are shown in a optional section
        if ($suggested) echo "<tbody id='moreusers' style='display:none;'>";
        $shade='shade1';

        while ($users = mysql_fetch_object($result))
        {
            echo "<tr class='$shade'>";
            echo "<td><label><input type='radio' name='userid' value='{$users->id}' /> ";
            // Have a look if this user has skills with this software
            $ssql = "SELECT softwareid FROM usersoftware WHERE userid={$users->id} AND softwareid={$incident->softwareid} ";
            $sresult = mysql_query($ssql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            if (mysql_num_rows($sresult) >=1 ) echo "<strong>".stripslashes($users->realname)."</strong>";
            else echo stripslashes($users->realname);
            echo "</label></td>";
            echo "<td>".userstatus_name($users->status)."</td>";
            $incpriority = user_incidents($users->id);
            $countincidents = ($incpriority['1']+$incpriority['2']+$incpriority['3']+$incpriority['4']);

            if ($countincidents >= 1) $countactive=user_activeincidents($users->id);
            else $countactive=0;
            $countdiff=$countincidents-$countactive;
            echo "<td align='center'>$countactive / {$countdiff}</td>";
            echo "<td align='center'>".$incpriority['4']."</td>";
            echo "<td align='center'>".$incpriority['3']."</td>";
            echo "<td align='center'>".$incpriority['2']."</td>";
            echo "<td align='center'>".$incpriority['1']."</td>";
            echo "<td align='center'>";
            echo $users->accepting=='Yes' ? $strYes : "<span class='error'>{$strNo}</span>";
            echo "</td>";
            echo "</tr>\n";
            if ($shade=='shade1') $shade='shade2';
            else $shade='shade1';
        }
        if ($suggested) echo "</tbody>";
        echo "</table><br />";
        if ($suggested) echo "<p id='morelink'><a href=\"#\" onclick=\"$('moreusers').toggle();$('morelink').toggle();\">{$countusers} {$strMore}</a></p>";
    }
    echo "<table class='vertical'>";


    if (empty($_REQUEST['backupid']) AND empty($_REQUEST['originalid']))
    {
    /*
        //
        // Radio Buttons, how should the incident be reassigned
        //
        if ($incident->owner == 0) echo "<tr><th>{$strReassign}:</th>";
        else echo "<tr><th>Reassign:</th>";
        echo "<td class='shade2'>";
        $suggested = suggest_reassign_userid($id);
        if (!empty($suggested)) echo user_realname($suggested)."<br />";
        if ($incident->towner == $sit[2])
        {
            // you are the temporary owner
            echo "<input type='radio' name='assign' value='permassign' checked='checked' />Assign back to original owner (".user_realname($incident->owner,TRUE).")<br />";
            echo "<input type='hidden' name='permnewowner' value='{$incident->owner}' />";
            echo "<input type='radio' name='assign' value='tempassign' />Reassign temporary ownership to ";
            user_drop_down("tempnewowner", 0, TRUE, array($incident->owner,$incident->towner), "onclick=\"document.assignform.assign[1].checked=true;\"");
        }
        elseif ($incident->owner == $sit[2])
        {
            // you are the perm owner
            if ($incident->towner == 0)
            {
                echo "<input type='radio' name='assign' value='tempassign' />Assign temporary ownership to ";
                user_drop_down("tempnewowner", 0, TRUE, array($incident->owner,$incident->towner), "onclck=\"document.assignform.assign[0].checked=true;\"");
                echo "<br />\n";
            }
            else
            {
                echo "<input type='radio' name='assign' value='tempassign' />Change temporary owner from ".user_realname($incident->towner,TRUE)." to ";
                user_drop_down("tempnewowner", 0, TRUE, array($incident->owner,$incident->towner), "onclick=\"document.assignform.assign[0].checked=true;\"");
                echo "<br />\n";
                echo "<input type='radio' name='assign' checked='checked' value='deltempassign' />Remove temporary ownership";
                echo "<br />\n";
            }
            echo "<input type='radio' name='assign' value='permassign' />Reassign to ";
            user_drop_down("permnewowner", 0, TRUE, array($incident->owner), "onclick=\"document.assignform.assign[1].checked=true;\"");
        }
        else if ($incident->owner != 0 )
        {
            // you are not the owner or the temp owner
            if ($incident->towner == 0)
            {
                echo "<input type='radio' name='assign' value='tempassign' checked='checked' />Assign temporary ownership to ";
                user_drop_down("tempnewowner", $sit[2], TRUE, array($incident->owner,$incident->towner), "onclick=\"document.assignform.assign[0].checked=true;\"");
                echo "<br />\n";
            }
            else
            {
                echo "<input type='radio' name='assign' value='tempassign' />Change temporary owner from ".user_realname($incident->towner,TRUE)." to ";
                user_drop_down("tempnewowner", 0, TRUE, array($incident->owner,$incident->towner), "onclick=\"document.assignform.assign[0].checked=true;\"");
                echo "<br />\n";
            }
            echo "<input type='radio' name='assign' value='permassign' />Reassign to ";
            user_drop_down("permnewowner", 0, TRUE, array($incident->owner), "onclick=\"document.assignform.assign[1].checked=true;\"");
        }
        else
        {
            // The incident has no owner at all
            echo "<input type='hidden' name='assign' value='permassign' />Assign to ";
            user_drop_down("permnewowner", 0, TRUE, array($incident->owner), "onclick=\"document.assignform.assign[1].checked=true;\"");
            $incident->status='1';
        }
        echo "</td></tr>\n";*/

        /*
        echo "<tr><td align='right' class='shade1'><strong>Reassign To</strong>:</td>";
        echo "<td class='shade2'>";
        user_drop_down("newowner", incident_owner($id));
        echo "</td></tr>\n";
        */
    }
    elseif (!empty($originalid))
    {
        echo "<tr><th>{$strReassign}:</th>";
        echo "<td>Reassign to original engineer (".user_realname($originalid,TRUE).")";
        echo "<input type='hidden' name='permnewowner' value='{$originalid}' />";
        echo "<input type='hidden' name='permassign' value='{$originalid}' />";
        echo "</td></tr>\n";
    }
    elseif (!empty($backupid))
    {
        echo "<tr><th>{$strReassign}:</strong>:</th>";
        echo "<td>To Substitute Engineer (".user_realname($backupid,TRUE).")";
        echo "<input type='hidden' name='tempnewowner' value='{$backupid}' />";
        echo "<input type='hidden' name='tempassign' value='{$originalid}' />";
        echo "</td></tr>\n";
    }

    echo "<tr><td colspan='2'><br />{$strReassignText}</td></tr>\n";
    echo "<tr><th>{$strUpdate}:</th>";
    echo "</th><td>";
    echo "<textarea name='bodytext' wrap='soft' rows='10' cols='65'>";
    if (!empty($reason)) echo $reason;
    echo "</textarea>";
    echo "</td></tr>\n";
    echo "<tr><th>Temporary:</th><td><label><input type='checkbox' name='temporary' value='yes' /> Temporary</label></td></tr>\n";
    echo "<tr><th>{$strVisibility}:</th><td><label><input type='checkbox' name='cust_vis' value='yes' /> {$strVisibleToCustomer}</label></td></tr>\n";

    echo "<tr><th>{$strNewIncidentStatus}:</th>";
    ?>
    <td><?php echo incidentstatus_drop_down("newstatus", $incident->status); ?></td></tr>
    </table>
    <?php
    echo "<p align='center'><input name='submit' type='submit' value=\"{$strReassign}\" /></p>";
    echo "</form>\n";
    include('incident_html_bottom.inc.php');
}
else
{
    // External variables
    $tempnewowner = cleanvar($_REQUEST['tempnewowner']);
    $permnewowner = cleanvar($_REQUEST['permnewowner']);
    $newstatus = cleanvar($_REQUEST['newstatus']);
    $userid = cleanvar($_REQUEST['userid']);
    $id = cleanvar($_REQUEST['id']);


    echo "<h1>$userid</h1>";
    exit;

    // Reassign the incident
    if (($_REQUEST['assign']=='tempassign' AND user_accepting($tempnewowner) == "Yes")
        OR ($_REQUEST['assign']=='permassign' AND user_accepting($permnewowner) == "Yes")
        OR ($_REQUEST['assign']=='permassign' AND $permnewowner == $sit[2])
        OR ($_REQUEST['assign']=='tempassign' AND $tempnewowner == $sit[2])
        OR ($_REQUEST['assign']=='deltempassign')
        OR (user_permission($sit[2],40)==TRUE))  // Force reassign
    {
        $oldstatus=incident_status($id);
        if ($newstatus != $oldstatus)
        $bodytext = "Status: ".incidentstatus_name($oldstatus)." -&gt; <b>" . incidentstatus_name($newstatus) . "</b>\n\n" . $bodytext;

        // update incident
        $sql = "UPDATE incidents SET ";
        if ($_REQUEST['assign']=='tempassign') $sql .= "towner='{$tempnewowner}', ";
        elseif ($_REQUEST['assign']=='deltempassign') $sql .= "towner='0', ";
        elseif ($_REQUEST['assign']=='permassign') $sql .= "owner='{$permnewowner}', towner='0', "; // perm assign removed temp one
        else $sql .= "owner='{$permnewowner}', towner='0', "; // perm assign removed temp one
        $sql .= "status='$newstatus', lastupdated='$now' WHERE id='$id' LIMIT 1";
        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        // add update
        if ($_REQUEST['assign']=='tempassign')
        {
            $assigntype='tempassigning';
            if (strtolower(user_accepting($tempnewowner)) != "yes")
                $bodytext = "(Incident temp assignment was forced because the user was not accepting)<hr>\n" . $bodytext;
        }
        elseif ($_REQUEST['assign']=='deltempassign')
        {
            $assigntype='reassigning';
        }
        else
        {
            $assigntype='reassigning';
            if (strtolower(user_accepting($permnewowner)) != "yes")
                $bodytext = "(Incident assignment was forced because the user was not accepting)<hr>\n" . $bodytext;
        }
        if ($_REQUEST['cust_vis']=='yes') $customervisibility='show';
        else $customervisibility='hide';


        $sql  = "INSERT INTO updates (incidentid, userid, bodytext, type, timestamp, currentowner, currentstatus, customervisibility) ";
        $sql .= "VALUES ($id, $sit[2], '$bodytext', '$assigntype', '$now', ";
        if ($_REQUEST['assign']=='permassign') $sql .= "'$permnewowner', ";
        elseif ($_REQUEST['assign']=='deltempassign') $sql .= "'{$sit[2]}', ";
        else $sql .= "'$tempnewowner', ";
        $sql .= "'$newstatus', '$customervisibility')";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        // Remove any tempassigns that are pending for this incident
        $sql = "DELETE FROM tempassigns WHERE incidentid='$id'";
        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        $newowner = '';
        if($_REQUEST['assign']=='permassign') $newowner = $permnewowner;
        else if($_REQUESR['assign']<>'deltempassign') $newowner = $tempnewowner; //not interested in deltemp state

        if(!empty($newowner))
        {
            if(user_notification_on_reassign($newowner)=='true')
            {
                send_template_email('INCIDENT_REASSIGNED_USER_NOTIFY', $id);
            }
        }

        journal(CFG_LOGGING_FULL,'Incident Reassigned', "Incident $id reassigned to user id $newowner", CFG_JOURNAL_SUPPORT, $id);

        if (!$result)
        {
            include('includes/incident_html_top.inc');
            echo "<p class='error'>Reassignment Failed</p>\n";
            include('includes/incident_htmlfooter.inc.php');
        }
        else  confirmation_page("2", "incident_details.php?id=" . $id, "<h2>Reassignment Successful</h2><h5>{$strPleaseWaitRedirect}...</h5>");
    }
    else
    {
        confirmation_page("4", "reassign_incident.php?id={$id}", "<h2 class='error'>Error</h2><h3>That user is not accepting incidents.</h3><h5>{$strPleaseWaitRedirect}...</h5>");
    }
}
?>