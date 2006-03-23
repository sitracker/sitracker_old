<?php
// reassign_incident.php - Form for re-assigning an incident to another user
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

$permission=13; // Reassign Incident
require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

// External variables
$bodytext = cleanvar($_REQUEST['bodytext']);
$id = cleanvar($_REQUEST['id']);
$backupid = cleanvar($_REQUEST['backupid']);
$originalid = cleanvar($_REQUEST['originalid']);
$reason = cleanvar($_REQUEST['reason']);

if (empty($bodytext))
{
    // No submit detected show reassign form
    $title = 'Reassign: '.$id . " - " . incident_title($id);
    include('incident_html_top.inc.php');
    ?>
    <form name='assignform' action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $id ?>" method="post">
    <table class='vertical'>
    <?php
    $sql = "SELECT * FROM incidents WHERE id='$id' LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $incident = mysql_fetch_object($result);

    echo "<tr><th>Current Owner:</th>";
    echo "<td>";
    if ($incident->towner==$sit[2])
    {
        echo "You are the temporary owner of this incident.";
        echo "<br />\n".user_realname($incident->owner)." is the original owner.";
    }
    elseif ($incident->owner==$sit[2])
    {
        echo "You are the owner of this incident.";
        if ($incident->towner > 0) echo "<br />\n".user_realname($incident->towner)." also has temporary ownership.";
    }
    else
    {
        echo user_realname($incident->owner).".";
        if ($incident->towner > 0) echo "<br />\n".user_realname($incident->towner)." also has temporary ownership.";
    }
    echo "</td></tr>";

    if (empty($_REQUEST['backupid']) AND empty($_REQUEST['originalid']))
    {
        if ($incident->softwareid > 0)
        {
            echo "<tr><th>Other users with relevent skills:</th>";
            $usql = "SELECT *,users.id AS userid FROM usersoftware, users WHERE usersoftware.userid=users.id AND usersoftware.softwareid={$incident->softwareid} ";
            $usql .= "ORDER BY realname";
            $uresult = mysql_query($usql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            $count_backup=mysql_num_rows($uresult);
            if ($count_backup > 0)
            {
                $count=1;
                echo "<td>";
                while ($backup = mysql_fetch_object($uresult))
                {
                    $count++;
                    if ($backup->userid!=$incident->owner)
                    {
                        echo "{$backup->realname}";
                        if ($count <=  $count_backup) echo ", ";
                    }
                }
                echo "</td>";
            }
            else echo "<td>None</td>";

            echo "</tr>\n";
        }

        //
        // Radio Buttons, how should the incident be reassigned
        //
        echo "<tr><th>Reassign:</th>";
        echo "<td class='shade2'>";
        if ($incident->towner == $sit[2])
        {
            // you are the temporary owner
            echo "<input type='radio' name='assign' value='permassign' checked='checked' />Assign back to original owner (".user_realname($incident->owner).")<br />";
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
                user_drop_down("tempnewowner", 0, TRUE, array($incident->owner,$incident->towner), "onclick=\"document.assignform.assign[0].checked=true;\"");
                echo "<br />\n";
            }
            else
            {
                echo "<input type='radio' name='assign' value='tempassign' />Change temporary owner from ".user_realname($incident->towner)." to ";
                user_drop_down("tempnewowner", 0, TRUE, array($incident->owner,$incident->towner), "onclick=\"document.assignform.assign[0].checked=true;\"");
                echo "<br />\n";
                echo "<input type='radio' name='assign' checked='checked' value='deltempassign' />Remove temporary ownership";
                echo "<br />\n";
            }
            echo "<input type='radio' name='assign' value='permassign' />Reassign to ";
            user_drop_down("permnewowner", 0, TRUE, array($incident->owner), "onclick=\"document.assignform.assign[1].checked=true;\"");
        }
        else
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
                echo "<input type='radio' name='assign' value='tempassign' />Change temporary owner from ".user_realname($incident->towner)." to ";
                user_drop_down("tempnewowner", 0, TRUE, array($incident->owner,$incident->towner), "onclick=\"document.assignform.assign[0].checked=true;\"");
                echo "<br />\n";
            }
            echo "<input type='radio' name='assign' value='permassign' />Reassign to ";
            user_drop_down("permnewowner", 0, TRUE, array($incident->owner), "onclick=\"document.assignform.assign[1].checked=true;\"");
        }
        echo "</td></tr>\n";

        /*
        echo "<tr><td align='right' class='shade1'><strong>Reassign To</strong>:</td>";
        echo "<td class='shade2'>";
        user_drop_down("newowner", incident_owner($id));
        echo "</td></tr>\n";
        */
    }
    elseif (!empty($originalid))
    {
        echo "<tr><th>Reassign:</th>";
        echo "<td>Reassign to original engineer (".user_realname($originalid).")";
        echo "<input type='hidden' name='permnewowner' value='{$originalid})' />";
        echo "<input type='hidden' name='permassign' value='{$originalid})' />";
        echo "</td></tr>\n";
    }
    elseif (!empty($backupid))
    {
        echo "<tr><th>Reassign</strong>:</th>";
        echo "<td>To Backup Engineer (".user_realname($backupid).")";
        echo "<input type='hidden' name='tempnewowner' value='{$backupid})' />";
        echo "<input type='hidden' name='tempassign' value='{$originalid})' />";
        echo "</td></tr>\n";
    }
    /*
    echo "<tr><td align='right' class='shade1'><strong>Type of Assignment</strong>:</td>";
    echo "<td class='shade2'><select name='asktemp'>";
    echo "<option value='permanent'";
    if ($_REQUEST['asktemp']=='permanent' OR $incident->towner < 1) echo " selected='selected'";
    echo ">Permanent (Change queue)</option>";
    echo "<option value='temporary'";
    if ($_REQUEST['asktemp']=='temporary' OR $incident->towner > 0) echo " selected='selected'";
    echo ">Temporary (Appears in both queues)</option>";
    echo "</select>";
    */
    ?>
    <tr>
    <th>Update Log:<br />
    Explain in detail why you are reassigning this incident and include instructions to the new owner as to what action should
    be taken next.  Please be as detailed as possible and include full descriptions of any work you have performed.<br />
    <br/>
    Check here <input type="checkbox" name="cust_vis" value="yes" /> to make this reassign visible to the customer.
    </th>
    <td>
    <?php
    echo "<textarea name='bodytext' wrap='soft' rows='15' cols='65'>";
    if (!empty($reason)) echo $reason;
    echo "</textarea>";
    echo "</td></tr>\n";

    echo "<tr><th>New Status:</th>";
    ?>

    <td><?php echo incidentstatus_drop_down("newstatus", incident_status($id)); ?></td></tr>
    </table>
    <?php
    echo "<p align='center'><input name='submit' type='submit' value='Reassign Incident' /></p>";
    ?>
    </form>
    <?php
    include('incident_html_bottom.inc.php');
}
else
{
    // External variables
    $tempnewowner = cleanvar($_REQUEST['tempnewowner']);
    $permnewowner = cleanvar($_REQUEST['permnewowner']);
    $newstatus = cleanvar($_REQUEST['newstatus']);
    $id = cleanvar($_REQUEST['id']);

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

        journal(CFG_LOGGING_FULL,'Incident Reassigned', "Incident $id reassigned to user id $newowner", CFG_JOURNAL_SUPPORT, $id);

        if (!$result)
        {
            include('includes/incident_html_top.inc');
            echo "<p class='error'>Reassignment Failed</p>\n";
            include('includes/incident_htmlfooter.inc.php');
        }
        else  confirmation_page("2", "incident_details.php?id=" . $id, "<h2>Reassignment Successful</h2><h5>Please wait while you are redirected...</h5>");
    }
    else
    {
        confirmation_page("4", "reassign_incident.php?id={$id}", "<h2 class='error'>Error</h2><h3>That user is not accepting incidents.</h3><h5>Please wait while you are returned...</h5>");
    }
}
?>