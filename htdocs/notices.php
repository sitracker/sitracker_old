<?php
// notices.php - modify and add global notices
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Kieran Hogg[at]users.sourceforge.net>

$permission=70;

require('db_connect.inc.php');
require('functions.inc.php');
require('auth.inc.php');


$action = cleanvar($_REQUEST['action']);
if($action == 'new')
{
    include('htmlheader.inc.php');
    echo "<h2>{$strPostNewNotices}</h2>";
    echo "<p align='center'>This page allows you to post a new global notice. HTML <strong>is</strong> allowed and useful variables are shown below.</p>";
    echo "<table align='center'></tr><th>Varaible</th><th>Value</th></tr>";
    echo '<tr><td>$CONFIG[\'application_name\']</td><td>'.$CONFIG['application_name'].'</td></tr>';
    echo '<tr><td>$CONFIG[\'application_shortname\']</td><td>'.$CONFIG['application_shortname'].'</td></tr>';
    echo "</table>";
    echo "<div align='center'><form action='{$_SERVER[PHP_SELF]}?action=post' method='POST'>";
    echo "<h3>Message</h3>";
    echo "<textarea cols=90 rows=4 name='text'></textarea><br />";
    echo "<input type='submit' value='{$strSubmit}'>";
    echo "</form></div>";
}
elseif($action == 'post')
{
    $text = cleanvar($_REQUEST['text']);
    
    //post new notice
    $sql = "INSERT INTO notices VALUES('', '$text', NOW())";
    mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
 
    $noticeid = mysql_insert_id();
      
    $sql = "SELECT id FROM users WHERE status != 0";
    $result = mysql_query($sql);
    while($user = mysql_fetch_object($result))
    {
        $sql = "INSERT INTO usernotices VALUES({$noticeid}, {$user->id}, 0)";
        mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    }
    
    confirmation_page(2, 'notices.php', '<h2>Notice Added</h2><p>You will be redirected, please wait...</p>');

}
elseif($action == 'delete')
{
    $noticeid = cleanvar($_REQUEST['id']);
    $sql = "DELETE FROM notices WHERE id={$noticeid} LIMIT 1";
    mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
            
    $sql = "SELECT id FROM users WHERE status != 0";
    $result = mysql_query($sql);
    while($user = mysql_fetch_object($result))
    {
        $sql = "DELETE FROM usernotices WHERE noticeid={$noticeid} AND userid={$user->id}";
        mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
    }
    
    confirmation_page(2, 'notices.php', '<h2>Notice Deleted</h2><p>You will be redirected, please wait...</p>');
}
else
{
    include('htmlheader.inc.php');
    echo "<h2>{$strNotices}</h2>";
    
    //get all notices
    $sql = "SELECT * FROM notices";
    $result = mysql_query($sql);
    print_r($notice);
    
    echo "<table align='center'>";
    echo "<tr><th>{$strID}</th><th>{$strDate}</th><th>Text</th><th>{$strActions}</th></tr>";
    while($notice = mysql_fetch_object($result))
    {
        echo "<tr><td>{$notice->id}</td><td>{$notice->timestamp}</td><td>";
        echo "{$notice->text}</td><td>";
        echo "<a href='{$_SERVER[PHP_SELF]}?action=update&id={$notice->id}'>{$strUpdate}</a> | <a href='{$_SERVER[PHP_SELF]}?action=delete&id={$notice->id}'>{$strDelete}</a></tr>";
    }
    echo "</table>";
    
    echo "<p align='center'><a href='{$_SERVER[PHP_SELF]}?action=new'>{$strPostNewNotice}</a></p>";
}

?>