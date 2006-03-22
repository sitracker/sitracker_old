<?php
// incidents_table.inc.php - Prints out a table of incidents based on the query that was executed in the page that included this file
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

// This Page Is Valid XHTML 1.0 Transitional!

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}

if ($CONFIG['debug']) echo "<!-- Support Incidents Table -->";
echo "<table align='center' style='width:95%;'>";
?>
<col width='10%'></col><col width='23%'></col><col width='17%'></col><col width='7%'></col><col width='10%'></col><col width='15%'></col><col width='10%'></col><col width='10%'></col><col width='8%'></col>
<tr>
<?php
echo "<th title='{$rowcount} Incidents'><a href='{$_SERVER['PHP_SELF']}{$querystring}sort=id&amp;sortorder={$newsortorder}'>ID</a></th>\n";
?>
<th align='left'><a href="<?php echo $_SERVER['PHP_SELF']; ?><?php echo $querystring ?>sort=title&amp;sortorder=<?php echo $newsortorder; ?>">Title</a></th>
<th align='left'><a href="<?php echo $_SERVER['PHP_SELF'] ?><?php echo $querystring ?>sort=contact&amp;sortorder=<?php echo $newsortorder; ?>">Contact</a></th>
<th><a href="<?php echo $_SERVER['PHP_SELF'] ?><?php echo $querystring ?>sort=priority&amp;sortorder=<?php echo $newsortorder; ?>">Priority</a></th>
<th><a href="<?php echo $_SERVER['PHP_SELF'] ?><?php echo $querystring ?>sort=status&amp;sortorder=<?php echo $newsortorder; ?>">Status</a></th>
<th><a href="<?php echo $_SERVER['PHP_SELF'] ?><?php echo $querystring ?>sort=lastupdated&amp;sortorder=<?php echo $newsortorder; ?>">Last Update</a></th>
<th><a href="<?php echo $_SERVER['PHP_SELF'] ?><?php echo $querystring ?>sort=nextaction&amp;sortorder=<?php echo $newsortorder; ?>">SLA Target</a></th>
<th><a href="<?php $_SERVER['PHP_SELF'] ?><?php echo $querystring ?>sort=duration&amp;sortorder=<?php echo $newsortorder; ?>">Info</a></th>
</tr>
<?php

