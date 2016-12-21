<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class JSSTshortcodes {

    function __construct() {
        add_shortcode('jssupportticket', array($this, 'show_main_ticket'));
        add_shortcode('jssupportticket_addticket', array($this, 'show_form_ticket'));
        add_shortcode('jssupportticket_mytickets', array($this, 'show_my_ticket'));
    }

    function show_main_ticket($raw_args, $content = null) {
        //default set of parameters for the front end shortcodes
        ob_start();
        $defaults = array(
            'jstmod' => '',
            'jstlay' => '',
        );
        $sanitized_args = shortcode_atts($defaults, $raw_args);
        jssupportticket::$_data['sanitized_args'] = $sanitized_args;
        jssupportticket::addStyleSheets();
        $pageid = get_the_ID();
        jssupportticket::setPageID($pageid);
        JSSTincluder::include_slug('');
        $content .= ob_get_clean();

        return $content;
    }

    function show_form_ticket($raw_args, $content = null) {
        //default set of parameters for the front end shortcodes
        ob_start();
        $pageid = get_the_ID();
        jssupportticket::setPageID($pageid);
        $module = JSSTRequest::getVar('jstmod', '', 'ticket');
        $layout = JSSTRequest::getVar('jstlay', '', 'addticket');
        if ($layout != 'addticket') {
            JSSTincluder::include_file($module);
        } else {
            $defaults = array(
                'job_type' => '',
                'city' => '',
                'company' => '',
            );
            $sanitized_args = shortcode_atts($defaults, $raw_args);
            jssupportticket::$_data['short_code_header'] = 'addticket';
            JSSTincluder::getJSModel('ticket')->getTicketsForForm(null);
            JSSTincluder::include_file($layout, 'ticket');
        }
        $content .= ob_get_clean();

        return $content;
    }

    function show_my_ticket($raw_args, $content = null) {
        //default set of parameters for the front end shortcodes
        ob_start();
        $pageid = get_the_ID();
        jssupportticket::setPageID($pageid);
        $module = JSSTRequest::getVar('jstmod', '', 'ticket');
        $layout = JSSTRequest::getVar('jstlay', '', 'mytickets');
        if ($layout != 'mytickets') {
            JSSTincluder::include_file($module);
        } else {
            $defaults = array(
                'list' => '',
                'ticketid' => '',
            );
            $list = JSSTrequest::getVar('list', 'get', null);
            $ticketid = JSSTrequest::getVar('ticketid', null, null);
            $option = get_option('jssupportticket-pageid', array());
            $id = $option[0];
            jssupportticket::$_pageid = $id;
            $args = shortcode_atts($defaults, $raw_args);
            if ($list == null)
                $list = $args['list'];
            if ($ticketid == null)
                $ticketid = $args['ticketid'];

            jssupportticket::$_data['short_code_header'] = 'myticket';
            JSSTincluder::getJSModel('ticket')->getMyTickets($list, $ticketid);
            JSSTincluder::include_file('myticket', 'ticket');
        }

        $content .= ob_get_clean();

        return $content;
    }

}

$shortcodes = new JSSTshortcodes();
?>
