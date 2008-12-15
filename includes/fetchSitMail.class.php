<?php

class fetchSitMail
{
    var $username;
    var $password;
    var $server;
    var $email;
    var $mailbox;
    var $servertype;
    //Append 
    function fetchSitMail($username, $password, $email, $server =
                          'localhost', $servertype = 'pop', $port = '', 
                          $options = '')
    {
        if (!empty($CONFIG['email_incoming_folder']))
        {
            $folder = $CONFIG['email_incoming_folder'];
        }
        else
        {
            $folder = 'INBOX';
        }
        if ($servertype == 'imap')
        {
            if (empty($port))
            {
                $port = '143';
            }
            $connectionString = "{{$server}:{$port}/imap{$options}".
                                 "/user={$user}}$folder";
        }
        else
        {
            if (empty($port))
            {
                $port = '110';                
            }
            $connectionString = "{{$server}:{$port}/pop3{$options}".
                                "/user={$user}}$folder";
        }
        $this->username = $username;
        $this->password = $password;
        $this->server = $connectionString;
        $this->email = $email;
        $this->servertype = $servertype;
    }

    function connect()
    {
        $this->mailbox = imap_open($this->server, $this->username,
                                   $this->password, 'CL_EXPUNGE') OR
            trigger_error(imap_last_error(), E_USER_ERROR)."\n";
    }
    
    function getNumUnreadEmails()
    {
        $headers = imap_headers($this->mailbox);
        return count($headers);
    }
    
    function getAttachments($id, $path)
    {
        $parts = imap_fetchstructure($this->mailbox, $id);
        $attachments = array();
        
        //FIXME if we do an is_array() here it breaks howver if we don't
        //we get foreach errors
        foreach($parts->parts as $key => $value)
        {
            $encoding = $parts->parts[$key]->encoding;
            if($parts->parts[$key]->ifdparameters)
            {
                $filename = $parts->parts[$key]->dparameters[0]->value;
                $message = imap_fetchbody($this->mailbox, $id, $key + 1);
                
                switch($encoding)
                {
                    case 0:
                        $message = imap_8bit($message);
                    case 1:
                        $message = imap_8bit ($message);
                    case 2:
                        $message = imap_binary ($message);
                    case 3:
                        $message = imap_base64 ($message);
                    case 4:
                        $message = quoted_printable_decode($message);
                    case 5:
                    default:
                        $message = $message;
                }
                
                $fp = fopen($path.$filename,"w");
//                 echo "writing ".$path.$filename."\n";
                fwrite($fp, $message);
                fclose($fp);
                $attachments[] = $filename;
            }
        }
        return $attachments;
        
    }
    
    function messageBody($id)
    {
        global $CONFIG;
        if ($CONFIG['debug']) echo "Retreiving message {$id}\n";
        if (imap_body($this->mailbox, $id))
        {
            return imap_body($this->mailbox, $id);
        }
        else 
        {    
            debug_log("Died on mesasge {$id} with: ".imap_last_error());
        }
    }    
    
    function getMessageHeader($id)
    {
        return imap_fetchheader($this->mailbox, $id);
    }
    
    function deleteEmail($id)
    {
        imap_delete($this->mailbox, $id) OR trigger_error(imap_last_error(), E_USER_ERROR);
    }
        
    function iso8859Decode($text)
    {
        return imap_utf7_encode($text);
    }

	function archiveEmail($id)
	{
		global $CONFIG;
		if ($CONFIG['debug']) echo "Moving mail to {$CONFIG['email_archive_folder']} folder\n";
		imap_mail_move($this->mailbox, $id, $CONFIG['email_archive_folder']) OR
			trigger_error(imap_last_error(), E_USER_ERROR);
	}
}
?>
