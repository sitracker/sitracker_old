<?php

// external_engineers.php - Shows incidents that have been escalated
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Authors: Paul Heaney <paulheaney[at]users.sourceforge.net>
//          Kieran Hogg <kieran_hogg[at]users.sourceforge.net>
// heavily based on the Salford Report by Paul Heaney

@include('../set_include_path.inc.php');
$permission=37; // Run Reports

include('db_connect.inc.php');
include('functions.inc.php');
require('auth.inc.php');

include('htmlheader.inc.php');
echo "<script type='text/javascript'>";
?>
function incident_details_window_l(incidentid,second)
{
    URL = "<?php  echo $CONFIG['application_uriprefix'].$CONFIG['application_webpath'] ?>incident_details.php?id=" + incidentid + "&amp;javascript=enabled";
    window.open(URL, "sit_popup", "toolbar=yes,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=700,height=600");
}
<?php
echo "</script>";

echo "<h2>{$strExternalEngineerCallDistribution}</h2>";

$sql = "SELECT id, name FROM escalationpaths";
$escs = mysql_query($sql);
while($escalations = mysql_fetch_object($escs))
{
        $html .= "<h3>{$escalations->name}</h3>";
        $sql = "SELECT incidents.*, software.name, contacts.forenames, contacts.surname, sites.name AS siteName FROM incidents, software, contacts, sites WHERE escalationpath = '{$escalations->id}' AND closed = '0' AND software.id = incidents.softwareid ";
        $sql .= " AND incidents.contact = contacts.id AND contacts.siteid = sites.id ";
        $sql .= "ORDER BY externalengineer";

        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        $i = 0;
        while($obj = mysql_fetch_object($result))
        {
            $name = $obj->externalengineer;
            if(empty($name)) $name=$strNoNameAssociated;
            $esc[$name]['name']=$name;
            $esc[$name]['count']++;
            $esc[$name][$obj->priority]++;
            $str = "<span><strong>".$obj->forenames." ".$obj->surname."</strong><br />".$obj->siteName."</span>";
            $esc[$name]['calls'][$i]['text'] = "<a href=\"javascript:incident_details_window_l('".$obj->id."', 'incident".$obj->id."')\"  title=\"{$obj->title}\" class='info'>[".$obj->id."]{$str}</a> #".$obj->externalid." ".$obj->title;
            $esc[$name]['calls'][$i]['software']=$obj->name;
            $esc[$name]['calls'][$i]['status']=$obj->status;
            $esc[$name]['calls'][$i]['localowner']=$obj->owner;
            $esc[$name]['calls'][$i]['salfordtowner']=$obj->towner;
            $i++;
        }
        if(!empty($esc))
        {
            $html .= "<table align='center'>";
            $html .= "<tr><th>{$strExternalEngineersName}</th><th>{$strNumOfCalls}</th>";
            $html .= "<th align='center'>".priority_icon(4)."</th>";
            $html .= "<th align='center'>".priority_icon(3)."</th>";
            $html .= "<th align='center'>".priority_icon(2)."</th>";
            $html .= "<th align='center'>".priority_icon(1)."</th>";
            $html .= "<td>";
            $html .= "<table width='100%'><tr><th width='50%'>{$strIncident}</th><th width='12%'>{$strInternalEngineer}</th><th width='25%'>{$strSoftware}</th><th>{$strStatus}</th></tr></table>\n";
            $html .= "</td>";
            $html .= "</tr>\n";

            foreach($esc AS $engineer)
            {
                if(empty($engineer['4']))  $engineer['4'] = 0;
                if(empty($engineer['3']))  $engineer['3'] = 0;
                if(empty($engineer['2']))  $engineer['2'] = 0;
                if(empty($engineer['1']))  $engineer['1'] = 0;

                $html .= "<tr>";
                $html .= "<td class='shade1'>{$engineer['name']}</td><td class='shade1'>".$engineer['count']."</td>";
                $html .= "<td class='shade1'>".$engineer['4']."</td>";
                $html .= "<td class='shade1'>".$engineer['3']."</td>";
                $html .= "<td class='shade1'>".$engineer['2']."</td>";
                $html .= "<td class='shade1'>".$engineer['1']."</td>";
                $html .= "<td  class='shade1' >";
                $html .= "<table width='100%'>";
                foreach($engineer['calls'] AS $call)
                {
                    $replace = array("Response","Action");
                    $html .= "<tr><td width='50%'>{$call['text']}</td><td width='12%'>".user_realname($call['localowner']);
                    if(!empty($call['salfordtowner'])) $html .= "<br />T: ".user_realname($call['salfordtowner']);
                    $html .= "</td><td width='25%'>".$call['software']."</td><td>".str_replace($replace,"",incidentstatus_name($call['status']))."</td></tr>";
                }
                $html .= "</table>\n\n";
                $html .= "</td>";
                $total+=$engineer['count'];
                $c['4']+=$engineer['4'];
                $c['3']+=$engineer['3'];
                $c['2']+=$engineer['2'];
                $c['1']+=$engineer['1'];
                $html .= "</tr>\n";
            }
            $html .= "<tr><td>{$strTotal}:</td><td>{$total}</td>";
            if(empty($c['4'])) $c['4']=0;
            if(empty($c['3'])) $c['3']=0;
            if(empty($c['2'])) $c['2']=0;
            if(empty($c['1'])) $c['1']=0;
            $html .= "<td>".$c['4']."</td>";
            $html .= "<td>".$c['3']."</td>";
            $html .= "<td>".$c['2']."</td>";
            $html .= "<td>".$c['1']."</td>";
            $html .= "</tr>\n";
            $html .= "</table>\n\n";
        }
        else
            $html .= "<p align='center'>{$strNoIncidents}</p>";
    unset($obj);
    unset($esc);
}
echo $html;
include('htmlfooter.inc.php');
?>
