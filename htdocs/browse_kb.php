<?php
// browse_kb.php - Browse knowledge base articles
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Authors: Ivan Lucas, Tom Gerrard

// This Page Is Valid XHTML 1.0 Transitional!  1Nov05

$permission=54; // View KB
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

// External variables
$search_string = cleanvar($_REQUEST['search_string']);
$mode = cleanvar($_REQUEST['mode']);

$title="Browse Knowledge Base";
include('htmlheader.inc.php');
if (empty($mode) && empty($search_string)) $mode='RECENT';
if (empty($search_string) AND empty($mode)) $search_string='a';
?>
<h2>Browse Knowledge Base</h2>
<?php
if (strtolower($mode)=='recent') echo "<h4>Articles published recently</h4>";
elseif (strtolower($mode)=='today') echo "<h4>Articles published today</h4>";
?>
<table summary="alphamenu" align="center">
    <tr>
        <td align="center">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
        <input type="text" name="search_string" /><input type="submit" value="go" />
        </form>
        </td>
        </tr>
        <tr>
        <td valign="middle">
        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?mode=RECENT" title="Recent Articles">Recent</a> |
        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=A">A</a> |
        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=B">B</a> |
        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=C">C</a> |
        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=D">D</a> |
        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=E">E</a> |
        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=F">F</a> |
        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=G">G</a> |
        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=H">H</a> |
        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=I">I</a> |
        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=J">J</a> |
        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=K">K</a> |
        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=L">L</a> |
        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=M">M</a> |
        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=N">N</a> |
        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=O">O</a> |
        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=P">P</a> |
        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=Q">Q</a> |
        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=R">R</a> |
        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=S">S</a> |
        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=T">T</a> |
        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=U">U</a> |
        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=V">V</a> |
        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=W">W</a> |
        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=X">X</a> |
        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=Y">Y</a> |
        <a href="<?php echo $_SERVER['PHP_SELF'] ?>?search_string=Z">Z</a>
        </td>
    </tr>
    </table>
    <br />
<?php
// ---------------------------------------------
// SQL Queries:

if (strlen($search_string) > 4)
{
    // Find Software
    $sql = "SELECT * FROM software WHERE name LIKE '%{$search_string}%' LIMIT 20";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    echo "<p align='center'><strong>Matching Skills</strong>: ";
    $softcount=mysql_num_rows($result);
    $count=1;
    $colcount=1;
    while ($software = mysql_fetch_object($result))
    {
        echo "{$software->name}";
        if ($count<$softcount) echo ", ";
        if ($colcount >= 4) {$colcount=0; echo "<br />"; }
        $count++; $colcount++;
    }
    echo "</p>\n";
}
// Find Articles
$sql = "SELECT * FROM kbarticles ";
if (strtolower($mode)=='myarticles') $sql .= "WHERE author='{$sit[2]}' ";
if (!empty($search_string))
{
    $sql .= "WHERE ";
    $search_string_len=strlen($search_string);
    if (is_numeric($search_string))
    {
        $sql .= "docid=('{$search_string}') ";
    }
    elseif (strtoupper(substr($search_string,0,strlen($CONFIG['kb_id_prefix'])))==strtoupper($CONFIG['kb_id_prefix']))
    {
        $sql .= "docid='".substr($search_string,strlen($CONFIG['kb_id_prefix']))."' ";
    }
    else if ($search_string_len<=2)
    {
        $sql .= "SUBSTRING(title,1,$search_string_len)=('{$search_string}') ";
    }
    else
    {
        $sql .= "title LIKE '%{$search_string}%' OR keywords LIKE '%{$search_string}%' ";
    }
}
if (strtolower($mode)=='recent') $sql .= "ORDER BY docid DESC LIMIT 20";

if (strtolower($mode)=='today') $sql .= " WHERE published > '".date('Y-m-d')."' ORDER BY published DESC";

$result = mysql_query($sql);
if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

if (mysql_num_rows($result) >= 1)
{
    echo "<p align='center'><strong>Matching Articles</strong> :</p>";
    echo "<table align='center' width='98%'>";
    $shade = 0;
    while ($kbarticle = mysql_fetch_object($result))
    {
        // FIXME: These styles and colours need moving to the webtrack.css file really so they can be customised
        if (empty($kbarticle->title)) $kbarticle->title='Untitled';
        else $kbarticle->title=stripslashes($kbarticle->title);
        echo "<tr class='mainshade'>";
        echo "<td style='font-size: 120%'>&bull; {$CONFIG['kb_id_prefix']}".leading_zero(4,$kbarticle->docid);
        echo " <a href='kb_view_article.php?id={$kbarticle->docid}'>{$kbarticle->title}</a>";
        // Lookup what software this applies to
        $ssql = "SELECT * FROM kbsoftware, software WHERE kbsoftware.softwareid=software.id ";
        $ssql .= "AND kbsoftware.docid='{$kbarticle->docid}' ORDER BY software.name";
        $sresult = mysql_query($ssql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        $rowcount = mysql_num_rows($sresult);
        if ($rowcount >= 1 AND $rowcount < 3)
        {
            while ($kbsoftware = mysql_fetch_object($sresult))
            {
                echo " : {$kbsoftware->name}";
            }
        }
        elseif ($rowcount >= 4)
        {
            echo " : Various Software";
        }
        echo "</td>";
        echo "</tr>\n";

        $asql = "SELECT LEFT(content,400) FROM kbcontent WHERE docid='{$kbarticle->docid}' ORDER BY id ASC LIMIT 1";
        $aresult = mysql_query($asql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        list($content)=mysql_fetch_row($aresult);
        $content=strip_tags(remove_slashes($content));
        if (!empty($_REQUEST['search_string'])) $content=str_replace($_REQUEST['search_string'],"<strong>{$_REQUEST['search_string']}</strong>",$content);
        echo "<tr class='shade1'>";
        echo "<td colspan='0'>$content : <strong style='color: #708090;'>{$kbarticle->keywords}</strong> : <strong>".date('j F Y', mysql2date($kbarticle->published))."</strong>";
        echo "<br /><br />\n</td>";
        echo "</tr>\n";
    }
    echo "</table>\n";
}
else
{
    echo "<p align='center'>No matching articles</p>";
}

echo "<!---SQL === $sql --->";
echo "<p align='center'><a href='kb_add_article.php'>Add a knowledge base article</a></p>";

include('htmlfooter.inc.php');

?>
