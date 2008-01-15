<?php
// portal.php - Simple customer interface
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Authors: Ivan Lucas <ivanlucas[at]users.sourceforge.net, Kieran Hogg <kieran_hogg[at]users.sourceforge.net>
// XHTML 1.0 Transitional valid 12/11/07 - KMH

@include('set_include_path.inc.php');
$permission=0; // not required
require('db_connect.inc.php');
require('functions.inc.php');
session_name($CONFIG['session_name']);
session_start();
// Load session language if it is set and different to the default language
if (!empty($_SESSION['lang']) AND $_SESSION['lang'] != $CONFIG['default_i18n'])
{
    include("i18n/{$_SESSION['lang']}.inc.php");
}
require('strings.inc.php');

if($CONFIG['portal'] == FALSE)
{
    // portal disabled
    $_SESSION['portalauth'] = FALSE;
    $page = $_SERVER['PHP_SELF'];
    if (!empty($_SERVER['QUERY_STRING'])) $page .= '?'.$_SERVER['QUERY_STRING'];
    $page = urlencode($page);
    header("Location: {$CONFIG['application_webpath']}index.php?id=2&page=$page");
    exit;
}

// Check session is authenticated, if not redirect to login page
if (!isset($_SESSION['portalauth']) OR $_SESSION['portalauth'] == FALSE)
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
    if (function_exists('session_regenerate_id'))
    {
        session_regenerate_id();
    }
    
    if (!version_compare(phpversion(),"4.3.3",">="))
    {
        setcookie(session_name(), session_id(),ini_get("session.cookie_lifetime"), "/");
    }
}

// External variables
$page = cleanvar($_REQUEST['page']);

$filter = array('page' => $page);

include('htmlheader.inc.php');

echo "<div id='menu'>\n";
echo "<ul id='menuList'>\n";
echo "<li><a href='logout.php'>{$strLogout}</a></li>";
echo "<li><a href='portal.php?page=entitlement'>{$strEntitlement}</a></li>";
echo "<li><a href='portal.php?page=incidents'>{$strIncidents}</a></li>";
echo "<li><a href='portal.php?page=details'>{$strDetails}</a></li>";
echo "</ul>";
echo "</div>";

