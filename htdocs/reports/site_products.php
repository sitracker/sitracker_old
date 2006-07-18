<?php
// site_products.php - List products that sites have under contract
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

$permission=37; // Run Reports
$title='Site Products';
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

if (empty($_REQUEST['mode']))
{
    include('htmlheader.inc.php');
    echo "<h2>$title</h2>";
    echo "<form action='{$_SERVER['PHP_SELF']}' method='post'>";
    echo "<table>";

    echo "<tr><td align='right' width='200' class='shade1'><b>Site Type</b>:</td>";
    echo "<td width=400 class='shade2'>";
    echo sitetype_drop_down('type', 0);
    echo "</td></tr>";

    echo "<tr><td align='right' width='200' class='shade1'><b>Output</b>:</td>";
    echo "<td width=400 class='shade2'>";
    echo "<select name='output'>";
    echo "<option value='screen'>Screen</option>";
    echo "<option value='csv'>Disk - Comma Seperated (CSV) file</option>";
    echo "</select>";
    echo "</td></tr>";
    echo "</table>";
    echo "<p align='center'>";
    echo "<input type='hidden' name='table1' value='{$_POST['table1']}' />";
    echo "<input type='hidden' name='mode' value='report' />";
    echo "<input type='submit' value='report' />";
    echo "</p>";
    echo "</form>";

    echo "<table align='center'><tr><td>";
    echo "<h4>When outputting to a CSV file the format is as follows:</h4>";
    echo "<strong>Field 1:</strong> Site Name<br />";
    echo "<strong>Field 2:</strong> Address Line 1<br />";
    echo "<strong>Field 3:</strong> Address Line 2<br />";
    echo "<strong>Field 4:</strong> City<br />";
    echo "<strong>Field 5:</strong> County<br />";
    echo "<strong>Field 6:</strong> Country<br />";
    echo "<strong>Field 7:</strong> Postcode<br />";
    echo "<strong>Field 8:</strong> Products<br />";
    echo "</td></tr></table>";
    include('htmlfooter.inc.php');
}
elseif ($_REQUEST['mode']=='report')
{
    $type = cleanvar($_REQUEST['type']);
    $sql = "SELECT * FROM sites WHERE typeid='$type' ORDER BY name";

    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    $numrows = mysql_num_rows($result);

    $html .= "<p align='center'>This report is a list of sites that you selected and the products they have (or have had) maintenance for.</p>";
    $html .= "<table width='99%' align='center'>";
    $html .= "<tr><th>Site</th><th>Address1</th><th>Address2</th><th>City</th><th>County</th><th>Country</th><th>Postcode</th><th>Products</th></tr>";
    $csvfieldheaders .= "site,address1,address2,city,county,country,postcode,products\r\n";
    $rowcount=0;
    while ($row = mysql_fetch_object($result))
    {
        $product="";
        $nicedate=date('d/m/Y',$row->opened);
        $html .= "<tr class='shade2'><td>{$row->name}</td><td>{$row->address1}</td><td>{$row->address2}</td><td>{$row->city}</td><td>{$row->county}</td><td>{$row->country}</td><td>{$row->postcode}</td>";
        $html .= "<td>";
        $psql  = "SELECT maintenance.id AS maintid, maintenance.term AS term, products.name AS product, resellers.name AS reseller, licence_quantity, licencetypes.name AS licence_type, expirydate, admincontact, contacts.forenames AS admincontactsforenames, contacts.surname AS admincontactssurname, maintenance.notes AS maintnotes ";
        $psql .= "FROM maintenance, contacts, products, licencetypes, resellers ";
        $psql .= "WHERE maintenance.product=products.id AND maintenance.reseller=resellers.id AND licence_type=licencetypes.id AND admincontact=contacts.id ";
        $psql .= "AND maintenance.site = '{$row->id}' ";
        $psql .= "ORDER BY products.name ASC";
        $presult = mysql_query($psql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        while ($prod = mysql_fetch_object($presult))
        {
            $product.= "{$prod->product}\n";
        }
        $html .= nl2br($product)."</td>";
        $html .= "</tr>";
        $csv .="'{$row->name}', '{$row->address1}','{$row->address2}','{$row->city}','{$row->county}','{$row->country}','{$row->postcode}',";
        $csv .= "".str_replace("\n", ",", $product)."\n";
        // flush();
    }
    $html .= "</table>";

    //  $html .= "<p align='center'>SQL Query used to produce this report:<br /><code>$sql</code></p>\n";

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
        header("Content-disposition: filename=site_products.csv");
        echo $csvfieldheaders;
        echo $csv;
    }
}
?>