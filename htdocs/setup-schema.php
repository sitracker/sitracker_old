<?php
// setup-schema.php - Defines database schema for use in setup.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

// TODO we need to clean this schema up to make it confirmed compatible with mysql4
$schema = "CREATE TABLE `billing_periods` (
`servicelevelid` INT( 5 ) NOT NULL ,
`engineerperiod` INT NOT NULL COMMENT 'In minutes',
`customerperiod` INT NOT NULL COMMENT 'In minutes',
PRIMARY KEY r( `servicelevelid` )
) ENGINE = MYISAM ;


CREATE TABLE `closingstatus` (
 `id` int(11) NOT NULL auto_increment,
 `name` varchar(50) default NULL,
 PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

INSERT INTO `closingstatus` VALUES (1, 'Sent Information');
INSERT INTO `closingstatus` VALUES (2, 'Solved Problem');
INSERT INTO `closingstatus` VALUES (3, 'Reported Bug');
INSERT INTO `closingstatus` VALUES (4, 'Action Taken');
INSERT INTO `closingstatus` VALUES (5, 'Duplicate');
INSERT INTO `closingstatus` VALUES (6, 'No Longer Relevant');
INSERT INTO `closingstatus` VALUES (7, 'Unsupported');
INSERT INTO `closingstatus` VALUES (8, 'Support Expired');
INSERT INTO `closingstatus` VALUES (9, 'Unsolved');
INSERT INTO `closingstatus` VALUES (10, 'Escalated');

CREATE TABLE `contactflags` (
 `contactid` int(11) default NULL,
 `flag` char(3) NOT NULL default '',
  KEY `contactid` (`contactid`),
  KEY `flag` (`flag`)
) ENGINE=MyISAM;

CREATE TABLE `contactproducts` (
 `id` int(11) NOT NULL auto_increment,
 `contactid` int(11) default NULL,
 `productid` int(11) default NULL,
 `maintenancecontractid` int(11) default NULL,
 `maintenancecontactid` int(11) default NULL,
 `expirydate` int(11) default NULL,
 `incidentpoolid` int(11) NOT NULL default '0',
 `servicelevelid` int(11) NOT NULL default '1',
 PRIMARY KEY  (`id`),
 KEY `maintenancecontractid` (`maintenancecontractid`),
 KEY `maintenancecontactid` (`maintenancecontactid`),
 KEY `incidentpoolid` (`incidentpoolid`),
 KEY `servicelevelid` (`servicelevelid`)
) ENGINE=MyISAM;


CREATE TABLE `contacts` (
  `id` int(11) NOT NULL auto_increment,
  `notify_contactid` int(11) NOT NULL default '0',
  `username` varchar(50) default NULL,
  `password` varchar(50) default NULL,
  `forenames` varchar(100) NOT NULL default '',
  `surname` varchar(100) NOT NULL default '',
  `jobtitle` varchar(255) NOT NULL default '',
  `salutation` varchar(50) NOT NULL default '',
  `siteid` int(11) NOT NULL default '0',
  `email` varchar(100) default NULL,
  `phone` varchar(50) default NULL,
  `mobile` varchar(50) NOT NULL default '',
  `fax` varchar(50) default NULL,
  `department` varchar(255) default NULL,
  `address1` varchar(255) default NULL,
  `address2` varchar(255) NOT NULL default '',
  `city` varchar(255) NOT NULL default '',
  `county` varchar(255) NOT NULL default '',
  `country` varchar(255) NOT NULL default '',
  `postcode` varchar(255) NOT NULL default '',
  `dataprotection_email` enum('No','Yes') default 'No',
  `dataprotection_phone` enum('No','Yes') default 'No',
  `dataprotection_address` enum('No','Yes') default 'No',
  `timestamp_added` int(11) default NULL,
  `timestamp_modified` int(11) default NULL,
  `notes` blob NOT NULL,
  `active` enum('true','false') NOT NULL default 'true',
  PRIMARY KEY  (`id`),
  KEY `siteid` (`siteid`),
  KEY `username` (`username`),
  KEY `forenames` (`forenames`),
  KEY `surname` (`surname`),
  KEY `notify_contactid` (`notify_contactid`)
) ENGINE=MyISAM;

INSERT INTO `contacts` (`id`, `notify_contactid`, `username`, `password`, `forenames`, `surname`, `jobtitle`, `salutation`, `siteid`, `email`, `phone`, `mobile`, `fax`, `department`, `address1`, `address2`, `city`, `county`, `country`, `postcode`, `dataprotection_email`, `dataprotection_phone`, `dataprotection_address`, `timestamp_added`, `timestamp_modified`, `notes`) VALUES
(1, 4, 'Acme1', '2830', 'John', 'Acme', 'Chairman', 'Mr', 1, 'acme@example.com', '0666 222111', '', '', '', '', '', '', '', '', '', 'Yes', 'Yes', 'Yes', 1132930556, 1187360933, '');

CREATE TABLE `dashboard` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `enabled` enum('true','false') NOT NULL default 'false',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

INSERT INTO `dashboard` (`id`, `name`, `enabled`) VALUES (1, 'random_tip', 'true'),
(2, 'statistics', 'true'),
(3, 'tasks', 'true'),
(4, 'user_incidents', 'true');

CREATE TABLE `drafts` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `incidentid` int(11) NOT NULL,
  `type` enum('update','email') NOT NULL,
  `content` text NOT NULL,
  `meta` text NOT NULL,
  `lastupdate` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

CREATE TABLE `emailsig` (
  `id` int(11) NOT NULL auto_increment,
  `signature` text NOT NULL,
  PRIMARY KEY  (`id`)
)  ENGINE=MyISAM COMMENT='Global Email Signature' ;

INSERT INTO `emailsig` (`id`, `signature`) VALUES (1, '--\r\n... Powered by Open Source Software: Support Incident Tracker (SiT!) is available free from http://sourceforge.net/projects/sitracker/');

CREATE TABLE `emailtype` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `type` enum('user','system') NOT NULL default 'user',
  `description` text NOT NULL,
  `tofield` varchar(100) default NULL,
  `fromfield` varchar(100) default NULL,
  `replytofield` varchar(100) default NULL,
  `ccfield` varchar(100) default NULL,
  `bccfield` varchar(100) default NULL,
  `subjectfield` varchar(255) default NULL,
  `body` text,
  `customervisibility` enum('show','hide') NOT NULL default 'show',
  `storeinlog` enum('No','Yes') NOT NULL default 'Yes',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

-- FIXME remove ID columns

INSERT INTO `emailtype` VALUES (1,'Support Email','user','','<contactemail>','<supportemail>','<supportemail>','','<useremail>','[<incidentid>] - <incidenttitle>','<contactfirstname>,\r\n\r\n<signature>\r\n<globalsignature>', 'show', 'yes');
INSERT INTO `emailtype` VALUES (2,'User Email','user','','<contactemail>','<useremail>','<useremail>','','','','<signature>\r\n<globalsignature>\r\n', 'show', 'yes');
INSERT INTO `emailtype` VALUES (5,'INCIDENT_CLOSURE','system','Notify contact that the incident has been marked for closure and will be closed shortly','<contactemail>','<supportemail>','<supportemail>','','<useremail>','[<incidentid>] - <incidenttitle>','<contactfirstname>,\r\n\r\nIncident <incidentid> has been marked for closure. If you still have outstanding issues relating to this incident then please reply with details, otherwise it will be closed after the next seven days.\r\n\r\n<signature>\r\n<globalsignature>', 'show', 'yes');
INSERT INTO `emailtype` VALUES (12,'INCIDENT_LOGGED_CALL','system','Acknowledge the contacts telephone call and notify them of the new incident number','<contactemail>','<supportemail>','<supportemail>','','<useremail>','[<incidentid>] - <incidenttitle>','Thank you for your call. The incident <incidentid> has been generated and your details stored in our tracking system. \r\n\r\nYou will be receiving a response from one of our product specialists as soon as possible. When referring to this incident please remember to quote incident <incidentid> in \r\nall communications. \r\n\r\nFor all email communications please title your email as [<incidentid>] - <incidenttitle>\r\n\r\n<globalsignature>\r\n', 'show', 'no');
INSERT INTO `emailtype` VALUES (13,'INCIDENT_CLOSED','system','Notify contact that an incident has now been closed','<contactemail>','<supportemail>','<supportemail>','','','[<incidentid>] - <incidenttitle> - Closed','This is an automated message to let you know that Incident <incidentid> has now been closed. \r\n\r\n<globalsignature>', 'show', 'yes');
INSERT INTO `emailtype` VALUES (42, 'OUT_OF_SLA', 'system', '', '<supportmanager>', '<supportemail>', '<supportemail>', '<useremail>', '', '<applicationshortname> SLA: Incident <incidentid> now outside SLA', 'This is an automatic notification that this incident has gone outside its SLA.  The SLA target <info1> expired <info2> minutes ago.\n\nIncident: [<incidentid>] - <incidenttitle>\nOwner: <incidentowner>\nPriority: <incidentpriority>\nExternal Id: <incidentexternalid>\nExternal Engineer: <incidentexternalengineer>\nSite: <contactsite>\nContact: <contactname>\n\n--\n<applicationshortname> v<applicationversion>\n<todaysdate>\n', 'hide', 'yes');
INSERT INTO `emailtype` VALUES (43, 'OUT_OF_REVIEW', 'system', '', '<supportmanager>', '<useremail>', '<supportemail>', '<supportemail>', '', '<applicationshortname> Review: Incident <incidentid> due for review soon', 'This is an automatic notification that this incident [<incidentid>] will soon be due for review.\n\nIncident: [<incidentid>] - <incidenttitle>\nEngineer: <incidentowner>\nPriority: <incidentpriority>\nExternal Id: <incidentexternalid>\nExternal Engineer: <incidentexternalengineer>\nSite: <contactsite>\nContact: <contactname>\n\n--\n<applicationshortname> v<applicationversion>\n<todaysdate>', 'hide', 'yes');
INSERT INTO `emailtype` VALUES (48,'INCIDENT_UPDATED','system','Acknoweldge contacts email and update to incident','<contactemail>','<supportemail>','<supportemail>','','','[<incidentid>] - <incidenttitle>','Thank you for your email. The incident <incidentid> has been updated and your details stored in our support database. \r\n\r\nYou will be receiving a response from one of our product specialists as soon as possible.\r\n\r\n<globalsignature>', 'show', 'no');
INSERT INTO `emailtype` VALUES (49,'INCIDENT_CLOSED_EXTERNAL','system','Notify external engineer that an incident has been closed','<incidentexternalemail>','<supportemail>','<supportemail>','','','Incident ref #<incidentexternalid>  - <incidenttitle> CLOSED - [<incidentid>]','<incidentexternalengineerfirstname>,\r\n\r\nThis is an automated email to let you know that Incident <incidentexternalid> has been closed within our tracking system.\r\n\r\nMany thanks for your help.\r\n\r\n<signature>\r\n<globalsignature>', 'hide', 'no');
INSERT INTO `emailtype` VALUES (9,'INCIDENT_LOGGED_EMAIL','system','Acknowledge the contacts email and notify them of the new incident number','<contactemail>','<supportemail>','<supportemail>','','<useremail>','[<incidentid>] - <incidenttitle>','Thank you for your email. The incident <incidentid> has been generated and your details stored in our tracking system. \r\n\r\nYou will be receiving a response from one of our product specialists as soon as possible. When referring to this incident please remember to quote incident <incidentid> in \r\nall communications.\r\n\r\nFor all email communications please title your email as [<incidentid>] - <incidenttitle>\r\n\r\n<globalsignature>', 'show', 'no');
INSERT INTO `emailtype` (`name`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`) VALUES ('INCIDENT_REASSIGNED_USER_NOTIFY', 'system', 'Notify user when call assigned to them', '<incidentreassignemailaddress>', '<supportemail>', '<supportemail>', '', '', 'A <incidentpriority> priority call ([<incidentid>] - <incidenttitle>) has been reassigned to you', 'Hi,\r\n\r\nIncident [<incidentid>] entitled <incidenttitle> has been reassigned to you.\r\n\r\nThe details of this incident are:\r\n\r\nPriority: <incidentpriority>\r\nContact: <contactname>\r\nSite: <contactsite>\r\n\r\n\r\nRegards\r\n<applicationname>\r\n\r\n\r\n---\r\n<todaysdate> - <applicationshortname> <applicationversion>', 'hide', 'No');
INSERT INTO `emailtype` (`name`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`) VALUES ('NEARING_SLA', 'system', 'Notification when an incident nears its SLA target', '<supportmanageremail>', '<supportemail>', '<supportemail>', '<useremail>', '', '<applicationshortname> SLA: Incident <incidentid> about to breach SLA', 'This is an automatic notification that this incident is about to breach its SLA.  The SLA target <info1> will expire in <info2> minutes.\r\n\r\nIncident: [<incidentid>] - <incidenttitle>\r\nOwner: <incidentowner>\r\nPriority: <incidentpriority>\r\nExternal Id: <incidentexternalid>\r\nExternal Engineer: <incidentexternalengineer>\r\nSite: <contactsite>\r\nContact: <contactname>\r\n\r\n--\r\n<applicationshortname> v<applicationversion>\r\n<todaysdate>\r\n', 'hide', 'Yes');

CREATE TABLE `escalationpaths` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `track_url` varchar(255) default NULL,
  `home_url` varchar(255) NOT NULL default '',
  `url_title` varchar(255) default NULL,
  `email_domain` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

CREATE TABLE `feedbackforms` (
  `id` int(5) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `introduction` text NOT NULL,
  `thanks` text NOT NULL,
  `description` text NOT NULL,
  `multi` enum('yes','no') NOT NULL default 'no',
  PRIMARY KEY  (`id`),
  KEY `multi` (`multi`)
) ENGINE=MyISAM;

CREATE TABLE `feedbackquestions` (
  `id` int(5) NOT NULL auto_increment,
  `formid` int(5) NOT NULL default '0',
  `question` varchar(255) NOT NULL default '',
  `questiontext` text NOT NULL,
  `sectiontext` text NOT NULL,
  `taborder` int(5) NOT NULL default '0',
  `type` varchar(255) NOT NULL default 'text',
  `required` enum('true','false') NOT NULL default 'false',
  `options` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `taborder` (`taborder`),
  KEY `type` (`type`),
  KEY `formid` (`formid`)
) ENGINE=MyISAM;


CREATE TABLE `feedbackreport` (
  `id` int(5) NOT NULL default '0',
  `formid` int(5) NOT NULL default '0',
  `respondent` int(11) NOT NULL default '0',
  `responseref` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `completed` enum('yes','no') NOT NULL default 'no',
  `created` timestamp(14) NOT NULL,
  `incidentid` int(5) NOT NULL default '0',
  `contactid` int(5) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `responseref` (`responseref`),
  KEY `formid` (`formid`),
  KEY `respondant` (`respondent`),
  KEY `completed` (`completed`),
  KEY `incidentid` (`incidentid`),
  KEY `contactid` (`contactid`)
) ENGINE=MyISAM;

CREATE TABLE `feedbackrespondents` (
  `id` int(5) NOT NULL auto_increment,
  `formid` int(5) NOT NULL default '0',
  `contactid` int(11) NOT NULL default '0',
  `incidentid` int(11) NOT NULL default '0',
  `email` varchar(255) NOT NULL default '',
  `completed` enum('yes','no') NOT NULL default 'no',
  `created` timestamp(14) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `responseref` (`incidentid`),
  KEY `formid` (`formid`),
  KEY `contactid` (`contactid`),
  KEY `completed` (`completed`)
) ENGINE=MyISAM;

CREATE TABLE `feedbackresults` (
  `id` int(5) NOT NULL auto_increment,
  `respondentid` int(5) NOT NULL default '0',
  `questionid` int(5) NOT NULL default '0',
  `result` varchar(255) NOT NULL default '',
  `resulttext` text,
  PRIMARY KEY  (`id`),
  KEY `questionid` (`questionid`),
  KEY `respondentid` (`respondentid`)
) ENGINE=MyISAM;


CREATE TABLE `files` (
  `id` int(11) NOT NULL auto_increment,
  `category` enum('public','private','protected') NOT NULL default 'public',
  `filename` varchar(255) NOT NULL default '',
  `size` bigint(11) NOT NULL default '0',
  `userid` int(11) NOT NULL default '0',
  `shortdescription` varchar(255) NOT NULL default '',
  `longdescription` blob NOT NULL,
  `webcategory` varchar(255) NOT NULL default '',
  `path` varchar(255) NOT NULL default '',
  `downloads` int(11) NOT NULL default '0',
  `filedate` int(11) NOT NULL default '0',
  `expiry` int(11) NOT NULL default '0',
  `fileversion` varchar(50) NOT NULL default '',
  `productid` int(11) NOT NULL default '0',
  `releaseid` int(11) NOT NULL default '0',
  `published` enum('yes','no') NOT NULL default 'no',
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`),
  KEY `productid` (`productid`),
  KEY `category` (`category`),
  KEY `filename` (`filename`),
  KEY `published` (`published`),
  KEY `webcategory` (`webcategory`)
) ENGINE=MyISAM;


CREATE TABLE `flags` (
  `flag` char(3) NOT NULL default '',
  `name` varchar(100) default NULL,
  PRIMARY KEY  (`flag`),
  KEY `flag` (`flag`)
) ENGINE=MyISAM;


CREATE TABLE `groups` (
  `id` int(5) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `imageurl` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM COMMENT='List of user groups' ;


CREATE TABLE `holidays` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(5) NOT NULL default '0',
  `type` int(11) NOT NULL default '1',
  `startdate` int(11) NOT NULL default '0',
  `length` enum('am','pm','day') NOT NULL default 'day',
  `approved` tinyint(1) NOT NULL default '0',
  `approvedby` int(5) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`),
  KEY `startdate` (`startdate`),
  KEY `type` (`type`),
  KEY `approved` (`approved`)
) ENGINE=MyISAM;