// Display the Support Incidents Themselves
$shade = 0;
while ($incidents = mysql_fetch_array($result))
{
    // calculate time to next action string
    if ($incidents["timeofnextaction"] == 0) $timetonextaction_string = "&nbsp;";  // was 'no time set'
    else
    {
        if (($incidents["timeofnextaction"] - $now) > 0) $timetonextaction_string = format_seconds($incidents["timeofnextaction"] - $now);
        else $timetonextaction_string = "<strong>Now</strong>";
    }
    // Make a readable site name
    $site = site_name($incidents['siteid']);
    $site = strlen($site) > 30 ? substr($site,0,30)."..." : $site;

    // Make a readble last updated field
    if (date('dmy', $incidents['lastupdated']) == date('dmy', time()))
        $updated = "Today @ ".date($CONFIG['dateformat_time'], $incidents['lastupdated']);
    elseif (date('dmy', $incidents['lastupdated']) == date('dmy', (time()-86400)))
        $updated = "Yesterday @ ".date($CONFIG['dateformat_time'], $incidents['lastupdated']);
    elseif (date('dmy', $incidents['lastupdated']) < date('dmy', (time()-86400)) AND
            date('dmy', $incidents['lastupdated']) > date('dmy', (time()-(86400*6))))
        $updated = date('l', $incidents['lastupdated'])." @ ".date($CONFIG['dateformat_time'], $incidents['lastupdated']);
    else
        $updated = date($CONFIG['dateformat_datetime'], $incidents["lastupdated"]);

    // Fudge for old ones
    $tag = $incidents['servicelevel'];
    if ($tag=='') $tag = servicelevel_id2tag(maintenance_servicelevel($incidents['maintenanceid']));

    $slsql = "select * from servicelevels where tag='{$tag}' and priority='{$incidents['priority']}' ";
    $slresult = mysql_query($slsql);
    if (mysql_error()) trigger_error("mysql query error ".mysql_error(), E_USER_ERROR);
    $servicelevel = mysql_fetch_object($slresult);
    if (mysql_num_rows($slresult) < 1) trigger_error("could not retrieve service level ($slsql)", E_USER_WARNING);

    // Get Last Update
    list($update_userid, $update_type, $update_currentowner, $update_currentstatus, $update_body, $update_timestamp, $update_nextaction)=incident_lastupdate($incidents['id']);

    // Get next target
    $target = incident_get_next_target($incidents['id']);
    $working_day_mins = ($CONFIG['end_working_day'] - $CONFIG['start_working_day']) / 60;
    // Calculate time remaining in SLA
    switch ($target->type)
    {
        case 'initialresponse': $slatarget=$servicelevel->initial_response_mins; break;
        case 'probdef': $slatarget=$servicelevel->prob_determ_mins; break;
        case 'actionplan': $slatarget=$servicelevel->action_plan_mins; break;
        case 'solution': $slatarget=($servicelevel->resolution_days * $working_day_mins); break;
        default: $slaremain=0; $slatarget=0;
    }
    if ($slatarget >0) $slaremain=($slatarget - $target->since);
    else $slaremain=0;

    // Get next review time
    $reviewsince = incident_get_next_review($incidents['id']);  // time since last review in minutes
    $reviewtarget=($servicelevel->review_days * 480);          // how often reviews should happen in minutes
    if ($reviewtarget >0) $reviewremain=($reviewtarget - $reviewsince);
    else $reviewremain=0;

    ##echo "<!-- target-info: ";
    ##print_r($target);
    ##echo "-->";

    // Remove Tags from update Body
    $update_body=trim($update_body);
    if (!empty($update_body))
    {
        $update_body=strip_tags($update_body);
        $update_body=nl2br(htmlspecialchars($update_body));
        $update_body=str_replace("&amp;gt;", "&gt;", $update_body);
        $update_body=str_replace("&amp;lt;", "&lt;", $update_body);
        if (strlen($update_body)>490) $update_body .= '...';
    }
    $update_user = user_realname($update_userid);

    // ======= Row Colors / Shading =======
    // Define Row Shading lowest to highest priority so that unimportant colors are overwritten by important ones
    switch($queue)
    {
        case 1: // Action Needed
            $class='shade2';
            $explain='';
            if ($slaremain >= 1)
            {
                if (($slaremain - ($slatarget * 0.15 )) < 0 ) $class='notice';
                if (($slaremain - ($slatarget * 0.10 )) < 0 ) $class='urgent';
                if (($slaremain - ($slatarget * 0.05 )) < 0 ) $class='critical';
                if ($incidents["priority"]==4) $class='critical';  // Force critical incidents to be critical always
            }
            elseif ($slaremain < 0) $class='critical';
            else
            {
                $class='shade1';
                $explain='';  // No&nbsp;Target
            }
            // if ($target->time > $now + ($target->targetval * 0.10 )) $class='critical';
        break;

        case 2: // Waiting
            $class='idle';
            $explain='No Action Set';
        break;

        case 3: // All Open
            $class='shade2';
            $explain='No Action Set';
        break;

        case 4: // All Closed
            $class='expired';
            $explain='No Action';
        break;
    }

    // Set Next Action text if not already set
    if ($update_nextaction=='') $update_nextaction=$explain;

    // Create URL for External ID's
    $externalid = format_external_id($incidents['externalid']);
    ?>
    <tr class='<?php echo $class; ?>'>
    <td align='center'>
    <?php
    // Note: Sales incident type is obsolete
    if ($incidents['type']!='Support') echo "<strong>".ucfirst($incidents['type'])."</strong>: ";
    echo "<a href='incident_details.php?id={$incidents['id']}' style='color: #000000;'>{$incidents['id']}</a>";
    ?><br /><?php if ($incidents["externalid"] != "") echo $externalid ?></td>
    <td>
    <?php
    if (!empty($incidents['softwareid'])) echo software_name($incidents['softwareid'])."<br />";
    ?>
    <a href="javascript:incident_details_window('<?php echo $incidents["id"] ?>','incident<?php echo $incidents["id"] ?>')" class='info'>
    <?php if (trim($incidents['title']) !='') echo htmlspecialchars(stripslashes($incidents['title'])); else echo 'Untitled';
    if (!empty($update_body) AND $update_body!='...') echo "<span>{$update_body}</span>";
    else
    {
        $update_currentownername = user_realname($update_currentowner);
        echo "<span>".str_replace('currentowner', $update_currentownername, $updatetypes[$update_type]['text'])." by {$update_user} on ".date($CONFIG['dateformat_datetime'],$update_timestamp)." </span>";
    }
    echo "</a></td>";

    echo "<td valign='top'>";
    echo $incidents['forenames'].' '.$incidents['surname']."<br />".htmlspecialchars($site)." </td>";
    ?>
    <td align='center' valign="top" ><?php
    // Service Level / Priority
    if (!empty($incidents['maintenanceid'])) echo $servicelevel->tag."<br />";
    elseif (!empty($incidents['servicelevel'])) echo $incidents['servicelevel']."<br />";
    else echo "Unknown service level<br />";
    $blinktime=(time()-($servicelevel->initial_response_mins * 60));
    if ($incidents['priority']==4 AND $incidents['lastupdated']<= $blinktime) echo "<strong style='text-decoration: blink;'>".priority_name($incidents["priority"])."</strong>";
        else echo priority_name($incidents['priority']);
    echo "</td>\n";

    echo "<td align='center' valign='top'>";
    echo incidentstatus_name($incidents["status"]);
    echo "</td>\n";
    echo "<td align='center' valign='top'>";
    echo "{$updated}<br />by {$update_user}";
    if ($incidents['towner'] > 0 AND $incidents['towner']!=$user) echo "<br />Temp: <strong>".user_realname($incidents['towner'])."</strong>";
    elseif ($incidents['owner']!=$user) echo '<br />Owner: <strong>'.user_realname($incidents['owner'])."</strong>";
    echo "</td>\n";
    echo "<td align='center' valign='top' title='{$explain}'>";
    // Next Action
    /*
      if ($target->time > $now) echo target_type_name($target->type);
      else echo "<strong style='color: red; background-color: white;'>&nbsp;".target_type_name($target->type)."&nbsp;</strong>";
    */
    $targettype = target_type_name($target->type);
    if ($targettype!='')
    {
        echo $targettype;
        if ($slaremain > 0)
        {
            echo "<br />in ".format_workday_minutes($slaremain);  //  ." left"
        }
        elseif ($slaremain < 0)
        {
            echo "<br />".format_workday_minutes((0-$slaremain))." late";  //  ." left"
        }
    }
    else
    {
        ## Don't print anything, because there is no target to meet
        //echo "...";
    }
    ##print_r($target);

    ##echo target_type_name($target->type);
    ##echo "<br />";
    ##if ($update_nextaction!=target_type_name($target->type))
    ##  echo "$update_nextaction";
    ##if (!empty($timetonextactionstring)) echo "<br />$timetonextaction_string";
    echo "</td>";

    // Final column
    if ($reviewremain>0 && $reviewremain<=2400)
    {
        // Only display if review is due in the next five days
        echo "<td align='center' valign='top'>";
        echo "Review in ".format_workday_minutes($reviewremain);
    }
    elseif ($reviewremain<=0)
    {
        echo "<td align='center' valign='top' class='review'>";
        echo "Review Due Now!";
    }
    else
    {
        echo "<td align='center' valign='top'>";
        if ($incidents['status'] == 2) echo "Age: ".format_seconds($incidents["duration_closed"]);
        else echo format_seconds($incidents["duration"])." old";
    }
    echo "</td>";
    echo "</tr>\n";
}
echo "</table>\n\n";
if ($CONFIG['debug']) echo "<!-- End of Support Incidents Table -->\n";
