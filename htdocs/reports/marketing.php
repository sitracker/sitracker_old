<?php
// marketing.php - Print/Export a list of contacts by product
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

$permission=37; // Run Reports

require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

// Temporary: put in place so that this 3.07 feature can be used under 3.06
if (!function_exists('strip_comma'))
{
    function strip_comma($string)
    {
        // also strips Tabs, CR's and LF's
        $string=str_replace(",", " ", $string);
        $string=str_replace("\r", " ", $string);
        $string=str_replace("\n", " ", $string);
        $string=str_replace("\t", " ", $string);
        return $string;
    }
}

if (empty($_REQUEST['mode']))
{
    include('htmlheader.inc.php');
    echo "<h2>{$strMarketingMailshot}</h2>";
    echo "<form action='{$_SERVER['PHP_SELF']}' method='post'>";
    echo "<table align='center'>";
    echo "<tr><th>Include</th><th>Exclude</th></tr>";
    echo "<tr><td>";
    $sql   = "SELECT * FROM products ORDER BY name";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    echo "<select name='inc[]' multiple='multiple' size='20'>";
    while ($product = mysql_fetch_object($result))
    {
        echo "<option value='{$product->id}'>$product->name</option>\n";
    }
    echo "</select>";
    echo "</td>";
    echo "<td>";
    $sql = "SELECT * FROM products ORDER BY name";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    echo "<select name='exc[]' multiple='multiple' size='20'>";
    while ($product = mysql_fetch_object($result))
    {
        echo "<option value='{$product->id}'>$product->name</option>\n";
    }
    echo "</select>";
    echo "</td></tr>\n";
    /*
    echo "<tr><td align='right' width='200' class='shade1'><b>Table 2</b>:</td>";
    echo "<td width=400 class='shade2'>";
    $result = mysql_list_tables($db_database);
    echo "<select name='table1'>";
    while ($row = mysql_fetch_row($result))
    {
        echo "<option value='{$row[0]}'>{$row[0]}</option>\n";
    }
    echo "</select>";
    echo "</td></tr>\n";
    */

    // echo "<tr><td align='right' width='200' class='shade1'><b>Limit to</b>:</td>";
    // echo "<td width=400 class='shade2'><input type='text' name='limit' value='9999' size='4' /> Records</td></tr>";

    echo "<tr><td  colspan='2'><label><input type='checkbox' name='activeonly' value='yes' /> Only show contacts with current/active contracts.</td></tr>";

    echo "<tr><td colspan='2'>{$strOutput}: <select name='output'>";
    echo "<option value='screen'>{$strScreen}</option>";
    // echo "<option value='printer'>Printer</option>";
    echo "<option value='csv'>{$strCSVfile}</option>";
    echo "</select>";
    echo "</td></tr>";
    echo "</table>";
    echo "<p align='center'>";
    echo "<input type='hidden' name='table1' value='{$_POST['table1']}' />";
    echo "<input type='hidden' name='mode' value='report' />";
    echo "<input type='reset' value=\"{$strReset}\" /> ";
    echo "<input type='submit' value=\"{$strRunReport}\" />";
    echo "</p>";
    echo "</form>";
    echo "<table align='center'><tr><td>";
    echo "<h4>When outputting to a CSV file the format is as follows:</h4>";
    echo "<strong>Field 1:</strong> {$strForenames}<br />";
    echo "<strong>Field 2:</strong> {$strSurname}<br />";
    echo "<strong>Field 3:</strong> {$strEmail}<br />";
    echo "<strong>Field 4:</strong> {$strSite}<br />";
    echo "<strong>Field 5:</strong> {$strAddress1}<br />";
    echo "<strong>Field 6:</strong> {$strAddress2}<br />";
    echo "<strong>Field 7:</strong> {$strCity}<br />";
    echo "<strong>Field 8:</strong> {$strCounty}<br />";
    echo "<strong>Field 9:</strong> {$strPostcode}<br />";
    echo "<strong>Field 10:</strong> {$strCountry}<br />";
    echo "<strong>Field 11:</strong> {$strTelephone}<br />";
    echo "<strong>Field 12:</strong> {$strProducts} <em>(Lists all the customers products regardless of selections made above)</em><br />";
    echo "</td></tr></table>";
    include('htmlfooter.inc.php');
}
elseif ($_REQUEST['mode']=='report')
{
    // don't include anything excluded
    if (is_array($_POST['inc']) && is_array($_POST['exc'])) $_POST['inc']=array_values(array_diff($_POST['inc'],$_POST['exc']));

    $includecount=count($_POST['inc']);
    if ($includecount >= 1)
    {
        // $html .= "<strong>Include:</strong><br />";
        $incsql .= "(";
        for ($i = 0; $i < $includecount; $i++)
        {
            // $html .= "{$_POST['inc'][$i]} <br />";
            $incsql .= "product={$_POST['inc'][$i]}";
            if ($i < ($includecount-1)) $incsql .= " OR ";
        }
        $incsql .= ")";
    }
    $excludecount=count($_POST['exc']);
    if ($excludecount >= 1)
    {
        // $html .= "<strong>Exclude:</strong><br />";
        $excsql .= "(";
        for ($i = 0; $i < $excludecount; $i++)
        {
            // $html .= "{$_POST['exc'][$i]} <br />";
            $excsql .= "product!={$_POST['exc'][$i]}";
            if ($i < ($excludecount-1)) $excsql .= " AND ";
        }
        $excsql .= ")";
    }

    $sql  = "SELECT *, contacts.id AS contactid, contacts.email AS contactemail, sites.name AS sitename FROM maintenance ";
    $sql .= "LEFT JOIN supportcontacts ON maintenance.id=supportcontacts.maintenanceid ";
    $sql .= "LEFT JOIN contacts ON supportcontacts.contactid=contacts.id ";
    $sql .= "LEFT JOIN sites ON contacts.siteid = sites.id ";

    if (empty($incsql)==FALSE OR empty($excsql)==FALSE OR $_REQUEST['activeonly']=='yes') $sql .= "WHERE ";
    if ($_REQUEST['activeonly']=='yes')
    {
        $sql .= "maintenance.term!='yes' AND maintenance.expirydate > '$now' ";
        if (empty($incsql)==FALSE OR empty($excsql)==FALSE) $sql .= "AND ";
    }
    if (!empty($incsql)) $sql .= "$incsql";
    if (empty($incsql)==FALSE AND empty($excsql)==FALSE) $sql .= " AND ";
    if (!empty($excsql)) $sql .= "$excsql";

    $sql .= " ORDER BY contacts.email ASC ";

    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    $numrows = mysql_num_rows($result);

    // FIXME i18n
    // FIXME strip slashes from output
    $html .= "<p align='center'>This report is a list of contact details for all customers ";
    if ($_REQUEST['activeonly']=='yes') $html .= "with <strong>current</strong> ";
    else $html .= "that have currently got (or at some time in the past had) ";
    $html .= "contracts for the products you selected - if you selected to exclude any products then customers who have contracts for those products are not shown.</p>";
    $html .= "<table width='99%' align='center'>";
    $html .= "<tr><th>{$strForenames}</th><th>{$strSurname}</th><th>{$strEmail}</th><th>{$strSite}</th><th>{$strAddress1}</th>";
    $html .= "<th>{$strAddress2}</th><th>{$strCity}</th><th>{$strCounty}</th><th>{$strPostcode}</th><th>{$strCountry}</th><th>{$strTelephone}</th><th>{$strProducts}</th></tr>";
    $csvfieldheaders .= "{$strForenames},{$strSurname},{$strEmail},{$strSite},{$strAddress1},{$strAddress2},{$strCity},{$strCounty},{$strPostcode},{$strCountry},{$strTelephone},{$strProducts}\r\n";
    $rowcount=0;
    while ($row = mysql_fetch_object($result))
    {
        if ($row->contactemail!=$lastemail)
        {
            $html .= "<tr class='shade2'><td>{$row->forenames}</td><td>{$row->surname}</td>";
            if ($row->dataprotection_email!='Yes') $html .= "<td>{$row->contactemail}</td>";
            else $html .= "<td><em style='color: red';>{$strWithheld}</em></td>";
            $html .= "<td>{$row->sitename}</td>";
            if ($row->dataprotection_address!='Yes')
                $html .= "<td>{$row->address1}</td><td>{$row->address2}</td><td>{$row->city}</td><td>{$row->county}</td><td>{$row->postcode}</td><td>{$row->country}</td>";
            else
                $html .= "<td colspan='6'><em style='color: red';>{$strWithheld}</em></td>";
            if ($row->dataprotection_phone!='Yes') $html .= "<td>{$row->phone}</td>";
            else $html .= "<td><em style='color: red';>{$strWithheld}</em></td>";

            $psql = "SELECT * FROM supportcontacts, maintenance, products WHERE ";
            $psql .= "supportcontacts.maintenanceid=maintenance.id AND ";
            $psql .= "maintenance.product=products.id ";
            $psql .= "AND supportcontacts.contactid='$row->contactid' ";
            $html .= "<td>";

            // FIXME dataprotection_address for csv
            $csv .= strip_comma($row->forenames).','
                . strip_comma($row->surname).',';
            if ($row->dataprotection_email!='Yes') $csv .= strip_comma(strtolower($row->contactemail)).',';
            else $csv .= ',';

            $csv  .= strip_comma($row->sitename).','
                . strip_comma($row->address1).','
                . strip_comma($row->address2).','
                . strip_comma($row->city).','
                . strip_comma($row->county).','
                . strip_comma($row->postcode).','
                . strip_comma($row->country).',';

            if ($row->dataprotection_phone!='Yes') $csv .= strip_comma(strtolower($row->phone)).',';
            else $csv .= ',';

            $presult = mysql_query($psql);
            if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
            $numproducts=mysql_num_rows($presult);
            $productcount=1;

            while ($product = mysql_fetch_object($presult))
            {
                $html .= strip_comma($product->name);
                $csv .=  strip_comma($product->name);
                if ($productcount < $numproducts) { $html .= " - "; $csv.=' - '; }
                $productcount++;
            }
            $html .= "</td>";
            // $html .= "<td>{$row->name}</td></tr>\n";
            $csv .= strip_comma($row->name) ."\r\n";

            $rowcount++;
        }
        $lastemail = $row->contactemail;
    }
    $html .= "</table>";
    $html .= "<p align='center'>$rowcount Records displayed from a total of $numrows query results</p>";
    $html .= "<p align='center'>SQL Query used to produce this report:<br /><code>$sql</code></p>\n";

    if ($_POST['output']=='screen')
    {
        include('htmlheader.inc.php');
        echo $html;
        include('htmlfooter.inc.php');
    }
    elseif ($_POST['output']=='csv')
    {
        // --- CSV File HTTP Header
        header("Content-type: text/csv\r\n");
        header("Content-disposition-type: attachment\r\n");
        header("Content-disposition: filename=qbe_report.csv");
        echo $csvfieldheaders;
        echo $csv;
    }
}
?>