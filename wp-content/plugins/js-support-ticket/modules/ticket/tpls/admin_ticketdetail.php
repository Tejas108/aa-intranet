<?php
JSSTmessage::getMessage();
wp_enqueue_script('file_validate.js', jssupportticket::$_pluginpath . 'includes/js/file_validate.js');
wp_enqueue_script('jquery-ui-tabs');
wp_enqueue_style('jquery-ui-css', 'http://www.example.com/your-plugin-path/css/jquery-ui.css');
wp_enqueue_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
?>
<script>
    jQuery(document).ready(function () {
        jQuery("#tabs").tabs();
    });
</script>
<script type="text/javascript">
    function checktinymcebyid(id) {
        var content = tinymce.get(id).getContent({format: 'text'});
        if (jQuery.trim(content) == '')
        {
            alert('<?php echo __('Some values are not acceptable please retry', 'js-support-ticket'); ?>');
            return false;
        }
        return true;
    }
    jQuery(document).ready(function ($) {
        jQuery("#tk_attachment_add").click(function () {
            var obj = this;
            var current_files = jQuery('input[type="file"]').length;
            var total_allow =<?php echo jssupportticket::$_config['no_of_attachement']; ?>;
            var append_text = "<span class='tk_attachment_value_text'><input name='filename[]' type='file' onchange=\"uploadfile(this,'<?php echo jssupportticket::$_config['file_maximum_size']; ?>','<?php echo jssupportticket::$_config['file_extension']; ?>');\" size='20' maxlenght='30'  /><span  class='tk_attachment_remove'></span></span>";
            if (current_files < total_allow) {
                jQuery(".tk_attachment_value_wrapperform").append(append_text);
            } else if ((current_files === total_allow) || (current_files > total_allow)) {
                alert('<?php echo __('File upload limit exceed', 'js-support-ticket'); ?>');
                obj.hide();
            }
        });
        jQuery(document).delegate(".tk_attachment_remove", "click", function (e) {
            jQuery(this).parent().remove();
            var current_files = jQuery('input[type="file"]').length;
            var total_allow =<?php echo jssupportticket::$_config['no_of_attachement']; ?>;
            if (current_files < total_allow) {
                jQuery("#tk_attachment_add").show();
            }
        });
        jQuery("a#showhidedetail").click(function (e) {
            e.preventDefault();
            var divid = jQuery(this).attr('data-divid');
            jQuery("div#" + divid).slideToggle();
            jQuery(this).find('img').toggleClass('js-hidedetail');
        });
        jQuery("a#showaction").click(function (e) {
            e.preventDefault();
            jQuery("div#action-div").slideToggle();
        });

        var height = jQuery(window).height();
        jQuery("a#showhistory").click(function (e) {
            e.preventDefault();
            jQuery("div#userpopup").slideDown('slow');
            jQuery('div#popup-back').show();
        });
        jQuery("div#popup-back,span.close-history").click(function (e) {
            jQuery("div#userpopup").slideUp('slow');
            setTimeout(function () {
                jQuery('div#popup-back').hide();
            }, 700);
        });
    });
    function actionticket(action) {
        /*  Action meaning
         * 1 -> Change Priority
         * 2 -> Close Ticket
         */
        jQuery("input#actionid").val(action);
        jQuery("form#adminTicketform").submit();
    }

