<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class jssupportticketadmin {

    function __construct() {
        add_action('admin_menu', array($this, 'mainmenu'));
    }

    function mainmenu() {
        add_menu_page(__('Control Panel', 'js-support-ticket'), // Page title
                __('JS Support Ticket', 'js-support-ticket'), // menu title
                'jsst_support_ticket', // capability
                'jssupportticket', //menu slug
                array($this, 'showAdminPage'), // function name
                plugins_url('js-support-ticket/includes/images/admin_ticket.png')
        );
        add_submenu_page('jssupportticket', // parent slug
                __('Tickets', 'js-support-ticket'), // Page title
                __('Tickets', 'js-support-ticket'), // menu title
                'jsst_support_ticket', // capability
                'ticket', //menu slug
                array($this, 'showAdminPage') // function name
        );
        add_submenu_page('jssupportticket', // parent slug
                __('Configurations', 'js-support-ticket'), // Page title
                __('Configurations', 'js-support-ticket'), // menu title
                'jsst_support_ticket', // capability
                'configuration', //menu slug
                array($this, 'showAdminPage') // function name
        );
        add_submenu_page('jssupportticket', // parent slug
                __('Department', 'js-support-ticket'), // Page title
                __('Departments', 'js-support-ticket'), // menu title
                'jsst_support_ticket', // capability
                'department', //menu slug
                array($this, 'showAdminPage') // function name
        );
        add_submenu_page('jssupportticket', // parent slug
                __('Priorities', 'js-support-ticket'), // Page title
                __('Priority', 'js-support-ticket'), // menu title
                'jsst_support_ticket', // capability
                'priority', //menu slug
                array($this, 'showAdminPage') // function name
        );
        add_submenu_page('jssupportticket', // parent slug
                __('Emails', 'js-support-ticket'), // Page title
                __('System Emails', 'js-support-ticket'), // menu title
                'jsst_support_ticket', // capability
                'email', //menu slug
                array($this, 'showAdminPage') // function name
        );
        add_submenu_page('jssupportticket', // parent slug
                __('Email Templates', 'js-support-ticket'), // Page title
                __('Email Templates', 'js-support-ticket'), // menu title
                'jsst_support_ticket', // capability
                'emailtemplate', //menu slug
                array($this, 'showAdminPage') // function name
        );
        add_submenu_page('jssupportticket', // parent slug
                __('System Errors', 'js-support-ticket'), // Page title
                __('System Errors', 'js-support-ticket'), // menu title
                'jsst_support_ticket', // capability
                'systemerror', //menu slug
                array($this, 'showAdminPage') // function name
        );
        add_submenu_page('jssupportticket', // parent slug
                __('Upgrade', 'js-support-ticket'), // Page title
                __('Upgrade', 'js-support-ticket'), // menu title
                'jsst_support_ticket', // capability
                'proinstaller', //menu slug
                array($this, 'showAdminPage') // function name
        );
        add_submenu_page('jssupportticket_hide', // parent slug
                __('Upgrade', 'js-support-ticket'), // Page title
                __('Reports', 'js-support-ticket'), // menu title
                'jsst_support_ticket', // capability
                'reports', //menu slug
                array($this, 'showAdminPage') // function name
        );
        add_submenu_page('jssupportticket_hide', // parent slug
                __('Fields ordering', 'js-support-ticket'), // Page title
                __('Fields ordering', 'js-support-ticket'), // menu title
                'jsst_support_ticket', // capability
                'fieldordering', //menu slug
                array($this, 'showAdminPage') // function name
        );
    }

    function showAdminPage() {
        jssupportticket::addStyleSheets();
        $page = JSSTrequest::getVar('page');
        JSSTincluder::include_file($page);
    }

}

$jssupportticketAdmin = new jssupportticketadmin();
?>
