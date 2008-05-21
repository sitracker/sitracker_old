<?php
// setup-schema.php - Defines database schema for use in setup.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>


// Important: When making changes to the schema you must add SQL to make the alterations
// to existing databases in $upgrade_schema[] at the bottom of the file
// *AND* you must also change $schema[] for new installations (at the top of the file)

// TODO we need to clean this schema up to make it confirmed compatible with mysql4

$schema = "CREATE TABLE `{$dbBillingPeriods}` (
`servicelevelid` INT( 5 ) NOT NULL ,
`engineerperiod` INT NOT NULL COMMENT 'In minutes',
`customerperiod` INT NOT NULL COMMENT 'In minutes',
`priority` INT( 4 ) NOT NULL,
`tag` VARCHAR( 10 ) NOT NULL,
`createdby` INT NULL ,
`modified` DATETIME NULL ,
`modifiedby` INT NULL ,
PRIMARY KEY ( `servicelevelid`,`priority` )
) ENGINE = MYISAM ;


CREATE TABLE `{$dbClosingStatus}` (
 `id` int(11) NOT NULL auto_increment,
 `name` varchar(50) default NULL,
 PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

INSERT INTO `{$dbClosingStatus}` VALUES (1, 'strSentInformation');
INSERT INTO `{$dbClosingStatus}` VALUES (2, 'strSolvedProblem');
INSERT INTO `{$dbClosingStatus}` VALUES (3, 'strReportedBug');
INSERT INTO `{$dbClosingStatus}` VALUES (4, 'strActionTaken');
INSERT INTO `{$dbClosingStatus}` VALUES (5, 'strDuplicate');
INSERT INTO `{$dbClosingStatus}` VALUES (6, 'strNoLongerRelevant');
INSERT INTO `{$dbClosingStatus}` VALUES (7, 'strUnsupported');
INSERT INTO `{$dbClosingStatus}` VALUES (8, 'strSupportExpired');
INSERT INTO `{$dbClosingStatus}` VALUES (9, 'strUnsolved');
INSERT INTO `{$dbClosingStatus}` VALUES (10, 'strEscalated');


CREATE TABLE `{$dbContacts}` (
`id` int(11) NOT NULL auto_increment,
  `notify_contactid` int(11) NOT NULL default '0',
  `username` varchar(50) default NULL,
  `password` varchar(50) default NULL,
  `forenames` varchar(100) NOT NULL default '',
  `surname` varchar(100) NOT NULL default '',
  `jobtitle` varchar(255) NOT NULL default '',
  `courtesytitle` varchar(50) NOT NULL default '',
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
  `created` datetime default NULL,
  `createdby` int(11) default NULL,
  `modified` datetime default NULL,
  `modifiedby` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `siteid` (`siteid`),
  KEY `username` (`username`),
  KEY `forenames` (`forenames`),
  KEY `surname` (`surname`),
  KEY `notify_contactid` (`notify_contactid`)
) TYPE=MyISAM ;

INSERT INTO `{$dbContacts}` (`id`, `notify_contactid`, `username`, `password`, `forenames`, `surname`, `jobtitle`, `courtesytitle`, `siteid`, `email`, `phone`, `mobile`, `fax`, `department`, `address1`, `address2`, `city`, `county`, `country`, `postcode`, `dataprotection_email`, `dataprotection_phone`, `dataprotection_address`, `timestamp_added`, `timestamp_modified`, `notes`) VALUES
(1, 4, 'Acme1', MD5(RAND()), 'John', 'Acme', 'Chairman', 'Mr', 1, 'acme@example.com', '0666 222111', '', '', '', '', '', '', '', '', '', 'Yes', 'Yes', 'Yes', 1132930556, 1187360933, '');


CREATE TABLE `{$dbDashboard}` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `version` mediumint(9) NOT NULL default '1',
  `enabled` enum('true','false') NOT NULL default 'false',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

INSERT INTO `{$dbDashboard}` (`id`, `name`, `enabled`) VALUES (1, 'random_tip', 'true'),
(2, 'statistics', 'true'),
(3, 'tasks', 'true'),
(4, 'user_incidents', 'true');


CREATE TABLE `{$dbDrafts}` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `incidentid` int(11) NOT NULL,
  `type` enum('update','email') NOT NULL,
  `content` text NOT NULL,
  `meta` text NOT NULL,
  `lastupdate` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;


CREATE TABLE `{$dbEmailSig}` (
  `id` int(11) NOT NULL auto_increment,
  `signature` text NOT NULL,
  `createdby` INT NULL ,
  `modified` DATETIME NULL ,
  `modifiedby` INT NULL ,
  PRIMARY KEY  (`id`)
)  ENGINE=MyISAM COMMENT='Global Email Signature' ;

INSERT INTO `{$dbEmailSig}` (`id`, `signature`) VALUES (1, '--\r\n... Powered by Open Source Software: Support Incident Tracker (SiT!) is available free from http://sitracker.sourceforge.net/');


CREATE TABLE `{$dbEmailTemplates}` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `type` enum( 'usertemplate', 'system', 'contact', 'site', 'incident', 'kb', 'user') NOT NULL COMMENT 'usertemplate is personal template owned by a user, user is a template relating to a user' DEFAULT 'user',
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
INSERT INTO `{$dbEmailTemplates}`(`name`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`)
VALUES ('Support Email','user','','<contactemail>','<supportemail>','<supportemail>','','<useremail>','[<incidentid>] - <incidenttitle>','<contactfirstname>,\r\n\r\n<signature>\r\n<globalsignature>', 'show', 'yes');
INSERT INTO `{$dbEmailTemplates}`(`name`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`)
VALUES ('User Email','user','','<contactemail>','<useremail>','<useremail>','','','','<signature>\r\n<globalsignature>\r\n', 'show', 'yes');
INSERT INTO `{$dbEmailTemplates}`(`name`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`)
VALUES ('INCIDENT_CLOSURE','system','Notify contact that the incident has been marked for closure and will be closed shortly','<contactemail>','<supportemail>','<supportemail>','','<useremail>','[<incidentid>] - <incidenttitle>','<contactfirstname>,\r\n\r\nIncident <incidentid> has been marked for closure. If you still have outstanding issues relating to this incident then please reply with details, otherwise it will be closed after the next seven days.\r\n\r\n<signature>\r\n<globalsignature>', 'show', 'yes');
INSERT INTO `{$dbEmailTemplates}`(`name`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`)
VALUES ('INCIDENT_LOGGED_CALL','system','Acknowledge the contacts telephone call and notify them of the new incident number','<contactemail>','<supportemail>','<supportemail>','','<useremail>','[<incidentid>] - <incidenttitle>','Thank you for your call. The incident <incidentid> has been generated and your details stored in our tracking system. \r\n\r\nYou will be receiving a response from one of our product specialists as soon as possible. When referring to this incident please remember to quote incident <incidentid> in \r\nall communications. \r\n\r\nFor all email communications please title your email as [<incidentid>] - <incidenttitle>\r\n\r\n<globalsignature>\r\n', 'show', 'no');
INSERT INTO `{$dbEmailTemplates}`(`name`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`)
VALUES ('INCIDENT_CLOSED','system','Notify contact that an incident has now been closed','<contactemail>','<supportemail>','<supportemail>','','','[<incidentid>] - <incidenttitle> - Closed','This is an automated message to let you know that Incident <incidentid> has now been closed. \r\n\r\n<globalsignature>', 'show', 'yes');
INSERT INTO `{$dbEmailTemplates}`(`name`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`)
VALUES ('OUT_OF_SLA', 'system', '', '<supportmanager>', '<supportemail>', '<supportemail>', '<useremail>', '', '<applicationshortname> SLA: Incident <incidentid> now outside SLA', 'This is an automatic notification that this incident has gone outside its SLA.  The SLA target <info1> expired <info2> minutes ago.\n\nIncident: [<incidentid>] - <incidenttitle>\nOwner: <incidentowner>\nPriority: <incidentpriority>\nExternal Id: <incidentexternalid>\nExternal Engineer: <incidentexternalengineer>\nSite: <contactsite>\nContact: <contactname>\n\n--\n<applicationshortname> v<applicationversion>\n<todaysdate>\n', 'hide', 'yes');
INSERT INTO `{$dbEmailTemplates}`(`name`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`)
VALUES ('OUT_OF_REVIEW', 'system', '', '<supportmanager>', '<useremail>', '<supportemail>', '<supportemail>', '', '<applicationshortname> Review: Incident <incidentid> due for review soon', 'This is an automatic notification that this incident [<incidentid>] will soon be due for review.\n\nIncident: [<incidentid>] - <incidenttitle>\nEngineer: <incidentowner>\nPriority: <incidentpriority>\nExternal Id: <incidentexternalid>\nExternal Engineer: <incidentexternalengineer>\nSite: <contactsite>\nContact: <contactname>\n\n--\n<applicationshortname> v<applicationversion>\n<todaysdate>', 'hide', 'yes');
INSERT INTO `{$dbEmailTemplates}`(`name`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`)
VALUES ('INCIDENT_UPDATED','system','Acknoweldge contacts email and update to incident','<contactemail>','<supportemail>','<supportemail>','','','[<incidentid>] - <incidenttitle>','Thank you for your email. The incident <incidentid> has been updated and your details stored in our support database. \r\n\r\nYou will be receiving a response from one of our product specialists as soon as possible.\r\n\r\n<globalsignature>', 'show', 'no');
INSERT INTO `{$dbEmailTemplates}`(`name`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`)
VALUES ('INCIDENT_CLOSED_EXTERNAL','system','Notify external engineer that an incident has been closed','<incidentexternalemail>','<supportemail>','<supportemail>','','','Incident ref #<incidentexternalid>  - <incidenttitle> CLOSED - [<incidentid>]','<incidentexternalengineerfirstname>,\r\n\r\nThis is an automated email to let you know that Incident <incidentexternalid> has been closed within our tracking system.\r\n\r\nMany thanks for your help.\r\n\r\n<signature>\r\n<globalsignature>', 'hide', 'no');
INSERT INTO `{$dbEmailTemplates}`(`name`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`)
VALUES ('INCIDENT_LOGGED_EMAIL','system','Acknowledge the contacts email and notify them of the new incident number','<contactemail>','<supportemail>','<supportemail>','','<useremail>','[<incidentid>] - <incidenttitle>','Thank you for your email. The incident <incidentid> has been generated and your details stored in our tracking system. \r\n\r\nYou will be receiving a response from one of our product specialists as soon as possible. When referring to this incident please remember to quote incident <incidentid> in \r\nall communications.\r\n\r\nFor all email communications please title your email as [<incidentid>] - <incidenttitle>\r\n\r\n<globalsignature>', 'show', 'no');
INSERT INTO `{$dbEmailTemplates}` (`name`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`) VALUES ('INCIDENT_REASSIGNED_USER_NOTIFY', 'system', 'Notify user when call assigned to them', '<incidentreassignemailaddress>', '<supportemail>', '<supportemail>', '', '', 'A <incidentpriority> priority call ([<incidentid>] - <incidenttitle>) has been reassigned to you', 'Hi,\r\n\r\nIncident [<incidentid>] entitled <incidenttitle> has been reassigned to you.\r\n\r\nThe details of this incident are:\r\n\r\nPriority: <incidentpriority>\r\nContact: <contactname>\r\nSite: <contactsite>\r\n\r\n\r\nRegards\r\n<applicationname>\r\n\r\n\r\n---\r\n<todaysdate> - <applicationshortname> <applicationversion>', 'hide', 'No');
INSERT INTO `{$dbEmailTemplates}` (`name`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`) VALUES ('NEARING_SLA', 'system', 'Notification when an incident nears its SLA target', '<supportmanageremail>', '<supportemail>', '<supportemail>', '<useremail>', '', '<applicationshortname> SLA: Incident <incidentid> about to breach SLA', 'This is an automatic notification that this incident is about to breach its SLA.  The SLA target <info1> will expire in <info2> minutes.\r\n\r\nIncident: [<incidentid>] - <incidenttitle>\r\nOwner: <incidentowner>\r\nPriority: <incidentpriority>\r\nExternal Id: <incidentexternalid>\r\nExternal Engineer: <incidentexternalengineer>\r\nSite: <contactsite>\r\nContact: <contactname>\r\n\r\n--\r\n<applicationshortname> v<applicationversion>\r\n<todaysdate>\r\n', 'hide', 'Yes');


CREATE TABLE `{$dbEscalationPaths}` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `track_url` varchar(255) default NULL,
  `home_url` varchar(255) NOT NULL default '',
  `url_title` varchar(255) default NULL,
  `email_domain` varchar(255) default NULL,
  `createdby` INT NULL ,
  `modified` DATETIME NULL ,
  `modifiedby` INT NULL ,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;


CREATE TABLE `{$dbFeedbackForms}` (
  `id` int(5) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `introduction` text NOT NULL,
  `thanks` text NOT NULL,
  `description` text NOT NULL,
  `multi` enum('yes','no') NOT NULL default 'no',
  `createdby` INT NULL ,
  `modified` DATETIME NULL ,
  `modifiedby` INT NULL ,
  PRIMARY KEY  (`id`),
  KEY `multi` (`multi`)
) ENGINE=MyISAM;


CREATE TABLE `{$dbFeedbackQuestions}` (
  `id` int(5) NOT NULL auto_increment,
  `formid` int(5) NOT NULL default '0',
  `question` varchar(255) NOT NULL default '',
  `questiontext` text NOT NULL,
  `sectiontext` text NOT NULL,
  `taborder` int(5) NOT NULL default '0',
  `type` varchar(255) NOT NULL default 'text',
  `required` enum('true','false') NOT NULL default 'false',
  `options` text NOT NULL,
  `createdby` INT NULL ,
  `modified` DATETIME NULL ,
  `modifiedby` INT NULL ,
  PRIMARY KEY  (`id`),
  KEY `taborder` (`taborder`),
  KEY `type` (`type`),
  KEY `formid` (`formid`)
) ENGINE=MyISAM;


CREATE TABLE `{$dbFeedbackReport}` (
  `id` int(5) NOT NULL default '0',
  `formid` int(5) NOT NULL default '0',
  `respondent` int(11) NOT NULL default '0',
  `responseref` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `completed` enum('yes','no') NOT NULL default 'no',
  `created` timestamp(14) NOT NULL,
  `incidentid` int(5) NOT NULL default '0',
  `contactid` int(5) NOT NULL default '0',
  `createdby` INT NULL ,
  `modified` DATETIME NULL ,
  `modifiedby` INT NULL ,
  PRIMARY KEY  (`id`),
  KEY `responseref` (`responseref`),
  KEY `formid` (`formid`),
  KEY `respondant` (`respondent`),
  KEY `completed` (`completed`),
  KEY `incidentid` (`incidentid`),
  KEY `contactid` (`contactid`)
) ENGINE=MyISAM;


CREATE TABLE `{$dbFeedbackRespondents}` (
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

CREATE TABLE `{$dbFeedbackResults}` (
  `id` int(5) NOT NULL auto_increment,
  `respondentid` int(5) NOT NULL default '0',
  `questionid` int(5) NOT NULL default '0',
  `result` varchar(255) NOT NULL default '',
  `resulttext` text,
  `createdby` INT NULL ,
  `modified` DATETIME NULL ,
  `modifiedby` INT NULL ,
  PRIMARY KEY  (`id`),
  KEY `questionid` (`questionid`),
  KEY `respondentid` (`respondentid`)
) ENGINE=MyISAM;


CREATE TABLE `{$dbFiles}` (
  `id` int(11) NOT NULL auto_increment,
  `category` enum('public','private','protected') NOT NULL default 'public',
  `filename` varchar(255) NOT NULL default '',
  `size` bigint(11) NOT NULL default '0',
  `userid` int(11) NOT NULL default '0',
  `usertype` ENUM( 'user', 'contact' ) NOT NULL DEFAULT 'user',
  `shortdescription` varchar(255) NOT NULL default '',
  `longdescription` TEXT NOT NULL,
  `webcategory` varchar(255) NOT NULL default '',
  `path` varchar(255) NOT NULL default '',
  `downloads` int(11) NOT NULL default '0',
  `filedate` DATETIME NOT NULL,
  `expiry` DATETIME NOT NULL,
  `fileversion` varchar(50) NOT NULL default '',
  `published` enum('yes','no') NOT NULL default 'no',
  `createdby` INT NULL ,
  `modified` DATETIME NULL ,
  `modifiedby` INT NULL ,
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`),
  KEY `category` (`category`),
  KEY `filename` (`filename`),
  KEY `published` (`published`),
  KEY `webcategory` (`webcategory`)
) ENGINE=MyISAM;


CREATE TABLE `{$dbGroups}` (
  `id` int(5) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `imageurl` varchar(255) NOT NULL default '',
  `createdby` INT NULL ,
  `modified` DATETIME NULL ,
  `modifiedby` INT NULL ,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM COMMENT='List of user groups' ;


CREATE TABLE `{$dbHolidays}` (
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


CREATE TABLE `{$dbIncidentPools}` (
  `id` int(11) NOT NULL auto_increment,
  `maintenanceid` int(11) NOT NULL default '0',
  `siteid` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `incidentsremaining` int(5) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `maintenanceid` (`maintenanceid`)
) ENGINE=MyISAM;


CREATE TABLE `{$dbIncidentProductInfo}` (
  `id` int(11) NOT NULL auto_increment,
  `incidentid` int(11) default NULL,
  `productinfoid` int(11) default NULL,
  `information` text,
   `createdby` INT NULL ,
  `modified` DATETIME NULL ,
  `modifiedby` INT NULL ,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;


CREATE TABLE `{$dbIncidents}` (
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
  `createdby` INT NULL ,
  `modified` DATETIME NULL ,
  `modifiedby` INT NULL ,
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


CREATE TABLE `{$dbIncidentStatus}` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `ext_name` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

INSERT INTO `{$dbIncidentStatus}` VALUES (1, 'strActive', 'strActive');
INSERT INTO `{$dbIncidentStatus}` VALUES (2, 'strClosed', 'strClosed');
INSERT INTO `{$dbIncidentStatus}` VALUES (3, 'strResearchNeeded', 'strResearching');
INSERT INTO `{$dbIncidentStatus}` VALUES (4, 'strCalledAndLeftMessage', 'strCalledAndLeftMessage');
INSERT INTO `{$dbIncidentStatus}` VALUES (5, 'strAwaitingColleagueResponse', 'strInternalEscalation');
INSERT INTO `{$dbIncidentStatus}` VALUES (6, 'strAwaitingSupportResponse', 'strExternalEscalation');
INSERT INTO `{$dbIncidentStatus}` VALUES (7, 'strAwaitingClosure', 'strAwaitingClosure');
INSERT INTO `{$dbIncidentStatus}` VALUES (8, 'strAwaitingCustomerAction', 'strCustomerHasAction');
INSERT INTO `{$dbIncidentStatus}` VALUES (9, 'strUnsupported', 'strUnsupported');
INSERT INTO `{$dbIncidentStatus}` VALUES (10, 'strActiveUnassigned', 'strActive');


CREATE TABLE `{$dbInterfaceStyles}` (
  `id` int(5) NOT NULL,
  `name` varchar(50) NOT NULL default '',
  `cssurl` varchar(255) NOT NULL default '',
  `iconset` varchar(255) NOT NULL default 'sit',
  `headerhtml` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 ;


INSERT INTO `{$dbInterfaceStyles}` (`id`, `name`, `cssurl`, `iconset`, `headerhtml`) VALUES (1, 'Light Blue', 'sit1.css', 'sit', ''),
(2, 'Grey', 'sit2.css', 'sit', ''),
(3, 'Green', 'sit3.css', 'sit', ''),
(4, 'Silver Blue', 'sit4.css', 'sit', ''),
(5, 'Classic', 'sit5.css', 'sit', ''),
(6, 'Orange', 'sit_ph2.css', 'sit', ''),
(7, 'Yellow and Blue', 'sit7.css', 'sit', ''),
(8, 'Neoteric', 'sit8.css', 'oxygen', ''),
(9, 'Toms Linux Style', 'sit9.css', 'sit', ''),
(10, 'Cool Blue', 'sit_ph.css', 'sit', ''),
(11, 'Just Light', 'sit10.css', 'sit', ''),
(12, 'Ex Pea', 'sit11.css', 'sit', ''),
(13, 'GUI Colours', 'sit12.css', 'sit', ''),
(14, 'Flashy', 'sit14/sit14.css', 'sit', ''),
(15, 'Richard', 'sit15.css', 'sit', '');


CREATE TABLE `{$dbJournal}` (
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


CREATE TABLE `{$dbKBArticles}` (
  `docid` int(5) NOT NULL auto_increment,
  `doctype` int(5) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `distribution` ENUM( 'public', 'private', 'restricted' ) NOT NULL DEFAULT 'public'
  COMMENT 'public appears in the portal, private is info never to be released to the public,
  restricted is info that is sensitive but could be mentioned if asked, for example' ,
  `published` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` varchar(255) NOT NULL default '',
  `reviewed` datetime NOT NULL default '0000-00-00 00:00:00',
  `reviewer` tinyint(4) NOT NULL default '0',
  `keywords` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`docid`),
  KEY `distribution` (`distribution`),
  KEY `title` (`title`)
) ENGINE=MyISAM COMMENT='Knowledge base articles' ;


CREATE TABLE `{$dbKBContent}` (
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


CREATE TABLE `{$dbKBSoftware}` (
  `docid` int(5) NOT NULL default '0',
  `softwareid` int(5) NOT NULL default '0',
  PRIMARY KEY  (`docid`,`softwareid`)
) ENGINE=MyISAM COMMENT='Links kb articles with software';


CREATE TABLE `{$dbLicenceTypes}` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

INSERT INTO `{$dbLicenceTypes}` VALUES (1, 'Per User');
INSERT INTO `{$dbLicenceTypes}` VALUES (2, 'Per Workstation');
INSERT INTO `{$dbLicenceTypes}` VALUES (3, 'Per Server');
INSERT INTO `{$dbLicenceTypes}` VALUES (4, 'Site');
INSERT INTO `{$dbLicenceTypes}` VALUES (5, 'Evaluation');


CREATE TABLE `{$dbLinks}` (
     `linktype` int(11) NOT NULL default '0',
     `origcolref` int(11) NOT NULL default '0',
     `linkcolref` int(11) NOT NULL default '0',
     `direction` enum('left','right','bi') NOT NULL default 'left',
     `userid` tinyint(4) NOT NULL default '0',
     PRIMARY KEY  (`linktype`,`origcolref`,`linkcolref`),
     KEY `userid` (`userid`)
) ENGINE=MyISAM ;


CREATE TABLE `{$dbLinkTypes}` (
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

INSERT INTO `{$dbLinkTypes}`
VALUES (1,'Task','Subtask','Parent Task','tasks','id','tasks','id','name','','view_task.php?id=%id%'),
(2,'Contact','Contact','Contact Task','tasks','id','contacts','id','CONCAT(forenames, \" \", surname)','','contact_details.php?id=%id%'),
(3,'Site','Site','Site Task','tasks','id','sites','id','name','','site_details.php?id=%id%'),
(4,'Incident','Incident','Task','tasks','id','incidents','id','title','','incident_details.php?id=%id%'),
(5,'Attachments', 'Update', 'File', 'updates', 'id', 'files', 'id', 'filename', '', 'incident_details.php?updateid=%id%&tab=files');

CREATE TABLE `{$dbMaintenance}` (
  `id` int(11) NOT NULL auto_increment,
  `site` int(11) default NULL,
  `product` int(11) default NULL,
  `reseller` int(11) default NULL,
  `expirydate` int(11) default NULL,
  `licence_quantity` int(11) default NULL,
  `licence_type` int(11) default NULL,
  `incident_quantity` int(5) NOT NULL default '0',
  `incidents_used` int(5) NOT NULL default '0',
  `notes` text,
  `admincontact` int(11) default NULL,
  `productonly` enum('yes','no') NOT NULL default 'no',
  `term` enum('no','yes') default 'no',
  `servicelevelid` int(11) NOT NULL default '1',
  `incidentpoolid` int(11) NOT NULL default '0',
  `supportedcontacts` INT( 255 ) NOT NULL DEFAULT '0',
  `allcontactssupported` ENUM( 'no', 'yes' ) NOT NULL DEFAULT 'no',
  `var_incident_visible_contacts` ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'no',
  `var_incident_visible_all` ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'no',
  PRIMARY KEY  (`id`),
  KEY `site` (`site`),
  KEY `productonly` (`productonly`)
) ENGINE=MyISAM;

-- FIXME - decide what the last two fields should be by default
INSERT INTO `{$dbMaintenance}` (id, site, product, reseller, expirydate, licence_quantity, licence_type, incident_quantity, incidents_used, notes, admincontact, productonly, term, servicelevelid, incidentpoolid) VALUES (1,1,1,2,1268179200,1,4,0,0,'This is an example contract.',1,'no','no',0,0);


CREATE TABLE `{$dbNotes}` (
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


CREATE TABLE `{$dbNotices}` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `template` varchar(255) NULL,
  `type` tinyint(4) NOT NULL,
  `text` tinytext NOT NULL,
  `linktext` varchar(50) default NULL,
  `link` varchar(100) NOT NULL,
  `referenceid` int(11) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `durability` enum('sticky','session') NOT NULL default 'sticky',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;


CREATE TABLE `{$dbNoticeTemplates}` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 255 ) NOT NULL ,
`type` TINYINT( 4 ) NOT NULL ,
`description` VARCHAR( 255 ) NOT NULL ,
`text` TINYTEXT NOT NULL ,
`linktext` VARCHAR( 50 ) NULL ,
`link` VARCHAR( 100 ) NULL ,
`durability` ENUM( 'sticky', 'session' ) NOT NULL DEFAULT 'sticky'
) ENGINE = MYISAM ;


INSERT INTO `{$dbNoticeTemplates}` (`name`, `type`, `description`, `text`, `linktext`, `link`, `durability`) VALUES('TRIGGER_INCIDENT_CREATED', 0, 'Used when a new incident has been created', 'Incident {incidentid} - {incidenttitle} has been logged', 'View Incident', 'javascript:incident_details_window({incidentid})', 'sticky');
INSERT INTO `{$dbNoticeTemplates}` (`name`, `type`, `description`, `text`, `linktext`, `link`, `durability`) VALUES('TRIGGER_INCIDENT_ASSIGNED_TRIGGER', 0, 'Used when a new incident is assigned to you', 'Incident {incidentid} - {incidenttitle} has been assigned to you', 'View Incident', 'javascript:incident_details_window({incidentid})', 'sticky');
INSERT INTO `{$dbNoticeTemplates}` (`name`, `type`, `description`, `text`, `linktext`, `link`, `durability`) VALUES('TRIGGER_INCIDENT_NEARING_SLA_TRIGGER', 0, 'Used when one of your incidents nears an SLA', 'Incident {incidentid} - {incidenttitle} is nearing its SLA', 'View Incident', 'javascript:incident_details_window({incidentid})', 'sticky');
INSERT INTO `{$dbNoticeTemplates}` (`name`, `type`, `description`, `text`, `linktext`, `link`, `durability`) VALUES('TRIGGER_USERS_INCIDENT_NEARING_SLA_TRIGGER', 0, 'Used when a user\'s incident you are watching is assigned to you', '{incidentowner}\'s incident {incidentid} - {incidenttitle} is nearing its SLA', 'View Incident', 'javascript:incident_details_window({incidentid})', 'sticky');
INSERT INTO `{$dbNoticeTemplates}` (`name`, `type`, `description`, `text`, `linktext`, `link`, `durability`) VALUES('TRIGGER_INCIDENT_EXCEEDED_SLA_TRIGGER', 0, 'Used when one of your incidents exceeds an SLA', 'Incident {incidentid} - {incidenttitle} has exceeded its SLA', 'View Incident', 'javascript:incident_details_window({incidentid})', 'sticky');
INSERT INTO `{$dbNoticeTemplates}` (`name`, `type`, `description`, `text`, `linktext`, `link`, `durability`) VALUES('TRIGGER_INCIDENT_REVIEW_DUE', 0, 'Used when an incident is due a review', 'Incident {incidentid} - {incidenttitle} is due for review', 'View Incident', 'javascript:incident_details_window({incidentid})', 'sticky');
INSERT INTO `{$dbNoticeTemplates}` (`name`, `type`, `description`, `text`, `linktext`, `link`, `durability`) VALUES('TRIGGER_KB_CREATED_TRIGGER', 0, 'Used when a new Knowledgebase article is created', 'KB Article {KBname} has been created', NULL, NULL, 'sticky');
INSERT INTO `{$dbNoticeTemplates}` (`name`, `type`, `description`, `text`, `linktext`, `link`, `durability`) VALUES('TRIGGER_NEW_HELD_EMAIL', 0, 'Used when there is a new email in the holding queue', 'There is a new email in the holding queue', 'View Holding Queue', '{sitpath}/review_incoming_updates.php', 'sticky');
INSERT INTO `{$dbNoticeTemplates}` (`name`, `type`, `description`, `text`, `linktext`, `link`, `durability`) VALUES('TRIGGER_MINS_HELD_EMAIL', 0, 'Used when there is a new email in the holding queue for x minutes', 'There has been an email in the holding queue for {holdingmins} minutes', 'View Holding Queue', '{sitpath}/review_incoming_updates.php', 'sticky');
INSERT INTO `{$dbNoticeTemplates}` (`name`, `type`, `description`, `text`, `linktext`, `link`, `durability`) VALUES('TRIGGER_SIT_UPGRADED', 0, 'Used when the system is upgraded', 'SiT! has been upgraded to {sitversion}', 'What\'s New?', '{sitpath}/releasenotes.php', 'sticky');
INSERT INTO `{$dbNoticeTemplates}` (`name`, `type`, `description`, `text`, `linktext`, `link`, `durability`) VALUES('TRIGGER_INCIDENT_OWNED_CLOSED_BY_USER', 0, 'Used when one of your incidents is closed by another engineer', 'Your incident {incidentid} - {incidenttitle} has been closed by {engineerclosedname}', NULL, NULL, 'sticky');
INSERT INTO `{$dbNoticeTemplates}` (`name`, `type`, `description`, `text`, `linktext`, `link`, `durability`) VALUES('TRIGGER_USER_SET_TO_AWAY', 0, 'Used when a watched user goes away', '{realname} is now [b]not accepting[/b] incidents', NULL, 'userid=1', 'sticky');
INSERT INTO `{$dbNoticeTemplates}` (`name`, `type`, `description`, `text`, `linktext`, `link`, `durability`) VALUES('TRIGGER_USER_RETURNS', 0, 'Used when a user sets back to accepting', '{realname} is now [b]accepting[/b] incidents', NULL, NULL, 'sticky');


CREATE TABLE `{$dbPermissions}` (
  `id` int(5) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;


INSERT INTO `{$dbPermissions}` VALUES (1, 'Add new contacts');
INSERT INTO `{$dbPermissions}` VALUES (2, 'Add new sites');
INSERT INTO `{$dbPermissions}` VALUES (3, 'Edit existing site details');
INSERT INTO `{$dbPermissions}` VALUES (4, 'Edit your profile');
INSERT INTO `{$dbPermissions}` VALUES (5, 'Add Incidents');
INSERT INTO `{$dbPermissions}` VALUES (6, 'View Incidents');
INSERT INTO `{$dbPermissions}` VALUES (7, 'Edit Incidents');
INSERT INTO `{$dbPermissions}` VALUES (8, 'Update Incidents');
INSERT INTO `{$dbPermissions}` VALUES (9, 'Edit User Permissions');
INSERT INTO `{$dbPermissions}` VALUES (10, 'Edit contacts');
INSERT INTO `{$dbPermissions}` VALUES (11, 'View Sites');
INSERT INTO `{$dbPermissions}` VALUES (12, 'View Contacts');
INSERT INTO `{$dbPermissions}` VALUES (13, 'Reassign Incidents');
INSERT INTO `{$dbPermissions}` VALUES (14, 'View Users');
INSERT INTO `{$dbPermissions}` VALUES (15, 'Add Supported Products');
INSERT INTO `{$dbPermissions}` VALUES (16, 'Add Email Templates');
INSERT INTO `{$dbPermissions}` VALUES (17, 'Edit Email Templates');
INSERT INTO `{$dbPermissions}` VALUES (18, 'Close Incidents');
INSERT INTO `{$dbPermissions}` VALUES (19, 'View Maintenance Contracts');
INSERT INTO `{$dbPermissions}` VALUES (20, 'Add Users');
INSERT INTO `{$dbPermissions}` VALUES (21, 'Edit Maintenance Contracts');
INSERT INTO `{$dbPermissions}` VALUES (22, 'Administrate');
INSERT INTO `{$dbPermissions}` VALUES (23, 'Edit User');
INSERT INTO `{$dbPermissions}` VALUES (24, 'Add Product');
INSERT INTO `{$dbPermissions}` VALUES (25, 'Add Product Information');
INSERT INTO `{$dbPermissions}` VALUES (26, 'Get Help');
INSERT INTO `{$dbPermissions}` VALUES (27, 'View Your Calendar');
INSERT INTO `{$dbPermissions}` VALUES (28, 'View Products and Software');
INSERT INTO `{$dbPermissions}` VALUES (29, 'Edit Products');
INSERT INTO `{$dbPermissions}` VALUES (30, 'View Supported Products');
INSERT INTO `{$dbPermissions}` VALUES (32, 'Edit Supported Products');
INSERT INTO `{$dbPermissions}` VALUES (33, 'Send Emails');
INSERT INTO `{$dbPermissions}` VALUES (34, 'Reopen Incidents');
INSERT INTO `{$dbPermissions}` VALUES (35, 'Set your status');
INSERT INTO `{$dbPermissions}` VALUES (36, 'Set contact flags');
INSERT INTO `{$dbPermissions}` VALUES (37, 'Run Reports');
INSERT INTO `{$dbPermissions}` VALUES (38, 'View Sales Incidents');
INSERT INTO `{$dbPermissions}` VALUES (39, 'Add Maintenance Contract');
INSERT INTO `{$dbPermissions}` VALUES (40, 'Reassign Incident when user not accepting');
INSERT INTO `{$dbPermissions}` VALUES (41, 'View Status');
INSERT INTO `{$dbPermissions}` VALUES (42, 'Review/Delete Incident updates');
INSERT INTO `{$dbPermissions}` VALUES (43, 'Edit Global Signature');
INSERT INTO `{$dbPermissions}` VALUES (44, 'Publish files to FTP site');
INSERT INTO `{$dbPermissions}` VALUES (45, 'View Mailing List Subscriptions');
INSERT INTO `{$dbPermissions}` VALUES (46, 'Edit Mailing List Subscriptions');
INSERT INTO `{$dbPermissions}` VALUES (47, 'Administrate Mailing Lists');
INSERT INTO `{$dbPermissions}` VALUES (48, 'Add Feedback Forms');
INSERT INTO `{$dbPermissions}` VALUES (49, 'Edit Feedback Forms');
INSERT INTO `{$dbPermissions}` VALUES (50, 'Approve Holidays');
INSERT INTO `{$dbPermissions}` VALUES (51, 'View Feedback');
INSERT INTO `{$dbPermissions}` VALUES (52, 'View Hidden Updates');
INSERT INTO `{$dbPermissions}` VALUES (53, 'Edit Service Levels');
INSERT INTO `{$dbPermissions}` VALUES (54, 'View KB Articles');
INSERT INTO `{$dbPermissions}` VALUES (55, 'Delete Sites/Contacts');
INSERT INTO `{$dbPermissions}` VALUES (56, 'Add Software');
INSERT INTO `{$dbPermissions}` VALUES (57, 'Disable User Accounts');
INSERT INTO `{$dbPermissions}` VALUES (58, 'Edit your Software Skills');
INSERT INTO `{$dbPermissions}` VALUES (59, 'Manage users software skills');
INSERT INTO `{$dbPermissions}` VALUES (60, 'Perform Searches');
INSERT INTO `{$dbPermissions}` VALUES (61, 'View Incident Details');
INSERT INTO `{$dbPermissions}` VALUES (62, 'View Incident Attachments');
INSERT INTO `{$dbPermissions}` VALUES (63, 'Add Reseller');
INSERT INTO `{$dbPermissions}` VALUES (64, 'Manage Escalation Paths');
INSERT INTO `{$dbPermissions}` VALUES (65, 'Delete Products');
INSERT INTO `{$dbPermissions}` VALUES (66, 'Install Dashboard Components');
INSERT INTO `{$dbPermissions}` VALUES (67, 'Run Management Reports');
INSERT INTO `{$dbPermissions}` VALUES (68, 'Manage Holidays');
INSERT INTO `{$dbPermissions}` VALUES (69, 'View your Tasks');
INSERT INTO `{$dbPermissions}` VALUES (70, 'Create/Edit your Tasks');
INSERT INTO `{$dbPermissions}` VALUES (71, 'Manage your Triggers');
INSERT INTO `{$dbPermissions}` VALUES (72, 'Manage System Triggers');
INSERT INTO `{$dbPermissions}` VALUES (73, 'Approve Billable Incidents');
INSERT INTO `{$dbPermissions}` VALUES (74, 'Set duration without activity (for billable incidents)');
INSERT INTO `{$dbPermissions}` VALUES (75, 'Set negative time for duration on incidents (for billable incidents - refunds)');
INSERT INTO `{$dbPermissions}` VALUES (76, 'View Transactions');
INSERT INTO `{$dbPermissions}` VALUES (77, 'View Billing Information');


CREATE TABLE `{$dbPriority}` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM COMMENT='Used in incidents.php' AUTO_INCREMENT=5 ;

INSERT INTO `{$dbPriority}` VALUES (1, 'Low');
INSERT INTO `{$dbPriority}` VALUES (2, 'Medium');
INSERT INTO `{$dbPriority}` VALUES (3, 'High');
INSERT INTO `{$dbPriority}` VALUES (4, 'Critical');


CREATE TABLE `{$dbProductInfo}` (
  `id` int(11) NOT NULL auto_increment,
  `productid` int(11) default NULL,
  `information` text,
  `moreinformation` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;


CREATE TABLE `{$dbProducts}` (
  `id` int(11) NOT NULL auto_increment,
  `vendorid` int(5) NOT NULL default '0',
  `name` varchar(50) default NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `vendorid` (`vendorid`),
  KEY `name` (`name`)
) ENGINE=MyISAM COMMENT='Current List of Products' ;

INSERT INTO `{$dbProducts}` VALUES (1,1,'Example Product','This is an example product.');


CREATE TABLE `{$dbRelatedIncidents}` (
`id` INT( 5 ) NOT NULL AUTO_INCREMENT ,
`incidentid` INT( 5 ) NOT NULL ,
`relation` ENUM( 'child', 'sibling' ) DEFAULT 'child' NOT NULL ,
`relatedid` INT( 5 ) NOT NULL ,
`owner` int(5) NOT NULL default '0',
PRIMARY KEY ( `id` ) ,
INDEX ( `incidentid` , `relatedid` )
) ENGINE=MyISAM;

CREATE TABLE `{$dbResellers}` (
  `id` tinyint(4) NOT NULL auto_increment,
  `name` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

INSERT INTO `{$dbResellers}` VALUES (1,'Us (No Reseller)');
INSERT INTO `{$dbResellers}` VALUES (2,'Example Reseller');


CREATE TABLE `{$dbRoles}` (
`id` INT( 5 ) NOT NULL AUTO_INCREMENT ,
`rolename` VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE=MyISAM;

INSERT INTO `{$dbRoles}` ( `id` , `rolename` ) VALUES ('1', 'Administrator');
INSERT INTO `{$dbRoles}` ( `id` , `rolename` ) VALUES ('2', 'Manager');
INSERT INTO `{$dbRoles}` ( `id` , `rolename` ) VALUES ('3', 'User');


CREATE TABLE `{$dbRolePermissions}` (
`roleid` tinyint( 4 ) NOT NULL default '0',
`permissionid` int( 5 ) NOT NULL default '0',
`granted` enum( 'true', 'false' ) NOT NULL default 'false',
PRIMARY KEY ( `roleid` , `permissionid` )
) ENGINE=MyISAM;

INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 1, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 2, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 3, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 4, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 5, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 6, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 7, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 8, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 9, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 10, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 11, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 12, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 13, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 14, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 15, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 16, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 17, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 18, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 19, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 20, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 21, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 22, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 23, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 24, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 25, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 26, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 27, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 28, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 29, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 30, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 32, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 33, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 34, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 35, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 36, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 37, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 38, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 39, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 40, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 41, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 42, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 43, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 44, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 45, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 46, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 47, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 48, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 49, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 50, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 51, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 52, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 53, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 54, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 55, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 56, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 57, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 58, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 59, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 60, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 61, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 62, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 63, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 64, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 65, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 66, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 67, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 68, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 69, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 70, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 71, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 72, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 73, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 74, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 75, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 1, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 2, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 3, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 4, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 5, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 6, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 7, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 8, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 10, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 11, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 12, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 13, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 14, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 15, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 16, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 17, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 18, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 19, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 21, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 24, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 25, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 26, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 27, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 28, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 29, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 30, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 32, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 33, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 34, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 35, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 36, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 37, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 38, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 39, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 40, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 41, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 42, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 43, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 44, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 45, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 46, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 47, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 48, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 49, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 50, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 51, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 52, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 53, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 54, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 55, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 56, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 58, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 59, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 60, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 61, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 62, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 67, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 69, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 70, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 71, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 1, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 2, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 3, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 4, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 5, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 6, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 7, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 8, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 10, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 11, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 12, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 13, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 14, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 18, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 19, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 26, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 27, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 28, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 30, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 33, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 34, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 35, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 36, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 37, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 38, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 41, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 44, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 52, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 54, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 58, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 60, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 61, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 62, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 69, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 70, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (3, 71, 'true');

CREATE TABLE `{$dbScheduler}` (
  `id` int(11) NOT NULL auto_increment,
  `action` varchar(50) NOT NULL,
  `params` varchar(255) NOT NULL,
  `paramslabel` varchar(255) default NULL,
  `description` tinytext NOT NULL,
  `status` enum('enabled','disabled') NOT NULL default 'enabled',
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  `interval` int(11) NOT NULL,
  `lastran` datetime NOT NULL,
  `success` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `job` (`action`)
) ENGINE=MyISAM  ;

INSERT INTO `{$dbScheduler}` (`id`, `action`, `params`, `paramslabel`, `description`, `status`, `start`, `end`, `interval`, `lastran`, `success`) VALUES (1, 'CloseIncidents', '554400', 'closure_delay', 'Close incidents that have been marked for closure for longer than the <var>closure_delay</var> parameter (which is in seconds)', 'enabled', '2008-01-01 00:00:00', '0000-00-00 00:00:00', 60, '0000-00-00 00:00:00', 1);
INSERT INTO `{$dbScheduler}` (`id`, `action`, `params`, `paramslabel`, `description`, `status`, `start`, `end`, `interval`, `lastran`, `success`) VALUES (2, 'SetUserStatus', '', NULL, '(EXPERIMENTAL) This will set users status                         based on data from their holiday calendar.                        e.g. Out of Office/Away sick.', 'enabled', '2008-01-01 00:00:00', '0000-00-00 00:00:00', 60, '0000-00-00 00:00:00', 1);
INSERT INTO `{$dbScheduler}` (`id`, `action`, `params`, `paramslabel`, `description`, `status`, `start`, `end`, `interval`, `lastran`, `success`) VALUES (3, 'PurgeJournal', '', NULL, 'Delete old journal entries according to the config setting <var>\$CONFIG[''journal_purge_after'']</var>', 'enabled', '2008-01-01 00:00:00', '0000-00-00 00:00:00', 300, '0000-00-00 00:00:00', 1);
INSERT INTO `{$dbScheduler}` (`id`, `action`, `params`, `paramslabel`, `description`, `status`, `start`, `end`, `interval`, `lastran`, `success`) VALUES (4, 'TimeCalc', '', NULL, 'Calculate SLA Target Times and trigger                        OUT_OF_SLA and OUT_OF_REVIEW system email templates where appropriate.', 'enabled', '2008-01-01 00:00:00', '0000-00-00 00:00:00', 60, '0000-00-00 00:00:00', 1);
INSERT INTO `{$dbScheduler}` (`id`, `action`, `params`, `paramslabel`, `description`, `status`, `start`, `end`, `interval`, `lastran`, `success`) VALUES (5, 'ChaseCustomers', '', NULL, 'Chase customers', 'disabled', '2008-01-01 00:00:00', '0000-00-00 00:00:00', 3600, '0000-00-00 00:00:00', 1);
INSERT INTO `{$dbScheduler}` (`id`, `action`, `params`, `paramslabel`, `description`, `status`, `start`, `end`, `interval`, `lastran`, `success`) VALUES (6, 'CheckWaitingEmail', '', NULL, 'Checks the holding queue for emails and fires the TRIGGER_WAITING_HELD_EMAIL trigger when it finds some.', 'enabled', '2008-01-01 00:00:00', '0000-00-00 00:00:00', 60, '0000-00-00 00:00:00', 1);
INSERT INTO `{$dbScheduler}` (`id`, `action`, `params`, `paramslabel`, `description`, `status`, `start`, `end`, `interval`, `lastran`, `success`) VALUES (7, 'PurgeExpiredFTPItems', '', NULL, 'purges files which have expired from the FTP site when run.', 'enabled', '2008-01-01 00:00:00', '0000-00-00 00:00:00', 216000, '0000-00-00 00:00:00', 1);


CREATE TABLE `{$dbServiceLevels}` (
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
  KEY `review_days` (`review_days`)
) ENGINE=MyISAM;

INSERT INTO `{$dbServiceLevels}` VALUES (0, 'standard', 1, 320, 380, 960, 14.00, 28, 90, 0);
INSERT INTO `{$dbServiceLevels}` VALUES (0, 'standard', 2, 240, 320, 960, 10.00, 20, 90, 0);
INSERT INTO `{$dbServiceLevels}` VALUES (0, 'standard', 3, 120, 180, 480, 7.00, 14, 90, 0);
INSERT INTO `{$dbServiceLevels}` VALUES (0, 'standard', 4, 60, 120, 240, 3.00, 6, 90, 0);


CREATE TABLE `{$dbSetTags}` (
`id` INT NOT NULL ,
`type` MEDIUMINT NOT NULL ,
`tagid` INT NOT NULL ,
PRIMARY KEY ( `id` , `type` , `tagid` )
) ENGINE=MYISAM;


CREATE TABLE `{$dbSiteContacts}` (
  `siteid` int(11) NOT NULL default '0',
  `contactid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`siteid`,`contactid`)
) ENGINE=MyISAM;


CREATE TABLE `{$dbSites}` (
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

INSERT INTO `{$dbSites}` (`id`, `name`, `department`, `address1`, `address2`, `city`, `county`,
`country`, `postcode`, `telephone`, `fax`, `email`, `notes`, `typeid`, `freesupport`, `licenserx`,
 `owner`) VALUES (1, 'ACME Widgets Co.', 'Manufacturing Dept.', '21 Any Street', '',
'Anytown', 'Anyshire', 'UNITED KINGDOM', 'AN1 0TH', '0555 555555', '0444 444444', 'acme@example.com',
'Example site', 1, 0, 0, 0);


CREATE TABLE `{$dbSiteTypes}` (
  `typeid` int(5) NOT NULL auto_increment,
  `typename` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`typeid`)
) ENGINE=MyISAM;

INSERT INTO `{$dbSiteTypes}` VALUES (1, 'Unclassified');
INSERT INTO `{$dbSiteTypes}` VALUES (2, 'Commercial');
INSERT INTO `{$dbSiteTypes}` VALUES (3, 'Academic');


CREATE TABLE `{$dbSoftware}` (
  `id` int(5) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `vendorid` INT( 5 ) NOT NULL default '0',
  `software` int(5) NOT NULL default '0',
  `lifetime_start` date default NULL,
  `lifetime_end` date default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM COMMENT='Individual software products as they are supported' AUTO_INCREMENT=1 ;

INSERT INTO `{$dbSoftware}` (`id`, `name`, `lifetime_start`, `lifetime_end`) VALUES (1, 'Example Software', NULL, NULL);


CREATE TABLE `{$dbSoftwareProducts}` (
  `productid` int(5) NOT NULL default '0',
  `softwareid` int(5) NOT NULL default '0',
  PRIMARY KEY  (`productid`,`softwareid`)
) ENGINE=MyISAM COMMENT='Table to link products with software';

INSERT INTO `{$dbSoftwareProducts}` VALUES (1,1);


CREATE TABLE `{$dbSpellcheck}` (
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


CREATE TABLE `{$dbSupportContacts}` (
  `id` int(11) NOT NULL auto_increment,
  `maintenanceid` int(11) default NULL,
  `contactid` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

INSERT INTO `{$dbSupportContacts}` VALUES (1,1,1);


CREATE TABLE `{$dbSystem}` (
  `id` int(1) NOT NULL default '0',
  `version` float(3,2) NOT NULL default '0.00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;


CREATE TABLE `{$dbTags}` (
  `tagid` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`tagid`)
) ENGINE=MyISAM;


CREATE TABLE `{$dbTasks}` (
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
  `distribution` enum('public','private', 'incident', 'event') NOT NULL default 'public',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `lastupdated` timestamp(14) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `owner` (`owner`)
) ENGINE=MyISAM ;


CREATE TABLE `{$dbTempAssigns}` (
  `incidentid` int(5) NOT NULL default '0',
  `originalowner` int(5) NOT NULL default '0',
  `userstatus` tinyint(4) NOT NULL default '1',
  `assigned` enum('yes','no') NOT NULL default 'no',
  PRIMARY KEY  (`incidentid`,`originalowner`),
  KEY `assigned` (`assigned`)
) ENGINE=MyISAM;


CREATE TABLE `{$dbTempIncoming}` (
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


CREATE TABLE `{$dbUpdates}` (
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


CREATE TABLE `{$dbUserGroups}` (
  `userid` int(5) NOT NULL default '0',
  `groupid` int(5) NOT NULL default '0',
  PRIMARY KEY  (`userid`,`groupid`)
) ENGINE=MyISAM COMMENT='Links users with groups';



CREATE TABLE `{$dbUserPermissions}` (
  `userid` tinyint(4) NOT NULL default '0',
  `permissionid` int(5) NOT NULL default '0',
  `granted` enum('true','false') NOT NULL default 'false',
  PRIMARY KEY  (`userid`,`permissionid`)
) ENGINE=MyISAM;

INSERT INTO `{$dbUserPermissions}` VALUES (1, 1, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 2, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 3, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 4, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 5, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 6, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 7, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 8, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 9, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 10, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 11, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 12, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 13, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 14, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 15, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 16, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 17, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 18, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 19, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 20, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 21, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 22, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 23, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 24, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 25, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 26, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 27, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 28, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 29, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 30, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 31, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 32, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 33, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 34, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 35, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 36, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 37, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 38, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 39, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 40, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 41, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 42, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 43, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 44, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 45, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 46, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 47, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 48, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 49, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 50, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 51, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 52, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 53, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 54, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 55, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 56, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 57, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 58, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 59, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 60, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 61, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 62, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 63, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 64, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 65, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 66, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 67, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 68, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 69, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 70, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 71, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 72, 'true');


CREATE TABLE `{$dbUsers}` (
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
  `var_hideautoupdates` enum('true','false') NOT NULL default 'false',
  `var_hideheader` enum('true','false') NOT NULL default 'false',
  `var_monitor` enum('true','false') NOT NULL default 'true',
  `var_i18n` varchar(20) default NULL,
  `var_utc_offset` int(11) NOT NULL default '0' COMMENT 'Offset from UTC (timezone)',
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


CREATE TABLE `{$dbUserSoftware}` (
  `userid` tinyint(4) NOT NULL default '0',
  `softwareid` int(5) NOT NULL default '0',
  `backupid` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`userid`,`softwareid`),
  KEY `backupid` (`backupid`)
) ENGINE=MyISAM COMMENT='Defines which software users have expertise with';


CREATE TABLE `{$dbUserStatus}` (
  `id` int(11) NOT NULL,
  `name` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;


INSERT INTO `{$dbUserStatus}` VALUES (0, 'strAccountDisabled');
INSERT INTO `{$dbUserStatus}` VALUES (1, 'strInOffice');
INSERT INTO `{$dbUserStatus}` VALUES (2, 'strNotInOffice');
INSERT INTO `{$dbUserStatus}` VALUES (3, 'strInMeeting');
INSERT INTO `{$dbUserStatus}` VALUES (4, 'strAtLunch');
INSERT INTO `{$dbUserStatus}` VALUES (5, 'strOnHoliday');
INSERT INTO `{$dbUserStatus}` VALUES (6, 'strWorkingFromHome');
INSERT INTO `{$dbUserStatus}` VALUES (7, 'strOnTrainingCourse');
INSERT INTO `{$dbUserStatus}` VALUES (8, 'strAbsentSick');
INSERT INTO `{$dbUserStatus}` VALUES (9, 'strWorkingAway');


CREATE TABLE `{$dbVendors}` (
  `id` int(5) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

INSERT INTO `{$dbVendors}` VALUES (1,'Default');


CREATE TABLE `{$dbTriggers}` (
  `id` int(11) NOT NULL auto_increment,
  `triggerid` varchar(50) NOT NULL,
  `userid` tinyint(4) NOT NULL,
  `action` enum('ACTION_NONE','ACTION_EMAIL','ACTION_NOTICE','ACTION_JOURNAL') NOT NULL default 'ACTION_NONE',
  `template` int(11) NOT NULL,
  `parameters` varchar(255) default NULL,
  `checks` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `triggerid` (`triggerid`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM;

INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_INCIDENT_CREATED', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_INCIDENT_ASSIGNED', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_INCIDENT_ASSIGNED_WHILE_AWAY', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_INCIDENT_ASSIGNED_WHILE_OFFLINE', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_INCIDENT_NEARING_SLA', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_USERS_INCIDENT_NEARING_SLA', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_INCIDENT_EXCEEDED_SLA', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_INCIDENT_REVIEW_DUE', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_CRITICAL_INCIDENT_CREATED', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_KB_CREATED', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_NEW_HELD_EMAIL', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_WAITING_HELD_EMAIL', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_USER_SET_TO_AWAY', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_SIT_UPGRADED', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_USER_RETURNS', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_INCIDENT_OWNED_CLOSED_BY_USER', '0', 'ACTION_JOURNAL');

";

// ********************************************************************

$upgrade_schema[321] = "CREATE TABLE `{$dbSystem}`
  (`id` INT( 1 ) NOT NULL ,
  `version` FLOAT( 3, 2 ) DEFAULT '0.00' NOT NULL ,
  PRIMARY KEY ( `id` )) ENGINE=MyISAM;

CREATE TABLE `{$dbFeedbackForms}`` (
  `id` int(5) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `introduction` text NOT NULL,
  `thanks` text NOT NULL,
  `description` text NOT NULL,
  `multi` enum('yes','no') NOT NULL default 'no',
  PRIMARY KEY  (`id`),
  KEY `multi` (`multi`)
) ENGINE=MyISAM;
ALTER TABLE `{$dbFeedbackRespondents}` CHANGE `respondent` `contactid` INT( 11 ) NOT NULL;
ALTER TABLE `{$dbFeedbackRespondents}` CHANGE `responseref` `incidentid` INT( 11 ) NOT NULL;
ALTER TABLE `{$dbFeedbackReport}` CHANGE `respondent` `respondent` INT( 11 ) NOT NULL;
ALTER TABLE `{$CONFIG['db_tableprefix']}emailtype` ADD `customervisibility` ENUM( 'show', 'hide' ) DEFAULT 'show' NOT NULL ;
";

$upgrade_schema[322] = "CREATE TABLE `{$dbRoles}` (
`id` INT( 5 ) NOT NULL AUTO_INCREMENT ,
`rolename` VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE=MyISAM;

INSERT INTO `{$dbRoles}` ( `id` , `rolename` ) VALUES ('1', 'Administrator');
INSERT INTO `{$dbRoles}` ( `id` , `rolename` ) VALUES ('2', 'Manager');
INSERT INTO `{$dbRoles}` ( `id` , `rolename` ) VALUES ('3', 'User');

CREATE TABLE `{$dbRolePermissions}` (
`roleid` tinyint( 4 ) NOT NULL default '0',
`permissionid` int( 5 ) NOT NULL default '0',
`granted` enum( 'true', 'false' ) NOT NULL default 'false',
PRIMARY KEY ( `roleid` , `permissionid` )
) ENGINE=MyISAM;

ALTER TABLE `{$dbUsers}` ADD `roleid` INT( 5 ) NOT NULL DEFAULT '1' AFTER `realname` ;
ALTER TABLE `{$dbUsers}` DROP `accesslevel` ;
";

$upgrade_schema[323] = "CREATE TABLE `{$dbRelatedIncidents}` (
`id` int(5) NOT NULL auto_increment,
`incidentid` int(5) NOT NULL default '0',
`relation` enum('child','sibling') NOT NULL default 'child',
`relatedid` int(5) NOT NULL default '0',
`owner` int(5) NOT NULL default '0',
PRIMARY KEY  (`id`),
KEY `incidentid` (`incidentid`,`relatedid`)
) ENGINE=MyISAM;

ALTER TABLE `{$dbSites}` CHANGE `notes` `notes` TEXT NOT NULL ;

ALTER TABLE `{$dbSites}` DROP `ftnpassword`;

UPDATE `{$dbPermissions}` SET `name` = 'View Products and Software' WHERE `id` =28 LIMIT 1 ;
UPDATE `{$dbPermissions}` SET `name` = 'Edit Products' WHERE `id` =29 LIMIT 1 ;
UPDATE `{$dbPermissions}` SET `name` = 'Add Feedback Forms' WHERE `id` =48 LIMIT 1 ;
UPDATE `{$dbPermissions}` SET `name` = 'Edit Feedback Forms' WHERE `id` =49 LIMIT 1 ;
UPDATE `{$dbPermissions}` SET `name` = 'View Feedback' WHERE `id` =51 LIMIT 1 ;
UPDATE `{$dbPermissions}` SET `name` = 'Edit Service Levels' WHERE `id` =53 LIMIT 1 ;

INSERT INTO `{$dbIncidentStatus}` VALUES (10, 'Active (Unassigned)', 'Active');
UPDATE `{$dbIncidentStatus}` SET `id` = '0' WHERE `id` =10 LIMIT 1 ;
";

$upgrade_schema[324] = "ALTER TABLE `{$dbUsers}` ADD `groupid` INT( 5 ) NULL AFTER `roleid` ;
ALTER TABLE `{$dbUsers}` ADD INDEX ( `groupid` ) ;
ALTER TABLE `{$dbSoftware}` ADD `lifetime_start` DATE NULL, ADD `lifetime_end` DATE NULL ;
ALTER TABLE `{$CONFIG['db_tableprefix']}emailtype` ADD `storeinlog` ENUM( 'No', 'Yes' ) NOT NULL DEFAULT 'Yes';
ALTER TABLE `{$dbUpdates}`
  DROP `timesincesla`,
  DROP `timesincereview`,
  DROP `reviewcalculated`,
  DROP `slacalculated`;
ALTER TABLE `{$dbUsers}` ADD `var_notify_on_reassign` ENUM( 'true', 'false' ) NOT NULL DEFAULT 'false' AFTER `var_monitor`;
UPDATE `{$dbUsers}` SET `var_notify_on_reassign` = 'false';
INSERT INTO `{$CONFIG['db_tableprefix']}emailtype` (`name`, `type`, `description`, `tofield`, `fromfield`,
`replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`,
`storeinlog`) VALUES ('INCIDENT_REASSIGNED_USER_NOTIFY', 'system', 'Notify user when call assigned to them',
'<incidentreassignemailaddress>', '<supportemail>','<supportemail>', '', '',
'A <incidentpriority> priority call ([<incidentid>] - <incidenttitle>) has been reassigned to you',
'Hi,\r\n\r\nIncident [<incidentid>] entitled <incidenttitle> has been reassigned to you.\r\n\r\nThe details of this incident are:\r\n\r\nPriority: <incidentpriority>\r\nContact: <contactname>\r\nSite: <contactsite>\r\n\r\n\r\nRegards\r\n<applicationname>\r\n\r\n\r\n---\r\n<todaysdate> - <applicationshortname> <applicationversion>',
'hide', 'No');
UPDATE `{$CONFIG['db_tableprefix']}emailtype` SET `toField` = '<incidentreassignemailaddress>' WHERE `name` =  'INCIDENT_REASSIGNED_USER_NOTIFY';

CREATE TABLE `{$dbTasks}` (
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

ALTER TABLE `{$dbTempIncoming}` ADD `lockeduntil` DATETIME NULL AFTER `locked` ;
INSERT INTO `{$dbPermissions}` VALUES (63, 'Add Reseller');

CREATE TABLE `{$dbNotes}` (
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

CREATE TABLE `{$dbEscalationPaths}` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `track_url` varchar(255) default NULL,
  `home_url` varchar(255) NOT NULL default '',
  `url_title` varchar(255) default NULL,
  `email_domain` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

ALTER TABLE `{$dbIncidents}` ADD `escalationpath` INT( 11 ) NULL AFTER `id` ;
INSERT INTO `{$dbPermissions}` VALUES (64, 'Manage Escalation Paths');

CREATE TABLE `{$dbDashboard}` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `enabled` enum('true','false') NOT NULL default 'false',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

INSERT INTO `{$dbDashboard}` (`id`, `name`, `enabled`) VALUES (1, 'random_tip', 'true'),
(2, 'statistics', 'true'),
(3, 'tasks', 'true'),
(4, 'user_incidents', 'true');

UPDATE `{$dbInterfaceStyles}` SET `name` = 'Light Blue' WHERE `id` =1 LIMIT 1 ;
";


/*
 3.25 (Actual release was 3.30)
*/
$upgrade_schema[325] = "
ALTER TABLE `{$dbInterfaceStyles}` ADD `iconset` VARCHAR( 255 ) NOT NULL DEFAULT 'sit' AFTER `cssurl` ;
ALTER TABLE `{$dbSites}` ADD `websiteurl` VARCHAR( 255 ) NULL AFTER `email` ;

CREATE TABLE `tags` (
  `tagid` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`tagid`)
) ENGINE=MyISAM;

CREATE TABLE `{$dbSetTags}` (
`id` INT NOT NULL ,
`type` MEDIUMINT NOT NULL ,
`tagid` INT NOT NULL ,
PRIMARY KEY ( `id` , `type` , `tagid` )
) ENGINE=MYISAM;

CREATE TABLE `{$dbLinks}` (
     `linktype` int(11) NOT NULL default '0',
     `origcolref` int(11) NOT NULL default '0',
     `linkcolref` int(11) NOT NULL default '0',
     `direction` enum('left','right','bi') NOT NULL default 'left',
     `userid` tinyint(4) NOT NULL default '0',
     PRIMARY KEY  (`linktype`,`origcolref`,`linkcolref`),
     KEY `userid` (`userid`)
   ) ENGINE=MyISAM ;

CREATE TABLE `{$dbLinkTypes}` (
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

INSERT INTO `{$dbLinkTypes}` VALUES (1,'Task','Subtask','Parent Task','tasks','id','tasks','id','name','','view_task.php?id=%id%'),(2,'Contact','Contact','Contact Task','tasks','id','contacts','id','forenames','','contact_details.php?id=%id%'),(3,'Site','Site','Site Task','tasks','id','sites','id','name','','site_details.php?id=%id%'),(4,'Incident','Incident','Task','tasks','id','incidents','id','title','','incident_details.php?id=%id%');

ALTER TABLE `{$dbUsers}` ADD `var_num_updates_view` INT NOT NULL DEFAULT '15' AFTER `var_update_order` ;
INSERT INTO `{$dbEmailType}` (`id`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`) VALUES ('NEARING_SLA', 'system', 'Notification when an incident nears its SLA target', '<supportmanageremail>', '<supportemail>', '<supportemail>', '<useremail>', '', '<applicationshortname> SLA: Incident <incidentid> about to breach SLA', 'This is an automatic notification that this incident is about to breach it\'s SLA.  The SLA target <info1> will expire in <info2> minutes.\r\n\r\nIncident: [<incidentid>] - <incidenttitle>\r\nOwner: <incidentowner>\r\nPriority: <incidentpriority>\r\nExternal Id: <incidentexternalid>\r\nExternal Engineer: <incidentexternalengineer>\r\nSite: <contactsite>\r\nContact: <contactname>\r\n\r\n--\r\n<applicationshortname> v<applicationversion>\r\n<todaysdate>\r\n', 'hide', 'Yes');

INSERT INTO `{$dbPermissions}` VALUES (65, 'Delete Products');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 65, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 65, 'true');

ALTER TABLE `{$dbUsers}` ADD `dashboard` VARCHAR( 255 ) NOT NULL DEFAULT '0-3,1-1,1-2,2-4';

INSERT INTO `{$dbPermissions}` VALUES (66, 'Install Dashboard Components');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 66, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 66, 'true');
INSERT INTO `{$dbClosingStatus}` ( `id` , `name` ) VALUES ( NULL , 'Escalated' );
ALTER TABLE `{$dbTasks}` ADD `enddate` DATETIME NULL AFTER `startdate` ;

INSERT INTO `{$dbPermissions}` VALUES (67, 'Run Management Reports');

INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 67, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 67, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 67, 'true');

INSERT INTO `{$dbPermissions}` VALUES (68, 'Manage Holidays');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 68, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 68, 'true');
INSERT INTO `{$dbUserPermissions}` VALUES (1, 68, 'true');

ALTER TABLE `{$dbSites}` ADD `active` ENUM( 'true', 'false' ) NOT NULL DEFAULT 'true';

ALTER TABLE `{$dbContacts}` ADD `active` ENUM( 'true', 'false' ) NOT NULL DEFAULT 'true';

-- beta 3
UPDATE `{$dbInterfaceStyles}` SET `iconset` = 'sit';

ALTER TABLE `{$dbUpdates}` CHANGE `type` `type` ENUM( 'default', 'editing', 'opening', 'email', 'reassigning', 'closing', 'reopening', 'auto', 'phonecallout', 'phonecallin', 'research', 'webupdate', 'emailout', 'emailin', 'externalinfo', 'probdef', 'solution', 'actionplan', 'slamet', 'reviewmet', 'tempassigning', 'auto_chase_email', 'auto_chase_phone', 'auto_chase_manager', 'auto_chased_phone','auto_chased_manager','auto_chase_managers_manager') NULL DEFAULT 'default';
";

$upgrade_schema[331] = "
ALTER TABLE `{$dbUpdates}` CHANGE `type` `type` ENUM( 'default', 'editing', 'opening', 'email', 'reassigning', 'closing', 'reopening', 'auto', 'phonecallout', 'phonecallin', 'research', 'webupdate', 'emailout', 'emailin', 'externalinfo', 'probdef', 'solution', 'actionplan', 'slamet', 'reviewmet', 'tempassigning', 'auto_chase_email', 'auto_chase_phone', 'auto_chase_manager', 'auto_chased_phone','auto_chased_manager','auto_chase_managers_manager','customerclosurerequest') NULL DEFAULT 'default';

CREATE TABLE IF NOT EXISTS `{$dbDrafts}` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `incidentid` int(11) NOT NULL,
  `type` enum('update','email') NOT NULL,
  `content` text NOT NULL,
  `meta` text NOT NULL,
  `lastupdate` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

ALTER TABLE `{$dbSoftware}` ADD `vendorid` INT( 5 ) NOT NULL AFTER `name` ;

CREATE TABLE IF NOT EXISTS `{$dbNotices}` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

ALTER TABLE `{$dbServiceLevels}` ADD `timed` enum('yes','no') NOT NULL DEFAULT 'no' ;

ALTER TABLE `{$dbUsers}` ADD `var_i18n` VARCHAR( 20 ) NULL AFTER `var_notify_on_reassign` ;

ALTER TABLE `{$dbUpdates}` ADD `duration` INT NULL ;

INSERT INTO `{$dbPermissions}` VALUES (69, 'Post Notices');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 69, 'true');
INSERT INTO `{$dbUserPermissions}` (`userid`, `permissionid`, `granted`) VALUES (1, 69, 'true');

ALTER TABLE `{$dbUsers}` ADD `lastseen` TIMESTAMP NOT NULL ;

ALTER TABLE `{$dbTasks}` CHANGE `distribution` `distribution` ENUM( 'public', 'private', 'incident' ) NOT NULL DEFAULT 'public' ;

ALTER TABLE `{$dbUpdates}` CHANGE `type` `type` ENUM( 'default', 'editing', 'opening', 'email', 'reassigning', 'closing', 'reopening', 'auto', 'phonecallout', 'phonecallin', 'research', 'webupdate', 'emailout', 'emailin', 'externalinfo', 'probdef', 'solution', 'actionplan', 'slamet', 'reviewmet', 'tempassigning', 'auto_chase_email', 'auto_chase_phone', 'auto_chase_manager', 'auto_chased_phone', 'auto_chased_manager', 'auto_chase_managers_manager', 'customerclosurerequest', 'fromtask' ) NULL DEFAULT 'default' ;


-- KMH 15Nov07
ALTER TABLE `{$dbMaintenance}` ADD `supportedcontacts` INT( 255 ) NOT NULL DEFAULT '0';
ALTER TABLE `{$dbMaintenance}` ADD `allcontactssupported` ENUM( 'No', 'Yes' ) NOT NULL DEFAULT 'No';

-- INL 25Nov07
DROP TABLE `{$CONFIG['db_tableprefix']}holidaytypes`;

-- PH 26Nov07
CREATE TABLE `{$dbBillingPeriods}` (
`servicelevelid` INT( 5 ) NOT NULL ,
`engineerperiod` INT NOT NULL COMMENT 'In minutes',
`customerperiod` INT NOT NULL COMMENT 'In minutes',
PRIMARY KEY r( `servicelevelid` )
) ENGINE = MYISAM ;

-- KMH 26/11/07
ALTER TABLE `{$dbIncidents}` ADD `slanotice` TINYINT(1) NOT NULL DEFAULT '0' AFTER `slaemail` ;

-- PH 1/12/07
ALTER TABLE `{$dbBillingPeriods}` ADD `{$dbPriority}` INT( 4 ) NOT NULL AFTER `servicelevelid` ;
ALTER TABLE `{$dbBillingPeriods}` ADD `tag` VARCHAR( 10 ) NOT NULL AFTER `{$dbPriority}` ;
ALTER TABLE `{$dbBillingPeriods}` DROP PRIMARY KEY, ADD PRIMARY KEY ( `servicelevelid` , `{$dbPriority}` ) ;

-- KMH 4/12/07
ALTER TABLE `{$dbUserStatus}` DROP INDEX `id` ;

-- PH 9/12/07
ALTER TABLE `{$dbDashboard}` ADD `version` MEDIUMINT NOT NULL DEFAULT '1' AFTER `name` ;

-- INL 10/12/07
ALTER TABLE `{$dbContacts}` ADD INDEX ( `active` );
ALTER TABLE `{$dbSites}` ADD INDEX ( `active` );
ALTER TABLE `{$dbUpdates}` ADD INDEX ( `customervisibility` );
DELETE FROM `{$dbIncidentstatus}` WHERE id = 0 OR id = 10;
INSERT INTO `{$dbIncidentstatus}` VALUES (10, 'Active (Unassigned)', 'Active');
";

$upgrade_schema[332] = "
-- INL 12Jan08
ALTER TABLE `{$dbContacts}` CHANGE `salutation` `courtesytitle` VARCHAR( 50 ) NOT NULL COMMENT 'Was ''salutation'' before 3.32';
-- INL 13Jan08
UPDATE `{$dbIncidentstatus}` SET `name` = 'strActive' WHERE `id` =1 LIMIT 1 ;
UPDATE `{$dbIncidentstatus}` SET `name` = 'strClosed' WHERE `id` =2 LIMIT 1 ;
UPDATE `{$dbIncidentstatus}` SET `name` = 'strResearchNeeded' WHERE `id` =3 LIMIT 1 ;
UPDATE `{$dbIncidentstatus}` SET `name` = 'strCalledAndLeftMessage' WHERE `id` =4 LIMIT 1 ;
UPDATE `{$dbIncidentstatus}` SET `name` = 'strAwaitingColleagueResponse' WHERE `id` =5 LIMIT 1 ;
UPDATE `{$dbIncidentstatus}` SET `name` = 'strAwaitingSupportResponse' WHERE `id` =6 LIMIT 1 ;
UPDATE `{$dbIncidentstatus}` SET `name` = 'strAwaitingClosure' WHERE `id` =7 LIMIT 1 ;
UPDATE `{$dbIncidentstatus}` SET `name` = 'strAwaitingCustomerAction' WHERE `id` =8 LIMIT 1 ;
UPDATE `{$dbIncidentstatus}` SET `name` = 'strUnsupported' WHERE `id` =9 LIMIT 1 ;
-- INL 24Jan08
ALTER TABLE `{$dbUsers}` ADD `var_utc_offset` INT NOT NULL DEFAULT '0' COMMENT 'Offset from UTC (timezone) in minutes' AFTER `var_i18n` ;
INSERT INTO `{$dbUserStatus}` (`id` ,`name`) VALUES ('0', 'Account Disabled');
";

$upgrade_schema[335]['t200805191400'] = "
-- KMH 17/12/07
CREATE TABLE IF NOT EXISTS `{$dbTriggers}` (
  `id` int(11) NOT NULL auto_increment,
  `triggerid` varchar(50) NOT NULL,
  `userid` tinyint(4) NOT NULL,
  `action` enum('ACTION_NONE','ACTION_EMAIL','ACTION_NOTICE','ACTION_JOURNAL') NOT NULL default 'ACTION_NONE',
  `template` varchar(50) NOT NULL,
  `parameters` varchar(255) default NULL,
  `checks` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `triggerid` (`triggerid`)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS `{$CONFIG['db_tableprefix']}contactflags`;
DROP TABLE IF EXISTS `{$CONFIG['db_tableprefix']}contactproducts`;

-- KMH 18/12/07
CREATE TABLE `{$dbNoticeTemplates}` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 255 ) NOT NULL ,
`type` TINYINT( 4 ) NOT NULL ,
`description` VARCHAR( 255 ) NOT NULL ,
`text` TINYTEXT NOT NULL ,
`linktext` VARCHAR( 50 ) NULL ,
`link` VARCHAR( 100 ) NULL ,
`durability` ENUM( 'sticky', 'session' ) NOT NULL DEFAULT 'sticky',
 INDEX ( `userid` ),
) ENGINE = MYISAM ;

INSERT INTO `{$dbNoticeTemplates}` (`name`, `type`, `description`, `text`, `linktext`, `link`, `durability`) VALUES('TRIGGER_INCIDENT_CREATED', 0, 'Used when a new incident has been created', 'Incident {incidentid} - {incidenttitle} has been logged', 'View Incident', 'javascript:incident_details_window({incidentid})', 'sticky');
INSERT INTO `{$dbNoticeTemplates}` (`name`, `type`, `description`, `text`, `linktext`, `link`, `durability`) VALUES('TRIGGER_INCIDENT_ASSIGNED_TRIGGER', 0, 'Used when a new incident is assigned to you', 'Incident {incidentid} - {incidenttitle} has been assigned to you', 'View Incident', 'javascript:incident_details_window({incidentid})', 'sticky');
INSERT INTO `{$dbNoticeTemplates}` (`name`, `type`, `description`, `text`, `linktext`, `link`, `durability`) VALUES('TRIGGER_INCIDENT_NEARING_SLA_TRIGGER', 0, 'Used when one of your incidents nears an SLA', 'Incident {incidentid} - {incidenttitle} is nearing its SLA', 'View Incident', 'javascript:incident_details_window({incidentid})', 'sticky');
INSERT INTO `{$dbNoticeTemplates}` (`name`, `type`, `description`, `text`, `linktext`, `link`, `durability`) VALUES('TRIGGER_USERS_INCIDENT_NEARING_SLA_TRIGGER', 0, 'Used when a user\'s incident you are watching is assigned to you', '{incidentowner}\'s incident {incidentid} - {incidenttitle} is nearing its SLA', 'View Incident', 'javascript:incident_details_window({incidentid})', 'sticky');
INSERT INTO `{$dbNoticeTemplates}` (`name`, `type`, `description`, `text`, `linktext`, `link`, `durability`) VALUES('TRIGGER_INCIDENT_EXCEEDED_SLA_TRIGGER', 0, 'Used when one of your incidents exceeds an SLA', 'Incident {incidentid} - {incidenttitle} has exceeded its SLA', 'View Incident', 'javascript:incident_details_window({incidentid})', 'sticky');
INSERT INTO `{$dbNoticeTemplates}` (`name`, `type`, `description`, `text`, `linktext`, `link`, `durability`) VALUES('TRIGGER_INCIDENT_REVIEW_DUE', 0, 'Used when an incident is due a review', 'Incident {incidentid} - {incidenttitle} is due for review', 'View Incident', 'javascript:incident_details_window({incidentid})', 'sticky');
INSERT INTO `{$dbNoticeTemplates}` (`name`, `type`, `description`, `text`, `linktext`, `link`, `durability`) VALUES('TRIGGER_KB_CREATED_TRIGGER', 0, 'Used when a new Knowledgebase article is created', 'KB Article {KBname} has been created', NULL, NULL, 'sticky');
INSERT INTO `{$dbNoticeTemplates}` (`name`, `type`, `description`, `text`, `linktext`, `link`, `durability`) VALUES('TRIGGER_NEW_HELD_EMAIL', 0, 'Used when there is a new email in the holding queue', 'There is a new email in the holding queue', 'View Holding Queue', '{sitpath}/review_incoming_updates.php', 'sticky');
INSERT INTO `{$dbNoticeTemplates}` (`name`, `type`, `description`, `text`, `linktext`, `link`, `durability`) VALUES('TRIGGER_MINS_HELD_EMAIL', 0, 'Used when there is a new email in the holding queue for x minutes', 'There has been an email in the holding queue for {holdingmins} minutes', 'View Holding Queue', '{sitpath}/review_incoming_updates.php', 'sticky');
INSERT INTO `{$dbNoticeTemplates}` (`name`, `type`, `description`, `text`, `linktext`, `link`, `durability`) VALUES('TRIGGER_SIT_UPGRADED', 0, 'Used when the system is upgraded', 'SiT! has been upgraded to {sitversion}', 'What\'s New?', '{sitpath}/releasenotes.php', 'sticky');
INSERT INTO `{$dbNoticeTemplates}` (`name`, `type`, `description`, `text`, `linktext`, `link`, `durability`) VALUES('TRIGGER_INCIDENT_OWNED_CLOSED_BY_USER', 0, 'Used when one of your incidents is closed by another engineer', 'Your incident {incidentid} - {incidenttitle} has been closed by {engineerclosedname}', NULL, NULL, 'sticky');
INSERT INTO `{$dbNoticeTemplates}` (`name`, `type`, `description`, `text`, `linktext`, `link`, `durability`) VALUES('TRIGGER_USER_SET_TO_AWAY', 0, 'Used when a watched user goes away', '{realname} is now [b]not accepting[/b] incidents', NULL, 'userid=1', 'sticky');
INSERT INTO `{$dbNoticeTemplates}` (`name`, `type`, `description`, `text`, `linktext`, `link`, `durability`) VALUES('TRIGGER_USER_RETURNS', 0, 'Used when a user sets back to accepting', '{realname} is now [b]accepting[/b] incidents', NULL, NULL, 'sticky');

-- KMH 06/01/08
ALTER TABLE `{$CONFIG['db_tableprefix']}emailtype` ADD `triggerid` INT( 11 ) NULL ;
INSERT INTO `{$CONFIG['db_tableprefix']}emailtype` (`id` ,`type` ,`description` ,`tofield` ,`fromfield` ,`replytofield` ,`ccfield` ,`bccfield` ,`subjectfield` ,`body` ,`customervisibility` ,
`storeinlog` ,`triggerid`)VALUES ('TRIGGER_INCIDENT_LOGGED', 'system', 'Trigger email sent when a new incident is logged.', '<useremail>', '<supportemail>', NULL , NULL , NULL , '[<incidentid>] - <incidenttitle>', 'Hello <contactfirstname>,\r\n\r\nIncident <incidentid> - <incidenttitle> has been logged.\r\n\r\n<signature> <globalsignature>\r\n-------------\r\nThis email is sent as a result of a system trigger. If you do not want to receive these emails, you can disable them from the ''Triggers'' page.', 'hide', 'No', '1');

-- KMH 09/01/08
INSERT INTO `{$CONFIG['db_tableprefix']}emailtype` (`id`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`) VALUES
('TRIGGER_INCIDENT_CREATED', 'system', 'Trigger email sent when a new incident is logged.', '<useremail>', '<supportemail>', NULL, NULL, NULL, '[<incidentid>] - <incidenttitle>', 'Hello <contactfirstname>,\r\n\r\nIncident <incidentid> - <incidenttitle> has been logged.\r\n\r\n<signature> <globalsignature>\r\n-------------\r\nThis email is sent as a result of a system trigger. If you do not want to receive these emails, you can disable them from the ''Triggers'' page.', 'hide', 'No'),
('TRIGGER_INCIDENT_NEARING_SLA', 'system', 'Trigger email sent when an incident is nearing its SLA.', '<useremail>', '<supportemail>', NULL, NULL, NULL, '[<incidentid>] - <incidenttitle>: SLA approaching', 'Hello <contactfirstname>,\r\n\r\nIncident <incidentid> - <incidenttitle> is approaching its SLA.\r\n\r\n<signature> <globalsignature>\r\n-------------\r\nThis email is sent as a result of a system trigger. If you do not want to receive these emails, you can disable them from the ''Triggers'' page.', 'hide', 'No'),
('TRIGGER_INCIDENT_ASSIGNED', 'user', 'Notify user when call assigned to them', '<useremail>', '<supportemail>', NULL, NULL, NULL, '[<incidentid>] - <incidenttitle>: has been assigned to you', 'Hello <contactfirstname>,\r\n\r\nIncident <incidentid> - <incidenttitle> has been assigned to you.\r\n\r\n<signature> <globalsignature>\r\n-------------\r\nThis email is sent as a result of a system trigger. If you do not want to receive these emails, you can disable them from the ''Triggers'' page.', 'show', 'Yes');

INSERT INTO `{$dbNoticeTemplates}` (`id`, `type`, `description`, `text`, `linktext`, `link`, `durability`) VALUES
('INCIDENT_OWNED_CLOSED_BY_USER', 0, '', 'Your incident <incidentid> - <incidenttitle> has been closed by <engineerclosedname>', NULL, NULL, 'sticky');

-- KMH 17/01/08
ALTER TABLE `{$dbNotices}` CHANGE `gid` `template` VARCHAR( 255 ) NULL DEFAULT NULL;
-- INL 22/01/08
ALTER TABLE `{$dbTasks}` CHANGE `distribution` `distribution` ENUM( 'public', 'private', 'incident', 'event' );
-- KMH 23/01/08
ALTER TABLE `{$dbTriggers}` ADD `checks` VARCHAR( 255 ) NULL ;
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_INCIDENT_CREATED', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_INCIDENT_ASSIGNED', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_INCIDENT_ASSIGNED_WHILE_AWAY', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_INCIDENT_ASSIGNED_WHILE_OFFLINE', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_INCIDENT_NEARING_SLA', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_USERS_INCIDENT_NEARING_SLA', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_INCIDENT_EXCEEDED_SLA', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_INCIDENT_REVIEW_DUE', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_CRITICAL_INCIDENT_CREATED', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_KB_CREATED', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_NEW_HELD_EMAIL', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_WAITING_HELD_EMAIL', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_USER_SET_TO_AWAY', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_SIT_UPGRADED', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_USER_RETURNS', '0', 'ACTION_JOURNAL');
INSERT INTO `{$dbTriggers}` (triggerid, userid, action) VALUES ('TRIGGER_INCIDENT_OWNED_CLOSED_BY_USER', '0', 'ACTION_JOURNAL');

-- KMHO 25/01/08
ALTER TABLE `{$CONFIG['db_tableprefix']}emailtype` CHANGE `type` `type` ENUM( 'usertemplate', 'system', 'contact', 'site', 'incident', 'kb', 'user') NOT NULL COMMENT 'usertemplate is personal template owned by a user, user is a template relating to a user' DEFAULT 'user';

-- INL 25Jan08
UPDATE `{$CONFIG['db_tableprefix']}emailtype` SET `type` = 'incident' WHERE `id` = 'INCIDENT_CLOSURE' ;
UPDATE `{$CONFIG['db_tableprefix']}emailtype` SET `type` = 'incident' WHERE `id` = 'INCIDENT_LOGGED_CALL' ;
UPDATE `{$CONFIG['db_tableprefix']}emailtype` SET `type` = 'incident' WHERE `id` = 'INCIDENT_CLOSED' ;
UPDATE `{$CONFIG['db_tableprefix']}emailtype` SET `type` = 'incident' WHERE `id` = 'OUT_OF_SLA' ;
UPDATE `{$CONFIG['db_tableprefix']}emailtype` SET `type` = 'incident' WHERE `id` = 'OUT_OF_REVIEW' ;
UPDATE `{$CONFIG['db_tableprefix']}emailtype` SET `type` = 'incident' WHERE `id` = 'INCIDENT UPDATED' ;
UPDATE `{$CONFIG['db_tableprefix']}emailtype` SET `type` = 'incident' WHERE `id` = 'INCIDENT CLOSED EXTERNAL' ;
UPDATE `{$CONFIG['db_tableprefix']}emailtype` SET `type` = 'incident' WHERE `id` = 'INCIDENT_LOGGED_EMAIL' ;

-- KMH 27/01/08
ALTER TABLE `{$dbTriggers}` ADD `checks` VARCHAR( 255 ) NULL ;

-- INL 28/01/08
ALTER TABLE `{$dbTriggers}` ADD `template` INT( 11 ) NOT NULL AFTER `action` ;

-- INL 29/01/08
ALTER TABLE `{$dbContacts}` ADD `created` DATETIME NULL ,
ADD `createdby` INT NULL ,
ADD `modified` DATETIME NULL ,
ADD `modifiedby` INT NULL ;

ALTER TABLE `{$dbSites}` ADD `created` DATETIME NULL ,
ADD `createdby` INT NULL ,
ADD `modified` DATETIME NULL ,
ADD `modifiedby` INT NULL ;

-- INL 14Feb08
CREATE TABLE IF NOT EXISTS `{$dbScheduler}` (
  `id` int(11) NOT NULL auto_increment,
  `action` varchar(50) NOT NULL,
  `params` varchar(255) NOT NULL,
  `paramslabel` varchar(255) default NULL,
  `description` tinytext NOT NULL,
  `status` enum('enabled','disabled') NOT NULL default 'enabled',
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  `interval` int(11) NOT NULL,
  `lastran` datetime NOT NULL,
  `success` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `job` (`action`)
) ENGINE=MyISAM  ;

INSERT INTO `{$dbScheduler}` (`id`, `action`, `params`, `paramslabel`, `description`, `status`, `start`, `end`, `interval`, `lastran`, `success`) VALUES (1, 'CloseIncidents', '554400', 'closure_delay', 'Close incidents that have been marked for closure for longer than the <var>closure_delay</var> parameter (which is in seconds)', 'enabled', '2008-01-01 00:00:00', '0000-00-00 00:00:00', 60, '0000-00-00 00:00:00', 1);
INSERT INTO `{$dbScheduler}` (`id`, `action`, `params`, `paramslabel`, `description`, `status`, `start`, `end`, `interval`, `lastran`, `success`) VALUES (2, 'SetUserStatus', '', NULL, '(EXPERIMENTAL) This will set users status                         based on data from their holiday calendar.                        e.g. Out of Office/Away sick.', 'enabled', '2008-01-01 00:00:00', '0000-00-00 00:00:00', 60, '0000-00-00 00:00:00', 1);
INSERT INTO `{$dbScheduler}` (`id`, `action`, `params`, `paramslabel`, `description`, `status`, `start`, `end`, `interval`, `lastran`, `success`) VALUES (3, 'PurgeJournal', '', NULL, 'Delete old journal entries according to the config setting <var>\$CONFIG[''journal_purge_after'']</var>', 'enabled', '2008-01-01 00:00:00', '0000-00-00 00:00:00', 300, '0000-00-00 00:00:00', 1);
INSERT INTO `{$dbScheduler}` (`id`, `action`, `params`, `paramslabel`, `description`, `status`, `start`, `end`, `interval`, `lastran`, `success`) VALUES (4, 'TimeCalc', '', NULL, 'Calculate SLA Target Times and trigger                        OUT_OF_SLA and OUT_OF_REVIEW system email templates where appropriate.', 'enabled', '2008-01-01 00:00:00', '0000-00-00 00:00:00', 60, '0000-00-00 00:00:00', 1);
INSERT INTO `{$dbScheduler}` (`id`, `action`, `params`, `paramslabel`, `description`, `status`, `start`, `end`, `interval`, `lastran`, `success`) VALUES (5, 'ChaseCustomers', '', NULL, 'Chase customers', 'disabled', '2008-01-01 00:00:00', '0000-00-00 00:00:00', 3600, '0000-00-00 00:00:00', 1);
INSERT INTO `{$dbScheduler}` (`id`, `action`, `params`, `paramslabel`, `description`, `status`, `start`, `end`, `interval`, `lastran`, `success`) VALUES (6, 'CheckWaitingEmail', '', NULL, 'Checks the holding queue for emails and fires the TRIGGER_WAITING_HELD_EMAIL trigger when it finds some.', 'enabled', '2008-01-01 00:00:00', '0000-00-00 00:00:00', 60, '0000-00-00 00:00:00', 1);


-- KMH 27/03/08 !!WARNING!! can take a while on large tables
ALTER TABLE `{$dbUpdates}` ADD FULLTEXT ( `bodytext`) ;
ALTER TABLE `{$dbIncidents}` ADD FULLTEXT (`title`) ;

-- KMH 31/03/08
UPDATE `{$dbIncidentStatus}` SET `name` = 'strActiveUnassigned' WHERE `id` =10 LIMIT 1 ;

UPDATE `{$dbIncidentStatus}` SET `ext_name` = 'strActive' WHERE `id` =1 LIMIT 1 ;
UPDATE `{$dbIncidentStatus}` SET `ext_name` = 'strClosed' WHERE `id` =2 LIMIT 1 ;
UPDATE `{$dbIncidentStatus}` SET `ext_name` = 'strResearching' WHERE `id` =3 LIMIT 1 ;
UPDATE `{$dbIncidentStatus}` SET `ext_name` = 'strCalledAndLeftMessage' WHERE `id` =4 LIMIT 1 ;
UPDATE `{$dbIncidentStatus}` SET `ext_name` = 'strInternalEscalation' WHERE `id` =5 LIMIT 1 ;
UPDATE `{$dbIncidentStatus}` SET `ext_name` = 'strExternalEscalation' WHERE `id` =6 LIMIT 1 ;
UPDATE `{$dbIncidentStatus}` SET `ext_name` = 'strAwaitingClosure' WHERE `id` =7 LIMIT 1 ;
UPDATE `{$dbIncidentStatus}` SET `ext_name` = 'strYouHaveAction' WHERE `id` =8 LIMIT 1 ;
UPDATE `{$dbIncidentStatus}` SET `ext_name` = 'strUnsupported' WHERE `id` =9 LIMIT 1 ;
UPDATE `{$dbIncidentStatus}` SET `ext_name` = 'strActive' WHERE `id` =10 LIMIT 1 ;

-- KMH 03/04/08
UPDATE `{$dbInterfacestyles}` SET `cssurl` = 'sit1.css' WHERE `id` = 1 LIMIT 1;
UPDATE `{$dbInterfacestyles}` SET `cssurl` = 'sit2.css' WHERE `id` = 2 LIMIT 1;
UPDATE `{$dbInterfacestyles}` SET `cssurl` = 'sit3.css' WHERE `id` = 3 LIMIT 1;
UPDATE `{$dbInterfacestyles}` SET `cssurl` = 'sit4.css' WHERE `id` = 4 LIMIT 1;
UPDATE `{$dbInterfacestyles}` SET `cssurl` = 'sit5.css' WHERE `id` = 5 LIMIT 1;
UPDATE `{$dbInterfacestyles}` SET `cssurl` = 'sit_ph2.css' WHERE `id` = 6 LIMIT 1;
UPDATE `{$dbInterfacestyles}` SET `cssurl` = 'sit7.css' WHERE `id` = 7 LIMIT 1;
UPDATE `{$dbInterfacestyles}` SET `cssurl` = 'sit8.css' WHERE `id` = 8 LIMIT 1;
UPDATE `{$dbInterfacestyles}` SET `cssurl` = 'sit9.css' WHERE `id` = 9 LIMIT 1;
UPDATE `{$dbInterfacestyles}` SET `cssurl` = 'sit_ph.css' WHERE `id` = 10 LIMIT 1;
UPDATE `{$dbInterfacestyles}` SET `cssurl` = 'sit10.css' WHERE `id` = 11 LIMIT 1;
UPDATE `{$dbInterfacestyles}` SET `cssurl` = 'sit11.css' WHERE `id` = 12 LIMIT 1;
UPDATE `{$dbInterfacestyles}` SET `cssurl` = 'sit12.css' WHERE `id` = 13 LIMIT 1;
UPDATE `{$dbInterfacestyles}` SET `cssurl` = 'sit13.css' WHERE `id` = 14 LIMIT 1;
UPDATE `{$dbInterfacestyles}` SET `cssurl` = 'sit14.css' WHERE `id` = 15 LIMIT 1;
UPDATE `{$dbInterfacestyles}` SET `iconset` = 'oxygen' WHERE `id` =8 LIMIT 1 ;

ALTER TABLE `{$dbMaintenance}`
ADD `var_incident_visible_contacts` ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'no',
ADD `var_incident_visible_all` ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'no';

-- KMH 08/04/08
ALTER TABLE `{$dbKBArticles}` CHANGE `distribution` `distribution` ENUM( 'public', 'private', 'restricted' ) NOT NULL DEFAULT 'public' COMMENT 'public appears in the portal, private is info never to be released to the public, restricted is info that is sensitive but could be mentioned if asked, for example' ;
UPDATE `{$dbKBArticles}` SET `distribution`='public' ;

-- KMH 12/04/08
 ALTER TABLE `{$dbKBContent}` ADD FULLTEXT (`content`) ;
 ALTER TABLE `{$dbContacts}` ADD FULLTEXT (`forenames`, `surname`);
 ALTER TABLE `{$dbSites}` ADD FULLTEXT (`name`) ;

-- KMH 17/04/08
UPDATE `{$dbPermissions}` SET `name` = 'View your tasks' WHERE `id` =69 ;
INSERT INTO `{$dbPermissions}` VALUES (70, 'Create/Edit your Tasks');
INSERT INTO `{$dbPermissions}` VALUES (71, 'Manage your Triggers');
INSERT INTO `{$dbPermissions}` VALUES (72, 'Manage System Triggers');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 70, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 71, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 72, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 70, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (2, 71, 'true');

-- INL 17/04/08 FIXME Need to check triggers schema for upgrades before 3.35 release

-- PH 20/04/08 Permissions for billing (for custardpie branch)
INSERT INTO `{$dbPermissions}` VALUES (73, 'Approve Billable Incidents');
INSERT INTO `{$dbPermissions}` VALUES (74, 'Set duration without timed task (for billable incidents)');
INSERT INTO `{$dbPermissions}` VALUES (75, 'Set negative time for duration on incidents (for billable incidents - refunds)');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 73, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 74, 'true');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 75, 'true');

-- INL 22/04/08 More permissions for billing (custardpie)
INSERT INTO `{$dbPermissions}` VALUES (76, 'View Transactions');
INSERT INTO `{$dbRolePermissions}` (`roleid`, `permissionid`, `granted`) VALUES (1, 76, 'true');

-- INL 23Apr08 timestamps for all user data tables
ALTER TABLE `{$dbBillingPeriods}` ADD `created` DATETIME NULL ,
ADD `createdby` INT NULL ,
ADD `modified` DATETIME NULL ,
ADD `modifiedby` INT NULL ;

ALTER TABLE `{$dbEmailSig}` ADD `created` DATETIME NULL ,
ADD `createdby` INT NULL ,
ADD `modified` DATETIME NULL ,
ADD `modifiedby` INT NULL ;

ALTER TABLE `{$dbEscalationPaths}` ADD `created` DATETIME NULL ,
ADD `createdby` INT NULL ,
ADD `modified` DATETIME NULL ,
ADD `modifiedby` INT NULL ;

ALTER TABLE `{$dbFeedbackForms}` ADD `created` DATETIME NULL ,
ADD `createdby` INT NULL ,
ADD `modified` DATETIME NULL ,
ADD `modifiedby` INT NULL ;

ALTER TABLE `{$dbFeedbackQuestions}` ADD `created` DATETIME NULL ,
ADD `createdby` INT NULL ,
ADD `modified` DATETIME NULL ,
ADD `modifiedby` INT NULL ;

ALTER TABLE `{$dbFeedbackResults}` ADD `created` DATETIME NULL ,
ADD `createdby` INT NULL ,
ADD `modified` DATETIME NULL ,
ADD `modifiedby` INT NULL ;

ALTER TABLE `{$dbFiles}` ADD `created` DATETIME NULL ,
ADD `createdby` INT NULL ,
ADD `modified` DATETIME NULL ,
ADD `modifiedby` INT NULL ;

ALTER TABLE `{$dbGroups}` ADD `created` DATETIME NULL ,
ADD `createdby` INT NULL ,
ADD `modified` DATETIME NULL ,
ADD `modifiedby` INT NULL ;

ALTER TABLE `{$dbIncidentProductInfo}` ADD `created` DATETIME NULL ,
ADD `createdby` INT NULL ,
ADD `modified` DATETIME NULL ,
ADD `modifiedby` INT NULL ;

ALTER TABLE `{$dbIncidents}` ADD `created` DATETIME NULL ,
ADD `createdby` INT NULL ,
ADD `modified` DATETIME NULL ,
ADD `modifiedby` INT NULL ;

DROP TABLE IF EXISTS `{$CONFIG['db_tableprefix']}flags`;

-- PH  04/05/08
INSERT INTO `{$dbScheduler}` (`id`, `action`, `params`, `paramslabel`, `description`, `status`, `start`, `end`, `interval`, `lastran`, `success`) VALUES (7, 'PurgeExpiredFTPItems', '', NULL, 'purges files which have expired from the FTP site when run.', 'enabled', '2008-01-01 00:00:00', '0000-00-00 00:00:00', 216000, '0000-00-00 00:00:00', 1);

-- KMH 06/05/08
ALTER TABLE `{$dbMaintenance}` CHANGE `allcontactssupported` `allcontactssupported` ENUM( 'no', 'yes' ) NOT NULL DEFAULT 'no' ;

-- KHM 10/05/08
ALTER TABLE `{$dbUsers}` DROP `var_collapse`, DROP `var_notify_on_reassign`;
ALTER TABLE `{$dbMaintenance}` CHANGE `licence_type` `licence_type` INT( 11 ) NULL DEFAULT NULL ;

-- KMH 13/05/08
INSERT INTO `{$dbLinkTypes}` (`id` ,`name` ,`lrname` ,`rlname` ,`origtab` ,`origcol` ,`linktab` ,`linkcol` ,`selectionsql` ,`filtersql` ,`viewurl`)
VALUES('Attachments', 'Update', 'File', 'updates', 'id', 'files', 'id', 'filename', '', 'incident_details.php?updateid=%id%&tab=files');

-- KMH 14/05/08
ALTER TABLE `{$dbFiles}` CHANGE `filedate` `filedate` DATETIME NOT NULL ;
ALTER TABLE `{$dbFiles}` CHANGE `expiry` `expiry` DATETIME NOT NULL ;
ALTER TABLE `{$dbFiles}` CHANGE `longdescription` `longdescription` TEXT ;
ALTER TABLE `{$dbFiles}` ADD `usertype` ENUM( 'user', 'contact' ) NOT NULL DEFAULT 'user' AFTER `userid` ;

-- PH 18/05/08
UPDATE `{$dbLinkTypes}` SET `selectionsql` = 'CONCAT(forenames, \" \", surname)' WHERE `{$dbLinktypes}`.`id` = 2 LIMIT 1;
";

// Schema updates from this point should in the format $upgrade_schema[315]["t200805191404"]
// where 315 is the sit version and 200805191404 is the timestamp in the format YYYYMMDDHHMM (don't forget the the 't' prefix!)
$upgrade_schema[335]["t200805201618"] = "CREATE TABLE IF NOT EXISTS `emailtemplates` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `type` enum('usertemplate','system','contact','site','incident','kb','user') NOT NULL default 'user' COMMENT 'usertemplate is personal template owned by a user, user is a template relating to a user',
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
  `created` datetime default NULL,
  `createdby` int(11) default NULL,
  `modified` datetime default NULL,
  `modifiedby` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;";
$upgrade_schema[335]["t200805201619"] = "TRUNCATE `$dbEmailTemplates`;";
$upgrade_schema[335]["t200805201620"] = "INSERT INTO `$dbEmailTemplates` (`id`, `name`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`, `created`, `createdby`, `modified`, `modifiedby`) VALUES(1, 'Support Email', 'incident', 'Used by default when you send an email from an incident.', '{contactemail}', '{supportemail}', '{supportemail}', '', '{useremail}', '[{incidentid}] - {incidenttitle}', 'Hi {contactfirstname},\r\n\r\n{signature}\r\n{globalsignature}', 'show', 'Yes', NULL, NULL, NULL, NULL);";
$upgrade_schema[335]["t200805201621"] = "INSERT INTO `$dbEmailTemplates` (`id`, `name`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`, `created`, `createdby`, `modified`, `modifiedby`) VALUES(2, 'INCIDENT_CLOSURE', 'system', 'Notify contact that the incident has been marked for closure and will be closed shortly', '{contactemail}', '{supportemail}', '{supportemail}', '', '{useremail}', 'Closure Notification: [{incidentid}] - {incidenttitle}', '{contactfirstname},\r\n\r\nIncident {incidentid} has been marked for closure. If you still have outstanding issues relating to this incident then please reply with details, otherwise it will be closed in the next seven days.\r\n\r\n{signature}\r\n{globalsignature}', 'show', 'Yes', NULL, NULL, NULL, NULL);";
$upgrade_schema[335]["t200805201622"] = "INSERT INTO `$dbEmailTemplates` (`id`, `name`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`, `created`, `createdby`, `modified`, `modifiedby`) VALUES(3, 'INCIDENT_LOGGED_CONTACT', 'incident', 'Acknowledge the contact\'s contact and notify them of the new incident number', '{contactemail}', '{supportemail}', '{supportemail}', '', '{useremail}', '[{incidentid}] - {incidenttitle}', 'Thank you for your contact. The incident {incidentid} has been generated and your details stored in our tracking system. \r\n\r\nYou will be receiving a response from one of our product specialists as soon as possible. When referring to this incident please remember to quote incident {incidentid} in \r\nall communications. \r\n\r\nFor all email communications please title your email as [{incidentid}] - {incidenttitle}\r\n\r\n{globalsignature}\r\n', 'hide', 'No', NULL, NULL, NULL, NULL);";
$upgrade_schema[335]["t200805201623"] = "INSERT INTO `$dbEmailTemplates` (`id`, `name`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`, `created`, `createdby`, `modified`, `modifiedby`) VALUES(4, 'INCIDENT_CLOSED', 'system', 'Notify contact that an incident has now been closed', '{contactemail}', '{supportemail}', '{supportemail}', '', '', 'Incident Closed: [{incidentid}] - {incidenttitle} - Closed', 'This is an automated message to let you know that Incident {incidentid} has now been closed. \r\n\r\n{globalsignature}', 'show', 'Yes', NULL, NULL, NULL, NULL);";
$upgrade_schema[335]["t200805201624"] = "INSERT INTO `$dbEmailTemplates` (`id`, `name`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`, `created`, `createdby`, `modified`, `modifiedby`) VALUES(5, 'OUT_OF_SLA', 'system', '', '{supportmanager}', '{supportemail}', '{supportemail}', '{useremail}', '', '{applicationshortname} SLA: Incident {incidentid} now outside SLA', 'This is an automatic notification that this incident has gone outside its SLA.  The SLA target {info1} expired {info2} minutes ago.\n\nIncident: [{incidentid}] - {incidenttitle}\nOwner: {incidentowner}\nPriority: {incidentpriority}\nExternal Id: {incidentexternalid}\nExternal Engineer: {incidentexternalengineer}\nSite: {contactsite}\nContact: {contactname}\n\n--\n{applicationshortname} v{applicationversion}\n{todaysdate}\n', 'hide', 'Yes', NULL, NULL, NULL, NULL);";
$upgrade_schema[335]["t200805201625"] = "INSERT INTO `$dbEmailTemplates` (`id`, `name`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`, `created`, `createdby`, `modified`, `modifiedby`) VALUES(6, 'OUT_OF_REVIEW', 'system', '', '{supportmanager}', '{useremail}', '{supportemail}', '{supportemail}', '', '{applicationshortname} Review: Incident {incidentid} due for review soon', 'This is an automatic notification that this incident [{incidentid}] will soon be due for review.\n\nIncident: [{incidentid}] - {incidenttitle}\nEngineer: {incidentowner}\nPriority: {incidentpriority}\nExternal Id: {incidentexternalid}\nExternal Engineer: {incidentexternalengineer}\nSite: {contactsite}\nContact: {contactname}\n\n--\n{applicationshortname} v{applicationversion}\n{todaysdate}', 'hide', 'Yes', NULL, NULL, NULL, NULL);";
$upgrade_schema[335]["t200805201626"] = "INSERT INTO `$dbEmailTemplates` (`id`, `name`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`, `created`, `createdby`, `modified`, `modifiedby`) VALUES(7, 'INCIDENT_CLOSED_EXTERNAL', 'system', 'Notify external engineer that an incident has been closed', '{incidentexternalemail}', '{supportemail}', '{supportemail}', '', '', 'Incident ref #{incidentexternalid}  - {incidenttitle} CLOSED - [{incidentid}]', '{incidentexternalengineerfirstname},\r\n\r\nThis is an automated email to let you know that Incident {incidentexternalid} has been closed within our tracking system.\r\n\r\nMany thanks for your help.\r\n\r\n{signature}\r\n{globalsignature}', 'hide', 'No', NULL, NULL, NULL, NULL);";
$upgrade_schema[335]["t200805201627"] = "INSERT INTO `$dbEmailTemplates` (`id`, `name`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`, `created`, `createdby`, `modified`, `modifiedby`) VALUES(8, 'INCIDENT_LOGGED_USER', 'incident', 'Notify a user that an incident has been logged', '{useremail}', '{supportemail}', '{supportemail}', '', '', '[{incidentid}] - {incidenttitle}', 'Hi,\r\n\r\nIncident [{incidentid}] {incidenttitle} has been logged.\r\n\r\nThe details of this incident are:\r\n\r\nPriority: {incidentpriority}\r\nContact: {contactname}\r\nSite: {contactsite}\r\n\r\n\r\nRegards\r\n{applicationname}\r\n\r\n\r\n---\r\n{todaysdate} - {applicationshortname} {applicationversion}', 'hide', 'No', NULL, NULL, NULL, NULL);";
$upgrade_schema[335]["t200805201628"] = "INSERT INTO `$dbEmailTemplates` (`id`, `name`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`, `created`, `createdby`, `modified`, `modifiedby`) VALUES(9, 'INCIDENT_REASSIGNED_USER_NOTIFY', 'system', 'Notify user when call assigned to them', '{incidentreassignemailaddress}', '{supportemail}', '{supportemail}', '', '', 'A {incidentpriority} priority call ([{incidentid}] - {incidenttitle}) has been reassigned to you', 'Hi,\r\n\r\nIncident [{incidentid}] entitled {incidenttitle} has been reassigned to you.\r\n\r\nThe details of this incident are:\r\n\r\nPriority: {incidentpriority}\r\nContact: {contactname}\r\nSite: {contactsite}\r\n\r\n\r\nRegards\r\n{applicationname}\r\n\r\n\r\n---\r\n{todaysdate} - {applicationshortname} {applicationversion}', 'hide', 'No', NULL, NULL, NULL, NULL);";
$upgrade_schema[335]["t200805201629"] = "INSERT INTO `$dbEmailTemplates` (`id`, `name`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`, `created`, `createdby`, `modified`, `modifiedby`) VALUES(10, 'NEARING_SLA', 'system', 'Notification when an incident nears its SLA target', '{supportmanageremail}', '{supportemail}', '{supportemail}', '{useremail}', '', '{applicationshortname} SLA: Incident {incidentid} about to breach SLA', 'This is an automatic notification that this incident is about to breach its SLA.  The SLA target {info1} will expire in {info2} minutes.\r\n\r\nIncident: [{incidentid}] - {incidenttitle}\r\nOwner: {incidentowner}\r\nPriority: {incidentpriority}\r\nExternal Id: {incidentexternalid}\r\nExternal Engineer: {incidentexternalengineer}\r\nSite: {contactsite}\r\nContact: {contactname}\r\n\r\n--\r\n{applicationshortname} v{applicationversion}\r\n{todaysdate}\r\n', 'hide', 'Yes', NULL, NULL, NULL, NULL);";
$upgrade_schema[335]["t200805201630"] = "INSERT INTO `$dbEmailTemplates` (`id`, `name`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`, `created`, `createdby`, `modified`, `modifiedby`) VALUES(11, 'CONTACT_RESET_PASSWORD', 'system', 'Sent to a contact to reset their password.', '{contactemail}', '{supportemail}', '{supportemail}', '', '', '{applicationshortname} - password reset', 'Hi {contactfirstname},\r\n\r\nThis is a email to reset your contact portal password for {applicationname}. If you did not request this, please ignore this email.\r\n\r\nTo complete your password reset please visit the following url:\r\n\r\n{passwordreseturl}\r\n\r\n\r\n{globalsignature}', 'hide', 'No', NULL, NULL, NULL, NULL);";
$upgrade_schema[335]["t200805201631"] = "INSERT INTO `$dbEmailTemplates` (`id`, `name`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`, `created`, `createdby`, `modified`, `modifiedby`) VALUES(12, 'USER_RESET_PASSWORD', 'system', 'Sent when a user resets their email', '{useremail}', '{supportemail}', '{supportemail}', '', '', '{applicationshortname} - password reset', 'Hi,\r\n\r\nThis is a email to reset your user account password for {applicationname}. If you did not request this, please ignore this email.\r\n\r\nTo complete your password reset please visit the following url:\r\n\r\n{passwordreseturl}\r\n\r\n\r\n{globalsignature}', 'hide', 'No', NULL, NULL, NULL, NULL);";
$upgrade_schema[335]["t200805201632"] = "INSERT INTO `$dbEmailTemplates` (`id`, `name`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`, `created`, `createdby`, `modified`, `modifiedby`) VALUES(13, 'NEW_CONTACT_DETAILS', 'system', 'Sent when a new contact is created', '{contactemail}', '{supportemail}', '', '', '', '{applicationshortname} - portal details', 'Hello {contactfirstname},\r\nYou have just been added as a contact on {applicationname} ({applicationurl}).\r\n\r\nThese details allow you to the login to the portal, where you can create, update and close your incidents, as well as view your sites'' incidents.\r\n\r\nYour details are as follows:\r\n\r\nusername: {contactusername}\r\npassword: {prepassword}\r\nPlease note, this password cannot be recovered, only reset. You may change it in the portal.\r\n\r\n{globalsignature}', 'hide', 'No', NULL, NULL, NULL, NULL);";
$upgrade_schema[335]["t200805212220"] = "UPDATE `$dbNoticeTemplates` SET `type` = ".TRIGGER_NOTICE_TYPE.";";

// Important: When making changes to the schema you must add SQL to make the alterations
// to existing databases in $upgrade_schema[] *AND* you must also change $schema[] for
// new installations (above the line of stars).

?>