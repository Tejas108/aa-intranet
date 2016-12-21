<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

add_filter('wp_insert_post_data', 'filter_handler_jssupportticket_slug', '99', 2);

function filter_handler_jssupportticket_slug($data, $postarr) {
    // do something with the post data
    $query = 'SELECT configvalue FROM `' . jssupportticket::$_db->prefix . 'js_ticket_config` WHERE configname = "system_slug"';
    $system_slug = jssupportticket::$_db->get_var($query);
    if ($data['post_name'] == $system_slug) {
        $data['post_name'] = $system_slug . '1';
    }
    return $data;
}

add_action('save_post', 'jsticket_update_rules');

function jsticket_update_rules($post_id) {
    update_option('rewrite_rules', '');
}

add_action('customize_save_after', 'jsst_customizersaveafter');

function jsst_customizersaveafter() {
    update_option('rewrite_rules', '');
}

function getParamsForJSSTTICKET(){
    $frontpage_id = get_option('page_on_front');
    $query = 'SELECT configvalue FROM `' . jssupportticket::$_db->prefix . 'js_ticket_config` WHERE configname = "system_slug"';
    $system_slug = jssupportticket::$_db->get_var($query);

    //Layout specific rules here
    //login
    $new_rules['(.?.+?)/'.$system_slug.'/login/?$'] = 'index.php?pagename=$matches[1]&jstmod=jssupportticket&jstlay=login';
    $new_rules[$system_slug.'/login/?$'] = 'index.php?page_id='.$frontpage_id.'&jstmod=jssupportticket&jstlay=login';
    //addticket
    //on all pages
    $new_rules['(.?.+?)/' . $system_slug . '/addticket/(.?.+?)/?$'] = 'index.php?pagename=$matches[1]&jstmod=ticket&jstlay=addticket&jssupportticketid=$matches[2]';
    $new_rules['(.?.+?)/' . $system_slug . '/addticket/?$'] = 'index.php?pagename=$matches[1]&jstmod=ticket&jstlay=addticket';
    //on home page only
    $new_rules[$system_slug . '/addticket/(.?.+?)/?$'] = 'index.php?page_id=' . $frontpage_id . '&jstmod=ticket&jstlay=addticket&jssupportticketid=$matches[1]';
    $new_rules[$system_slug . '/addticket/?$'] = 'index.php?page_id=' . $frontpage_id . '&jstmod=ticket&jstlay=addticket';

    //ticketdetail
    $new_rules['(.?.+?)/' . $system_slug . '/ticketdetail/(.?.+?)/?$'] = 'index.php?pagename=$matches[1]&jstmod=ticket&jstlay=ticketdetail&jssupportticketid=$matches[2]';
    $new_rules[$system_slug . '/ticketdetail/(.?.+?)/?$'] = 'index.php?page_id=' . $frontpage_id . '&jstmod=ticket&jstlay=ticketdetail&jssupportticketid=$matches[1]';
    //myticket
    $new_rules['(.?.+?)/' . $system_slug . '/myticket/(.?.+?)/(.?.+?)/?$'] = 'index.php?pagename=$matches[1]&jstmod=ticket&jstlay=myticket&list=$matches[2]&sortby=$matches[3]';
    $new_rules['(.?.+?)/' . $system_slug . '/myticket/(.?.+?)/?$'] =  'index.php?pagename=$matches[1]&jstmod=ticket&jstlay=myticket&list=$matches[2]';
    $new_rules['(.?.+?)/' . $system_slug . '/myticket/?$'] = 'index.php?pagename=$matches[1]&jstmod=ticket&jstlay=myticket';
    $new_rules['(.?.+?)/' . $system_slug . '/myticket/jsst-ticketsearchkeys/(.?.+?)/?$'] = 'index.php?pagename=$matches[1]&jstmod=ticket&jstlay=myticket&jsst-ticketsearchkeys=$matches[2]';
    $new_rules[$system_slug . '/myticket/(.?.+?)/(.?.+?)/?$'] = 'index.php?page_id=' . $frontpage_id . '&jstmod=ticket&jstlay=myticket&list=$matches[1]&sortby=$matches[2]';
    $new_rules[$system_slug . '/myticket/(.?.+?)/?$'] = 'index.php?page_id=' . $frontpage_id . '&jstmod=ticket&jstlay=myticket&list=$matches[1]';
    $new_rules[$system_slug . '/myticket/?$'] = 'index.php?page_id=' . $frontpage_id . '&jstmod=ticket&jstlay=myticket';
    $new_rules[$system_slug . '/myticket/jsst-ticketsearchkeys/(.?.+?)/?$'] = 'index.php?page_id=' . $frontpage_id . '&jstmod=ticket&jstlay=myticket&jsst-ticketsearchkeys=$matches[1]';
    //Controlpanel
    $new_rules['(.?.+?)/' . $system_slug . '/controlpanel/?$'] = 'index.php?pagename=$matches[1]&jstmod=jssupportticket&jstlay=controlpanel';
    $new_rules[$system_slug . '/controlpanel/?$'] = 'index.php?page_id=' . $frontpage_id . '&jstmod=jssupportticket&jstlay=controlpanel';
	return $new_rules;
}