CREATE TABLE `incidentpools` (
  `id` int(11) NOT NULL auto_increment,
  `maintenanceid` int(11) NOT NULL default '0',
  `siteid` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `incidentsremaining` int(5) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `maintenanceid` (`maintenanceid`)
) ENGINE=MyISAM;


CREATE TABLE `incidentproductinfo` (
  `id` int(11) NOT NULL auto_increment,
  `incidentid` int(11) default NULL,
  `productinfoid` int(11) default NULL,
  `information` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;


CREATE TABLE `incidents` (
  `id` int(11) NOT NULL auto_increment,
  `escalationpath` int(11) default NULL,
  `externalid` varchar(50) default NULL,
  `externalengineer` varchar(80) NOT NULL default '',
  `externalemail` varchar(255) NOT NULL default '',
  `ccemail` varchar(255) default NULL,
  `title` varchar(150) default NULL,
  `owner` tinyint(4) default NULL,
  `towner` tinyint(4) NOT NULL default '0',
  `contact` int(11) default '0',
  `priority` tinyint(4) default NULL,
  `servicelevel` varchar(10) default NULL,
  `status` tinyint(4) default NULL,
  `type` enum('Support','Sales','Other','Free') default 'Support',
  `maintenanceid` int(11) NOT NULL default '0',
  `product` int(11) default NULL,
  `softwareid` int(5) NOT NULL default '0',
  `productversion` varchar(50) default NULL,
  `productservicepacks` varchar(100) default NULL,
  `opened` int(11) default NULL,
  `lastupdated` int(11) default NULL,
  `timeofnextaction` int(11) default '0',
  `closed` int(11) default '0',
  `closingstatus` tinyint(4) default NULL,
  `slaemail` tinyint(1) NOT NULL default '0',
  `slanotice` tinyint(1) NOT NULL default '0',
  `locked` tinyint(4) NOT NULL default '0',
  `locktime` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `type` (`type`),
  KEY `owner` (`owner`),
  KEY `status` (`status`),
  KEY `priority` (`priority`),
  KEY `timeofnextaction` (`timeofnextaction`),
  KEY `maintenanceid` (`maintenanceid`),
  KEY `softwareid` (`softwareid`),
  KEY `contact` (`contact`),
  KEY `title` (`title`),
  KEY `opened` (`opened`),
  KEY `closed` (`closed`),
  KEY `servicelevel` (`servicelevel`)
) ENGINE=MyISAM ;

CREATE TABLE `incidentstatus` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `ext_name` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=10 ENGINE=MyISAM;

INSERT INTO `incidentstatus` VALUES (0, 'Active (Unassigned)', 'Active');
INSERT INTO `incidentstatus` VALUES (1, 'Active', 'Active');
INSERT INTO `incidentstatus` VALUES (2, 'Closed', 'Closed');
INSERT INTO `incidentstatus` VALUES (3, 'Research Needed', 'Research');
INSERT INTO `incidentstatus` VALUES (4, 'Called And Left Message', 'Called And Left Message');
INSERT INTO `incidentstatus` VALUES (5, 'Awaiting Colleague Response', 'Internal Escalation');
INSERT INTO `incidentstatus` VALUES (6, 'Awaiting Support Response', 'External Escalation');
INSERT INTO `incidentstatus` VALUES (7, 'Awaiting Closure', 'Awaiting Closure');
INSERT INTO `incidentstatus` VALUES (8, 'Awaiting Customer Action', 'Customer has Action');
INSERT INTO `incidentstatus` VALUES (9, 'Unsupported', 'Unsupported');



CREATE TABLE `interfacestyles` (
  `id` int(5) NOT NULL,
  `name` varchar(50) NOT NULL default '',
  `cssurl` varchar(255) NOT NULL default '',
  `iconset` varchar(255) NOT NULL default 'sit',
  `headerhtml` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=16 ;

INSERT INTO `interfacestyles` (`id`, `name`, `cssurl`, `iconset`, `headerhtml`) VALUES (1, 'Light Blue', 'webtrack1.css', 'sit', ''),
(2, 'Grey', 'webtrack2.css', 'sit', ''),
(3, 'Green', 'webtrack3.css', 'sit', ''),
(4, 'Silver Blue', 'webtrack4.css', 'sit', ''),
(5, 'Classic', 'webtrack5.css', 'sit', ''),
(6, 'Orange', 'webtrack_ph2.css', 'sit', ''),
(7, 'Yellow and Blue', 'webtrack7.css', 'sit', ''),
(8, 'Neoteric', 'webtrack8.css', 'sit', ''),
(9, 'Toms Linux Style', 'webtrack9.css', 'sit', ''),
(10, 'Cool Blue', 'webtrack_ph.css', 'sit', ''),
(11, 'Just Light', 'webtrack10.css', 'sit', ''),
(12, 'Ex Pea', 'webtrack11.css', 'sit', ''),
(13, 'GUI Colours', 'webtrack12.css', 'sit', ''),
(14, 'Flashy', 'webtrack14/webtrack14.css', 'sit', ''),
(15, 'Richard', 'webtrack15.css', 'sit', '');


CREATE TABLE `journal` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL default '0',
  `timestamp` timestamp(14) NOT NULL,
  `event` varchar(40) NOT NULL default '',
  `bodytext` text NOT NULL,
  `journaltype` int(11) NOT NULL default '0',
  `refid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `refid` (`refid`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM;


CREATE TABLE `kbarticles` (
  `docid` int(5) NOT NULL auto_increment,
  `doctype` int(5) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `distribution` int(5) NOT NULL default '0',
  `published` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` varchar(255) NOT NULL default '',
  `reviewed` datetime NOT NULL default '0000-00-00 00:00:00',
  `reviewer` tinyint(4) NOT NULL default '0',
  `keywords` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`docid`),
  KEY `distribution` (`distribution`),
  KEY `title` (`title`)
) ENGINE=MyISAM COMMENT='Knowledge base articles' ;


