<?php
// log.inc.php - Displays the incident update log
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
//         Paul Heaney <paulheaney[at]users.sourceforge.net>

// Included by ../incident.php

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}

$offset = cleanvar($_REQUEST['offset']);
if (empty($offset))
{
    $offset = 0;
}

/**
    * @author Ivan Lucas
*/
function count_updates($incidentid)
{
    $count_updates = 0;
    $sql = "SELECT COUNT(id) FROM `{$GLOBALS['dbUpdates']}` WHERE incidentid='{$incidentid}'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
    list ($count_updates) = mysql_fetch_row($result);

    return $count_updates;
}

$count_updates = count_updates($incidentid);


/**
    * @author Paul Heaney
*/
function log_nav_bar()
{
    global $incidentid;
    global $firstid;
    global $updateid;
    global $offset;
    global $count_updates;
    global $records;

    if ($offset > $_SESSION['num_update_view'])
    {
        $previous = $offset - $_SESSION['num_update_view'];
    }
    else
    {
        $previous = 0;
    }
    $next = $offset + $_SESSION['num_update_view'];

    $nav .= "<table width='98%' align='center'><tr>";
    $nav .= "<td align='left' style='width: 33%;'>";
    if ($offset > 0)
    {
        $nav .= "<a href='{$_SERVER['PHP_SELF']}?id={$incidentid}&amp;";
        $nav .= "javascript=enabled&amp;offset={$previous}&amp;'>&lt;&lt; ";
        $nav .= "{$GLOBALS['strPrevious']}</a>";
    }
    $nav .= "</td>";
    $nav .= "<td align='center' style='width: 34%;'>";
    if ($count_updates > $_SESSION['num_update_view'])
    {
        if ($records != 'all')
        {
            $nav .= "<a href='{$_SERVER['PHP_SELF']}?id={$incidentid}&amp;";
            $nav .= "javascript=enabled&amp;offset=0&amp;records=all'>";
            $nav .= "{$GLOBALS['strShowAll']}</a>";
        }
        else
        {
            $nav .= "<a href='{$_SERVER['PHP_SELF']}?id={$incidentid}&amp;";
            $nav .= "javascript=enabled&amp;offset=0&amp;'>{$GLOBALS['strShowPaged']}</a>";
        }
    }
    $nav .= "</td>";
    $nav .= "<td align='right' style='width: 33%;'>";
    if ($offset < ($count_updates - $_SESSION['num_update_view']) AND
        $records!='all')
    {
        $nav .= "<a href='{$_SERVER['PHP_SELF']}?id={$incidentid}&amp;";
        $nav .= "javascript=enabled&amp;offset={$next}&amp;'>";
        $nav .= "{$GLOBALS['strNext']} &gt;&gt;</a>";
    }
    $nav .= "</td>";
    $nav .= "</tr></table>\n";

    return $nav;
}

$records = strtolower(cleanvar($_REQUEST['records']));

if ($incidentid=='' OR $incidentid < 1)
{
    trigger_error("Incident ID cannot be zero or blank", E_USER_ERROR);
}

$sql  = "SELECT * FROM `{$dbUpdates}` WHERE incidentid='{$incidentid}' ";
$sql .= "ORDER BY timestamp {$_SESSION['update_order']}, id {$_SESSION['update_order']} ";

if (empty($records))
{
    $numupdates = intval($_SESSION['num_update_view']);
    $sql .= "LIMIT {$offset},{$numupdates}";
}
elseif (is_numeric($records))
{
    $sql .= "LIMIT {$offset},{$records}";
}

$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error $sql".mysql_error(), E_USER_WARNING);

$keeptags = array('b','i','u','hr','&lt;', '&gt;');
foreach ($keeptags AS $keeptag)
{
    if (substr($keeptag,0,1)=='&')
    {
        $origtag[]="$keeptag";
        $temptag[]="[[".substr($keeptag, 1, strlen($keeptag)-1)."]]";
        $origtag[]=strtoupper("$keeptag");
        $temptag[]="[[".strtoupper(substr($keeptag, 1, strlen($keeptag)-1))."]]";
    }
    else
    {
        $origtag[] = "<{$keeptag}>";
        $origtag[] = "</{$keeptag}>";
        $origtag[] = "<'.strtoupper($keeptag).'>";
        $origtag[] = "</'.strtoupper($keeptag).'>";
        $temptag[] = "[[{$keeptag}]]";
        $temptag[] = "[[/{$keeptag}]]";
        $temptag[] = "[['.strtoupper($keeptag).']]";
        $temptag[] = "[[/'.strtoupper($keeptag).']]";
    }
}

echo log_nav_bar();
$count = 0;
$billable_incident_approved = is_billable_incident_approved($incidentid);

