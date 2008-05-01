<?php
/*
portal/showincident.inc.php - Displays an incident in the portal included by ../portal.php

SiT (Support Incident Tracker) - Support call tracking system
Copyright (C) 2000-2008 Salford Software Ltd. and Contributors

This software may be used and distributed according to the terms
of the GNU General Public License, incorporated herein by reference.
*/

include 'portalheader.inc.php';

$incidentid = $_REQUEST['id'];
$sql = "SELECT title, contact, status, opened FROM `{$dbIncidents}` WHERE id={$incidentid}";
$result = mysql_query($sql);
$user = mysql_fetch_object($result);

echo "<h2>[{$incidentid}] {$user->title}</h2>";
echo "<p align='center'><strong>{$strContact}</strong>: ".contact_realname($user->contact);
echo "<br /><strong>{$strOpened}</strong>: ".date("jS M Y", $user->opened)."</p>";


/*
//check if this user owns the incident
if ($user->contact != $_SESSION['contactid'])
{
    echo "<p align='center'>$strNoPermission.</p>";
    include ('htmlfooter.inc.php');
    exit;
}*/

$records = strtolower(cleanvar($_REQUEST['records']));

if ($incidentid=='' OR $incidentid < 1)
{
    trigger_error("Incident ID cannot be zero or blank", E_USER_ERROR);
}

$sql  = "SELECT * FROM `{$dbUpdates}` ";
$sql .= "WHERE incidentid='{$incidentid}' AND customervisibility='show' ";
$sql .= "ORDER BY timestamp DESC, id DESC";

if ($offset > 0)
{
    if (empty($records))
    {
        $sql .= "LIMIT {$offset},{$_SESSION['num_update_view']}";
    }
    elseif (is_numeric($records))
    {
        $sql .= "LIMIT {$offset},{$records}";
    }
}
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error $sql".mysql_error(), E_USER_ERROR);

