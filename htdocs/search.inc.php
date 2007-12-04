<?php
// search.inc.php - Global search with combined results
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

// This Page Is Valid XHTML 1.0 Transitional! 29Nov05

$permission=60; // Perform Searches
$limit_results=2000;

//FIXME make search_string safe
if($searchmode != 'related')
{
    $search_string = $_REQUEST['search_string'];
    $search_domain = cleanvar($_REQUEST['search_domain']);
    if (empty($search_domain) OR strtolower($search_domain)=='all') $search_domain='incidents';
    $sort = cleanvar($_REQUEST['sort']);
    if (empty($sort)) $sort='date';
    $order = cleanvar($_REQUEST['order']);
    if (empty($order)) $order='d';


    echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/search.png' width='32' height='32' alt='' /> ";
    echo "{$strSearch} {$CONFIG['application_shortname']}</h2>\n";
    echo "<form action='{$_SERVER['PHP_SELF']}' method='get'>";
    echo "<table align='center'>";
    echo "<tr><th>";
    echo "{$strSearch} ";
    $domains=array('incidents'=>'Incidents', 'customers' => 'Customers', 'maintenance' => 'Maintenance', 'knowledgebase' => 'KnowlegeBase');
    echo array_drop_down($domains, 'search_domain', $search_domain);
    echo " {$strFor}:";
    echo "</th>";
    echo "<td>";
    echo "<input maxlength='100' name='search_string' size='35' type='text' value='".strip_tags(urldecode($search_string))."' /> ";
    echo "(<a href='advanced_search_incidents.php'>{$strAdvanced}</a> | <a href='view_tags.php'>{$strTagCloud}</a>)";
    echo "</td>";
    echo "</tr>\n";
    echo "</table>\n";
    echo "<p><input name='submit' type='submit' value='{$strSearch}' /></p>";
    echo "</form>";
}
// echo "<table align='center'>";
// echo "<tr><th>OTHER SEARCHES</th></tr>";
// echo "<tr><td><a href='advanced_search_incidents.php'>Search for an Incident</a> (Advanced)</td></tr>";
// // echo "<tr><td class='shade2' width=350><a href='search_contacts.php'>Search for a Contact</a></td></tr>";
// echo "<tr><td><a href='search_renewals.php'>Search Contract Renewals</a></td></tr>";
// // #echo "<tr><td class='shade2' width=350><a href='search_sites.php'>Search for a Site</a></td></tr>";
// // echo "<tr><td class='shade2' width=350><a href='search_maintenance.php'>Search for a Maintenance Contract</a></td></tr>";
// echo "<tr><td><a href='search_expired.php'>Search Expired Contracts</a></td></tr>";
// echo "</table>\n";

