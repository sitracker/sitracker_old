<?php
// contact_add.php - Adds a new contact
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// This Page Is Valid XHTML 1.0 Transitional!  31Oct05

@include ('set_include_path.inc.php');
$permission = 1; // Add new contact

require ($lib_path.'db_connect.inc.php');
require ($lib_path.'functions.inc.php');
// This page requires authentication
require ($lib_path.'auth.inc.php');

$pagescripts = array('dojo/dojo.js');

// External variables
$siteid = mysql_real_escape_string($_REQUEST['siteid']);
$submit = $_REQUEST['submit'];

if (empty($submit) OR !empty($_SESSION['formerrors']['add_contact']))
{
    include ('./inc/htmlheader.inc.php');
    ?>
    <script type='text/javascript'>
    //<![CDATA[
        dojo.require ("dojo.widget.ComboBox");
    //]]>
    </script>
    <?php
    echo show_add_contact($siteid, 'internal');
    include ('./inc/htmlfooter.inc.php');
}
else
{
    echo process_add_contact();
}
?>
