<?php 
# 
# mime_email - PHP class for decoding MIME Emails
# Version 0.1
#
# You are tired to use the imap-module of php in order to parse
# MIME emails? This class is an abstraction to this, you can
# just include it and go decoding your emails.
# Its pretty hard to follow all the email RFCs, so i went a different
# way, i looked at many emails and hope to be RFC compliant :)
# 
# Copyright 2001
# Marc Logemann (loge@mail.com)
#
# Originally written for www.phpgroupware.org
# 
# Licence: 
# GPL
# 
# 
# Methods: 
# 
# mime_email() 
#    Constructor used when creating a new instance of the mime_devode class. 
#    Returns true on success. 
#    ie. 
#        $a=new mime_email;
# 
# 
# set_emaildata(string email) 
#    method to submit the raw email data in order work with it furthermore
#    Returns true on success. 
#    ie. 
#        $a->set_emaildata($youremail);
#
# 
# set_htmlentities(boolean bool)
#	 method where you can turn on htmlentities conversion of all attributes
#    must be called before go_decode* methods of course
#
#
# go_decode() 
#    method that will do the nasty work to parse the email in a way the w3c wants it :)
#    Returns mime_mail object ($this)
#    ie. 
#        $a->go_decode();
#
#
# go_decode_headeronly() 
#    method that will do the nasty work to parse the email in a way the w3c wants it :)
#	 but compared to go_decode, this method will only parse the header, useful for
#	 displaying lists which only contain header informations
#    Returns mime_mail object ($this)
#    ie. 
#        $a->go_decode();
#

class mime_email
{ 
    
// =============================================
// Public Attributes
// =============================================

    var $from;  							# from-header value
    var $to;								# to-header value
    var $replyto;							# replyto-header value
    var $in_replyto;						# inreplyto-header value (Reference to 
    										# message which this  message is a reply to.)

    var $date;								# date-header value
    var $cc;								# cc-header value
    var $subject;							# subject-header value
    var $message_id; 						# messageid-header value

    var $mimeversion;						# mimeversion-header value
	var $importance;						# importance header value
	var $sensitivity;						# sensitivity header value
	var $disposition_notification_to;		# MDN header value
	
    var $contenttype;						# content-type-header value (the real content type i.e. text/plain)
	var $contenttypebound;					# content-type-boundary value (boundary value of content type)
	var $contenttypecharset;				# content-type-charset value (charset value of content type)	
	var $contenttransencoding;				# content-transferencoding value (encoding value of content type)		
	
	var $emailtextplain;					# email text (plain)
	var $emailtexthtml;						# email text (html)
	
	var $xmailer;							# signature of sending email client
	var $xpriority;							# X-Priority header

    var $mime_block = array(); 				# array of mime_block objects
    
    var $isHTMLmail;						# indicates whether this i an HTML email
    var $isATTACHMENTmail;					# indicates whether this mail has attachments
    var $from_name;							# only the name of FROM field (for example "James Clark <clark@clark.com>"
    										#								only James Clark will be there)
	var $from_email;						# only the email of FROM field (for example "James Clark <clark@clark.com>"
											#								only clark@clark.com will be there)
    
// =============================================
// Private Attributes
// =============================================
    
    var $rawemail = array();				# complete email, with all headers and content
    var $varhtmlentities = false;			# true if each special char in the attributes should be entities
    var $bodystart; 						# array index where the body starts

// =============================================
// Public Methods
// =============================================
    
    function mime_email(){ 
    } 

    function set_emaildata ($myemail){ 
    	$this->rawemail = $myemail;
    }

	function set_htmlentities($boolean) {
		$this->varhtmlentities = $boolean;
	}
    
    function go_decode() {
		$this->parseHeader($this->rawemail);
		$this->parseBody($this->rawemail);
		return $this;
	}
	
	
    function go_decode_headeronly() {
		$this->parseHeader($this->rawemail);
		return $this;
	}
		
