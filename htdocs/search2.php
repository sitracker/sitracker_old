<?php
// search2.php - New search
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Kieran Hogg <kieran_hogg[at]users.sourceforge.net>

@include ('set_include_path.inc.php');
require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

include ('htmlheader.inc.php');

echo "<h2>{$strSearch}</h2>";
if(isset($_GET['q']))
{
    $search = cleanvar($_GET['q']);
    $sql = "SELECT * FROM updates ";
    $sql .= "WHERE MATCH (bodytext) against ('{$search}')";
    $result = mysql_query($sql);
    
    while($row = mysql_fetch_object($result))
    {
        echo "<p>{$row->id} - {$row->bodytext}</p>";
    }
}
else
{
    echo "<input></input>";
    echo "<input type='submit' />";
}



include ('htmlfooter.inc.php');

?>
