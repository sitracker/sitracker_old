<?php
// search2.php - New search
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Kieran Hogg <kieran_hogg[at]users.sourceforge.net>

@include ('set_include_path.inc.php');
require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

$resultsperpage = 20;
if(!isset($_GET['start']))
{
    $start = 0;
}
else
{
    $start = $_GET['start'];
}
$domain = cleanvar($_GET['domain']);
$q = cleanvar($_GET['q']);
$filter = array('start' => $start, 'domain' => $domain, 'q' => $q);

function search_highlight($x,$var)
{
    //$x is the string, $var is the text to be highlighted
    $x = strip_tags($x);
    $x = str_replace("\n", '', $x);
    // Trim the string to a reasonable length
    $pos1=stripos($x,$var);
    if ($pos1===FALSE) $pos1=0;
    if ($pos1>30) $pos1-=25;
    $pos2=strlen($var)+70;
    $x = substr($x,$pos1,$pos2);

    if ($var != "")
    {
        $xtemp = "";
        $i=0;

        while ($i < strlen($x))
        {
            if ((($i + strlen($var)) <= strlen($x)) && (strcasecmp($var, substr($x, $i, strlen($var))) == 0))
            {
                $xtemp .= "<span class='search_highlight'>" . substr($x, $i , strlen($var)) . "</span>";
                $i += strlen($var);
            }
            else
            {
                $xtemp .= $x{$i};
                $i++;
            }
        }
        $x = $xtemp;
    }
    return $x;
}


include ('htmlheader.inc.php');

echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/search.png' width='32' height='32' alt='' /> {$strSearch} {$CONFIG['application_shortname']}</h2>";

if(isset($_GET['q']))
{
    $search = cleanvar($_GET['q']);
    $domain = strtolower($_GET['domain']);

    //$sql = "SELECT *,incidentid AS id FROM `{$dbUpdates}` as u ";
    //$sql .= "WHERE MATCH (bodytext) against ('{$search}' IN BOOLEAN MODE) AS score FROM u ";
    //$sql .= "GROUP BY incidentid LIMIT {$start}, {$end} ";
    //$sql .= "ORDER BY score desc";

    $sql = "SELECT *,incidentid AS id, i.title,MATCH (bodytext) AGAINST ('{$search}') AS score ";
    $sql .= "FROM `{$dbUpdates}` as u, `{$dbIncidents}` as i ";
    $sql .= "WHERE MATCH (bodytext) AGAINST ('{$search}') ";
    $sql .= "AND u.incidentid=i.id ";
    $sql .= "GROUP BY u.incidentid ";
    if (!empty($sort))
    {
        if ($sort=='id') $sql .= "ORDER BY k.title ";
        elseif ($sort=='incident') $sql .= " ORDER BY k.published ";
        elseif ($sort=='date') $sql .= " ORDER BY k.keywords ";
        else $sql .= " ORDER BY u.score ";

        if ($order=='a' OR $order=='ASC' OR $order='') $sql .= "ASC";
        else $sql .= "DESC";
    }
    else
    {
        $sql .= " ORDER BY score DESC ";
    }

    $countsql = $sql;
    $sql .= "LIMIT {$start}, {$resultsperpage} ";
    if($result = mysql_query($sql))
    {

        $results = mysql_num_rows($result);
        $countresult = mysql_query($countsql);
        $results = mysql_num_rows($countresult);
        $end = $start + $resultsperpage;
        if($end > $results)
        {
            $end = $results;
        }
        echo "<p>".sprintf($strShowingXtoXofX, $start+1, $end, $results)."</p>";
        echo "<p align='center'>";
        if(!empty($_GET['start']))
        {
            echo " <a href='{$_SERVER['PHP_SELF']}?domain={$domain}&q={$q}&start=";
            echo $start-$resultsperpage."'>{$strPrevious}</a> ";
        }
        else
        {
            echo $strPrevious;
        }
        echo " | ";
        if($end != $numtotal)
        {
            echo " <a href='{$_SERVER['PHP_SELF']}?domain={$domain}&q={$q}&start=";
            echo $start+$resultsperpage."&amp;sort={$sort}&amp;order={$order}&amp;view={$view}'>{$strNext}</a> ";    }
        else
        {
            echo $strNext;
        }
        echo "</p>";
        echo "<table align='center' width='80%'>";
        echo "<tr>".colheader(id, $strID, $sort, $order, $filter);
        echo colheader(incident, $strIncident, $sort, $order, $filter);
        echo colheader(result, $strResult, $sort, $order, $filter);
        echo colheader(score, $strScore, $sort, $order, $filter);
        echo colheader(date, $strDate, $sort, $order, $filter);

        $shade = 'shade1';
        while($row = mysql_fetch_object($result))
        {
            switch ($domain)
            {
                case 'incidents':
                    $url = "javascript:incident_details_window('{$row->id}', 'incident{$row->id}')";
                    break;
                case 'contact':
                    $url = "contact_details.php?id={$row->id}";
                    break;
                case 'site':
                    $url = "site_details.php?id={$row->id}";
                    break;
                case 'contract':
                    $url = "contract_details.php?id={$row->id}";
                    break;
                case 'kb':
                    $url = "kb_view_article.php?id={$row->id}";
                    break;
                default:
                    $url = "javascript:alert('nothing to link to');";
            }
            echo "<tr class='{$shade}'><td><a href=\"{$url}\">{$row->id}</a></td>
                    <td>{$row->title}</td>
                    <td>".search_highlight($row->bodytext, $search)."</td>
                    <td>".number_format($row->score, 2)."</td>
                    <td>".ldate($CONFIG['dateformat_datetime'], $row->timestamp)."</td></tr>";

            if ($shade == 'shade1')
                $shade = 'shade2';
            else
                $shade = 'shade1';
        }

        echo "</table>";
    }
    else
    {
        echo "<p>{$strNoResults}</p>";
        echo "<p><a href='search.php'>{$strSearchAgain}</a></p>";
    }
}
else
{
    $search_string = $_REQUEST['q'];
    $search_domain = cleanvar($_REQUEST['domain']);
    if (empty($search_domain) OR strtolower($search_domain)=='all') $domain='incidents';
    $sort = cleanvar($_REQUEST['sort']);
    if (empty($sort)) $sort='date';
    $order = cleanvar($_REQUEST['order']);
    if (empty($order)) $order='d';

    echo "<form action='{$_SERVER['PHP_SELF']}' method='get'>";
    echo "<table align='center'>";
    echo "<tr><th>";
    echo "{$strSearch} ";
    $domains=array('incidents'=> $strIncidents, 'customers' => $strCustomers, 'maintenance' => $strContracts, 'knowledgebase' => $strKnowledgeBase);
    echo array_drop_down($domains, 'domain', $search_domain);
    echo " {$strFor}:";
    echo "</th>";
    echo "<td>";
    echo "<input maxlength='100' name='q' size='35' type='text' value='".strip_tags(urldecode($search_string))."' /> ";
    echo "(<a href='advanced_search_incidents.php'>{$strAdvanced}</a> | <a href='view_tags.php'>{$strTagCloud}</a>)";
    echo "</td>";
    echo "</tr>\n";
    echo "</table>\n";
    echo "<p><input type='submit' value='{$strSearch}' /></p>";
    echo "</form>";
}



include ('htmlfooter.inc.php');

?>