CREATE TABLE `kbcontent` (
  `docid` int(5) NOT NULL default '0',
  `id` int(7) NOT NULL auto_increment,
  `ownerid` int(5) NOT NULL default '0',
  `headerstyle` char(2) NOT NULL default 'h1',
  `header` varchar(255) NOT NULL default '',
  `contenttype` int(5) NOT NULL default '1',
  `content` mediumtext NOT NULL,
  `distribution` enum('public','private','restricted') NOT NULL default 'private',
  PRIMARY KEY  (`id`),
  KEY `distribution` (`distribution`),
  KEY `ownerid` (`ownerid`),
  KEY `docid` (`docid`),
  FULLTEXT KEY `c_index` (`content`)
) ENGINE=MyISAM;


CREATE TABLE `kbsoftware` (
  `docid` int(5) NOT NULL default '0',
  `softwareid` int(5) NOT NULL default '0',
  PRIMARY KEY  (`docid`,`softwareid`)
) ENGINE=MyISAM COMMENT='Links kb articles with software';



CREATE TABLE `licencetypes` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;


INSERT INTO `licencetypes` VALUES (1, 'Per User');
INSERT INTO `licencetypes` VALUES (2, 'Per Workstation');
INSERT INTO `licencetypes` VALUES (3, 'Per Server');
INSERT INTO `licencetypes` VALUES (4, 'Site');
INSERT INTO `licencetypes` VALUES (5, 'Evaluation');

CREATE TABLE `links` (
     `linktype` int(11) NOT NULL default '0',
     `origcolref` int(11) NOT NULL default '0',
     `linkcolref` int(11) NOT NULL default '0',
     `direction` enum('left','right','bi') NOT NULL default 'left',
     `userid` tinyint(4) NOT NULL default '0',
     PRIMARY KEY  (`linktype`,`origcolref`,`linkcolref`),
     KEY `userid` (`userid`)
   ) ENGINE=MyISAM ;

CREATE TABLE `linktypes` (
     `id` int(11) NOT NULL auto_increment,
     `name` varchar(255) NOT NULL default '',
     `lrname` varchar(255) NOT NULL default '',
     `rlname` varchar(255) NOT NULL default '',
     `origtab` varchar(255) NOT NULL default '',
     `origcol` varchar(255) NOT NULL default '',
     `linktab` varchar(255) NOT NULL default '',
     `linkcol` varchar(255) NOT NULL default 'id',
     `selectionsql` varchar(255) NOT NULL default '',
     `filtersql` varchar(255) NOT NULL default '',
     `viewurl` varchar(255) NOT NULL default '',
     PRIMARY KEY  (`id`),
     KEY `origtab` (`origtab`),
     KEY `linktab` (`linktab`)
   ) ENGINE=MyISAM;


INSERT INTO `linktypes` VALUES (1,'Task','Subtask','Parent Task','tasks','id','tasks','id','name','','view_task.php?id=%id%'),(2,'Contact','Contact','Contact Task','tasks','id','contacts','id','forenames','','contact_details.php?id=%id%'),(3,'Site','Site','Site Task','tasks','id','sites','id','name','','site_details.php?id=%id%'),(4,'Incident','Incident','Task','tasks','id','incidents','id','title','','incident_details.php?id=%id%');