</script>
<span style="display:none" id="filesize"><?php echo __('Error file size too large', 'js-support-ticket'); ?></span>
<span style="display:none" id="fileext"><?php echo __('Error file ext mismatch', 'js-support-ticket'); ?></span>
<span class="js-admin-title"><?php echo __('Ticket Details', 'js-support-ticket'); ?></span>
<?php
if (!empty(jssupportticket::$_data[0])) {
    if (jssupportticket::$_data[0]->status == 0) {
        $style = "red;";
        $status = __('New', 'js-support-ticket');
    } elseif (jssupportticket::$_data[0]->status == 1) {
        $style = "orange;";
        $status = __('Waiting reply', 'js-support-ticket');
    } elseif (jssupportticket::$_data[0]->status == 3) {
        $style = "green;";
        $status = __('Replied', 'js-support-ticket');
    } elseif (jssupportticket::$_data[0]->status == 4) {
        $style = "blue;";
        $status = __('Closed', 'js-support-ticket');
    }
    $cur_uid = get_current_user_id();
    ?>	

    <div id="popup-back" style="display:none;"> </div>
    <div id="userpopup" style="display:none;">
        <div class="js-row">
        </div>      
    </div>

    <div class="js-col-md-12 js-ticket-detail-wrapper">
        <div class="js-row js-ticket-topbar">
            <div class="js-col-md-5 js-openclosed">
                <div class="js-col-md-4 js-ticket-openclosed">
                    <?php
                    $field_array = JSSTincluder::getJSModel('fieldordering')->getFieldTitleByFieldfor(1);
                    if (jssupportticket::$_data[0]->status == 4)
                        $ticketmessage = __('Closed', 'js-support-ticket');
                    else
                        $ticketmessage = __('Open', 'js-support-ticket');
                    echo $ticketmessage;
                    ?>
                </div>
                <div class="js-col-md-8">
                    <?php
                    echo __('Created', 'js-support-ticket') . ' ';
                    $startTimeStamp = strtotime(jssupportticket::$_data[0]->created);
                    $endTimeStamp = strtotime("now");
                    $timeDiff = abs($endTimeStamp - $startTimeStamp);
                    $numberDays = $timeDiff / 86400;  // 86400 seconds in one day
                    // and you might want to convert to integer
                    $numberDays = intval($numberDays);
                    if ($numberDays != 0 && $numberDays == 1) {
                        $day_text = __('Day', 'js-support-ticket');
                    } elseif ($numberDays > 1) {
                        $day_text = __('Days', 'js-support-ticket');
                    } elseif ($numberDays == 0) {
                        $day_text = __('Today', 'js-support-ticket');
                    }
                    if ($numberDays == 0) {
                        echo $day_text;
                    } else {
                        echo $numberDays . ' ' . $day_text . ' ';
                        echo __('Ago', 'js-support-ticket');
                    }
                    echo ' ' . date_i18n("d F, Y, h:i:s A", strtotime(jssupportticket::$_data[0]->created));
                    ?>
                </div>
            </div>
            <div class="js-col-md-4">
                <div class="js-row js-margin-bottom">
                    <div class="js-col-xs-6 js-col-md-6 js-ticket-title"><?php echo __('Ticket ID', 'js-support-ticket'); ?></div>
                    <div class="js-col-xs-6 js-col-md-5 js-ticket-value js-border-box"><?php echo jssupportticket::$_data[0]->ticketid; ?></div>
                </div>
                <div class="js-row">
                    <div class="js-col-xs-6 js-col-md-6 js-ticket-title"><?php echo __($field_array['priority'], 'js-support-ticket'); ?></div>
                    <div class="js-col-xs-6 js-col-md-5 js-ticket-value js-ticket-wrapper-textcolor" style="background:<?php echo jssupportticket::$_data[0]->prioritycolour; ?>;"><?php echo __(jssupportticket::$_data[0]->priority, 'js-support-ticket'); ?></div>
                </div>
            </div>
            <div class="js-col-md-3 js-last-left">
                <div class="js-row">
                    <div class="js-col-xs-6 js-col-md-6 js-ticket-title"><?php echo __('Last Reply', 'js-support-ticket'); ?></div>
                    <div class="js-col-xs-6 js-col-md-6 js-ticket-value"><?php
                        if (empty(jssupportticket::$_data[0]->lastreply) || jssupportticket::$_data[0]->lastreply == '0000-00-00 00:00:00')
                            echo __('No Last Reply', 'js-support-ticket');
                        else
                            echo date_i18n(jssupportticket::$_config['date_format'], strtotime(jssupportticket::$_data[0]->lastreply));
                        ?></div>
                </div>
                <div class="js-row">
                    <div class="js-col-xs-6 js-col-md-6 js-ticket-title"><?php echo __($field_array['status'], 'js-support-ticket'); ?></div>
                    <div class="js-col-xs-6 js-col-md-6 js-ticket-value">
                        <?php
                        $printstatus = 1;
                        if ($printstatus == 1) {
                            echo $ticketmessage;
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="js-row js-ticket-middlebar">
            <div class="js-col-xs-12 js-col-md-9 js-admin-xs-nullpadding">
                <div class="js-col-xs-12 js-col-md-12" style = "margin-bottom:5px;">
                    <span class="js-ticket-title"><?php echo __($field_array['subject'], 'js-support-ticket'); ?>&nbsp;:&nbsp;</span>
                    <span class="js-ticket-value"><?php echo jssupportticket::$_data[0]->subject; ?></span>
                </div>
                <div class="js-col-xs-12 js-col-md-12" style = "margin-bottom:5px;" >
                    <span class="js-ticket-title"><?php echo __('From', 'js-support-ticket'); ?>&nbsp;:&nbsp;</span>
                    <span class="js-ticket-value"><?php echo jssupportticket::$_data[0]->name; ?></span>
                </div>
                <div class="js-col-xs-12 js-col-md-12" style = "margin-bottom:5px;">
                    <span class="js-ticket-title"><?php echo __($field_array['department'], 'js-support-ticket'); ?>&nbsp;:&nbsp;</span>
                    <span class="js-ticket-value"><?php echo __(jssupportticket::$_data[0]->departmentname, 'js-support-ticket'); ?></span>
                </div>
                <?php
                    $customfields = JSSTincluder::getObjectClass('customfields')->userFieldsData(1);
                    foreach ($customfields as $field) {
                        echo JSSTincluder::getObjectClass('customfields')->showCustomFields($field,2, jssupportticket::$_data[0]->params);
                    }
                ?>
                <div class="js-col-xs-12 js-col-md-12" style = "margin-bottom:5px;">
                    <span class="js-ticket-value"><?php echo (jssupportticket::$_data[0]->ticketviaemail) ? __('Created via Email', 'js-support-ticket') : ''; ?></span>
                </div>
            </div>
            <div class="js-col-xs-12 js-col-md-3 js-button-margin">
                <a class="button" href="?page=ticket&jstlay=addticket&jssupportticketid=<?php echo jssupportticket::$_data[0]->id; ?>"><img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/edit.png" title="<?php echo __('Edit Ticket', 'js-support-ticket'); ?>" /></a>
                <?php if (jssupportticket::$_data[0]->status != 4) { ?>
                    <a class="button" href="#" onclick="actionticket(2);"><img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/close.png" title="<?php echo __('Close Ticket', 'js-support-ticket'); ?>" /></a>
                <?php } else { ?>
                    <a class="button" href="#" onclick="actionticket(3);"><img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/reopen.png" title="<?php echo __('Reopen Ticket', 'js-support-ticket'); ?>" /></a>
    <?php } ?>
                <a class="button" href="#" id="showaction"><img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/down.png" title="<?php echo __('More Option', 'js-support-ticket'); ?>" /></a>
            </div>
        </div>
        <!-- ACTION AREA  -->
        <form method="post" action="<?php echo admin_url("admin.php?page=ticket&task=actionticket"); ?>" id="adminTicketform" enctype="multipart/form-data">
            <div class="js-col-md-12" id="action-div" style="display:none;">
                <div class="js-row">
                    <div class="js-col-md-6 js-col-xs-6"><?php echo JSSTformfield::select('priority', JSSTincluder::getJSModel('priority')->getPriorityForCombobox(), jssupportticket::$_data[0]->priorityid, __('Change Priority', 'js-support-ticket'), array()); ?></div>
                    <div class="js-col-md-6 js-col-xs-6"><?php echo JSSTformfield::button('changepriority', __('Change Priority', 'js-support-ticket'), array('class' => 'changeprioritybutton', 'onclick' => 'actionticket(1);')); ?></div>
                </div>
            </div>
            <?php echo JSSTformfield::hidden('actionid', ''); ?>
            <?php echo JSSTformfield::hidden('ticketid', jssupportticket::$_data[0]->id); ?>
            <?php echo JSSTformfield::hidden('uid', get_current_user_id()); ?>
            <?php echo JSSTformfield::hidden('action', 'reply_savereply'); ?>
    <?php echo JSSTformfield::hidden('form_request', 'jssupportticket'); ?>
        </form>  <!--END of action Area --> 
    </div>
    <div class="js-col-md-12 js-ticket-detail-wrapper">
        <div class="js-row js-ticket-requester"><?php echo __('Requester Info', 'js-support-ticket'); ?></div>
        <div class="js-row js-ticket-bottombar">
            <div class="js-col-md-5">
                <?php
                if (isset(jssupportticket::$_data[0]->uid) && !empty(jssupportticket::$_data[0]->uid)) {
                    echo get_avatar(jssupportticket::$_data[0]->uid,20);
                } else { ?>
                    <img src="<?php echo jssupportticket::$_pluginpath . 'includes/images/smallticketman.png'; ?>" />
                <?php } ?>                            
                <?php echo jssupportticket::$_data[0]->name; ?>
            </div>
            <div class="js-col-md-5">
                <img src="<?php echo jssupportticket::$_pluginpath . 'includes/images/smallmail.png'; ?>" />
                <?php echo jssupportticket::$_data[0]->email; ?>
            </div>
            <div class="js-col-md-2">
                <a href="#" id="showhidedetail" data-divid="js-hidden-ticket-data"><img class="js-showdetail" src="<?php echo jssupportticket::$_pluginpath . 'includes/images/showhide.png'; ?>" /><?php echo __('Show Detail', 'js-support-ticket'); ?></a>
            </div>
            <div id="js-hidden-ticket-data">
                <div class="js-row js-ticket-requester"><?php echo __('More Detail', 'js-support-ticket'); ?></div>
                <div class="js-row">
                    <div class="js-col-md-4 js-ticket-moredetail">
                        <div class="js-col-md-6 js-ticket-data-title"><?php echo __($field_array['phone'], 'js-support-ticket'); ?></div>
                        <div class="js-col-md-6 js-ticket-data-value"><?php if (!empty(jssupportticket::$_data[0]->phone))
        echo jssupportticket::$_data[0]->phone;
    else
        echo __("Not Filled", 'js-support-ticket');
    ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Tickect internal Note Area -->


    <!-- Tickect  Reply  Area -->
    <span class="js-admin-title"><?php echo __('Ticket Thread', 'js-support-ticket'); ?></span>
    <div class="js-col-md-2 js-col-xs-3 js-ticket-thread-pic">
        <?php
        if (isset(jssupportticket::$_data[0]->uid) && !empty(jssupportticket::$_data[0]->uid)) {
            echo get_avatar(jssupportticket::$_data[0]->uid);
        } else { ?>
            <img src="<?php echo jssupportticket::$_pluginpath . 'includes/images/ticketman.png'; ?>" />
        <?php } ?>                            
    </div>
    <div class="js-col-md-10 js-col-xs-9 js-ticket-thread-wrapper colored">
        <div class="js-ticket-detail-corner"></div>
        <div class="js-ticket-thread-upperpart">
            <span class="js-ticket-thread-replied"><?php echo __('Replied By', 'js-support-ticket'); ?>&nbsp;:&nbsp;</span>
            <span class="js-ticket-thread-person"><?php echo jssupportticket::$_data[0]->name; ?></span>
            <span class="js-ticket-thread-date">(&nbsp;<?php echo date_i18n("l F d, Y, h:i:s", strtotime(jssupportticket::$_data[0]->created)); ?>&nbsp;)</span>
        </div>
        <div class="js-ticket-thread-middlepart">
        <?php echo jssupportticket::$_data[0]->message; ?>
        </div>
        <?php
        if (!empty(jssupportticket::$_data['ticket_attachment'])) {
            foreach (jssupportticket::$_data['ticket_attachment'] AS $attachment) {
                $path = admin_url("?page=ticket&action=jstask&task=downloadbyid&id=".$attachment->id);
                echo '
            <div class="js_ticketattachment">
              ' . $attachment->filename . ' ( ' . $attachment->filesize . ' ) ' . '              
              <a class="button" target="_blank" href="' . $path . '">' . __('Download', 'js-support-ticket') . '</a>
            </div>';
            }
        }
        ?>
    </div>
    <?php
    $colored = "colored";
    if (!empty(jssupportticket::$_data[4]))
        foreach (jssupportticket::$_data[4] AS $reply) {
            if ($cur_uid == $reply->uid)
                $colored = '';
            ?>
            <div class="js-col-md-2 js-col-xs-3 js-ticket-thread-pic">
                <?php
                if (isset($reply->uid) && !empty($reply->uid)) {
                    echo get_avatar($reply->uid);
                } else { ?>
                    <img src="<?php echo jssupportticket::$_pluginpath . '/includes/images/ticketmanbig.png'; ?>" />
                <?php } ?>                            
            </div>
            <div class="js-col-md-10 js-col-xs-9 js-ticket-thread-wrapper <?php echo $colored; ?>">
                <div class="js-ticket-detail-corner"></div>
                <div class="js-ticket-thread-upperpart">
                    <span class="js-ticket-thread-replied"><?php echo __('Replied By', 'js-support-ticket'); ?>&nbsp;:&nbsp;</span>
                    <span class="js-ticket-thread-person"><?php echo $reply->name; ?></span>
                    <span class="js-ticket-thread-date">(&nbsp;<?php echo date_i18n("l F d, Y, h:i:s", strtotime($reply->created)); ?>&nbsp;)</span>
                    <span class="js-ticket-via-email"><?php echo ($reply->ticketviaemail == 1) ? __('Created via Email', 'js-support-ticket') : ''; ?></span>
                </div>
                <div class="js-ticket-thread-middlepart">
                <?php echo $reply->message; ?>
                </div>
                <?php
                if (!empty($reply->attachments)) {
                    foreach ($reply->attachments AS $attachment) {
                        $path = admin_url("?page=ticket&action=jstask&task=downloadbyid&id=".$attachment->id);
                        echo '
                  <div class="js_ticketattachment">
                    ' . $attachment->filename . ' ( ' . $attachment->filesize . ' ) ' . '
                    <a class="button" target="_blank" href="' . $path . '">' . __('Download', 'js-support-ticket') . '</a>
                  </div>';
                    }
                }
                ?>
            </div>
        <?php } ?>

    <form method="post" action="<?php echo admin_url("admin.php?page=reply&task=savereply"); ?>"  enctype="multipart/form-data">
        <span class="js-admin-title"><?php echo __('Post Reply', 'js-support-ticket'); ?></span>
        <div class="js-form-wrapper">
            <div class="js-form-title"><label id="responcemsg" for="responce"><?php echo __('Response', 'js-support-ticket'); ?><font color="red">*</font></label></div>
            <div class="js-form-field"><?php echo wp_editor('', 'jsticket_message', array('media_buttons' => false)); ?></div>
        </div>
        <div class="js-form-wrapper">
            <div class="js-form-title"><?php echo __('Attachments', 'js-support-ticket'); ?></div>
            <div class="js-form-field">
                <div class="tk_attachment_value_wrapperform">
                    <span class="tk_attachment_value_text">
                        <input type="file" class="inputbox" name="filename[]" onchange="uploadfile(this, '<?php echo jssupportticket::$_config['file_maximum_size']; ?>', '<?php echo jssupportticket::$_config['file_extension']; ?>');" size="20" maxlenght='30'/>
                        <span class='tk_attachment_remove'></span>
                    </span>
                </div>
                <span class="tk_attachments_configform">
                    <small><?php
    __('Maximum File Size', 'js-support-ticket');
    echo ' (' . jssupportticket::$_config['file_maximum_size'];
    ?>KB)<br><?php
    __('File Extension Type', 'js-support-ticket');
    echo ' (' . jssupportticket::$_config['file_extension'] . ')';
    ?></small>
                </span>
                <div class="js-md-col-12"><span id="tk_attachment_add" class="tk_attachments_addform"><?php echo __('Add More File', 'js-support-ticket'); ?></span></div>
            </div>
        </div>
        <div class="js-form-wrapper">
            <div class="js-form-title"><?php echo __('Append Signature', 'js-support-ticket'); ?></div>
            <div class="js-form-field">
                <?php echo JSSTformfield::checkbox('departmentsignature', array('1' => __('Department Signature', 'js-support-ticket')), '', array('class' => 'radiobutton')); ?>
                <?php echo JSSTformfield::checkbox('nonesignature', array('1' => __('None', 'js-support-ticket')), '', array('class' => 'radiobutton')); ?>
            </div>
        </div>
        <div class="js-form-wrapper">
            <div class="js-form-title"><?php echo __('Ticket Status', 'js-support-ticket'); ?></div>
            <div class="js-form-field">
        <?php echo JSSTformfield::checkbox('closeonreply', array('1' => __('Close on reply', 'js-support-ticket')), '', array('class' => 'radiobutton')); ?>
            </div>
        </div>
        <div class="js-form-button">
        <?php echo JSSTformfield::submitbutton('postreply', __('Post Reply', 'js-support-ticket'), array('class' => 'button', 'onclick' => "return checktinymcebyid('message');")); ?>
        </div>
    <?php echo JSSTformfield::hidden('departmentid', jssupportticket::$_data[0]->departmentid); ?>
    <?php echo JSSTformfield::hidden('ticketid', jssupportticket::$_data[0]->id); ?>
    <?php echo JSSTformfield::hidden('uid', get_current_user_id()); ?>
    <?php echo JSSTformfield::hidden('action', 'reply_savereply'); ?>
    <?php echo JSSTformfield::hidden('form_request', 'jssupportticket'); ?>
    </form>   

    <?php
} else {
    JSSTlayout::getNoRecordFound();
}
?>
