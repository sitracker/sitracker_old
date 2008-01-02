<?php
// incident_details.php - Show incident details
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// This file will soon be superceded by incident.php - 20Oct05 INL

@include ('set_include_path.inc.php');
$permission = 61; // View Incident Details
require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

// External variables
$incidentid = cleanvar($_REQUEST['id']);
$id = $incidentid;

?>

<?php
if ($_REQUEST['win']=='incomingview')
{
    $title = 'Incoming';
    $incidentid = '';
    include ('incident_html_top.inc.php');
    include ('incident/incoming.inc.php');
}
elseif ($_REQUEST['win']=='jump')
{
    if (incident_owner($incidentid) > 0)
    {
        echo "<html><head>";
        echo "<script src='{$CONFIG['application_webpath']}scripts/prototype/prototype.js' type='text/javascript'></script>\n";
        echo "<script src='{$CONFIG['application_webpath']}webtrack.js' type='text/javascript'></script>\n";
        echo "</head><body onload=\"incident_details_window($incidentid,'win');window.location='{$_SERVER['HTTP_REFERER']}';\"></body></html>";
    }
    else
    {
        // return without loading popup
        echo "<html><head>";
        echo "<script src='{$CONFIG['application_webpath']}scripts/prototype/prototype.js' type='text/javascript'></script>\n";
        echo "<script src='{$CONFIG['application_webpath']}webtrack.js' type='text/javascript'></script>\n";
        echo "</head><body onload=\"window.location='{$_SERVER['HTTP_REFERER']}';\"></body></html>";
    }
}
elseif ($_REQUEST['win'] == 'holdingview')
{
    $_REQUEST['win'] = 'incomingview';
    $title='Incoming';
    $incidentid='';
    include('incident_html_top.inc.php');
    include('incident/details.inc.php');

    include('incident/log.inc.php');
}

else
{
    $title='Details';
    include ('incident_html_top.inc.php');

    include ('incident/details.inc.php');

    include ('incident/log.inc.php');
}

include ('incident_html_bottom.inc.php');
?>
