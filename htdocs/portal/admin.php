<?php
/*
portal/index.php - Lists incidents in the portal

SiT (Support Incident Tracker) - Support call tracking system
Copyright (C) 2000-2008 Salford Software Ltd. and Contributors

This software may be used and distributed according to the terms
of the GNU General Public License, incorporated herein by reference.
*/

include 'portalheader.inc.php';

if($_POST['submit'])
{
    print_r($_POST);
    foreach(array_keys($_POST['contact']) as $contact)
    {
        echo $_POST['contact'][$contact];
        if($_POST['contact'][$contact] == 'on')
            $value = 'true';
        else
            $value = 'false';
        $sql = "UPDATE `{$dbMaintenance}` AS m ";
        $sql .= "SET var_incident_visible_contacts='{$value}' ";
        $sql .= "WHERE m.id={$contact}";
        echo $sql;
        mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

    }
}

echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/settings.png' alt='{$strAdmin}' /> ";
echo $strAdmin."</h2>";

if ($CONFIG['portal_site_incidents'])
{
    $contracts = admin_contact_contracts($_SESSION['contactid'], $_SESSION['siteid']);
    echo "<h3><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/privacy.png'
            alt='{$strPrivacy}' />  Privacy</h3>";
    
    echo '<br /><p>You are an Admin Contact for the following contracts. You can choose who is able to see those incidents in the portal.</p>';
    
    echo "<table align='center' width='40%'><tr>";
    echo colheader('id', $strID, $sort, $order, $filter);
    echo colheader('product', $strContract, $sort, $order, $filter);
    echo colheader('expiry', $strExpiryDate, $sort, $order, $filter);
    echo colheader('visbility', $strVisibility);
    echo "<form action='{$_SERVER['PHP_SELF']}' method='post'>";
    foreach($contracts as $contract)
    {
        $sql = "SELECT *, m.id AS id ";
        $sql .= "FROM `{$dbMaintenance}` AS m, `{$dbProducts}` AS p ";
        $sql .= "WHERE m.id={$contract} ";
        $sql .= "AND (m.expirydate > UNIX_TIMESTAMP(NOW()) OR m.expirydate = -1) ";
        $sql .= "AND m.product=p.id ";
        
        $result = mysql_query($sql);
        
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    
        if($row = mysql_fetch_object($result))
        {
            print_r($row);
            if($row->expirydate == -1)
                $row->expirydate = $strUnlimited;
            else
                $row->expirydate = ldate("jS F Y", $row->timestamp);
            echo "<tr><td>{$row->id}</td><td>{$row->name}</td><td>{$row->expirydate}</td>";
            echo "<td>";
            
            echo "<select name='visibility'>";
            echo "<option value='no-one'>No-one</option>";
            echo "<option value='named'>Named Contacts</option>";
            echo "<option value='all'>All Contacts</option>";

            
            echo "</td></tr>";
        }
    }
    echo "</table>";
    echo "<p align='center'><input type='submit' id='submit' name='submit'  value='{$strUpdate}' /></form></p>";
    
    echo "<p><strong>Contract contacts</strong><br />
        Named contacts for this contact are all able to see other incidents logged
        under their named contract.</p>
        
        <p><strong>All contacts</strong><br />
        All contacts from your site can see all the incidents whether or not they
        are entitled to log any</p>";
}
?>