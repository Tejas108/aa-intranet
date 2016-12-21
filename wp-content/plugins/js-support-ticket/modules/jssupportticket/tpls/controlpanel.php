<?php
JSSTmessage::getMessage();
if (jssupportticket::$_config['offline'] == 2) {
    if (get_current_user_id() != 0|| jssupportticket::$_config['visitor_can_create_ticket'] == 1) {
        ?>
        <h1 class="js-ticket-heading"><?php echo jssupportticket::$_config['title']; ?></h1>
        <?php if (jssupportticket::$_config['cplink_openticket_user'] == 1) { ?>
            <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-frontend-manu" href="<?php echo site_url("?page_id=" . jssupportticket::getPageid() . "&jstmod=ticket&jstlay=addticket"); ?>">
                <span class="js-ticket-frontend-manu-text"><?php echo __('Open Ticket', 'js-support-ticket'); ?></span>
                <div class="js-ticket-frontend-manu-circle">
                    <div class="js-ticket-frontend-manu-circle-inner1">
                        <img class="js-ticket-frontend-manu-icon" src="<?php echo jssupportticket::$_pluginpath . 'includes/images/open_ticket.png'; ?>" />
                    </div>
                </div>
            </a>
        <?php } ?>
        <?php if (jssupportticket::$_config['cplink_myticket_user'] == 1) { ?>
            <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-frontend-manu" href="<?php echo site_url("?page_id=" . jssupportticket::$_pageid . "&jstmod=ticket&jstlay=myticket"); ?>">
                <span class="js-ticket-frontend-manu-text"><?php echo __('My Tickets', 'js-support-ticket'); ?></span>
                <div class="js-ticket-frontend-manu-circle">            
                    <div class="js-ticket-frontend-manu-circle-inner2">
                        <img class="js-ticket-frontend-manu-icon" src="<?php echo jssupportticket::$_pluginpath . 'includes/images/my_tickets.png'; ?>"/>
                    </div>
                </div>
            </a>
        <?php } ?>
        <?php if (jssupportticket::$_config['cplink_login_logout_user'] == 1){ ?>
        <?php if (!is_user_logged_in()): ?>
            <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-frontend-manu" href="<?php echo site_url("?page_id=" . jssupportticket::getPageid() . "&jstmod=jssupportticket&jstlay=login"); ?>">
                <span class="js-ticket-frontend-manu-text"><?php echo __('Log In', 'js-support-ticket'); ?></span>
                <div class="js-ticket-frontend-manu-circle">
                    <div class="js-ticket-frontend-manu-circle-inner1">
                        <img class="js-ticket-frontend-manu-icon" src="<?php echo jssupportticket::$_pluginpath . 'includes/images/login.png'; ?>" />
                    </div>
                </div>
            </a>
        <?php endif; ?>
        <?php if (is_user_logged_in()): ?>
            <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-frontend-manu" href="<?php echo wp_logout_url( home_url() ); ?>">
                <span class="js-ticket-frontend-manu-text"><?php echo __('Log Out', 'js-support-ticket'); ?></span>
                <div class="js-ticket-frontend-manu-circle">
                    <div class="js-ticket-frontend-manu-circle-inner1">
                        <img class="js-ticket-frontend-manu-icon" src="<?php echo jssupportticket::$_pluginpath . 'includes/images/logout.png'; ?>" />
                    </div>
                </div>
            </a>
        <?php endif;
        } ?>
        <?php
    } else {// User is guest
        JSSTlayout::getUserGuest();
    }
} else { // System is offline
    JSSTlayout::getSystemOffline();
}
?>