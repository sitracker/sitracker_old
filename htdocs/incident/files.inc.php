<?php
/*
incident/files.inc.php - Lists files associated with an incident, included by ../incident.php

SiT (Support Incident Tracker) - Support call tracking system
Copyright (C) 2000-2008 Salford Software Ltd. and Contributors

This software may be used and distributed according to the terms
of the GNU General Public License, incorporated herein by reference.
*/

// FIXME Need to link back into ftp_publish.php to publish these files

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}

if (empty($incidentid)) $incidentid = mysql_real_escape_string($_REQUEST['id']);

// append incident number to attachment path to show this users attachments
$incident_attachment_fspath = $CONFIG['attachment_fspath'] . $incidentid;
$att_max_filesize = return_bytes($CONFIG['upload_max_filesize']);

// Have a look to see if we've uploaded a file and process it if we have
if ($_FILES['attachment']['name'] != "")
{
    // Check if we had an error whilst uploading
    if($_FILES['attachment']['error'] != '' AND $_FILES['attachment']['error'] != UPLOAD_ERR_OK)
    {
        echo "<div class='detailinfo'>\n";

        echo "An error occurred while uploading <strong>{$_FILES['attachment']['name']}</strong>";

        echo "<p class='error'>";
        switch ($_FILES['attachment']['error'])
        {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:  echo "The uploaded file was too large"; break;
            case UPLOAD_ERR_PARTIAL: echo "The file was only partially uploaded"; break;
            case UPLOAD_ERR_NO_FILE: echo "No file was uploaded"; break;
            case UPLOAD_ERR_NO_TMP_DIR: echo "Temporary folder is missing"; break;
            default: echo "An unknown file upload error occurred"; break;
        }
        echo "</p>";
        echo "</div>";
    }
    else
    {
        // OK to proceed
        // make incident attachment dir if it doesn't exist
        $newfilename = $incident_attachment_fspath.'/'.$_FILES['attachment']['name'];
        $umask=umask(0000);
        $mk=TRUE;
        if (!file_exists($incident_attachment_fspath))
        {
           $mk = mkdir($incident_attachment_fspath, 0770);
           if (!$mk) trigger_error('Failed creating incident attachment directory: '.$incident_attachment_fspath .$id, E_USER_WARNING);
        }
        // Move the uploaded file from the temp directory into the incidents attachment dir
        $mv = move_uploaded_file($_FILES['attachment']['tmp_name'], $newfilename);
        if (!$mv) trigger_error('!Error: Problem moving attachment from temp directory to: '.$newfilename, E_USER_WARNING);

        echo "<div class='detailinfo'>\n";
        if ($mk AND $mv) echo "File <strong>{$_FILES['attachment']['name']}</strong> ({$_FILES['attachment']['type']} {$_FILES['attachment']['size']} bytes) uploaded OK";
        else echo "An error occurred while uploading <strong>{$_FILES['attachment']['name']}</strong>";

        // Debug
        //echo " tmp filename: {$_FILES['attachment']['tmp_name']}<br />";
        //echo "error: {$_FILES['attachment']['eroor']}<br />";
        //echo "new filename: {$newfilename}<br />";
        echo "</div>";
    }
}

// Have a look to see if we've posted a list of files, process them if we have
if (isset($_REQUEST['fileselection']))
{
    echo "<div class='detailhead'>\n";
    echo "Tested these files";
    echo "</div>";
    echo "<div class='detailentry'>\n";
    foreach($fileselection AS $filesel)
    {
        echo "$filesel &hellip; ";
        echo "listed";
        echo "<br />";
    }
    echo "</div>";
}


$j = 0;
$ext = array("Bytes","KBytes","MBytes","GBytes","TBytes"); // FIXME bytes/kbytes etc.
while ($att_max_filesize >= pow(1024,$j)) ++$j;
    $attmax = round($att_max_filesize / pow(1024,$j-1) * 100) / 100 . ' ' . $ext[$j-1];