/**
    * @author Ivan Lucas
*/
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
        while($i<strlen($x))
        {
            if((($i + strlen($var)) <= strlen($x)) && (strcasecmp($var, substr($x, $i, strlen($var))) == 0))
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


/**
    * @author Ivan Lucas
*/
function search_fix_quoted(&$sterms)
{
    $numterms = count($sterms);
    for ($i=0;$i < $numterms; $i++)
    {
        // start quote
                if (substr($sterms[$i],0,1)=='"')
        {
            for ($n=$i;$n < $numterms; $n++)
            {
                if (substr($sterms[$n],-1)=='"')
                {
                    for ($q=$i;$q<=$n;$q++)
                    {
                        $quotedterm .= str_replace('"','',$sterms[$q]);
                        if ($q < $n) $quotedterm .= '_';
                        if ($q > $i) unset($sterms[$q]);
                    }
                    break;
                }
            }
            $sterms[$i]=$quotedterm;
        }
    }
}


/**
    * @author Ivan Lucas
*/
function search_build_query($column, $sterms)
{
    $numterms = count($sterms);
//       echo "<pre>$column\n\n".print_r($sterms,TRUE)."</pre>";
    for ($i=0;$i < $numterms; $i++)
{
    if ($i>0)
    {
        if ($sterms[$i]=='AND')
        {
            $sql.= 'AND ';
            $i++;
        }
        elseif ($sterms[$i]=='OR')
        {
            $sql.= 'OR ';
            $i++;
        }
        elseif ($sterms[$i]=='NOT')
        {
            $sql.= 'AND NOT ';
            $i++;
        }

        else $sql .= "AND ";
    }
    if (!empty($sterms[$i])) $sql .= "{$column} LIKE '%".str_replace('_',' ',$sterms[$i])."%' ";
}

    return $sql;
}


/**
    * @author Ivan Lucas
*/
function search_build_results(&$srch_results,$entry)
{
    global $key;
    $res = array_multi_search($entry['ref'], $srch_results, 'ref');
    if ($res===FALSE)
    {
//         echo "<h2>{$entry['ref']} Not found</h2>";
        $srch_results[$key]=$entry;
        $key++;
    }
    else
    {
        if ($entry['date'] > $srch_results[$res]['date'])
        {
            $srch_results[$res]['date']=$entry['date'];
            $srch_results[$res]['title']=$entry['title'];
            $srch_results[$res]['string']=$entry['string'];
            $srch_results[$res]['extra']=$entry['extra'];
            $srch_results[$res]['ref']=$entry['ref'];
        }
        $srch_results[$res]['score']+=2;
//         echo "<h2>{$entry['ref']} Result: ".print_r($res,true)."</h2>";
    }
}

function search_score_adjust($sterms, $string)
{
    $score_modifier=0;
    if ($string != 'AND' && $string!='OR' && $string != 'NOT')
    {
        foreach($sterms AS $sterm)
        {
            $positions=string_find_all($string, $sterm);
            if (count($positions) >0 ) $score_modifier+=$numpositions;
        }
    }
    return $score_modifier;
}

// Remove some characters
// '\\"'
$removechars=array(',',';',"'");
$search_string = str_replace($removechars, '', $search_string);

if (!empty($search_string))
{
    echo "<p>Searching for '$search_string' in ".ucfirst($search_domain)."&hellip;</p>"; // FIXME i18n searching for
    flush();
    $sterms = explode(' ',trim($search_string));
    search_fix_quoted($sterms);
    $key=0;
    $srch_results=array();
    switch ($search_domain)
    {
        case 'all':
            // All search not supported now, so just do incidents instead
        case 'incidents':
            $sql = "SELECT * FROM incidents WHERE ";
            if (is_numeric($sterms[0])) $sql .= search_build_query('id', $sterms)."OR ";
            $sql .= search_build_query('title', $sterms);
            if(!empty($software) AND $software != '0') $sql .= "AND softwareid = {$software}";
//             echo "<pre>$sql</pre>";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
            while ($sresult = mysql_fetch_object($result))
            {
                $entry['id']=$sresult->id;
                $entry['ref']="incident-{$sresult->id}";
                $entry['string'] = $sresult->title;
                $entry['score'] = 10 + search_score_adjust($sterms, $entry['string']);
                $entry['title'] = $sresult->title;
                $entry['date'] = $sresult->lastupdated;
                $entry['extra']['opened'] = date($CONFIG['dateformat_datetime'],$sresult->opened);
                if ($sresult->status==2) $entry['extra']['closed'] = date($CONFIG['dateformat_datetime'],$sresult->closed);
                foreach($sterms AS $sterm)
                {
                    $positions=string_find_all($entry['string'], $sterm);
                    $numpositions=count($positions);
                    if ($numpositions >0 ) $entry['score']+=$numpositions;
//                     echo "numpos: $numpositions<br />";
                }
                search_build_results($srch_results,$entry);
                unset($entry);
            }
            if($searchmode != 'related')
            {
            // Incident updates
                $sql = "SELECT DISTINCT incidents.id AS incidentid, incidents.title, updates.bodytext, updates.timestamp, incidents.opened, incidents.closed FROM incidents,updates WHERE ";
                $sql .= "updates.incidentid = incidents.id AND (";
                $sql .= search_build_query('updates.bodytext', $sterms);
                $sql .= ") GROUP BY incidents.id";
                $result = mysql_query($sql);
                if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
                while ($sresult = mysql_fetch_object($result))
                {
                    $entry['id']=$sresult->incidentid;
                    $entry['ref']="incident-{$sresult->incidentid}";
                    $entry['string'] = strip_tags($sresult->bodytext);
                    $entry['score'] = 8 + search_score_adjust($sterms, $entry['string']);
                    $entry['title'] = $sresult->title;
                    $entry['date'] = $sresult->timestamp;
                    $entry['extra']['opened'] = date($CONFIG['dateformat_datetime'],$sresult->opened);
                    if ($sresult->status==2) $entry['extra']['closed'] = date($CONFIG['dateformat_datetime'],$sresult->closed);
                    search_build_results($srch_results,$entry);
                    unset($entry);
                }
//                 echo "<pre>$sql</pre>";
            }
        break;

        case 'customers':
            $sql = "SELECT *, contacts.id AS contactid FROM contacts WHERE ";
            $sql .= search_build_query("CONCAT(forenames,' ',surname)", $sterms);
//             echo "<pre>$sql</pre>";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
            while ($sresult = mysql_fetch_object($result))
            {
                $entry['id']=$sresult->contactid;
                $entry['ref']="contact-{$sresult->id}";
                $entry['string'] = "{$sresult->forenames} {$sresult->surname}";
                $entry['score'] = 10 + search_score_adjust($sterms, $entry['string']);
                $entry['title'] = "{$sresult->forenames} {$sresult->surname}";
                $entry['date'] = $sresult->timestamp_modified;
                search_build_results($srch_results,$entry);
                unset($entry);
            }

            // Sites
                    $sql = "SELECT * FROM sites WHERE ";
            $sql .= search_build_query('name', $sterms);
//             echo "<pre>$sql</pre>";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
            while ($sresult = mysql_fetch_object($result))
            {
                $entry['id']=$sresult->id;
                $entry['ref']="site-{$sresult->id}";
                $entry['string'] = "{$sresult->name}";
                $entry['score'] = 10 + search_score_adjust($sterms, $entry['string']);
                $entry['title'] = $sresult->name;
                $entry['date'] = 0;
                search_build_results($srch_results,$entry);
                unset($entry);
            }
            break;

        case 'maintenance':
            $sql = "SELECT *,maintenance.id AS maintid FROM maintenance,sites WHERE maintenance.site=sites.id AND (";
            $sql .= search_build_query('sites.name', $sterms);
            $sql .= ")";
//             echo "<pre>$sql</pre>";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
            while ($sresult = mysql_fetch_object($result))
            {
                $entry['id']=$sresult->maintid;
                $entry['ref']="contract-{$sresult->maintid}";
                $entry['string'] = "{$sresult->name}";
                $entry['score'] = 10 + search_score_adjust($sterms, $entry['string']);
                $entry['title'] = $sresult->name;
                $entry['extra'] = product_name($sresult->product);
                $entry['date'] = $sresult->expirydate;
                search_build_results($srch_results,$entry);
                unset($entry);
            }
            break;

        case 'knowledgebase':
            $sql = "SELECT * FROM kbarticles WHERE ";
            $sql .= search_build_query('title', $sterms);
//             echo "<pre>$sql</pre>";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
            while ($sresult = mysql_fetch_object($result))
            {
                $entry['id']=$sresult->docid;
                $entry['ref']="kb-{$sresult->docid}";
                $entry['string'] = "{$sresult->title}";
                $entry['score'] = 10 + search_score_adjust($sterms, $entry['string']);
                $entry['title'] = $sresult->title;
                $entry['date'] = mysql2date($sresult->published);
                search_build_results($srch_results,$entry);
                unset($entry);
            }
            break;

        default:
            echo "<p>Searching in this domain not available yet</p>";
    }


    if (count($srch_results) > 0)
    {
        $num_results = count($srch_results);
        if ($sort=='date' AND $order=='d') $srch_results=ansort($srch_results,'date','numeric');
        elseif ($sort=='date' AND $order=='a') $srch_results = array_reverse(ansort($srch_results,'date','numeric'), TRUE);
        elseif ($sort=='score' AND $order=='d') $srch_results=ansort($srch_results,'score','numeric');
        elseif ($sort=='score' AND $order=='a') $srch_results=array_reverse(ansort($srch_results,'score','numeric'), TRUE);
        elseif ($sort=='id' AND $order=='d') $srch_results=ansort($srch_results,'id','numeric');
        elseif ($sort=='id' AND $order=='a') $srch_results=array_reverse(ansort($srch_results,'id','numeric'), TRUE);
        elseif ($sort=='result' AND $order=='a') $srch_results=ansort($srch_results,'title','strnatcasecmp');
        elseif ($sort=='result' AND $order=='d') $srch_results=array_reverse(ansort($srch_results,'title','strnatcasecmp'));
        else $srch_results=ansort($srch_results,'date');

//          echo "<pre>Results:\n".print_r($srch_results,TRUE)."</pre>";

        echo "<p>".sprintf($strResultsNum, $num_results).":</p>";
        echo "<table style='width: 70%; margin-left: auto; margin-right: auto;'>";
        echo "<tr>";
        $filter = array('search_string' => $search_string,
                        'search_domain' => $search_domain);
        echo colheader('id', $strID, $sort, $order, $filter);
        echo colheader('result', $strResult, $sort, $order, $filter);
        echo colheader('score', $strScore, $sort, $order, $filter);
        echo colheader('date', $strDate, $sort, $order, $filter);
        echo "</tr>";
        $shade='shade1';
        foreach($srch_results AS $sresult)
        {
            $type = explode('-',$sresult['ref']);
            $type = $type[0];
            echo "<tr class='$shade'>";
            echo "<td>".ucfirst($type).": {$sresult['id']}</td>";
            switch ($type)
            {
                case 'incident': $url = "javascript:incident_details_window('{$sresult['id']}', 'incident{$sresult['id']}')"; break;
                case 'contact': $url = "contact_details.php?id={$sresult['id']}"; break;
                case 'site': $url = "site_details.php?id={$sresult['id']}"; break;
                case 'contract': $url = "contract_details.php?id={$sresult['id']}"; break;
                case 'kb': $url = "kb_view_article.php?id={$sresult['id']}"; break;
                default: $url = "javascript:alert('nothing to link to');";
            }
            echo "<td style='width:400px;'><a href=\"$url\" class='info'>{$sresult['title']}";
            if (is_array($sresult['extra']))
            {
                echo "<span>";
                foreach($sresult['extra'] AS $extrakey => $extravalue)
                {
                    echo "<strong>".ucfirst($extrakey)."</strong>: $extravalue<br />\n";
                }
                echo "</span></a>";
            }
            else
            {
                echo "</a>";
                if (!empty($sresult['extra'])) echo " ({$sresult['extra']})";
            }
            echo "<br />&hellip;".search_highlight($sresult['string'],$search_string)."&hellip;</td>";
            echo "<td>{$sresult['score']}</td>";
            echo "<td>";
            if ($sresult['date']>0) echo date($CONFIG['dateformat_datetime'],$sresult['date']);
            echo "</td>";
            echo "</tr>\n";
            if ($shade=='shade1') $shade='shade2';
            else $shade='shade1';
        }
        echo "</table>";
    }
    else echo "<p>No results</p>";
}
if($searchmode != 'related')
{
    // FIXME opensearch plugin
    echo "<p>Firefox 2 and IE 7 users: You can <a href=\"javascript:window.external.AddSearchProvider('{$CONFIG['application_uriprefix']}{$CONFIG['application_webpath']}opensearch.php')\">install this search plugin</a> to make searching easier.</p>";
}
?>