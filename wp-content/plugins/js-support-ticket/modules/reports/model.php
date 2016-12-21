<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class JSSTreportsModel {

    function getOverallReportData() {

        //Overall Data by status
        $query = "SELECT COUNT(id) FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` WHERE status = 0 AND (lastreply = '0000-00-00 00:00:00' OR lastreply = '')";
        $openticket = jssupportticket::$_db->get_var($query);

        $query = "SELECT COUNT(id) FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` WHERE status = 4";
        $closeticket = jssupportticket::$_db->get_var($query);

        $query = "SELECT COUNT(id) FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0";
        $answeredticket = jssupportticket::$_db->get_var($query);

        $query = "SELECT COUNT(id) FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` WHERE isanswered != 1 AND status != 4 AND (lastreply != '0000-00-00 00:00:00' AND lastreply != '')";
        $pendingticket = jssupportticket::$_db->get_var($query);

        jssupportticket::$_data['status_chart'] = "['" . __('New', 'js-support-ticket') . "',$openticket],['" . __('Answered', 'js-support-ticket') . "',$answeredticket],['" . __('Pending', 'js-support-ticket') . "',$pendingticket]";
        $total = $openticket + $closeticket + $answeredticket + $pendingticket;
        jssupportticket::$_data['bar_chart'] = "
        ['" . __('New', 'js-support-ticket') . "',$openticket,'#FF9900'],
        ['" . __('Answered', 'js-support-ticket') . "',$answeredticket,'#179650'],
        ['" . __('Closed', 'js-support-ticket') . "',$closeticket,'#5F3BBB'],
        ['" . __('Pending', 'js-support-ticket') . "',$pendingticket,'#D98E11'],
        ";

        $query = "SELECT dept.departmentname,(SELECT COUNT(id) FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` WHERE departmentid = dept.id) AS totalticket
                    FROM `" . jssupportticket::$_db->prefix . "js_ticket_departments` AS dept";
        $department = jssupportticket::$_db->get_results($query);
        jssupportticket::$_data['pie3d_chart1'] = "";
        foreach ($department AS $dept) {
            jssupportticket::$_data['pie3d_chart1'] .= "['$dept->departmentname',$dept->totalticket],";
        }

        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` WHERE priorityid = priority.id) AS totalticket
                    FROM `" . jssupportticket::$_db->prefix . "js_ticket_priorities` AS priority";
        $department = jssupportticket::$_db->get_results($query);
        jssupportticket::$_data['pie3d_chart2'] = "";
        foreach ($department AS $dept) {
            jssupportticket::$_data['pie3d_chart2'] .= "['$dept->priority',$dept->totalticket],";
        }

        $query = "SELECT COUNT(id) FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` WHERE ticketviaemail = 1";
        $ticketviaemail = jssupportticket::$_db->get_var($query);
        $query = "SELECT COUNT(id) FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` WHERE ticketviaemail = 0";
        $directticket = jssupportticket::$_db->get_var($query);
        $query = "SELECT COUNT(id) FROM `" . jssupportticket::$_db->prefix . "js_ticket_replies` WHERE ticketviaemail = 1";
        $replyviaemail = jssupportticket::$_db->get_var($query);
        $query = "SELECT COUNT(id) FROM `" . jssupportticket::$_db->prefix . "js_ticket_replies` WHERE ticketviaemail = 0";
        $directreply = jssupportticket::$_db->get_var($query);

        jssupportticket::$_data['stack_data'] = "['" . __('Tickets', 'js-support-ticket') . "',$directticket,$ticketviaemail,''],['" . __('Replies', 'js-support-ticket') . "',$directreply,$replyviaemail,'']";

        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` WHERE priorityid = priority.id AND status = 0 AND (lastreply = '0000-00-00 00:00:00' OR lastreply = '') ) AS totalticket
                    FROM `" . jssupportticket::$_db->prefix . "js_ticket_priorities` AS priority ORDER BY priority.priority";
        $openticket_pr = jssupportticket::$_db->get_results($query);
        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` WHERE priorityid = priority.id AND isanswered = 1 AND status != 4 AND status != 0 ) AS totalticket
                    FROM `" . jssupportticket::$_db->prefix . "js_ticket_priorities` AS priority ORDER BY priority.priority";
        $answeredticket_pr = jssupportticket::$_db->get_results($query);
        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` WHERE priorityid = priority.id AND isanswered != 1 AND status != 4 AND (lastreply != '0000-00-00 00:00:00' AND lastreply != '') ) AS totalticket
                    FROM `" . jssupportticket::$_db->prefix . "js_ticket_priorities` AS priority ORDER BY priority.priority";
        $pendingticket_pr = jssupportticket::$_db->get_results($query);
        jssupportticket::$_data['stack_chart_horizontal']['title'] = "['" . __('Tickets', 'js-support-ticket') . "',";
        jssupportticket::$_data['stack_chart_horizontal']['data'] = "['" . __('Pending', 'js-support-ticket') . "',";

        foreach ($pendingticket_pr AS $pr) {
            jssupportticket::$_data['stack_chart_horizontal']['title'] .= "'" . $pr->priority . "',";
            jssupportticket::$_data['stack_chart_horizontal']['data'] .= $pr->totalticket . ",";
        }
        jssupportticket::$_data['stack_chart_horizontal']['title'] .= "]";
        jssupportticket::$_data['stack_chart_horizontal']['data'] .= "],['" . __('Answered', 'js-support-ticket') . "',";

        foreach ($answeredticket_pr AS $pr) {
            jssupportticket::$_data['stack_chart_horizontal']['data'] .= $pr->totalticket . ",";
        }

        jssupportticket::$_data['stack_chart_horizontal']['data'] .= "],['" . __('New', 'js-support-ticket') . "',";

        foreach ($openticket_pr AS $pr) {
            jssupportticket::$_data['stack_chart_horizontal']['data'] .= $pr->totalticket . ",";
        }

        jssupportticket::$_data['stack_chart_horizontal']['data'] .= "]";
    }

}

?>