echo "<div class='detailhead'>\n";
echo "{$strFileManagement}";
echo "</div>";
echo "<div class='detailentry'>\n";
echo "<form action='{$_SERVER['PHP_SELF']}?id={$incidentid}' method='post' name='updateform' id='updateform' enctype='multipart/form-data'>\n";
echo "<input type='hidden' name='tab' value='{$selectedtab}' />";
echo "<input type='hidden' name='action' value='{$selectedaction}' />";
echo "<input type='hidden' name='MAX_FILE_SIZE' value='{$att_max_filesize}' />";
// maxfilesize='{$att_file_size}'
echo "<input class='textbox' type='file' name='attachment' size='30' /> ";
echo "<input type='submit' value=\"{$strAttachFile}\" /> (&lt;{$attmax})";
echo "</form>";
echo "</div>";

// try to figure out what delimeter is being used (for windows or unix)...
$delim = (strstr($incident_attachment_fspath,"/")) ? "/" : "\\";


/**
    * Convert a binary string into something viewable in a web browser
*/
function encode_binary($string)
{
   $chars = array();
   $ent = null;
   $chars = preg_split("//", $string, -1, PREG_SPLIT_NO_EMPTY);
   for ($i = 0; $i < count($chars); $i++ )
   {
     if ( preg_match("/^(\w| )$/",$chars[$i]))
         $ent[$i] = $chars[$i];
     elseif( ord($chars[$i]) < 32) $ent[$i]=".";
     else
         $ent[$i] = "&#" . ord($chars[$i]) . ";";
   }

   if ( sizeof($ent) < 1)
     return "";

   return implode("",$ent);
}


/**
    * @author Ivan Lucas
*/
function draw_file_row($file, $delim, $incidentid, $incident_attachment_fspath)
{
    global $CONFIG;
    $filepathparts=explode($delim, $file);
    $parts = count($filepathparts);
    $filename=$filepathparts[$parts-1];
    $filedir=$filepathparts[$parts-2];
    $preview=''; // reset the preview

    if ($filedir != $incidentid)
    {
        // files are in a subdirectory
        //$url="attachments/$id/".substr($filesarray[$c],strrpos($directory,$delim)+1,strlen($filesarray[$c])-strlen(urlencode($filename)).urlencode(filename));
        $url = "{$CONFIG['attachment_webpath']}{$incidentid}/{$filedir}/".str_replace('+','%20',urlencode($filename));
    }
    else
    {
        // files are in the root of the incident attachment directory
        // $url="attachments/".substr($filesarray[$c],strrpos($directory,$delim)+1,strlen($filesarray[$c])-strlen(urlencode($filename)).urlencode(filename));
        $url="{$CONFIG['attachment_webpath']}{$incidentid}/".str_replace('+','%20',urlencode($filename));
    }
    // calculate filesize
    $j = 0;
    $ext = array("Bytes","KiloBytes","MegaBytes","GigaBytes","TerraBytes");
    $filesize = filesize($file);
    while ($filesize >= pow(1024,$j)) ++$j;
    $file_size = round($filesize / pow(1024,$j-1) * 100) / 100 . ' ' . $ext[$j-1];
    $mime_type = mime_content_type($file);  // FIXME this requires php > 4.3 and is deprecated

    $html = "<tr>";
    $html .= "<td align='right' width='5%'>";
    $html .= "<a href=\"$url\"><img src='".getattachmenticon($filename)."' alt='Icon' title='{$filename} ({$file_size})' /></a>";
    $html .= "&nbsp;</td>";
    $html .= "<td width='30%'><a href='$url'";
    if (substr($mime_type, 0, 4)=='text' AND $filesize < 512000)
    {
        // The file is text, extract some of the contents of the file into a string for a preview
        $handle = fopen($file, "r");
        $preview = fread($handle, 512); // only read this much, we can't preview the whole thing, not enough space
        fclose($handle);
        // Make the preview safe to display
        $preview=nl2br(encode_binary(strip_tags($preview)));
        $html .= " class='info'><span>{$preview}</span>$filename</a>";
    }
    else $html .= ">$filename</a>";
    $html .= "</td>";
    $html .= "<td width='20%'>$file_size</td>";
    $html .= "<td width='20%'>$mime_type</td>";
    $html .= "<td width='20%'>".date($CONFIG['dateformat_filedatetime'],filemtime($file))."</td>";
    // $html .= "<td width='5%'><input type='checkbox' name='fileselection[]' value='{$filename}' onclick=\"togglerow(this, 'tt');\"/></td>";
    $html .= "</tr>\n";
    return $html;
}