switch ($page)
{
    //show the user's contracts
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
            echo colheader('actions', $strOperation);
            echo "</tr>";
            $shade = 'shade1';
            while ($contract = mysql_fetch_object($result))
            {
                echo "<tr class='$shade'><td>{$contract->id}</td><td>{$contract->name}</td>";
                echo "<td>";
                if ($contract->incident_quantity==0)
                {
                    echo "&#8734; {$strUnlimited}";
                }
                else
                {
                    echo "{$contract->availableincidents}";
                }
                echo "</td>";
                echo "<td>{$contract->incidents_used}</td>";
                echo "<td>".date($CONFIG['dateformat_date'],$contract->expirydate)."</td>";
                echo "<td><a href='$_SERVER[PHP_SELF]?page=add&amp;contractid={$contract->id}'>{$strAddIncident}</a></td></tr>\n";
                if ($shade == 'shade1') $shade = 'shade2';
                else $shade = 'shade1';
            }
            echo "</table>";
        }
        else
        {
            echo "<p class='info'>{$strNone}</p>";
        }
    break;

    //show their open incidents
    case 'incidents':
        $showclosed = $_REQUEST['showclosed'];
        if(empty($showclosed)) $showclosed = "false";

        if($showclosed == "true")
        {
            echo "<h2>{$strYourClosedIncidents}</h2>";
            echo "<p align='center'><a href='$_SERVER[PHP_SELF]?page=incidents&amp;showclosed=false'>{$strShowOpenIncidents}</a></p>";
            $sql = "SELECT * FROM incidents WHERE status = 2 AND contact = '{$_SESSION['contactid']}'";
        }
        else
        {
            echo "<h2>{$strYourCurrentOpenIncidents}</h2>";
            echo "<p align='center'><a href='$_SERVER[PHP_SELF]?page=incidents&amp;showclosed=true'>{$strShowClosedIncidents}</a></p>";
            $sql = "SELECT * FROM incidents WHERE status != 2 AND contact = '{$_SESSION['contactid']}'";
        }
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
            if($showclosed == "false")
            {
                echo colheader('actions', $strOperation);
            }
            
            echo "</tr>\n";
            while ($incident = mysql_fetch_object($result))
            {
                echo "<tr class='$shade'><td><a href='portal.php?page=showincident&amp;id={$incident->id}'>{$incident->id}</a></td>";
                echo "<td>";
                if (!empty($incident->softwareid))
                {
                    echo software_name($incident->softwareid)."<br />";
                }
                
                echo "<strong><a href='portal.php?page=showincident&amp;id={$incident->id}'>{$incident->title}</a></strong></td>";
                echo "<td>".format_date_friendly($incident->lastupdated)."</td>";
                echo "<td>".incidentstatus_name($incident->status)."</td>";

                if ($showclosed == "false")
                {
                    echo "<td><a href='{$_SERVER[PHP_SELF]}?page=update&amp;id={$incident->id}'>{$strUpdate}</a> | ";

                    //check if the customer has requested a closure
                    $lastupdate = list($update_userid, $update_type, $update_currentowner, $update_currentstatus, $update_body, $update_timestamp, $update_nextaction, $update_id)=incident_lastupdate($incident->id);

                    if($lastupdate[1] == "customerclosurerequest")
                    {
                        echo "{$strClosureRequested}</td>";
                    }
                    else
                    {
                        echo "<a href='{$_SERVER[PHP_SELF]}?page=close&amp;id={$incident->id}'>{$strRequestClosure}</a></td>";
                    }
                }
                echo "</tr>";
                if ($shade == 'shade1') $shade = 'shade2';
                else $shade = 'shade1';
            }
            echo "</table>";
        }
        else echo "<p class='info'>{$strNoIncidents}</p>";

        echo "<p align='center'><a href='{$_SERVER[PHP_SELF]}?page=entitlement'>{$strAddIncident}</a></p>";
    break;

    //update an open incident
    case 'update':
        if(empty($_REQUEST['update']))
        {
            $id = $_REQUEST['id'];
            echo "<h2>{$strUpdateIncident} {$_REQUEST['id']}</h2>";
            echo "<div id='update' align='center'><form action='{$_SERVER[PHP_SELF]}?page=update&amp;id=$id' method='post'>";
            echo "<p>{$strUpdate}:</p><textarea cols='50' rows='10' name='update'></textarea><br />";
            echo "<input type='submit' value=\"{$strSave}\"/></form></div>";
        }
        else
        {
            $usersql = "SELECT forenames, surname FROM contacts WHERE id={$_SESSION['contactid']}";
            $result = mysql_query($usersql);
            $user = mysql_fetch_object($result);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

            //add the update
            $update = "Updated via the portal by <b>{$user->forenames} {$user->surname}</b>\n\n";
            $update .= $_REQUEST['update'];
            $sql = "INSERT INTO updates (incidentid, userid, type, currentstatus, bodytext, timestamp, customervisibility) ";
            $sql .= "VALUES('{$_REQUEST['id']}', '0', 'webupdate', '1', '{$update}', '{$now}', 'show')";
            mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

            //set incident back to active
            $sql = "UPDATE incidents SET status=1, lastupdated=$now WHERE id={$_REQUEST['id']}";
            mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);


            html_redirect("portal.php?page=incidents");
        }
        break;

    //close an open incident
    case 'close':
        if(empty($_REQUEST['reason']))
        {
            $id = $_REQUEST['id'];
            echo "<h2>{$strClosureRequestForIncident} {$_REQUEST['id']}</h2>";
            echo "<div id='update' align='center'><form action='{$_SERVER[PHP_SELF]}?page=close&amp;id=$id' method='POST'>";
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
            $sql = "INSERT into updates (incidentid, userid, type, currentstatus, bodytext, timestamp, customervisibility) ";
            $sql .= "VALUES('{$_REQUEST['id']}', '0', 'customerclosurerequest',  '1', '{$reason}',
            '{$now}', 'show')";
            mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

            //set incident back to active
            $sql = "UPDATE incidents SET status=1, lastupdated=$now WHERE id={$_REQUEST['id']}";
            mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);


            html_redirect("portal.php?page=incidents");
        }
        break;

    //add a new incident
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
            $servicelevel = servicelevel_id2tag(maintenance_servicelevel($contractid));

            $updatetext = "Opened via the portal by <b>".contact_realname($contactid)."</b>\n\n";
            if (!empty($probdesc))
            {
                $updatetext .= "<b>Problem Description</b>\n{$probdesc}\n\n";
            }
            
            if (!empty($workarounds))
            {
                $updatetext .= "<b>Workarounds Attempted</b>\n{$workarounds}\n\n";
            }
            
            if (!empty($reproduction))
            {
                $updatetext .= "<b>Problem Reproduction</b>\n{$reproduction}\n\n";
            }
            
            if (!empty($impact))
            {
                $updatetext .= "<b>Customer Impact</b>\n{$impact}\n\n";
            }

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
                $servicelevel = maintenance_servicelevel($contractid);
                $sql = "SELECT * FROM servicelevels WHERE id='$servicelevel' AND priority='$priority' ";
            }
            else $sql = "SELECT * FROM servicelevels WHERE tag='$servicelevel' AND priority='$priority' ";

            $result = mysql_query($sql);
            if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            $level = mysql_fetch_object($result);

            $targetval = $level->initial_response_mins * 60;
            $initialresponse = $now + $targetval;

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

            html_redirect("portal.php?page=incidents", TRUE, $strIncidentAdded);
            exit;

        }
        break;

    //show user's details
    case 'details':
        //if new details posted
        if (cleanvar($_REQUEST['action']) == 'update')
        {
            $forenames = cleanvar($_REQUEST['forenames']);
            $surname = cleanvar($_REQUEST['surname']);
            $department = cleanvar($_REQUEST['department']);
            $address1 = cleanvar($_REQUEST['address1']);
            $address2 = cleanvar($_REQUEST['address2']);
            $county = cleanvar($_REQUEST['county']);
            $country = cleanvar($_REQUEST['country']);
            $postcode = cleanvar($_REQUEST['postcode']);
            $phone = cleanvar($_REQUEST['phone']);
            $fax = cleanvar($_REQUEST['fax']);
            $email = cleanvar($_REQUEST['email']);
            $errors = 0;

            // VALIDATION CHECKS */

            if ($surname == '')
            {
                $errors = 1;
                echo "<p class='error'>You must enter a surname</p>\n";
            }
            
            if ($email == "" OR $email=='none' OR $email=='n/a')
            {
                $errors = 1;
                echo "<p class='error'>You must enter an email address</p>\n";
            }
            
            if ($errors == 0)
            {
                $updatesql = "UPDATE contacts SET forenames='$forenames', surname='$surname', department='$department', address1='$address1', address2='$address2', county='$county', country='$country', postcode='$postcode', phone='$phone', fax='$fax', email='$email' ";
                $updatesql .= "WHERE id='{$_SESSION['contactid']}'";
                mysql_query($updatesql);
                if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
            }
        }

        echo "<h2>{$strYourDetails}</h2>";
        $sql = "SELECT contacts.forenames, contacts.surname, contacts.department, contacts.address1, contacts.address2, contacts.county, contacts.country, contacts.postcode, contacts.phone, contacts.fax, contacts.email ";
        $sql .= "FROM contacts, sites ";
        $sql .= "WHERE contacts.siteid=sites.id ";
        $sql .= "AND contacts.id={$_SESSION['contactid']}";
        $query = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        $user = mysql_fetch_object($query);

        echo "<form action='$_SERVER[PHP_SELF]?page=details&amp;action=update' method='post'>";
        echo "<table align='center' class='vertical'>";
        echo "<tr><th colspan='2'><h3><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/contact.png' width='32' height='32' alt='' /> {$user->forenames} {$user->surname}</h3></th></tr>\n";
        echo "<tr><th>{$strForenames}: </th><td><input name='forenames' value='{$user->forenames}' /></td></tr>";
        echo "<tr><th>{$strSurname}: </th><td><input name='surname' value='{$user->surname}' /></td></tr>";
        echo "<tr><th>{$strDepartment}: </th><td><input name='department' value='{$user->department}' /></td></tr>";
        echo "<tr><th>{$strAddress1}: </th><td><input name='address1' value='{$user->address1}' /></td></tr>";
        echo "<tr><th>{$strAddress2}: </th><td><input name='address2' value='{$user->address2}' /></td></tr>";
        echo "<tr><th>{$strCounty}: </th><td><input name='county' value='{$user->county}' /></td></tr>";
        echo "<tr><th>{$strCountry}: </th><td><input name='country' value='{$user->country}' /></td></tr>";
        echo "<tr><th>{$strPostcode}: </th><td><input name='postcode' value='{$user->postcode}' /></td></tr>";
        echo "<tr><th>{$strTelephone}: </th><td><input name='phone' value='{$user->phone}' /></td></tr>";
        echo "<tr><th>{$strFax}: </th><td><input name='fax' value='{$user->fax}' /></td></tr>";
        echo "<tr><th>{$strEmail}: </th><td><input name='email' value='{$user->email}' /></td></tr>";
        echo "</table>";
        echo "<p align='center'><input type='submit' value='{$strUpdate}' /></p></form>";
        break;

    //show specified incident
    case 'showincident':
        $incidentid = $_REQUEST['id'];
        $sql = "SELECT title, contact, status FROM incidents WHERE id={$incidentid}";
        $result = mysql_query($sql);
        $user = mysql_fetch_object($result);

        echo "<h2>Details: {$incidentid} - {$user->title}</h2>"; // FIXME i18n

        if ($user->status != 2)
        {
            echo "<p align='center'><a href='{$_SERVER[PHP_SELF]}?page=update&amp;id={$incidentid}'>{$strUpdate}</a> | ";

            //check if the customer has requested a closure
            $lastupdate = list($update_userid, $update_type, $update_currentowner, $update_currentstatus, $update_body, $update_timestamp, $update_nextaction, $update_id)=incident_lastupdate($incidentid);

            if($lastupdate[1] == "customerclosurerequest")
            {
                echo "{$strClosureRequested}</td>";
            }
            else
            {
                echo "<a href='{$_SERVER[PHP_SELF]}?page=close&amp;id={$incidentid}'>{$strRequestClosure}</a></p>";
            }
        }

        /*
        //check if this user owns the incident
        if($user->contact != $_SESSION['contactid'])
        {
            echo "<p align='center'>$strNoPermission.</p>";
            include('htmlfooter.inc.php');
            exit;
        }*/

        $records = strtolower(cleanvar($_REQUEST['records']));

        if ($incidentid=='' OR $incidentid < 1)
        {
            trigger_error("Incident ID cannot be zero or blank", E_USER_ERROR);
        }

        $sql  = "SELECT * FROM updates WHERE incidentid='{$incidentid}' AND customervisibility='show' ";
        $sql .= "ORDER BY timestamp DESC, id DESC";
        
        if ($offset > 0)
        {
            if (empty($records))
            {
                $sql .= "LIMIT {$offset},{$_SESSION['num_update_view']}";
            }
            elseif (is_numeric($records))
            {
                $sql .= "LIMIT {$offset},{$records}";
            }
        }
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error $sql".mysql_error(), E_USER_ERROR);

        $keeptags = array('b','i','u','hr','&lt;', '&gt;');
        foreach ($keeptags AS $keeptag)
        {
            if (substr($keeptag,0,1)=='&')
            {
                $origtag[] = $keeptag;
                $temptag[] = "[[".substr($keeptag, 1, strlen($keeptag)-1)."]]";
                $origtag[] = strtoupper("$keeptag");
                $temptag[] = "[[".strtoupper(substr($keeptag, 1, strlen($keeptag)-1))."]]";
            }
            else
            {
                $origtag[] = "<{$keeptag}>";
                $origtag[] = "</{$keeptag}>";
                $origtag[] = "<'.strtoupper($keeptag).'>";
                $origtag[] = "</'.strtoupper($keeptag).'>";
                $temptag[] = "[[{$keeptag}]]";
                $temptag[] = "[[/{$keeptag}]]";
                $temptag[] = "[['.strtoupper($keeptag).']]";
                $temptag[] = "[[/'.strtoupper($keeptag).']]";
            }
        }

        while ($update = mysql_fetch_object($result))
        {
            if (empty($firstid))
            {
                $firstid = $update->id;
            }
            
            $updateid = $update->id;
            $updatebody=trim($update->bodytext);

            //remove empty updates
            if (!empty($updatebody) AND $updatebody != "<hr>")
            {
                $updatebodylen = strlen($updatebody);

                $updatebody = str_replace($origtag, $temptag, $updatebody);
                // $updatebody = htmlspecialchars($updatebody);
                $updatebody = str_replace($temptag, $origtag, $updatebody);

                // Put the header part (up to the <hr /> in a seperate DIV)
                if (strpos($updatebody, '<hr>') !== FALSE)
                {
                    $updatebody = "<div class='iheader'>".str_replace('<hr>',"</div>",$updatebody);
                }
                // Style quoted text
                // $quote[0]="/^(&gt;\s.*)\W$/m";
                // $quote[0]="/^(&gt;[\s]*.*)[\W]$/m";
                $quote[0] = "/^(&gt;([\s][\d\w]).*)[\n\r]$/m";
                $quote[1] = "/^(&gt;&gt;([\s][\d\w]).*)[\n\r]$/m";
                $quote[2] = "/^(&gt;&gt;&gt;+([\s][\d\w]).*)[\n\r]$/m";
                $quote[3] = "/^(&gt;&gt;&gt;(&gt;)+([\s][\d\w]).*)[\n\r]$/m";

                //$quote[3]="/(--\s?\s.+-{8,})/U";  // Sigs
                        $quote[4]="/(-----\s?Original Message\s?-----.*-{3,})/s";
                $quote[5] = "/(-----BEGIN PGP SIGNED MESSAGE-----)/s";
                $quote[6] = "/(-----BEGIN PGP SIGNATURE-----.*-----END PGP SIGNATURE-----)/s";
                $quote[7] = "/^(&gt;)[\r]*$/m";
                $quote[8] = "/^(&gt;&gt;)[\r]*$/m";
                $quote[9] = "/^(&gt;&gt;(&gt;){1,8})[\r]*$/m";

                $quotereplace[0] = "<span class='quote1'>\\1</span>";
                $quotereplace[1] = "<span class='quote2'>\\1</span>";
                $quotereplace[2] = "<span class='quote3'>\\1</span>";
                $quotereplace[3] = "<span class='quote4'>\\1</span>";
                //$quotereplace[3]="<span class='sig'>\\1</span>";
                $quotereplace[4] = "<span class='quoteirrel'>\\1</span>";
                $quotereplace[5] = "<span class='quoteirrel'>\\1</span>";
                $quotereplace[6] = "<span class='quoteirrel'>\\1</span>";
                $quotereplace[7] = "<span class='quote1'>\\1</span>";
                $quotereplace[8] = "<span class='quote2'>\\1</span>";
                $quotereplace[9] = "<span class='quote3'>\\1</span>";

                $updatebody = preg_replace($quote, $quotereplace, $updatebody);

                $updatebody = bbcode($updatebody);

                //$updatebody = emotion($updatebody);

                //"!(http:/{2}[\w\.]{2,}[/\w\-\.\?\&\=\#]*)!e"
                // [\n\t ]+
                $updatebody = preg_replace("!([\n\t ]+)(http[s]?:/{2}[\w\.]{2,}[/\w\-\.\?\&\=\#\$\%|;|\[|\]~:]*)!e", "'\\1<a href=\"\\2\" title=\"\\2\">'.(strlen('\\2')>=70 ? substr('\\2',0,70).'...':'\\2').'</a>'", $updatebody);


                // Lookup some extra data
                $updateuser = user_realname($update->userid,TRUE);
                $updatetime = readable_date($update->timestamp);
                $currentowner = user_realname($update->currentowner,TRUE);
                $currentstatus = incident_status($update->currentstatus);

                echo "<div class='detailhead' align='center'>";
                //show update type icon
                if (array_key_exists($update->type, $updatetypes))
                {
                    if (!empty($update->sla) AND $update->type=='slamet')
                    {
                        echo "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/{$slatypes[$update->sla]['icon']}' width='16' height='16' alt='{$update->type}' />";
                    }
                    echo "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/{$updatetypes[$update->type]['icon']}' width='16' height='16' alt='{$update->type}' />";
                }
                else
                {
                    echo "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/{$updatetypes['research']['icon']}' width='16' height='16' alt='Research' />";
                    echo "<span>Click to {$newmode}</span></a> ";
                    if($update->sla != '')
                    {
                        echo "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/{$slatypes[$update->sla]['icon']}' width='16' height='16' alt='{$update->type}' />";
                    }
                }
                echo " {$updatetime}</div>";
                echo "</div>\n";
                if ($updatebody!='')
                {
                    if ($update->customervisibility == 'show')
                    {
                        echo "<div class='detailentry'>\n";
                    }
                    else
                    {
                        echo "<div class='detailentryhidden'>\n";
                    }
                    
                    if ($updatebodylen > 5)
                    {
                        echo nl2br($updatebody);
                    }
                    else
                    {
                        echo $updatebody;
                    }
                    echo "</div>\n"; // detailentry
                }
            }
        }
        break;


    case '':
    default:
        echo "<p align='center'>{$strWelcome} ".contact_realname($_SESSION['contactid'])."</p>";
}

include('htmlfooter.inc.php');

?>