    function dump($complete_email = false) {
	
		if($complete_email) {
			echo "============= RAW EMAIL================<br> ";
			while ( list ($linenbr, $line) = each ($this->rawemail) ) {
				echo htmlentities($line) . "<br>";
			}
		}
		echo "==============<br>";
		echo " AFTER PARSING<br>";
		echo "==============<br>";
		
		echo "============= Standard Headerfields================<br> ";
		echo "From: ".$this->from ."<br>";
		echo "To: ".$this->to ."<br>";
		echo "Subject: ".$this->subject ."<br>";
		echo "Reply-To: ".$this->replyto ."<br>";
		echo "X-Mailer: ".$this->xmailer ."<br>";
		echo "X-Priority: ".$this->xpriority ."<br>";
		echo "Importance: ".$this->importance ."<br>";
		echo "Sensitivity: ".$this->sensitivity ."<br>";
		echo "Dispo-Notif-To: ".$this->disposition_notification_to ."<br>";
		echo "Date: ".$this->date ."<br>";
		echo "CC: ".$this->cc ."<br>";
		echo "content-type: ".$this->contenttype ."<br>";
		echo "content-typecharset: ".$this->contenttypecharset ."<br>";
		echo "content-typebound: ".$this->contenttypebound ."<br>";
		echo "content-Trans-Enc: ".$this->contenttransencoding ."<br>";
		echo "============= MyCustomFields ================<br> ";
		echo "HTML EMAIL: ".$this->isHTMLmail ."<br>";
		echo "ATTACH MAIL: ".$this->isATTACHMENTmail ."<br>";
		for($i=0;$i<count($this->mime_block);$i++) {
			echo "============= MIME $i ================<br> ";
			echo "ctype: ". $this->mime_block[$i]->getMimeContentType()."<br>";
			echo "boundary: ".$this->mime_block[$i]->getMimeContentTypeBoundary()."<br>";
			echo "name: ".$this->mime_block[$i]->getMimeContentTypeName()."<br>";
			echo "charset: ".$this->mime_block[$i]->getMimeContentTypeCharset()."<br>";
			echo "transferenc: ".$this->mime_block[$i]->getMimeTransferEncoding()."<br>";
			echo "content-dispo: " . $this->mime_block[$i]->getMimeContentDisposition() . "<br>";
			echo "content-disponame: " . $this->mime_block[$i]->getMimeContentDispositionName() . "<br>";
			echo "content: ".htmlentities($this->mime_block[$i]->getMimeContent())."<br>";
		}
		echo "============= emailtextplain ================<br> ";
		echo nl2br(quoted_printable_decode($this->emailtextplain)) . "<br>";
		echo "============= emailtexthtml ================<br> ";
		echo htmlentities($this->emailtexthtml). "<br>";
	}

// =============================================
// Private Methods
// =============================================

    # a method for performance measuring, we have some problems
    # with big attachments, so we need this internally right now
    # hopefully we can delete this method soon :)
    
    function getmicrotime(){ 
    	list($usec, $sec) = explode(" ",microtime()); 
    	return ((float)$usec + (float)$sec); 
	} 
	
	
	# function to grab all the necessary data from the email header and put
	# them into the object/attributes
	
