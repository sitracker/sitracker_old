<?php
// setup-schema.php - Defines database schema for use in setup.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas

$schema = "CREATE TABLE `closingstatus` (
 `id` int(11) NOT NULL auto_increment,
 `name` varchar(50) default NULL,
 PRIMARY KEY  (`id`)
) ;

INSERT INTO `closingstatus` VALUES (1, 'Sent Information');
INSERT INTO `closingstatus` VALUES (2, 'Solved Problem');
INSERT INTO `closingstatus` VALUES (3, 'Reported Bug');
INSERT INTO `closingstatus` VALUES (4, 'Action Taken');
INSERT INTO `closingstatus` VALUES (5, 'Duplicate');
INSERT INTO `closingstatus` VALUES (6, 'No Longer Relevant');
INSERT INTO `closingstatus` VALUES (7, 'Unsupported');
INSERT INTO `closingstatus` VALUES (8, 'Support Expired');
INSERT INTO `closingstatus` VALUES (9, 'Unsolved');


CREATE TABLE `contactflags` (
 `contactid` int(11) default NULL,
 `flag` char(3) NOT NULL default '',
  KEY `contactid` (`contactid`),
  KEY `flag` (`flag`)
);

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
);


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
  PRIMARY KEY  (`id`),
  KEY `siteid` (`siteid`),
  KEY `username` (`username`),
  KEY `forenames` (`forenames`),
  KEY `surname` (`surname`),
  KEY `notify_contactid` (`notify_contactid`)
) ;

INSERT INTO `contacts` VALUES (1,0,'Acme1','2830','John','Acme','Chairman','Mr',1,'acme@example.com',
'0666 222111','','','','','','','','','','No','No','No',1132930556,1132930556,'');

CREATE TABLE `emailsig` (
  `id` int(11) NOT NULL auto_increment,
  `signature` text NOT NULL,
  PRIMARY KEY  (`id`)
) COMMENT='Global Email Signature' ;


INSERT INTO `emailsig` VALUES (1, '');

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
  PRIMARY KEY  (`id`)
) ;

