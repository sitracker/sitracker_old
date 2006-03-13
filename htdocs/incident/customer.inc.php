<?php
/*
incident/customer.inc.php - Displays the customer visible view of incidents, included by ../incident.php

SiT (Support Incident Tracker) - Support call tracking system
Copyright (C) 2000-2006 Salford Software Ltd.

This software may be used and distributed according to the terms
of the GNU General Public License, incorporated herein by reference.
*/



// this may not be required, we may be able to use the normal log page - INL

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}


?>