	function parseHeader($rawemail)
	{
		// for performance measuring
		// $time_start = $this->getmicrotime();

		while ( list ($linenbr, $line) = each ($rawemail) ) {

			$hc++;
			
			// if there is a blank line, we know that the header ended and
			// now the body begins, but we dont need the body right now,
			// so we break out of the loop
			if(trim($line) == "") {$this->bodystart = $hc; break;}
			
    		if ( (!$from) and (eregi ("^From:(.*)", $line, $match)) ) {
      			$this->from = trim ( $match[1] );

    		} elseif ( eregi ("^Subject:(.*)", $line, $match)) {
      			$this->subject = trim ( $match[1] );

    		} elseif (eregi ("^Date:(.*)", $line, $match)) {
      			$this->date = trim ( $match[1] );

    		} elseif (eregi ("^cc:(.*)", $line, $match)) {
      			$this->cc = trim ( $match[1] );

	    	} elseif (eregi ("^To:(.*)", $line, $match)) {
    	  		$this->to = trim ( $match[1] );

	    	} elseif (eregi ("^Reply-to:(.*)", $line, $match)) {
    	  		$this->replyto = trim ( $match[1] );
    	  		
			} elseif (eregi ("^Message-ID:(.*)", $line, $match)) {
				$this->message_id = trim ( $match[1] );

			} elseif (eregi ("^In-Reply-To:(.*)", $line, $match)) {
				$this->in_replyto = trim ( $match[1] );
    	  		
	    	} elseif (eregi ("^Sensitivity:(.*)", $line, $match)) {
    	  		$this->sensitivity = trim ( $match[1] );

	    	} elseif (eregi ("^Importance:(.*)", $line, $match)) {
    	  		$this->importance = trim ( $match[1] );

	    	} elseif (eregi ("^Disposition-Notification-To:(.*)", $line, $match)) {
    	  		$this->disposition_notification_to = trim ( $match[1] );
    	
    		} elseif (eregi ("^X-Priority:(.*)", $line, $match)) {
    	  		$this->xpriority = trim ( $match[1] );
	    	
	    	} elseif (eregi ("^X-Mailer:(.*)", $line, $match)) {
    	  		$this->xmailer = trim ( $match[1] );

	    	// ## do some advanced stuff like content-type grabbing ##
	    	} elseif (eregi ("^Content-Type:(.*)", $line, $match)) {

				//check to see if content-type is splitted up into several lines
				//if yes, the first line ends with a colon ";"
				if(substr(trim($match[1]), strlen(trim($match[1]))-1,1)==";") {
					$ctypecontinue = true;
					$ctypelinehold = $match[1];
				}
				else {
					$myhash = $this->parseContentTypeLine($match[1]);
					$this->contenttype = $myhash[type];
					$this->contenttypebound = $myhash[boundary];
					$this->contenttypecharset = $myhash[charset];
				}
				
	    	} elseif (eregi ("^Content-Transfer-Encoding:(.*)", $line, $match)) {
	    		$this->contenttransencoding = trim ( $match[1] );

	   	  	} elseif ($ctypecontinue && !eregi(":",$line)) {
	   	  		$o++;
	   	  		if(substr(trim($line), strlen(trim($line))-1,1)==";") {
	   	  			$ctypelinehold .= $line;
	   	  	}
	   	  	else {
	   	  		$ctypelinehold .= $line;
	   	  		$myhash = $this->parseContentTypeLine($ctypelinehold);
				$this->contenttype = $myhash[type];
				$this->contenttypebound = $myhash[boundary];
				$this->contenttypecharset = $myhash[charset];
			}
	   	  }
    	}
    	
    	//split from adress like: Firstname Lastname <foo@bar.com>  into two data pieces
    	// name will be stored in from_name and adress will be stored in from_email
		// cut away these silly quotations BTW (if there are any)
		if(eregi ("<(.*)>$", $this->from, $match)) {
			$this->from_email = $match[1];
			$this->from_name = str_replace("\"", "", substr($this->from,0,strpos($this->from,"<")-1));
		}
		else {
			$this->from_email = $this->from;
			$this->from_name = str_replace("\"","",$this->from);
		}
    	
    	// do some FLAG things (only the first shot, a multipart/mixed can also be HTML email, but this
    	// 						will be checked in the body part, not here)
    	if(strtolower($this->contenttype) == "multipart/mixed") $this->isATTACHMENTmail = true;
    	if(strtolower($this->contenttype) == "multipart/alternative") $this->isHTMLmail = true;
    	if(strtolower($this->contenttype) == "text/plain") {$this->isHTMLmail = false;$this->isATTACHMENTmail = false;}
    	$ctypecontinue = false;
    	$ctypelinehold = "";
    	
    	// if htmlentities flag is on, convert all normally visible attributes according to htmlentities
    	if($varhtmlentities) {
    		$this->from = htmlentities($this->from);
    		$this->to = htmlentities($this->to);
    		$this->subject = htmlentities($this->subject);
    		$this->replyto = htmlentities($this->replyto);
    		$this->xmailer = htmlentities($this->xmailer);
		}
    	
   	// for performance measuring
   	// $time_end = $this->getmicrotime();
	// $time = $time_end - $time_start;
	// echo "<h3>echo *HEADERPARSE Start: $time_start   End: $time_end    Diff: $time</h3>";
    
    }

	
	# function to grab all the necessary data from the email body and put
	# them into the object/attributes
	
