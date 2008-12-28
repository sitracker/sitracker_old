<?php
// incidents_by_software.php - List the number of incidents for each software
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Authors: Paul Heaney <paulheaney[at]users.sourceforge.net>

// Requested by Tech Support team (26 Spet 06)

// Notes:
//  Counts activate calls within the specified period (i.e. those with a lastupdate time > timespecified)

@include ('../set_include_path.inc.php');
$permission = 37; // Run Reports

require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

$title = $strIncidentsBySkill;
$pagescripts = array('dojo/dojo.js');

if (empty($_REQUEST['mode']))
{
    include ('htmlheader.inc.php');

    echo "<h2>$title</h2>";
    echo "<form action='{$_SERVER['PHP_SELF']}' id='incidentsbysoftware' method='post'>";
    echo "<table class='vertical'>";
    echo "<tr><th>{$strStartDate}:</th>";
    echo "<td><input type='text' name='startdate' id='startdate' size='10' /> ";
    echo date_picker('incidentsbysoftware.startdate');
    echo "</td></tr>\n";
    echo "<tr><th>{$strEndDate}:</th>";
    echo "<td><input type='text' name='enddate' id='enddate' size='10' /> ";
    echo date_picker('incidentsbysoftware.enddate');
    echo "</td></tr>\n";
    echo "<tr><th>{$strMonthBreakdown}</th><td><input type='checkbox' name='monthbreakdown' /></td></tr>\n";
    echo "<tr><th>{$strSkill}</th><td>".software_drop_down('software', 0)."</td></tr>\n";
    echo "</table>\n";
    echo "<p align='center'>";
    echo "<input type='hidden' name='mode' value='report' />";
    echo "<input type='submit' value=\"{$strRunReport}\" />";
    echo "</p>";
    echo "</form>\n";

    include ('htmlfooter.inc.php');
}
else
{
    $monthbreakdownstatus = $_REQUEST['monthbreakdown'];
    $startdate = strtotime($_REQUEST['startdate']);
    $enddate = strtotime($_REQUEST['enddate']);

    $sql = "SELECT count(s.id) AS softwarecount, s.name, s.id ";
    $sql .= "FROM `{$dbSoftware}` AS s, `{$dbIncidents}` AS i ";
    $sql .= "WHERE s.id = i.softwareid AND i.opened > '{$startdate}' ";
    if (!empty($enddate)) $sql .= "AND i.opened < '{$enddate}' ";
    $software = $_REQUEST['software'];
    if (!empty($software)) $sql .= "AND s.id ='{$software}' ";
    $sql .= "GROUP BY s.id ORDER BY softwarecount DESC";

    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);

    $countArray[0] = 0;
    $softwareNames[0] = 'Name';
    $softwareID[0] = 0;
    $c = 0;
    $count = 0;
    while ($row = mysql_fetch_array($result))
    {
        $countArray[$c] = $row['softwarecount'];
        $count += $countArray[$c];
        $softwareNames[$c] = $row['name'];
        $softwareID[$c] = $row['id'];
        $c++;
    }

    include ('htmlheader.inc.php');

    echo "<h2>{$strIncidentsBySkill}</h2>";

    if (mysql_num_rows($result) > 0)
    {
        $sqlSLA = "SELECT DISTINCT(tag) FROM `{$dbServiceLevels}`";
        $resultSLA = mysql_query($sqlSLA);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);

        if ($startdate > 1)
        {
            echo "<p align='center'>".sprintf($strSinceX, ldate($CONFIG['dateformat_date'], $startdate))."</p>";
        }
        echo "<table class='vertical' align='center'>";
        echo "<tr><th>{$strNumOfCalls}</th><th>%</th><th>{$strSkill}</th>";
        while ($sla = mysql_fetch_object($resultSLA))
        {
            echo "<th>".$sla->tag."</th>";
            $slas[$sla->tag]['name'] = $sla->tag;
            $slas[$sla->tag]['notEscalated'] = 0;
            $slas[$sla->tag]['escalated'] = 0;
        }
        echo "<tr>";

        $others = 0;
        $shade = 'shade1';
        for ($i = 0; $i < $c; $i++)
        {
            if ($i <= 25)
            {
                $data .= $countArray[$i]."|";
                $percentage = number_format(($countArray[$i]/$count) * 100,1);
                $legend .= $softwareNames[$i]." ({$percentage}%)|";
            }
            else
            {
                $others += $countArray[$i];
            }

            $sqlN = "SELECT id, servicelevel, opened FROM `{$dbIncidents}` WHERE softwareid = '".$softwareID[$i]."'";
            $sqlN .= " AND opened > '{$startdate}' ORDER BY opened";

            $resultN = mysql_query($sqlN);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
            $numrows = mysql_num_rows($resultN);

            foreach ($slas AS $slaReset)
            {
                $slaReset['notEscalated'] = 0;
                $slaReset['escalated'] = 0;
            }


            if ($numrows > 0)
            {
                unset($monthbreakdown);
                while ($obj = mysql_fetch_object($resultN))
                {
                    $datestr = date("M y",$obj->opened);

                    // FIXME this sql uses the body to find out which incidents have been escalated
                    $sqlL = "SELECT count(id) FROM `{$dbUpdates}` AS u ";
                    $sqlL .= "WHERE u.bodytext LIKE \"External ID%\" AND incidentid = '".$obj->id."'";
                    $resultL = mysql_query($sqlL);
                    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_WARNING);
                    list($numrowsL) = mysql_fetch_row($resultL);

                    if ($numrowsL > 0) $slas[$obj->servicelevel]['escalated']++;
                    else $slas[$obj->servicelevel]['notEscalated']++;

                    $monthbreakdown[$datestr][$obj->servicelevel]++;
                    $monthbreakdown[$datestr]['month']=$datestr;
                }
            }
            echo "<tr class='$shade'><td>{$countArray[$i]}</td>";
            echo "<td>{$percentage}%</td>";
            echo "<td>{$softwareNames[$i]}</td>";

            foreach ($slas AS $sla)
            {
                echo "<td>";
                echo ($sla['notEscalated']+$sla['escalated'])." / ".$sla['escalated'];
                echo "</td>";
            }

            if ($monthbreakdownstatus === "on")
            {
                echo "<tr class='$shade'><td></td><td colspan='".(count($slas)+2)."'>";
                echo "<table style='width: 100%'><tr>";
                foreach ($monthbreakdown AS $month)
                {
                    echo "<th>{$month['month']}</th>";
                }
                echo "</tr>\n<tr>";
                foreach ($monthbreakdown AS $month)
                {//echo "<pre>".print_r($month)."</pre>";
                    echo "<td><table>";
                    $total = 0;
                    foreach ($slas AS $slaNames)
                    {
                        if (empty($month[$slaNames['name']])) $month[$slaNames['name']] = 0;
                        echo "<tr>";
                        echo "<td>".$slaNames['name']."</td><td>".$month[$slaNames['name']]."</td>";
                        echo "</tr>\n";
                        $total += $month[$slaNames['name']];
                    }
                    echo "<tr><td><strong>{$strTotal}</strong></td><td><strong>";
                    echo $total;
                    echo "</strong></td></tr>\n";
                    $monthtotals[$month['month']]['month'] = $month['month'];
                    $monthtotals[$month['month']]['value'] += $total;
                    $skilltotals[$softwareNames[$i]]['name'] = $softwareNames[$i];
                    $skilltotals[$softwareNames[$i]][$month['month']]['month'] = $month['month'];
                    $skilltotals[$softwareNames[$i]][$month['month']]['numberofincidents'] = $total;

                    $months[date_to_str($month['month'])] = $month['month'];
                    echo "</table></td>";
                }
                echo "</tr></table>";
                echo "</td></tr>\n";
            }
            if ($shade == 'shade1') $shade = 'shade2';
            else $shade = 'shade1';
        }
        echo "</table>";

        if ($monthbreakdownstatus === "on")
        {
            echo "<p><table align='center'>";
            echo "<tr><th>{$strMonth}</th><th>{$strNumOfCalls}</th></tr>";
            $shade = 'shade1';

            $total = 0;

            foreach ($monthtotals AS $m)
            {
                echo "<tr class='$shade'>";
                echo "<td>".$m['month']."</td><td align='center'>".$m['value']."</td><tr>";
                $total += $m['value'];
                echo "</tr>";
                if ($shade == 'shade1') $shade = 'shade2';
                else $shade = 'shade1';
            }
            echo "<tfoot><tr><th>{$strTotal}</th><td align='center'><strong>{$total}</strong></td></tr></tfoot>";
            echo "</table>";

            ksort($months);

            //echo "<pre>";
            //print_r($skilltotals);
            //print_r($months);
            //echo "</pre>";

            $shade = "shade1";

            echo "<p><table align='center'><tr><td></td>";
            foreach ($months AS $m)
            {
                echo "<th>{$m}</th>";
            }
            echo "<th>{$strTotal}</th></tr>";
            $js_coordCounter = 0;
            $min = 0;
            $max = 0;
            foreach ($skilltotals AS $skill)
            {

                echo "<tr class='{$shade}'><td>{$skill['name']}</td>";
                $sum = 0;
                $counter = 0;
                $coords = '';
                foreach ($months AS $m)
                {
                    $val = $skill[$m]['numberofincidents'];
                    if (empty($val)) $val = 0;
                    echo "<td>{$val}</td>";
                    $sum += $val;

                    if ($val < $min) $min = $val;
                    if ($val > $max) $max = $val;

                    $coords .= "{ x: {$counter}, y: {$val} }, ";
                    $counter++;
                }
                echo "<td>{$sum}</td></tr>";

                $percentage = ($sum / $total) * 100;

                if ($shade == "shade1") $shade = "shade2";
                else $shade = "shade1";

                $clgth = strlen($coords)-2;
                $coords = substr($coords, 0, $clgth);

                if ($percentage >= 5)
                {
                    //only show on graph items with 5% or more of the share
                    $javascript .= "var d{$js_coordCounter} = [ {$coords} ]\n\n";
                    $javascript .= "var store{$js_coordCounter} = new dojo.collections.Store();\n";
                    $javascript .= "store{$js_coordCounter}.setData(d{$js_coordCounter});";
                    $javascript .= "var s{$js_coordCounter} = new dojo.charting.Series({";
                    $javascript .= "dataSource:store{$js_coordCounter},";
                    $javascript .= "bindings:{ x:\"x\", y:\"y\", size:\"size\" },";
                    $javascript .= "label:\"{$skill['name']}\"";
                    $javascript .= "});\n\n\n\n";

                    //echo $javascript."<br />";

                    $js_coordCounter++;
                }
            }

            $javascript .= "var xA = new dojo.charting.Axis();\n";
            $javascript .= "xA.range={upper:".($counter-1).", lower:0};\n";
            $javascript .= "xA.origin=\"max\";\n";
            $javascript .= "xA.showTicks = true;\n";
            $javascript .= "xA.label = \"Months\";\n";
            /*$javascript .= "xA.labels = [ "Mon", "Tue", 2, 3, 4, 5 ];";*/
            $javascript .= "xA.labels = [";
            foreach ($months AS $m)
            {
                $javascript .= "\"{$m}\", ";
            }
            $javascript .= "];\n";

            $javascript .= "var yA = new dojo.charting.Axis();\n";
            $javascript .= "yA.range={upper:{$max},lower:{$min}};\n";
            $javascript .= "yA.labels = [ {label:\"{$min}\", value:{$min} }, { label:\"{$max}\",value:35 }, { label:\"{$max}\", value:{$max} } ];\n";
            $javascript .= "yA.label = \"Volume\"\n\n";

            $javascript .= "var p = new dojo.charting.Plot(xA, yA);\n\n";

            for($i = 0; $i  < $js_coordCounter; $i++)
            {
                    $javascript .= "p.addSeries({ data:s{$i}, plotter: dojo.charting.Plotters.CurvedLine });";
            }

            $javascript .= "var pa = new dojo.charting.PlotArea();";
            $javascript .= "pa.size={width:700,height:170};";
            $javascript .= "pa.padding={top:20, right:20, bottom:30, left:50 };";
            $javascript .= "pa.plots.push(p);";

            $javascript .= "pa._color =  { h: 9, s: 246, l: 143, step: 90 };";

                    //  auto assign colors, and increase the step (since we've only 2 series)
            for($i = 0; $i  < $js_coordCounter; $i++)
            {
                $javascript .= "s{$i}.color = pa.nextColor();";

            }

            $javascript .= "var pA = new dojo.charting.Plot(xA, yA);";

            $grandsum = 0;

            echo "<th>{$strTotal}</th>";
            foreach ($months AS $m)
            {
                echo "<td>";
                echo $monthtotals[$m]['value'];
                echo "</td>";

                $grandsum += $monthtotals[$m]['value'];
            }

            echo "<td>{$grandsum}</td></table></p>";


            echo "<script type='text/javascript'>\n//<![CDATA[\n";
                echo "dojo.require ('dojo.collections.Store');";
                echo "dojo.require ('dojo.charting.Chart');";
                echo "dojo.require ('dojo.widget.ContentPane');";
                echo "dojo.require ('dojo.json');";

                echo "var legend;";

                echo "dojo.addOnLoad(function(){";
                    echo $javascript;

                    echo "var chart = new dojo.charting.Chart(null, \"{$strIncidentsBySkill}\", \"A chart\");";
                    echo "chart.addPlotArea({ x:50,y:50, plotArea:pa });";

                    echo "legend = pa.getLegendInfo();";

                    echo "chart.node = dojo.byId(\"incidentsBySkill\");";
                    echo "chart.render();";


                    echo "var docpane = dojo.widget.byId(\"legend\");";
                    //docpane.setContent("Booo");
                    echo "var a=\"<table>\";";
                    echo "for(var i=0; i<legend.length;i++){";
                        echo "a = a+\"<tr><td style='color: \"+legend[i].color+\";'>\"+legend[i].label+\"</td></tr>\";";
                    echo "}";
                    echo "a = a+\"</table>\";";
                    echo "docpane.setContent(a);";
                echo "});";
            echo "\n//]]>\n</script>";

            echo "<style>";
                echo "#incidentsBySkill {";
                    echo "margin:12px;";
                    echo "width:800px;";
                    echo "height:300px;";
                    echo "background-color:#dedeed;";
                    echo "border:1px solid #999;";
                echo "}";
            echo "</style>";
            echo "<div id='incidentsBySkill' style='margin-right:auto;margin-left:auto;'></div>";
            echo "<div  dojoType='ContentPane' layoutAlign='client' style='background-color: #f5ffbf; padding: 10px; width: 20%;margin-right:auto;margin-left:auto; '";
            echo "id='legend' executeScripts='true'></div>";


        }

        $data .= $others."|";
        $percentage = @number_format(($others/$count) * 100,1);
        $legend .= "Others ($percentage)|";


        echo "</p>";

        if (extension_loaded('gd'))
        {
            $data = substr($data,0,strlen($data)-1);
            $legend = substr($legend,0,strlen($legend)-1);
            $title = urlencode($strIncidentsBySkill);
            echo "\n<br /><p><div style='text-align:center;'>";
            echo "\n<img src='../chart.php?type=pie&data=$data&legends=$legend&title=$title' />";
            echo "\n</div></p>";
        }
    }
    else
    {
        echo "<p class='error'>{$strNoRecords}</p>";
    }
    include ('htmlfooter.inc.php');

}

/**
    * @author Paul Heaney
*/
function date_to_str($date)
{
    $s = explode(" ",$date);
    switch ($s[0])
    {
        case 'Jan': return $s[1]."01";
            break;
        case 'Feb': return $s[1]."02";
                    break;
        case 'Mar': return $s[1]."03";
                    break;
        case 'Apr': return $s[1]."04";
                    break;
        case 'May': return $s[1]."05";
                    break;
        case 'Jun': return $s[1]."06";
                    break;
        case 'Jul': return $s[1]."07";
                    break;
        case 'Aug': return $s[1]."08";
                    break;
        case 'Sep': return $s[1]."09";
                    break;
        case 'Oct': return $s[1]."10";
                    break;
        case 'Nov': return $s[1]."11";
                    break;
        case 'Dec': return $s[1]."12";
                    break;
    }
}

?>
