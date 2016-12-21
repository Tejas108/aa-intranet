<?php

/**
 * JS Support Ticket Uninstall
 *
 * Uninstalling JS Support Ticket tables, and pages.
 *
 * @author 		Ahmed Bilal
 * @category 	Core
 * @package 	JS Support Ticket/Uninstaller
 * @version     1.0
 */
if (!defined('WP_UNINSTALL_PLUGIN'))
    exit();

global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}js_ticket_attachments");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}js_ticket_config");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}js_ticket_departments");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}js_ticket_email");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}js_ticket_emailtemplates");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}js_ticket_priorities");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}js_ticket_replies");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}js_ticket_system_errors");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}js_ticket_tickets");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}js_ticket_userfields");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}js_ticket_userfieldvalues");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}js_ticket_userfield_data");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}js_ticket_fieldsordering");