	function parseBody($rawemail)
	{
		if($this->contenttype == "text/plain" or $this->contenttype=="") {
			//
			// just a text/plain email, thats easy
			//
			for($l=$this->bodystart;$l<count($rawemail);$l++) {
				$body .= $rawemail[$l];
			}
			
			if($this->contenttransencoding=="quoted-printable")
				$this->emailtextplain = quoted_printable_decode($body);
			else
				$this->emailtextplain = $body;
			
			if($varhtmlentities) $this->emailtextplain = htmlentities($this->emailtextplain);
		}
		else {
			//
			// this is a multipart one, it gets tricky
			//
			$this->parseBoundary($this->contenttypebound, $rawemail);
		}

	}

	# parse given Boundary Marker, this has to be a function because for example a html email
	# with attachments has several (mostly 2) nested Boundary Markers
	
	function parseBoundary($boundary, $rawemail) {
		
		// default value for newboundary, because if we leave it blank, our
		// regex below would become true if some write in his email things like "--"
		// so our app would thing that this is a boundary marker
		// ThisIsAUniqueLineFromMarcLogemann should be unique, i cant imagine that someone
		// writes this into his email
		$newboundary = "ThisIsAUniqueLineFromMarcLogemann";
		
    	// for performance measuring
    	// $time_start = $this->getmicrotime();
		
		while ( list ($linenbr, $line) = each ($rawemail) ) {

			$line = trim($line);
			
			if(($hit and eregi("^--".$boundary,$line)) or
				($hit and eregi("^--".$boundary."--",$line)) or
				($hit and eregi("^--".$newboundary,$line)) or
				($hit and eregi("^--".$newboundary."--",$line))) { $hit = false; $body = false;}
				
			if($hit) {
				$i++;
				// -------------------------------------------------------
				if(eregi ("^Content-Type:(.*)", $line, $match)) {
					
					$my_mime_block = new mime_block();
					$cdispocontinue = false;
					
					//check to see if content-type is splitted up into several lines
					//if yes, the first line ends with a colon ";"
					if(substr($match[1], strlen(trim($match[1])),1)==";") {
						$ctypecontinue = true;
						$ctypelinehold = $match[1];
					}
					else {
						$ctypecontinue = false;
						$myhash = $this->parseContentTypeLine($match[1]);
						$my_mime_block->setMimeContentType($myhash[type]);
						$my_mime_block->setMimeContentTypeBoundary($myhash[boundary]);
						$my_mime_block->setMimeContentTypeCharset($myhash[charset]);
						$my_mime_block->setMimeContentTypeName($myhash[name]);
						
						if($my_mime_block->getMimeContentTypeBoundary()!="") 
							$newboundary = $myhash[boundary];
					}
					
				// -------------------------------------------------------
				} elseif(eregi ("^Content-Transfer-Encoding:(.*)", $line, $match)) {
					$my_mime_block->setMimeTransferEncoding(htmlentities(trim ( $match[1] )));
					$cdispocontinue = false;
					$ctypecontinue = false;

				// -------------------------------------------------------
				} elseif(eregi ("^Content-Disposition:(.*)", $line, $match)) {
					
					$ctypecontinue = false;
					
					//check to see if content-disposition is splitted up into several lines
					//if yes, the first line ends with a colon ";"
					
					if(substr(trim($match[1]), strlen(trim($match[1]))-1,1)==";") {
						$cdispocontinue = true;
						$cdispolinehold = $match[1];
					}
					else {
						$cdispocontinue = false;
						$myhash2 = $this->parseContentDispositionLine($match[1]);
						$my_mime_block->setMimeContentDisposition($myhash2[type]);
						$my_mime_block->setMimeContentDispositionName($myhash2[name]);
					}

	   	  		// -------------------------------------------------------
	   	  		} elseif ($ctypecontinue && !eregi(":",$line)) {
	   	  			//
	   	  			// the line is a continue line for content-type
	   	  			//
	   	  			if(substr($line, strlen($line)-1,1)==";") {
	   	  				//
	   	  				// the line will be continued again, so no object write just append the string
	   	  				//
	   	  				$ctypelinehold .= $line;
	   	  			}
	   	  			else {
	   	  				//
	   	  				// the line will not be continued, so append string and write to object attributes
	   	  				//
	   	  				$ctypelinehold .= $line;
	   	  				$myhash = $this->parseContentTypeLine($ctypelinehold);
						$my_mime_block->setMimeContentType($myhash[type]);
						$my_mime_block->setMimeContentTypeBoundary($myhash[boundary]);
						$my_mime_block->setMimeContentTypeCharset($myhash[charset]);
						$my_mime_block->setMimeContentTypeName($myhash[name]);
						$ctypecontinue = false;
					}

	   	  		// -------------------------------------------------------
	   	  		} elseif ($cdispocontinue && !eregi(":",$line)) {
	   	  			//
	   	  			//  the line is a continue line for content-disposition
	   	  			//
	   	  			if(substr($line, strlen($line)-1,1)==";") {
	   	  				//
	   	  				// the line will be continued again, so no object write just append the string
	   	  				//
	   	  				$cdispolinehold .= $line;
	   	  			}
	   	  			else {
	   	  				//
	   	  				// the line will not be continued, so append string and write to object attributes
	   	  				//
	   	  				$cdispolinehold .= $line;
	   	  				$myhash = $this->parseContentDispositionLine($cdispolinehold);
						$my_mime_block->setMimeContentDisposition($myhash[type]);
						$my_mime_block->setMimeContentDispositionName($myhash[name]);
						$cdispocontinue = false;
					}
				}
	   	  		// -------------------------------------------------------
				
				// we append each line oif the body and append manually a newline...
				if($body) {
					 $bodycontent .= $line . "\n";
				}

				if($line =="") $body = true;
			}
				
			if($line == "--" . $boundary OR $line == "--" . $boundary . "--") {
				$z++;
				$hit = true;
					
				// only add content to array if this is not the first MIME marker
				if($z>1) {
					$my_mime_block->setMimeContent($bodycontent);
					$this->mime_block[] = $my_mime_block;
					if($myhash[type]=="text/plain") {
						if($varhtmlentities) $this->emailtextplain = htmlentities($bodycontent);
						else $this->emailtextplain = $bodycontent;
					}
					
					if($myhash[type]=="text/html") {
						$this->emailtexthtml = $bodycontent;
						$this->isHTMLmail = true;
					}
				}
					
				// clear bodycontent var
				$bodycontent = ""; 
				
				// if boundary ends....leave the loop, because of performance gains with large attachment
				if($line == "--" . $boundary . "--") break;
			}
		}
	    
	    // for performance measuring
	    //$time_end = $this->getmicrotime();
		//$time = $time_end - $time_start;
		//echo "<h3>echo *LOOP Start: $time_start   End: $time_end    Diff: $time</h3>";
		
		if($newboundary != "" and $newboundary != "ThisIsAUniqueLineFromMarcLogemann") $this->parseBoundary($newboundary, $rawemail);
	}
	
	
	# function to grab all the different content-type data (like boundary, charset and type)
	# input w/o "Content-Type:" String
	# return hashmap $array[type], $array[boundary], $array[charset]
	
	
	function parseContentTypeLine($ctline)
	{
   		$ctline = trim($ctline);
   		   		
   		if(!strpos($ctline,";")) $endpointer = strlen($ctline); else $endpointer = strpos($ctline,";");
    	$hashmap[type] = substr($ctline,0,$endpointer);
    	
    	if(eregi("charset", $ctline)) {
    		if(strpos($ctline, ";", strpos($ctline, "charset"))>0) {
    			//
    			// if charset is not at the end of the string and therefore a colon is present
				//    			
    			$substrfrom = strpos($ctline,"=",strpos($ctline,"charset"))+1;
    			$substrtoA = strpos($ctline,";", strpos($ctline, "charset"));
    			$substrtoB = strpos($ctline,"=",strpos($ctline,"charset"))+1;
    	  				
    			$hashmap[charset] = str_replace("\"","", substr($ctline,	$substrfrom, $substrtoA - $substrtoB));
    	  		}
    	  	else {
    			//
    			// if charset is at the end of the content-type line, so there is only a \n at the end
				//
    			$hashmap[charset] = str_replace("\"","", substr($ctline, 
    	  							strpos($ctline,"=",strpos($ctline,"charset"))+1,
    	  							strlen($ctline)- strpos($ctline,"=", strpos($ctline, "charset"))));
    	  	}    	  				
    	}
    	  		
    	  		
    	if(eregi("boundary", $ctline)) $hashmap[boundary] = str_replace("\"","", substr($ctline, 
    	  												strpos($ctline,"=",strpos($ctline,"boundary"))+1,
    	  												strlen($ctline)- strpos($ctline,"=")));
    	if(eregi("name", $ctline)) $hashmap[name] = str_replace("\"","", substr($ctline, 
    	  												strpos($ctline,"=",strpos($ctline,"name"))+1,
    	  												strlen($ctline)- strpos($ctline,"=")));
    	return $hashmap;
	}

