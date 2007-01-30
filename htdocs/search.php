<?php
// search.php - Global search with combined results
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

// This Page Is Valid XHTML 1.0 Transitional! 29Nov05

$permission=60; // Perform Searches
$limit_results=2000;

require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

$search_string = cleanvar($_REQUEST['search_string']);
$search_domain = cleanvar($_REQUEST['search_domain']);

if (empty($search_string) OR empty($search_domain))
{
    include('htmlheader.inc.php');
    echo "<h2>Search {$CONFIG['application_shortname']}</h2>\n";
    echo "<form action='{$_SERVER['PHP_SELF']}' method='get'>";
    echo "<table align='center'>";
    echo "<tr><th>";
    /*
    echo "Search ";
    echo "<select name='search_domain'>";
    echo "<option value='incidents'>Incidents</option>";
    echo "<option value='contacts'>Contacts</option>";
    echo "<option value='sites'>Sites</option>";
    echo "<option value='contracts'>Maintenance Contracts</option>";
    echo "<option value='kb'>Knowledge Base</option>";
    echo "</select>";
    */
    echo "<input type='hidden' name='search_domain' value='all' />";
    echo " for:";
    echo "</th>";
    echo "<td>";
    echo "<input maxlength='100' name='search_string' size='35' type='text' value='".strip_tags(urldecode($search_string))."' />";
    echo "</td>";
    echo "</tr>\n";
    echo "</table>\n";
    echo "<p><input name='submit' type='submit' value='Search' /></p>";
    echo "</form>";

    echo "<table align='center'>";
    echo "<tr><th>OTHER SEARCHES</th></tr>";
    echo "<tr><td><a href='advanced_search_incidents.php'>Search for an Incident</a> (Advanced)</td></tr>";
    // echo "<tr><td class='shade2' width=350><a href='search_contacts.php'>Search for a Contact</a></td></tr>";
    echo "<tr><td><a href='search_renewals.php'>Search Contract Renewals</a></td></tr>";
    // #echo "<tr><td class='shade2' width=350><a href='search_sites.php'>Search for a Site</a></td></tr>";
    // echo "<tr><td class='shade2' width=350><a href='search_maintenance.php'>Search for a Maintenance Contract</a></td></tr>";
    echo "<tr><td><a href='search_expired.php'>Search Expired Contracts</a></td></tr>";
    echo "</table>\n";

    echo "<p /><table align='center'>";
    echo "<tr><th>Firefox 2 and IE 7 users</th></tr>";
    echo "<tr><td>You can <a href=\"javascript:window.external.AddSearchProvider('{$CONFIG['application_uriprefix']}{$CONFIG['application_webpath']}opensearch.php')\">install this search plugin</a> to make searching easier</td></tr>";

    echo "</table>";
    include('htmlfooter.inc.php');
}
else
{
    if ($_REQUEST['sourceid']=='Mozilla-search-ivan' || TRUE==TRUE)
    {
        function highlight($x,$var)
        {
            //$x is the string, $var is the text to be highlighted
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


        function ansort($x,$var,$cmp='strcasecmp')
        {
            // Numeric descending sort of multi array
            if ( is_string($var) ) $var = "'$var'";
            // uasort($x, create_function('$a,$b', 'return '.$cmp.'( $a['.$var.'],$b['.$var.']);'));
            uasort($x, create_function('$a,$b', 'return '.'( $a['.$var.'] < $b['.$var.']);'));
            return $x;
        }

        function mansort($x,$sortby)
        {
            // Numeric descending sort of multi array
            static $sort_funcs = array();
            if (empty($sort_funcs[$sortby]))
            {
                $code = "\$c=0;\n";
                foreach (explode(',', $sortby) as $key)
                {
                    $code .= "if (\$a['$key'] < \$b['$key']) return \$c;\n";
                }
                $code .= "return $c;";
                $sort_func = $sort_funcs[$sortby] = create_function('$a, $b', $code);
            }
            else
            {
                $sort_func = $sort_funcs[$sortby];
            }
            // echo $code;
            $sort_func = $sort_funcs[$sortby];
            uasort($x, $sort_func);

            // if ( is_string($var) ) $var = "'$var'";
            // uasort($x, create_function('$a,$b', 'return '.$cmp.'( $a['.$var.'],$b['.$var.']);'));
            // uasort($x, create_function('$a,$b', 'return '.'( $a['.$var.'] < $b['.$var.']);'));
            return $x;
        }


        function masort(&$data, $sortby)
        {
            static $sort_funcs = array();
            if (empty($sort_funcs[$sortby]))
            {
                $code = "\$c=0;";
                foreach (split(',', $sortby) as $key)
                {
                    // $code .= "if ( (\$c = strcasecmp(\$a['$key'],\$b['$key'])) != 0 ) return \$c;\n";
                    $code .= "if (";
                }
                $code .= 'return $c;';
                $sort_func = $sort_funcs[$sortby] = create_function('$a, $b', $code);
            }
            else
            {
                $sort_func = $sort_funcs[$sortby];
            }
            $sort_func = $sort_funcs[$sortby];
            uasort($data, $sort_func);
        }

        $key=0;
        // Scoring Points
        // 10 for a match in the title or primary field
        // 8 for a match in a secondary field
        // 5 for a match in a lesser field
        // 1 for a match in a minor field
        // -5 for an expired, closed or disabled result

        function search_incidents($search_string)
        {
            global $key, $limit_results;
            $search_string=strtolower($search_string);
            $sql  = "SELECT incidents.id, externalid, title, priority, siteid, incidents.owner, type, surname, forenames, lastupdated, status, sites.name AS sitename FROM incidents, contacts, sites WHERE contact=contacts.id AND contacts.siteid=sites.id AND ";
            $sql .= "(title LIKE ('%$search_string%') OR ";
            $sql .= "incidents.id LIKE ('%$search_string%') OR ";
            $sql .= "externalid LIKE ('%$search_string%') OR ";
            $sql .= "sites.name LIKE ('%$search_string%') OR ";
            $sql .= "surname LIKE ('%$search_string%') OR ";
            $sql .= "forenames LIKE ('%$search_string%') OR ";
            $sql .= "CONCAT(forenames, ' ', surname) LIKE ('%$search_string%')) ORDER BY incidents.id DESC LIMIT $limit_results";
            $result=mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Error".mysql_error(), E_USER_ERROR);
            $srch_results = array();
            if (mysql_num_rows($result)>=1)
            {
                while($row = mysql_fetch_object($result))
                {
                    $srch_results[$key]['url']="<a href=\"javascript:incident_details_window('{$row->id}', 'incident{$row->id}');\">Incident {$row->id}: ".stripslashes($row->title)."</a>";
                    $owner=user_realname($row->owner,TRUE);
                    $srch_results[$key]['string'] = stripslashes($row->title);
                    if ($row->status==2) $srch_results[$key]['string'] .= " (Closed)";
                    $srch_results[$key]['string'] .= "\n{$row->forenames} {$row->surname}, {$row->sitename}\n{$owner} {$row->externalid}";
                    $srch_results[$key]['date']=$row->lastupdated;
                    $srch_results[$key]['score']+=(substr_count(strtolower($row->title),$search_string)*10);
                    $srch_results[$key]['score']+=(substr_count($row->id,$search_string)*10);
                    $srch_results[$key]['score']+=(substr_count($row->externalid,$search_string)*8);
                    $srch_results[$key]['score']+=(substr_count(strtolower($row->sitename),$search_string)*8);
                    $srch_results[$key]['score']+=(substr_count(strtolower($row->surname),$search_string)*5);
                    $srch_results[$key]['score']+=(substr_count(strtolower($row->forenames),$search_string)*1);
                    $srch_results[$key]['score']+=(substr_count(strtolower("{$row->forenames} {$row->surname}"),$search_string)*8);
                    $srch_results[$key]['score']+=(substr_count(strtolower($owner),$search_string)*10);
                    if ($row->status==2) $srch_results[$key]['score']-=5;
                    $key++;
                }
            }
            return($srch_results);
        }

        function search_contacts($search_string)
        {
            global $key, $limit_results;
            $sql = "SELECT * FROM contacts WHERE (surname LIKE ('%$search_string%') OR forenames LIKE ('%$search_string%') ";
            $sql .= "OR CONCAT(forenames, ' ', surname) LIKE ('%$search_string%')) LIMIT $limit_results";
            $result=mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Error".mysql_error(), E_USER_ERROR);
            $srch_results = array();
            if (mysql_num_rows($result)>=1)
            {
                while($row = mysql_fetch_object($result))
                {
                    $srch_results[$key]['url'] = "<a href='contact_details.php?id={$row->id}'>Contact {$row->id}: {$row->forenames} {$row->surname}</a>";
                    $srch_results[$key]['string'] = "{$row->forenames} {$row->surname}";
                    $srch_results[$key]['score']+=(substr_count(strtolower($row->surname),$search_string)*10);
                    $srch_results[$key]['score']+=(substr_count(strtolower($row->forenames),$search_string)*8);
                    $srch_results[$key]['score']+=(substr_count(strtolower("{$row->forenames} {$row->surname}"),$search_string)*10);
                    $srch_results[$key]['date']=mysqlts2date($row->timestamp_modified);
                    $key++;
                }
            }
            return($srch_results);
        }

        function search_kb($search_string)
        {
            global $key, $limit_results;
            $sql = "SELECT * FROM kbarticles WHERE (title LIKE ('%$search_string%') OR keywords LIKE ('%$search_string%'))LIMIT $limit_results";
            $result=mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Error".mysql_error(), E_USER_ERROR);
            $srch_results = array();
            if (mysql_num_rows($result)>=1)
            {
                while($row = mysql_fetch_object($result))
                {
                    $srch_results[$key]['url'] = "<a href='kb_view_article.php?id={$row->docid}'>Knowledge Base Article SKB{$row->docid}</a>";
                    $srch_results[$key]['string'] = "{$row->title}\n{$row->keywords}";
                    $srch_results[$key]['score']+=(substr_count(strtolower($row->title),$search_string)*10);
                    $srch_results[$key]['score']+=(substr_count(strtolower($row->keywords),$search_string)*10);
                    $srch_results[$key]['date'] = mysql2date($row->published);
                    $key++;
                }
            }
            return($srch_results);
        }

        function search_sites($search_string)
        {
            global $key, $limit_results;
            $sql = "SELECT * FROM sites WHERE name LIKE ('%$search_string%') ORDER BY name LIMIT $limit_results";
            $result=mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Error".mysql_error(), E_USER_ERROR);
            $srch_results = array();
            if (mysql_num_rows($result)>=1)
            {
                while($row = mysql_fetch_object($result))
                {
                    $srch_results[$key]['url'] = "<a href='site_details.php?id={$row->id}'>Site {$row->id}: {$row->name}</a> ";
                    $srch_results[$key]['string'] = "{$row->name}\n";
                    if (!empty($row->department)) $srch_results[$key]['string'] .= "{$row->department}\n";
                    if (!empty($row->city)) $srch_results[$key]['string'] .= " {$row->city}";
                    if (!empty($row->county)) $srch_results[$key]['string'] .= " {$row->county}";
                    if (!empty($row->country)) $srch_results[$key]['string'] .= " {$row->country}";
                    $srch_results[$key]['score']+=(substr_count(strtolower($row->name),$search_string)*10);
                    $srch_results[$key]['score']+=(substr_count(strtolower($row->city),$search_string)*8);
                    $srch_results[$key]['score']+=(substr_count(strtolower($row->department),$search_string)*5);
                    $key++;
                }
            }
            return($srch_results);
        }

        function search_updates($search_string)
        {
            global $key, $limit_results;
            $sql = "SELECT * FROM updates WHERE bodytext LIKE ('%$search_string%') ORDER BY id DESC LIMIT $limit_results";
            $result=mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Error".mysql_error(), E_USER_ERROR);
            $srch_results = array();
            if (mysql_num_rows($result)>=1)
            {
                while($row = mysql_fetch_object($result))
                {
                    $srch_results[$key]['url']= "<a href='incident_details.php?id={$row->incidentid}'>Incident {$row->incidentid}: Update entry {$row->id}</a>";
                    $srch_results[$key]['string'] = "{$row->bodytext}";
                    $srch_results[$key]['score']+=(substr_count(strtolower($row->bodytext),$search_string)*5);
                    $srch_results[$key]['date']=mysqlts2date($row->timestamp);
                    if ($srch_results[$key]['score'] >= 10) $srch_results[$key]['score']=10;
                    $key++;
                }
            }
            return($srch_results);
        }

        function search_maintenance($search_string)
        {
            global $key, $limit_results;
            $sql = "SELECT *, sites.name AS sitename, products.name AS productname FROM maintenance, sites, products WHERE maintenance.site=sites.id ";
            $sql .= "AND maintenance.product=products.id ";
            $sql .= "AND (sites.name LIKE ('%$search_string%') ";
            $sql .= "OR products.name LIKE ('%$search_string%')) ";
            $sql .= "ORDER BY maintenance.id DESC LIMIT $limit_results";
            $result=mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Error".mysql_error(), E_USER_ERROR);
            $srch_results = array();
            if (mysql_num_rows($result)>=1)
            {
                while($row = mysql_fetch_object($result))
                {
                    $srch_results[$key]['url'] = "<a href='maintenance_details.php?id={$row->id}'>Contract {$row->id}: {$row->sitename}</a>";
                    $srch_results[$key]['string'] = "{$row->sitename}\n{$row->productname}";
                    $srch_results[$key]['score']+=(substr_count(strtolower($row->sitename),$search_string)*10);
                    $srch_results[$key]['score']+=(substr_count(strtolower($row->productname),$search_string)*10);
                    if ($row->term=='yes')
                    {
                        $srch_results[$key]['score']-=5;
                        $srch_results[$key]['string'] .= " (Terminated)";
                    }
                    if ($row->expirydate <= time())
                    {
                        $srch_results[$key]['score']-=2;
                        $srch_results[$key]['string'] .= " (Expired)";
                    }
                    $key++;
                }
            }
            return($srch_results);
        }

        include('htmlheader.inc.php');
        $srch_results1 = search_incidents($search_string);
        $srch_results2 = search_contacts($search_string);
        $srch_results3 = search_kb($search_string);
        $srch_results4 = search_sites($search_string);
        $srch_results6 = search_maintenance($search_string);
        // $srch_results7 = search_updates($search_string); // bugbug not finished
        // , $srch_results7
        //$srch_results = array_merge($srch_results1, $srch_results2, $srch_results3, $srch_results4, $srch_results5, $srch_results6);
        $srch_results = array_merge($srch_results1, $srch_results2, $srch_results3, $srch_results4, $srch_results6);

        // bugbug also offer a sort by date
        if ($sortby=='date') $srch_results=ansort($srch_results,'date');
        else $srch_results=ansort($srch_results,'score');

        // masort($src_results,'score,date');
        // $srch_results=mansort($srch_results,'score,date');

        if ($srch_results)
        {
            echo "<div style='text-align: center;'><form action='{$_SERVER['PHP_SELF']}' method='GET'>";
            echo "<input type='text' name='search_string' value='$search_string' />";
            echo "<input type='hidden' name='search_domain' value='all' />";
            echo "<input type='submit' value='Search' />";
            echo "</form></div>";
            echo "<h2>Search Results:</h2>";
            echo "<h5>$key Matches for '$search_string'</h5>";
            echo "<div style='margin-left: 10%; margin-right: auto; width=70%;'>";

            //print_r($srch_results);

            echo "<dl>";
            foreach($srch_results AS $row => $val)
            {
                $relavance=($val['score']*100)/25;
                if ($relavance > 100) $relavance=100;
                echo "<dt><h3 style='text-align: left'>{$val['url']}</h3></dt>\n";
                echo "<dd>".nl2br(highlight($val['string'], $search_string))."<br />";
                echo "<strong title='{$val['score']}'>Relevance</strong>: {$relavance}% ";
                if ($val['date']>1000) echo "<strong>Date</strong>: ".date('d M Y',$val['date']);
                echo "</dd>\n";
                ## print_r($val);
            }
            echo "</dl>\n";
            echo "</div>";
        }
        include('htmlfooter.inc.php');
    }
    else
    {
        switch ($search_domain)
        {
            case 'incidents':
                header("Location: search_incidents.php?fields=all&search_string=".urlencode($search_string));
                exit;
            break;

            case 'contacts':
                header("Location: search_contacts.php?fields=all&search_string=".urlencode($search_string));
                exit;
            break;

            case 'sites':
                header("Location: search_sites.php?fields=all&search_string=".urlencode($search_string));
                exit;
            break;

            case 'contracts':
                header("Location: search_maintenance.php?fields=all&search_string=".urlencode($search_string));
                exit;
            break;

            case 'kb':
                header("Location: browse_kb.php?search_string=".urlencode($search_string));
                exit;
            break;

            default:
                header("Location: search.php");
                exit;
        }
    }
}
?>
