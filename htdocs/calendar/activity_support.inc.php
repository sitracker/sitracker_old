<?php

// activity_support.inc.php - Support activity information
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Tom Gerrard <tom.gerrard[at]salfordsoftware.co.uk>
//
// Included by timesheet.inc.php
$permission=27; // View your calendar FIXME
include_once('db_connect.inc.php');
include_once('functions.inc.php');
include_once('auth.inc.php');


foreach(array('level', 'data', 'ws' ) as $var)
	eval("\$$var=cleanvar(\$_REQUEST['$var']);");

if ($level == "")
{
    $activity_types['Support'] = "";
    echo "<script type='text/javascript'>
        
        function activitySupport(level)
        {
            if (level == 0)
            {
                var newid = weekSchedule_ajaxObjects.length;
                weekSchedule_ajaxObjects[newid] = new sack();
                weekSchedule_ajaxObjects[newid].requestFile = 'calendar/activity_support.inc.php?level=' + level + '&data=' + getSelectedActivity() + '&ws=' + dateStartOfWeek.getTime();
                weekSchedule_ajaxObjects[newid].onCompletion = function(){ activitySupportCallback(level, newid); }; 
                weekSchedule_ajaxObjects[newid].runAJAX();		                
            }
        }
        
        function activitySupportCallback(level, newid)
        {
            var incidents = new Array();
            var items = weekSchedule_ajaxObjects[newid].response.split(/<item>/g);
            weekSchedule_ajaxObjects[newid] = false;
            for (var i = 1; i < items.length; i ++)
            {
                var lines = items[i].split(/\\n/g);
                incidents[i] = new Array();
                for (var j = 0; j < lines.length; j ++)
                {
                    var key = lines[j].replace(/<([^>]+)>.*/g,'$1');
                    if (key) key = trimString(key);
                    var pattern = new RegExp('<\\/?' + key + '>', 'g');
                    var value = lines[j].replace(pattern,'');
                    value = trimString(value);
                    incidents[i][key] = value;
                }
            }
            level ++;
            
            while ($('addactivityselect' + level).length > 1)
            {
                $('addactivityselect' + level).remove(0);
            }
            
            for (i = 1; i < incidents.length; i ++)
            {
                $('addactivitydescription' + level).innerHTML = incidents[i]['description'];
                $('addactivitydescription' + level).parentNode.parentNode.style.display = 'table-row';
                var incidentname = '" . $strIncident . " ' + incidents[i]['id'];
                $('newactivityalias').value = incidentname;               
             	hint = incidents[i]['title'];
                appendOption($('addactivityselect' + level), incidentname + ' - ' + hint, incidentname);
            }           
        }
        
        activityTypes['Support'] = activitySupport;
    
    </script>
";    
}
else
{
    header('Content-Type: text/xml');
    echo '<?xml version="1.0" ?>' . "\n";
    
    $descr[1] = 'You may choose an incident id:';

    $incidents = array();
    
    $ws = floor($ws / 1000);
    
    // Get all incidents that this user has touched
    $sql = 'SELECT DISTINCT incidents.id AS id, sites.name AS sitename, incidents.title as title ';
    $sql.= 'FROM incidents, sites, updates, maintenance ';
    $sql.= 'WHERE sites.id = maintenance.site AND updates.incidentid = incidents.id ';
    $sql.= 'AND maintenance.id = incidents.maintenanceid AND updates.userid = \'' . $sit[2] . "' ";
    $sql.= "AND updates.timestamp >= $ws AND updates.timestamp <= ($ws + 86400 * 7)";
    $res = mysql_query($sql);
    echo mysql_error() . "\n";
    while($inf = mysql_fetch_array($res))
    {
        echo "<item>\n";
        foreach ($inf as $key => $value)
        {
            if (!is_numeric($key)) echo "  <$key>$value</$key>\n";	
        }
        echo "  <description>{$descr[1]}</description>\n";
        echo "</item>\n";
    }
}
?>