INSERT INTO `emailtype` VALUES (1,'Support Email','user','','<contactemail>','<supportemail>','<supportemail>','','<useremail>','[<incidentid>] - <incidenttitle>','<contactfirstname>,\r\n\r\n<signature>\r\n<globalsignature>', 'show');
INSERT INTO `emailtype` VALUES (2,'User Email','user','','<contactemail>','<useremail>','<useremail>','','','','<signature>\r\n<globalsignature>\r\n', 'show');
INSERT INTO `emailtype` VALUES (5,'INCIDENT_CLOSURE','system','Notify contact that the incident has been marked for closure and will be closed shortly','<contactemail>','<supportemail>','<supportemail>','','<useremail>','[<incidentid>] - <incidenttitle>','<contactfirstname>,\r\n\r\nIncident <incidentid> has been marked for closure. If you still have outstanding issues relating to this incident then please reply with details, otherwise it will be closed after the next seven days.\r\n\r\n<signature>\r\n<globalsignature>', 'show');
INSERT INTO `emailtype` VALUES (12,'INCIDENT_LOGGED_CALL','system','Acknowledge the contacts telephone call and notify them of the new incident number','<contactemail>','<supportemail>','<supportemail>','','<useremail>','[<incidentid>] - <incidenttitle>','Thank you for your call. The incident <incidentid> has been generated and your details stored in our tracking system. \r\n\r\nYou will be receiving a response from one of our product specialists as soon as possible. When referring to this incident please remember to quote incident <incidentid> in \r\nall communications. \r\n\r\nFor all email communications please title your email as [<incidentid>] - <incidenttitle>\r\n\r\n<globalsignature>\r\n', 'show');
INSERT INTO `emailtype` VALUES (13,'INCIDENT_CLOSED','system','Notify contact that an incident has now been closed','<contactemail>','<supportemail>','<supportemail>','','','[<incidentid>] - <incidenttitle> - Closed','This is an automated message to let you know that Incident <incidentid> has now been closed. \r\n\r\n<globalsignature>', 'show');
INSERT INTO `emailtype` VALUES (42, 'OUT_OF_SLA', 'system', '', '<supportmanager>', '<supportemail>', '<supportemail>', '<useremail>', '', '<applicationshortname> SLA: Incident <incidentid> now outside SLA', 'This is an automatic notification that this incident has gone outside it''s SLA.  The SLA target <info1> expired <info2> minutes ago.\n\nIncident: [<incidentid>] - <incidenttitle>\nOwner: <incidentowner>\nPriority: <incidentpriority>\nExternal Id: <incidentexternalid>\nExternal Engineer: <incidentexternalengineer>\nSite: <contactsite>\nContact: <contactname>\n\n--\n<applicationshortname> v<applicationversion>\n<todaysdate>\n', 'hide');
INSERT INTO `emailtype` VALUES (43, 'OUT_OF_REVIEW', 'system', '', '<supportmanager>', '<useremail>', '<supportemail>', '<supportemail>', '', '<applicationshortname> Review: Incident <incidentid> due for review soon', 'This is an automatic notification that this incident [<incidentid>] will soon be due for review.\n\nIncident: [<incidentid>] - <incidenttitle>\nEngineer: <incidentowner>\nPriority: <incidentpriority>\nExternal Id: <incidentexternalid>\nExternal Engineer: <incidentexternalengineer>\nSite: <contactsite>\nContact: <contactname>\n\n--\n<applicationshortname> v<applicationversion>\n<todaysdate>', 'hide');
INSERT INTO `emailtype` VALUES (48,'INCIDENT UPDATED','system','Aknoweldge contacts email and update to incident','<contactemail>','<supportemail>','<supportemail>','','','[<incidentid>] - <incidenttitle>','Thank you for your email. The incident <incidentid> has been updated and your details stored in our support database. \r\n\r\nYou will be receiving a response from one of our product specialists as soon as possible.\r\n\r\n<globalsignature>', 'show');
INSERT INTO `emailtype` VALUES (49,'INCIDENT CLOSED EXTERNAL','system','Notify external engineer that an incident has been closed','<incidentexternalemail>','<supportemail>','<supportemail>','','','Incident ref #<incidentexternalid>  - <incidenttitle> CLOSED - [<incidentid>]','<incidentexternalengineerfirstname>,\r\n\r\nThis is an automated email to let you know that Incident <incidentexternalid> has been closed within our tracking system.\r\n\r\nMany thanks for your help.\r\n\r\n<signature>\r\n<globalsignature>', 'hide');
INSERT INTO `emailtype` VALUES (9,'INCIDENT_LOGGED_EMAIL','system','Acknowledge the contacts email and notify them of the new incident number','<contactemail>','<supportemail>','<supportemail>','','<useremail>','[<incidentid>] - <incidenttitle>','Thank you for your email. The incident <incidentid> has been generated and your details stored in our tracking system. \r\n\r\nYou will be receiving a response from one of our product specialists as soon as possible. When referring to this incident please remember to quote incident <incidentid> in \r\nall communications.\r\n\r\nFor all email communications please title your email as [<incidentid>] - <incidenttitle>\r\n\r\n<globalsignature>', 'show');

CREATE TABLE `feedbackforms` (
  `id` int(5) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `introduction` text NOT NULL,
  `thanks` text NOT NULL,
  `description` text NOT NULL,
  `multi` enum('yes','no') NOT NULL default 'no',
  PRIMARY KEY  (`id`),
  KEY `multi` (`multi`)
);

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
) ;


CREATE TABLE `feedbackreport` (
  `id` int(5) NOT NULL default '0',
  `formid` int(5) NOT NULL default '0',
  `respondent` int(11) NOT NULL default '',
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
);

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
);

CREATE TABLE `feedbackresults` (
  `id` int(5) NOT NULL auto_increment,
  `respondentid` int(5) NOT NULL default '0',
  `questionid` int(5) NOT NULL default '0',
  `result` varchar(255) NOT NULL default '',
  `resulttext` text,
  PRIMARY KEY  (`id`),
  KEY `questionid` (`questionid`),
  KEY `respondentid` (`respondentid`)
) ;


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
) ;


CREATE TABLE `flags` (
  `flag` char(3) NOT NULL default '',
  `name` varchar(100) default NULL,
  PRIMARY KEY  (`flag`),
  KEY `flag` (`flag`)
);


CREATE TABLE `groups` (
  `id` int(5) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `imageurl` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) COMMENT='List of user groups' ;


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
) ;


CREATE TABLE `holidaytypes` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ;

