<?php
// portal.php - Simple customer interface
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Kieran Hoogg <kieran_hogg[at]users.sourceforge.net>

$permission=0; // not required
require('db_connect.inc.php');
require('functions.inc.php');

session_name($CONFIG['session_name']);
session_start();

/*if($CONFIG['portal'] == FALSE)
{
    // portal disabled
    $_SESSION['portalauth'] = FALSE;
    $page = $_SERVER['PHP_SELF'];
    if (!empty($_SERVER['QUERY_STRING'])) $page .= '?'.$_SERVER['QUERY_STRING'];
    $page = urlencode($page);
    header("Location: {$CONFIG['application_webpath']}index.php?id=2&page=$page");
    exit;
}*/

// Check session is authenticated, if not redirect to login page
/*if (!isset($_SESSION['portalauth']) OR $_SESSION['portalauth'] == FALSE)
{
    $_SESSION['portalauth'] = FALSE;
    // Invalid user
    $page = $_SERVER['PHP_SELF'];
    if (!empty($_SERVER['QUERY_STRING'])) $page .= '?'.$_SERVER['QUERY_STRING'];
    $page = urlencode($page);
    header("Location: {$CONFIG['application_webpath']}index.php?id=2&page=$page");
    exit;
}
else
{
    // Attempt to prevent session fixation attacks
    session_regenerate_id();

}*/
// External variables
$page = cleanvar($_REQUEST['page']);

$filter=array('page' => $page);

include('htmlheader.inc.php');

echo "<div id='menu'>\n";
echo "<ul id='menuList'>\n";
echo "<li><a href='logout.php'>{$strLogout}</a></li>";
echo "<li><a href='portal.php?page=entitlement'>{$strEntitlement}</a></li>";
echo "<li><a href='portal.php?page=incidents'>{$strIncidents}</a></li>";
echo "</ul>";
echo "</div>";

