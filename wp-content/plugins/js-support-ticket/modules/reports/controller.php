<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class JSSTreportsController {

    function __construct() {
        self::handleRequest();
    }

    function handleRequest() {
        $layout = JSSTrequest::getLayout('jstlay', null, 'reports');
        if (self::canaddfile()) {
            switch ($layout) {
                case 'admin_reports':
                    break;
                case 'admin_staffreport':
                    JSSTincluder::getJSModel('reports')->getStaffReports();
                    break;
                case 'admin_userreport':
                    JSSTincluder::getJSModel('reports')->getUserReports();
                    break;
                case 'admin_staffdetailreport':
                    $id = JSSTrequest::getVar('id');
                    JSSTincluder::getJSModel('reports')->getStaffDetailReportByStaffId($id);
                    break;
                case 'admin_userdetailreport':
                    $id = JSSTrequest::getVar('id');
                    JSSTincluder::getJSModel('reports')->getStaffDetailReportByUserId($id);
                    break;
                case 'admin_overallreport':
                    JSSTincluder::getJSModel('reports')->getOverallReportData();
                    break;
            }
            $module = (is_admin()) ? 'page' : 'jstmod';
            $module = JSSTrequest::getVar($module, null, 'faq');
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

}

$reportsController = new JSSTreportsController();
?>
