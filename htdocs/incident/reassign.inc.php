<?php
// reassign.inc.php - Reassign incident
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}
?>
    <form name='assignform' action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $id ?>" method="post">
    <table class='vertical'>
    <?php
    $sql = "SELECT * FROM incidents WHERE id='$id' LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    $incident = mysql_fetch_object($result);

    if ($incident->owner!=0)
    {
        echo "<tr><th>Current Owner:</th>";
        echo "<td>";
        if ($incident->towner==$sit[2])
        {
            echo "You are the temporary owner of this incident.";
            echo "<br />\n".user_realname($incident->owner,TRUE)." is the original owner.";
        }
        elseif ($incident->owner==$sit[2])
        {
            echo "You are the owner of this incident.";
            if ($incident->towner > 0) echo "<br />\n".user_realname($incident->towner,TRUE)." also has temporary ownership.";
        }
        else
        {
            echo user_realname($incident->owner,TRUE).".";
            if ($incident->towner > 0) echo "<br />\n".user_realname($incident->towner,TRUE)." also has temporary ownership.";
        }
        echo "</td></tr>";
    }
    if (empty($_REQUEST['backupid']) AND empty($_REQUEST['originalid']))
    {
        if ($incident->softwareid > 0)
        {
            echo "<tr><th>Users with relevent skills:</th>";
            $usql = "SELECT *,users.id AS userid FROM usersoftware, users WHERE usersoftware.userid=users.id AND usersoftware.softwareid={$incident->softwareid} ";
            $usql .= "AND users.status != 0 "; //the account isn't disabled
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
        if ($incident->owner == 0) echo "<tr><th>Reassign:</th>";
        else echo "<tr><th>Reassign:</th>";
        echo "<td class='shade2'>";
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
                user_drop_down("tempnewowner", 0, TRUE, array($incident->owner,$incident->towner), "onclick=\"document.assignform.assign[0].checked=true;\"");
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
        echo "<td>Reassign to original engineer (".user_realname($originalid,TRUE).")";
        echo "<input type='hidden' name='permnewowner' value='{$originalid}' />";
        echo "<input type='hidden' name='permassign' value='{$originalid}' />";
        echo "</td></tr>\n";
    }
    elseif (!empty($backupid))
    {
        echo "<tr><th>Reassign</strong>:</th>";
        echo "<td>To Backup Engineer (".user_realname($backupid,TRUE).")";
        echo "<input type='hidden' name='tempnewowner' value='{$backupid}' />";
        echo "<input type='hidden' name='tempassign' value='{$originalid}' />";
        echo "</td></tr>\n";
    }

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
    if (!empty($resassignmessage)) echo $resassignmessage;
    echo "</textarea>";
    echo "</td></tr>\n";

    echo "<tr><th>New Status:</th>";
    ?>
    <td><?php echo incidentstatus_drop_down("newstatus", $incident->status); ?></td></tr>
    </table>
    <?php
    echo "<p align='center'><input name='submit' type='submit' value='Reassign Incident' /></p>";
    ?>
    <input type="hidden" name='action' value="reassign" />
    </form>