$keeptags = array('b','i','u','hr','&lt;', '&gt;');
foreach ($keeptags AS $keeptag)
{
    if (substr($keeptag,0,1)=='&')
    {
        $origtag[] = $keeptag;
        $temptag[] = "[[".substr($keeptag, 1, strlen($keeptag)-1)."]]";
        $origtag[] = strtoupper("$keeptag");
        $temptag[] = "[[".strtoupper(substr($keeptag, 1, strlen($keeptag)-1))."]]";
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
echo "<div class='portaleft'>";
echo "<h3>{$strActions}</h3>";
if ($user->status != 2)
{
    echo "<p><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/note.png' alt='{$strUpdate}' /> <a href='update.php?id={$incidentid}'>{$strUpdate}</a></p>";

    //check if the customer has requested a closure
    $lastupdate = list($update_userid, $update_type, $update_currentowner, $update_currentstatus, $update_body, $update_timestamp, $update_nextaction, $update_id)=incident_lastupdate($incidentid);

    if ($lastupdate[1] == "customerclosurerequest")
    {
        echo "{$strClosureRequested}</td>";
    }
    else
    {
        echo "<p><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/close.png' alt='{$strRequestClosure}' /> <a href='close.php?id={$incidentid}'>{$strRequestClosure}</a></p>";
    }
}

echo "<h3>{$strFiles}</h3>";
$filesql = "SELECT * FROM files WHERE category='incident' and refid='{$incidentid}'";
$fileresult = mysql_query($filesql);
if (mysql_error()) trigger_error("MySQL Query Error $sql".mysql_error(), E_USER_ERROR);

while ($filerow = mysql_fetch_object($fileresult))
{
    echo "<a>{$filerow->filename}</a><br />";
    echo "uploaded ";
    if ($filerow->userid != 0)
    {        
        if ($filerow->usertype == 'contact')
        {
            echo "by ".contact_realname($filerow->userid)." ";
        }
        else
        {
            echo "by ".user_realname($filerow->userid)." ";
        }
    }
    echo "@ ".ldate($CONFIG['dateformat_datetime'], $filerow->filedate)."<br /><br />";
}
echo "</div>";

echo "<div style='width:50%;margin:0 auto;'>";
while ($update = mysql_fetch_object($result))
{
    if (empty($firstid))
    {
        $firstid = $update->id;
    }

    $updateid = $update->id;
    $updatebody=trim($update->bodytext);
    $updatebody = preg_replace("/\[\[att\]\](.*?)\[\[\/att\]\]/", "<a href = '{$attachment_webpath}/$1'>$1</a>", $updatebody);

    //remove empty updates
    if (!empty($updatebody) AND $updatebody != "<hr>")
    {
        $updatebodylen = strlen($updatebody);

        $updatebody = str_replace($origtag, $temptag, $updatebody);
        // $updatebody = htmlspecialchars($updatebody);
        $updatebody = str_replace($temptag, $origtag, $updatebody);

        // Put the header part (up to the <hr /> in a seperate DIV)
 /*       if (strpos($updatebody, '<hr>') !== FALSE)
        {
            $updatebody = "<div class='iheader'>".str_replace("<hr>", "", $updatebody)."</div>";
        }*/
        $updatebody = str_replace("<hr>", "", $updatebody);

        // Style quoted text
        // $quote[0]="/^(&gt;\s.*)\W$/m";
        // $quote[0]="/^(&gt;[\s]*.*)[\W]$/m";
        $quote[0] = "/^(&gt;([\s][\d\w]).*)[\n\r]$/m";
        $quote[1] = "/^(&gt;&gt;([\s][\d\w]).*)[\n\r]$/m";
        $quote[2] = "/^(&gt;&gt;&gt;+([\s][\d\w]).*)[\n\r]$/m";
        $quote[3] = "/^(&gt;&gt;&gt;(&gt;)+([\s][\d\w]).*)[\n\r]$/m";

        //$quote[3]="/(--\s?\s.+-{8,})/U";  // Sigs
                $quote[4]="/(-----\s?Original Message\s?-----.*-{3,})/s";
        $quote[5] = "/(-----BEGIN PGP SIGNED MESSAGE-----)/s";
        $quote[6] = "/(-----BEGIN PGP SIGNATURE-----.*-----END PGP SIGNATURE-----)/s";
        $quote[7] = "/^(&gt;)[\r]*$/m";
        $quote[8] = "/^(&gt;&gt;)[\r]*$/m";
        $quote[9] = "/^(&gt;&gt;(&gt;){1,8})[\r]*$/m";

        $quotereplace[0] = "<span class='quote1'>\\1</span>";
        $quotereplace[1] = "<span class='quote2'>\\1</span>";
        $quotereplace[2] = "<span class='quote3'>\\1</span>";
        $quotereplace[3] = "<span class='quote4'>\\1</span>";
        //$quotereplace[3]="<span class='sig'>\\1</span>";
        $quotereplace[4] = "<span class='quoteirrel'>\\1</span>";
        $quotereplace[5] = "<span class='quoteirrel'>\\1</span>";
        $quotereplace[6] = "<span class='quoteirrel'>\\1</span>";
        $quotereplace[7] = "<span class='quote1'>\\1</span>";
        $quotereplace[8] = "<span class='quote2'>\\1</span>";
        $quotereplace[9] = "<span class='quote3'>\\1</span>";

        $updatebody = preg_replace($quote, $quotereplace, $updatebody);

        $updatebody = bbcode($updatebody);

        //$updatebody = emotion($updatebody);

        //"!(http:/{2}[\w\.]{2,}[/\w\-\.\?\&\=\#]*)!e"
        // [\n\t ]+
        $updatebody = preg_replace("!([\n\t ]+)(http[s]?:/{2}[\w\.]{2,}[/\w\-\.\?\&\=\#\$\%|;|\[|\]~:]*)!e", "'\\1<a href=\"\\2\" title=\"\\2\">'.(strlen('\\2')>=70 ? substr('\\2',0,70).'...':'\\2').'</a>'", $updatebody);


        // Lookup some extra data
        $updateuser = user_realname($update->userid,TRUE);
        $updatetime = readable_date($update->timestamp);
        $currentowner = user_realname($update->currentowner,TRUE);
        $currentstatus = incident_status($update->currentstatus);

        echo "<div class='detailhead' align='center'>";
        //show update type icon
        if (array_key_exists($update->type, $updatetypes))
        {
            if (!empty($update->sla) AND $update->type=='slamet')
            {
                echo "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/{$slatypes[$update->sla]['icon']}' width='16' height='16' alt='{$update->type}' />";
            }
            echo "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/{$updatetypes[$update->type]['icon']}' width='16' height='16' alt='{$update->type}' />";
        }
        else
        {
            echo "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/{$updatetypes['research']['icon']}' width='16' height='16' alt='Research' />";
            if ($update->sla != '')
            {
                echo "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/{$slatypes[$update->sla]['icon']}' width='16' height='16' alt='{$update->type}' />";
            }
        }
        echo " {$updatetime}</div>";
        if ($updatebody!='')
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
            echo "</div>\n"; // detailentry
        }
    }
}
echo "</div>";
include 'htmlfooter.inc.php';
?>