switch ($page)
{
    case 'entitlement':
        echo "<h2>{$strYourSupportEntitlement}</h2>";
        $sql = "SELECT maintenance.*, products.*, ";
        $sql .= "(maintenance.incident_quantity - maintenance.incidents_used) AS availableincidents ";
        $sql .= "FROM supportcontacts, maintenance, products ";
        $sql .= "WHERE supportcontacts.maintenanceid=maintenance.id ";
        $sql .= "AND maintenance.product=products.id ";
        $sql .= "AND supportcontacts.contactid='{$_SESSION['contactid']}'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        $numcontracts = mysql_num_rows($result);
        if ($numcontracts >= 1)
        {
            echo "<table align='center'>";
            echo "<tr>";
            echo colheader('id',$strContractID);
            echo colheader('name',$strProduct);
            echo colheader('availableincidents',$strIncidentsAvailable);
            echo colheader('usedincidents',$strIncidentsUsed);
            echo colheader('expirydate', $strExpiryDate);
            echo colheader('actions', $strActions);
            echo "</tr>";
            $shade='shade1';
            while ($contract = mysql_fetch_object($result))
            {
                echo "<tr class='$shade'><td>{$contract->id}</td><td>{$contract->name}</td>";
                echo "<td>";
                if ($contract->incident_quantity==0) echo "&#8734; Unlimited";
                else echo "{$contract->availableincidents}";
                echo "</td>";
                echo "<td>{$contract->incidents_used}</td>";
                echo "<td>".date($CONFIG['dateformat_date'],$contract->expirydate)."</td>";
                echo "<td><a href='$_SERVER[PHP_SELF]?page=add&contractid={$contract->id}'>Add Incident</a></td></tr>\n";
                if ($shade=='shade1') $shade='shade2';
                else $shade='shade1';
            }
            echo "</table>";
        }
        else
        {
            echo "<p class='info'>No contracts</p>";
        }
    break;

    case 'incidents':
        echo "<h2>{$strYourCurrentIncidents}</h2>";
        $sql = "SELECT * FROM incidents WHERE status!=2 AND contact = '{$_SESSION['contactid']}'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        $numincidents = mysql_num_rows($result);
        if ($numincidents >= 1)
        {
            $shade='shade1';
            echo "<table align='center'>";
            echo "<tr>";

            echo colheader('id', $strID, $sort, $order, $filter);
            echo colheader('title',$strTitle);
            echo colheader('lastupdated',$strLastUpdated);
            echo colheader('status',$strStatus);
            echo colheader('actions', $strActions);
            while ($incident = mysql_fetch_object($result))
            {
                echo "<tr class='$shade'><td>{$incident->id}</td>";
                echo "<td>Product<br /><strong>{$incident->title}</strong></td>";
                echo "<td>".format_date_friendly($incident->lastupdated)."</td>";
                echo "<td>".incidentstatus_name($incident->status)."</td>";
                echo "<td><a href='{$_SERVER[PHP_SELF]}?page=update&id={$incident->id}'>Update</a> | ";
                echo "<a href='{$_SERVER[PHP_SELF]}?page=close&id={$incident->id}'>Request Close</a></td>";
                echo "</tr>";
                if ($shade=='shade1') $shade='shade2';
                else $shade='shade1';
            }
            echo "</table>";
        }
        else echo "<p class='info'>{$strNoIncidents}</p>";
        
        echo "<p align='center'><a href='{$_SERVER[PHP_SELF]}?page=entitlement'>{$strAddIncident}</a></p>";
    break;

    case 'update':
        if(empty($_REQUEST['update']))
        {
            $id = $_REQUEST['id'];
            echo "<h2>{$strUpdateIncident} {$_REQUEST['id']}</h2>";
            echo "<div id='update' align='center'><form action='{$_SERVER[PHP_SELF]}?page=update&id=$id' method='POST'>";
            echo "<p>Update:</p><textarea cols='50' rows='10' name='update'></textarea><br />";
            echo "<input type='submit'></form></div>";
        }
        else
        {
            $usersql = "SELECT forenames, surname FROM contacts WHERE id={$_SESSION['contactid']}";
            $result = mysql_query($usersql);
            $user = mysql_fetch_object($result);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            
            $update = "Updated via the portal by <b>{$user->forenames} {$user->surname}</b>\n\n";
            $update .= $_REQUEST['update'];
            $sql = "INSERT into updates VALUES('', '{$_REQUEST['id']}', '{$_SESSION['contactid']}', 'webupdate', '', '1', '{$update}',
                    '{$now}', '', '', '', '', '', '', '')";
            mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            
            confirmation_page("2", "portal.php?page=incidents", "<h2>Update Successful</h2><p align='center'>Please wait while you are redirected...</p>");
        }
        break;
        
    case 'close':
        if(empty($_REQUEST['reason']))
        {
            $id = $_REQUEST['id'];
            echo "<h2>{$strClosureRequestForIncident} {$_REQUEST['id']}</h2>";
            echo "<div id='update' align='center'><form action='{$_SERVER[PHP_SELF]}?page=close&id=$id' method='POST'>";
            echo "<p>Reason:</p><textarea name='reason' cols='50' rows='10'></textarea><br />";
            echo "<input type='submit'></form></div>";
        }
        else
        {
            $usersql = "SELECT forenames, surname FROM contacts WHERE id={$_SESSION['contactid']}";
            $result = mysql_query($usersql);
            $user = mysql_fetch_object($result);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            
            $reason = "Incident closure requested via the portal by <b>{$user->forenames} {$user->surname}</b>\n\n";
            $reason .= "<b>Reason:</b> {$_REQUEST['reason']}";
            $sql = "INSERT into updates VALUES('', '{$_REQUEST['id']}', '{$_SESSION['contactid']}', 'webupdate', '', '1', '{$reason}',
            '{$now}', '', '', '', '', '', '', '')";
            mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            
            confirmation_page("2", "portal.php?page=incidents", "<h2>Closure request Successful</h2><p align='center'>Please wait while you are redirected...</p>");
        }
        break;
        
    case 'add':
        if(!$_REQUEST['action'])
        {
            echo "<h2>{$strAddIncident}</h2>";
            echo "<table align='center' width='50%' class='vertical'>";
            echo "<form action='{$_SERVER[PHP_SELF]}?page=add&action=submit' method='post'>";
            echo "<tr><th>{$strSoftware}:</th><td>".softwareproduct_drop_down('software', 1, $_REQUEST['contractid'])."</td></tr>";
            echo "<tr><th>{$strSoftwareVersion}:<t/h><td><input maxlength='100' name='version' size=40 type='text' /></td></tr>";
            echo "<tr><th>{$strServicePacksApplied}:</th><td><input maxlength='100' name='productservicepacks' size=40 type='text' /></td></tr>";
            echo "<tr><th>{$strIncidentTitle}:</th><td><input maxlength='100' name='title' size=40 type='text' /></td></tr>";
            echo "<tr><th>{$strProblemDescription}:<br />{$strProblemDescriptionCustomerText}</th><td><textarea name='probdesc' rows='10' cols='60'></textarea></td></tr>";
            echo "<tr><th>{$strWorkAroundsAttempted}:<br />{$strWorkAroundsAttemptedCustomerText}</th><td><textarea name='workarounds' rows='10' cols='60'></textarea></td></tr>";
            echo "<tr><th>{$strProblemReproduction}:<br />{$strProblemReproductionCustomerText}</th><td><textarea name='reproduction' rows='10' cols='60'></textarea></td></tr>";
            echo "<tr><th>$strCustomerImpact:<br />{$strCustomerImpactCustomerText}</th><td><textarea name='impact' rows='10' cols='60'></textarea></td></tr>";
    
            echo "</table>";
            echo "<input name='contractid' value='{$_REQUEST['contractid']}' type='hidden'>";
            echo "<p align='center'><input type='submit' value='{$strAddIncident}' /></p>";
            echo "</form>";
        }
        else //submit
        {
            print_r($_REQUEST);
            $contactid = $_SESSION['contactid'];
            $contractid = cleanvar($_REQUEST['contractid']);
            $software = cleanvar($_REQUEST['software']);
            $softwareversion = cleanvar($_REQUEST['version']);
            $softwareservicepacks = cleanvar($_REQUEST['productservicepacks']);
            $incidenttitle = cleanvar($_REQUEST['title']);
            $probdesc = cleanvar($_REQUEST['probdesc']);
            $workarounds = cleanvar($_REQUEST['workarounds']);
            $reproduction = cleanvar($_REQUEST['reproduction']);
            $impact = cleanvar($_REQUEST['impact']);
            $servicelevel = maintenance_servicelevel($contractid);
                        
            $updatetext = "Opened via the portal by <b>".contact_realname($contactid)."</b>\n\n";
            if(!empty($probdesc)) $updatetext .= "<b>Problem Description</b>\n{$probdesc}\n\n";
            if(!empty($workarounds))  $updatetext .= "<b>Workarounds Attempted</b>\n{$workarounds}\n\n";
            if(!empty($reproduction)) $updatetext .= "<b>Problem Reproduction</b>\n{$reproduction}\n\n";
            if(!empty($impact)) $updatetext .= "<b>Customer Impact</b>\n{$impact}\n\n";
            
            //create new incident
            $sql  = "INSERT INTO incidents (title, owner, contact, priority, servicelevel, status, type, maintenanceid, ";
            $sql .= "product, softwareid, productversion, productservicepacks, opened, lastupdated) ";
            $sql .= "VALUES ('$incidenttitle', '0', '$contactid', '1', '$servicelevel', '1', 'Support', '', ";
            $sql .= "'$contractid', '$software', '$softwareversion', '$softwareservicepacks', '$now', '$now')";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            
            $incidentid = mysql_insert_id();
            $_SESSION['incidentid'] = $incidentid;
            
            // Create a new update
            $sql  = "INSERT INTO updates (incidentid, userid, type, bodytext, timestamp, currentowner, ";
            $sql .= "currentstatus, customervisibility) ";
            $sql .= "VALUES ('$incidentid', '0', 'opening', '$updatetext', '$now', '', ";
            $sql .= "'1', 'show')";
            $result = mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            
            // get the service level
            // find out when the initial response should be according to the service level
            if (empty($servicelevel) OR $servicelevel==0)
            {
                // FIXME: for now we use id but in future use tag, once maintenance uses tag
                $servicelevel=maintenance_servicelevel($contractid);
                $sql = "SELECT * FROM servicelevels WHERE id='$servicelevel' AND priority='$priority' ";
            }
            else $sql = "SELECT * FROM servicelevels WHERE tag='$servicelevel' AND priority='$priority' ";

            $result = mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            $level = mysql_fetch_object($result);

            $targetval = $level->initial_response_mins * 60;
            $initialresponse=$now + $targetval;

            // Insert the first SLA update, this indicates the start of an incident
            // This insert could possibly be merged with another of the 'updates' records, but for now we keep it seperate for clarity
            $sql  = "INSERT INTO updates (incidentid, userid, type, timestamp, currentowner, currentstatus, customervisibility, sla, bodytext) ";
            $sql .= "VALUES ('$incidentid', '0', 'slamet', '$now', '0', '1', 'hide', 'opened','The incident is open and awaiting action.')";
            mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

            // Insert the first Review update, this indicates the review period of an incident has started
            // This insert could possibly be merged with another of the 'updates' records, but for now we keep it seperate for clarity
            $sql  = "INSERT INTO updates (incidentid, userid, type, timestamp, currentowner, currentstatus, customervisibility, sla, bodytext) ";
            $sql .= "VALUES ('$incidentid', '0', 'reviewmet', '$now', '0', '1', 'hide', 'opened','')";
            mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

            plugin_do('incident_created');

            //TODO
            /*
            // Decrement free support, where appropriate
           if ($type=='free')
            {
                decrement_free_incidents(contact_siteid($contactid));
                plugin_do('incident_created_site');
            }
            else
            {
                // decrement contract incident by incrementing the number of incidents used
                increment_incidents_used($contractid);
                plugin_do('incident_created_contract');
            }*/

 
            
        }
        break;
    
    case '':
    default:
        echo "<p align='center'>{$strWelcome} ".contact_realname($_SESSION['contactid'])."</p>";
}

include('htmlfooter.inc.php');

?>
