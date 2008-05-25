-- phpMyAdmin SQL Dump
-- version 2.11.3deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 25, 2008 at 09:11 AM
-- Server version: 5.0.51
-- PHP Version: 5.2.4-2ubuntu5.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `sit`
--

-- --------------------------------------------------------

--
-- Table structure for table `emailtemplates`
--

CREATE TABLE IF NOT EXISTS `emailtemplates` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `emailtemplates`
--

INSERT INTO `emailtemplates` (`id`, `name`, `type`, `description`, `tofield`, `fromfield`, `replytofield`, `ccfield`, `bccfield`, `subjectfield`, `body`, `customervisibility`, `storeinlog`, `created`, `createdby`, `modified`, `modifiedby`) VALUES
(1, 'Support Email', 'incident', 'Used by default when you send an email from an incident.', '{contactemail}', '{supportemail}', '{supportemail}', '', '{useremail}', '[{incidentid}] - {incidenttitle}', 'Hi {contactfirstname},\r\n\r\n{signature}\r\n{globalsignature}', 'show', 'Yes', NULL, NULL, NULL, NULL),
(2, 'INCIDENT_CLOSURE', 'system', 'Notify contact that the incident has been marked for closure and will be closed shortly', '{contactemail}', '{supportemail}', '{supportemail}', '', '{useremail}', 'Closure Notification: [{incidentid}] - {incidenttitle}', '{contactfirstname},\r\n\r\nIncident {incidentid} has been marked for closure. If you still have outstanding issues relating to this incident then please reply with details, otherwise it will be closed in the next seven days.\r\n\r\n{signature}\r\n{globalsignature}', 'show', 'Yes', NULL, NULL, NULL, NULL),
(3, 'INCIDENT_LOGGED_CONTACT', 'system', 'Acknowledge the contact''s contact and notify them of the new incident number', '{contactemail}', '{supportemail}', '{supportemail}', '', '{useremail}', '[{incidentid}] - {incidenttitle}', 'Thank you for your contact. The incident {incidentid} has been generated and your details stored in our tracking system. \r\n\r\nYou will be receiving a response from one of our product specialists as soon as possible. When referring to this incident please remember to quote incident {incidentid} in \r\nall communications. \r\n\r\nFor all email communications please title your email as [{incidentid}] - {incidenttitle}\r\n\r\n{globalsignature}\r\n', 'hide', 'No', NULL, NULL, NULL, NULL),
(4, 'INCIDENT_CLOSED', 'system', 'Notify contact that an incident has now been closed', '{contactemail}', '{supportemail}', '{supportemail}', '', '', 'Incident Closed: [{incidentid}] - {incidenttitle} - Closed', 'This is an automated message to let you know that Incident {incidentid} has now been closed. \r\n\r\n{globalsignature}', 'show', 'Yes', NULL, NULL, NULL, NULL),
(5, 'OUT_OF_SLA', 'system', '', '{supportmanager}', '{supportemail}', '{supportemail}', '{useremail}', '', '{applicationshortname} SLA: Incident {incidentid} now outside SLA', 'This is an automatic notification that this incident has gone outside its SLA.  The SLA target {info1} expired {info2} minutes ago.\n\nIncident: [{incidentid}] - {incidenttitle}\nOwner: {incidentowner}\nPriority: {incidentpriority}\nExternal Id: {incidentexternalid}\nExternal Engineer: {incidentexternalengineer}\nSite: {contactsite}\nContact: {contactname}\n\n--\n{applicationshortname} v{applicationversion}\n{todaysdate}\n', 'hide', 'Yes', NULL, NULL, NULL, NULL),
(6, 'OUT_OF_REVIEW', 'system', '', '{supportmanager}', '{useremail}', '{supportemail}', '{supportemail}', '', '{applicationshortname} Review: Incident {incidentid} due for review soon', 'This is an automatic notification that this incident [{incidentid}] will soon be due for review.\n\nIncident: [{incidentid}] - {incidenttitle}\nEngineer: {incidentowner}\nPriority: {incidentpriority}\nExternal Id: {incidentexternalid}\nExternal Engineer: {incidentexternalengineer}\nSite: {contactsite}\nContact: {contactname}\n\n--\n{applicationshortname} v{applicationversion}\n{todaysdate}', 'hide', 'Yes', NULL, NULL, NULL, NULL),
(7, 'INCIDENT_CLOSED_EXTERNAL', 'system', 'Notify external engineer that an incident has been closed', '{incidentexternalemail}', '{supportemail}', '{supportemail}', '', '', 'Incident ref #{incidentexternalid}  - {incidenttitle} CLOSED - [{incidentid}]', '{incidentexternalengineerfirstname},\r\n\r\nThis is an automated email to let you know that Incident {incidentexternalid} has been closed within our tracking system.\r\n\r\nMany thanks for your help.\r\n\r\n{signature}\r\n{globalsignature}', 'hide', 'No', NULL, NULL, NULL, NULL),
(8, 'INCIDENT_LOGGED_USER', 'system', 'Notify a user that an incident has been logged', '{useremail}', '{supportemail}', '{supportemail}', '', '', '[{incidentid}] - {incidenttitle}', 'Hi,\r\n\r\nIncident [{incidentid}] {incidenttitle} has been logged.\r\n\r\nThe details of this incident are:\r\n\r\nPriority: {incidentpriority}\r\nContact: {contactname}\r\nSite: {contactsite}\r\n\r\n\r\nRegards\r\n{applicationname}\r\n\r\n\r\n---\r\n{todaysdate} - {applicationshortname} {applicationversion}', 'hide', 'No', NULL, NULL, NULL, NULL),
(9, 'INCIDENT_REASSIGNED_USER_NOTIFY', 'system', 'Notify user when call assigned to them', '{useremail}', '{supportemail}', '{supportemail}', '', '', 'A {incidentpriority} priority call ([{incidentid}] - {incidenttitle}) has been reassigned to you', 'Hi,\r\n\r\nIncident [{incidentid}] entitled {incidenttitle} has been reassigned to you.\r\n\r\nThe details of this incident are:\r\n\r\nPriority: {incidentpriority}\r\nContact: {contactname}\r\nSite: {sitename}\r\n\r\n\r\nRegards\r\n{applicationname}\r\n\r\n\r\n---\r\n{todaysdate} - {applicationshortname} {applicationversion}', 'hide', 'No', NULL, NULL, NULL, NULL),
(10, 'NEARING_SLA', 'system', 'Notification when an incident nears its SLA target', '{supportmanageremail}', '{supportemail}', '{supportemail}', '{useremail}', '', '{applicationshortname} SLA: Incident {incidentid} about to breach SLA', 'This is an automatic notification that this incident is about to breach its SLA.  The SLA target {info1} will expire in {info2} minutes.\r\n\r\nIncident: [{incidentid}] - {incidenttitle}\r\nOwner: {incidentowner}\r\nPriority: {incidentpriority}\r\nExternal Id: {incidentexternalid}\r\nExternal Engineer: {incidentexternalengineer}\r\nSite: {contactsite}\r\nContact: {contactname}\r\n\r\n--\r\n{applicationshortname} v{applicationversion}\r\n{todaysdate}\r\n', 'hide', 'Yes', NULL, NULL, NULL, NULL),
(11, 'CONTACT_RESET_PASSWORD', 'system', 'Sent to a contact to reset their password.', '{contactemail}', '{supportemail}', '{supportemail}', '', '', '{applicationshortname} - password reset', 'Hi {contactfirstname},\r\n\r\nThis is a email to reset your contact portal password for {applicationname}. If you did not request this, please ignore this email.\r\n\r\nTo complete your password reset please visit the following url:\r\n\r\n{passwordreseturl}\r\n\r\n\r\n{globalsignature}', 'hide', 'No', NULL, NULL, NULL, NULL),
(12, 'USER_RESET_PASSWORD', 'system', 'Sent when a user resets their email', '{useremail}', '{supportemail}', '{supportemail}', '', '', '{applicationshortname} - password reset', 'Hi,\r\n\r\nThis is a email to reset your user account password for {applicationname}. If you did not request this, please ignore this email.\r\n\r\nTo complete your password reset please visit the following url:\r\n\r\n{passwordreseturl}\r\n\r\n\r\n{globalsignature}', 'hide', 'No', NULL, NULL, NULL, NULL),
(13, 'NEW_CONTACT_DETAILS', 'system', 'Sent when a new contact is created', '{contactemail}', '{supportemail}', '', '', '', '{applicationshortname} - portal details', 'Hello {contactfirstname},\r\nYou have just been added as a contact on {applicationname} ({applicationurl}).\r\n\r\nThese details allow you to the login to the portal, where you can create, update and close your incidents, as well as view your sites'' incidents.\r\n\r\nYour details are as follows:\r\n\r\nusername: {contactusername}\r\npassword: {prepassword}\r\nPlease note, this password cannot be recovered, only reset. You may change it in the portal.\r\n\r\n{globalsignature}', 'hide', 'No', NULL, NULL, NULL, NULL),
(14, 'EMAIL_REVIEW_DUE', 'system', 'Email sent when a review is due for an incident.', '{supportmanageremail}', '{supportemail}', '{supportemail}', NULL, NULL, '{applicationshortname}: review due', 'Hi,\r\n\r\nThe review for incident {incidentid} - {incidenttitle} is now due for review.\r\n\r\nYou can view the incident at {applicationurl}{applicationpath}incident_details.php?id={incidentid}\r\n\r\n{globalsignature}', 'hide', 'No', NULL, NULL, NULL, NULL);