if (file_exists($incident_attachment_fspath))
{
    $dirarray=array();
    echo "<form name='filelistform' action='{$_SERVER['PHP_SELF']}' method='post' onsubmit=\"return confirm_action('Are you sure?'\">";
    // FIXME  echo "<input type='submit' name='test' value='List' />";
    echo "<input type='hidden' name='id' value='{$incidentid}' />";
    echo "<input type='hidden' name='tab' value='{$selectedtab}' />";
    echo "<input type='hidden' name='action' value='{$selectedaction}' />";

    // List the directories first
    $temparray=list_dir($incident_attachment_fspath, 0);
    if (count($temparray) == 0) echo "<p class='info'>No files<p>";
    else
    {
        foreach($temparray as $value) {
            if (is_dir($value)) $dirarray[] = $value;
            elseif (is_file($value) AND substr($value,-1)!='.' AND substr($value,-8)!='mail.eml') $rfilearray[] = $value;
        }

        if (count($rfilearray) >= 1)
        {
            $headhtml = "<div class='detailhead'>\n";
            $headhtml .= "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/folder.png' alt='Root dir' title='Root dir' border='0' height='16' width='16' /> \\";
            $headhtml .= "</div>\n";
            echo $headhtml;
            echo "<div class='detailentry'>\n";

            echo "<p><em>Root of Incident {$incidentid}</em></p>\n";
            echo "<table>\n";
            foreach($rfilearray AS $rfile)
            {
                echo draw_file_row($rfile, $delim, $incidentid, $incident_attachment_fspath);
            }
            echo "</table>\n";
            echo "</div>";
        }

        foreach($dirarray AS $dir)
        {
            $directory=substr($dir,0,strrpos($dir,$delim));
            $dirname=substr($dir,strrpos($dir,$delim)+1,strlen($dir));
            if ( is_number($dirname) && $dirname!=$id && strlen($dirname)==10) $dirprettyname=date('l jS M Y @ g:ia',$dirname);
            else $dirprettyname=$dirname;
            $headhtml = "<div class='detailhead'>\n";
            $headhtml .= "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/folder.png' alt='{$id}' title='{$dir}' border='0' height='16' width='16' valign='top' /> {$dirprettyname}";
            $headhtml .= "</div>\n";
            $tempfarray=list_dir($dir, 1);
            if (count($tempfarray)==1 AND (substr($tempfarray[0],-8)=='mail.eml'))
            {
                // do nothing if theres only an email in the dir, don't even list the directory
            }
            else
            {
                echo $headhtml;  // print the directory header bar that we drew above
                echo "<div class='detailentry'>\n";
                if (in_array("{$dir}{$delim}mail.eml",$tempfarray))
                {
                    $updatelink=readlink($dir);
                    $updateid=substr($updatelink,strrpos($updatelink,$delim)+1,strlen($updatelink));
                    echo "<p>These files arrived by <a href='{$CONFIG['attachment_webpath']}{$incidentid}/{$dirname}/mail.eml'>email</a>, jump to the appropriate <a href='incident_details.php?id={$incidentid}#$updateid'>entry in the log</a></p>";
                }
                foreach($tempfarray as $fvalue)
                {
                    if (is_file($fvalue) AND substr($fvalue,-8)!='mail.eml') $filearray[] = $fvalue;
                }
                echo "<table>\n";
                foreach($filearray AS $file)
                {
                    echo draw_file_row($file, $delim, $incidentid, $incident_attachment_fspath);

                }
                echo "</table>\n";
                echo "</div>";
            }
            unset($filearray);
        }
    }
}
echo "</form>";
?>
