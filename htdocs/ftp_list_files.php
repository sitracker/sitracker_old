<?php
// ftp_list_files.php -
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

// This Page Is Valid XHTML 1.0 Transitional!   1Nov05

@include('set_include_path.inc.php');
$permission=44; // FTP Publishing
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

$title= $strFTPFilesDB;
include('htmlheader.inc.php');

// External Variables
$orderby=cleanvar($_REQUEST['orderby']);

?>
<script type="text/javascript">
function upload_window()
{
    URL = "ftp_upload_file.php";
        window.open(URL, "upload_window", "toolbar=yes,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=700,height=600");
}
</script>
<?php
echo "<h2>$title</h2>";
if (!empty($CONFIG['ftp_hostname']) AND !empty($CONFIG['ftp_username'])) echo "<p align='center'><a href='ftp_upload_file.php'>Upload a new file</a></p>";
echo "<table summary='files' align='center'>
<tr>
    <th>&nbsp;</th>
    <th><a href='{$_SERVER['PHP_SELF']}?orderby=filename'>{$strFilename}</a></th>
    <th><a href='{$_SERVER['PHP_SELF']}?orderby=size'>{$strSize}</a></th>
    <th><a href='{$_SERVER['PHP_SELF']}?orderby=shortdescription'>{$strTitle}</a></th>
    <th><a href='{$_SERVER['PHP_SELF']}?orderby=version'>{$strVersion}</a></th>
    <th><a href='{$_SERVER['PHP_SELF']}?orderby=date'>{$strDate}</a></th>
    <th><a href='{$_SERVER['PHP_SELF']}?orderby=expiry'>{$strExpiryDate}</a></th>
</tr>";

$sql="SELECT id, filename, size, userid, shortdescription, path, downloads, filedate, fileversion, productid, ";
$sql .="releaseid, expiry, published FROM files ";

switch($orderby)
{
    case 'filename':
        $sql.="ORDER by filename ";
    break;

    case 'shortdescription':
        $sql.="ORDER by shortdescription ";
    break;

    case 'size':
        $sql.="ORDER by size ";
    break;

    case 'version':
        $sql.="ORDER BY fileversion ";
    break;

    case 'expiry':
        $sql.="ORDER by expiry ";
    break;

    case 'date':
        $sql.="ORDER BY filedate ";
    break;

    default:
        $sql.="ORDER by filename ";
    break;
}

$result=mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

while (list($id, $filename, $size, $userid, $shortdescription, $path, $downloads, $filedate, $fileversion,
            $productid, $releaseid, $expiry, $published)=mysql_fetch_row($result))
{
    // calculate filesize
    $j = 0;
    $ext = array($strBytes, $strKBytes, $strMBytes, $strGBytes, $strTBytes);
    $pretty_file_size = $size;
    while ($pretty_file_size >= pow(1024,$j)) ++$j;
    $pretty_file_size = round($pretty_file_size / pow(1024,$j-1) * 100) / 100 . ' ' . $ext[$j-1];

    if ($published=='no') echo "<tr class='urgent'>";
    else echo "<tr>";
    echo "<td align='right'><img src=\"".getattachmenticon($filename)."\" alt=\"$filename ($pretty_file_size)\" border='0' /></td>";
    echo "<td><strong><a href=\"ftp_file_details.php?id=$id\">$filename</a></strong></td>";
    echo "<td>$pretty_file_size</td>";
    echo "<td>$shortdescription</td>";
    echo "<td>$fileversion</td>";
    echo "<td>".ldate($CONFIG['dateformat_filedatetime'],$filedate)."</td>";
    echo "<td>";
    if ($expiry==0)
    {
        echo 'Never';
    }
    else
    {
        echo ldate($CONFIG['dateformat_filedatetime'],$expiry);
    }
    echo "</td>";
    echo "</tr>\n";
}
echo "</table>\n";
include('htmlfooter.inc.php');
?>