CREATE TABLE `maintenance` (
  `id` int(11) NOT NULL auto_increment,
  `site` int(11) default NULL,
  `product` int(11) default NULL,
  `reseller` int(11) default NULL,
  `expirydate` int(11) default NULL,
  `licence_quantity` int(11) default NULL,
  `licence_type` int(11) default NULL default 6,
  `incident_quantity` int(5) NOT NULL default '0',
  `incidents_used` int(5) NOT NULL default '0',
  `notes` text,
  `admincontact` int(11) default NULL,
  `productonly` enum('yes','no') NOT NULL default 'no',
  `term` enum('no','yes') default 'no',
  `servicelevelid` int(11) NOT NULL default '1',
  `incidentpoolid` int(11) NOT NULL default '0',
  `supportedcontacts` INT( 255 ) NOT NULL DEFAULT '0',
  `allcontactssupported` ENUM( 'No', 'Yes' ) NOT NULL DEFAULT 'No',
  PRIMARY KEY  (`id`),
  KEY `site` (`site`),
  KEY `productonly` (`productonly`)
) ENGINE=MyISAM;

-- FIXME - decide what the last two fields should be by default
INSERT INTO `maintenance`(id, site, product, reseller, expirydate, licence_quantity, licence_type, incident_quantity, incidents_used, notes, admincontact, productonly, term, servicelevelid, incidentpoolid) VALUES (1,1,1,2,1268179200,1,4,0,0,'This is an example contract.',1,'no','no',0,0);

CREATE TABLE `notes` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL default '0',
  `timestamp` timestamp(14) NOT NULL,
  `bodytext` text NOT NULL,
  `link` int(11) NOT NULL default '0',
  `refid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `refid` (`refid`),
  KEY `userid` (`userid`),
  KEY `link` (`link`)
) ENGINE=MyISAM ;

CREATE TABLE `permissions` (
  `id` int(5) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;


INSERT INTO `permissions` VALUES (1, 'Add new contacts');
INSERT INTO `permissions` VALUES (2, 'Add new sites');
INSERT INTO `permissions` VALUES (3, 'Edit existing site details');
INSERT INTO `permissions` VALUES (4, 'Edit your profile');
INSERT INTO `permissions` VALUES (5, 'Add Incidents');
INSERT INTO `permissions` VALUES (6, 'View Incidents');
INSERT INTO `permissions` VALUES (7, 'Edit Incidents');
INSERT INTO `permissions` VALUES (8, 'Update Incidents');
INSERT INTO `permissions` VALUES (9, 'Edit User Permissions');
INSERT INTO `permissions` VALUES (10, 'Edit contacts');
INSERT INTO `permissions` VALUES (11, 'View Sites');
INSERT INTO `permissions` VALUES (12, 'View Contacts');
INSERT INTO `permissions` VALUES (13, 'Reassign Incidents');
INSERT INTO `permissions` VALUES (14, 'View Users');
INSERT INTO `permissions` VALUES (15, 'Add Supported Products');
INSERT INTO `permissions` VALUES (16, 'Add Email Templates');
INSERT INTO `permissions` VALUES (17, 'Edit Email Templates');
INSERT INTO `permissions` VALUES (18, 'Close Incidents');
INSERT INTO `permissions` VALUES (19, 'View Maintenance Contracts');
INSERT INTO `permissions` VALUES (20, 'Add Users');
INSERT INTO `permissions` VALUES (21, 'Edit Maintenance Contracts');
INSERT INTO `permissions` VALUES (22, 'Administrate');
INSERT INTO `permissions` VALUES (23, 'Edit User');
INSERT INTO `permissions` VALUES (24, 'Add Product');
INSERT INTO `permissions` VALUES (25, 'Add Product Information');
INSERT INTO `permissions` VALUES (26, 'Get Help');
INSERT INTO `permissions` VALUES (27, 'View Your Calendar');
INSERT INTO `permissions` VALUES (28, 'View Products and Software');
INSERT INTO `permissions` VALUES (29, 'Edit Products');
INSERT INTO `permissions` VALUES (30, 'View Supported Products');
INSERT INTO `permissions` VALUES (32, 'Edit Supported Products');
INSERT INTO `permissions` VALUES (33, 'Send Emails');
INSERT INTO `permissions` VALUES (34, 'Reopen Incidents');
INSERT INTO `permissions` VALUES (35, 'Set your status');
INSERT INTO `permissions` VALUES (36, 'Set contact flags');
INSERT INTO `permissions` VALUES (37, 'Run Reports');
INSERT INTO `permissions` VALUES (38, 'View Sales Incidents');
INSERT INTO `permissions` VALUES (39, 'Add Maintenance Contract');
INSERT INTO `permissions` VALUES (40, 'Reassign Incident when user not accepting');
INSERT INTO `permissions` VALUES (41, 'View Status');
INSERT INTO `permissions` VALUES (42, 'Review/Delete Incident updates');
INSERT INTO `permissions` VALUES (43, 'Edit Global Signature');
INSERT INTO `permissions` VALUES (44, 'Publish files to FTP site');
INSERT INTO `permissions` VALUES (45, 'View Mailing List Subscriptions');
INSERT INTO `permissions` VALUES (46, 'Edit Mailing List Subscriptions');
INSERT INTO `permissions` VALUES (47, 'Administrate Mailing Lists');
INSERT INTO `permissions` VALUES (48, 'Add Feedback Forms');
INSERT INTO `permissions` VALUES (49, 'Edit Feedback Forms');
INSERT INTO `permissions` VALUES (50, 'Approve Holidays');
INSERT INTO `permissions` VALUES (51, 'View Feedback');
INSERT INTO `permissions` VALUES (52, 'View Hidden Updates');
INSERT INTO `permissions` VALUES (53, 'Edit Service Levels');
INSERT INTO `permissions` VALUES (54, 'View KB Articles');
INSERT INTO `permissions` VALUES (55, 'Delete Sites/Contacts');
INSERT INTO `permissions` VALUES (56, 'Add Software');
INSERT INTO `permissions` VALUES (57, 'Disable User Accounts');
INSERT INTO `permissions` VALUES (58, 'Edit your Software Skills');
INSERT INTO `permissions` VALUES (59, 'Manage users software skills');
INSERT INTO `permissions` VALUES (60, 'Perform Searches');
INSERT INTO `permissions` VALUES (61, 'View Incident Details');
INSERT INTO `permissions` VALUES (62, 'View Incident Attachments');
INSERT INTO `permissions` VALUES (63, 'Add Reseller');
INSERT INTO `permissions` VALUES (64, 'Manage Escalation Paths');
INSERT INTO `permissions` VALUES (65, 'Delete Products');
INSERT INTO `permissions` VALUES (66, 'Install Dashboard Components');
INSERT INTO `permissions` VALUES (67, 'Run Management Reports');
INSERT INTO `permissions` VALUES (68, 'Manage Holidays');
INSERT INTO `permissions` VALUES (69, 'Change User Statuses');
INSERT INTO `permissions` VALUES (70, 'Post Global Notices');


CREATE TABLE `priority` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM COMMENT='Used in incidents.php' AUTO_INCREMENT=5 ;

INSERT INTO `priority` VALUES (1, 'Low');
INSERT INTO `priority` VALUES (2, 'Medium');
INSERT INTO `priority` VALUES (3, 'High');
INSERT INTO `priority` VALUES (4, 'Critical');



CREATE TABLE `productinfo` (
  `id` int(11) NOT NULL auto_increment,
  `productid` int(11) default NULL,
  `information` text,
  `moreinformation` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;


CREATE TABLE `products` (
  `id` int(11) NOT NULL auto_increment,
  `vendorid` int(5) NOT NULL default '0',
  `name` varchar(50) default NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `vendorid` (`vendorid`),
  KEY `name` (`name`)
) ENGINE=MyISAM COMMENT='Current List of Products' ;

INSERT INTO `products` VALUES (1,1,'Example Product','This is an example product.');

CREATE TABLE `relatedincidents` (
`id` INT( 5 ) NOT NULL AUTO_INCREMENT ,
`incidentid` INT( 5 ) NOT NULL ,
`relation` ENUM( 'child', 'sibling' ) DEFAULT 'child' NOT NULL ,
`relatedid` INT( 5 ) NOT NULL ,
PRIMARY KEY ( `id` ) ,
INDEX ( `incidentid` , `relatedid` )
) ENGINE=MyISAM;

CREATE TABLE `resellers` (
  `id` tinyint(4) NOT NULL auto_increment,
  `name` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

INSERT INTO `resellers` VALUES (1,'Us (No Reseller)');
INSERT INTO `resellers` VALUES (2,'Example Reseller');

CREATE TABLE `roles` (
`id` INT( 5 ) NOT NULL AUTO_INCREMENT ,
`rolename` VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE=MyISAM;

INSERT INTO `roles` ( `id` , `rolename` ) VALUES ('1', 'Administrator');
INSERT INTO `roles` ( `id` , `rolename` ) VALUES ('2', 'Manager');
INSERT INTO `roles` ( `id` , `rolename` ) VALUES ('3', 'User');

CREATE TABLE `rolepermissions` (
`roleid` tinyint( 4 ) NOT NULL default '0',
`permissionid` int( 5 ) NOT NULL default '0',
`granted` enum( 'true', 'false' ) NOT NULL default 'false',
PRIMARY KEY ( `roleid` , `permissionid` )
) ENGINE=MyISAM;

INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 1, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 2, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 3, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 4, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 5, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 6, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 7, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 8, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 9, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 10, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 11, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 12, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 13, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 14, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 15, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 16, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 17, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 18, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 19, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 20, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 21, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 22, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 23, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 24, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 25, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 26, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 27, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 28, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 29, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 30, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 32, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 33, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 34, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 35, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 36, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 37, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 38, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 39, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 40, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 41, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 42, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 43, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 44, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 45, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 46, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 47, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 48, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 49, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 50, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 51, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 52, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 53, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 54, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 55, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 56, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 57, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 58, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 59, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 60, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 61, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 62, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 63, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 64, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 65, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 66, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 67, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 68, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 69, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 70, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 1, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 2, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 3, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 4, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 5, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 6, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 7, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 8, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 10, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 11, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 12, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 13, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 14, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 15, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 16, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 17, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 18, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 19, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 21, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 24, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 25, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 26, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 27, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 28, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 29, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 30, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 32, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 33, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 34, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 35, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 36, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 37, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 38, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 39, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 40, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 41, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 42, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 43, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 44, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 45, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 46, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 47, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 48, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 49, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 50, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 51, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 52, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 53, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 54, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 55, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 56, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 58, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 59, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 60, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 61, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 62, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 67, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 1, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 2, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 3, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 4, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 5, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 6, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 7, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 8, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 10, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 11, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 12, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 13, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 14, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 18, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 19, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 26, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 27, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 28, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 30, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 33, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 34, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 35, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 36, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 37, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 38, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 41, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 44, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 52, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 54, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 58, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 60, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 61, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 62, 'true');

CREATE TABLE `servicelevels` (
  `id` int(5) NOT NULL default '0',
  `tag` varchar(10) NOT NULL default '',
  `priority` int(5) NOT NULL default '0',
  `initial_response_mins` int(11) NOT NULL default '0',
  `prob_determ_mins` int(11) NOT NULL default '0',
  `action_plan_mins` int(11) NOT NULL default '0',
  `resolution_days` float(5,2) NOT NULL default '0.00',
  `contact_days` int(11) NOT NULL default '0',
  `review_days` int(11) NOT NULL default '365',
  `timed` enum('yes','no') NOT NULL default 'no',
  PRIMARY KEY  (`tag`,`priority`),
  KEY `id` (`id`),
  KEY `review_days` (`review_days`)) ENGINE=MyISAM;


INSERT INTO `servicelevels` VALUES (0, 'standard', 1, 320, 380, 960, 14.00, 28, 90, 0);
INSERT INTO `servicelevels` VALUES (0, 'standard', 2, 240, 320, 960, 10.00, 20, 90, 0);
INSERT INTO `servicelevels` VALUES (0, 'standard', 3, 120, 180, 480, 7.00, 14, 90, 0);
INSERT INTO `servicelevels` VALUES (0, 'standard', 4, 60, 120, 240, 3.00, 6, 90, 0);

CREATE TABLE `set_tags` (
`id` INT NOT NULL ,
`type` MEDIUMINT NOT NULL ,
`tagid` INT NOT NULL ,
PRIMARY KEY ( `id` , `type` , `tagid` )
) ENGINE=MYISAM;

CREATE TABLE `sitecontacts` (
  `siteid` int(11) NOT NULL default '0',
  `contactid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`siteid`,`contactid`)
) ENGINE=MyISAM;

