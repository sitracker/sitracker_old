<?php
// add_escalation_path.php - Display a form for adding an escalation path
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>

//// This Page Is Valid XHTML 1.0 Transitional!  (1 Oct 2006)

@include('set_include_path.inc.php');
$permission=64; // Manage escalation paths

require('db_connect.inc.php');
require('functions.inc.php');
// This page requires authentication
require('auth.inc.php');

$submit = $_REQUEST['submit'];

$title = $strNewEscalationPath;



if(empty($submit))
{
    include('htmlheader.inc.php');
    ?>
    <script type='text/javascript'>
    function confirm_submit()
    {
        return window.confirm('Are you sure you want to add this escalation path?');
    }
    </script>
    <?php
    echo show_errors();
    echo "<h2>{$title}</h2>";

    echo "<form action='".$_SERVER['PHP_SELF']."' method='post' onsubmit='return confirm_submit()'>";
    echo "<table class='vertical'>";

    //FIXME i18n
    echo "<tr><th>{$strName}</th><td><input name='name'";
    if($_SESSION['formdata']['name'] != "")
        echo "value='{$_SESSION['formdata']['name']}'";
    echo "/></td></tr>";

    echo "<tr><th>Track URL<br /></th><td><input name='trackurl'";
    if($_SESSION['formdata']['trackurl'] != "")
        echo "value='{$_SESSION['formdata']['trackurl']}'";
    echo "/><br />Note: insert '%externalid%' for automatic incident number insertion</td></tr>";

    echo "<tr><th>Home URL</th><td><input name='homeurl'";
    if($_SESSION['formdata']['homeurl'] != "")
        echo "value='{$_SESSION['formdata']['homeurl']}'";
    echo "/></td></tr>";

    echo "<tr><th>{$strTitle}</th><td><input name='title'";
    if($_SESSION['formdata']['title'] != "")
        echo "value='{$_SESSION['formdata']['title']}'";
    echo "/></td></tr>";

    echo "<tr><th>Email domain</th><td><input name='emaildomain'";
    if($_SESSION['formdata']['emaildomain'] != "")
        echo "value='{$_SESSION['formdata']['emaildomain']}'";
    echo "/></td></tr>";

    echo "</table>";

    echo "<p align='center'><input type='submit' name='submit' value='{$strAdd}' /></p>";

    echo "</form>";

    include('htmlfooter.inc.php');
}
else
{
    $name = cleanvar($_REQUEST['name']);
    $trackurl = cleanvar($_REQUEST['trackurl']);
    $homeurl = cleanvar($_REQUEST['homeurl']);
    $title = cleanvar($_REQUEST['title']);
    $emaildomain = cleanvar($_REQUEST['emaildomain']);

    $_SESSION['formdata'] = $_REQUEST;

    $errors = 0;
    if(empty($name))
    {
        $errors++;
        $_SESSION['formerrors']['name'] = "You must enter a name for the escalation path\n";
    }

    if($errors == 0)
    {
        $sql = "INSERT INTO escalationpaths (name,track_url,home_url,url_title,email_domain) VALUES ";
        $sql .= " ('{$name}','{$trackurl}','{$homeurl}','{$title}','{$emaildomain}')";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        if(!$result) $_SESSION['formerrors']['error'] = "Addition of escalation path failed";
        else
        {
            html_redirect("escalation_paths.php");
        }
        $_SESSION['formerrors'] = NULL;
        $_SESSION['formdata'] = NULL;
    }
    else
    {
        include 'htmlheader.inc.php';
        html_redirect("add_escalation_path.php", FALSE);
    }
}


?>