INSERT INTO `holidaytypes` VALUES (1, 'Holiday');
INSERT INTO `holidaytypes` VALUES (2, 'Sickness');
INSERT INTO `holidaytypes` VALUES (3, 'Working Away');
INSERT INTO `holidaytypes` VALUES (4, 'Training');
INSERT INTO `holidaytypes` VALUES (5, 'Maternity/Paternity/Compassionate Leave');


CREATE TABLE `incidentpools` (
  `id` int(11) NOT NULL auto_increment,
  `maintenanceid` int(11) NOT NULL default '0',
  `siteid` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `incidentsremaining` int(5) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `maintenanceid` (`maintenanceid`)
) ;


CREATE TABLE `incidentproductinfo` (
  `id` int(11) NOT NULL auto_increment,
  `incidentid` int(11) default NULL,
  `productinfoid` int(11) default NULL,
  `information` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) ;


CREATE TABLE `incidents` (
  `id` int(11) NOT NULL auto_increment,
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
) ;

CREATE TABLE `incidentstatus` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `ext_name` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=10 ;

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
  `id` int(5) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `cssurl` varchar(255) NOT NULL default '',
  `headerhtml` text NOT NULL,
  PRIMARY KEY  (`id`)
) ;


INSERT INTO `interfacestyles` VALUES (1, 'Light Blue (Default)', 'webtrack1.css', '');
INSERT INTO `interfacestyles` VALUES (2, 'Grey', 'webtrack2.css', '');
INSERT INTO `interfacestyles` VALUES (3, 'Green', 'webtrack3.css', '');
INSERT INTO `interfacestyles` VALUES (4, 'Silver Blue', 'webtrack4.css', '');
INSERT INTO `interfacestyles` VALUES (5, 'Classic', 'webtrack5.css', '');
INSERT INTO `interfacestyles` VALUES (6, 'Orange', 'webtrack_ph2.css', '');
INSERT INTO `interfacestyles` VALUES (7, 'Yellow and Blue', 'webtrack7.css', '');
INSERT INTO `interfacestyles` VALUES (8, 'Neoteric', 'webtrack8.css', '');
INSERT INTO `interfacestyles` VALUES (9, 'Toms Linux Style', 'webtrack9.css', '');
INSERT INTO `interfacestyles` VALUES (10, 'Cool Blue', 'webtrack_ph.css', '');
INSERT INTO `interfacestyles` VALUES (11, 'Just Light', 'webtrack10.css', '');
INSERT INTO `interfacestyles` VALUES (12, 'Ex Pea', 'webtrack11.css', '');
INSERT INTO `interfacestyles` VALUES (13, 'GUI Colours', 'webtrack12.css', '');
INSERT INTO `interfacestyles` VALUES (14, 'Flashy', 'webtrack14/webtrack14.css', '');
INSERT INTO `interfacestyles` VALUES (15, 'Richard', 'webtrack15.css', '');

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
) ;


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
) COMMENT='Knowledge base articles' ;


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
) ;


CREATE TABLE `kbsoftware` (
  `docid` int(5) NOT NULL default '0',
  `softwareid` int(5) NOT NULL default '0',
  PRIMARY KEY  (`docid`,`softwareid`)
) COMMENT='Links kb articles with software';



CREATE TABLE `licencetypes` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ;


INSERT INTO `licencetypes` VALUES (1, 'Per User');
INSERT INTO `licencetypes` VALUES (2, 'Per Workstation');
INSERT INTO `licencetypes` VALUES (3, 'Per Server');
INSERT INTO `licencetypes` VALUES (4, 'Site');
INSERT INTO `licencetypes` VALUES (5, 'Evaluation');


CREATE TABLE `maintenance` (
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
  PRIMARY KEY  (`id`),
  KEY `site` (`site`),
  KEY `productonly` (`productonly`)
) ;

INSERT INTO `maintenance` VALUES (1,1,1,2,1268179200,1,4,0,0,'This is an example contract.',1,'no','no',0,0);