CREATE TABLE `sites` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `department` varchar(255) NOT NULL default '',
  `address1` varchar(255) NOT NULL default '',
  `address2` varchar(255) NOT NULL default '',
  `city` varchar(255) NOT NULL default '',
  `county` varchar(255) NOT NULL default '',
  `country` varchar(255) NOT NULL default '',
  `postcode` varchar(255) NOT NULL default '',
  `telephone` varchar(255) NOT NULL default '',
  `fax` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `websiteurl` varchar(255) default NULL,
  `notes` blob NOT NULL,
  `typeid` int(5) NOT NULL default '1',
  `freesupport` int(5) NOT NULL default '0',
  `licenserx` int(5) NOT NULL default '0',
  `ftnpassword` varchar(40) NOT NULL default '',
  `owner` tinyint(4) NOT NULL default '0',
  `active` enum('true','false') NOT NULL default 'true',
  PRIMARY KEY  (`id`),
  KEY `typeid` (`typeid`),
  KEY `owner` (`owner`)
) ENGINE=MyISAM;

INSERT INTO `sites` (`id`, `name`, `department`, `address1`, `address2`, `city`, `county`,
`country`, `postcode`, `telephone`, `fax`, `email`, `notes`, `typeid`, `freesupport`, `licenserx`,
 `owner`) VALUES (1, 'ACME Widgets Co.', 'Manufacturing Dept.', '21 Any Street', '',
'Anytown', 'Anyshire', 'UNITED KINGDOM', 'AN1 0TH', '0555 555555', '0444 444444', 'acme@example.com',
'Example site', 1, 0, 0, 0);

CREATE TABLE `sitetypes` (
  `typeid` int(5) NOT NULL auto_increment,
  `typename` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`typeid`)
) ENGINE=MyISAM;

INSERT INTO `sitetypes` VALUES (1, 'Unclassified');
INSERT INTO `sitetypes` VALUES (2, 'Commercial');
INSERT INTO `sitetypes` VALUES (3, 'Academic');