add_action('init', 'jsst_add_rules', 10, 0);
add_action('generate_rewrite_rules', 'jsst_add_rule');

function jsst_add_rule($wp_rewrite) {
	$new_rules = getParamsForJSSTTICKET();
	$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
}

function jsst_add_rules() {
	$new_rules = getParamsForJSSTTICKET();
	foreach($new_rules AS $key => $value){
		add_rewrite_rule($key, $value, 'top');
	}    
}

add_action('init', 'jsst_rewrite_tag', 10, 0);

function jsst_rewrite_tag() {
    add_rewrite_tag('%jstmod%', '([^&]+)');
    add_rewrite_tag('%jstlay%', '([^&]+)');
    add_rewrite_tag('%task%', '([^&]+)');
    add_rewrite_tag('%list%', '([^&]+)');
    add_rewrite_tag('%sortby%', '([^&]+)');
    add_rewrite_tag('%jssupportticketid%', '([^&]+)');
    // Search Variables
    add_rewrite_tag('%jsst-title%', '([^&]+)');
    add_rewrite_tag('%jsst-cat%', '([^&]+)');
    add_rewrite_tag('%jsst-type%', '([^&]+)');
    add_rewrite_tag('%jsst-search%', '([^&]+)');
    add_rewrite_tag('%jsst-dept%', '([^&]+)');
    add_rewrite_tag('%jsst-subject%', '([^&]+)');
    add_rewrite_tag('%jsst-role%', '([^&]+)');
    add_rewrite_tag('%jsst-name%', '([^&]+)');
    add_rewrite_tag('%jsst-status%', '([^&]+)');
    add_rewrite_tag('%jsst-ticketsearchkeys%', '([^&]+)');
}

add_action('parse_request', 'parse_request1');

function parse_request1($q) {
//    echo '<pre>';
//    print_r($q->query_vars);
//    echo '</pre>';
}

add_filter('redirect_canonical', 'jsst_redirect_canonical', 10, 2);

