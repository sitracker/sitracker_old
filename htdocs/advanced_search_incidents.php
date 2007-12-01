<?php
// advanced_search_incidents.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
// Removed mention of contactproducts - INL 08Oct01
// This Page Is Valid XHTML 1.0 Transitional!   - INL 6Apr06

$permission=6;  // view incidents

require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

// Don't return more than this number of results
$maxresults = 1000;

// External variables
$search_title = cleanvar($_REQUEST['search_title']);
$search_id = cleanvar($_REQUEST['search_id']);
$search_externalid = cleanvar($_REQUEST['search_externalid']);
$search_contact = cleanvar($_REQUEST['search_contact']);
$search_servicelevel = cleanvar($_REQUEST['search_servicelevel']);
$search_details = cleanvar($_REQUEST['search_details']);
$search_range = cleanvar($_REQUEST['search_range']);
$search_date = cleanvar($_REQUEST['search_date']);
$search_user = cleanvar($_REQUEST['search_user']);
$action = cleanvar($_REQUEST['action']);


include('htmlheader.inc.php');
// show search incidents form
if (empty($action))
{
    echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/search.png' width='32' height='32' alt='' /> ";
    echo "{$strSearch} ({$strAdvanced})</h2>";
    echo "<form action=\"{$_SERVER['PHP_SELF']}\" method='get'>";
    echo "<table class='vertical'>";
    echo "<tr><th colspan='2'>{$strIncidents}</th><tr>\n";
    echo "<tr><th>{$strTitle}:</th><td><input maxlength='100' name='search_title' size='30' type='text' /></td></tr>\n";
    echo "<tr><th>{$strIncident} ID:</th><td><input maxlength='100' name='search_id' size='30' type='text' /></td></tr>\n";
    echo "<tr><th>{$strExternalID}:</th><td><input maxlength='100' name='search_externalid' size='30' type='text' /></td></tr>\n";
    echo "<tr><th>{$strServiceLevel}:</th><td>".serviceleveltag_drop_down('search_servicelevel', 0, TRUE)."</td></tr>\n";
    echo "<tr><th>{$strContact}:</th><td><input maxlength='100' name='search_contact' size='30' type='text' /></td></tr>\n";
    echo "<tr><th>{$strPriority}:</th><td>".priority_drop_down('search_priority', 0)."</td></tr>\n";
    echo "<tr><th>{$strProduct}:</th><td>".product_drop_down('search_product', 0)."</td></tr>\n";
    echo "<tr><th>{$strDetails}:</th><td><input maxlength='100' name='search_details' size='30' type='text' /></td></tr>\n";
    echo "<tr><th>{$strStatus}<br />{$strOpen}/{$strClosed}:</th><td>";
    echo "<select size='1' name='search_range'>";
    echo "<option selected='selected' value='All'>{$strAll}</option>";
    echo "<option value='Open'>{$strAllOpen}</option>";
    echo "<option value='Closed'>{$strAllClosed}</option>";
    echo "</select>\n";
    echo "</td></tr>\n";
    echo "<tr><th>{$strLastUpdated}:</th><td width='300'>";
    // FIXME i18n date ranges
    echo "<select size='1' name='search_date'>
    <option selected='selected' value='All'>{$strAll}</option>
    <option value='Recent180'>Updated in past six months only</option>
    <option value='Recent90'>Updated in past three months only</option>
    <option value='Recent30'>Updated in past month only</option>
    <option value='Recent14'>Updated in past fortnight only</option>
    <option value='Recent7'>Updated in past week only</option>
    <option value='Recent1'>Updated today</option>
    <option value='RecentHour'>Updated this hour</option>
    <option value='OldHour'>Not updated in the past hour</option>
    <option value='Old7'>Not updated this week</option>
    <option value='Old30'>Not updated this month</option>
    <option value='Old90'>Not updated in the past three months</option>
    <option value='Old180'>Not updated in the past six months</option>
    </select>";
    echo "</td></tr>\n";
    echo "<tr><th>{$strOwner}:</th><td width='300'>";
    user_drop_down('search_user',0);
    echo "</td></tr>";
    echo "<tr><th>{$strSortResults}:</th><td width='300'>";
    echo "<select size='1' name='sort_results'>
    <option selected='selected' value='DateDESC'>By date, newest first</option>
    <option value='DateASC'>By Date, oldest first</option>
    <option value='IDASC'>By Incident ID</option>
    <option value='TitleASC'>By Title</option>
    <option value='ContactASC'>By Contact Name</option>
    <option value='SiteASC'>By Site Name</option>
    </select>"; // i18n
    echo "</td></tr>\n";
    echo "<tr><td></td><td><input type='hidden' name='action' value='search' />";
    echo "<input name='reset' type='reset' value=\"{$strReset}\" />&nbsp;";
    echo "<input name='submit' type='submit' value=\"{$strSearch}\" />";
    echo "</td></tr>\n";
    echo "</table>\n";
    echo "</form>\n";
}
else
{
    // perform search

    // search for criteria
    if ($errors == 0)
    {
        // build SQL
        $recent_sixmonth = time() - (180 * 86400);
        $recent_threemonth = time() - (90 * 86400);
        $recent_month = time() - (30 * 86400);
        $recent_fortnight = time() - (14 * 86400);
        $recent_week = time() - (7 * 86400);
        $recent_today = time() - (1 * 86400);
        $recent_hour = time() - (3600);

        if ($search_details =='') $sql = "SELECT DISTINCT incidents.id, externalid, title, priority, siteid, owner, type, forenames, surname, lastupdated, status, opened, servicelevel FROM incidents, contacts WHERE incidents.contact=contacts.id  ";
        if ($search_details !='')
        {
            //           $sql = "SELECT incidents.id, externalid, title, priority, site, owner, incidents.type, realname, lastupdated, status FROM incidents, contacts ";
            //           $sql.= "LEFT JOIN updates on updates.incidentid=incidents.id WHERE contact=contacts.id ";
            $sql = "SELECT DISTINCT incidents.id, updates.incidentid, incidents.externalid, incidents.title, incidents.priority, incidents.owner, incidents.type, incidents.lastupdated, incidents.status, contacts.forenames, contacts.surname, contacts.siteid, incidents.opened FROM updates, incidents, contacts WHERE updates.incidentid=incidents.id AND incidents.contact=contacts.id AND bodytext LIKE ('%$search_details%') ";
        }

        if ($search_title != '') $sql.= "AND title LIKE ('%$search_title%') ";
        if ($search_id != '') $sql.= "AND incidents.id LIKE ('%$search_id%') ";
        if ($search_externalid !='') $sql.= "AND externalid LIKE ('%$search_externalid%') ";
        if ($search_contact != '') $sql.= "AND (contacts.surname LIKE '%$search_contact%' OR forenames LIKE '%$search_contact%') ";
        if ($search_servicelevel != '') $sql.= "AND (incidents.servicelevel = '{$search_servicelevel}') ";
        if ($search_range == 'Closed') $sql.= "AND closed != '0' ";
        if ($search_range == 'Open') $sql.= "AND closed = '0' ";
        if ($search_date == 'Recent180') $sql.= "AND lastupdated >= '$recent_sixmonth' ";
        if ($search_date == 'Recent90') $sql.= "AND lastupdated >= '$recent_threemonth' ";
        if ($search_date == 'Recent30') $sql.= "AND lastupdated >= '$recent_month' ";
        if ($search_date == 'Recent14') $sql.= "AND lastupdated >= '$recent_fortnight' ";
        if ($search_date == 'Recent7') $sql.= "AND lastupdated >= '$recent_week' ";
        if ($search_date == 'Recent1') $sql.= "AND lastupdated >= '$recent_today' ";
        if ($search_date == 'RecentHour') $sql.= "AND lastupdated >= '$recent_hour' ";
        if ($search_date == 'Old180') $sql.= "AND lastupdated <= '$recent_sixmonth' ";
        if ($search_date == 'Old90') $sql.= "AND lastupdated <= '$recent_threemonth' ";
        if ($search_date == 'Old30') $sql.= "AND lastupdated <= '$recent_month' ";
        if ($search_date == 'Old7') $sql.= "AND lastupdated <= '$recent_week' ";
        if ($search_date == 'OldHour') $sql.= "AND lastupdated <= '$recent_hour' ";
        if ($search_user != 0) $sql.= "AND owner = '$search_user' ";
        if ($search_priority != 0) $sql.= "AND priority = '$search_priority' ";
        if ($search_product != 0) $sql.="AND product = '$search_product' ";

        // Sorting
        if ($sort_results == 'DateASC') $sql.="ORDER BY lastupdated ASC ";
        if ($sort_results == 'DateDESC') $sql.="ORDER BY lastupdated DESC ";
        if ($sort_results == 'IDASC') $sql.="ORDER BY incidents.id ASC ";
        if ($sort_results == 'TitleASC') $sql.="ORDER BY incidents.title ASC ";
        if ($sort_results == 'ContactASC') $sql.="ORDER BY contacts.surname ASC ";
        if ($sort_results == 'SiteASC') $sql.="ORDER BY contacts.siteid ASC ";

        //         if ($search_details !='') $sql.= "AND updates.bodytext = '$search_details' ";

        $sql .= "LIMIT {$maxresults}";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        $countresults=  mysql_num_rows($result);
        if ($countresults == 0)
        {
            echo "<h2>{$strNoResults}</h2>\n";
            echo "<p align='center'><a href=\"advanced_search_incidents.php\">{$strSearchAgain}</a></p>";
        }
        else
        {
            echo "<h2>".sprintf($strResultsNum, $countresults)."</h2>";
            echo "<table align='center'>
            <tr>
            <th>{$strID} (Ext ID)</th>
            <th>{$strTitle}</th>
            <th>{$strContact}</th>
            <th>{$strSite}</th>
            <th>{$strPriority}</th>
            <th>{$strOwner}</th>
            <th>{$strOpened}</th>
            <th>{$strLastUpdated}</th>
            <th>{$strType}</th>
            <th>{$strStatus}</th>
            </tr>";
            $shade = 0;
            while ($results = mysql_fetch_array($result))
            {
                // define class for table row shading
                if ($shade) $class = "shade1";
                else $class = "shade2";
                ?>
                <tr class='<?php echo $class; ?>'>
                <td align='center'  width='100'><?php echo $results["id"] ?> (<?php if ($results["externalid"] == "") echo "None"; else echo stripslashes($results["externalid"]) ?>)</td>
                <td width='150'><a href="javascript:incident_details_window('<?php echo $results["id"] ?>')"><?php echo $results["title"] ?></a></td>
                <td align='center' width='100'><?php echo stripslashes($results['forenames'].' '.$results['surname']); ?></td>
                <td align='center' width='100'><?php echo site_name($results['siteid']) ?></td>
                <td align='center' width='50'><?php echo $results['servicelevel']."<br />".priority_name($results["priority"]); ?></td>
                <td align='center' width='100'><?php echo user_realname($results['owner'],TRUE) ?></td>
                <td align='center' width='150'><?php echo date($CONFIG['dateformat_datetime'], $results["opened"]); ?></td>
                <td align='center' width='150'><?php echo date($CONFIG['dateformat_datetime'], $results["lastupdated"]); ?></td>
                <td align='center' width='50'><?php echo $results["type"] ?></td>
                <td align='center' width='50'><?php echo incidentstatus_name($results["status"]); ?></td>
                </tr>
                <?php
                // invert shade
                if ($shade == 1) $shade = 0;
                else $shade = 1;
            }
        }
        echo "</table>";
        echo "<br />";
        echo "<p align='center'><a href=\"advanced_search_incidents.php\">{$strSearchAgain}</a></p>";
        // FIXME v3.2x Replace maxresults limit with paging
        // FIXME i18n
        if ($countresults >= $maxresults) echo "<p class='info'>A maximum of {$maxresults} results are displayed, your search might have returned more.</p>";
    }
}
include('htmlfooter.inc.php');
?>