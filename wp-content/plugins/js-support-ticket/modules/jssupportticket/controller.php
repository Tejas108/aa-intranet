<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class JSSTjssupportticketController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $layout = JSSTrequest::getLayout('jstlay', null, 'controlpanel');
        if (self::canaddfile()) {
            switch ($layout) {
                case 'admin_controlpanel':
                    include_once jssupportticket::$_path . 'includes/updates/updates.php';
                    JSSTupdates::checkUpdates();
                    JSSTincluder::getJSModel('jssupportticket')->getControlPanelData();
                    break;
                case 'controlpanel':
                    JSSTincluder::getJSModel('jssupportticket')->getControlPanelData();
                    break;
                case 'admin_gettingstart':
                    JSSTincluder::getJSModel('jssupportticket')->gettingStartData();
                    break;
            }
            $module = (is_admin()) ? 'page' : 'jstmod';
            $module = JSSTrequest::getVar($module, null, 'jssupportticket');
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

    static function addpageslug(){
        $data = JSSTrequest::get('post');
        JSSTincluder::getJSModel('jssupportticket')->addPageSlug($data);
        $url = admin_url("admin.php?page=jssupportticket&jstlay=gettingstart");
        wp_redirect($url);
        exit;
    }

}

$controlpanelController = new JSSTjssupportticketController();
?>
