<?php
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
?>
<script type="text/javascript">
    function resetFrom() {
        var form = jQuery('form#jssupportticketform');
        form.find("input[type=text], input[type=email], input[type=password], textarea").val("");
        form.find('input:checkbox').removeAttr('checked');
        form.find('select').prop('selectedIndex', 0);
        form.find('input[type="radio"]').prop('checked', false);
        document.getElementById('subject').value = '';
        document.getElementById('name').value = '';
        document.getElementById('email').value = '';
        document.getElementById('ticketid').value = '';
        document.getElementById('jssupportticketform').submit();
    }
    jQuery(document).ready(function($){
        $('.date').datepicker({dateFormat: 'yy-mm-dd'});
    });
</script>
<?php JSSTmessage::getMessage(); ?>
<span class="js-admin-title"><?php echo __('Tickets', 'js-support-ticket'); ?></span>
<?php
$list = JSSTrequest::getVar('list', null, 1);
$open = ($list == 1) ? 'active' : '';
$answered = ($list == 2) ? 'active' : '';
$closed = ($list == 4) ? 'active' : '';
$alltickets = ($list == 5) ? 'active' : '';
?>
<form class="js-filter-form" name="jssupportticketform" id="jssupportticketform" method="post" action="<?php echo admin_url("admin.php?page=ticket&jstlay=tickets&list=" . $list); ?>">
    <?php echo JSSTformfield::text('subject', jssupportticket::$_data['filter']['subject'], array('placeholder' => __('Subject', 'js-support-ticket'))); ?>
    <?php echo JSSTformfield::text('name', jssupportticket::$_data['filter']['name'], array('placeholder' => __('From', 'js-support-ticket'))); ?>
    <?php echo JSSTformfield::text('email', jssupportticket::$_data['filter']['email'], array('placeholder' => __('Email', 'js-support-ticket'))); ?>
    <?php echo JSSTformfield::select('departmentid', JSSTincluder::getJSModel('department')->getDepartmentForCombobox(), jssupportticket::$_data['filter']['departmentid'], __('Select department','js-support-ticket')); ?>
    <?php echo JSSTformfield::select('priorityid', JSSTincluder::getJSModel('priority')->getPriorityForCombobox(), jssupportticket::$_data['filter']['priorityid'], __('Select priority','js-support-ticket')); ?>
    <?php echo JSSTformfield::text('datestart', jssupportticket::$_data['filter']['datestart'], array('placeholder' => __('Date start', 'js-support-ticket'), 'class' => 'date')); ?>
    <?php echo JSSTformfield::text('dateend', jssupportticket::$_data['filter']['dateend'], array('placeholder' => __('Date end', 'js-support-ticket'), 'class' => 'date')); ?>
    <?php echo JSSTformfield::text('ticketid', jssupportticket::$_data['filter']['ticketid'], array('placeholder' => __('Ticket ID', 'js-support-ticket'))); ?>
    <?php echo JSSTformfield::hidden('JSST_form_search', 'JSST_SEARCH'); ?>
    <?php 
        $customfields = JSSTincluder::getObjectClass('customfields')->userFieldsForSearch(1);
        foreach ($customfields as $field) {
            JSSTincluder::getObjectClass('customfields')->formCustomFieldsForSearch($field, $k, 1);
        }
    ?>
    <?php echo JSSTformfield::submitbutton('go', __('Search', 'js-support-ticket'), array('class' => 'button')); ?>
    <?php echo JSSTformfield::button(__('Reset', 'js-support-ticket'), __('Reset', 'js-support-ticket'), array('class' => 'button', 'onclick' => 'resetFrom();')); ?>
