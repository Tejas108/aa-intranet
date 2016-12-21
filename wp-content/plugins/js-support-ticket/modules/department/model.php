<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class JSSTdepartmentModel {

    function getDepartments() {
        // Filter
        $isadmin = is_admin();
        $deptname = ($isadmin) ? 'departmentname' : 'jsst-dept';

        $departmentname = JSSTrequest::getVar($deptname);

        if ($isadmin) {
            $formsearch = JSSTrequest::getVar('JSST_form_search', 'post');
            if ($formsearch == 'JSST_SEARCH') {
                $_SESSION['JSST_SEARCH']['departmentname'] = $departmentname;
            }
        }

        if (JSSTrequest::getVar('pagenum', 'get', null) != null && $isadmin) {
            $departmentname = (isset($_SESSION['JSST_SEARCH']['departmentname']) && $_SESSION['JSST_SEARCH']['departmentname'] != '') ? $_SESSION['JSST_SEARCH']['departmentname'] : null;
        }
        $departmentname = jssupportticket::parseSpaces($departmentname);
        $inquery = '';
        if ($departmentname != null)
            $inquery .= " WHERE department.departmentname LIKE '%$departmentname%'";

        jssupportticket::$_data['filter'][$deptname] = $departmentname;

        // Pagination
        $query = "SELECT COUNT(`id`) FROM `" . jssupportticket::$_db->prefix . "js_ticket_departments` AS department";
        $query .= $inquery;
        $total = jssupportticket::$_db->get_var($query);
        jssupportticket::$_data['total'] = $total;
        jssupportticket::$_data[1] = JSSTpagination::getPagination($total);

        // Data
        $query = "SELECT department.*,email.email AS outgoingemail
                    FROM `" . jssupportticket::$_db->prefix . "js_ticket_departments` AS department 
                    LEFT JOIN `" . jssupportticket::$_db->prefix . "js_ticket_email` AS email ON email.id = department.emailid ";
        $query .= $inquery;
        $query .= " ORDER BY department.ordering ASC,department.departmentname ASC LIMIT " . JSSTpagination::getOffset() . ", " . JSSTpagination::getLimit();
        jssupportticket::$_data[0] = jssupportticket::$_db->get_results($query);
        if (jssupportticket::$_db->last_error != null) {
            JSSTincluder::getJSModel('systemerror')->addSystemError();
        }
        return;
    }

    function getDepartmentForForm($id) {
        if ($id) {
            if (!is_numeric($id))
                return false;
            $query = "SELECT department.*,email.email AS outgoingemail
                        FROM `" . jssupportticket::$_db->prefix . "js_ticket_departments` AS department 
                        JOIN `" . jssupportticket::$_db->prefix . "js_ticket_email` AS email ON email.id = department.emailid 
                        WHERE department.id = " . $id;
            jssupportticket::$_data[0] = jssupportticket::$_db->get_row($query);
            if (jssupportticket::$_db->last_error != null) {
                JSSTincluder::getJSModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            }
        }
        return;
    }

    private function getNextOrdering() {
        $query = "SELECT MAX(ordering) FROM `" . jssupportticket::$_db->prefix . "js_ticket_departments`";
        $result = jssupportticket::$_db->get_var($query);
        if (jssupportticket::$_db->last_error != null) {
            JSSTincluder::getJSModel('systemerror')->addSystemError();
        }
        return $result + 1;
    }

    function storeDepartment($data) {
        if ($data['id'])
            $data['updated'] = date_i18n('Y-m-d H:i:s');
        else
            $data['created'] = date_i18n('Y-m-d H:i:s');
        $query_array = array('id' => $data['id'],
            'emailid' => $data['emailid'],
            'ispublic' => $data['ispublic'],
            'departmentname' => $data['departmentname'],
            'departmentsignature' => wpautop(wptexturize(stripslashes($data['departmentsignature']))),
            'canappendsignature' => $data['canappendsignature'],
            'status' => $data['status'],
            'created' => $data['created'],
            'updated' => $data['updated']
        );
        if (!$data['id']) { //new
            $query_array['ordering'] = $this->getNextOrdering();
        } else {
            $query_array['ordering'] = $data['ordering'];
        }

        jssupportticket::$_db->replace(jssupportticket::$_db->prefix . 'js_ticket_departments', $query_array);
        if (jssupportticket::$_db->last_error == null) {
            JSSTmessage::setMessage(__('Department has been stored', 'js-support-ticket'), 'updated');
        } else {
            JSSTincluder::getJSModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            JSSTmessage::setMessage(__('Department has not been stored', 'js-support-ticket'), 'error');
        }
        return;
    }

    function setOrdering($id) {
        if (!is_numeric($id))
            return false;
        $order = JSSTrequest::getVar('order', 'get');
        if ($order == 'down') {
            $order = ">";
            $direction = "ASC";
        } else {
            $order = "<";
            $direction = "DESC";
        }
        $query = "SELECT t.ordering,t.id,t2.ordering AS ordering2 FROM `" . jssupportticket::$_db->prefix . "js_ticket_departments` AS t,`" . jssupportticket::$_db->prefix . "js_ticket_departments` AS t2 WHERE t.ordering $order t2.ordering AND t2.id = $id ORDER BY t.ordering $direction LIMIT 1";
        $result = jssupportticket::$_db->get_row($query);
        $query = "UPDATE `" . jssupportticket::$_db->prefix . "js_ticket_departments` SET ordering = " . $result->ordering . " WHERE id = " . $id;
        jssupportticket::$_db->query($query);
        $query = "UPDATE `" . jssupportticket::$_db->prefix . "js_ticket_departments` SET ordering = " . $result->ordering2 . " WHERE id = " . $result->id;
        jssupportticket::$_db->query($query);
        if (jssupportticket::$_db->last_error == null) {
            JSSTmessage::setMessage(__('Departments', 'js-support-ticket') . ' ' . __('ordering has been changed', 'js-support-ticket'), 'updated');
        } else {
            JSSTincluder::getJSModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            JSSTmessage::setMessage(__('Departments', 'js-support-ticket') . ' ' . __('ordering has not changed', 'js-support-ticket'), 'error');
        }
        return;
    }

    function removeDepartment($id) {
        if (!is_numeric($id))
            return false;
        if ($this->canRemoveDepartment($id)) {
            jssupportticket::$_db->delete(jssupportticket::$_db->prefix . 'js_ticket_departments', array('id' => $id));
            if (jssupportticket::$_db->last_error == null) {
                JSSTmessage::setMessage(__('Department has been deleted', 'js-support-ticket'), 'updated');
            } else {
                JSSTincluder::getJSModel('systemerror')->addSystemError();
                JSSTmessage::setMessage(__('Department has not been deleted', 'js-support-ticket'), 'error');
            }
        } else {
            JSSTmessage::setMessage(__('Department in use cannot deleted', 'js-support-ticket'), 'error');
        }
        return;
    }

    private function canRemoveDepartment($id) {
        if (!is_numeric($id))
            return false;
        $query = "SELECT (
                    (SELECT COUNT(id) FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` WHERE departmentid = " . $id . ")
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

    function getDepartmentForCombobox() {
        $query = "SELECT id, departmentname AS text FROM `" . jssupportticket::$_db->prefix . "js_ticket_departments` WHERE status = 1";
        if (!is_admin()) {
            $query .= '  AND ispublic = 1';
        }
        $query .= " ORDER BY ordering";
        $list = jssupportticket::$_db->get_results($query);
        if (jssupportticket::$_db->last_error != null) {
            JSSTincluder::getJSModel('systemerror')->addSystemError();
        }
        return $list;
    }

    function changeStatus($id) {
        if (!is_numeric($id))
            return false;

        $query = "UPDATE `" . jssupportticket::$_db->prefix . "js_ticket_departments` SET status = 1-status WHERE id=" . $id;
        jssupportticket::$_db->query($query);

        if (jssupportticket::$_db->last_error == null) {
            JSSTmessage::setMessage(__('Department' . 'js-support-ticket') . ' ' . __('status has been changed', 'js-support-ticket'), 'updated');
        } else {
            JSSTincluder::getJSModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            JSSTmessage::setMessage(__('Department', 'js-support-ticket') . ' ' . __('status has not been changed', 'js-support-ticket'), 'error');
        }
        return;
    }

    function getSignatureByID($id) {
        if (!is_numeric($id))
            return false;
        $query = "SELECT departmentsignature FROM `" . jssupportticket::$_db->prefix . "js_ticket_departments` WHERE id = " . $id;
        $signature = jssupportticket::$_db->get_var($query);
        return $signature;
    }

    function getDepartmentById($id) {
        if (!is_numeric($id))
            return false;
        $query = "SELECT departmentname FROM `" . jssupportticket::$_db->prefix . "js_ticket_departments` WHERE id = " . $id;
        $departmentname = jssupportticket::$_db->get_var($query);
        return $departmentname;
    }

}

?>
