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
   $start = 1;
else
    $start = $_GET['start'];
$end = $start + $resultsperpage;

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
                $xtemp .= "<span style='background: #FFFACD; font-weight: bolder;'>" . substr($x, $i , strlen($var)) . "</span>";
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
    
    $sql = "SELECT *,incidentid AS id FROM updates ";
    $sql .= "WHERE MATCH (bodytext) against ('{$search}') GROUP BY incidentid LIMIT {$start}, {$end}";
    echo $sql;
    $result = mysql_query($sql);
    $results = mysql_num_rows($result);
    
    echo "<strong>Display results {$start} to ";
    if(($start + $resultsperpage) > $results)
        echo $results;
    else
        echo $start + $resultsperpage;
        
    echo " of $results</strong>";
    
    echo "<table align='center' width='60%'>";
    echo "<tr>".colheader(id, $strID, $sort, $order, $filter);
    echo colheader(result, $strResult, $sort, $order, $filter);
    echo colheader(score, $strScore, $sort, $order, $filter);
    echo colheader(date, $strDate, $sort, $order, $filter);


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
        echo "<tr><td><a href=\"{$url}\">{$row->id}</a></td><td>".search_highlight($row->bodytext, $search)."</td>";
        echo "<td></td><td></td></tr>";
    }
    
    echo "</table>";
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