while ($update = mysql_fetch_object($result))
{

    if (empty($firstid))
    {
        $firstid = $update->id;
    }

    $updateid = $update->id;
    $updatebody = trim($update->bodytext);
    $updatebodylen = strlen($updatebody);
    $updatebody = str_replace($origtag, $temptag, $updatebody);
    $updatebody = str_replace($temptag, $origtag, $updatebody);

    // Put the header part (up to the <hr /> in a seperate DIV)
    if (strpos($updatebody, '<hr>') !== FALSE)
    {
        $updatebody = "<div class='iheader'>".str_replace('<hr>',"</div>",$updatebody);
    }
    // Style quoted text
    $quote[0] = "/^(&gt;([\s][\d\w]).*)[\n\r]$/m";
    $quote[1] = "/^(&gt;&gt;([\s][\d\w]).*)[\n\r]$/m";
    $quote[2] = "/^(&gt;&gt;&gt;+([\s][\d\w]).*)[\n\r]$/m";
    $quote[3] = "/^(&gt;&gt;&gt;(&gt;)+([\s][\d\w]).*)[\n\r]$/m";
    $quote[4] = "/(-----\s?Original Message\s?-----.*-{3,})/s";
    $quote[5] = "/(-----BEGIN PGP SIGNED MESSAGE-----)/s";
    $quote[6] = "/(-----BEGIN PGP SIGNATURE-----.*-----END PGP SIGNATURE-----)/s";
    $quote[7] = "/^(&gt;)[\r]*$/m";
    $quote[8] = "/^(&gt;&gt;)[\r]*$/m";
    $quote[9] = "/^(&gt;&gt;(&gt;){1,8})[\r]*$/m";

    $quotereplace[0] = "<span class='quote1'>\\1</span>";
    $quotereplace[1] = "<span class='quote2'>\\1</span>";
    $quotereplace[2] = "<span class='quote3'>\\1</span>";
    $quotereplace[3] = "<span class='quote4'>\\1</span>";
    $quotereplace[4] = "<span class='quoteirrel'>\\1</span>";
    $quotereplace[5] = "<span class='quoteirrel'>\\1</span>";
    $quotereplace[6] = "<span class='quoteirrel'>\\1</span>";
    $quotereplace[7] = "<span class='quote1'>\\1</span>";
    $quotereplace[8] = "<span class='quote2'>\\1</span>";
    $quotereplace[9] = "<span class='quote3'>\\1</span>";

    $updatebody = preg_replace($quote, $quotereplace, $updatebody);

    // Make URL's into Hyperlinks
    /* This breaks BBCode by replacing URls in a tags PH 19/10/2008
    $search = array("/(?<!quot;|[=\"]|:[\\n]\/{2})\b((\w+:\/{2}|www\.).+?)"."(?=\W*([<>\s]|$))/i");
    $replace = array("<a href=\"\\1\">\\1</a>");
    $updatebody = preg_replace ($search, $replace, $updatebody);
    */
    $updatebody = preg_replace("/href=\"(?!http[s]?:\/\/)/", "href=\"http://", $updatebody);
    $updatebody = bbcode($updatebody);
    if (user_emoticon_preference($sit[2]) == 'true')
    {
        $updatebody = emoticons($updatebody);
    }
    $updatebody = preg_replace("!([\n\t ]+)(http[s]?:/{2}[\w\.]{2,}[/\w\-\.\?\&\=\#\$\%|;|\[|\]~:]*)!e", "'\\1<a href=\"\\2\" title=\"\\2\">'.(strlen('\\2')>=70 ? substr('\\2',0,70).'...':'\\2').'</a>'", $updatebody);

    // Make KB article references into a hyperlink
    $updatebody = preg_replace("/\b{$CONFIG['kb_id_prefix']}([0-9]{3,4})\b/", "<a href=\"kb_view_article.php?id=$1\" title=\"View KB Article $1\">$0</a>", $updatebody);

    // Lookup some extra data
    $updateuser = user_realname($update->userid,TRUE);
    $updatetime = readable_date($update->timestamp);
    $currentowner = user_realname($update->currentowner,TRUE);
    $currentstatus = incident_status($update->currentstatus);

    $updateheadertext = $updatetypes[$update->type]['text'];
    if ($currentowner != $updateuser)
    {
        $updateheadertext = str_replace('currentowner', $currentowner, $updateheadertext);
    }
    else
    {
        $updateheadertext = str_replace('currentowner', $strSelf, $updateheadertext);
    }

    $updateheadertext = str_replace('updateuser', $updateuser, $updateheadertext);
    $updateheadertext = str_replace('updateuser', $updateuser, $updateheadertext);

    if ($update->type == 'reviewmet' AND
        ($update->sla == 'opened' OR $update->userid == 0))
    {
        $updateheadertext = str_replace('updatereview', $strPeriodStarted, $updateheadertext);
    }
    elseif ($update->type == 'reviewmet' AND $update->sla == '')
    {
        $updateheadertext = str_replace('updatereview', $strCompleted, $updateheadertext);
    }

    if ($update->type == 'slamet')
    {
        $updateheadertext = str_replace('updatesla', $slatypes[$update->sla]['text'], $updateheadertext);
    }

    echo "<a name='update{$count}'></a>";

    // Print a header row for the update
    if ($updatebody == '' AND $update->customervisibility == 'show')
    {
        echo "<div class='detailinfo'>";
    }
    elseif ($updatebody == '' AND $update->customervisibility != 'show')
    {
        echo "<div class='detailinfohidden'>";
    }
    elseif ($updatebody != '' AND $update->customervisibility == 'show')
    {
        echo "<div class='detailhead'>";
    }
    else
    {
        echo "<div class='detailheadhidden'>";
    }

    if ($offset > $_SESSION['num_update_view'])
    {
        $previous = $offset - $_SESSION['num_update_view'];
    }
    else
    {
        $previous = 0;
    }
    $next = $offset + $_SESSION['num_update_view'];

    echo "<div class='detaildate'>";
    if ($count == 0)
    {
        // Put the header part (up to the <hr /> in a seperate DIV)
        if (strpos($updatebody, '<hr>') !== FALSE)
        {
            echo "<a href='{$_SERVER['PHP_SELF']}?id={$incidentid}&amp;";
            echo "javascript=enabled&amp;offset={$previous}&amp;direction=";
            echo "previous' class='info'>";
            echo icon('navup', 16, $strPreviousUpdate)."</a>";
        }
    }
    else
    {
        echo "<a href='#update".($count-1)."' class='info'>";
        echo icon('navup', 16, $strPreviousUpdate)."</a>";
    }
    // Style quoted text
    $quote[0] = "/^(&gt;([\s][\d\w]).*)[\n\r]$/m";
    $quote[1] = "/^(&gt;&gt;([\s][\d\w]).*)[\n\r]$/m";
    $quote[2] = "/^(&gt;&gt;&gt;+([\s][\d\w]).*)[\n\r]$/m";
    $quote[3] = "/^(&gt;&gt;&gt;(&gt;)+([\s][\d\w]).*)[\n\r]$/m";
    $quote[4] = "/(-----\s?Original Message\s?-----.*-{3,})/s";
    $quote[5] = "/(-----BEGIN PGP SIGNED MESSAGE-----)/s";
    $quote[6] = "/(-----BEGIN PGP SIGNATURE-----.*-----END PGP SIGNATURE-----)/s";
    $quote[7] = "/^(&gt;)[\r]*$/m";
    $quote[8] = "/^(&gt;&gt;)[\r]*$/m";
    $quote[9] = "/^(&gt;&gt;(&gt;){1,8})[\r]*$/m";

    $quotereplace[0] = "<span class='quote1'>\\1</span>";
    $quotereplace[1] = "<span class='quote2'>\\1</span>";
    $quotereplace[2] = "<span class='quote3'>\\1</span>";
    $quotereplace[3] = "<span class='quote4'>\\1</span>";
    $quotereplace[4] = "<span class='quoteirrel'>\\1</span>";
    $quotereplace[5] = "<span class='quoteirrel'>\\1</span>";
    $quotereplace[6] = "<span class='quoteirrel'>\\1</span>";
    $quotereplace[7] = "<span class='quote1'>\\1</span>";
    $quotereplace[8] = "<span class='quote2'>\\1</span>";
    $quotereplace[9] = "<span class='quote3'>\\1</span>";

    $updatebody = preg_replace($quote, $quotereplace, $updatebody);

    // Make URL's into Hyperlinks
    /* This breaks BBCode by replacing URls in a tags PH 19/10/2008
    $search = array("/(?<!quot;|[=\"]|:[\\n]\/{2})\b((\w+:\/{2}|www\.).+?)"."(?=\W*([<>\s]|$))/i");
    $replace = array("<a href=\"\\1\">\\1</a>");
    $updatebody = preg_replace ($search, $replace, $updatebody);
    */

    // [begin] Insert link for old-style attachments [[att]]file.ext[[/att]] format
    // This code is required to support attachments written prior to v3.35
    // Please don't remove without a plan for what to do about old-style
    // attachments.  INL 14Dec08
    if (file_exists("{$CONFIG['attachment_fspath']}{$update->incidentid}/{$update->timestamp}"))
    {
        $attachment_webpath = "{$CONFIG['attachment_webpath']}{$update->incidentid}/{$update->timestamp}";
    }
    else
    {
        $attachment_webpath = "{$CONFIG['attachment_webpath']}updates/{$update->id}";
    }
    $updatebody = preg_replace("/\[\[att\]\](.*?)\[\[\/att\]\]/", "<a href = '{$attachment_webpath}/$1'>$1</a>", $updatebody);
    // [end] Insert link for old-style attachments [[att]]file.ext[[/att]] format

    $updatebody = preg_replace("/href=\"(?!http[s]?:\/\/)/", "href=\"http://", $updatebody);
    $updatebody = bbcode($updatebody);
    $updatebody = preg_replace("!([\n\t ]+)(http[s]?:/{2}[\w\.]{2,}[/\w\-\.\?\&\=\#\$\%|;|\[|\]~:]*)!e", "'\\1<a href=\"\\2\" title=\"\\2\">'.(strlen('\\2')>=70 ? substr('\\2',0,70).'...':'\\2').'</a>'", $updatebody);

    // Make KB article references into a hyperlink
    $updatebody = preg_replace("/\b{$CONFIG['kb_id_prefix']}([0-9]{3,4})\b/", "<a href=\"kb_view_article.php?id=$1\" title=\"View KB Article $1\">$0</a>", $updatebody);

    if ($currentowner != $updateuser)
    {
        echo "<a href='{$_SERVER['PHP_SELF']}?id={$incidentid}&amp;";
        echo "javascript=enabled&amp;offset={$next}&amp;direction=next' ";
        echo "class='info'>";
        echo icon('navdown', 16, $strNextUpdate)."</a>";
    }
    else
    {
        echo "<a href='#update".($count+1)."' class='info'>";
        echo icon('navdown', 16, $strNextUpdate)."</a>";
    }
    echo "</div>";

    // Specific header
    echo "<div class='detaildate'>{$updatetime}</div>";

    if ($update->customervisibility == 'show')
    {
        $newmode='hide';
    }
    else
    {
        $newmode='show';
    }

    echo "<a href='incident_showhide_update.php?mode={$newmode}&amp;";
    echo "incidentid={$incidentid}&amp;updateid={$update->id}&amp;view";
    echo "={$view}&amp;expand={$expand}' name='{$update->id}' class='info'>";
    if (array_key_exists($update->type, $updatetypes))
    {
        if (!empty($update->sla) AND $update->type=='slamet')
        {
            echo icon($slatypes[$update->sla]['icon'], 16, $update->type);
        }
        echo icon($updatetypes[$update->type]['icon'], 16, $update->type);

        if ($update->customervisibility == 'show')
        {
            echo "<span>{$strHideInPortal}</span>";
        }
        else
        {
            echo "<span>{$strMakeVisibleInPortal}</span>";
        }

        echo "</a> {$updateheadertext}";
    }
    else
    {
        echo icon($updatetypes['research']['icon'], 16, $strResearch);
        if ($update->customervisibility == 'show')
        {
            echo "<span>{$strHideInPortal}</span>";
        }
        else
        {
            echo "<span>{$strMakeVisibleInPortal}</span>";
        }

        if ($update->sla != '')
        {
            echo icon($slatypes[$update->sla]['icon'], 16, $update->type);
        }
        echo "</a>" . sprintf($strUpdatedXbyX, "(".$update->type.")", $updateuser);
    }

    echo "</div>\n";
    if (!empty($updatebody))
    {
        if ($update->customervisibility == 'show')
        {
            echo "<div class='detailentry'>\n";
        }
        else
        {
            echo "<div class='detailentryhidden'>\n";
        }

        if ($updatebodylen > 5)
        {
            echo nl2br($updatebody);
        }
        else
        {
            echo $updatebody;
        }

        if (!empty($update->nextaction) OR $update->duration != 0)
        {
            echo "<div class='detailhead'>";

            if ($update->duration != 0)
            {
                $inminutes = ceil($update->duration/60); // Always round up
                echo  "{$strDuration}: {$inminutes} {$strMinutes}";
                
                // Permision to adjust durations is 81
                if ($CONFIG['allow_duration_adjustment'] AND user_permission($sit[2], 81) AND !$billable_incident_approved)
                {
                    echo " <a href='billing/edit_activity_duration.php?mode=showform&amp;incidentid={$incidentid}&amp;updateid={$update->id}'>{$strEdit}</a>";
                }
                
                echo "<br />";
            }

            if (!empty($update->nextaction))
            {
                echo "{$strNextAction}: {$update->nextaction}";
            }

            echo "</div>";
        }

    }
    echo "</div>";
    $count++;
}

if ($_SESSION['num_update_view'] > 0)
{
    echo log_nav_bar();
}

?>
