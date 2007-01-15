<?php

$permission=0; // not required
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');


//0=4,1=2-1,2=3,
/*
  0 has 4
  1 has 2 and 1
  2 has 3
*/
$id = $_REQUEST['id'];
$val = $_REQUEST['val'];

/*$file = fopen("/tmp/file",'w');
fwrite($file, $val);*/

//echo $val;
//echo $id;
//echo "S".$_SESSION['userid'];
if($id == $_SESSION['userid'])
{
//echo $id;
    //check your changing your own
    $sql = "UPDATE users SET dashboard = '$val' WHERE id = '$id'";
    $contactresult = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
}

?>