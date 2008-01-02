<?php
// incident_attachments.php -
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

// INL 2Nov05
// This file will be superceded by htdocs/incidents/files.inc.php

@include('set_include_path.inc.php');
$permission=62; // View incident attachments

require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

// External variables
$id = cleanvar($_REQUEST['id']);
$incidentid=$id;

$title = $strFiles;
include('incident_html_top.inc.php');

// append incident number to attachment path to show this users attachments
$incident_attachment_fspath = $CONFIG['attachment_fspath'] . $id;

include('incident/files.inc.php');

/*
if (file_exists($incident_attachment_fspath))
{

    // set array full of recursed files and directories
    $temparray=list_dir($incident_attachment_fspath, 1);
    foreach($temparray as $value)
    {
        if (substr($value,-8)!='mail.eml')
        $filesarray[] = $value;
    }
    if (is_array($filesarray))
    {
        $numfiles=count($filesarray);
        reset($filesarray);

        echo "<table summary='files' align='center'>";
        $shade=1;
        $prevdir='';
        for($c=0;$c<$numfiles;$c++)
        {
            // try to figure out what delimeter is being used (for windows or unix)...
            $delim = (strstr($filesarray[$c],"/")) ? "/" : "\\";

            $filename=substr($filesarray[$c],strrpos($filesarray[$c],$delim)+1,strlen($filesarray[$c]));
            $directory=substr($filesarray[$c],0,strrpos($filesarray[$c],$delim));
            $dirname=substr($directory,strrpos($directory,$delim)+1,strlen($directory));
            if ( is_number($dirname) && $dirname!=$id && strlen($dirname)==10)
            {
                $dirtext=date('D jS M Y @ g:i A',$dirname);
            }
            else
            {
                $dirtext=$dirname;
            }

            ## Added by Ivan at 3.07.1 - 10 Sep 03 to fix attachments problem (urlencode in wrong place)
            ## Need to encode just the length of the filename at the end of the  path, not the entire path or slashes
            ## get encodes.
            if (substr($filesarray[$c],0,strrpos($directory,$delim))==$incident_attachment_fspath)
            {
                // files are in a subdirectory
                //$url="attachments/$id/".substr($filesarray[$c],strrpos($directory,$delim)+1,strlen($filesarray[$c])-strlen(urlencode($filename)).urlencode(filename));
                $url="attachments/$id/$dirname/".str_replace('+','%20',urlencode($filename));
            }
            else
            {
                // files are in the root of the incident attachment directory
                // $url="attachments/".substr($filesarray[$c],strrpos($directory,$delim)+1,strlen($filesarray[$c])-strlen(urlencode($filename)).urlencode(filename));
                $url="attachments/$id/".str_replace('+','%20',urlencode($filename));
            }
            // calculate filesize
            $j = 0;
            $ext =
            array("Bytes","KBytes","MBytes","GBytes","TBytes");
            $file_size = filesize($filesarray[$c]);
            while ($file_size >= pow(1024,$j)) ++$j;
            $file_size = round($file_size / pow(1024,$j-1) * 100) / 100 . ' ' . $ext[$j-1];
            if ($prevdir!=$directory )
            {
                echo "<tr><td colspan='3' style='background-color: transparent'>&nbsp;</td></tr>";
                echo "<tr class='shade1'>";
                echo "<td colspan='2' align='center'><img src=\"{$CONFIG['application_webpath']}images/smallicons/folder.gif\" alt=\"$dirtext\" border=0 height='16' width='16' valign='top'> <b>$dirtext</b></td><td>&nbsp;</td>";
                echo "</tr>\n";
            }
            echo "<tr class='shade2'>";
            echo "<td class='shade1' align='right'>";
            echo "<a href=\"$url\"><img src=\"".getattachmenticon($filename)."\" alt=\"$filename ($file_size)\" border=0></a>";
            echo "&nbsp;</td>";
            echo "<td>&nbsp;<a href=\"$url\"><big>$filename</big></a></td>";
            echo "<td>$file_size<br /><!--<a href=\"ftp_publish.php?source_file=$incident_attachment_fspath/".substr($filesarray[$c],strrpos($directory,$delim)+1,strlen($filesarray[$c]))."&destination_file=$filename\">FTP Publish</a>--></td>";
            echo "</tr>\n";

            $prevdir=$directory;
        }
        ?>
        </table></p>
        <?php
    }
    else
    {
        // directory exists but is empty
        echo "<p class='error' align='center'>Incident $id has no files attached, directory is empty: $incident_attachment_fspath</p>";
    }
}
else
{
    // Check to see if the main attachments directory exists
    if (!file_exists($CONFIG['attachment_fspath'])) trigger_error("Attachments path '{$CONFIG['attachment_fspath']}' does not exist.",E_USER_WARNING);
    // directory doesn't exist
    echo "<p class='error'>Incident $id has no files attached, attachment directory doesn't exist: $incident_attachment_fspath</p>";
}
*/
include('incident_html_bottom.inc.php');

?>