	# function to grab all the different content-type data (like boundary, charset and type)
	# input w/o "Content-Type:" String
	# return hashmap $array[type], $array[boundary], $array[charset]
	
	
	function parseContentDispositionLine($cdline)
	{
   		$cdline = trim($cdline);

   		if(!strpos($cdline,";")) $endpointer = strlen($cdline); else $endpointer = strpos($cdline,";");
    	$hashmap[type] = trim(substr($cdline,0,$endpointer));
    	
    	if(eregi("filename", $cdline)) $hashmap[name] = str_replace("\"","", substr($cdline, 
    	  												strpos($cdline,"=",strpos($cdline,"filename"))+1,
    	  												strlen($cdline)- strpos($cdline,"=")));
    	return $hashmap;
	}
}

# Class mime_block
# 
# If you have a MIME email, you have several MIME blocks in there (boundaries), these
# blocks have their own content-type, transfer encoding and content itself
# this class represents the data in such a boundary
#
# Methods
#
# getter and setter Methods for each attribute, its not mandatory to use them, but
# because i programmed a lot java these days, i just cant manipulate the attributes
# directly :) some kind of a psycho thing .... hehe
# 
# 

class mime_block {
	
	var $mime_contenttype; 				# content-type (i.e. text/plain)
    var $mime_contenttypebound;			# coontent-type boundary marker
    var $mime_contenttypecharset; 		# content-type charset (i.e. iso-8859-1)
    var $mime_contenttypename;			# content-type name (i.e. filename.txt)

