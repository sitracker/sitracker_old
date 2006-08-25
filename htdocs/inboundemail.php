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
    $rawemail[] = fgets($fp); // , 1024
}
fclose($fp);

// Create and populate the email object
$email = new mime_email;
$email->set_emaildata($rawemail);
unset($rawemail);


//echo "------------------------------\n\n\n\n";

$decoded_email = $email->go_decode();


echo "Decoded mail...\n";
print_r($decoded_email);
//echo $decoded_email->emailtextplain;



$part=1;
if ($decoded_email->contenttype=='multipart/mixed'
    OR $decoded_email->contenttype=='multipart/alternative')
{
    // This is a MIME message
    foreach($decoded_email->mime_block AS $block)
    {
        print_r($block);
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
        if ($block->mime_contentdisposition=='inline' OR $block->mime_contentdisposition=='')
        {
            switch ($block->mime_contenttype)
            {
                case 'text/plain':
                    $message .= htmlentities($block->mime_content);
                break;

                case 'text/html':
                    // Only use HTML version if we have no text version
                    if (empty($message)) $message = htmlentities(strip_tags($block->mime_content));
                break;

                default:
                    $message .= "Inline content of type {$block->mime_contenttype} ommitted.\n";
                    // FIXME we should treat these blocks as attachments
            }
        }
        else
        {
            $filename=str_replace(' ','_',$block->mime_contentdispositionname);
            if (empty($filename)) $filename = "part{$part}";
            echo "* FILE ATTACHMENT: $filename\n";
        }
        $part++;
    }
}
if (empty($message)) $message = htmlentities($decoded_email->emailtextplain);

// Strip excessive line breaks
$message = str_replace("\n\n\n\n","\n", $message);

echo "#*#-[START MESSAGE]*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#\n";
echo $message;
echo "\n#*#-[END MESSAGE]*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#\n";

?>