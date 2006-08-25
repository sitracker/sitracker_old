#!/usr/bin/php
<?php
// inboundemail.php - Process incoming emails
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Ivan Lucas


// TODO Stub
// This script is uninished as of 25 Aug 06, committing it to SVN so I can work on it
// elsewhere -- Ivan
// The intention is that emails are piped to this script

echo "Mail decoder\n";
//require('db_connect.inc.php');
//require('functions.inc.php');
require('mime_email.class.php');



// read the email from stdin (it should be piped to us by the MTA)
$fp = fopen("php://stdin", "r");
$rawemail = '';
while (!feof($fp))
{
    $rawemail .= fread($fp, 1024);
}
fclose($fp);

// Create and populate the email object
$email = new mime_email;
$email->set_emaildata($rawemail);

print_r($email);
//echo "------------------------------\n\n\n\n";

$decoded_email = $email->go_decode();

echo "Decoded mail...\n";
//print_r($decoded_email->mime_block);
echo "----\n----\n-------------------------------\n\n\n\n";
$part=1;
foreach($decoded_email->mime_block AS $block)
{
    // Do the decoding
    switch ($block->mime_transferenc)
    {
        case 'quoted-printable':
            $block->mime_content = quoted_printable_decode($block->mime_content);
        break;

        case 'base64':
            $block->mime_content = base64_decode($block->mime_content);
        break;

        default:
            // Do no decoding
    }
    // Extract any inline text into the incident log (if it's HTML strip the tags first)
    if ($block->mime_contentdisposition=='inline')
    {
        switch ($block->mime_contenttype)
        {
            case 'text/plain':
                $message .= htmlentities($block->mime_content);
            break;

            case 'text/html':
                $message .= htmlentities(strip_tags($block->mime_content));
            break;

            default:
                $message .= "Inline content of type {$block->mime_contenttype} ommitted.";
        }
    }
    else
    {
        $filename=str_replace(' ','_',$block->mime_contentdispositionname);
        if (empty($filename)) $filename = "part{$part}";
        echo "FILENAME: $filename\n\n\n";
    }
    $part++;
}

echo $message;
echo "---------------------------------------\n\n\n\n";

?>