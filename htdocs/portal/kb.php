<?php
/*
portal/kb.php - Show knowledgebase entries

SiT (Support Incident Tracker) - Support call tracking system
Copyright (C) 2000-2008 Salford Software Ltd. and Contributors

This software may be used and distributed according to the terms
of the GNU General Public License, incorporated herein by reference.
*/

include 'portalheader.inc.php';

echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/kb.png' width='32' height='32' alt='' /> {$strKnowledgeBase}</h2>";
$perpage = 20;
$order = cleanvar($_GET['order']);
$sort = cleanvar($_GET['sort']);

if(!isset($_GET['start']))
{
    $start = 0;
}
else
{
    $start = $_GET['start'];
}

$view = $_GET['view'];
$end = $start + $perpage;
$filter = array('start' => $start, 'view' => $view);

$sql = "SELECT k.*, kbs.*, s.name FROM `{$dbKBArticles}` AS k, `{$dbKBSoftware}` as kbs, `{$dbSoftware}` as s ";
$sql .= "WHERE k.distribution='public' ";
$sql .= "AND kbs.docid=k.docid ";
$sql .= "AND kbs.softwareid=s.id ";


if($view != 'all')
{
    $softwares = contract_software();
    $sql .= "AND (1=0 ";
    foreach($softwares AS $software)
    {
        $sql .= "OR kbs.softwareid={$software} ";
    }
    $sql .= ")";
    
    echo "<p class='info'>{$strShowingOnlyRelevantArticles} - ";
    echo "<a href='{$_SERVER['PHP_SELF']}?view=all'>{$strShowAll}</a></p>";
}
else
{
    echo "<p class='info'>{$strShowingAllArticles} - ";
    echo "<a href='{$_SERVER['PHP_SELF']}'>{$strShowOnlyRelevant}</a></p>";
}

//get the full SQL so we can see the total rows
$countsql = $sql;

if (!empty($sort))
{
    if ($sort=='title') $sql .= "ORDER BY k.title ";
    elseif ($sort=='date') $sql .= " ORDER BY k.published ";
    elseif ($sort=='author') $sql .= " ORDER BY k.author ";
    elseif ($sort=='keywords') $sql .= " ORDER BY k.keywords ";
    else $sql .= " ORDER BY k.docid ";

    if ($order=='a' OR $order=='ASC' OR $order='') $sql .= "ASC";
    else $sql .= "DESC";
}
else
{
    $sql .= " ORDER BY k.docid DESC ";
}
$sql .= " LIMIT {$start}, {$end} ";
if($result = mysql_query($sql))
{
    $countresult = mysql_query($countsql);
    $numtotal = mysql_num_rows($countresult);
    if($end > $numtotal)
    {
        $end = $numtotal;
    }
    echo "Showing {$start} to {$end} of {$numtotal}";
    echo "<table align='center' width='80%'>";
    echo colheader('id', $strID, $sort, $order, $filter, '', '5');
    echo colheader('title', $strTitle, $sort, $order, $filter);
    echo colheader('date', $strDate, $sort, $order, $filter, '', '15');
    echo colheader('author', $strAuthor, $sort, $order, $filter);
    echo colheader('keywords', $strKeywords, $sort, $order, $filter, '', '15');

    $shade = 'shade1';
    while($row = mysql_fetch_object($result))
    {
        echo "<tr class='{$shade}'>
                <td><a href='kbarticle.php?id={$row->docid}'>
                <img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/kb.png' alt='{$strID}' />
                    {$CONFIG['kb_id_prefix']}{$row->docid}</a></td>
                <td>{$row->name}<br />
                <a href='kbarticle.php?id={$row->docid}'>{$row->title}</a></td>
                <td>".ldate($CONFIG['dateformat_date'], mysql2date($row->published))."</td>
                <td>".user_realname($row->author)."</td>
                <td>{$row->keywords}</td</tr>";
                
        if($shade == 'shade1')
            $shade = 'shade2';
        else
            $shade = 'shade1';
    }
    echo "<p align='center'>";



    if(!empty($_GET['start']))
    {
        echo " <a href='{$_SERVER['PHP_SELF']}?start=";
        echo $start-$perpage."'>{$strPrevious}</a> ";
    }
    else
    {
        echo $strPrevious;
    }
    echo " | ";
    if($end != $numtotal)
    {
        echo " <a href='{$_SERVER['PHP_SELF']}?start=";
        echo $start+$perpage."&amp;sort={$sort}&amp;order={$order}&amp;view={$view}'>{$strNext}</a> ";    }
    else
    {
        echo $strNext;
    }
    echo "<p align='center'>";
}
else
{
    echo "<p align='center'>{$strNoRecords}</p>";
}

echo "</table>";
include ('htmlfooter.inc.php');

?>