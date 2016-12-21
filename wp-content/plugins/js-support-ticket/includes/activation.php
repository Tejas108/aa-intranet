<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class JSSTactivation {

    static function jssupportticket_activate() {
        // Install Database
        JSSTactivation::insertMenu();
        JSSTactivation::runSQL();
        JSSTactivation::checkUpdates();
        JSSTactivation::addCapabilites();        
    }

    static private function addCapabilites() {
        $role = get_role('administrator');
        $role->add_cap('jsst_support_ticket');
    }

    static private function checkUpdates() {
        include_once jssupportticket::$_path . 'includes/updates/updates.php';
        JSSTupdates::checkUpdates();
    }

    static private function insertMenu() {
        $pageexist = jssupportticket::$_db->get_var("Select COUNT(id) FROM `" . jssupportticket::$_db->prefix . "posts` WHERE post_name = 'js-support-ticket-controlpanel'");
        if ($pageexist == 0) {
            $post = array(
                'post_name' => 'js-support-ticket-controlpanel',
                'post_title' => 'JS Support Ticket',
                'post_status' => 'publish',
                'post_content' => '[jssupportticket]',
                'post_type' => 'page'
            );
            wp_insert_post($post);
        } else {
            jssupportticket::$_db->get_var("UPDATE `" . jssupportticket::$_db->prefix . "posts` SET post_status = 'publish' WHERE post_name = 'js-support-ticket-controlpanel'");
        }
        update_option('rewrite_rules', '');
    }

    static private function runSQL() {
        $pageid = jssupportticket::$_db->get_var("SELECT id FROM `" . jssupportticket::$_db->prefix . "posts` WHERE post_name = 'js-support-ticket-controlpanel'");
        // Run Sql to install the database;
        $query = "CREATE TABLE IF NOT EXISTS `" . jssupportticket::$_db->prefix . "js_ticket_config` (
                      `configname` varchar(100) NOT NULL DEFAULT '',
                      `configvalue` varchar(255) NOT NULL DEFAULT '',
                      `configfor` varchar(50) DEFAULT NULL,
                      PRIMARY KEY (`configname`),
                      FULLTEXT KEY `config_name` (`configname`),
                      FULLTEXT KEY `config_for` (`configfor`)
                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
        jssupportticket::$_db->query($query);
        $uid = get_current_user_id();
        $runConfig = jssupportticket::$_db->get_var("SELECT COUNT(configname) FROM `" . jssupportticket::$_db->prefix . "js_ticket_config`");
        if ($runConfig == 0) {
            $query = "CREATE TABLE IF NOT EXISTS `" . jssupportticket::$_db->prefix . "js_ticket_attachments` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `ticketid` int(11) DEFAULT NULL,
                                  `replyattachmentid` int(11) DEFAULT NULL,
                                  `filesize` varchar(32) DEFAULT NULL,
                                  `filename` varchar(128) DEFAULT NULL,
                                  `filekey` varchar(128) DEFAULT NULL,
                                  `deleted` tinyint(1) DEFAULT NULL,
                                  `status` tinyint(1) DEFAULT NULL,
                                  `created` datetime DEFAULT NULL,
                                  PRIMARY KEY (`id`)
                                ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
            jssupportticket::$_db->query($query);

            $query = "INSERT INTO `" . jssupportticket::$_db->prefix . "js_ticket_config` (`configname`, `configvalue`, `configfor`) VALUES ('title', 'JS Support Ticket System', 'default'),('offline', '2', 'default'),('offline_message', 'We are offline now please come back soon.\r\n\r\nThank you', 'default'),('data_directory', 'jssupportticketdata', 'default'),('date_format', 'd-m-Y', 'default'),('ticket_overdue', '5', 'default'),('ticket_auto_close', '5', 'default'),('no_of_attachement', '5', 'default'),('file_maximum_size', '3072', 'default'),('file_extension', 'doc,docx,odt,pdf,txt,png,jpeg,jpg', 'default'),('show_current_location', '2', 'default'),('maximum_tickets', '100', 'default'),('reopen_ticket_within_days', '5', 'default'),('visitor_can_create_ticket', '2', 'default'),('show_captcha_on_visitor_from_ticket', '1', 'default'),('default_alert_email', '1', 'default'),('default_admin_email', '1', 'default'),('new_ticket_mail_to_admin', '1', 'default'),('new_ticket_mail_to_staff_members', '1', 'default'),('banemail_mail_to_admin', '1', 'default'),('ticket_reassign_admin', '1', 'default'),('ticket_reassign_staff', '1', 'default'),('ticket_reassign_user', '0', 'default'),('ticket_close_admin', '1', 'default'),('ticket_close_staff', '1', 'default'),('ticket_close_user', '1', 'default'),('ticket_delete_admin', '1', 'default'),('ticket_delete_staff', '1', 'default'),('ticket_delete_user', '1', 'default'),('ticket_mark_overdue_admin', '1', 'default'),('ticket_mark_overdue_staff', '1', 'default'),('ticket_mark_overdue_user', '1', 'default'),('ticket_ban_email_admin', '1', 'default'),('ticket_ban_email_staff', '1', 'default'),('ticket_ban_email_user', '1', 'default'),('ticket_department_transfer_admin', '0', 'default'),('ticket_department_transfer_staff', '1', 'default'),('ticket_department_transfer_user', '0', 'default'),('ticket_reply_ticket_user_admin', '0', 'default'),('ticket_reply_ticket_user_staff', '1', 'default'),('ticket_reply_ticket_user_user', '1', 'default'),('ticket_response_to_staff_admin', '0', 'default'),('ticket_response_to_staff_staff', '1', 'default'),('ticket_response_to_staff_user', '1', 'default'),('ticker_ban_eamil_and_close_ticktet_admin', '1', 'default'),('ticker_ban_eamil_and_close_ticktet_staff', '1', 'default'),('ticker_ban_eamil_and_close_ticktet_user', '1', 'default'),('unban_email_admin', '1', 'default'),('unban_email_staff', '1', 'default'),('unban_email_user', '1', 'default'),('maximum_open_tickets', '25', 'deafult'),('pagination_default_page_size', '10', 'deafult'),('recaptcha_publickey', '', 'default'),('recaptcha_privatekey', '', 'default'),('captcha_selection', '2', 'default'),('owncaptcha_calculationtype', '1', 'default'),('owncaptcha_totaloperand', '2', 'default'),('owncaptcha_subtractionans', '1', 'default'),('ticket_lock_staff', '1', 'email'),('ticket_lock_admin', '1', 'email'),('ticket_lock_user', '1', 'email'),('ticket_unlock_staff', '1', 'email'),('ticket_unlock_admin', '1', 'email'),('ticket_unlock_user', '1', 'email'),('ticket_mark_progress_staff', '1', 'email'),('ticket_mark_progress_admin', '0', 'email'),('ticket_mark_progress_user', '1', 'email'),('ticket_priority_staff', '1', 'email'),('ticket_priority_admin', '0', 'email'),('ticket_priority_user', '1', 'email'),('new_ticket_message', '', 'default'),('cplink_openticket_staff', '1', 'cplink'),('cplink_myticket_staff', '1', 'cplink'),('cplink_addrole_staff', '2', 'cplink'),('cplink_roles_staff', '1', 'cplink'),('cplink_addstaff_staff', '2', 'cplink'),('cplink_staff_staff', '1', 'cplink'),('cplink_adddepartment_staff', '2', 'cplink'),('cplink_department_staff', '1', 'cplink'),('cplink_addcategory_staff', '2', 'cplink'),('cplink_category_staff', '1', 'cplink'),('cplink_addkbarticle_staff', '2', 'cplink'),('cplink_kbarticle_staff', '1', 'cplink'),('cplink_adddownload_staff', '2', 'cplink'),('cplink_download_staff', '1', 'cplink'),('cplink_addannouncement_staff', '2', 'cplink'),('cplink_announcement_staff', '1', 'cplink'),('cplink_addfaq_staff', '2', 'cplink'),('cplink_faq_staff', '1', 'cplink'),('cplink_mail_staff', '1', 'cplink'),('cplink_myprofile_staff', '1', 'cplink'),('cplink_openticket_user', '1', 'cplink'),('cplink_myticket_user', '1', 'cplink'),('cplink_checkticketstatus_user', '1', 'cplink'),('cplink_downloads_user', '1', 'cplink'),('cplink_announcements_user', '1', 'cplink'),('cplink_faqs_user', '1', 'cplink'),('cplink_knowledgebase_user', '1', 'cplink'),('tplink_home_staff', '1', 'tplink'),('tplink_tickets_staff', '1', 'tplink'),('tplink_knowledgebase_staff', '1', 'tplink'),('tplink_announcements_staff', '1', 'tplink'),('tplink_downloads_staff', '1', 'tplink'),('tplink_faqs_staff', '1', 'tplink'),('tplink_home_user', '1', 'tplink'),('tplink_tickets_user', '1', 'tplink'),('tplink_knowledgebase_user', '1', 'tplink'),('tplink_announcements_user', '1', 'tplink'),('tplink_downloads_user', '1', 'tplink'),('tplink_faqs_user', '1', 'tplink'),('show_breadcrumbs', '1', 'default'),('productcode', 'jsticket', 'default'),('versioncode', '1.1.5', 'default'),('productversion', '115', 'default'),('producttype', 'free', 'default'),('tve_enabled', '2', 'default'),('tve_mailreadtype', '3', 'default'),('tve_attachment', '1', 'default'),('tve_emailreadingdelay', '300', 'default'),('tve_hosttype', '4', 'default'),('tve_hostname', '', 'default'),('tve_emailaddress', '', 'default'),('tve_emailpassword', '', 'default'),('lastEmailReadingTime', '', 'default'),('login_redirect', '2', 'default'),('count_on_myticket', '1', 'default'),('system_slug', 'jssupportticket', 'default'),('default_pageid', '".$pageid."', 'default'),('support_screentag', '1', 'default'),('screentag_position', '1', 'default'),('last_step_updater','','default'),('cplink_login_logout_user','1','cplink'),('cplink_login_logout_staff','1','cplink');";
            jssupportticket::$_db->query($query);

            $query = "CREATE TABLE IF NOT EXISTS `" . jssupportticket::$_db->prefix . "js_ticket_departments` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `emailtemplateid` int(11) DEFAULT NULL,
                                  `emailid` int(11) DEFAULT NULL,
                                  `autoresponceemailid` int(11) DEFAULT NULL,
                                  `managerid` int(11) DEFAULT NULL,
                                  `departmentname` varchar(32) DEFAULT NULL,
                                  `departmentsignature` text,
                                  `ispublic` tinyint(1) DEFAULT NULL,
                                  `ticketautoresponce` tinyint(1) DEFAULT NULL,
                                  `messageautoresponce` tinyint(1) DEFAULT NULL,
                                  `canappendsignature` tinyint(1) DEFAULT NULL,
                                  `ordering` int(11) NOT NULL,
                                  `updated` datetime DEFAULT NULL,
                                  `created` datetime DEFAULT NULL,
                                  `status` tinyint(1) DEFAULT NULL,
                                  `isdefault` tinyint(1) DEFAULT NULL,
                                  `sendmail` tinyint NOT NULL DEFAULT '0',
                                  PRIMARY KEY (`id`)
                                ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2;";
            jssupportticket::$_db->query($query);
            $query = "INSERT INTO `" . jssupportticket::$_db->prefix . "js_ticket_departments` (`id`, `emailtemplateid`, `emailid`, `autoresponceemailid`, `managerid`, `departmentname`, `departmentsignature`, `ispublic`, `ticketautoresponce`, `messageautoresponce`, `canappendsignature`, `ordering`, `updated`, `created`, `status`) VALUES (1, NULL, 1, NULL, NULL, 'Support', '-- \n\n Support Department.', 1, NULL, NULL, 1, 1, '" . date_i18n('Y-m-d H:i:s') . "', '" . date_i18n('Y-m-d H:i:s') . "', 1);";
            jssupportticket::$_db->query($query);
            $query = "CREATE TABLE IF NOT EXISTS `" . jssupportticket::$_db->prefix . "js_ticket_email` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `autoresponse` tinyint(1) DEFAULT NULL,
                                  `priorityid` int(11) DEFAULT NULL,
                                  `email` varchar(125) DEFAULT NULL,
                                  `name` varchar(32) DEFAULT NULL,
                                  `uid` int(11) DEFAULT NULL,
                                  `password` varchar(125) DEFAULT NULL COMMENT '    ',
                                  `status` tinyint(1) DEFAULT NULL,
                                  `mailhost` varchar(125) DEFAULT NULL,
                                  `mailprotocol` enum('pop','map') DEFAULT NULL,
                                  `mailencryption` enum('NONE','SSL') DEFAULT NULL,
                                  `mailport` smallint(6) DEFAULT NULL,
                                  `mailfetchfrequency` tinyint(3) DEFAULT NULL,
                                  `mailfetchmaximum` tinyint(4) DEFAULT NULL,
                                  `maildeleted` tinyint(1) DEFAULT NULL,
                                  `mailerrors` tinyint(3) DEFAULT NULL,
                                  `maillasterror` datetime DEFAULT NULL,
                                  `maillastfetch` datetime DEFAULT NULL,
                                  `smtpactive` tinyint(1) DEFAULT NULL,
                                  `smtphost` varchar(125) DEFAULT NULL,
                                  `smtpport` smallint(6) DEFAULT NULL,
                                  `smtpsecure` tinyint(1) DEFAULT NULL,
                                  `smtpauthencation` tinyint(1) DEFAULT NULL,
                                  `created` datetime DEFAULT NULL,
                                  `updated` datetime DEFAULT NULL,
                                  PRIMARY KEY (`id`)
                                ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2;";
            jssupportticket::$_db->query($query);
            $systememail = get_option('admin_email');
            $query = "INSERT INTO `" . jssupportticket::$_db->prefix . "js_ticket_email` (`id`,`autoresponse`, `priorityid`, `email`, `name`, `uid`, `password`, `status`, `mailhost`, `mailprotocol`, `mailencryption`, `mailport`, `mailfetchfrequency`, `mailfetchmaximum`, `maildeleted`, `mailerrors`, `maillasterror`, `maillastfetch`, `smtpactive`, `smtphost`, `smtpport`, `smtpsecure`, `smtpauthencation`, `created`, `updated`) VALUES
                                (1,1, 1, '" . $systememail . "', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2014-10-02 10:38:48', '0000-00-00 00:00:00');";
            jssupportticket::$_db->query($query);

            $query = "CREATE TABLE IF NOT EXISTS `" . jssupportticket::$_db->prefix . "js_ticket_emailtemplates` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `templatefor` varchar(50) DEFAULT NULL,
                                  `title` varchar(50) DEFAULT NULL,
                                  `subject` varchar(255) DEFAULT NULL,
                                  `body` text,
                                  `created` datetime DEFAULT NULL,
                                  `status` tinyint(1) DEFAULT NULL,
                                  PRIMARY KEY (`id`)
                                ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26;";
            jssupportticket::$_db->query($query);
            $query = "INSERT INTO `" . jssupportticket::$_db->prefix . "js_ticket_emailtemplates` (`id`, `templatefor`, `title`, `subject`, `body`, `created`, `status`) VALUES
                                (1, 'ticket-new', '', 'JS Tickets: New Ticket Received ', 'Dear {USERNAME},\r\n\r\nYour support ticket <strong>{SUBJECT}</strong> has been submitted. We try to reply to all tickets as soon as possible, usually within 24 hours.\r\n\r\nYour tracking ID: {TRACKINGID}\r\n\r\nYour Email ID: {EMAIL}\r\n\r\nTicket message: {MESSAGE}\r\n\r\nYou can view the status of your ticket here:\r\n{TICKETURL}\r\n\r\nYou will receive an e-mail notification when our staff replies to your ticket.\r\n\r\n<span style=\"color: red;\"><strong>*DO NOT REPLY TO THIS E-MAIL*</strong></span>\r\nThis is an automated e-mail message sent from our support system. Do not reply to this e-mail as we wonot receive your reply!', NULL, 0),
                                (2, 'department-new', '', 'JS Tickets :  New Department {DEPARTMENT_TITLE} has beed received ', 'Hello Admin ,\r\n\r\nWe receive new department.\r\n\r\n<span style=\"color: red;\"><strong>*DO NOT REPLY TO THIS E-MAIL*</strong></span>\r\nThis is an automated e-mail message sent from our support system. Do not reply to this e-mail as we wonot receive your reply!', NULL, 0),
                                (3, 'group-new', '', 'JS Tickets :  New Group {GROUP_TITLE} has beed received ', 'Hello Admin ,\r\n\r\nWe receive new group.\r\n\r\n<span style=\"color: red;\"><strong>*DO NOT REPLY TO THIS E-MAIL*</strong></span>\r\nThis is an automated e-mail message sent from our support system. Do not reply to this e-mail as we wonot receive your reply!', NULL, 0),
                                (4, 'staff-new', '', 'JS Tickets :  New Staff Member {STAFF_MEMBER_NAME} has beed received ', 'Hello Admin ,\r\n\r\nWe receive new staff member.\r\n\r\n<span style=\"color: red;\"><strong>*DO NOT REPLY TO THIS E-MAIL*</strong></span>\r\nThis is an automated e-mail message sent from our support system. Do not reply to this e-mail as we wonot receive your reply!', NULL, 0),
                                (5, 'helptopic-new', '', 'JS Tickets :  New Help Topic {HELPTOPIC_TITLE} has beed received', 'Hello Admin ,\r\n\r\nWe receive new help topic {HELPTOPIC_TITLE} of department {DEPARTMENT_TITLE}.\r\n\r\n<span style=\"color: red;\"><strong>*DO NOT REPLY TO THIS E-MAIL*</strong></span>\r\nThis is an automated e-mail message sent from our support system. Do not reply to this e-mail as we wonot receive your reply!', NULL, 0),
                                (6, 'reassign-tk', '', 'JS Tickets: Reassign Ticket  ', 'Sucess: Ticket Reassign\r\n\r\nTicket Subject : {SUBJECT}\r\n\r\nYour ticket has been successfully Reassign! Ticket ID:{TRACKINGID} is Reassign to Staff Member:{STAFF_MEMBER_TITLE}\r\n\r\nYou can manage this ticket here\r\n\r\n{TICKETURL}\r\n\r\n<span style=\"color: red;\"><strong>*DO NOT REPLY TO THIS E-MAIL*</strong></span>\r\nThis is an automated e-mail message sent from our support system. Do not reply to this e-mail as we wonot receive your reply!', NULL, 0),
                                (7, 'close-tk', '', 'JS Tickets:  Close Ticket ', 'Ticket Close\r\n\r\nTicket Subject : {SUBJECT}\r\n\r\nTicket ID:{TRACKINGID} is Closed.\r\n\r\nYou can manage this ticket here\r\n\r\n{TICKETURL}\r\n\r\n<span style=\"color: red;\"><strong>*DO NOT REPLY TO THIS E-MAIL*</strong></span>\r\nThis is an automated e-mail message sent from our support system. Do not reply to this e-mail as we wonot receive your reply!', NULL, 0),
                                (8, 'delete-tk', '', 'JS Tickets:  Delete Ticket', 'Ticket Deleted\r\n\r\nTicket Subject : {SUBJECT}\r\n\r\nTicket ID:{TRACKINGID} is Deleted.\r\n\r\n<span style=\"color: red;\"><strong>*DO NOT REPLY TO THIS E-MAIL*</strong></span>\r\nThis is an automated e-mail message sent from our support system. Do not reply to this e-mail as we wonot receive your reply!', NULL, 0),
                                (9, 'moverdue-tk', '', 'JS Tickets:  Markoverdue Ticket ', 'Ticket Markoverdue\r\n\r\nTicket Subject : {SUBJECT}\r\n\r\nTicket ID:{TRACKINGID} is Markoverdue.\r\n\r\nYou can manage this ticket here\r\n\r\n{TICKETURL}\r\n\r\n<span style=\"color: red;\"><strong>*DO NOT REPLY TO THIS E-MAIL*</strong></span>\r\nThis is an automated e-mail message sent from our support system. Do not reply to this e-mail as we wonot receive your reply!', NULL, 0),
                                (10, 'banemail-tk', '', 'JS Tickets:  Email Baned ', 'Email Baned\r\n\r\nThis email {EMAIL_ADDRESS} is Baned.\r\n\r\n<span style=\"color: red;\"><strong>*DO NOT REPLY TO THIS E-MAIL*</strong></span>\r\nThis is an automated e-mail message sent from our support system. Do not reply to this e-mail as we wonot receive your reply!', NULL, 0),
                                (11, 'deptrans-tk', '', 'JS Tickets:  Ticket Transfer to Department{DEPARTMENT_TITLE} ', 'Ticket transfer to department\r\n\r\nTicket Subject : {SUBJECT}\r\n\r\nTicket ID: {TRACKINGID} is transfer to department{DEPARTMENT_TITLE}.\r\n\r\n<span style=\"color: red;\"><strong>*DO NOT REPLY TO THIS E-MAIL*</strong></span>\r\nThis is an automated e-mail message sent from our support system. Do not reply to this e-mail as we wonot receive your reply!', NULL, 0),
                                (12, 'banemailcloseticket-tk', '', 'JS Tickets: Email Baned and ticket close ', 'Email Baned and ticket close\r\n\r\nTicket Subject : {SUBJECT}\r\n\r\nThis email {EMAIL_ADDRESS} is Baned and ticket ID:{TICKETID} is closed.\r\n\r\n<span style=\"color: red;\"><strong>*DO NOT REPLY TO THIS E-MAIL*</strong></span>\r\nThis is an automated e-mail message sent from our support system. Do not reply to this e-mail as we wonot receive your reply!', NULL, 0),
                                (13, 'unbanemail-tk', '', 'JS Tickets:  Email Unbaned ', 'Email Unbaned\r\n\r\nThis email {EMAIL_ADDRESS} is Unbaned\r\n\r\n<span style=\"color: red;\"><strong>*DO NOT REPLY TO THIS E-MAIL*</strong></span>\r\nThis is an automated e-mail message sent from our support system. Do not reply to this e-mail as we wonot receive your reply!', NULL, 0),
                                (14, 'reply-tk', '', 'JS Tickets:  Ticket Reply  ', 'Hello,\r\n\r\nA new reply of ticket <strong>{SUBJECT} </strong>has been submitted.\r\n\r\nUser Name: {USERNAME}\r\n\r\nTracking ID: {TRACKINGID}\r\n\r\nEmail: {EMAIL}\r\n\r\nMessage: {MESSAGE}\r\n\r\nYou can manage this ticket here:\r\n{TICKETURL}\r\n\r\n<span style=\"color: red;\"><strong>*DO NOT REPLY TO THIS E-MAIL*</strong></span>\r\nThis is an automated e-mail message sent from our support system. Do not reply to this e-mail as we wonot receive your reply!', NULL, 0),
                                (15, 'responce-tk', '', 'JS Tickets:  Ticket Responce ', 'Hello,\r\n\r\nStaff just reply of your ticket <strong>{SUBJECT}</strong>.\r\n\r\nUser Name: {USERNAME}\r\n\r\nTracking ID: {TRACKINGID}\r\n\r\nEmail: {EMAIL}\r\n\r\nMessage: {MESSAGE}\r\n\r\nYou can manage this ticket here:\r\n{TICKETURL}\r\n\r\n<span style=\"color: red;\"><strong>*DO NOT REPLY TO THIS E-MAIL*</strong></span>\r\nThis is an automated e-mail message sent from our support system. Do not reply to this e-mail as we wonot receive your reply!', NULL, 0),
                                (16, 'ticket-staff', '', 'JS Tickets: New Ticket', 'Sucess: Ticket Submitted\r\n\r\nNew ticket <strong>{SUBJECT}</strong> has been submitted with the following details.\r\n\r\nTracking ID: {TRACKINGID}\r\n\r\nEmail ID: {EMAIL}\r\n\r\nTicket message: {MESSAGE}\r\n\r\nHelp Topic: {HELP_TOPIC}\r\n\r\nYou can view the status of your ticket here:\r\n{TICKETURL}\r\n\r\nYou will receive an e-mail notification when our staff replies to your ticket.\r\n\r\n<span style=\"color: red;\"><strong>*DO NOT REPLY TO THIS E-MAIL*</strong></span>\r\nThis is an automated e-mail message sent from our support system. Do not reply to this e-mail as we wonot receive your reply!', NULL, 0),
                                (17, 'banemail-trtk', '', 'JS Tickets: Banemail try new ticket ', 'Hello Admin ,\r\n\r\nThis email {EMAIL_ADDRESS} is baned and try open new ticket .\r\n\r\n<span style=\"color: red;\"><strong>*DO NOT REPLY TO THIS E-MAIL*</strong></span>\r\nThis is an automated e-mail message sent from our support system. Do not reply to this e-mail as we wonot receive your reply!', NULL, 0),
                                (18, 'ticket-new-admin', '', 'JS Tickets: New Ticket Received ', 'Hello,\r\n\r\nA new support ticket <strong>{SUBJECT}</strong> has been submitted. Ticket details.\r\n\r\nTracking ID: {TRACKINGID}\r\n\r\nEmail ID: {EMAIL}\r\n\r\nTicket message: {MESSAGE}\r\n\r\nYou can manage this ticket here:\r\n{TICKETURL}\r\n\r\n<span style=\"color: #ff0000;\"><strong>*DO NOT REPLY TO THIS E-MAIL*</strong></span>\r\nThis is an automated e-mail message sent from our support system. Do not reply to this e-mail as we wonot receive your reply!', NULL, 0),
                                (19, 'lock-tk', '', 'JS Tickets :  Ticket {TRACKINGID} has been locked', 'Dear {USERNAME} ,\r\n\r\nYour Ticket {TRACKINGID} has been locked.\r\n\r\nUsername : {USERNAME}\r\nSubject : {SUBJECT}\r\nTicket ID : {TRACKINGID}\r\nEmail : {EMAIL}\r\n\r\nYou can manage ticket here {TICKETURL}\r\n\r\n<span style=\"color: red;\"><strong>*DO NOT REPLY TO THIS E-MAIL*</strong></span>\r\nThis is an automated e-mail message sent from our support system. Do not reply to this e-mail as we wonot receive your reply!', NULL, 0),
                                (21, 'unlock-tk', '', 'JS Tickets :  Ticket {TRACKINGID} has been unlocked', 'Dear {USERNAME} ,\r\n\r\nYour Ticket {TRACKINGID} has been unlocked.\r\n\r\nUsername : {USERNAME}\r\nSubject : {SUBJECT}\r\nTicket ID : {TRACKINGID}\r\nEmail : {EMAIL}\r\n\r\nYou can manage ticket here {TICKETURL}\r\n\r\n<span style=\"color: red;\"><strong>*DO NOT REPLY TO THIS E-MAIL*</strong></span>\r\nThis is an automated e-mail message sent from our support system. Do not reply to this e-mail as we wonot receive your reply!', NULL, 0),
                                (22, 'minprogress-tk', '', 'JS Tickets:  Mark in progress Ticket ', 'Ticket Mark in progress\r\n\r\nTicket Subject : {SUBJECT}\r\n\r\nTicket ID:{TRACKINGID} is Mark in progress.\r\n\r\nYou can manage this ticket here\r\n\r\n{TICKETURL}\r\n\r\n<span style=\"color: red;\"><strong>*DO NOT REPLY TO THIS E-MAIL*</strong></span>\r\nThis is an automated e-mail message sent from our support system. Do not reply to this e-mail as we wonot receive your reply!', NULL, 0),
                                (23, 'prtrans-tk', '', 'JS Tickets:  Ticket Priority changed ', 'Ticket priority is changed\r\n\r\nTicket Subject : {SUBJECT}\r\n\r\nTicket ID: {TRACKINGID} is priority changed and new priority and new priority {PRIORITY_TITLE}.\r\n\r\nYou can manage this ticket here\r\n\r\n{TICKETURL}\r\n\r\n<span style=\"color: red;\"><strong>*DO NOT REPLY TO THIS E-MAIL*</strong></span>\r\nThis is an automated e-mail message sent from our support system. Do not reply to this e-mail as we wonot receive your reply!', NULL, 0),
                                (24, 'mail-new', '', 'JS Tickets:  New Mail has been sent by {STAFF_MEMBER_NAME}', '<p>New mail has been sent by the {STAFF_MEMBER_NAME}</p>\n<p>Mail Subject : {SUBJECT}</p>\n<p>Message : {MESSAGE}</p>\n<p><span style=\"color: red;\"><strong>*DO NOT REPLY TO THIS E-MAIL*</strong></span><br />\nThis is an automated e-mail message sent from our support system. Do not reply to this e-mail as we wonot receive your reply!</p>\n', NULL, 0),
                                (25, 'mail-rpy', '', 'JS Tickets:  New reply has been sent by {STAFF_MEMBER_NAME}', '<p>New reply has been sent by the {STAFF_MEMBER_NAME}</p>\n<p>Mail Subject : {SUBJECT}</p>\n<p>Message : {MESSAGE}</p>\n<p><span style=\"color: red;\"><strong>*DO NOT REPLY TO THIS E-MAIL*</strong></span><br />\nThis is an automated e-mail message sent from our support system. Do not reply to this e-mail as we wonot receive your reply!</p>\n', NULL, 0);";
            jssupportticket::$_db->query($query);
            $query = "CREATE TABLE IF NOT EXISTS `" . jssupportticket::$_db->prefix . "js_ticket_priorities` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `priority` varchar(60) DEFAULT NULL,
                                  `prioritycolour` varchar(7) DEFAULT NULL,
                                  `priorityurgency` int(1) DEFAULT NULL,
                                  `ispublic` varchar(45) DEFAULT NULL,
                                  `ordering` int(11) NOT NULL,
                                  `isdefault` tinyint(1) DEFAULT NULL,
                                  `status` tinyint(4) NOT NULL DEFAULT '0',
                                  PRIMARY KEY (`id`)
                                ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5;";
            jssupportticket::$_db->query($query);
            $query = "INSERT INTO `" . jssupportticket::$_db->prefix . "js_ticket_priorities` (`id`, `priority`, `prioritycolour`, `priorityurgency`, `ispublic`, `ordering`, `isdefault`, `status`) VALUES (1, 'Low', '#86f793', 0, '1', 1, 1, 0),(2, 'High', '#ed8e00', 0, '1', 3, 0, 1),(3, 'Normal', '#c7cbf5', 0, '1', 2, 0, 1),(4, 'Urgent', '#c90000', 0, '', 4, 0, 0);";
            jssupportticket::$_db->query($query);
            $query = "CREATE TABLE IF NOT EXISTS `" . jssupportticket::$_db->prefix . "js_ticket_replies` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `uid` int(11) NOT NULL,
                                  `ticketid` int(11) DEFAULT NULL,
                                  `name` varchar(50) DEFAULT NULL,
                                  `message` text,
                                  `staffid` int(11) DEFAULT NULL,
                                  `rating` enum('1','5') DEFAULT NULL,
                                  `status` tinyint(1) DEFAULT NULL,
                                  `created` datetime DEFAULT NULL,
                                  `ticketviaemail` tinyint(1) NOT NULL,
                                  PRIMARY KEY (`id`)
                                ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
            jssupportticket::$_db->query($query);
            $query = "CREATE TABLE IF NOT EXISTS `" . jssupportticket::$_db->prefix . "js_ticket_system_errors` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `uid` int(11) DEFAULT NULL,
                                  `error` text,
                                  `isview` tinyint(1) DEFAULT '0',
                                  `created` datetime DEFAULT NULL,
                                  PRIMARY KEY (`id`)
                                ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
            jssupportticket::$_db->query($query);
            $query = "CREATE TABLE IF NOT EXISTS `" . jssupportticket::$_db->prefix . "js_ticket_tickets` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `uid` int(11) DEFAULT NULL,
                                  `ticketid` varchar(11) DEFAULT NULL,
                                  `departmentid` int(11) DEFAULT NULL,
                                  `priorityid` int(11) DEFAULT NULL,
                                  `staffid` int(11) DEFAULT NULL,
                                  `email` varchar(255) DEFAULT NULL,
                                  `name` varchar(100) DEFAULT NULL,
                                  `subject` varchar(255) DEFAULT NULL,
                                  `message` text,
                                  `helptopicid` int(11) DEFAULT NULL,
                                  `phone` varchar(100) DEFAULT NULL,
                                  `phoneext` varchar(25) DEFAULT NULL,
                                  `status` tinyint(1) DEFAULT NULL,
                                  `isoverdue` tinyint(1) DEFAULT NULL,
                                  `isanswered` tinyint(1) NOT NULL DEFAULT '0',
                                  `duedate` datetime DEFAULT NULL,
                                  `reopened` datetime DEFAULT NULL,
                                  `closed` datetime DEFAULT NULL,
                                  `lastreply` datetime DEFAULT NULL,
                                  `created` datetime DEFAULT NULL,
                                  `updated` datetime DEFAULT NULL,
                                  `lock` tinyint(4) NOT NULL DEFAULT '0',
                                  `ticketviaemail` tinyint(1) NOT NULL,
                                  `attachmentdir` VARCHAR(50) NOT NULL,
                                  `params` longtext NULL,
                                  PRIMARY KEY (`id`)
                                ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
            jssupportticket::$_db->query($query);
            $query = "CREATE TABLE IF NOT EXISTS `" . jssupportticket::$_db->prefix . "js_ticket_fieldsordering` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `field` varchar(50) NOT NULL,
                        `fieldtitle` varchar(50) DEFAULT NULL,
                        `ordering` int(11) DEFAULT NULL,
                        `section` varchar(20) DEFAULT NULL,
                        `fieldfor` tinyint(2) DEFAULT NULL,
                        `published` tinyint(1) DEFAULT NULL,
                        `sys` tinyint(1) NOT NULL,
                        `cannotunpublish` tinyint(1) NOT NULL,
                        `required` tinyint(1) NOT NULL DEFAULT '0',
                        `size` varchar(200) DEFAULT NULL,
                        `maxlength` varchar(200) DEFAULT NULL,
                        `cols` varchar(200) DEFAULT NULL,
                        `rows` varchar(200) DEFAULT NULL,
                        `isuserfield` tinyint(4) DEFAULT NULL,
                        `userfieldtype` varchar(250) DEFAULT NULL,
                        `depandant_field` varchar(250) DEFAULT NULL,
                        `showonlisting` tinyint(4) DEFAULT NULL,
                        `cannotshowonlisting` tinyint(4) DEFAULT NULL,
                        `search_user` tinyint(4) DEFAULT NULL,
                        `cannotsearch` tinyint(4) DEFAULT NULL,
                        `isvisitorpublished` tinyint(4) DEFAULT NULL,
                        `search_visitor` tinyint(4) DEFAULT NULL,
                        `userfieldparams` longtext,
                        PRIMARY KEY (`id`),KEY `fieldordering_filedfor` (`fieldfor`)) 
                        ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14;";
            jssupportticket::$_db->query($query);
            $query = "INSERT INTO `" . jssupportticket::$_db->prefix . "js_ticket_fieldsordering` (`id`, `field`, `fieldtitle`, `ordering`, `section`, `fieldfor`, `published`, `sys`, `cannotunpublish`, `required`,`cannotshowonlisting`,`cannotsearch`,`isvisitorpublished`) VALUES (1, 'email', 'Email Address', 2, '10', 1, 1, 0, 1, 1, 1, 1, 1),(15, 'users', 'Users', 1, '10', 1, 1, 0, 0, 0, 1, 1, 1),(2, 'fullname', 'Full Name', 3, '10', 1, 1, 0, 1, 1, 1, 1, 1),(3, 'phone', 'Phone', 4, '10', 1, 1, 0, 0, 0, 1, 1, 1),(4, 'department', 'Department', 5, '10', 1, 1, 0, 0, 0, 1, 1, 1),(5, 'helptopic', 'Help Topic', 6, '10', 1, 1, 0, 0, 0, 1, 1, 1),(6, 'priority', 'Priority', 7, '10', 1, 1, 0, 1, 1, 1, 1, 1),(7, 'subject', 'Subject', 8, '10', 1, 1, 0, 1, 1, 1, 1, 1),(8, 'premade', 'Premade', 9, '10', 1, 1, 0, 0, 0, 1, 1, 1),(9, 'issuesummary', 'Issue Summary', 10, '10', 1, 1, 0, 1, 1, 1, 1, 1),(10, 'attachments', 'Attachments', 11, '10', 1, 1, 0, 0, 0, 1, 1, 1),(11, 'internalnotetitle', 'Internal Note Title', 12, '10', 1, 1, 0, 0, 0, 1, 1, 1),(12, 'assignto', 'Assign To', 13, '10', 1, 1, 0, 0, 0, 1, 1, 1),(13, 'duedate', 'Due Date', 14, '10', 1, 1, 0, 0, 0, 1, 1, 1),(14, 'status', 'Status', 15, '10', 1, 1, 0, 0, 0, 1, 1, 1),(15, 'users', 'Users', 15, '10', 1, 1, 0, 0, 0, 1, 1, 1);";
            jssupportticket::$_db->query($query);
        }
    }

}

?>
