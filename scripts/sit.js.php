<?php
// sit.js.php - JAVASCRIPT file
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

// Note: This file is PHP that outputs Javascript code, this is primarily
//       to enable us to pass variables from PHP to Javascript.
//

$permission = 0; // not required
require ('..'.DIRECTORY_SEPARATOR.'core.php');

session_name($CONFIG['session_name']);
session_start();

require (APPLICATION_LIBPATH . 'functions.inc.php');

header('Content-type: text/javascript');

echo "
var application_webpath = '{$CONFIG['application_webpath']}';
var strJanAbbr = '{$strJanAbbr}';
var strFebAbbr = '{$strFebAbbr}';
var strMarAbbr = '{$strMarAbbr}';
var strAprAbbr = '{$strAprAbbr}';
var strMayAbbr = '{$strMayAbbr}';
var strJunAbbr = '{$strJunAbbr}';
var strJulAbbr = '{$strJulAbbr}';
var strAugAbbr = '{$strAugAbbr}';
var strSepAbbr = '{$strSepAbbr}';
var strOctAbbr = '{$strOctAbbr}';
var strNovAbbr = '{$strNovAbbr}';
var strDecAbbr = '{$strDecAbbr}';


/**
  * Display/Hide contents of a password field
  * (converts from a password to text field and back)
  * @author Ivan Lucas
  * @param string elem. The ID of the password input HTML element
**/
function password_reveal(elem)
{
    var elemlink = 'link' + elem;
    if ($(elem).type == 'password')
    {
        $(elem).type = 'text';
        $(elemlink).innerHTML = '{$strHide}';
    }
    else
    {
        $(elem).type = 'password';
        $(elemlink).innerHTML = '{$strReveal}';
    }
}

";




?>
