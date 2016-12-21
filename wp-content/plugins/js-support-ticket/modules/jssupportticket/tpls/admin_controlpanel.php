<script type="text/javascript" src="https://www.google.com/jsapi?autoload={'modules':[{'name':'visualization','version':'1','packages':['corechart']}]}"></script>
<script>
            google.setOnLoadCallback(drawStackChartHorizontal);
            function drawStackChartHorizontal() {
            var data = google.visualization.arrayToDataTable([
<?php
echo jssupportticket::$_data['stack_chart_horizontal']['title'] . ',';
echo jssupportticket::$_data['stack_chart_horizontal']['data'];
?>
            ]);
                    var view = new google.visualization.DataView(data);
                    var options = {
                    height:250,
                            legend: { position: 'top', maxLines: 3 },
                            bar: { groupWidth: '75%' },
                            isStacked: true
                    };
                    var chart = new google.visualization.BarChart(document.getElementById("stack_chart_horizontal"));
                    chart.draw(view, options);
            }
</script>
<span class="js-admin-title"><?php echo __('Statistics', 'js-support-ticket'); ?>&nbsp;<small>
        <?php
        $curdate = date_i18n('Y-m-d');
        $fromdate = date_i18n('Y-m-d', strtotime("now -1 month"));
        echo " ($fromdate - $curdate)";
        ?></small>
    <div class="right_side">
        <?php
        $url = 'http://www.joomsky.com/jssupportticketsys/wordpress/getlatestversion.php';
        $pvalue = "dt=" . date('Y-m-d');
        if (in_array('curl', get_loaded_extensions())) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 8);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $pvalue);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
            $curl_errno = curl_errno($ch);
            $curl_error = curl_error($ch);
            $result = curl_exec($ch);
            curl_close($ch);
            if ($result == str_replace('.', '', jssupportticket::$_data['version'])) {
                ?>
                <img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/latestversion.png" height="35" width="35" title="<?php echo __('Your System Is Up To Date', 'js-support-ticket'); ?>">
                <?php echo __('Your System Is Up To Date', 'js-support-ticket'); ?>
                </a>
            <?php } elseif ($result) {
                ?>
                <img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/newversion.png" height="35" width="35" title="<?php echo __('New Version Is Available', 'js-support-ticket'); ?>">
                <?php echo __('New Version Is Available', 'js-support-ticket'); ?>
            <?php } else {
                ?>
                <img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/unableconnect.png" height="35" width="35" title="<?php echo __('Unable Connect To Server', 'js-support-ticket'); ?>">
                <?php echo __('Unable Connect To Server', 'js-support-ticket'); ?>
                <?php
            }
        } else {
            ?>
            <img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/unableconnect.png" height="35" width="35" title="<?php echo __('Unable Connect To Server', 'js-support-ticket'); ?>">
            <?php echo __('Unable Connect To Server', 'js-support-ticket'); ?>
            <?php
        }
        ?>
    </div>

</span>
<div class="js-wrapper">
    <div class="js-col-md-12">
        <div id="stack_chart_horizontal" style="width:100%;"></div>
    </div>
    <div class="js-admin-report-box-wrapper js-admin-controlpanel">
        <div class="js-col-md-4 js-admin-box js-col-md-offset-2 box1" >
            <div class="js-col-md-4 js-admin-box-image">
                <img src="<?php echo jssupportticket::$_pluginpath; ?>/includes/images/report/ticket_icon.png" />
            </div>
            <div class="js-col-md-8 js-admin-box-content">
                <div class="js-col-md-12 js-admin-box-content-number"><?php echo jssupportticket::$_data['ticket_total']['openticket']; ?></div>
                <div class="js-col-md-12 js-admin-box-content-label"><?php echo __('New', 'js-support-ticket'); ?></div>
            </div>
            <div class="js-col-md-12 js-admin-box-label"></div>
        </div>  
        <div class="js-col-md-4 js-admin-box box2">
            <div class="js-col-md-4 js-admin-box-image">
                <img src="<?php echo jssupportticket::$_pluginpath; ?>/includes/images/report/ticket_answered.png" />
            </div>
            <div class="js-col-md-8 js-admin-box-content">
                <div class="js-col-md-12 js-admin-box-content-number"><?php echo jssupportticket::$_data['ticket_total']['answeredticket']; ?></div>
                <div class="js-col-md-12 js-admin-box-content-label"><?php echo __('Answered', 'js-support-ticket'); ?></div>
            </div>
            <div class="js-col-md-12 js-admin-box-label"></div>
        </div>  
        <div class="js-col-md-4 js-admin-box box3">
            <div class="js-col-md-4 js-admin-box-image">
                <img src="<?php echo jssupportticket::$_pluginpath; ?>/includes/images/report/ticket_pending.png" />
            </div>
            <div class="js-col-md-8 js-admin-box-content">
                <div class="js-col-md-12 js-admin-box-content-number"><?php echo jssupportticket::$_data['ticket_total']['pendingticket']; ?></div>
                <div class="js-col-md-12 js-admin-box-content-label"><?php echo __('Pending', 'js-support-ticket'); ?></div>
            </div>
            <div class="js-col-md-12 js-admin-box-label"></div>
        </div>  
    </div>