CREATE TABLE `software` (
  `id` int(5) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `software` int(5) NOT NULL default '0',
  `lifetime_start` date default NULL,
  `lifetime_end` date default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM COMMENT='Individual software products as they are supported' AUTO_INCREMENT=2 ;

INSERT INTO `software` (`id`, `name`, `lifetime_start`, `lifetime_end`) VALUES (1, 'Example Software', NULL, NULL);

CREATE TABLE `softwareproducts` (
  `productid` int(5) NOT NULL default '0',
  `softwareid` int(5) NOT NULL default '0',
  PRIMARY KEY  (`productid`,`softwareid`)
) ENGINE=MyISAM COMMENT='Table to link products with software';

INSERT INTO `softwareproducts` VALUES (1,1);


CREATE TABLE `spellcheck` (
  `id` int(11) NOT NULL auto_increment,
  `updateid` int(11) NOT NULL default '0',
  `bodytext` text NOT NULL,
  `newincidentstatus` int(11) default NULL,
  `timetonextaction_none` varchar(50) default NULL,
  `timetonextaction_days` int(11) default NULL,
  `timetonextaction_hours` int(11) default NULL,
  `timetonextaction_minutes` int(11) default NULL,
  `day` int(11) default NULL,
  `month` int(11) default NULL,
  `year` int(11) default NULL,
  `fromfield` varchar(255) default NULL,
  `replytofield` varchar(255) default NULL,
  `ccfield` varchar(255) default NULL,
  `bccfield` varchar(255) default NULL,
  `tofield` varchar(255) default NULL,
  `subjectfield` varchar(255) default NULL,
  `attachmenttype` varchar(255) default NULL,
  `filename` varchar(255) default NULL,
  `timestamp` timestamp(14) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `updateid` (`updateid`)
) ENGINE=MyISAM COMMENT='Temporary table used during spellcheck' ;


CREATE TABLE `supportcontacts` (
  `id` int(11) NOT NULL auto_increment,
  `maintenanceid` int(11) default NULL,
  `contactid` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

INSERT INTO `supportcontacts` VALUES (1,1,1);


CREATE TABLE `system` (
  `id` int(1) NOT NULL default '0',
  `version` float(3,2) NOT NULL default '0.00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

CREATE TABLE `tags` (
  `tagid` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`tagid`)
) ENGINE=MyISAM;


CREATE TABLE `tasks` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `description` text NOT NULL,
  `priority` tinyint(4) default NULL,
  `owner` tinyint(4) NOT NULL default '0',
  `duedate` datetime default NULL,
  `startdate` datetime default NULL,
  `enddate` datetime default NULL,
  `completion` tinyint(4) default NULL,
  `value` float(6,2) default NULL,
  `distribution` enum('public','private', 'incident') NOT NULL default 'public',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `lastupdated` timestamp(14) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `owner` (`owner`)
) ENGINE=MyISAM ;



CREATE TABLE `tempassigns` (
  `incidentid` int(5) NOT NULL default '0',
  `originalowner` int(5) NOT NULL default '0',
  `userstatus` tinyint(4) NOT NULL default '1',
  `assigned` enum('yes','no') NOT NULL default 'no',
  PRIMARY KEY  (`incidentid`,`originalowner`),
  KEY `assigned` (`assigned`)
) ENGINE=MyISAM;

CREATE TABLE `tempincoming` (
  `id` int(11) NOT NULL auto_increment,
  `updateid` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `incidentid` int(11) NOT NULL default '0',
  `from` varchar(255) default NULL,
  `subject` varchar(255) default NULL,
  `emailfrom` varchar(255) default NULL,
  `locked` tinyint(4) default NULL,
  `lockeduntil` datetime default NULL,
  `reason` varchar(255) default NULL,
  `contactid` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `updateid` (`updateid`)
) ENGINE=MyISAM COMMENT='Temporary store for incoming attachment paths' ;

CREATE TABLE `updates` (
  `id` int(11) NOT NULL auto_increment,
  `incidentid` int(11) default NULL,
  `userid` int(11) default NULL,
  `type` enum('default','editing','opening','email','reassigning','closing','reopening','auto','phonecallout','phonecallin','research','webupdate','emailout','emailin','externalinfo','probdef','solution','actionplan','slamet','reviewmet','tempassigning', 'auto_chase_email', 'auto_chase_phone', 'auto_chase_manager','auto_chased_phone','auto_chased_manager','auto_chase_managers_manager', 'customerclosurerequest', 'fromtask') default 'default',
  `currentowner` tinyint(4) NOT NULL default '0',
  `currentstatus` int(11) NOT NULL default '0',
  `bodytext` text,
  `timestamp` int(11) default NULL,
  `nextaction` varchar(50) NOT NULL default '',
  `customervisibility` enum('show','hide','unset') default 'unset',
  `sla` enum('opened','initialresponse','probdef','actionplan','solution','closed') default NULL,
  `duration` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `currentowner` (`currentowner`,`currentstatus`),
  KEY `incidentid` (`incidentid`),
  KEY `timestamp` (`timestamp`),
  KEY `type` (`type`)
) ENGINE=MyISAM;


CREATE TABLE `usergroups` (
  `userid` int(5) NOT NULL default '0',
  `groupid` int(5) NOT NULL default '0',
  PRIMARY KEY  (`userid`,`groupid`)
) ENGINE=MyISAM COMMENT='Links users with groups';



CREATE TABLE `userpermissions` (
  `userid` tinyint(4) NOT NULL default '0',
  `permissionid` int(5) NOT NULL default '0',
  `granted` enum('true','false') NOT NULL default 'false',
  PRIMARY KEY  (`userid`,`permissionid`)
) ENGINE=MyISAM;

INSERT INTO `userpermissions` VALUES (1, 1, 'true');
INSERT INTO `userpermissions` VALUES (1, 2, 'true');
INSERT INTO `userpermissions` VALUES (1, 3, 'true');
INSERT INTO `userpermissions` VALUES (1, 4, 'true');
INSERT INTO `userpermissions` VALUES (1, 5, 'true');
INSERT INTO `userpermissions` VALUES (1, 6, 'true');
INSERT INTO `userpermissions` VALUES (1, 7, 'true');
INSERT INTO `userpermissions` VALUES (1, 8, 'true');
INSERT INTO `userpermissions` VALUES (1, 9, 'true');
INSERT INTO `userpermissions` VALUES (1, 10, 'true');
INSERT INTO `userpermissions` VALUES (1, 11, 'true');
INSERT INTO `userpermissions` VALUES (1, 12, 'true');
INSERT INTO `userpermissions` VALUES (1, 13, 'true');
INSERT INTO `userpermissions` VALUES (1, 14, 'true');
INSERT INTO `userpermissions` VALUES (1, 15, 'true');
INSERT INTO `userpermissions` VALUES (1, 16, 'true');
INSERT INTO `userpermissions` VALUES (1, 17, 'true');
INSERT INTO `userpermissions` VALUES (1, 18, 'true');
INSERT INTO `userpermissions` VALUES (1, 19, 'true');
INSERT INTO `userpermissions` VALUES (1, 20, 'true');
INSERT INTO `userpermissions` VALUES (1, 21, 'true');
INSERT INTO `userpermissions` VALUES (1, 22, 'true');
INSERT INTO `userpermissions` VALUES (1, 23, 'true');
INSERT INTO `userpermissions` VALUES (1, 24, 'true');
INSERT INTO `userpermissions` VALUES (1, 25, 'true');
INSERT INTO `userpermissions` VALUES (1, 26, 'true');
INSERT INTO `userpermissions` VALUES (1, 27, 'true');
INSERT INTO `userpermissions` VALUES (1, 28, 'true');
INSERT INTO `userpermissions` VALUES (1, 29, 'true');
INSERT INTO `userpermissions` VALUES (1, 30, 'true');
INSERT INTO `userpermissions` VALUES (1, 31, 'true');
INSERT INTO `userpermissions` VALUES (1, 32, 'true');
INSERT INTO `userpermissions` VALUES (1, 33, 'true');
INSERT INTO `userpermissions` VALUES (1, 34, 'true');
INSERT INTO `userpermissions` VALUES (1, 35, 'true');
INSERT INTO `userpermissions` VALUES (1, 36, 'true');
INSERT INTO `userpermissions` VALUES (1, 37, 'true');
INSERT INTO `userpermissions` VALUES (1, 38, 'true');
INSERT INTO `userpermissions` VALUES (1, 39, 'true');
INSERT INTO `userpermissions` VALUES (1, 40, 'true');
INSERT INTO `userpermissions` VALUES (1, 41, 'true');
INSERT INTO `userpermissions` VALUES (1, 42, 'true');
INSERT INTO `userpermissions` VALUES (1, 43, 'true');
INSERT INTO `userpermissions` VALUES (1, 44, 'true');
INSERT INTO `userpermissions` VALUES (1, 45, 'true');
INSERT INTO `userpermissions` VALUES (1, 46, 'true');
INSERT INTO `userpermissions` VALUES (1, 47, 'true');
INSERT INTO `userpermissions` VALUES (1, 48, 'true');
INSERT INTO `userpermissions` VALUES (1, 49, 'true');
INSERT INTO `userpermissions` VALUES (1, 50, 'true');
INSERT INTO `userpermissions` VALUES (1, 51, 'true');
INSERT INTO `userpermissions` VALUES (1, 52, 'true');
INSERT INTO `userpermissions` VALUES (1, 53, 'true');
INSERT INTO `userpermissions` VALUES (1, 54, 'true');
INSERT INTO `userpermissions` VALUES (1, 55, 'true');
INSERT INTO `userpermissions` VALUES (1, 56, 'true');
INSERT INTO `userpermissions` VALUES (1, 57, 'true');
INSERT INTO `userpermissions` VALUES (1, 58, 'true');
INSERT INTO `userpermissions` VALUES (1, 59, 'true');
INSERT INTO `userpermissions` VALUES (1, 60, 'true');
INSERT INTO `userpermissions` VALUES (1, 61, 'true');
INSERT INTO `userpermissions` VALUES (1, 62, 'true');
INSERT INTO `userpermissions` VALUES (1, 63, 'true');
INSERT INTO `userpermissions` VALUES (1, 64, 'true');
INSERT INTO `userpermissions` VALUES (1, 65, 'true');
INSERT INTO `userpermissions` VALUES (1, 66, 'true');
INSERT INTO `userpermissions` VALUES (1, 67, 'true');
INSERT INTO `userpermissions` VALUES (1, 68, 'true');
INSERT INTO `userpermissions` VALUES (1, 69, 'true');
INSERT INTO `userpermissions` VALUES (1, 70, 'true');

CREATE TABLE `users` (
  `id` tinyint(4) NOT NULL auto_increment,
  `username` varchar(50) default NULL,
  `password` varchar(50) default NULL,
  `realname` varchar(50) default NULL,
  `roleid` int(5) NOT NULL default '1',
  `groupid` int(5) default NULL,
  `title` varchar(50) default NULL,
  `signature` text,
  `email` varchar(50) default NULL,
  `icq` varchar(15) NOT NULL default '',
  `aim` varchar(25) NOT NULL default '',
  `msn` varchar(70) NOT NULL default '',
  `phone` varchar(50) default NULL,
  `mobile` varchar(50) NOT NULL default '',
  `fax` varchar(50) default NULL,
  `status` tinyint(4) default NULL,
  `message` varchar(150) default NULL,
  `accepting` enum('No','Yes') default 'Yes',
  `var_incident_refresh` int(11) default '60',
  `var_update_order` enum('desc','asc') default 'desc',
  `var_num_updates_view` int(11) NOT NULL default '15',
  `var_style` int(11) default '1',
  `var_collapse` enum('true','false') NOT NULL default 'true',
  `var_hideautoupdates` enum('true','false') NOT NULL default 'false',
  `var_hideheader` enum('true','false') NOT NULL default 'false',
  `var_monitor` enum('true','false') NOT NULL default 'true',
  `var_notify_on_reassign` enum('true','false') NOT NULL default 'false',
  `var_i18n` varchar(20) default NULL,
  `listadmin` tinytext,
  `holiday_entitlement` float NOT NULL default '0',
  `qualifications` tinytext,
  `dashboard` varchar(255) NOT NULL default '0-3,1-1,1-2,2-4',
  `lastseen` TIMESTAMP NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `username` (`username`),
  KEY `accepting` (`accepting`),
  KEY `status` (`status`),
  KEY `groupid` (`groupid`)
) ENGINE=MyISAM;


CREATE TABLE `usersoftware` (
  `userid` tinyint(4) NOT NULL default '0',
  `softwareid` int(5) NOT NULL default '0',
  `backupid` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`userid`,`softwareid`),
  KEY `backupid` (`backupid`)
) ENGINE=MyISAM COMMENT='Defines which software users have expertise with';


CREATE TABLE `userstatus` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM;


