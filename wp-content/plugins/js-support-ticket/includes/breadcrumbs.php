<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class JSSTbreadcrumbs {

    static function getBreadcrumbs() {
        if (jssupportticket::$_config['show_breadcrumbs'] != 1)
            return false;
        if (!is_admin()) {
            $editid = JSSTrequest::getVar('jssupportticketid');
            $isnew = ($editid == null) ? true : false;
            //$array[] = array('link' => site_url(home_url()), 'text' => __('Home','js-support-ticket'));
            $array[] = array('link' => site_url("?page_id=" . jssupportticket::getPageid() . "&jstmod=jssupportticket&jstlay=controlpanel"), 'text' => __('Control Panel', 'js-support-ticket'));
            $module = JSSTrequest::getVar('jstmod');
            $layout = JSSTrequest::getVar('jstlay');
            if (isset(jssupportticket::$_data['short_code_header'])) {
                switch (jssupportticket::$_data['short_code_header']) {
                    case 'myticket':
                        $module = 'ticket';
                        $layout = 'myticket';
                        break;
                    case 'addticket':
                        $module = 'ticket';
                        $layout = 'addticket';
                        break;
                }
            }

            if ($module != null) {
                switch ($module) {
                    case 'jssupportticket':
                        switch ($layout) {
                                case 'login':
                                    $text = __('Login', 'js-support-ticket');
                                    $array[] = array('link' => site_url('?page_id=' . jssupportticket::getPageid() . '&jstmod=jssupportticket&jstlay=login'), 'text' => $text);
                                break;
                            }    
                    break;
                    case 'ticket':
                        // Add default module link
                        $array[] = array('link' => site_url('?page_id=' . jssupportticket::getPageid() . '&jstmod=ticket&jstlay=myticket'), 'text' => __('Tickets', 'js-support-ticket'));
                        switch ($layout) {
                            case 'addticket':
                                // $layout1 = (JSSTincluder::getJSModel('staff')->isUserStaff()) ? 'staffmyticket':'myticket';
                                // $array[] = array('link' => site_url('?page_id='.jssupportticket::getPageid().'&jstmod=ticket&jstlay='.$layout1), 'text'=>__('My Tickets','js-support-ticket'));
                                $text = ($isnew) ? __('Add Ticket', 'js-support-ticket') : __('Edit Ticket', 'js-support-ticket');
                                $array[] = array('link' => site_url('?page_id=' . jssupportticket::getPageid() . '&jstmod=ticket&jstlay=addticket'), 'text' => $text);
                                break;
                            case 'myticket':
                                $array[] = array('link' => site_url('?page_id=' . jssupportticket::getPageid() . '&jstmod=ticket&jstlay=myticket'), 'text' => __('My Tickets', 'js-support-ticket'));
                                break;
                            case 'ticketdetail':
                                $layout1 = 'myticket';
                                $array[] = array('link' => site_url('?page_id=' . jssupportticket::getPageid() . '&jstmod=ticket&jstlay=' . $layout1), 'text' => __('My Tickets', 'js-support-ticket'));
                                $array[] = array('link' => site_url('?page_id=' . jssupportticket::getPageid() . '&jstmod=ticket&jstlay=ticketdetail'), 'text' => __('Ticket Detail', 'js-support-ticket'));
                                break;
                            case 'ticketstatus':
                                $array[] = array('link' => site_url('?page_id=' . jssupportticket::getPageid() . '&jstmod=ticket&jstlay=ticketstatus'), 'text' => __('Ticket Status', 'js-support-ticket'));
                                break;
                        }
                        break;
                }
            }
        }

        if (isset($array)) {
            $count = count($array);
            $i = 0;
            echo '<div id="jsst_breadcrumbs_parent">';
            foreach ($array AS $obj) {
                if ($i == 0) {
                    echo '<div class="home"><a href="' . $obj['link'] . '"><img class="homeicon" src="' . jssupportticket::$_pluginpath . 'includes/images/homeicon.png"/></a></div>';
                } else {
                    if ($i == ($count - 1)) {
                        echo '<div class="lastlink">' . $obj['text'] . '</div>';
                    } else {
                        echo '<div class="links"><a class="links" href="' . $obj['link'] . '">' . $obj['text'] . '</a></div>';
                    }
                }
                $i++;
            }
            echo '</div>';
        }
    }

}

$jsbreadcrumbs = new JSSTbreadcrumbs;
?>