    var $mime_transferenc;				# transfer-encoding (i.e. base64)
	
	var $mime_contentdisposition;		# content-disposition (i.e. attachment or inline)
	var $mime_contentdispositionname; 	# content-disposition name (i.e. filename.txt)

    var $mime_content;					# content  (could be base64 encoded binary)

	function setMimeContentType($ctype) {
		$this->mime_contenttype = $ctype;
	}
	
	function getMimeContentType() {
		return $this->mime_contenttype;
	}
	
	function setMimeContentTypeBoundary($boundary) {
		$this->mime_contenttypebound = $boundary;
	}
	
	function getMimeContentTypeBoundary() {
		return $this->mime_contenttypebound;
	}
	
	function setMimeContentTypeCharset($charset) {
		$this->mime_contenttypecharset = $charset;
	}
		
	function getMimeContentTypeCharset() {
		return $this->mime_contenttypecharset;
	}
	
	function setMimeContentTypeName($name) {
		$this->mime_contenttypename = $name;
	}
	
	function getMimeContentTypeName() {
		return $this->mime_contenttypename;
	}
	
	function setMimeTransferEncoding($transenc) {
		$this->mime_transferenc = $transenc;
	}
	
	function getMimeTransferEncoding() {
		return $this->mime_transferenc;
	}
	
	function setMimeContentDisposition($cdispo) {
		$this->mime_contentdisposition = $cdispo;
	}

	function getMimeContentDisposition() {
		return $this->mime_contentdisposition;
	}

	function setMimeContentDispositionName($cdisponame) {
		$this->mime_contentdispositionname = $cdisponame;
	}

	function getMimeContentDispositionName() {
		return $this->mime_contentdispositionname;
	}

	function setMimeContent($content) {
		$this->mime_content = $content;
	}
	
	function getMimeContent() {
		return $this->mime_content;
	}
}
    
?>