INSERT INTO `userstatus` VALUES (1, 'In Office');
INSERT INTO `userstatus` VALUES (2, 'Not In Office');
INSERT INTO `userstatus` VALUES (3, 'In Meeting');
INSERT INTO `userstatus` VALUES (4, 'At Lunch');
INSERT INTO `userstatus` VALUES (5, 'On Holiday');
INSERT INTO `userstatus` VALUES (6, 'Working From Home');
INSERT INTO `userstatus` VALUES (7, 'On Training Course');
INSERT INTO `userstatus` VALUES (8, 'Absent Sick');
INSERT INTO `userstatus` VALUES (9, 'Working Away');


CREATE TABLE `vendors` (
  `id` int(5) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

INSERT INTO `vendors` VALUES (1,'Default');

CREATE TABLE IF NOT EXISTS `notices` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `gid` text,
  `type` tinyint(4) NOT NULL,
  `text` tinytext NOT NULL,
  `linktext` varchar(50) default NULL,
  `link` varchar(100) NOT NULL,
  `referenceid` int(11) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `durability` enum('sticky','session') NOT NULL default 'sticky',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

";

// ********************************************************************

$upgrade_schema[321] = "CREATE TABLE `system`
  (`id` INT( 1 ) NOT NULL ,
  `version` FLOAT( 3, 2 ) DEFAULT '0.00' NOT NULL ,
  PRIMARY KEY ( `id` )) ENGINE=MyISAM;

CREATE TABLE `feedbackforms` (
  `id` int(5) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `introduction` text NOT NULL,
  `thanks` text NOT NULL,
  `description` text NOT NULL,
  `multi` enum('yes','no') NOT NULL default 'no',
  PRIMARY KEY  (`id`),
  KEY `multi` (`multi`)
) ENGINE=MyISAM;
ALTER TABLE `feedbackrespondents` CHANGE `respondent` `contactid` INT( 11 ) NOT NULL;
ALTER TABLE `feedbackrespondents` CHANGE `responseref` `incidentid` INT( 11 ) NOT NULL;
ALTER TABLE `feedbackreport` CHANGE `respondent` `respondent` INT( 11 ) NOT NULL;
ALTER TABLE `emailtype` ADD `customervisibility` ENUM( 'show', 'hide' ) DEFAULT 'show' NOT NULL ;
";

$upgrade_schema[322] = "CREATE TABLE `roles` (
`id` INT( 5 ) NOT NULL AUTO_INCREMENT ,
`rolename` VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE=MyISAM;

INSERT INTO `roles` ( `id` , `rolename` ) VALUES ('1', 'Administrator');
INSERT INTO `roles` ( `id` , `rolename` ) VALUES ('2', 'Manager');
INSERT INTO `roles` ( `id` , `rolename` ) VALUES ('3', 'User');

CREATE TABLE `rolepermissions` (
`roleid` tinyint( 4 ) NOT NULL default '0',
`permissionid` int( 5 ) NOT NULL default '0',
`granted` enum( 'true', 'false' ) NOT NULL default 'false',
PRIMARY KEY ( `roleid` , `permissionid` )
) ENGINE=MyISAM;

ALTER TABLE `users` ADD `roleid` INT( 5 ) NOT NULL DEFAULT '1' AFTER `realname` ;
ALTER TABLE `users` DROP `accesslevel` ;
";

$upgrade_schema[323] = "CREATE TABLE `relatedincidents` (
`id` int(5) NOT NULL auto_increment,
`incidentid` int(5) NOT NULL default '0',
`relation` enum('child','sibling') NOT NULL default 'child',
`relatedid` int(5) NOT NULL default '0',
`owner` int(5) NOT NULL default '0',
PRIMARY KEY  (`id`),
KEY `incidentid` (`incidentid`,`relatedid`)
) ENGINE=MyISAM;

ALTER TABLE `sites` CHANGE `notes` `notes` TEXT NOT NULL ;

ALTER TABLE `sites` DROP `ftnpassword`;

UPDATE `permissions` SET `name` = 'View Products and Software' WHERE `id` =28 LIMIT 1 ;
UPDATE `permissions` SET `name` = 'Edit Products' WHERE `id` =29 LIMIT 1 ;
UPDATE `permissions` SET `name` = 'Add Feedback Forms' WHERE `id` =48 LIMIT 1 ;
UPDATE `permissions` SET `name` = 'Edit Feedback Forms' WHERE `id` =49 LIMIT 1 ;
UPDATE `permissions` SET `name` = 'View Feedback' WHERE `id` =51 LIMIT 1 ;
UPDATE `permissions` SET `name` = 'Edit Service Levels' WHERE `id` =53 LIMIT 1 ;

INSERT INTO `incidentstatus` VALUES (10, 'Active (Unassigned)', 'Active');
UPDATE `incidentstatus` SET `id` = '0' WHERE `id` =10 LIMIT 1 ;
";

$upgrade_schema[324] = "ALTER TABLE `users` ADD `groupid` INT( 5 ) NULL AFTER `roleid` ;
ALTER TABLE `users` ADD INDEX ( `groupid` ) ;
ALTER TABLE `software` ADD `lifetime_start` DATE NULL ,
ADD `lifetime_end` DATE NULL ;
ALTER TABLE `emailtype` ADD `storeinlog` ENUM( 'No', 'Yes' ) NOT NULL DEFAULT 'Yes';
ALTER TABLE `updates`
  DROP `timesincesla`,
  DROP `timesincereview`,
  DROP `reviewcalculated`,
  DROP `slacalculated`;
ALTER TABLE `users` ADD `var_notify_on_reassign` ENUM( 'true', 'false' ) NOT NULL DEFAULT 'false' AFTER `var_monitor`;
UPDATE users SET `var_notify_on_reassign` = 'false';
INSERT INTO `emailtype` (`name`, `type`, `description`, `tofield`, `fromfield`,
`replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`,
`storeinlog`) VALUES ('INCIDENT_REASSIGNED_USER_NOTIFY', 'system', 'Notify user when call assigned to them',
'<incidentreassignemailaddress>', '<supportemail>','<supportemail>', '', '',
'A <incidentpriority> priority call ([<incidentid>] - <incidenttitle>) has been reassigned to you',
'Hi,\r\n\r\nIncident [<incidentid>] entitled <incidenttitle> has been reassigned to you.\r\n\r\nThe details of this incident are:\r\n\r\nPriority: <incidentpriority>\r\nContact: <contactname>\r\nSite: <contactsite>\r\n\r\n\r\nRegards\r\n<applicationname>\r\n\r\n\r\n---\r\n<todaysdate> - <applicationshortname> <applicationversion>',
'hide', 'No');
UPDATE emailtype SET `toField` = '<incidentreassignemailaddress>' WHERE `name` =  'INCIDENT_REASSIGNED_USER_NOTIFY';

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `description` text NOT NULL,
  `priority` tinyint(4) default NULL,
  `owner` tinyint(4) NOT NULL default '0',
  `duedate` datetime default NULL,
  `startdate` datetime default NULL,
  `completion` tinyint(4) default NULL,
  `value` float(6,2) default NULL,
  `distribution` enum('public','private') NOT NULL default 'public',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `lastupdated` timestamp,
  PRIMARY KEY  (`id`),
  KEY `owner` (`owner`)
) ENGINE=MyISAM  ;

ALTER TABLE `tempincoming` ADD `lockeduntil` DATETIME NULL AFTER `locked` ;
INSERT INTO `permissions` VALUES (63, 'Add Reseller');

