<?php
// autocomplete.php - Page to aid in the auto completion of fields
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>

$permission=0; // not required
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

$action = $_REQUEST['action'];

switch($action)
{
    case 'tags':
        $sql = "SELECT DISTINCT tags.name FROM set_tags, tags WHERE set_tags.tagid = tags.tagid GROUP BY tags.name";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        if(mysql_num_rows($result) > 0)
        {
            while($obj = mysql_fetch_object($result))
            {
                $str .= "[".$obj->name."],";
            }
        }
        break;
    case 'contact' :
        $sql = "SELECT DISTINCT forenames,surname FROM contacts WHERE active='true'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        if(mysql_num_rows($result) > 0)
        {
            while($obj = mysql_fetch_object($result))
            {
                $str .= "[\"".$obj->surname."\"],";
                $str .= "[\"".$obj->forenames." ".$obj->surname."\"],";
            }
        }
        break;
    case 'sites':
        $sql = "SELECT DISTINCT name FROM sites WHERE active='true'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        if(mysql_num_rows($result) > 0)
        {
            while($obj = mysql_fetch_object($result))
            {
                $str .= "[\"".$obj->name."\"],";
            }
        }
        break;
    default : break;
}

echo "[".substr($str,0,-1)."]";

?>
