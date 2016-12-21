<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

class JSSTattachmentModel {

    function getAttachmentForForm($id) {
        if (!is_numeric($id))
            return false;
        $query = "SELECT filename,filesize,id
					FROM `" . jssupportticket::$_db->prefix . "js_ticket_attachments`
					WHERE ticketid = " . $id . " and replyattachmentid = 0";
        jssupportticket::$_data[5] = jssupportticket::$_db->get_results($query);
        if (jssupportticket::$_db->last_error != null) {
            JSSTincluder::getJSModel('systemerror')->addSystemError();
        }
        return;
    }

    function getAttachmentForReply($id, $replyattachmentid) {
        if (!is_numeric($id))
            return false;
        if (!is_numeric($replyattachmentid))
            return false;
        $query = "SELECT filename,filesize,id
					FROM `" . jssupportticket::$_db->prefix . "js_ticket_attachments`
					WHERE ticketid = " . $id . " AND replyattachmentid = " . $replyattachmentid;
        $result = jssupportticket::$_db->get_results($query);
        if (jssupportticket::$_db->last_error != null) {
            JSSTincluder::getJSModel('systemerror')->addSystemError();
        }
        return $result;
    }

    function uploadFile($i, $id, $action, $isdeletefile) {
        if (is_numeric($id) == false)
            return false;
        $datadirectory = jssupportticket::$_config['data_directory'];
        $path = jssupportticket::$_path . '/' . $datadirectory;
        if (!file_exists($path)) { // create user directory
            JSSTincluder::getJSModel('jssupportticket')->makeDir($path);
        }
        $isupload = false;
        $path = $path . '/attachmentdata';
        if (!file_exists($path)) { // create user directory
            JSSTincluder::getJSModel('jssupportticket')->makeDir($path);
        }
        $path = $path . '/ticket';
        if (!file_exists($path)) { // create user directory
            JSSTincluder::getJSModel('jssupportticket')->makeDir($path);
        }
        if ($action == 1) { //Company logo
            if ($_FILES['filename']['size'][$i] > 0) {
                $file_name = str_replace(' ', '_', $_FILES['filename']['name'][$i]);
                $file_tmp = $_FILES['filename']['tmp_name'][$i]; // actual location
                $query = "SELECT attachmentdir FROM `".jssupportticket::$_db->prefix."js_ticket_tickets` WHERE id = ".$id;
                $foldername = jssupportticket::$_db->get_var($query);
                $userpath = $path . '/' . $foldername;
                if (!file_exists($userpath)) { //create user directory
                    JSSTincluder::getJSModel('jssupportticket')->makeDir($userpath);
                }
                $isupload = true;
            }
        }

        if ($isupload) {
            $files = glob($userpath . '/*.*');
            move_uploaded_file($file_tmp, $userpath . '/' . $file_name);
            return 1;
        } else { // DELETE FILES
            if ($action == 1) { // company logo
                if ($isdeletefile == 1) {
                    $query = "SELECT attachmentdir FROM `".jssupportticket::$_db->prefix."js_ticket_tickets` WHERE id = ".$id;
                    $foldername = jssupportticket::$_db->get_var($query);
                    $userpath = $path . '/' . $foldername;
                    $files = glob($userpath . '/*.*');
                    array_map('unlink', $files); // delete all file in the direcoty
                }
            }
            return 1;
        }
    }

    function storeAttachments($data) {
        $ticketid = $data['ticketid'];
        $filesize = jssupportticket::$_config['file_maximum_size'];
        if (isset($_FILES['filename'])) {
            $total = count($_FILES['filename']['name']);
            for ($i = 0; $i < $total; $i++) {
                if ($_FILES['filename']['name'][$i] != '') {
                    if ($_FILES['filename']['size'][$i] > 0) {
                        $uploadfilesize = $_FILES['filename']['size'][$i];
                        $uploadfilesize = $uploadfilesize / 1024; //kb
                        if ($uploadfilesize > $filesize) { // file size error
                            JSSTmessage::setMessage(__('Error file size too large', 'js-support-ticket'), 'error');
                            return;
                        }
                        $filename = str_replace(' ', '_', $_FILES['filename']['name'][$i]);
                        $result = JSSTincluder::getJSModel('jssupportticket')->checkExtension($filename);
                        if ($result == 'N') {//file extension error
                            JSSTmessage::setMessage(__('Error file ext mismatch', 'js-support-ticket'), 'error');
                            return;
                        }
                        $returnvalue = $this->uploadFile($i, $ticketid, 1, 0);
                        if ($returnvalue == 1) {
                            $replyattachmentid = isset($data['replyattachmentid']) ? $data['replyattachmentid'] : '';
                            $result = $this->storeTicketAttachment($ticketid, $replyattachmentid, $uploadfilesize, $filename);
                        } else {
                            message::setMessage(__('File read write issue files cannot upload', 'js-support-ticket'), 'error');
                        }
                    }
                }
            }
        }
        return;
    }