CREATE TABLE `notes` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL default '0',
  `timestamp` timestamp,
  `bodytext` text NOT NULL,
  `link` int(11) NOT NULL default '0',
  `refid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `refid` (`refid`),
  KEY `userid` (`userid`),
  KEY `link` (`link`)
) ENGINE=MyISAM ;

CREATE TABLE `escalationpaths` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `track_url` varchar(255) default NULL,
  `home_url` varchar(255) NOT NULL default '',
  `url_title` varchar(255) default NULL,
  `email_domain` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

ALTER TABLE `incidents` ADD `escalationpath` INT( 11 ) NULL AFTER `id` ;
INSERT INTO `permissions` VALUES (64, 'Manage Escalation Paths');

CREATE TABLE `dashboard` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `enabled` enum('true','false') NOT NULL default 'false',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

INSERT INTO `dashboard` (`id`, `name`, `enabled`) VALUES (1, 'random_tip', 'true'),
(2, 'statistics', 'true'),
(3, 'tasks', 'true'),
(4, 'user_incidents', 'true');

UPDATE `interfacestyles` SET `name` = 'Light Blue' WHERE `id` =1 LIMIT 1 ;
";


/*
 3.25
*/

$upgrade_schema[325] = "
ALTER TABLE `interfacestyles` ADD `iconset` VARCHAR( 255 ) NOT NULL DEFAULT 'sit' AFTER `cssurl` ;
ALTER TABLE `sites` ADD `websiteurl` VARCHAR( 255 ) NULL AFTER `email` ;

CREATE TABLE `tags` (
  `tagid` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`tagid`)
) ENGINE=MyISAM;

CREATE TABLE `set_tags` (
`id` INT NOT NULL ,
`type` MEDIUMINT NOT NULL ,
`tagid` INT NOT NULL ,
PRIMARY KEY ( `id` , `type` , `tagid` )
) ENGINE=MYISAM;

CREATE TABLE `links` (
     `linktype` int(11) NOT NULL default '0',
     `origcolref` int(11) NOT NULL default '0',
     `linkcolref` int(11) NOT NULL default '0',
     `direction` enum('left','right','bi') NOT NULL default 'left',
     `userid` tinyint(4) NOT NULL default '0',
     PRIMARY KEY  (`linktype`,`origcolref`,`linkcolref`),
     KEY `userid` (`userid`)
   ) ENGINE=MyISAM ;

CREATE TABLE `linktypes` (
     `id` int(11) NOT NULL auto_increment,
     `name` varchar(255) NOT NULL default '',
     `lrname` varchar(255) NOT NULL default '',
     `rlname` varchar(255) NOT NULL default '',
     `origtab` varchar(255) NOT NULL default '',
     `origcol` varchar(255) NOT NULL default '',
     `linktab` varchar(255) NOT NULL default '',
     `linkcol` varchar(255) NOT NULL default 'id',
     `selectionsql` varchar(255) NOT NULL default '',
     `filtersql` varchar(255) NOT NULL default '',
     `viewurl` varchar(255) NOT NULL default '',
     PRIMARY KEY  (`id`),
     KEY `origtab` (`origtab`),
     KEY `linktab` (`linktab`)
   ) ENGINE=MyISAM;

INSERT INTO `linktypes` VALUES (1,'Task','Subtask','Parent Task','tasks','id','tasks','id','name','','view_task.php?id=%id%'),(2,'Contact','Contact','Contact Task','tasks','id','contacts','id','forenames','','contact_details.php?id=%id%'),(3,'Site','Site','Site Task','tasks','id','sites','id','name','','site_details.php?id=%id%'),(4,'Incident','Incident','Task','tasks','id','incidents','id','title','','incident_details.php?id=%id%');

ALTER TABLE `users` ADD `var_num_updates_view` INT NOT NULL DEFAULT '15' AFTER `var_update_order` ;
INSERT INTO `emailtype` (`name`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`) VALUES ('NEARING_SLA', 'system', 'Notification when an incident nears its SLA target', '<supportmanageremail>', '<supportemail>', '<supportemail>', '<useremail>', '', '<applicationshortname> SLA: Incident <incidentid> about to breach SLA', 'This is an automatic notification that this incident is about to breach it\'s SLA.  The SLA target <info1> will expire in <info2> minutes.\r\n\r\nIncident: [<incidentid>] - <incidenttitle>\r\nOwner: <incidentowner>\r\nPriority: <incidentpriority>\r\nExternal Id: <incidentexternalid>\r\nExternal Engineer: <incidentexternalengineer>\r\nSite: <contactsite>\r\nContact: <contactname>\r\n\r\n--\r\n<applicationshortname> v<applicationversion>\r\n<todaysdate>\r\n', 'hide', 'Yes');

INSERT INTO `permissions` VALUES (65, 'Delete Products');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 65, 'true');
INSERT INTO `userpermissions` VALUES (1, 65, 'true');

ALTER TABLE `users` ADD `dashboard` VARCHAR( 255 ) NOT NULL DEFAULT '0-3,1-1,1-2,2-4';

INSERT INTO `permissions` VALUES (66, 'Install Dashboard Components');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 66, 'true');
INSERT INTO `userpermissions` VALUES (1, 66, 'true');
INSERT INTO `closingstatus` ( `id` , `name` ) VALUES ( NULL , 'Escalated' );
ALTER TABLE `tasks` ADD `enddate` DATETIME NULL AFTER `startdate` ;

INSERT INTO `permissions` VALUES (67, 'Run Management Reports');

INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 67, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 67, 'true');
INSERT INTO `userpermissions` VALUES (1, 67, 'true');

INSERT INTO `permissions` VALUES (68, 'Manage Holidays');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 68, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (2, 68, 'true');
INSERT INTO `userpermissions` VALUES (1, 68, 'true');

INSERT INTO `permissions` VALUES (69, 'Post Notices');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 69, 'true');
INSERT INTO `userpermissions` VALUES (1, 69, 'true');

ALTER TABLE `sites` ADD `active` ENUM( 'true', 'false' ) NOT NULL DEFAULT 'true';

ALTER TABLE `contacts` ADD `active` ENUM( 'true', 'false' ) NOT NULL DEFAULT 'true';

-- beta 3
UPDATE `interfacestyles` SET `iconset` = 'sit';

ALTER TABLE `updates` CHANGE `type` `type` ENUM( 'default', 'editing', 'opening', 'email', 'reassigning', 'closing', 'reopening', 'auto', 'phonecallout', 'phonecallin', 'research', 'webupdate', 'emailout', 'emailin', 'externalinfo', 'probdef', 'solution', 'actionplan', 'slamet', 'reviewmet', 'tempassigning', 'auto_chase_email', 'auto_chase_phone', 'auto_chase_manager', 'auto_chased_phone','auto_chased_manager','auto_chase_managers_manager') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'default';
";

$upgrade_schema[330] = "
ALTER TABLE `updates` CHANGE `type` `type` ENUM( 'default', 'editing', 'opening', 'email', 'reassigning', 'closing', 'reopening', 'auto', 'phonecallout', 'phonecallin', 'research', 'webupdate', 'emailout', 'emailin', 'externalinfo', 'probdef', 'solution', 'actionplan', 'slamet', 'reviewmet', 'tempassigning', 'auto_chase_email', 'auto_chase_phone', 'auto_chase_manager', 'auto_chased_phone','auto_chased_manager','auto_chase_managers_manager','customerclosurerequest') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'default';

CREATE TABLE IF NOT EXISTS `drafts` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `incidentid` int(11) NOT NULL,
  `type` enum('update','email') NOT NULL,
  `content` text NOT NULL,
  `meta` text NOT NULL,
  `lastupdate` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

ALTER TABLE `software` ADD `vendorid` INT( 5 ) NOT NULL AFTER `name` ;

CREATE TABLE IF NOT EXISTS `notices` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `gid` text,
  `type` tinyint(4) NOT NULL,
  `text` tinytext NOT NULL,
  `linktext` varchar(50) default NULL,
  `link` varchar(100) NOT NULL,
  `referenceid` int(11) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `durability` enum('sticky','session') NOT NULL default 'sticky',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `servicelevels` ADD `timed` enum('yes','no') NOT NULL DEFAULT 'no' ;

ALTER TABLE `users` ADD `var_i18n` VARCHAR( 20 ) NULL AFTER `var_notify_on_reassign` ;

ALTER TABLE `updates` ADD `duration` INT NULL ;

INSERT INTO `userpermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 68, 'true');
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (1, 69, 'true');
INSERT INTO `userpermissions` (`userid`, `permissionid`, `granted`) VALUES (1, 68, 'true');
INSERT INTO `userpermissions` (`userid`, `permissionid`, `granted`) VALUES (1, 69, 'true');

ALTER TABLE `users` ADD `lastseen` TIMESTAMP NOT NULL ;

ALTER TABLE `tasks` CHANGE `distribution` `distribution` ENUM( 'public', 'private', 'incident' ) NOT NULL DEFAULT 'public' ;

ALTER TABLE `updates` CHANGE `type` `type` ENUM( 'default', 'editing', 'opening', 'email', 'reassigning', 'closing', 'reopening', 'auto', 'phonecallout', 'phonecallin', 'research', 'webupdate', 'emailout', 'emailin', 'externalinfo', 'probdef', 'solution', 'actionplan', 'slamet', 'reviewmet', 'tempassigning', 'auto_chase_email', 'auto_chase_phone', 'auto_chase_manager', 'auto_chased_phone', 'auto_chased_manager', 'auto_chase_managers_manager', 'customerclosurerequest', 'fromtask' ) NULL DEFAULT 'default' ;


-- KMH 15Nov07
ALTER TABLE `maintenance` ADD `supportedcontacts` INT( 255 ) NOT NULL DEFAULT '0';
ALTER TABLE `maintenance` ADD `allcontactssupported` ENUM( 'No', 'Yes' ) NOT NULL DEFAULT 'No';

-- INL 22Nov07
ALTER TABLE `dashboard_rss` ADD `items` INT( 5 ) NULL AFTER `url`;
-- INL 25Nov07
DROP TABLE `holidaytypes`;

-- PH 26Nov07
CREATE TABLE `billing_periods` (
`servicelevelid` INT( 5 ) NOT NULL ,
`engineerperiod` INT NOT NULL COMMENT 'In minutes',
`customerperiod` INT NOT NULL COMMENT 'In minutes',
PRIMARY KEY r( `servicelevelid` )
) ENGINE = MYISAM ;

 -- KMH 26/11/07
 ALTER TABLE `incidents` ADD `slanotice` TINYINT(1) NOT NULL DEFAULT '0' AFTER `slaemail` ;

-- KMH 27/11/07 - Type 6 is none, workaround for browse_contact.php
ALTER TABLE `maintenance` CHANGE `licence_type` `licence_type` INT( 11 ) NULL DEFAULT '6'
";


// Important: When making changes to the schema you must add SQL to make the alterations
// to existing databases in $upgrade_schema[] *AND* you must also change $schema[] for
// new installations.

?>