</form>
<a class="js-add-link button" href="?page=ticket&jstlay=addticket"><img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/add_icon.png" /><?php echo __('Add Ticket', 'js-support-ticket'); ?></a>
<div class="js-col-md-12" style = "margin-bottom:10px;margin-top:10px;">
    <div class="js-col-md-2 js-myticket-link js-col-md-offset-2">
        <a class="js-myticket-link <?php echo $open; ?>" href="<?php echo admin_url("admin.php?page=ticket&jstlay=tickets&list=1"); ?>" >
            <?php
            echo __('Open', 'js-support-ticket');
            if (jssupportticket::$_config['count_on_myticket'] == 1)
                echo ' ( ' . jssupportticket::$_data['count']['openticket'] . ' )';
            ?>
        </a>
    </div>
    <div class="js-col-md-2 js-myticket-link">
        <a class="js-myticket-link <?php echo $answered; ?>" href="<?php echo admin_url("admin.php?page=ticket&jstlay=tickets&list=2"); ?>" >
            <?php
            echo __('Answered', 'js-support-ticket');
            if (jssupportticket::$_config['count_on_myticket'] == 1)
                echo ' ( ' . jssupportticket::$_data['count']['answeredticket'] . ' )';
            ?>
        </a>
    </div>
    <div class="js-col-md-2 js-myticket-link">
        <a class="js-myticket-link <?php echo $closed; ?>" href="<?php echo admin_url("admin.php?page=ticket&jstlay=tickets&list=4"); ?>" >
            <?php
            echo __('Closed', 'js-support-ticket');
            if (jssupportticket::$_config['count_on_myticket'] == 1)
                echo ' ( ' . jssupportticket::$_data['count']['closedticket'] . ' )';
            ?>
        </a>
    </div>
    <div class="js-col-md-2 js-myticket-link">
        <a class="js-myticket-link <?php echo $alltickets; ?>" href="<?php echo admin_url("admin.php?page=ticket&jstlay=tickets&list=5"); ?>" >
            <?php
            echo __('All Tickets', 'js-support-ticket');
            if (jssupportticket::$_config['count_on_myticket'] == 1)
                echo ' ( ' . jssupportticket::$_data['count']['allticket'] . ' )';
            ?>
        </a>
    </div>
</div>

<?php
$link = '?page=ticket';
if (jssupportticket::$_sortorder == 'ASC')
    $img = "sort0.png";
else
    $img = "sort1.png";
?>
<div class="js-admin-sorting js-col-md-12">
    <span class="js-col-md-2 js-admin-sorting-link"><a href="<?php echo $link ?>&sortby=<?php echo jssupportticket::$_sortlinks['subject']; ?>" class="<?php if (jssupportticket::$_sorton == 'subject') echo 'selected' ?>"><?php echo __('Subject', 'js-support-ticket'); ?><?php if (jssupportticket::$_sorton == 'subject') { ?> <img src="<?php echo jssupportticket::$_pluginpath . 'includes/images/' . $img ?>"> <?php } ?></a></span>
    <span class="js-col-md-2 js-admin-sorting-link"><a href="<?php echo $link ?>&sortby=<?php echo jssupportticket::$_sortlinks['priority']; ?>" class="<?php if (jssupportticket::$_sorton == 'priority') echo 'selected' ?>"><?php echo __('Priority', 'js-support-ticket'); ?><?php if (jssupportticket::$_sorton == 'priority') { ?> <img src="<?php echo jssupportticket::$_pluginpath . 'includes/images/' . $img ?>"> <?php } ?></a></span>
    <span class="js-col-md-2 js-admin-sorting-link"><a href="<?php echo $link ?>&sortby=<?php echo jssupportticket::$_sortlinks['ticketid']; ?>" class="<?php if (jssupportticket::$_sorton == 'ticketid') echo 'selected' ?>"><?php echo __('Ticket ID', 'js-support-ticket'); ?><?php if (jssupportticket::$_sorton == 'ticketid') { ?> <img src="<?php echo jssupportticket::$_pluginpath . 'includes/images/' . $img ?>"> <?php } ?></a></span>
    <span class="js-col-md-2 js-admin-sorting-link"><a href="<?php echo $link ?>&sortby=<?php echo jssupportticket::$_sortlinks['isanswered']; ?>" class="<?php if (jssupportticket::$_sorton == 'isanswered') echo 'selected' ?>"><?php echo __('Answered', 'js-support-ticket'); ?><?php if (jssupportticket::$_sorton == 'isanswered') { ?> <img src="<?php echo jssupportticket::$_pluginpath . 'includes/images/' . $img ?>"> <?php } ?></a></span>
    <span class="js-col-md-2 js-admin-sorting-link"><a href="<?php echo $link ?>&sortby=<?php echo jssupportticket::$_sortlinks['status']; ?>" class="<?php if (jssupportticket::$_sorton == 'status') echo 'selected' ?>"><?php echo __('Status', 'js-support-ticket'); ?><?php if (jssupportticket::$_sorton == 'status') { ?> <img src="<?php echo jssupportticket::$_pluginpath . 'includes/images/' . $img ?>"> <?php } ?></a></span>
    <span class="js-col-md-2 js-admin-sorting-link"><a href="<?php echo $link ?>&sortby=<?php echo jssupportticket::$_sortlinks['created']; ?>" class="<?php if (jssupportticket::$_sorton == 'created') echo 'selected' ?>"><?php echo __('Created', 'js-support-ticket'); ?><?php if (jssupportticket::$_sorton == 'created') { ?> <img src="<?php echo jssupportticket::$_pluginpath . 'includes/images/' . $img ?>"> <?php } ?></a></span>
