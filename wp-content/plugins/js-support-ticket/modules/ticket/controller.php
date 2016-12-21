<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class JSSTticketController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        if (is_admin()) {
            $defaultlayout = "tickets";
        } else
            $defaultlayout = "myticket";
        $layout = JSSTrequest::getLayout('jstlay', null, $defaultlayout);
        if (self::canaddfile()) {
            switch ($layout) {
                case 'admin_tickets':
                    $list = null;
                    if (isset($_GET['list']))
                        $list = $_GET['list'];
                    JSSTincluder::getJSModel('ticket')->getTicketsForAdmin($list);
                    break;
                case 'admin_addticket':
                case 'addticket':
                    $id = JSSTrequest::getVar('jssupportticketid');
                    JSSTincluder::getJSModel('ticket')->getTicketsForForm($id);
                    break;
                case 'admin_ticketdetail':
                case 'ticketdetail':
                    $id = JSSTrequest::getVar('jssupportticketid');
                    JSSTincluder::getJSModel('ticket')->getTicketForDetail($id);
                    break;
                case 'myticket':
                    JSSTincluder::getJSModel('ticket')->getMyTickets();
                    break;
            }
            $module = (is_admin()) ? 'page' : 'jstmod';
            $module = JSSTrequest::getVar($module, null, 'ticket');
            JSSTincluder::include_file($layout, $module);
        }
    }

    function canaddfile() {
        if (isset($_POST['form_request']) && $_POST['form_request'] == 'jssupportticket')
            return false;
        elseif (isset($_GET['action']) && $_GET['action'] == 'jstask')
            return false;
        else
            return true;
    }

    function closeticket() {
        $id = JSSTrequest::getVar('ticketid');
        JSSTincluder::getJSModel('ticket')->closeTicket($id);
        if (is_admin()) {
            $url = admin_url("admin.php?page=ticket&jstlay=tickets");
        } else {
            $url = site_url("?page_id=" . jssupportticket::getPageid() . "&jstmod=ticket&jstlay=ticketdetail&jssupportticketid=".$id);
        }
        wp_redirect($url);
        exit;
    }

    static function saveticket() {
        $data = JSSTrequest::get('post');
        $result = JSSTincluder::getJSModel('ticket')->storeTickets($data);
        if (is_admin()) {
            if ($result == false) {
                $url = admin_url("admin.php?page=ticket&jstlay=addticket");
            } else {
                $url = admin_url("admin.php?page=ticket&jstlay=tickets");
            }
        } else {
            if ($result == 'captcha_failed' || $result == false) { // error on captcha or ticket validation
                $addticket = 'addticket';
                $url = site_url("?page_id=" . jssupportticket::getPageid() . "&jstmod=ticket&jstlay=$addticket");
            } else {
                if(get_current_user_id() != 0){
                    $myticket = 'myticket';
                    $url = site_url("?page_id=" . jssupportticket::getPageid() . "&jstmod=ticket&jstlay=$myticket");
                }else{
                    $url = site_url("?page_id=" . jssupportticket::getPageid());
                }
            }
        }
        if ($result == false) {
            JSSTformfield::setFormData($data);
        }
        wp_redirect($url);
        exit;
    }

    static function deleteticket() {
        $id = JSSTrequest::getVar('ticketid');
        JSSTincluder::getJSModel('ticket')->removeTicket($id);
        if (is_admin()) {
            $url = admin_url("admin.php?page=ticket&jstlay=tickets");
        } else {
            $url = site_url("?page_id=" . jssupportticket::getPageid() . "&jstmod=ticket&jstlay=myticket");
        }
        wp_redirect($url);
        exit;
    }

    static function enforcedeleteticket() {
        $id = JSSTrequest::getVar('ticketid');
        JSSTincluder::getJSModel('ticket')->removeEnforceTicket($id);
        if (is_admin()) {
            $url = admin_url("admin.php?page=ticket&jstlay=tickets");
        } else {
            $url = site_url("?page_id=" . jssupportticket::getPageid() . "&jstmod=ticket&jstlay=myticket");
        }
        wp_redirect($url);
        exit;
    }

    static function changepriority() {
        $id = JSSTrequest::getVar('ticketid');
        $priorityid = JSSTrequest::getVar('priority');
        JSSTincluder::getJSModel('ticket')->changeTicketPriority($id, $priorityid);
        if (is_admin()) {
            $url = admin_url("admin.php?page=ticket&jstlay=ticketdetail&jssupportticketid=" . $id);
        } else {
            $url = site_url("?page_id=" . jssupportticket::getPageid() . "&jstmod=ticket&jstlay=ticketdetail&jssupportticketid=" . $id);
        }
        wp_redirect($url);
        exit;
    }

    static function reopenticket() { // for user
        $ticketid = JSSTrequest::getVar('ticketid');
        $data['ticketid'] = $ticketid;
        JSSTincluder::getJSModel('ticket')->reopenTicket($data);
        $url = "&jstlay=ticketdetail&jssupportticketid=" . $data['ticketid'];
        if (is_admin()) {
            $url = admin_url("admin.php?page=ticket" . $url);
        } else {
            $url = site_url("?page_id=" . jssupportticket::getPageid() . "&jstmod=ticket" . $url);
        }
        wp_redirect($url);
        exit;
    }

    static function actionticket() {
        $data = JSSTrequest::get('post');
        /* to handle actions */
        switch ($data['actionid']) {
            case 1: /* Change Priority Ticket */
                JSSTincluder::getJSModel('ticket')->changeTicketPriority($data['ticketid'], $data['priority']);
                $url = "&jstlay=ticketdetail&jssupportticketid=" . $data['ticketid'];
                break;
            case 2: /* close ticket */
                JSSTincluder::getJSModel('ticket')->closeTicket($data['ticketid']);
                $url = "&jstlay=ticketdetail&jssupportticketid=" . $data['ticketid'];
                break;
            case 3: /* Reopen Ticket */
                JSSTincluder::getJSModel('ticket')->reopenTicket($data);
                $url = "&jstlay=ticketdetail&jssupportticketid=" . $data['ticketid'];
                break;
        }

        if (is_admin()) {
            $url = admin_url("admin.php?page=ticket" . $url);
        } else {
            $url = site_url("?page_id=" . jssupportticket::getPageid() . "&jstmod=ticket" . $url);
        }
        wp_redirect($url);
        exit;
    }
    
    static function showticketstatus() {
        $token = JSSTrequest::getVar('token');
        if ($token == null) { // in case it come from ticket status form
            $emailaddress = JSSTrequest::getVar('email');
            $trackingid = JSSTrequest::getVar('ticketid');
            $token = JSSTincluder::getJSModel('ticket')->createTokenByEmailAndTrackingId($emailaddress, $trackingid);
        }
        $_SESSION['js-support-ticket']['token'] = $token;
        $ticketid = JSSTincluder::getJSModel('ticket')->getTicketidForVisitor($token);
        if ($ticketid) {
            $url = site_url("?page_id=" . jssupportticket::getPageid() . "&jstmod=ticket&jstlay=ticketdetail&jssupportticketid=" . $ticketid);
        } else {
            $url = site_url("?page_id=" . jssupportticket::getPageid() . "&jstmod=ticket&jstlay=ticketstatus");
            JSSTmessage::setMessage(__('Record not found', 'js-support-ticket'), 'error');
        }
        wp_redirect($url);
        exit;
    }

    function downloadbyid(){
        $id = JSSTrequest::getVar('id');
        JSSTincluder::getJSModel('attachment')->getDownloadAttachmentById($id);
    }

    function downloadbyname(){
        $name = JSSTrequest::getVar('name');
        $id = JSSTrequest::getVar('id');
        JSSTincluder::getJSModel('attachment')->getDownloadAttachmentByName($name,$id);
    }
}

$ticketController = new JSSTticketController();
?>
