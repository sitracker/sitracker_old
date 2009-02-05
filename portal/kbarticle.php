<?php
// kbarticle.php - Display a single portal knowledge base article
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author Kieran Hogg <kieran[at]sitracker.org>

$lib_path = dirname( __FILE__ ).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR;
require $lib_path.'db_connect.inc.php';
require $lib_path.'functions.inc.php';

$accesslevel = 'any';

include $lib_path.'portalauth.inc.php';
include '../inc/portalheader.inc.php';

if (!empty($_REQUEST['id']))
{
    $id = cleanvar($_REQUEST['id']);
}
if (empty($id))
{
    header("Location: kb.php");
    exit;
}

echo "<h2>".icon('kb', 32)." {$strKnowledgeBaseArticle}</h2>";
echo kb_article($id, 'external');

include ('../inc/htmlfooter.inc.php');

?>
