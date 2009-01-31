<?php
// incident_details.php - Show incident details
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// This file will soon be superceded by incident.php - 20Oct05 INL

@include ('set_include_path.inc.php');
$permission = 61; // View Incident Details
require ($lib_path.'db_connect.inc.php');
require ($lib_path.'functions.inc.php');

require_once ($lib_path . 'billing.inc.php');

// This page requires authentication
require ($lib_path.'auth.inc.php');

// External variables
$incidentid = cleanvar($_REQUEST['id']);
$id = $incidentid;

if ($_REQUEST['win'] == 'incomingview')
{
    $title = 'Incoming';
    $incidentid = '';
    include ('inc/incident_html_top.inc.php');
    include ('incident/incoming.inc.php');
}
elseif ($_REQUEST['win'] == 'jump')
{
    if (incident_owner($incidentid) > 0)
    {
        echo "<html><head>";
        echo "<script src='{$CONFIG['application_webpath']}scripts/prototype/prototype.js' type='text/javascript'></script>\n";
        echo "<script src='{$CONFIG['application_webpath']}scripts/webtrack.js' type='text/javascript'></script>\n";
        if (!empty($_GET['return']))
        {
            $return = cleanvar($_GET['return']);
            echo "</head><body onload=\"\"><a href=\"$return\">{$strPleaseWaitRedirect}</a>";
            echo "<script type='text/javascript'>\n//<![CDATA[\n";
            echo "var popwin = incident_details_window($incidentid,'win', true);\n";
            echo "if (!popwin) alert('Did your browser block the popup window?');\n";
            echo "else window.location='{$return}';\n";
            echo "\n//]]>\n</script>\n";
            echo "</body></html>";
        }
        else
        {
            // echo "</head><body onload=\"incident_details_window($incidentid,'win');window.location='{$_SERVER['HTTP_REFERER']}';\">{$strPleaseWaitRedirect}</body></html>";
            echo "</head><body onload=\"\"><a href=\"{$_SERVER['HTTP_REFERER']}\"{$strPleaseWaitRedirect}</a>";
            echo "<script type='text/javascript'>\n//<![CDATA[\n";
            echo "var popwin = incident_details_window($incidentid,'win', true);\n";
            echo "if (!popwin) alert('Did your browser block the popup window?');\n";
            echo "else window.location='{$_SERVER['HTTP_REFERER']}';\n";
            echo "\n//]]>\n</script>\n";
            echo "</body></html>";
        }
    }
    else
    {
        // return without loading popup
        echo "<html><head>";
        echo "<script src='{$CONFIG['application_webpath']}scripts/prototype/prototype.js' type='text/javascript'></script>\n";
        echo "<script src='{$CONFIG['application_webpath']}scripts/webtrack.js' type='text/javascript'></script>\n";
        if (!empty($_GET['return']))
        {
            $return = cleanvar($_GET['return']);
            echo "</head><body onload=\"incident_details_window($incidentid,'win');window.location='{$return}';\"></body></html>";
        }
        else
        {
            echo "</head><body onload=\"incident_details_window($incidentid,'win');window.location='{$_SERVER['HTTP_REFERER']}';\"></body></html>";
        }
    }
}
elseif ($_REQUEST['win'] == 'holdingview')
{
    $_REQUEST['win'] = 'incomingview';
    $title = $strIncoming;
    $incidentid='';
    include ('inc/incident_html_top.inc.php');

    include ('incident/details.inc.php');

    include ('incident/log.inc.php');
}

else
{
    $title = $strDetails;

    include ('inc/incident_html_top.inc.php');

    include ('incident/details.inc.php');

    include ('incident/log.inc.php');
}

include ('inc/incident_html_bottom.inc.php');
?>