</div>
<span class="js-admin-title"><?php echo __('Control Panel', 'js-support-ticket'); ?></span>
<div class="js-wrapper">
    <div class="js-box-wrapper js-1">
        <a class="js-admin-link" href="?page=ticket&jstlay=tickets"><div class="js-box"><div class="img-wrapper"><img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/ticket.png"/></div><div class="text-wrapper"><?php echo __('Tickets', 'js-support-ticket'); ?></div></div></a>
        <a class="js-admin-link" href="?page=configuration&jstlay=configurations"><div class="js-box"><div class="img-wrapper"><img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/configuration.png"/></div><div class="text-wrapper"><?php echo __('Configurations', 'js-support-ticket'); ?></div></div></a>
        <a class="js-admin-link" href="?page=department&jstlay=departments"><div class="js-box"><div class="img-wrapper"><img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/departments.png"/></div><div class="text-wrapper"><?php echo __('Departments', 'js-support-ticket'); ?></div></div></a>
        <a class="js-admin-link" href="?page=priority&jstlay=priorities"><div class="js-box"><div class="img-wrapper"><img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/priority.png"/></div><div class="text-wrapper"><?php echo __('Priorities', 'js-support-ticket'); ?></div></div></a>
    </div>
    <div class="js-box-wrapper js-2">
        <a class="js-admin-link" href="?page=email&jstlay=emails"><div class="js-box"><div class="img-wrapper"><img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/system_email.png"/></div><div class="text-wrapper"><?php echo __('System Emails', 'js-support-ticket'); ?></div></div></a>
        <a class="js-admin-link" href="?page=emailtemplate&jstlay=emailtemplates"><div class="js-box"><div class="img-wrapper"><img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/email_template.png"/></div><div class="text-wrapper"><?php echo __('Email Templates', 'js-support-ticket'); ?></div></div></a>
<?php /*        <a class="js-admin-link" href="?page=userfeild&jstlay=userfeilds"><div class="js-box"><div class="img-wrapper"><img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/user_fields.png"/></div><div class="text-wrapper"><?php echo __('User Fields', 'js-support-ticket'); ?></div></div></a> */ ?>
        <a class="js-admin-link" href="?page=fieldordering&jstlay=fieldordering"><div class="js-box"><div class="img-wrapper"><img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/fieldordering.png"/></div><div class="text-wrapper"><?php echo __('Fields ordering', 'js-support-ticket'); ?></div></div></a>
        <a class="js-admin-link" href="?page=systemerror&jstlay=systemerrors"><div class="js-box"><div class="img-wrapper"><img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/system_error.png"/></div><div class="text-wrapper"><?php echo __('System Errors', 'js-support-ticket'); ?></div></div></a>
    </div>
    <div class="js-box-wrapper js-3">
        <a class="js-admin-link" href="?page=reports&jstlay=overallreport"><div class="js-box"><div class="img-wrapper"><img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/report_icon.png"/></div><div class="text-wrapper"><?php echo __('Reports', 'js-support-ticket'); ?></div></div></a>		
        <a class="js-admin-link" href="?page=jssupportticket&jstlay=propage&task=jssupportticket_propage"><div class="js-box"><div class="img-wrapper"><img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/pro_icon.png"/></div><div class="text-wrapper"><?php echo __('Pro Version', 'js-support-ticket'); ?></div></div></a>
        <a class="js-admin-link" href="?page=jssupportticket&jstlay=aboutus&task=jssupportticket_aboutus"><div class="js-box"><div class="img-wrapper"><img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/about_us.png"/></div><div class="text-wrapper"><?php echo __('About Us', 'js-support-ticket'); ?></div></div></a>
        <a class="js-admin-link" href="?page=proinstaller&jstlay=step1"><div class="js-box"><div class="img-wrapper"><img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/upgrade.png"/></div><div class="text-wrapper"><?php echo __('Upgrade', 'js-support-ticket'); ?></div></div></a>		
    </div>
    <span class="js-admin-title"><?php echo __('Support Area', 'js-support-ticket'); ?></span>
    <div class="js-box-wrapper js-1">        
        <a class="js-admin-link" href="?page=jssupportticket&jstlay=shortcodes"><div class="js-box"><div class="img-wrapper"><img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/short_code.png"/></div><div class="text-wrapper"><?php echo __('Short Codes', 'js-support-ticket'); ?></div></div></a>
        <a class="js-admin-link" target="_blank" href="http://joomshark.com/index.php/documentations/jsdocumentation/categories/default/5"><div class="js-box"><div class="img-wrapper"><img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/documentation.png"/></div><div class="text-wrapper"><?php echo __('Documentation', 'js-support-ticket'); ?></div></div></a>
        <a class="js-admin-link" target="_blank" href="http://joomshark.com/index.php/forum/js-support-ticket-wp"><div class="js-box"><div class="img-wrapper"><img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/forum.png"/></div><div class="text-wrapper"><?php echo __('Forum', 'js-support-ticket'); ?></div></div></a>
        <a class="js-admin-link" target="_blank" href="http://joomshark.com/index.php/jstickets"><div class="js-box"><div class="img-wrapper"><img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/support.png"/></div><div class="text-wrapper"><?php echo __('Support', 'js-support-ticket'); ?></div></div></a>
    </div>
