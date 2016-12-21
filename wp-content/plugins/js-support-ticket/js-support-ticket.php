<?php

/**
 * @package JS Support Ticket
 * @author Ahmad Bilal
 * @version 1.1.6
 */
/*
  Plugin Name: JS Support Ticket
  Plugin URI: http://www.joomsky.com
  Description: JS Support Ticket is a trusted open source ticket system. JS Support ticket is a simple, easy to use, web-based customer support system. User can create ticket from front-end. JS support ticket comes packed with lot features than most of the expensive(and complex) support ticket system on market. JS Support ticket provide you best industry help desk system.
  Author: Joom Sky
  Version: 1.1.6
  Author URI: http://www.joomsky.com
 */

if (!defined('ABSPATH'))
    die('Restricted Access');

class jssupportticket {

    public static $_path;
    public static $_pluginpath;
    public static $_data; /* data[0] for list , data[1] for total paginition ,data[2] userfieldsforview , data[3] userfield for form , data[4] for reply , data[5] for ticket history  , data[6] for internal notes  , data[7] for ban email  , data['ticket_attachment'] for attachment */
    public static $_pageid;
    public static $_db;
    public static $_config;
    public static $_sorton;
    public static $_sortorder;
    public static $_ordering;
    public static $_sortlinks;
    public static $_currentversion;
    public static $_wpprefixforuser;