</div>
<?php
if (!empty(jssupportticket::$_data[0])) {
    $field_array = JSSTincluder::getJSModel('fieldordering')->getFieldTitleByFieldfor(1);
    ?>
    <!-- Tabs Area -->
    <?php
    foreach (jssupportticket::$_data[0] AS $ticket) {
        if ($ticket->status == 0) {
            $style = "#9ACC00;";
            $status = __('New', 'js-support-ticket');
        } elseif ($ticket->status == 1) {
            $style = "#217ac3;";
            $status = __('Waiting Your Reply', 'js-support-ticket');
        } elseif ($ticket->status == 2) {
            $style = "#FE7C2C;";
            $status = __('In Progress', 'js-support-ticket');
        } elseif ($ticket->status == 3) {
            $style = "#FFB613;";
            $status = __('Waiting Customer Reply', 'js-support-ticket');
        } elseif ($ticket->status == 4) {
            $style = "#F04646;";
            $status = __('Closed', 'js-support-ticket');
        }
        ?>  		
        <div class="js-col-xs-12 js-col-md-12 js-ticket-wrapper">
            <div class="js-col-xs-12 js-col-md-12 js-ticket-toparea">
                <div class="js-col-xs-2 js-col-md-1 js-ticket-pic">
                    <?php if(isset($ticket->uid) && !empty($ticket->uid)){
                            echo get_avatar($ticket->uid);
                        }else{ ?>
                        <img src="<?php echo jssupportticket::$_pluginpath . 'includes/images/ticketman.png'; ?>" />
                    <?php } ?>                            
                </div>
                <div class="js-col-xs-10 js-col-md-8 js-ticket-data js-nullpadding">
                    <div class="js-col-xs-12 js-col-md-12 js-ticket-body-data-elipses">
                        <span class="js-ticket-title"><?php echo __($field_array['subject'], 'js-support-ticket'); ?>&nbsp;:&nbsp;</span>
                        <a href="?page=ticket&jstlay=ticketdetail&jssupportticketid=<?php echo $ticket->id; ?>"><?php echo $ticket->subject; ?></a>
                    </div>
                    <div class="js-col-xs-12 js-col-md-12">
                        <span class="js-ticket-title"><?php echo __('From', 'js-support-ticket'); ?>&nbsp;:&nbsp;</span>
                        <span class="js-ticket-value"><?php echo $ticket->name; ?></span>
                    </div>
                    <div class="js-col-xs-12 js-col-md-12">
                        <span class="js-ticket-title"><?php echo __($field_array['department'], 'js-support-ticket'); ?>&nbsp;:&nbsp;</span>
                        <span class="js-ticket-value"><?php echo $ticket->departmentname; ?></span>
                    </div>
                    <?php
                            jssupportticket::$_data['ticketid'] = $ticket->id;
                            $customfields = JSSTincluder::getObjectClass('customfields')->userFieldsData(1, 1);
                            foreach ($customfields as $field) {
                                echo JSSTincluder::getObjectClass('customfields')->showCustomFields($field,1, $ticket->params);
                            }
                            ?>
                    <span class="js-ticket-status" style="background:<?php echo $style; ?>">
                        <?php
                        $counter = 'one';
                        if ($ticket->lock == 1) {
                            ?>
                            <img class="ticketstatusimage <?php echo $counter;
                $counter = 'two';
                            ?>" src="<?php echo jssupportticket::$_pluginpath . "includes/images/lockstatus.png"; ?>" title="<?php echo __('Ticket Locked', 'js-support-ticket'); ?>" />
                             <?php } ?>
        <?php echo $status; ?>
                    </span>
                </div>
                <div class="js-col-xs-12 js-col-md-3 js-ticket-data1">
                    <div class="js-row">
                        <div class="js-col-xs-6 js-col-md-6"><?php echo __('Ticket ID', 'js-support-ticket'); ?></div>
                        <div class="js-col-xs-6 js-col-md-6"><?php echo $ticket->ticketid; ?></div>
                    </div>
                    <div class="js-row">
                        <div class="js-col-xs-6 js-col-md-6"><?php echo __('Last Reply', 'js-support-ticket'); ?></div>
                        <div class="js-col-xs-6 js-col-md-6"><?php
                            if (empty($ticket->lastreply) || $ticket->lastreply == '0000-00-00 00:00:00')
                                echo __('No Last Reply', 'js-support-ticket');
                            else
                                echo date_i18n(jssupportticket::$_config['date_format'], strtotime($ticket->lastreply));
                            ?></div>
                    </div>
                    <div class="js-row">
                        <div class="js-col-xs-6 js-col-md-6"><?php echo __('Priority', 'js-support-ticket'); ?></div>
                        <div class="js-col-xs-6 js-col-md-6 js-ticket-wrapper-textcolor" style="background:<?php echo $ticket->prioritycolour; ?>;"><?php echo __($ticket->priority, 'js-support-ticket'); ?></div>
                    </div>
                </div>
                <div class="js-ticket-bottom-line"></div>
            </div>
            <div class="js-col-xs-12 js-col-md-12 js-ticket-bottom-data-part">
                <span class="js-ticket-created"><?php echo __('Created', 'js-support-ticket'); ?>&nbsp;:&nbsp;<?php echo date_i18n(jssupportticket::$_config['date_format'], strtotime($ticket->created)); ?></span>
                <div class="js-ticket-datapart-buttons-action">	
                    <a class="js-ticket-bottom-data-part-action-button button"  onclick="return confirm('<?php echo __('Are you sure to enforce delete', 'js-support-ticket'); ?>');" href="?page=ticket&task=enforcedeleteticket&action=jstask&ticketid=<?php echo $ticket->id; ?>"><img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/close.png" /><?php echo __('Enforce delete', 'js-support-ticket'); ?></a>
                    <a class="js-ticket-bottom-data-part-action-button button"  onclick="return confirm('<?php echo __('Are you sure to delete', 'js-support-ticket'); ?>');" href="?page=ticket&task=deleteticket&action=jstask&ticketid=<?php echo $ticket->id; ?>"><img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/remove.png" /><?php echo __('Delete', 'js-support-ticket'); ?></a>
                    <a class="js-ticket-bottom-data-part-action-button button" href="?page=ticket&jstlay=addticket&jssupportticketid=<?php echo $ticket->id; ?>"><img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/edit.png" /><?php echo __('Edit', 'js-support-ticket'); ?></a>
                </div>
                <div class="js-ticket-datapart-buttons-detail">
                    <a class="js-ticket-bottom-data-part-action-button button" href="?page=ticket&jstlay=ticketdetail&jssupportticketid=<?php echo $ticket->id; ?>"><img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/detail.png" /><?php echo __('Ticket Detail', 'js-support-ticket'); ?></a>
                </div>
            </div>
        </div>
        <?php
    }
    if (jssupportticket::$_data[1]) {
        echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . jssupportticket::$_data[1] . '</div></div>';
    }
} else {
    JSSTlayout::getNoRecordFound();
}
?>