function jsst_redirect_canonical($redirect_url, $requested_url) {

    global $wp_rewrite;

    // if url already include, do not include it again. Fix for polylang
    $query = 'SELECT configvalue FROM `' . jssupportticket::$_db->prefix . 'js_ticket_config` WHERE configname = "system_slug"';
    $system_slug = jssupportticket::$_db->get_var($query);
    if(strstr($redirect_url,'/'.$system_slug.'/')){
        return;
    }
    
    // Abort if not using pretty permalinks, is a feed, or not an archive for the post type 'book'
    if (!$wp_rewrite->using_permalinks() || is_feed())
        return $redirect_url;

    // Get the original query parts
    $redirect = @parse_url($requested_url);
    $original = $redirect_url;
    if (!isset($redirect['query']))
        $redirect['query'] = '';

    // If is year/month/day - append year
    if (is_year() || is_month() || is_day()) {
        $year = get_query_var('year');
        $redirect['query'] = remove_query_arg('year', $redirect['query']);
        $redirect_url = user_trailingslashit(get_post_type_archive_link('book')) . $year;
    }

    // If is month/day - append month
    if (is_month() || is_day()) {
        $month = zeroise(intval(get_query_var('monthnum')), 2);
        $redirect['query'] = remove_query_arg('monthnum', $redirect['query']);
        $redirect_url .= '/' . $month;
    }

    // If is day - append day
    if (is_day()) {
        $day = zeroise(intval(get_query_var('day')), 2);
        $redirect['query'] = remove_query_arg('day', $redirect['query']);
        $redirect_url .= '/' . $day;
    }

    // If is page_id
    if (get_query_var('page_id')) {
        $page_id = get_query_var('page_id');
        $redirect['query'] = remove_query_arg('page_id', $redirect['query']);
        $redirect_url = user_trailingslashit(get_page_link($page_id));
    }
    // If is jstmod
    if (get_query_var('jstmod')) {
        $module = get_query_var('jstmod');
        $redirect['query'] = remove_query_arg('jstmod', $redirect['query']);
        $query = 'SELECT configvalue FROM `' . jssupportticket::$_db->prefix . 'js_ticket_config` WHERE configname = "system_slug"';
        $system_slug = jssupportticket::$_db->get_var($query);
        $lastcharactor = substr($redirect_url, -1);
        if($lastcharactor != '/'){
            $redirect_url .= '/';
        }
        $redirect_url .= $system_slug;
    }
    // If is jstlay
    if (get_query_var('jstlay')) {
        $jstlay = get_query_var('jstlay');
        $redirect['query'] = remove_query_arg('jstlay', $redirect['query']);
        $redirect_url .= '/' . $jstlay;
    }
    // If is list
    if (get_query_var('list')) {
        $list = get_query_var('list');
        $redirect['query'] = remove_query_arg('list', $redirect['query']);
        $redirect_url .= '/' . $list;
    }
    // If is sortby
    if (get_query_var('sortby')) {
        $sortby = get_query_var('sortby');
        $redirect['query'] = remove_query_arg('sortby', $redirect['query']);
        $redirect_url .= '/' . $sortby;
    }
    // If is jssupportticket_ticketid
    if (get_query_var('jssupportticketid')) {
        $jssupportticket_ticketid = get_query_var('jssupportticketid');
        $redirect['query'] = remove_query_arg('jssupportticketid', $redirect['query']);
        $redirect_url .= '/' . $jssupportticket_ticketid;
    }

    //Search Variables
    switch ($jstlay) {
        case 'departments':
            if (get_query_var('jsst-dept')) {
                $dept = get_query_var('jsst-dept');
                $redirect_url .= '/jsst-dept/' . $dept;
            }
            break;
        case 'myticket':
            if (get_query_var('jsst-ticketsearchkeys')) {
                $ticketsearchkeys = get_query_var('jsst-ticketsearchkeys');
                $redirect_url .= '/jsst-ticketsearchkeys/' . $ticketsearchkeys;
            }
            break;
    }
    $redirect['query'] = remove_query_arg('jsst-title', $redirect['query']);
    $redirect['query'] = remove_query_arg('jsst-cat', $redirect['query']);
    $redirect['query'] = remove_query_arg('jsst-type', $redirect['query']);
    $redirect['query'] = remove_query_arg('jsst-search', $redirect['query']);
    $redirect['query'] = remove_query_arg('jsst-dept', $redirect['query']);
    $redirect['query'] = remove_query_arg('jsst-subject', $redirect['query']);
    $redirect['query'] = remove_query_arg('jsst-role', $redirect['query']);
    $redirect['query'] = remove_query_arg('jsst-name', $redirect['query']);
    $redirect['query'] = remove_query_arg('jsst-status', $redirect['query']);
    $redirect['query'] = remove_query_arg('jsst-ticketsearchkeys', $redirect['query']);
    $redirect['query'] = remove_query_arg('jsst-go', $redirect['query']);
    $redirect['query'] = remove_query_arg('jsst-reset', $redirect['query']);
    // If paged, apppend pagination
    if (get_query_var('paged') > 0) {
        $paged = (int) get_query_var('paged');
        $redirect['query'] = remove_query_arg('paged', $redirect['query']);

        if ($paged > 1)
            $redirect_url .= user_trailingslashit("/page/$paged", 'paged');
    }
    if ($redirect_url == $original)
        return $original;
    // tack on any additional query vars
    $redirect['query'] = preg_replace('#^\??&*?#', '', $redirect['query']);
    if ($redirect_url && !empty($redirect['query'])) {
        parse_str($redirect['query'], $_parsed_query);
        $_parsed_query = array_map('rawurlencode', $_parsed_query);
        $redirect_url = add_query_arg($_parsed_query, $redirect_url);
    }

    // if($redirect_url == $requested_url) return false;
    // wp_redirect( $redirect_url, 301 );
    // exit();

    return $redirect_url;
}

?>