CREATE TABLE `permissions` (
  `id` int(5) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ;


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
INSERT INTO `permissions` VALUES (28, 'Add Quotation');
INSERT INTO `permissions` VALUES (29, 'Edit Quotation');
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
INSERT INTO `permissions` VALUES (48, 'View Sales Purchase Orders');
INSERT INTO `permissions` VALUES (49, 'Add/Edit Sales Purchase Orders');
INSERT INTO `permissions` VALUES (50, 'Approve Holidays');
INSERT INTO `permissions` VALUES (51, 'View InnerWeb');
INSERT INTO `permissions` VALUES (52, 'View Hidden Updates');
INSERT INTO `permissions` VALUES (53, 'Administrate Store');
INSERT INTO `permissions` VALUES (54, 'View KB Articles');
INSERT INTO `permissions` VALUES (55, 'Delete Sites/Contacts');
INSERT INTO `permissions` VALUES (56, 'Add Software');
INSERT INTO `permissions` VALUES (57, 'Disable User Accounts');
INSERT INTO `permissions` VALUES (58, 'Edit your Software Skills');
INSERT INTO `permissions` VALUES (59, 'Manage users software skills');
INSERT INTO `permissions` VALUES (60, 'Perform Searches');
INSERT INTO `permissions` VALUES (61, 'View Incident Details');
INSERT INTO `permissions` VALUES (62, 'View Incident Attachments');


CREATE TABLE `priority` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) COMMENT='Used in incidents.php' AUTO_INCREMENT=5 ;

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
) ;


CREATE TABLE `products` (
  `id` int(11) NOT NULL auto_increment,
  `vendorid` int(5) NOT NULL default '0',
  `name` varchar(50) default NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `vendorid` (`vendorid`),
  KEY `name` (`name`)
) COMMENT='Current List of Products' ;

INSERT INTO `products` VALUES (1,1,'Example Product','This is an example product.');

CREATE TABLE `relatedincidents` (
`id` INT( 5 ) NOT NULL AUTO_INCREMENT ,
`incidentid` INT( 5 ) NOT NULL ,
`relation` ENUM( 'child', 'sibling' ) DEFAULT 'child' NOT NULL ,
`relatedid` INT( 5 ) NOT NULL ,
PRIMARY KEY ( `id` ) ,
INDEX ( `incidentid` , `relatedid` )
) ;

CREATE TABLE `resellers` (
  `id` tinyint(4) NOT NULL auto_increment,
  `name` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ;

INSERT INTO `resellers` VALUES (1,'Us (No Reseller)');
INSERT INTO `resellers` VALUES (2,'Example Reseller');

CREATE TABLE `roles` (
`id` INT( 5 ) NOT NULL AUTO_INCREMENT ,
`rolename` VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( `id` )
) ;

INSERT INTO `roles` ( `id` , `rolename` ) VALUES ('1', 'Administrator');
INSERT INTO `roles` ( `id` , `rolename` ) VALUES ('2', 'Manager');
INSERT INTO `roles` ( `id` , `rolename` ) VALUES ('3', 'User');

CREATE TABLE `rolepermissions` (
`roleid` tinyint( 4 ) NOT NULL default '0',
`permissionid` int( 5 ) NOT NULL default '0',
`granted` enum( 'true', 'false' ) NOT NULL default 'false',
PRIMARY KEY ( `roleid` , `permissionid` )
);

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
INSERT INTO `rolepermissions` (`roleid`, `permissionid`, `granted`) VALUES (3, 29, 'true');
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
  PRIMARY KEY  (`tag`,`priority`),
  KEY `id` (`id`),
  KEY `review_days` (`review_days`));


INSERT INTO `servicelevels` VALUES (0, 'standard', 1, 320, 380, 960, 14.00, 28, 90);
INSERT INTO `servicelevels` VALUES (0, 'standard', 2, 240, 320, 960, 10.00, 20, 90);
INSERT INTO `servicelevels` VALUES (0, 'standard', 3, 120, 180, 480, 7.00, 14, 90);
INSERT INTO `servicelevels` VALUES (0, 'standard', 4, 60, 120, 240, 3.00, 6, 90);

