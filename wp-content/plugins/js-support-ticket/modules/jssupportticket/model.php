<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class JSSTjssupportticketModel {

    function getControlPanelData() {
        $curdate = date_i18n('Y-m-d');
        $fromdate = date_i18n('Y-m-d', strtotime("now -1 month"));

        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` WHERE priorityid = priority.id AND status = 0 AND (lastreply = '0000-00-00 00:00:00' OR lastreply = '') AND date(created) >= '" . $fromdate . "' AND date(created) <= '" . $curdate . "' ) AS totalticket
                    FROM `" . jssupportticket::$_db->prefix . "js_ticket_priorities` AS priority ORDER BY priority.priority";
        $openticket_pr = jssupportticket::$_db->get_results($query);
        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` WHERE priorityid = priority.id AND isanswered = 1 AND status != 4 AND status != 0 AND date(created) >= '" . $fromdate . "' AND date(created) <= '" . $curdate . "') AS totalticket
                    FROM `" . jssupportticket::$_db->prefix . "js_ticket_priorities` AS priority ORDER BY priority.priority";
        $answeredticket_pr = jssupportticket::$_db->get_results($query);
        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` WHERE priorityid = priority.id AND isanswered != 1 AND status != 4 AND (lastreply != '0000-00-00 00:00:00' AND lastreply != '') AND date(created) >= '" . $fromdate . "' AND date(created) <= '" . $curdate . "') AS totalticket
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

        jssupportticket::$_data['ticket_total']['openticket'] = 0;
        jssupportticket::$_data['ticket_total']['overdueticket'] = 0;
        jssupportticket::$_data['ticket_total']['pendingticket'] = 0;
        jssupportticket::$_data['ticket_total']['answeredticket'] = 0;

        $count = count($openticket_pr);
        for ($i = 0; $i < $count; $i++) {
            jssupportticket::$_data['ticket_total']['openticket'] += $openticket_pr[$i]->totalticket;
            jssupportticket::$_data['ticket_total']['pendingticket'] += $pendingticket_pr[$i]->totalticket;
            jssupportticket::$_data['ticket_total']['answeredticket'] += $answeredticket_pr[$i]->totalticket;
        }

        $query = "SELECT ticket.id,ticket.ticketid,ticket.subject,ticket.name,ticket.created,priority.priority,priority.prioritycolour,ticket.status
		 			FROM `" . jssupportticket::$_db->prefix . "js_ticket_tickets` AS ticket
		 			JOIN `" . jssupportticket::$_db->prefix . "js_ticket_priorities` AS priority ON priority.id = ticket.priorityid
		 			ORDER BY ticket.status ASC, ticket.created DESC LIMIT 0, 5";
        jssupportticket::$_data['tickets'] = jssupportticket::$_db->get_results($query);
        jssupportticket::$_data['version'] = jssupportticket::$_config['versioncode'];
        return;
    }

    function makeDir($path) {
        if (!file_exists($path)) { // create directory
            mkdir($path, 0755);
            $ourFileName = $path . '/index.html';
            $ourFileHandle = fopen($ourFileName, 'w') or die(__('Cannot open file', 'js-support-ticket'));
            fclose($ourFileHandle);
        }
    }

    function checkExtension($filename) {
        $i = strrpos($filename, ".");
        if (!$i)
            return 'N';
        $l = strlen($filename) - $i;
        $ext = substr($filename, $i + 1, $l);
        $extensions = explode(",", jssupportticket::$_config['file_extension']);
        $match = 'N';
        foreach ($extensions as $extension) {
            if (strtolower($extension) == strtolower($ext)) {
                $match = 'Y';
                break;
            }
        }
        return $match;
    }

    function getUserListForRegistration() {
        $query = "SELECT DISTINCT user.ID AS userid, user.user_login AS username, user.user_email AS useremail, user.display_name AS userdisplayname
                    FROM `" . jssupportticket::$_wpprefixforuser . "users` AS user ";
        $users = jssupportticket::$_db->get_results($query);
        return $users;
    }

    function getusersearchajax() {
        $username = JSSTrequest::getVar('username');
        $name = JSSTrequest::getVar('name');
        $emailaddress = JSSTrequest::getVar('emailaddress');
        $canloadresult = false;

        $userlimit = JSSTrequest::getVar('userlimit',null,0);
        $maxrecorded = 4;
        $query = "SELECT DISTINCT COUNT(user.ID) 
                    FROM `" . jssupportticket::$_wpprefixforuser . "users` AS user
                    WHERE NOT EXISTS(SELECT umeta_id FROM `".jssupportticket::$_wpprefixforuser."usermeta` WHERE meta_value LIKE '%administrator%' AND user_id = user.id) ";
        if (strlen($name) > 0) {
            $query .= " AND user.display_name LIKE '%$name%'";
            $canloadresult = true;
        }
        if (strlen($emailaddress) > 0) {
            $query .= " AND user.user_email LIKE '%$emailaddress%'";
            $canloadresult = true;
        }
        if (strlen($username) > 0) {
            $query .= " AND user.user_login LIKE '%$username%'";
            $canloadresult = true;
        }
        $total = jssupportticket::$_db->get_var($query);
        $limit = $userlimit * $maxrecorded;
        if($limit >= $total){
            $limit = 0;
        }
        $query = "SELECT DISTINCT user.ID AS userid, user.user_login AS username, user.user_email AS useremail, user.display_name AS userdisplayname
                    FROM `" . jssupportticket::$_wpprefixforuser . "users` AS user
                    WHERE NOT EXISTS(SELECT umeta_id FROM `".jssupportticket::$_wpprefixforuser."usermeta` WHERE meta_value LIKE '%administrator%' AND user_id = user.id) ";
        if (strlen($name) > 0) {
            $query .= " AND user.display_name LIKE '%$name%'";
            $canloadresult = true;
        }
        if (strlen($emailaddress) > 0) {
            $query .= " AND user.user_email LIKE '%$emailaddress%'";
            $canloadresult = true;
        }
        if (strlen($username) > 0) {
            $query .= " AND user.user_login LIKE '%$username%'";
            $canloadresult = true;
        }
        $query .= " LIMIT $limit, $maxrecorded";
        $users = jssupportticket::$_db->get_results($query);
        $html = $this->makeUserList($users,$total,$maxrecorded,$userlimit);
        return $html;
    }

    function getuserlistajax(){
        $userlimit = JSSTrequest::getVar('userlimit',null,0);
        $maxrecorded = 4;
        $query = "SELECT DISTINCT COUNT(user.id)
                    FROM `" . jssupportticket::$_wpprefixforuser . "users` AS user
                    WHERE NOT EXISTS(SELECT umeta_id FROM `".jssupportticket::$_wpprefixforuser."usermeta` WHERE meta_value LIKE '%administrator%' AND user_id = user.id)";
        $total = jssupportticket::$_db->get_var($query);
        $limit = $userlimit * $maxrecorded;
        if($limit >= $total){
            $limit = 0;
        }
        $query = "SELECT DISTINCT user.ID AS userid, user.user_login AS username, user.user_email AS useremail, user.display_name AS userdisplayname, user.user_nicename AS usernicename
                    FROM `" . jssupportticket::$_wpprefixforuser . "users` AS user
                    WHERE NOT EXISTS(SELECT umeta_id FROM `".jssupportticket::$_wpprefixforuser."usermeta` WHERE meta_value LIKE '%administrator%' AND user_id = user.id)
                    LIMIT $limit, $maxrecorded";
        $users = jssupportticket::$_db->get_results($query);
        $html = $this->makeUserList($users,$total,$maxrecorded,$userlimit);
        return $html;
    }

    function makeUserList($users,$total,$maxrecorded,$userlimit){
        $html = '';
        if(!empty($users)){
            if(is_array($users)){
                $html ='
                    <div class="js-col-md-2 js-title">'.__('User id','js-support-ticket').'</div>
                    <div class="js-col-md-3 js-title">'.__('Username','js-support-ticket').'</div>
                    <div class="js-col-md-4 js-title">'.__('Email address','js-support-ticket').'</div>
                    <div class="js-col-md-3 js-title">'.__('Name','js-support-ticket').'</div>';
                foreach($users AS $user){
                    $html .='
                        <div class="user-records-wrapper js-value" style="display:inline-block;width:100%;">
                            <div class="js-col-xs-12 js-col-md-2">
                                <span class="js-user-title-xs">'.__('User id','js-support-ticket').' : </span>'.$user->userid.'
                            </div>                            
                            <div class="js-col-xs-12 js-col-md-3">
                                <span class="js-user-title-xs">'.__('Username','js-support-ticket').' : </span>';
                                $username = empty($user->userdisplayname) ? $user->usernicename : $user->userdisplayname;
                                    $html .='<a href="#" class="js-userpopup-link" data-id="'.$user->userid.'" data-email="'.$user->useremail.'" data-name="'.$username.'">'.$user->username.'</a> </div>';
                            $html .=
                            '<div class="js-col-xs-12 js-col-md-4">
                                <span class="js-user-title-xs">'.__('Email address','js-support-ticket').' : </span>'.$user->useremail.'
                            </div>
                            <div class="js-col-xs-12 js-col-md-3">
                                <span class="js-user-title-xs">'.__('Display name','js-support-ticket').' : </span>'.$user->userdisplayname.'
                            </div>
                        </div>';
                }
                $num_of_pages = ceil($total / $maxrecorded);
                $num_of_pages = ($num_of_pages > 0) ? ceil($num_of_pages) : floor($num_of_pages);
                if($num_of_pages > 0){
                    $page_html = '';
                    $prev = $userlimit;
                    if($prev > 0){
                        $page_html .= '<a class="jsst_userlink" href="#" onclick="updateuserlist('.($prev - 1).');">'.__('Previous','js-support-ticket').'</a>';
                    }
                    for($i = 0; $i < $num_of_pages; $i++){
                        if($i == $userlimit)
                            $page_html .= '<span class="jsst_userlink selected" >'.($i + 1).'</span>';
                        else
                            $page_html .= '<a class="jsst_userlink" href="#" onclick="updateuserlist('.$i.');">'.($i + 1).'</a>';

                    }
                    $next = $userlimit + 1;
                    if($next < $num_of_pages){
                        $page_html .= '<a class="jsst_userlink" href="#" onclick="updateuserlist('.$next.');">'.__('Next','js-support-ticket').'</a>';
                    }
                    if($page_html != ''){
                        $html .= '<div class="jsst_userpages">'.$page_html.'</div>';
                    }
                }
            }
        }else{
            $html = JSSTlayout::getNoRecordFound();
        }
        return $html;        
    }

    function gettingStartData(){
        $query = "SELECT COUNT(ID) FROM `".jssupportticket::$_db->prefix."posts` WHERE post_type = 'page' AND post_content LIKE '%[jssupportticket]%';";
        $pageexist = jssupportticket::$_db->get_var($query);
        if($pageexist > 0){ // page exist
            $query = "SELECT post_status FROM `".jssupportticket::$_db->prefix."posts` WHERE post_type = 'page' AND post_content LIKE '%[jssupportticket]%';";
            $poststatus = jssupportticket::$_db->get_var($query);
        }else{ // page not exist
            $poststatus = 0;
        }
        jssupportticket::$_data['pageexist'] = $pageexist;
        jssupportticket::$_data['poststatus'] = $poststatus;
        return;
    }

    function getPageList() {
        $query = "SELECT ID AS id, post_title AS text FROM `" . jssupportticket::$_db->prefix . "posts` WHERE post_type = 'page' AND post_status = 'publish' AND post_content NOT LIKE '%[jssupportticket]%'; ";
        $emails = jssupportticket::$_db->get_results($query);
        if (jssupportticket::$_db->last_error != null) {
            JSSTincluder::getJSModel('systemerror')->addSystemError();
        }
        return $emails;
    }

    function addPageSlug($data){
        if(!is_numeric($data['ID'])) return false;
        $query = "UPDATE `".jssupportticket::$_db->prefix."posts` SET post_content = CONCAT('[jssupportticket]<br/>',post_content) WHERE ID = ".$data['ID'];
        jssupportticket::$_db->query($query);
        if (jssupportticket::$_db->last_error == null) {
            JSSTmessage::setMessage(__('Shortcode has been sucessfully added', 'js-support-ticket'), 'updated');
        } else {
            JSSTincluder::getJSModel('systemerror')->addSystemError(); // if there is an error add it to system errorrs
            JSSTmessage::setMessage(__('Shortcode has not been added', 'js-support-ticket'), 'error');
        }
    }
}

?>
