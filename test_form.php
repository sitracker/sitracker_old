<?php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

$permission = 0; 
require ('core.php');
require (APPLICATION_LIBPATH.'functions.inc.php');
// This page requires authentication
require (APPLICATION_LIBPATH.'auth.inc.php');
require (APPLICATION_LIBPATH.'sitform.inc.php');


include (APPLICATION_INCPATH . 'htmlheader.inc.php');

$f = new Form("testform", $strSearch, "table", "insert");
$c1 = new Cell();
$c1->setIsHeader(TRUE);
$c1->addComponent(new Label("My first label"));
$c2 = new Cell();
$c2->addComponent(new SingleLineEntry("myfirstrow", 10, "mfr"));
$c3 = new Cell();
$c3->setIsHeader(TRUE);
$c3->addComponent(new Label("Start Date"));
$c4 = new Cell();
// $c4->addComponent(new DateC("startdate", 10));
$c4->addComponent(new SingleLineEntry("myfirstrow1", 10, "mfr1"));
$r = new Row();
$r->addComponent($c1);
$r->addComponent($c2);
$f->addRow($r);
$r1 = new Row();
$r1->addComponent($c3);
$r1->addComponent($c4);
$f->addRow($r1);

$f->run();

include (APPLICATION_INCPATH . 'htmlfooter.inc.php');

?>
