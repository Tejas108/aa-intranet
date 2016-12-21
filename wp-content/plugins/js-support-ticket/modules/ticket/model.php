<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class JSSTticketModel {

    function getTicketsForAdmin($list) {
        $this->getOrdering();
        // Filter
        $subject = trim(JSSTrequest::getVar('subject'));
        $name = trim(JSSTrequest::getVar('name'));
        $email = trim(JSSTrequest::getVar('email'));
        $ticketid = trim(JSSTrequest::getVar('ticketid'));
        $departmentid = trim(JSSTrequest::getVar('departmentid'));
        $priorityid = trim(JSSTrequest::getVar('priorityid'));
        $datestart = trim(JSSTrequest::getVar('datestart'));
        $dateend = trim(JSSTrequest::getVar('dateend'));
        $inquery = '';
        switch ($list) {
            // Ticket Default Status
            // 0 -> New Ticket
            // 1 -> Waiting admin/staff reply
            // 3 -> waiting for customer reply
            // 4 -> close ticket
            case 1:$inquery .= " AND ticket.status != 4 ";
                break;
            case 2:$inquery .= " AND ticket.isanswered = 1 ";
                break;
            case 4:$inquery .= " AND ticket.status = 4 ";
                break;
            case 5://$inquery .= " AND ticket.uid =" . get_current_user_id();
                break;
        }
        $data = JSSTincluder::getObjectClass('customfields')->userFieldsForSearch(1);
        $valarray = array();
        if (!empty($data)) {
            foreach ($data as $uf) {
                $valarray[$uf->field] = JSSTrequest::getVar($uf->field, 'post');
                if (isset($valarray[$uf->field]) && $valarray[$uf->field] != null) {
                    switch ($uf->userfieldtype) {
                        case 'text':
                            $inquery .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*' . htmlspecialchars($valarray[$uf->field]) . '.*"\' ';
                            break;
                        case 'combo':
                            $inquery .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                            break;
                        case 'depandant_field':
                            $inquery .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                            break;
                        case 'radio':
                            $inquery .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                            break;
                        case 'checkbox':
                            $finalvalue = '';
                            foreach($valarray[$uf->field] AS $value){
                                $finalvalue .= $value.'.*';
                            }
                            $inquery .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*' . htmlspecialchars($finalvalue) . '.*"\' ';
                            break;
                        case 'date':
                            $inquery .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                            break;
                        case 'textarea':
                            $inquery .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*' . htmlspecialchars($valarray[$uf->field]) . '.*"\' ';
                            break;
                        case 'multiple':
                            $finalvalue = '';
                            foreach($valarray[$uf->field] AS $value){
                                if($value != null){
                                    $finalvalue .= $value.'.*';
                                }    
                            }
                            if($finalvalue !=''){
                                $inquery .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"].*'.htmlspecialchars($finalvalue).'.*"\'';
                            }
                            break;
                    }
                    jssupportticket::$_data['filter']['params'] = $valarray;
                }
            }
        }
        //end
        $formsearch = JSSTrequest::getVar('JSST_form_search', 'post');
        if ($formsearch == 'JSST_SEARCH') {
            $_SESSION['JSST_SEARCH']['subject'] = $subject;
            $_SESSION['JSST_SEARCH']['name'] = $name;
            $_SESSION['JSST_SEARCH']['email'] = $email;
            $_SESSION['JSST_SEARCH']['ticketid'] = $ticketid;
            $_SESSION['JSST_SEARCH']['departmentid'] = $departmentid;
            $_SESSION['JSST_SEARCH']['priorityid'] = $priorityid;
            $_SESSION['JSST_SEARCH']['datestart'] = $datestart;
            $_SESSION['JSST_SEARCH']['dateend'] = $dateend;
        }

        if (JSSTrequest::getVar('pagenum', 'get', null) != null) {
            $subject = (isset($_SESSION['JSST_SEARCH']['subject']) && $_SESSION['JSST_SEARCH']['subject'] != '') ? $_SESSION['JSST_SEARCH']['subject'] : null;
            $name = (isset($_SESSION['JSST_SEARCH']['name']) && $_SESSION['JSST_SEARCH']['name'] != '') ? $_SESSION['JSST_SEARCH']['name'] : null;
            $email = (isset($_SESSION['JSST_SEARCH']['email']) && $_SESSION['JSST_SEARCH']['email'] != '') ? $_SESSION['JSST_SEARCH']['email'] : null;
            $ticketid = (isset($_SESSION['JSST_SEARCH']['ticketid']) && $_SESSION['JSST_SEARCH']['ticketid'] != '') ? $_SESSION['JSST_SEARCH']['ticketid'] : null;
            $departmentid = (isset($_SESSION['JSST_SEARCH']['departmentid']) && $_SESSION['JSST_SEARCH']['departmentid'] != '') ? $_SESSION['JSST_SEARCH']['departmentid'] : null;
            $priorityid = (isset($_SESSION['JSST_SEARCH']['priorityid']) && $_SESSION['JSST_SEARCH']['priorityid'] != '') ? $_SESSION['JSST_SEARCH']['priorityid'] : null;
            $datestart = (isset($_SESSION['JSST_SEARCH']['datestart']) && $_SESSION['JSST_SEARCH']['datestart'] != '') ? $_SESSION['JSST_SEARCH']['datestart'] : null;
            $dateend = (isset($_SESSION['JSST_SEARCH']['dateend']) && $_SESSION['JSST_SEARCH']['dateend'] != '') ? $_SESSION['JSST_SEARCH']['dateend'] : null;
        }

        if ($ticketid != null)
            $inquery .= " AND ticket.ticketid LIKE '%$ticketid%'";
        if ($subject != null)
            $inquery .= " AND ticket.subject LIKE '%$subject%'";
        if ($name != null)
            $inquery .= " AND ticket.name LIKE '%$name%'";
        if ($email != null)
            $inquery .= " AND ticket.email LIKE '%$email%'";
        if ($departmentid != null)
            $inquery .= " AND ticket.departmentid = $departmentid";
        if ($priorityid != null)
            $inquery .= " AND ticket.priorityid = $priorityid";
        if ($datestart != null)
            $inquery .= " AND DATE(ticket.created) >= '$datestart'";
        if ($dateend != null)
            $inquery .= " AND DATE(ticket.created) <= '$dateend'";

        jssupportticket::$_data['filter']['subject'] = $subject;
        jssupportticket::$_data['filter']['ticketid'] = $ticketid;
        jssupportticket::$_data['filter']['name'] = $name;
        jssupportticket::$_data['filter']['email'] = $email;
        jssupportticket::$_data['filter']['departmentid'] = $departmentid;
        jssupportticket::$_data['filter']['priorityid'] = $priorityid;
        jssupportticket::$_data['filter']['datestart'] = $datestart;
        jssupportticket::$_data['filter']['dateend'] = $dateend;

        // Pagination
        $query = "SELECT COUNT(ticket.id) "
                . "FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` AS ticket "
                . "LEFT JOIN `" . jssupportticket::$_db->prefix . "js_ticket_departments` AS department ON ticket.departmentid = department.id "
                . "JOIN `" . jssupportticket::$_db->prefix . "js_ticket_priorities` AS priority ON ticket.priorityid = priority.id "
                . "WHERE 1 = 1";
        $query .= $inquery;
        $total = jssupportticket::$_db->get_var($query);
        jssupportticket::$_data[1] = JSSTpagination::getPagination($total);

        /*
          list variable detail
          1=>For open ticket
          2=>For answered  ticket
          4=>For Closed tickets
          5=>For mytickets tickets
         */
        jssupportticket::$_data['list'] = $list; // assign for reference
        // Data
        $query = "SELECT ticket.*,department.departmentname AS departmentname ,priority.priority AS priority,priority.prioritycolour AS prioritycolour
                    FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` AS ticket
                    LEFT JOIN `" . jssupportticket::$_db->prefix . "js_ticket_departments` AS department ON ticket.departmentid = department.id
                    JOIN `" . jssupportticket::$_db->prefix . "js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                    WHERE 1 = 1";
        $query .= $inquery;
        $query .= " ORDER BY " . jssupportticket::$_ordering . " LIMIT " . JSSTpagination::getOffset() . ", " . JSSTpagination::getLimit();
        jssupportticket::$_data[0] = jssupportticket::$_db->get_results($query);
        // check email is bane 
        //Hook action
        do_action('jsst-ticketbeforelisting', jssupportticket::$_data[0]);
        if (jssupportticket::$_db->last_error != null) {
            JSSTincluder::getJSModel('systemerror')->addSystemError();
        }
        if (jssupportticket::$_config['count_on_myticket'] == 1) {
            $query = "SELECT COUNT(ticket.id) "
                    . "FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` AS ticket "
                    . "LEFT JOIN `" . jssupportticket::$_db->prefix . "js_ticket_departments` AS department ON ticket.departmentid = department.id "
                    . "JOIN `" . jssupportticket::$_db->prefix . "js_ticket_priorities` AS priority ON ticket.priorityid = priority.id "
                    . "WHERE ticket.status != 4";
            jssupportticket::$_data['count']['openticket'] = jssupportticket::$_db->get_var($query);

            $query = "SELECT COUNT(ticket.id) "
                    . "FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` AS ticket "
                    . "LEFT JOIN `" . jssupportticket::$_db->prefix . "js_ticket_departments` AS department ON ticket.departmentid = department.id "
                    . "JOIN `" . jssupportticket::$_db->prefix . "js_ticket_priorities` AS priority ON ticket.priorityid = priority.id "
                    . "WHERE ticket.isanswered = 1";
            jssupportticket::$_data['count']['answeredticket'] = jssupportticket::$_db->get_var($query);

            $query = "SELECT COUNT(ticket.id) "
                    . "FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` AS ticket "
                    . "LEFT JOIN `" . jssupportticket::$_db->prefix . "js_ticket_departments` AS department ON ticket.departmentid = department.id "
                    . "JOIN `" . jssupportticket::$_db->prefix . "js_ticket_priorities` AS priority ON ticket.priorityid = priority.id "
                    . "WHERE ticket.isoverdue = 1";
            jssupportticket::$_data['count']['overdueticket'] = jssupportticket::$_db->get_var($query);

            $query = "SELECT COUNT(ticket.id) "
                    . "FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` AS ticket "
                    . "LEFT JOIN `" . jssupportticket::$_db->prefix . "js_ticket_departments` AS department ON ticket.departmentid = department.id "
                    . "JOIN `" . jssupportticket::$_db->prefix . "js_ticket_priorities` AS priority ON ticket.priorityid = priority.id "
                    . "WHERE ticket.status = 4";
            jssupportticket::$_data['count']['closedticket'] = jssupportticket::$_db->get_var($query);

            $query = "SELECT COUNT(ticket.id) "
                    . "FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` AS ticket "
                    . "LEFT JOIN `" . jssupportticket::$_db->prefix . "js_ticket_departments` AS department ON ticket.departmentid = department.id "
                    . "JOIN `" . jssupportticket::$_db->prefix . "js_ticket_priorities` AS priority ON ticket.priorityid = priority.id "
                    . "WHERE 1 = 1";
            jssupportticket::$_data['count']['allticket'] = jssupportticket::$_db->get_var($query);
        }
        return;
    }

    function getOrdering() {
        $sort = JSSTrequest::getVar('sortby', '');
        if ($sort == '') {
            $list = JSSTrequest::getVar('list', null, 1);
            if ($list == 1)
                $sort = 'statusasc';
            elseif ($list == 2)
                $sort = 'createddesc';
            elseif ($list == 3)
                $sort = 'statusasc';
            elseif ($list == 4)
                $sort = 'createddesc';
            elseif ($list == 5)
                $sort = 'statusasc';
        }
        $this->getTicketListOrdering($sort);
        $this->getTicketListSorting($sort);
    }

    function combineOrSingleSearch() {
        $ticketkeys = trim(JSSTrequest::getVar('jsst-ticketsearchkeys', 'post'));
        $inquery = '';
        if ($ticketkeys) {
            if (strlen($ticketkeys) == 9)
                $inquery = " AND ticket.ticketid = '$ticketkeys'";
            else if (strpos($ticketkeys, '@') && strpos($ticketkeys, '.'))
                $inquery = " AND ticket.email LIKE '%$ticketkeys%'";
            else
                $inquery = " AND ticket.subject LIKE '%$ticketkeys%'";

            jssupportticket::$_data['filter']['ticketsearchkeys'] = $ticketkeys;
            jssupportticket::$_data['filter']['combinesearch'] = false;
        }
        else {
            $ticketid = JSSTrequest::getVar('jsst-ticket', 'post');
            $from = JSSTrequest::getVar('jsst-from', 'post');
            $email = JSSTrequest::getVar('jsst-email', 'post');
            $departmentid = JSSTrequest::getVar('jsst-departmentid', 'post');
            $priorityid = JSSTrequest::getVar('jsst-priorityid', 'post');
            $subject = JSSTrequest::getVar('jsst-subject', 'post');
            $datestart = JSSTrequest::getVar('jsst-datestart', 'post');
            $dateend = JSSTrequest::getVar('jsst-dateend', 'post');

            if ($ticketid != null) {
                $inquery .= " AND ticket.ticketid LIKE '$ticketid'";
                jssupportticket::$_data['filter']['ticketid'] = $ticketid;
            }
            if ($from != null) {
                $inquery .= " AND ticket.name LIKE '%$from%'";
                jssupportticket::$_data['filter']['from'] = $from;
            }
            if ($email != null) {
                $inquery .= " AND ticket.email LIKE '$email'";
                jssupportticket::$_data['filter']['email'] = $email;
            }
            if ($departmentid != null) {
                $inquery .= " AND ticket.departmentid LIKE '$departmentid'";
                jssupportticket::$_data['filter']['departmentid'] = $departmentid;
            }
            if ($priorityid != null) {
                $inquery .= " AND ticket.priorityid LIKE '$priorityid'";
                jssupportticket::$_data['filter']['priorityid'] = $priorityid;
            }
            if ($subject != null) {
                $inquery .= " AND ticket.subject LIKE '%$subject%'";
                jssupportticket::$_data['filter']['subject'] = $subject;
            }
            if ($datestart != null) {
                $inquery .= " AND '$datestart' <= DATE(ticket.created)";
                jssupportticket::$_data['filter']['datestart'] = $datestart;
            }
            if ($dateend != null) {
                $inquery .= " AND '$dateend' >= DATE(ticket.created)";
                jssupportticket::$_data['filter']['dateend'] = $dateend;
            }
            $data = JSSTincluder::getObjectClass('customfields')->userFieldsForSearch(1);
            $valarray = array();
            if (!empty($data)) {
                foreach ($data as $uf) {
                    $valarray[$uf->field] = JSSTrequest::getVar($uf->field, 'post');
                    if (isset($valarray[$uf->field]) && $valarray[$uf->field] != null) {
                        switch ($uf->userfieldtype) {
                            case 'text':
                                $inquery .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*' . htmlspecialchars($valarray[$uf->field]) . '.*"\' ';
                                break;
                            case 'combo':
                                $inquery .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                                break;
                            case 'depandant_field':
                                $inquery .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                                break;
                            case 'radio':
                                $inquery .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                                break;
                            case 'checkbox':
                                $finalvalue = '';
                                foreach($valarray[$uf->field] AS $value){
                                    $finalvalue .= $value.'.*';
                                }
                                $inquery .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*' . htmlspecialchars($finalvalue) . '.*"\' ';
                                break;
                            case 'date':
                                $inquery .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                                break;
                            case 'textarea':
                                $inquery .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*' . htmlspecialchars($valarray[$uf->field]) . '.*"\' ';
                                break;
                            case 'multiple':
                                $finalvalue = '';
                                foreach($valarray[$uf->field] AS $value){
                                    if($value != null){
                                        $finalvalue .= $value.'.*';
                                    }    
                                }
                                if($finalvalue !=''){
                                    $inquery .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"].*'.htmlspecialchars($finalvalue).'.*"\'';
                                }
                                break;
                        }
                        jssupportticket::$_data['filter']['params'] = $valarray;
                    }
                }
            }
            //end
            
            if ($inquery != '')
                jssupportticket::$_data['filter']['combinesearch'] = true;
            else
                jssupportticket::$_data['filter']['combinesearch'] = false;
        }
        return $inquery;
    }

    function getMyTickets() {
        $this->getOrdering();
        // Filter
        /*
          list variable detail
          1=>For open ticket
          2=>For closed ticket
          3=>For open answered ticket
          4=>For all my tickets
         */

        $inquery = $this->combineOrSingleSearch();

        $list = JSSTrequest::getVar('list', null, 1);
        jssupportticket::$_data['list'] = $list; // assign for reference
        switch ($list) {
            // Ticket Default Status
            // 0 -> New Ticket
            // 1 -> Waiting admin/staff reply
            // 3 -> waiting for customer reply
            // 4 -> close ticket
            case 1:$inquery .= " AND ticket.status != 4 ";
                break;
            case 2:$inquery .= " AND ticket.status = 4 ";
                break;
            case 3:$inquery .= " AND ticket.status = 3 ";
                break;
            case 4:$inquery .= " ";
                break;
        }

        $uid = get_current_user_id();
        if ($uid) {
            // Pagination
            $query = "SELECT COUNT(ticket.id) 
                        FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` AS ticket 
                        LEFT JOIN `" . jssupportticket::$_db->prefix . "js_ticket_departments` AS department ON ticket.departmentid = department.id
                        JOIN `" . jssupportticket::$_db->prefix . "js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                        WHERE ticket.uid = $uid ";
            $query .= $inquery;
            $total = jssupportticket::$_db->get_var($query);
            jssupportticket::$_data[1] = JSSTpagination::getPagination($total);

            // Data
            $query = "SELECT ticket.*,department.departmentname AS departmentname ,priority.priority AS priority,priority.prioritycolour AS prioritycolour
                        FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` AS ticket
                        LEFT JOIN `" . jssupportticket::$_db->prefix . "js_ticket_departments` AS department ON ticket.departmentid = department.id
                        JOIN `" . jssupportticket::$_db->prefix . "js_ticket_priorities` AS priority ON ticket.priorityid = priority.id";
            $query .= " WHERE ticket.uid = $uid " . $inquery;
            $query .= " ORDER BY " . jssupportticket::$_ordering . " LIMIT " . JSSTpagination::getOffset() . ", " . JSSTpagination::getLimit();
            jssupportticket::$_data[0] = jssupportticket::$_db->get_results($query);
            if (jssupportticket::$_db->last_error != null) {
                JSSTincluder::getJSModel('systemerror')->addSystemError();
            }
            if (jssupportticket::$_config['count_on_myticket'] == 1) {
                $query = "SELECT COUNT(ticket.id) "
                        . "FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` AS ticket "
                        . "LEFT JOIN `" . jssupportticket::$_db->prefix . "js_ticket_departments` AS department ON ticket.departmentid = department.id "
                        . "JOIN `" . jssupportticket::$_db->prefix . "js_ticket_priorities` AS priority ON ticket.priorityid = priority.id "
                        . "WHERE ticket.uid = $uid AND ticket.status != 4";
                jssupportticket::$_data['count']['openticket'] = jssupportticket::$_db->get_var($query);
                ;

                $query = "SELECT COUNT(ticket.id) "
                        . "FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` AS ticket "
                        . "LEFT JOIN `" . jssupportticket::$_db->prefix . "js_ticket_departments` AS department ON ticket.departmentid = department.id "
                        . "JOIN `" . jssupportticket::$_db->prefix . "js_ticket_priorities` AS priority ON ticket.priorityid = priority.id "
                        . "WHERE ticket.uid = $uid AND ticket.status = 3";
                jssupportticket::$_data['count']['answeredticket'] = jssupportticket::$_db->get_var($query);
                ;

                $query = "SELECT COUNT(ticket.id) "
                        . "FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` AS ticket "
                        . "LEFT JOIN `" . jssupportticket::$_db->prefix . "js_ticket_departments` AS department ON ticket.departmentid = department.id "
                        . "JOIN `" . jssupportticket::$_db->prefix . "js_ticket_priorities` AS priority ON ticket.priorityid = priority.id "
                        . "WHERE ticket.uid = $uid AND ticket.status = 4";
                jssupportticket::$_data['count']['closedticket'] = jssupportticket::$_db->get_var($query);
                ;

                $query = "SELECT COUNT(ticket.id) "
                        . "FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` AS ticket "
                        . "LEFT JOIN `" . jssupportticket::$_db->prefix . "js_ticket_departments` AS department ON ticket.departmentid = department.id "
                        . "JOIN `" . jssupportticket::$_db->prefix . "js_ticket_priorities` AS priority ON ticket.priorityid = priority.id "
                        . "WHERE ticket.uid = $uid";
                jssupportticket::$_data['count']['allticket'] = jssupportticket::$_db->get_var($query);
                ;
            }
        }
        return;
    }

    function getTicketsForForm($id) {
        if ($id) {
            if (!is_numeric($id))
                return false;
            $query = "SELECT ticket.*,department.departmentname AS departmentname ,priority.priority AS priority,priority.prioritycolour AS prioritycolour,user.user_nicename AS user_login
                        FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` AS ticket
                        LEFT JOIN `" . jssupportticket::$_db->prefix . "js_ticket_departments` AS department ON ticket.departmentid = department.id
                        JOIN `" . jssupportticket::$_db->prefix . "js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                        LEFT JOIN `".jssupportticket::$_wpprefixforuser."users` AS user ON user.id = ticket.uid 
                        WHERE ticket.id = " . $id;
            jssupportticket::$_data[0] = jssupportticket::$_db->get_row($query);
            if (jssupportticket::$_db->last_error != null) {
                JSSTincluder::getJSModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            }
        }
        JSSTincluder::getJSModel('attachment')->getAttachmentForForm($id);
        JSSTincluder::getJSModel('fieldordering')->getFieldsOrderingforForm(1);
        return;
    }

    function getTicketForDetail($id) {
        if (!is_numeric($id))
            return $id;

        if (is_user_logged_in()) {
            if (is_admin())
                jssupportticket::$_data['permission_granted'] = true;
            else
                jssupportticket::$_data['permission_granted'] = $this->validateTicketDetailForUser($id);
        }else{
            jssupportticket::$_data['permission_granted'] = $this->validateTicketDetailForVisitor($id);
        }
        if (!jssupportticket::$_data['permission_granted']) { // validation failed
            return;
        }

        $query = "SELECT ticket.*,department.departmentname AS departmentname ,priority.priority AS priority,priority.prioritycolour AS prioritycolour
                    FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` AS ticket
                    LEFT JOIN `" . jssupportticket::$_db->prefix . "js_ticket_departments` AS department ON ticket.departmentid = department.id
                    JOIN `" . jssupportticket::$_db->prefix . "js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                    WHERE ticket.id = " . $id;
        jssupportticket::$_data[0] = jssupportticket::$_db->get_row($query);
        if (jssupportticket::$_db->last_error != null) {
            JSSTincluder::getJSModel('systemerror')->addSystemError();
        }
        JSSTincluder::getJSModel('reply')->getReplies($id);
        jssupportticket::$_data['ticket_attachment'] = JSSTincluder::getJSModel('attachment')->getAttachmentForReply($id, 0);
        jssupportticket::$_data['custom']['ticketid'] = $id;
        //Hooks
        do_action('jsst-ticketbeforeview', jssupportticket::$_data);

        return;
    }

    function getRandomTicketId() {
        $query = "SELECT ticketid FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets`";
        $match = '';
        do {
            $ticketid = "";
            $length = 9;
            $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
            // we refer to the length of $possible a few times, so let's grab it now
            $maxlength = strlen($possible);
            if ($length > $maxlength) { // check for length overflow and truncate if necessary
                $length = $maxlength;
            }
            // set up a counter for how many characters are in the ticketid so far
            $i = 0;
            // add random characters to $password until $length is reached
            while ($i < $length) {
                // pick a random character from the possible ones
                $char = substr($possible, mt_rand(0, $maxlength - 1), 1);
                if (!strstr($ticketid, $char)) {
                    if ($i == 0) {
                        if (ctype_alpha($char)) {
                            $ticketid .= $char;
                            $i++;
                        }
                    } else {
                        $ticketid .= $char;
                        $i++;
                    }
                }
            }
            $rows = jssupportticket::$_db->get_results($query);
            foreach ($rows as $row) {
                if ($ticketid == $row->ticketid)
                    $match = 'Y';
                else
                    $match = 'N';
            }
        }while ($match == 'Y');
        return $ticketid;
    }

    function getRandomFolderName() {
        $foldername = "";
        $length = 7;
        $possible = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";
        // we refer to the length of $possible a few times, so let's grab it now
        $maxlength = strlen($possible);
        if ($length > $maxlength) { // check for length overflow and truncate if necessary
            $length = $maxlength;
        }
        // set up a counter for how many characters are in the ticketid so far
        $i = 0;
        // add random characters to $password until $length is reached
        while ($i < $length) {
            // pick a random character from the possible ones
            $char = substr($possible, mt_rand(0, $maxlength - 1), 1);
            if (!strstr($foldername, $char)) {
                if ($i == 0) {
                    if (ctype_alpha($char)) {
                        $foldername .= $char;
                        $i++;
                    }
                } else {
                    $foldername .= $char;
                    $i++;
                }
            }
        }
        return $foldername;
    }

    function getIpAddress() {
        //if client use the direct ip
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    function captchaValidate() {
        if (!is_user_logged_in()) {
            if (jssupportticket::$_config['show_captcha_on_visitor_from_ticket'] == 1) {
                if (jssupportticket::$_config['captcha_selection'] == 1) { // Google recaptcha
                    $gresponse = $_POST['g-recaptcha-response'];
                    $resp = googleRecaptchaHTTPPost(jssupportticket::$_config['recaptcha_privatekey'],$gresponse);
                    if ($resp == true) {
                        return true;
                    } else {
                        JSSTmessage::setMessage(__('Incorrect Captcha code', 'js-support-ticket'), 'error');
                        return false;
                    }
                } else { // own captcha
                    $captcha = new JSSTcaptcha;
                    $result = $captcha->checkCaptchaUserForm();
                    if ($result == 1) {
                        return true;
                    } else {
                        JSSTmessage::setMessage(__('Incorrect Captcha code', 'js-support-ticket'), 'error');
                        return false;
                    }
                }
            }
        }
		return true;
    }
    
    function storeTickets($data) {
        if (!is_admin()) { //if not admin
            if (!$this->captchaValidate()) {
                return 'captcha_failed';
            }
        }
        $sendEmail = true;
        if ($data['id']) {
            $sendEmail = false;
            $updated = date_i18n('Y-m-d H:i:s');
            $created = $data['created'];
        } else {
            $data['ticketid'] = $this->getRandomTicketId();
            $data['attachmentdir'] = $this->getRandomFolderName();
            $created = date_i18n('Y-m-d H:i:s');
            $updated = '';
        }
        $data['status'] = isset($data['status']) ? $data['status'] : '';
        $data['lastreply'] = isset($data['lastreply']) ? $data['lastreply'] : '';
        $data['message'] = wpautop(wptexturize(stripslashes($data['jsticket_message'])));
        if (empty($data['message'])) {
            JSSTmessage::setMessage(__('Message field cannot be empty', 'js-support-ticket'), 'error');
            return false;
        }
//custom field code start
        $customflagforadd = false;
        $customflagfordelete = false;
        $custom_field_namesforadd = array();
        $custom_field_namesfordelete = array();
        $userfield = JSSTincluder::getJSModel('fieldordering')->getUserfieldsfor(1);
        $params = '';
        foreach ($userfield AS $ufobj) {
            if($ufobj->userfieldtype == 'file'){
                if(isset($data[$ufobj->field.'_1']) && $data[$ufobj->field.'_1']== 0){
                    $vardata = $data[$ufobj->field.'_2'];
                }else{
                    $vardata = $_FILES[$ufobj->field]['name'];
                }
                $customflagforadd=true;
                $custom_field_namesforadd[]=$ufobj->field;
            }else{
                $vardata = isset($data[$ufobj->field]) ? $data[$ufobj->field] : '';
            }
            if($data[$ufobj->field.'_1'] == 1){
                $customflagfordelete = true;
                $custom_field_namesfordelete[]= $data[$ufobj->field.'_2'];
                }
            if($vardata != ''){
                //had to comment this so that multpli field should work properly
                // if($ufobj->userfieldtype == 'multiple'){
                //     $vardata = explode(',', $vardata[0]); // fixed index
                // }
                if(is_array($vardata)){
                    $vardata = implode(', ', $vardata);
                }
                $params[$ufobj->field] = htmlspecialchars($vardata);
            }
        }
        if (!empty($params)) {
            $params = json_encode($params);
        }
        $data['params'] = $params;
//custom field code end
        $query_array = array('id' => $data['id'],
            'email' => $data['email'],
            'name' => $data['name'],
            'uid' => $data['uid'],
            'phone' => $data['phone'],
            'phoneext' => $data['phoneext'],
            'departmentid' => $data['departmentid'],
            'priorityid' => $data['priorityid'],
            'subject' => $data['subject'],
            'message' => $data['message'],
            'status' => $data['status'],
            'duedate' => '',
            'attachmentdir' => $data['attachmentdir'],
            'lastreply' => $data['lastreply'],
            'created' => $created,
            'updated' => $updated,
            'params' => $data['params'],
            'ticketid' => $data['ticketid']
        );
        jssupportticket::$_db->replace(jssupportticket::$_db->prefix . 'js_ticket_tickets', $query_array);
        $ticketid = jssupportticket::$_db->insert_id; // get the ticket id
        if (jssupportticket::$_db->last_error != null) {
            JSSTincluder::getJSModel('systemerror')->addSystemError();
            $messagetype = __('Error', 'js-support-ticket');
            $sendEmail = false;
            JSSTmessage::setMessage(__('Ticket has not been stored', 'js-support-ticket'), 'error');
        } else {
            $messagetype = __('Successfully', 'js-support-ticket');
            
            // Storing Attachments
            $data['ticketid'] = $ticketid;
            JSSTincluder::getJSModel('attachment')->storeAttachments($data);
            JSSTmessage::setMessage(__('Ticket has been stored', 'js-support-ticket'), 'updated');

            //removing custom field attachments
            if($customflagfordelete == true){
                foreach ($custom_field_namesforadd as $key) {
                    $res = $this->removeFileCustom($ticketid,$key);
                }
            }
            //storing custom field attachments
            if($customflagforadd == true){
                foreach ($custom_field_namesforadd as $key) {
                    if ($_FILES[$key]['size'] > 0) { // logo
                        $res = $this->uploadFileCustom($ticketid,$key);
                    }
                }
            }
        }

        // Send Emails
        if ($sendEmail == true) {
            JSSTincluder::getJSModel('email')->sendMail(1, 1, $ticketid); // Mailfor, Create Ticket, Ticketid
            //For Hook
            $ticketobject = jssupportticket::$_db->get_row("SELECT * FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` WHERE id = " . $ticketid);
            do_action('jsst-ticketcreate', $ticketobject);
        }
        return $ticketid;
    }

    function uploadFileCustom($id,$field){
        if ($_FILES[$field]['size'] > 0) {
            $file_name = str_replace(' ', '_', $_FILES[$field]['name']);
            $file_tmp = $_FILES[$field]['tmp_name']; // actual location
        }else{
            return;
        }
        $file_size = jssupportticket::$_config['file_maximum_size'];
        if($_FILES[$field]['size'] > ($file_size * 1024)){
            return;
        }
        if ($file_name != "" AND $file_tmp != "") {
            $is_allow = JSSTincluder::getJSModel('jssupportticket')->checkExtension($filename);
            if($is_allow == false){
                return;
            }
        }        
        $datadirectory = jssupportticket::$_config['data_directory'];
        $path = jssupportticket::$_path . '/' . $datadirectory;
        if (!file_exists($path)) { // create user directory
            JSSTincluder::getJSModel('jssupportticket')->makeDir($path);
        }
        $path = $path . '/attachmentdata';
        if (!file_exists($path)) { // create user directory
            JSSTincluder::getJSModel('jssupportticket')->makeDir($path);
        }
        $path = $path . '/ticket';
        if (!file_exists($path)) { // create user directory
            JSSTincluder::getJSModel('jssupportticket')->makeDir($path);
        }

        $query = "SELECT attachmentdir FROM `".jssupportticket::$_db->prefix."js_ticket_tickets` WHERE id = ".$id;
        $foldername = jssupportticket::$_db->get_var($query);
        $userpath = $path . '/' . $foldername;
        if (!file_exists($userpath)) { // create user directory
            JSSTincluder::getJSModel('jssupportticket')->makeDir($userpath);
        }
        move_uploaded_file($file_tmp, $userpath . '/' . $file_name);
        return ;
    }

    function removeFileCustom($id,$key){
        $filename = str_replace(' ', '_', $key);
        
        $datadirectory = jssupportticket::$_config['data_directory'];
        $path = jssupportticket::$_path . '/' . $datadirectory. '/attachmentdata/ticket';
        
        $query = "SELECT attachmentdir FROM `".jssupportticket::$_db->prefix."js_ticket_tickets` WHERE id = ".$id;
        $foldername = jssupportticket::$_db->get_var($query);
        $userpath = $path . '/' . $foldername.'/'.$filename;
        unlink($userpath);
        return ;
    }

    function removeTicket($id) {
        $sendEmail = true;
        if (!is_numeric($id))
            return false;

        if ($this->canRemoveTicket($id)) {
            jssupportticket::$_data['ticketid'] = $this->getTrackingIdById($id);
            jssupportticket::$_data['ticketemail'] = $this->getTicketEmailById($id);
            jssupportticket::$_data['ticketsubject'] = $this->getTicketSubjectById($id);
            jssupportticket::$_db->delete(jssupportticket::$_db->prefix . 'js_ticket_tickets', array('id' => $id));

            if (jssupportticket::$_db->last_error == null) {
                $messagetype = __('Successfully', 'js-support-ticket');
                JSSTmessage::setMessage(__('Ticket has been deleted', 'js-support-ticket'), 'updated');
                // delete user fields
                jssupportticket::$_db->delete(jssupportticket::$_db->prefix . 'js_ticket_userfield_data', array('referenceid' => $id));
            } else {
                JSSTincluder::getJSModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
                JSSTmessage::setMessage(__('Ticket has not been deleted', 'js-support-ticket'), 'error');
                $messagetype = __('Error', 'js-support-ticket');
                $sendEmail = false;
            }

            // Send Emails
            if ($sendEmail == true) {
                JSSTincluder::getJSModel('email')->sendMail(1, 3); // Mailfor, Delete Ticket
                $ticketobject = (object) array('ticketid' => jssupportticket::$_data['ticketid'], 'ticketemail' => jssupportticket::$_data['ticketemail']);
                do_action('jsst-ticketdelete', $ticketobject);
            }
        } else {
            JSSTmessage::setMessage(__('Ticket', 'js-support-ticket') . ' ' . __('in use cannot be deleted', 'js-support-ticket'), 'error');
        }

        return;
    }

    function removeEnforceTicket($id) {
        $sendEmail = true;
        if (!is_numeric($id))
            return false;

        jssupportticket::$_data['ticketid'] = $this->getTrackingIdById($id);
        jssupportticket::$_data['ticketemail'] = $this->getTicketEmailById($id);
        jssupportticket::$_data['ticketsubject'] = $this->getTicketSubjectById($id);
        jssupportticket::$_db->delete(jssupportticket::$_db->prefix . 'js_ticket_tickets', array('id' => $id));

        if (jssupportticket::$_db->last_error == null) {
            $messagetype = __('Successfully', 'js-support-ticket');
            JSSTmessage::setMessage(__('Ticket has been deleted', 'js-support-ticket'), 'updated');
        } else {
            JSSTincluder::getJSModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            JSSTmessage::setMessage(__('Ticket has not been deleted', 'js-support-ticket'), 'error');
            $messagetype = __('Error', 'js-support-ticket');
            $sendEmail = false;
        }

        // Send Emails
        if ($sendEmail == true) {
            JSSTincluder::getJSModel('email')->sendMail(1, 3); // Mailfor, Delete Ticket
            $ticketobject = (object) array('ticketid' => jssupportticket::$_data['ticketid'], 'ticketemail' => jssupportticket::$_data['ticketemail']);
            do_action('jsst-ticketdelete', $ticketobject);
        }
        // delete replies
        JSSTincluder::getJSModel('reply')->removeTicketReplies($id);
        return;
    }

    private function canRemoveTicket($id) {
        if (!is_numeric($id))
            return false;
        $query = "SELECT (
                    (SELECT COUNT(id) FROM `" . jssupportticket::$_db->prefix . "js_ticket_attachments` WHERE ticketid = " . $id . ")
                    +(SELECT COUNT(id) FROM `" . jssupportticket::$_db->prefix . "js_ticket_replies` WHERE ticketid = " . $id . ")
                    ) AS total";
        $result = jssupportticket::$_db->get_var($query);
        if (jssupportticket::$_db->last_error != null) {
            JSSTincluder::getJSModel('systemerror')->addSystemError();
        }
        if ($result == 0)
            return true;
        else
            return false;
    }

    function getTicketSubjectById($id) {
        if (!is_numeric($id))
            return false;
        $query = "SELECT subject FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` WHERE id = " . $id;
        if (jssupportticket::$_db->last_error != null) {
            JSSTincluder::getJSModel('systemerror')->addSystemError();
        }
        $subject = jssupportticket::$_db->get_var($query);
        return $subject;
    }

    function getTrackingIdById($id) {
        if (!is_numeric($id))
            return false;
        $query = "SELECT ticketid FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` WHERE id = " . $id;
        if (jssupportticket::$_db->last_error != null) {
            JSSTincluder::getJSModel('systemerror')->addSystemError();
        }
        $ticketid = jssupportticket::$_db->get_var($query);
        return $ticketid;
    }

    function getTicketEmailById($id) {
        if (!is_numeric($id))
            return false;
        $query = "SELECT email FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` WHERE id = " . $id;
        if (jssupportticket::$_db->last_error != null) {
            JSSTincluder::getJSModel('systemerror')->addSystemError();
        }
        $ticketemail = jssupportticket::$_db->get_var($query);
        return $ticketemail;
    }

    function setStatus($status, $ticketid) {
        // 0 -> New Ticket
        // 1 -> Waiting admin/staff reply
        // 2 -> in progress
        // 3 -> waiting for customer reply
        // 4 -> close ticket
        if (!is_numeric($status))
            return false;
        if (!is_numeric($ticketid))
            return false;
        $query = "UPDATE `" . jssupportticket::$_db->prefix . "js_ticket_tickets` SET status = " . $status . " WHERE id = " . $ticketid;
        jssupportticket::$_db->query($query);
        if (jssupportticket::$_db->last_error != null) {
            JSSTincluder::getJSModel('systemerror')->addSystemError();
        }
        return;
    }

    function updateLastReply($id) {
        if (!is_numeric($id))
            return false;
        $date = date_i18n('Y-m-d H:i:s');
        $isanswered = " , isanswered = 0 ";
        if (is_admin()) {
            $isanswered = " , isanswered = 1 ";
        }
        $query = "UPDATE `" . jssupportticket::$_db->prefix . "js_ticket_tickets` SET lastreply = '" . $date . "' " . $isanswered . " WHERE id = " . $id;
        jssupportticket::$_db->query($query);
        if (jssupportticket::$_db->last_error != null) {
            JSSTincluder::getJSModel('systemerror')->addSystemError();
        }
        return;
    }

    function closeTicket($id) {
        if (!is_numeric($id))
            return false;
        //Check if its allowed to close ticket
        if (!$this->checkActionStatusSame($id, array('action' => 'closeticket'))) {
            JSSTmessage::setMessage(__('Ticket already closed', 'js-support-ticket'), 'error');
            return;
        }
        $sendEmail = true;
        $date = date_i18n('Y-m-d H:i:s');
        $query = "UPDATE `" . jssupportticket::$_db->prefix . "js_ticket_tickets` SET status = 4, closed = '" . $date . "' WHERE id = " . $id;
        jssupportticket::$_db->query($query);
        if (jssupportticket::$_db->last_error == null) {
            JSSTmessage::setMessage(__('Ticket has been closed', 'js-support-ticket'), 'updated');
            $messagetype = __('Successfully', 'js-support-ticket');
        } else {
            JSSTincluder::getJSModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            JSSTmessage::setMessage(__('Ticket has not been closed', 'js-support-ticket'), 'error');
            $messagetype = __('Error', 'js-support-ticket');
            $sendEmail = false;
        }


        // Send Emails
        if ($sendEmail == true) {
            JSSTincluder::getJSModel('email')->sendMail(1, 2, $id); // Mailfor, Close Ticket, Ticketid
            $ticketobject = jssupportticket::$_db->get_row("SELECT * FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` WHERE id = " . $ticketid);
            do_action('jsst-ticketclose', $ticketobject);
        }

        return;
    }

    function getTicketListOrdering($sort) {
        switch ($sort) {
            case "subjectdesc":
                jssupportticket::$_ordering = "ticket.subject DESC";
                jssupportticket::$_sorton = "subject";
                jssupportticket::$_sortorder = "DESC";
                break;
            case "subjectasc":
                jssupportticket::$_ordering = "ticket.subject ASC";
                jssupportticket::$_sorton = "subject";
                jssupportticket::$_sortorder = "ASC";
                break;
            case "prioritydesc":
                jssupportticket::$_ordering = "priority DESC";
                jssupportticket::$_sorton = "priority";
                jssupportticket::$_sortorder = "DESC";
                break;
            case "priorityasc":
                jssupportticket::$_ordering = "priority ASC";
                jssupportticket::$_sorton = "priority";
                jssupportticket::$_sortorder = "ASC";
                break;
            case "ticketiddesc":
                jssupportticket::$_ordering = "ticket.ticketid DESC";
                jssupportticket::$_sorton = "ticketid";
                jssupportticket::$_sortorder = "DESC";
                break;
            case "ticketidasc":
                jssupportticket::$_ordering = "ticket.ticketid ASC";
                jssupportticket::$_sorton = "ticketid";
                jssupportticket::$_sortorder = "ASC";
                break;
            case "isanswereddesc":
                jssupportticket::$_ordering = "ticket.isanswered DESC";
                jssupportticket::$_sorton = "isanswered";
                jssupportticket::$_sortorder = "DESC";
                break;
            case "isansweredasc":
                jssupportticket::$_ordering = "ticket.isanswered ASC";
                jssupportticket::$_sorton = "isanswered";
                jssupportticket::$_sortorder = "ASC";
                break;
            case "statusdesc":
                jssupportticket::$_ordering = "ticket.status DESC";
                jssupportticket::$_sorton = "status";
                jssupportticket::$_sortorder = "DESC";
                break;
            case "statusasc":
                jssupportticket::$_ordering = "ticket.status ASC";
                jssupportticket::$_sorton = "status";
                jssupportticket::$_sortorder = "ASC";
                break;
            case "createddesc":
                jssupportticket::$_ordering = "ticket.created DESC";
                jssupportticket::$_sorton = "created";
                jssupportticket::$_sortorder = "DESC";
                break;
            case "createdasc":
                jssupportticket::$_ordering = "ticket.created ASC";
                jssupportticket::$_sorton = "created";
                jssupportticket::$_sortorder = "ASC";
                break;
            default: jssupportticket::$_ordering = "ticket.id DESC";
        }
        return;
    }

    function getSortArg($type, $sort) {
        $mat = array();
        if (preg_match("/(\w+)(asc|desc)/i", $sort, $mat)) {
            if ($type == $mat[1]) {
                return ( $mat[2] == "asc" ) ? "{$type}desc" : "{$type}asc";
            } else {
                return $type . $mat[2];
            }
        }
        return "iddesc";
    }

    function getTicketListSorting($sort) {
        jssupportticket::$_sortlinks['subject'] = $this->getSortArg("subject", $sort);
        jssupportticket::$_sortlinks['priority'] = $this->getSortArg("priority", $sort);
        jssupportticket::$_sortlinks['ticketid'] = $this->getSortArg("ticketid", $sort);
        jssupportticket::$_sortlinks['isanswered'] = $this->getSortArg("isanswered", $sort);
        jssupportticket::$_sortlinks['status'] = $this->getSortArg("status", $sort);
        jssupportticket::$_sortlinks['created'] = $this->getSortArg("created", $sort);
        return;
    }

    function changeTicketPriority($id, $priorityid) {
        if (!is_numeric($id))
            return false;
        if (!is_numeric($priorityid))
            return false;
        if (!$this->checkActionStatusSame($id, array('action' => 'priority', 'id' => $priorityid))) {
            JSSTmessage::setMessage(__('TICKET_ALREADY_HAVE_SAME_PRIORITY', 'js-support-ticket'), 'error');
            return;
        }
        $sendEmail = true;
        $date = date_i18n('Y-m-d H:i:s');
        $query = "UPDATE `" . jssupportticket::$_db->prefix . "js_ticket_tickets` SET priorityid = " . $priorityid . ", updated = '" . $date . "' WHERE id = " . $id;
        jssupportticket::$_db->query($query);
        if (jssupportticket::$_db->last_error == null) {
            JSSTmessage::setMessage(__('Priority has been changed', 'js-support-ticket'), 'updated');
            $messagetype = __('Successfully', 'js-support-ticket');
        } else {
            JSSTincluder::getJSModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            JSSTmessage::setMessage(__('Priority has not been changed', 'js-support-ticket'), 'error');
            $messagetype = __('Error', 'js-support-ticket');
            $sendEmail = false;
        }
        // Send Emails
        if ($sendEmail == true) {
            JSSTincluder::getJSModel('email')->sendMail(1, 11, $id, 'js_ticket_tickets'); // Mailfor, Ban email, Ticketid
        }

        return;
    }

    /* check can a ticket be opened with in the given days */

    function checkCanReopenTicket($ticketid) {
        if (!is_numeric($ticketid))
            return false;
        $lastreply = JSSTincluder::getJSModel('reply')->getLastReply($ticketid);
        if (!$lastreply)
            $lastreply = date_i18n('Y-m-d H:i:s');
        $days = jssupportticket::$_config['reopen_ticket_within_days'];
        $date = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime($lastreply)) . " +" . $days . " day"));
        if ($date < date_i18n('Y-m-d H:i:s'))
            return false;
        else
            return true;
    }

    function reopenTicket($data) {
        $ticketid = $data['ticketid'];
        $lastreply = isset($data['lastreplydate']) ? $data['lastreplydate'] : '';
        if (!is_numeric($ticketid))
            return false;
        /* check can a ticket be opened with in the given days */
        if ($this->checkCanReopenTicket($ticketid)) {
            $sendEmail = true;
            $date = date_i18n('Y-m-d H:i:s');
            $query = "UPDATE `" . jssupportticket::$_db->prefix . "js_ticket_tickets` SET status = 0 , updated = '" . $date . "' WHERE id = " . $ticketid;
            jssupportticket::$_db->query($query);
            if (jssupportticket::$_db->last_error == null) {
                JSSTmessage::setMessage(__('Ticket has been reopend', 'js-support-ticket'), 'updated');
                $messagetype = __('Successfully', 'js-support-ticket');
            } else {
                JSSTincluder::getJSModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
                JSSTmessage::setMessage(__('Ticket has not been reopened', 'js-support-ticket'), 'error');
                $messagetype = __('Error', 'js-support-ticket');
                $sendEmail = false;
            }
        } else {
            JSSTmessage::setMessage(__('TICKET_REOPEN_TIME_LIMIT_END', 'js-support-ticket'), 'error');
        }


        return;
    }

    function updateTicketStatusCron() {
        // close ticket
        $query = "UPDATE `" . jssupportticket::$_db->prefix . "js_ticket_tickets` SET status = 4 WHERE date(DATE_ADD(lastreply,INTERVAL " . jssupportticket::$_config['ticket_auto_close'] . " DAY)) < CURDATE() AND isanswered = 1";
        jssupportticket::$_db->query($query);
        if (jssupportticket::$_db->last_error != null) {
            JSSTincluder::getJSModel('systemerror')->addSystemError();
        }
    }

    function validateTicketDetailForUser($id) {
        if (!is_numeric($id))
            return false;
        $query = "SELECT uid FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` WHERE id = " . $id;
        $uid = jssupportticket::$_db->get_var($query);

        if ($uid == get_current_user_id()) {
            return true;
        } else {
            return false;
        }
    }

    function checkActionStatusSame($id, $array) {
        switch ($array['action']) {
            case 'priority':
                $result = jssupportticket::$_db->get_var('SELECT COUNT(id) FROM `' . jssupportticket::$_db->prefix . 'js_ticket_tickets` WHERE id = ' . $id . ' AND priorityid = ' . $array['id']);
                break;
            case 'closeticket':
                $result = jssupportticket::$_db->get_var('SELECT COUNT(id) FROM `' . jssupportticket::$_db->prefix . 'js_ticket_tickets` WHERE id = ' . $id . ' AND status = 4');
                break;
        }
        if ($result > 0) {
            return false;
        } else {
            return true;
        }
    }

    function getTicketidForVisitor($token) {
        include_once jssupportticket::$_path . 'includes/encoder.php';
        $encoder = new JSSTEncoder(jssupportticket::$_config['encoder_key']);
        $decryptedtext = $encoder->decrypt($token);
        $array = json_decode($decryptedtext, true);
        $emailaddress = $array['emailaddress'];
        $trackingid = $array['trackingid'];
        $query = "SELECT id FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` WHERE email = '" . $emailaddress . "' AND ticketid = '" . $trackingid . "'";
        $ticketid = jssupportticket::$_db->get_var($query);
        return $ticketid;
    }

    function createTokenByEmailAndTrackingId($emailaddress, $trackingid) {
        include_once jssupportticket::$_path . 'includes/encoder.php';
        $encoder = new JSSTEncoder(jssupportticket::$_config['encoder_key']);
        $token = $encoder->encrypt(json_encode(array('emailaddress' => $emailaddress, 'trackingid' => $trackingid)));
        return $token;
    }
    
    function validateTicketDetailForVisitor($id) {
        if (!isset($_SESSION['js-support-ticket']['token'])) {
            return false;
        }
        $token = $_SESSION['js-support-ticket']['token'];
        include_once jssupportticket::$_path . 'includes/encoder.php';
        $encoder = new JSSTEncoder(jssupportticket::$_config['encoder_key']);
        $decryptedtext = $encoder->decrypt($token);
        $array = json_decode($decryptedtext, true);
        $emailaddress = $array['emailaddress'];
        $trackingid = $array['trackingid'];
        $query = "SELECT id FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` WHERE email = '" . $emailaddress . "' AND ticketid = '" . $trackingid . "'";
        $ticketid = jssupportticket::$_db->get_var($query);

        if ($ticketid == $id) {
            return true;
        } else {
            return false;
        }
    }
}

?>
