<?php
// site_products_matrix.php -
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
//  Author:   Ivan Lucas

@include ('set_include_path.inc.php');
$permission=37;  // Run Reports

include ('db_connect.inc.php');
include ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

// show search renewal form
switch ($_POST['action'])
{
    case 'runreport':
        $min_expire = cleanvar($_POST['min_expire']);
        $max_expire = cleanvar($_POST['max_expire']);
        $output = cleanvar($_POST['output']);
        $vendor = cleanvar($_POST['vendor']);

        if (!empty($min_expire)) $min_expiry=strtotime($min_expire);
        else $min_expiry = $now;

        if (!empty($max_expire)) $max_expiry=strtotime($max_expire);
        else $max_expiry = $now;

        $sql = "SELECT products.id, products.name FROM products, maintenance ";
        $sql .= "WHERE products.id = maintenance.product AND ";
        $sql .= "maintenance.expirydate <= $max_expiry AND maintenance.term != 'yes' AND ";
        $sql .= "products.vendorid = '{$vendor}' ORDER BY name";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        while ($prod = mysql_fetch_object($result))
        {
            $product[$prod->id] = $prod->name;
        }

        $vendor = cleanvar($_POST['vendor']);

        $sql  = "SELECT m.id AS maintid, sites.name AS site, sites.id AS siteid, contacts.address1 AS address1, products.name AS product, resellers.name AS reseller, licence_quantity, licencetypes.name AS licence_type, expirydate, admincontact, contacts.forenames AS forenames, .contacts.surname AS admincontactname, m.notes, ";
        $sql .= "contacts.department AS department, contacts.address2 AS address2, contacts.city AS city, contacts.county, contacts.country, contacts.postcode, sitetypes.typename AS typename ";
        $sql .= "FROM `{$dbMaintenance}` AS m, sites, sitetypes, contacts, products, licencetypes, resellers WHERE ";
        $sql .= "(m.site=sites.id ";
        $sql .= "AND sites.typeid=sitetypes.typeid ";
        $sql .= "AND product=products.id ";
        if (!empty($vendor)) $sql .= "AND products.vendorid='{$vendor}' ";
        $sql .= "AND reseller=resellers.id AND licence_type=licencetypes.id AND admincontact=contacts.id) AND ";
        $sql .= "expirydate <= $max_expiry AND expirydate >= $min_expiry AND m.term != 'yes' GROUP BY sites.id ORDER BY expirydate ASC";

// echo $sql;

        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

        if (mysql_num_rows($result) > 0)
        {
            $html = "<table><tr><th>Site</th>";
            $csv = "Site";
            // products list
            foreach ($product AS $prodid => $prodname)
            {
                $html .= "<th>{$prodname}</th>";
                $csv .= ",{$prodname}";
            }
            $html .= "</tr>";
            $csv .= "\n";

            while ($site = mysql_fetch_array($result))
            {
                $html .= "<tr><td>{$site['site']}</td>";
                $csv .= strip_comma($site['site']);

                $prodsql  = "SELECT products.name AS product, products.id AS productid, maintenance.expirydate AS expirydate, maintenance.term AS term, ";
                $prodsql .= "maintenance.productonly AS productonly, maintenance.licence_type AS licencetype, ";
                $prodsql .= "maintenance.licence_quantity AS licencequantity FROM products, maintenance ";
                $prodsql .= "WHERE products.id=maintenance.product AND maintenance.site='{$site['siteid']}' ";
                if (!empty($vendor)) $sql .= "AND products.vendorid='{$vendor}' ";
                $prodsql .= "AND expirydate <= $max_expiry AND expirydate >= $min_expiry ";
                $prodsql .= "AND maintenance.term!='yes' ";
                $prodsql .= "ORDER BY expirydate ASC";
//                 echo $prodsql;
                $prodresult = mysql_query($prodsql);
                if (mysql_error()) throw_error('!Error: MySQL Query Error:',mysql_error());

                if (mysql_num_rows($prodresult)>0)
                {
                    $numofproducts = mysql_num_rows($prodresult);
                    while ($siteproducts = mysql_fetch_array($prodresult))
                    {
                        $supportedproduct[$site['siteid']][$siteproducts['productid']] = $siteproducts['product'];
                    }
                }
                // products list
                foreach ($product AS $prodid => $prodname)
                {
                    if (array_key_exists($prodid, $supportedproduct[$site['siteid']]))
                    {
                        $html .= "<td>{$prodname}</td>";
                        $csv .= ",".strip_comma($prodname);
                    }
                    else
                    {
                        $html .= "<td></td>";
                        $csv .= ",";
                    }
                }
                $html .= "</tr>\n";
                $csv .= "\n";
            }
            $html .= "</table>";
            mysql_free_result($result);

            // Print Headers
            if ($output == 'csv')
            {
                header("Content-type: text/csv\r\n");
                //header(\"Content-length: $fsize\\r\\n\");
                header("Content-disposition-type: attachment\r\n");
                header("Content-disposition: filename=site_products_matrix.csv");
                echo $csv;
            }
            else
            {
                include ('htmlheader.inc.php');
                echo $html;
//                 echo "<hr />";
//                 echo "<pre>{$csv}</pre>";
                include ('htmlfooter.inc.php');
            }
        }
        else
        {
            html_redirect($_SERVER['PHP_SELF'], FALSE, $strNoResults);
        }
        break;

    default:
        include ('htmlheader.inc.php');
        echo "<h2>Site Product Matrix</h2>";
        echo "<form name='report' action='{$_SERVER['PHP_SELF']}' method='post'>";
        echo "<table class='vertical'>";
        echo "<tr><th>{$strVendor}</th>";
        echo "<td>".vendor_drop_down('vendor', 0)."</td></tr>\n";
        echo "<tr><th>Earliest Expiry:</th>";
        echo "<td><input maxlength='100' id='min_expire' name='min_expire' size='10' type='text' value=\"".date('Y-m-d')."\" /> ";
        echo date_picker('report.min_expire');
        echo "</td></tr>\n";
        echo "<tr><th>Latest Expiry:</th>";
        echo "<td><input maxlength='100' id='max_expire' name='max_expire' size='10' type='text' value=\"".date('Y-m-d',mktime(0,0,0,date('m'),date('d'),date('Y')+1))."\" /> ";
        echo date_picker('report.max_expire');
        echo "</td></tr>\n";
        echo "<tr><th>{$strOutput}:</th>";
        echo "<td>";
        echo "<select name='output'>";
        echo "<option value='screen'>{$strScreen}</option>";
        echo "<option value='csv'>{$strCSVfile}</option>";
        echo "</select>";
        echo "</td></tr>\n";
        echo "</table>";
        echo "<p><input name='submit' type='submit' value=\"{$strRunReport}\" /></p>";
        echo "<input type='hidden' name='action' value='runreport' />";
        echo "</form>\n";
        include ('htmlfooter.inc.php');
        break;
}
?>