    function storeTicketAttachment($ticketid, $replyattachmentid, $filesize, $filename) {
        if (!is_numeric($ticketid))
            return false;
        $created = date_i18n('Y-m-d H:i:s');
        $query_array = array('ticketid' => $ticketid,
            'replyattachmentid' => $replyattachmentid,
            'filesize' => $filesize,
            'filename' => $filename,
            'status' => 1,
            'created' => $created
        );
        jssupportticket::$_db->replace(jssupportticket::$_db->prefix . 'js_ticket_attachments', $query_array);
        //tickets attachments store
        if (jssupportticket::$_db->last_error != null) {
            JSSTincluder::getJSModel('systemerror')->addSystemError();
        }
        return;
    }

    function removeAttachment($id) {
        if (!is_numeric($id))
            return false;
        $query = "SELECT filename FROM `" . jssupportticket::$_db->prefix . "js_ticket_attachments` WHERE id = " . $id;
        $filename = jssupportticket::$_db->get_var($query);
        jssupportticket::$_db->delete(jssupportticket::$_db->prefix . 'js_ticket_attachments', array('id' => $id));
        if (jssupportticket::$_db->last_error == null) {
            $datadirectory = jssupportticket::$_config['data_directory'];
            $path = jssupportticket::$_path . '/' . $datadirectory;
            $path = $path . '/attachmentdata';
            $path = $path . '/ticket/ticket_' . $id . '/' . $filename;
            unlink($path);
            //$files = glob($path.'/*.*');
            //array_map('unlink', $files); // delete all file in the direcoty
            JSSTmessage::setMessage(__('Attachment has been removed', 'js-support-ticket'), 'updated');
        } else {
            JSSTincluder::getJSModel('systemerror')->addSystemError();
            JSSTmessage::setMessage(__('Attachment has not been removed', 'js-support-ticket'), 'error');
        }
    }

    function getDownloadAttachmentById($id){
        if(!is_numeric($id)) return false;
        $query = "SELECT ticket.id AS ticketid, ticket.attachmentdir AS foldername, attach.filename  "
                . " FROM `".jssupportticket::$_db->prefix."js_ticket_attachments` AS attach "
                . " JOIN `".jssupportticket::$_db->prefix."js_ticket_tickets` AS ticket ON ticket.id = attach.ticketid "
                . " WHERE attach.id = $id";
        $object = jssupportticket::$_db->get_row($query);
        $foldername = $object->foldername;
        $ticketid = $object->ticketid;
        $filename = $object->filename;
        $download = false;
        if(is_user_logged_in()){
            if(is_admin()){
                $download = true;
            }else{
                if(JSSTincluder::getJSModel('ticket')->validateTicketDetailForUser($ticketid)){
                    $download = true;
                }
            }            
        }else{ // user is visitor
            $download = JSSTincluder::getJSModel('ticket')->validateTicketDetailForVisitor($ticketid);
        }
        if($download == true){
            $datadirectory = jssupportticket::$_config['data_directory'];
            if(is_admin()){
                $path = '../wp-content/plugins/js-support-ticket/' . $datadirectory;
            }else{
                $path = 'wp-content/plugins/js-support-ticket/' . $datadirectory;
            }            
            $path = $path . '/attachmentdata';
            $path = $path . '/ticket/' . $foldername . '/';
            $file = $path . '/'.$filename;
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            //ob_clean();
            flush();
            readfile($file);
            exit();
        }else{
            include( get_query_template( '404' ) );
            exit;
        }
    }
    
    function getDownloadAttachmentByName($file_name,$id){
        if(empty($file_name)) return false;
        if(!is_numeric($id)) return false;
        $filename = str_replace(' ', '_',$file_name);
        $query = "SELECT attachmentdir FROM `".jssupportticket::$_db->prefix."js_ticket_tickets` WHERE id = ".$id;
        $foldername = jssupportticket::$_db->get_var($query);

        $datadirectory = jssupportticket::$_config['data_directory'];
        if(is_admin()){
            $path = '../wp-content/plugins/js-support-ticket/' . $datadirectory;
        }else{
            $path = 'wp-content/plugins/js-support-ticket/' . $datadirectory;
        }           
        $path = $path . '/attachmentdata';
        $path = $path . '/ticket/' . $foldername;
        $file = $path . '/'.$filename;

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        //ob_clean();
        flush();
        readfile($file);
        exit();
        exit;
    }
}

?>