</div>
<span class="js-admin-title"><?php echo __('Latest Tickets', 'js-support-ticket'); ?></span>
<div class="js-ticket-admin-cp-tickets">
    <div class="js-row js-ticket-admin-cp-head js-ticket-admin-hide-head">
        <div class="js-col-xs-12 js-col-md-2"><?php echo __('Ticket ID', 'js-support-ticket'); ?></div>
        <div class="js-col-xs-12 js-col-md-3"><?php echo __('Subject', 'js-support-ticket'); ?></div>
        <div class="js-col-xs-12 js-col-md-1"><?php echo __('Status', 'js-support-ticket'); ?></div>
        <div class="js-col-xs-12 js-col-md-2"><?php echo __('From', 'js-support-ticket'); ?></div>
        <div class="js-col-xs-12 js-col-md-2"><?php echo __('Priority', 'js-support-ticket'); ?></div>
        <div class="js-col-xs-12 js-col-md-2"><?php echo __('Created', 'js-support-ticket'); ?></div>
    </div>
    <?php foreach (jssupportticket::$_data['tickets'] AS $ticket): ?>
        <div class="js-ticket-admin-cp-data">
            <div class="js-col-xs-12 js-col-md-2"><span class="js-ticket-admin-cp-showhide"><?php
                    echo __('Ticket ID', 'js-support-ticket');
                    echo " : ";
                    ?></span> <a href="<?php echo admin_url("admin.php?page=ticket&jstlay=ticketdetail&jssupportticketid=" . $ticket->id); ?>"><?php echo $ticket->ticketid; ?></a></div>
            <div class="js-col-xs-12 js-col-md-3 js-admin-cp-text-elipses"><span class="js-ticket-admin-cp-showhide" ><?php
                    echo __('Subject', 'js-support-ticket');
                    echo " : ";
                    ?></span> <?php echo $ticket->subject; ?></div>
            <div class="js-col-xs-12 js-col-md-1">
                <span class="js-ticket-admin-cp-showhide" ><?php
                echo __('Status', 'js-support-ticket');
                echo " : ";
                ?></span>
                <?php
                if ($ticket->status == 0) {
                    $style = "red;";
                    $status = __('New', 'js-support-ticket');
                } elseif ($ticket->status == 1) {
                    $style = "orange;";
                    $status = __('Waiting Staff Reply', 'js-support-ticket');
                } elseif ($ticket->status == 2) {
                    $style = "#FF7F50;";
                    $status = __('In Progress', 'js-support-ticket');
                } elseif ($ticket->status == 3) {
                    $style = "green;";
                    $status = __('Waiting Your Reply', 'js-support-ticket');
                } elseif ($ticket->status == 4) {
                    $style = "blue;";
                    $status = __('Closed', 'js-support-ticket');
                }
                echo '<span style="color:' . $style . '">' . $status . '</span>';
                ?>
            </div>
            <div class="js-col-xs-12 js-col-md-2"> <span class="js-ticket-admin-cp-showhide" ><?php
                    echo __('From', 'js-support-ticket');
                    echo " : ";
                    ?></span> <?php echo $ticket->name; ?></div>
            <div class="js-col-xs-12 js-col-md-2" style="color:<?php echo $ticket->prioritycolour; ?>;"> <span class="js-ticket-admin-cp-showhide" ><?php
                    echo __('Priority', 'js-support-ticket');
                    echo " : ";
                    ?></span> <?php echo __($ticket->priority, 'js-support-ticket'); ?></div>
            <div class="js-col-xs-12 js-col-md-2"><span class="js-ticket-admin-cp-showhide" ><?php
    echo __('Created', 'js-support-ticket');
    echo " : ";
    ?></span> <?php echo date_i18n(jssupportticket::$_config['date_format'], strtotime($ticket->created)); ?></div>
        </div>
<?php endforeach; ?>
</div>
<div id="wp-jobs-banner">
    <a href="https://wordpress.org/plugins/js-jobs/" target="_blank">
        <img src="<?php echo jssupportticket::$_pluginpath."/includes/images/wp-jobs-banner.png"; ?>" />
    </a>
</div>