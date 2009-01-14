<?php
// kb_view_article.php - Display a single knowledge base article
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Authors: Ivan Lucas <ivanlucas[at]users.sourceforge.net>, Tom Gerrard
@include ('set_include_path.inc.php');
$permission = 54; // View KB

require ('db_connect.inc.php');
require ('functions.inc.php');
// This page requires authentication
require ('auth.inc.php');


if (!empty($_REQUEST['id']))
{
    $id = cleanvar($_REQUEST['id']);
}
if (!empty($_REQUEST['kbid']))
{
    $id = cleanvar($_REQUEST['kbid']);
}
if (empty($id))
{
    header("Location: browse_kb.php");
    exit;
}
include ('htmlheader.inc.php');

echo "<h2>".icon('kb', 32)." {$strKnowledgeBaseArticle}</h2>";
echo kb_article($id);

include ('htmlfooter.inc.php');

?>