CREATE TABLE `sitecontacts` (
  `siteid` int(11) NOT NULL default '0',
  `contactid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`siteid`,`contactid`)
);

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
  `notes` text NOT NULL,
  `typeid` int(5) NOT NULL default '1',
  `freesupport` int(5) NOT NULL default '0',
  `licenserx` int(5) NOT NULL default '0',
  `ftnpassword` varchar(40) NOT NULL default '',
  `owner` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `typeid` (`typeid`),
  KEY `owner` (`owner`)
) ;

INSERT INTO `sites` (`id`, `name`, `department`, `address1`, `address2`, `city`, `county`,
`country`, `postcode`, `telephone`, `fax`, `email`, `notes`, `typeid`, `freesupport`, `licenserx`,
`ftnpassword`, `owner`) VALUES (1, 'ACME Widgets Co.', 'Manufacturing Dept.', '21 Any Street', '',
'Anytown', 'Anyshire', 'UNITED KINGDOM', 'AN1 0TH', '0555 555555', '0444 444444', 'acme@example.com',
'Example site', 1, 0, 0, '', 0);

CREATE TABLE `sitetypes` (
  `typeid` int(5) NOT NULL auto_increment,
  `typename` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`typeid`)
) ;

INSERT INTO `sitetypes` VALUES (1, 'Unclassified');
INSERT INTO `sitetypes` VALUES (2, 'Commercial');
INSERT INTO `sitetypes` VALUES (3, 'Academic');

CREATE TABLE `software` (
  `id` int(5) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) COMMENT='Individual software products as they are supported' ;

INSERT INTO `software` VALUES (1,'Example Software');

CREATE TABLE `softwareproducts` (
  `productid` int(5) NOT NULL default '0',
  `softwareid` int(5) NOT NULL default '0',
  PRIMARY KEY  (`productid`,`softwareid`)
) COMMENT='Table to link products with software';

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
) COMMENT='Temporary table used during spellcheck' ;


CREATE TABLE `supportcontacts` (
  `id` int(11) NOT NULL auto_increment,
  `maintenanceid` int(11) default NULL,
  `contactid` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ;

INSERT INTO `supportcontacts` VALUES (1,1,1);


CREATE TABLE `system` (
  `id` int(1) NOT NULL default '0',
  `version` float(3,2) NOT NULL default '0.00',
  PRIMARY KEY  (`id`)
) ;


CREATE TABLE `tempassigns` (
  `incidentid` int(5) NOT NULL default '0',
  `originalowner` int(5) NOT NULL default '0',
  `userstatus` tinyint(4) NOT NULL default '1',
  `assigned` enum('yes','no') NOT NULL default 'no',
  PRIMARY KEY  (`incidentid`,`originalowner`),
  KEY `assigned` (`assigned`)
);


CREATE TABLE `tempincoming` (
  `id` int(11) NOT NULL auto_increment,
  `updateid` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `incidentid` int(11) NOT NULL default '0',
  `from` varchar(255) default NULL,
  `subject` varchar(255) default NULL,
  `emailfrom` varchar(255) default NULL,
  `locked` tinyint(4) default NULL,
  `reason` varchar(255) default NULL,
  `contactid` int(11) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `updateid` (`updateid`)
) COMMENT='Temporary store for incoming attachment paths' ;



CREATE TABLE `updates` (
  `id` int(11) NOT NULL auto_increment,
  `incidentid` int(11) default NULL,
  `userid` int(11) default NULL,
  `type` enum('default','editing','opening','email','reassigning','closing','reopening','auto','phonecallout','phonecallin','research','webupdate','emailout','emailin','externalinfo','probdef','solution','actionplan','slamet','reviewmet','tempassigning') default 'default',
  `currentowner` tinyint(4) NOT NULL default '0',
  `currentstatus` int(11) NOT NULL default '0',
  `bodytext` text,
  `timestamp` int(11) default NULL,
  `nextaction` varchar(50) NOT NULL default '',
  `customervisibility` enum('show','hide','unset') default 'unset',
  `timesincesla` int(5) default NULL,
  `timesincereview` int(5) default NULL,
  `reviewcalculated` enum('true','false') NOT NULL default 'false',
  `sla` enum('opened','initialresponse','probdef','actionplan','solution','closed') default NULL,
  `slacalculated` enum('false','true') NOT NULL default 'false',
  PRIMARY KEY  (`id`),
  KEY `currentowner` (`currentowner`,`currentstatus`),
  KEY `incidentid` (`incidentid`),
  KEY `timestamp` (`timestamp`),
  KEY `type` (`type`),
  KEY `timesincereview` (`timesincereview`,`reviewcalculated`)
) ;


CREATE TABLE `usergroups` (
  `userid` int(5) NOT NULL default '0',
  `groupid` int(5) NOT NULL default '0',
  PRIMARY KEY  (`userid`,`groupid`)
) COMMENT='Links users with groups';



CREATE TABLE `userpermissions` (
  `userid` tinyint(4) NOT NULL default '0',
  `permissionid` int(5) NOT NULL default '0',
  `granted` enum('true','false') NOT NULL default 'false',
  PRIMARY KEY  (`userid`,`permissionid`)
);

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

CREATE TABLE `users` (
  `id` tinyint(4) NOT NULL auto_increment,
  `username` varchar(50) default NULL,
  `password` varchar(50) default NULL,
  `realname` varchar(50) default NULL,
  `roleid` int(5) NOT NULL default '1',
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
  `var_style` int(11) default '1',
  `var_collapse` enum('true','false') NOT NULL default 'true',
  `var_hideautoupdates` enum('true','false') NOT NULL default 'false',
  `var_hideheader` enum('true','false') NOT NULL default 'false',
  `var_monitor` enum('true','false') NOT NULL default 'true',
  `listadmin` tinytext,
  `holiday_entitlement` float NOT NULL default '0',
  `qualifications` tinytext,
  PRIMARY KEY  (`id`),
  KEY `username` (`username`),
  KEY `accepting` (`accepting`),
  KEY `status` (`status`)
) ;

INSERT INTO `users` VALUES (1, 'admin', '5f4dcc3b5aa765d61d8327deb882cf99', 'Administrator', 1, 'Administrator', 'Regards,\r\n\r\nAdministrator', '', '', '', '', '', '', '', 1, '', 'Yes', 60, 'desc', 8, 'false', 'false', 'false', 'false', '', 32, '');


CREATE TABLE `usersoftware` (
  `userid` tinyint(4) NOT NULL default '0',
  `softwareid` int(5) NOT NULL default '0',
  `backupid` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`userid`,`softwareid`),
  KEY `backupid` (`backupid`)
) COMMENT='Defines which software users have expertise with';


CREATE TABLE `userstatus` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) ;


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
) ;

INSERT INTO `vendors` VALUES (1,'Default');

";

$upgrade_schema[321] = "CREATE TABLE `system`
  (`id` INT( 1 ) NOT NULL ,
  `version` FLOAT( 3, 2 ) DEFAULT '0.00' NOT NULL ,
  PRIMARY KEY ( `id` )) ;

CREATE TABLE `feedbackforms` (
  `id` int(5) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `introduction` text NOT NULL,
  `thanks` text NOT NULL,
  `description` text NOT NULL,
  `multi` enum('yes','no') NOT NULL default 'no',
  PRIMARY KEY  (`id`),
  KEY `multi` (`multi`)
);
ALTER TABLE `feedbackrespondents` CHANGE `respondent` `contactid` INT( 11 ) NOT NULL;
ALTER TABLE `feedbackrespondents` CHANGE `responseref` `incidentid` INT( 11 ) NOT NULL;
ALTER TABLE `feedbackreport` CHANGE `respondent` `respondent` INT( 11 ) NOT NULL;
ALTER TABLE `emailtype` ADD `customervisibility` ENUM( 'show', 'hide' ) DEFAULT 'show' NOT NULL ;
";

$upgrade_schema[322] = "CREATE TABLE `roles` (
`id` INT( 5 ) NOT NULL AUTO_INCREMENT ,
`rolename` VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( `id` )
) ;

INSERT INTO `roles` ( `id` , `rolename` ) VALUES ('1', 'Administrator');
INSERT INTO `roles` ( `id` , `rolename` ) VALUES ('2', 'Manager');
INSERT INTO `roles` ( `id` , `rolename` ) VALUES ('3', 'User');

CREATE TABLE `rolepermissions` (
`roleid` tinyint( 4 ) NOT NULL default '0',
`permissionid` int( 5 ) NOT NULL default '0',
`granted` enum( 'true', 'false' ) NOT NULL default 'false',
PRIMARY KEY ( `roleid` , `permissionid` )
);

ALTER TABLE `users` ADD `roleid` INT( 5 ) NOT NULL DEFAULT '1' AFTER `realname` ;
ALTER TABLE `users` DROP `accesslevel` ;
";

$upgrade_schema[323] = "CREATE TABLE `relatedincidents` (
`id` INT( 5 ) NOT NULL AUTO_INCREMENT ,
`incidentid` INT( 5 ) NOT NULL ,
`relation` ENUM( 'child', 'sibling' ) DEFAULT 'child' NOT NULL ,
`relatedid` INT( 5 ) NOT NULL ,
PRIMARY KEY ( `id` ) ,
INDEX ( `incidentid` , `relatedid` )
) ;

ALTER TABLE `sites` CHANGE `notes` `notes` TEXT NOT NULL ;
";

// Important: When making changes to the schema you must add SQL to make the alterations
// to existing databases in $upgrade_schema[] *AND* you must also change $schema[]

?>