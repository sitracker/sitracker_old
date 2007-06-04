<?php
// log.inc.php - Displays the incident update log
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

// Included by ../incident.php

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}

if ($incidentid=='' OR $incidentid < 1) trigger_error("Incident ID cannot be zero or blank", E_USER_ERROR);

$sql  = "SELECT * FROM updates WHERE incidentid='{$incidentid}' ";
// Don't show hidden updates if we're on the customer view tab
if (strtolower($selectedtab)=='customer_view') $sql .= "AND customervisibility='show' ";
$sql .= "ORDER BY timestamp DESC";
$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

$keeptags=array('b','i','u','hr','&lt;', '&gt;');
foreach($keeptags AS $keeptag)
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
        $origtag[]="<{$keeptag}>";
        $origtag[]="</{$keeptag}>";
        $origtag[]="<'.strtoupper($keeptag).'>";
        $origtag[]="</'.strtoupper($keeptag).'>";
        $temptag[]="[[{$keeptag}]]";
        $temptag[]="[[/{$keeptag}]]";
        $temptag[]="[['.strtoupper($keeptag).']]";
        $temptag[]="[[/'.strtoupper($keeptag).']]";
    }
}

while ($update = mysql_fetch_object($result))
{
    $updatebody=trim($update->bodytext);
    $updatebodylen=strlen($updatebody);

    $updatebody = str_replace($origtag, $temptag, $updatebody);
    // $updatebody = htmlspecialchars($updatebody);
    $updatebody = str_replace($temptag, $origtag, $updatebody);

    // Insert path to attachments
    //$updatebody = preg_replace("/\[\[att\]\](.*?)\[\[\/att\]\]/",
    //                           "<a href = '/attachments/updates/{$update->id}/$1'>$1</a> ",
    //                           $updatebody);
    $updatebody = preg_replace("/\[\[att\]\](.*?)\[\[\/att\]\]/",
                               "<a href = '/attachments/{$update->incidentid}/{$update->timestamp}/$1'>$1</a>",
                               $updatebody);

    // Put the header part (up to the <hr /> in a seperate DIV)
    if (strpos($updatebody, '<hr>')!==FALSE)
    {
        $updatebody = "<div class='iheader'>".str_replace('<hr>',"</div>",$updatebody);
    }

    // Style quoted text
    // $quote[0]="/^(&gt;\s.*)\W$/m";
    // $quote[0]="/^(&gt;[\s]*.*)[\W]$/m";
    $quote[0]="/^(&gt;([\s][\d\w]).*)[\n\r]$/m";
    $quote[1]="/^(&gt;&gt;([\s][\d\w]).*)[\n\r]$/m";
    $quote[2]="/^(&gt;&gt;&gt;+([\s][\d\w]).*)[\n\r]$/m";
    $quote[3]="/^(&gt;&gt;&gt;(&gt;)+([\s][\d\w]).*)[\n\r]$/m";

    //$quote[3]="/(--\s?\s.+-{8,})/U";  // Sigs
    $quote[4]="/(-----\s?Original Message\s?-----.*-{3,})/s";
    $quote[5]="/(-----BEGIN PGP SIGNED MESSAGE-----)/s";
    $quote[6]="/(-----BEGIN PGP SIGNATURE-----.*-----END PGP SIGNATURE-----)/s";
    $quote[7]="/^(&gt;)[\r]*$/m";
    $quote[8]="/^(&gt;&gt;)[\r]*$/m";
    $quote[9]="/^(&gt;&gt;(&gt;){1,8})[\r]*$/m";

    $quotereplace[0]="<span class='quote1'>\\1</span>";
    $quotereplace[1]="<span class='quote2'>\\1</span>";
    $quotereplace[2]="<span class='quote3'>\\1</span>";
    $quotereplace[3]="<span class='quote4'>\\1</span>";
    //$quotereplace[3]="<span class='sig'>\\1</span>";
    $quotereplace[4]="<span class='quoteirrel'>\\1</span>";
    $quotereplace[5]="<span class='quoteirrel'>\\1</span>";
    $quotereplace[6]="<span class='quoteirrel'>\\1</span>";
    $quotereplace[7]="<span class='quote1'>\\1</span>";
    $quotereplace[8]="<span class='quote2'>\\1</span>";
    $quotereplace[9]="<span class='quote3'>\\1</span>";

    $updatebody=preg_replace($quote, $quotereplace, $updatebody);

    // Make URL's into Hyperlinks
    //$search = array("/(?<!quot;|[=\"]|:[\\n]\/{2})\b((\w+:\/{2}|www\.).+?)"."(?=\W*([<>\s]|$))/i");  // , "/(([\w\.]+))(@)([\w\.]+)\b/i"
    //$replace = array("<a href=\"\\1\">\\1</a>"); // , "<a href=\"mailto:$0\">$0</a>"
    //$updatebody = preg_replace("/href=\"www/i", "href=\"http://www", preg_replace ($search, $replace, $updatebody));

    // $updatebody = bbcode($updatebody);

    //$updatebody = emotion($updatebody);

    //"!(http:/{2}[\w\.]{2,}[/\w\-\.\?\&\=\#]*)!e"
    // [\n\t ]+
    //
    $updatebody = preg_replace("!([\n\t ]+)(http[s]?:/{2}[\w\.]{2,}[/\w\-\.\?\&\=\#\$\%|;|\[|\]~:]*)!e", "'\\1<a href=\"\\2\" title=\"\\2\">'.(strlen('\\2')>=70 ? substr('\\2',0,70).'...':'\\2').'</a>'", $updatebody);

    // Make KB article references into a hyperlink
    $updatebody = preg_replace("/\b{$CONFIG['kb_id_prefix']}([0-9]{3,4})\b/", "<a href=\"kb_view_article.php?id=$1\" title=\"View KB Article $1\">$0</a>", $updatebody);

    // Lookup some extra data
    $updateuser=user_realname($update->userid,TRUE);
    $updatetime = readable_date($update->timestamp);
    $currentowner=user_realname($update->currentowner,TRUE);
    $currentstatus=incident_status($update->currentstatus);

    $updateheadertext = $updatetypes[$update->type]['text'];
    $updateheadertext = str_replace('currentowner', $currentowner, $updateheadertext);
    $updateheadertext = str_replace('updateuser', $updateuser, $updateheadertext);

    // Print a header row for the update
    if ($updatebody=='' AND $update->customervisibility=='show') echo "<div class='detailinfo'>";
    elseif ($updatebody=='' AND $update->customervisibility!='show') echo "<div class='detailinfohidden'>";
    elseif ($updatebody!='' AND $update->customervisibility=='show') echo "<div class='detailhead'>";
    else echo "<div class='detailheadhidden'>";

    // Specific header
    // $updatetypes['email'] = array('icon' => 'email.png', 'text' => 'Email sent');
    echo " <div class='detaildate'>{$updatetime}</div>";

    if ($update->customervisibility=='show') $newmode='hide';
    else $newmode='show';
    echo "<a href='incident_showhide_update.php?mode={$newmode}&amp;incidentid={$incidentid}&amp;updateid={$update->id}&amp;view={$view}&amp;expand={$expand}' name='{$update->id}' class='info'>";

    if (array_key_exists($update->type, $updatetypes))
    {
        echo "<img src='{$CONFIG['application_webpath']}images/icons/kdeclassic/16x16/{$updatetypes[$update->type]['icon']}' width='16' height='16' alt='{$update->type}' />";
        echo "<span>Click here to {$newmode} this update</span></a> ";
        echo "{$updateheadertext}"; //  by {$updateuser}
    }
    else
    {
        echo "<img src='{$CONFIG['application_webpath']}images/icons/kdeclassic/16x16/{$updatetypes['research']['icon']}' width='16' height='16' alt='Research' />";
        echo "<span>Click to {$newmode}</span></a> ";
        echo "Updated ({$update->type}) by {$updateuser}";
    }

    echo "</div>\n";
    if ($updatebody!='')
    {
        if ($update->customervisibility=='show') echo "<div class='detailentry'>\n";
        else echo "<div class='detailentryhidden'>\n";
        if ($updatebodylen > 5) echo stripslashes(nl2br($updatebody));
        else echo stripslashes($updatebody);
        if (!empty($update->nextaction)) echo "<div class='detailhead'>Next action: ".stripslashes($update->nextaction)."</div>";
        echo "</div>\n"; // detailentry
    }
}

?>