    function __construct() {
        self::includes();
        self::registeractions();
        self::$_path = plugin_dir_path(__FILE__);
        self::$_pluginpath = plugins_url('/', __FILE__);
        self::$_data = '';
        self::$_currentversion = '116';
        global $wpdb;
        self::$_db = $wpdb;
        if(is_multisite()) {  
            self::$_wpprefixforuser = $wpdb->base_prefix;
        }else{
            self::$_wpprefixforuser = self::$_db->prefix;
        }  
        JSSTincluder::getJSModel('configuration')->getConfiguration();
        register_activation_hook(__FILE__, array($this, 'jssupportticket_activate'));
        register_deactivation_hook(__FILE__, array($this, 'jssupportticket_deactivate'));
        add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));
        add_action('jssupporticket_updateticketstatus', array($this, 'updateticketstatus'));
        add_action('admin_init', array($this, 'jssupportticket_activation_redirect'));
        add_action( 'wp_footer', array($this,'checkScreenTag') );
    }

    function jssupportticket_activate() {
        include_once 'includes/activation.php';
        JSSTactivation::jssupportticket_activate();
        wp_schedule_event(time(), 'daily', 'jssupporticket_updateticketstatus');
        add_option('jssupportticket_do_activation_redirect', true);        
    }

    function jssupportticket_activation_redirect(){
        if (get_option('jssupportticket_do_activation_redirect', false)) {
            delete_option('jssupportticket_do_activation_redirect');
            exit(wp_redirect(admin_url('admin.php?page=jssupportticket&jstlay=gettingstart')));
        }        
    }

    function updateticketstatus() {
        $query = "UPDATE `" . jssupportticket::$_db->prefix . "js_ticket_tickets` SET status = 4 WHERE date(DATE_ADD(lastreply,INTERVAL " . jssupportticket::$_config['ticket_auto_close'] . " DAY)) < CURDATE() AND isanswered = 1";
        jssupportticket::$_db->query($query);
        if (jssupportticket::$_db->last_error != null) {
            JSSTincluder::getJSModel('systemerror')->addSystemError();
        }
    }

    function jssupportticket_deactivate() {
        include_once 'includes/deactivation.php';
        JSSTdeactivation::jssupportticket_deactivate();
    }

    // function jsst_redirectlogin($user_login,$user) {
    //     $isadmin = $user->caps['administrator'];
    //     if(!$isadmin){
    //         if(jssupportticket::$_config['login_redirect'] == 1){
    //             $pageid = jssupportticket::getPageid();
    //             $link = "index.php?page_id=".$pageid;
    //             wp_redirect($link);
    //             exit;
    //         }
    //     }
    // }

    // function jsst_login_redirect( $redirect_to, $request, $user ) {
    //     //is there a user to check?
    //     global $user;
    //     if ( isset( $user->roles ) && is_array( $user->roles ) ) {
    //         //check for admins
    //         if ( in_array( 'administrator', $user->roles ) ) {
    //             // redirect them to the default place
    //             return $redirect_to;
    //         } else {
    //             if(jssupportticket::$_config['login_redirect'] == 1){
    //                 $pageid = jssupportticket::getPageid();
    //                 $link = "index.php?page_id=".$pageid;
    //                 return $link;
    //             }else{
    //                 return home_url();
    //             }
    //         }
    //     } else {
    //         return $redirect_to;
    //     }
    // }

    function registeractions() {
        //Extra Hooks
        //add_filter( 'login_redirect', array($this,'jsst_login_redirect'), 10, 3 );
        //Ticket Action Hooks
        add_action('jsst-ticketcreate', array($this, 'ticketcreate'), 10, 1);
        add_action('jsst-ticketreply', array($this, 'ticketreply'), 10, 1);
        add_action('jsst-ticketclose', array($this, 'ticketclose'), 10, 1);
        add_action('jsst-ticketdelete', array($this, 'ticketdelete'), 10, 1);
        add_action('jsst-ticketbeforelisting', array($this, 'ticketbeforelisting'), 10, 1);
        add_action('jsst-ticketbeforeview', array($this, 'ticketbeforeview'), 10, 1);
        //Email Hooks
        add_action('jsst-beforeemailticketcreate', array($this, 'beforeemailticketcreate'), 10, 4);
        add_action('jsst-beforeemailticketreply', array($this, 'beforeemailticketreply'), 10, 4);
        add_action('jsst-beforeemailticketclose', array($this, 'beforeemailticketclose'), 10, 4);
        add_action('jsst-beforeemailticketdelete', array($this, 'beforeemailticketdelete'), 10, 4);
    }

    //Funtions for Ticket Hooks
    function ticketcreate($ticketobject) {
        return $ticketobject;
    }

    function ticketreply($ticketobject) {
        return $ticketobject;
    }

    function ticketclose($ticketobject) {
        return $ticketobject;
    }

    function ticketdelete($ticketobject) {
        return $ticketobject;
    }

    function ticketbeforelisting($ticketobject) {
        return $ticketobject;
    }

    function ticketbeforeview($ticketobject) {
        return $ticketobject;
    }

    //Funtion for Email Hooks
    function beforeemailticketcreate($recevierEmail, $subject, $body, $senderEmail) {
        return;
    }

    function beforeemailticketdelete($recevierEmail, $subject, $body, $senderEmail) {
        return;
    }

    function beforeemailticketreply($recevierEmail, $subject, $body, $senderEmail) {
        return;
    }

    function beforeemailticketclose($recevierEmail, $subject, $body, $senderEmail) {
        return;
    }

    /*
     * Include the required files
     */

    function includes() {
        if (is_admin()) {
            include_once 'includes/jssupportticketadmin.php';
        }
        include_once 'includes/captcha.php';
        include_once 'includes/recaptchalib.php';
        include_once 'includes/pagination.php';
        include_once 'includes/breadcrumbs.php';
        include_once 'includes/includer.php';
        include_once 'includes/formfield.php';
        include_once 'includes/request.php';
        include_once 'includes/formhandler.php';
        include_once 'includes/shortcodes.php';
        include_once 'includes/paramregister.php';
        include_once 'includes/message.php';
        include_once 'includes/layout.php';
        include_once 'includes/ajax.php';
    }

    /**
     * Localization
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain('js-support-ticket', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    public static function getPageid() {
        if(jssupportticket::$_pageid != ''){
            return jssupportticket::$_pageid;
        }else{
            $pageid = JSSTrequest::getVar('page_id','GET');
            if($pageid){
                return $pageid;
            }else{ // in case of categories popup
		        $query = "SELECT configvalue FROM `".jssupportticket::$_db->prefix."js_ticket_config` WHERE configname = 'default_pageid'";
		        $pageid = jssupportticket::$_db->get_var($query);
		        return $pageid;
			}
        }
    }

    public static function setPageID($id) {
        jssupportticket::$_pageid = $id;
        return;
    }

    /*
     * function for the Style Sheets
     */

    Static function addStyleSheets() {
        if (is_admin()) {
            wp_register_style('jsticket-admincss', jssupportticket::$_pluginpath . 'includes/css/admincss.css');
            wp_enqueue_style('jsticket-admincss');
            if(is_rtl()){
                wp_register_style('jsticket-admincss-rtl', jssupportticket::$_pluginpath . 'includes/css/admincssrtl.css');
                wp_enqueue_style('jsticket-admincss-rtl');
            }
        } else {
            wp_register_style('jsticket-style', jssupportticket::$_pluginpath . 'includes/css/style.css');
            wp_enqueue_style('jsticket-style');
            if(is_rtl()){
                wp_register_style('jsticket-style-rtl', jssupportticket::$_pluginpath . 'includes/css/stylertl.css');
                wp_enqueue_style('jsticket-style-rtl');
            }
        }
        wp_register_style('jsticket-bootstarp', jssupportticket::$_pluginpath . 'includes/css/bootstrap.min.css');
        wp_enqueue_style('jsticket-bootstarp');
        wp_enqueue_script('commonjs',jssupportticket::$_pluginpath.'includes/js/common.js');
    }

    /*
     * function to parse the spaces in given string
     */

    public static function parseSpaces($string) {
        return str_replace('%20', ' ', $string);
    }
    
    static function checkScreenTag(){
        if(!is_admin()){
            if (jssupportticket::$_config['support_screentag'] == 1) { // we need to show the support ticket tag
                $location = 'left';
                $borderradius = '0px 8px 8px 0px';
                $padding = '5px 10px 5px 20px';
                switch (jssupportticket::$_config['screentag_position']) {
                    case 1: // Top left
                        $top = "30px";
                        $left = "0px";
                        $right = "none";
                        $bottom = "none";
                    break;
                    case 2: // Top right
                        $top = "30px";
                        $left = "none";
                        $right = "0px";
                        $bottom = "none";
                        $location = 'right';
                        $borderradius = '8px 0px 0px 8px';
                        $padding = '5px 20px 5px 10px';
                    break;
                    case 3: // middle left
                        $top = "48%";
                        $left = "0px";
                        $right = "none";
                        $bottom = "none";
                    break;
                    case 4: // middle right
                        $top = "48%";
                        $left = "none";
                        $right = "0px";
                        $bottom = "none";
                        $location = 'right';
                        $borderradius = '8px 0px 0px 8px';
                        $padding = '5px 20px 5px 10px';
                    break;
                    case 5: // bottom left
                        $top = "none";
                        $left = "0px";
                        $right = "none";
                        $bottom = "30px";
                    break;
                    case 6: // bottom right
                        $top = "none";
                        $left = "none";
                        $right = "0px";
                        $bottom = "30px";
                        $location = 'right';
                        $borderradius = '8px 0px 0px 8px';
                        $padding = '5px 20px 5px 10px';
                    break;
                }
                $html = '<style type="text/css">
                            div#jsjobs_screentag{opacity:0;position:fixed;top:'.$top.';left:'.$left.';right:'.$right.';bottom:'.$bottom.';padding:'.$padding.';background:rgba(149,149,149,.50);z-index:9999;border-radius:'.$borderradius.';}
                            div#jsjobs_screentag img.jsjobs_screentag_image{margin-'.$location.':10px;display:inline-block;}
                            div#jsjobs_screentag a.jsjobs_screentag_anchor{color:#ffffff;text-decoration:none;}
                            div#jsjobs_screentag span.text{display:inline-block;font-family:sans-serif;font-size:15px;}
                        </style>
                        <div id="jsjobs_screentag">
                        <a class="jsjobs_screentag_anchor" href="' . site_url('?page_id=' . jssupportticket::$_config['default_pageid']) . '">';
                if($location == 'right'){
                    $html .= '<img class="jsjobs_screentag_image" src="'.jssupportticket::$_pluginpath.'includes/images/support-icon.png" /><span class="text">'.__("Support",'js-support-ticket').'</span>';
                }else{
                    $html .= '<span class="text">'.__("Support",'js-support-ticket').'</span><img class="jsjobs_screentag_image" src="'.jssupportticket::$_pluginpath.'includes/images/support-icon.png" />';
                }
                $html .= '</a>
                        </div>
                        <script type="text/javascript">
                            jQuery(document).ready(function(){
                                jQuery("div#jsjobs_screentag").css("'.$location.'","-"+(jQuery("div#jsjobs_screentag span.text").width() + 25)+"px");
                                jQuery("div#jsjobs_screentag").css("opacity",1);
                                jQuery("div#jsjobs_screentag").hover(
                                    function(){
                                        jQuery(this).animate({'.$location.': "+="+(jQuery("div#jsjobs_screentag span.text").width() + 25)}, 1000);
                                    },
                                    function(){
                                        jQuery(this).animate({'.$location.': "-="+(jQuery("div#jsjobs_screentag span.text").width() + 25)}, 1000);
                                    }
                                );
                            });
                        </script>';
                echo $html;
            }
        }
    }

}

add_action('init', 'custom_init_session', 1);

function custom_init_session() {
    wp_enqueue_script("jquery");
    jssupportticket::addStyleSheets();
    if (!session_id())
        session_start();
}

$jssupportticket = new jssupportticket();


?>
