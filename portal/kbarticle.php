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

require ('..'.DIRECTORY_SEPARATOR.'core.php');
require (APPLICATION_LIBPATH . 'functions.inc.php');

$accesslevel = 'any';

include (APPLICATION_LIBPATH . 'portalauth.inc.php');
include (APPLICATION_INCPATH . 'portalheader.inc.php');

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

include (APPLICATION_INCPATH . 'htmlfooter.inc.php');

?>