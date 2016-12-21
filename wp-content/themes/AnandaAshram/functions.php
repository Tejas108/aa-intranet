<?php
if (function_exists('add_theme_support'))
{
    // Add Menu Support
    add_theme_support('menus');

    // Add Thumbnail Theme Support
    add_theme_support('post-thumbnails');
    add_image_size('large', 700, '', true); // Large Thumbnail
    add_image_size('medium', 250, '', true); // Medium Thumbnail
    add_image_size('small', 120, '', true); // Small Thumbnail
    add_image_size('custom-size', 700, 200, true); // Custom Thumbnail Size call using the_post_thumbnail('custom-size');

    // Add Support for Custom Backgrounds - Uncomment below if you're going to use
    /*add_theme_support('custom-background', array(
	'default-color' => 'FFF',
	'default-image' => get_template_directory_uri() . '/img/bg.jpg'
    ));*/

    // Add Support for Custom Header - Uncomment below if you're going to use
    /*add_theme_support('custom-header', array(
	'default-image'			=> get_template_directory_uri() . '/img/headers/default.jpg',
	'header-text'			=> false,
	'default-text-color'		=> '000',
	'width'				=> 1000,
	'height'			=> 198,
	'random-default'		=> false,
	'wp-head-callback'		=> $wphead_cb,
	'admin-head-callback'		=> $adminhead_cb,
	'admin-preview-callback'	=> $adminpreview_cb
    ));*/

    // Enables post and comment RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Localisation Support
    load_theme_textdomain('rainworld', get_template_directory() . '/languages');
}

// Functions

function rainworld_nav()
{
	wp_nav_menu(
	array(
		'theme_location'  => 'header-menu',
		'menu'            => '',
		'container'       => 'nav',
		'container_class' => 'main-menu',
		'container_id'    => '',
		'menu_class'      => 'menu',
		'menu_id'         => '',
		'echo'            => true,
		'fallback_cb'     => 'wp_page_menu',
		'before'          => '',
		'after'           => '',
		'link_before'     => '',
		'link_after'      => '',
		'items_wrap'      => '<ul>%3$s</ul>',
		'depth'           => 0,
		'walker'          => ''
		)
	);
}

function rainworld_header_scripts()
{
    if ($GLOBALS['pagenow'] != 'wp-login.php' && !is_admin()) {

        wp_register_script('modernizr', get_template_directory_uri() . '/assets/js/lib/modernizr.min.js', array(), '2.8.3'); // Modernizr
        wp_enqueue_script('modernizr'); 

        wp_deregister_script('jquery');
        wp_register_script('jquery', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js", false, null);
        wp_enqueue_script('jquery');

        wp_register_script('rainworldscripts', get_template_directory_uri() . '/assets/js/scripts.js', array('jquery'), '1.0.0', true); // Custom scripts
        wp_enqueue_script('rainworldscripts'); 
    }
}

function rainworld_styles()
{
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');
    wp_register_style('rainworld', get_template_directory_uri() . '/assets/css/global.css', array(), '1.0', 'all');
    wp_enqueue_style('rainworld'); 
}


// Register rainworld navigation
function register_rainworld_menu()
{
    register_nav_menus(array( // Using array to specify more menus if needed
        'header-menu' => __('Header Menu', 'rainworld'), // Main Navigation
        'sidebar-menu' => __('Sidebar Menu', 'rainworld'), // Sidebar Navigation
        'extra-menu' => __('Extra Menu', 'rainworld') // Extra Navigation if needed (duplicate as many as you need!)
    ));
}

// Remove the <div> surrounding the dynamic navigation to cleanup markup
function my_wp_nav_menu_args($args = '')
{
    $args['container'] = false;
    return $args;
}

// Remove invalid rel attribute values in the categorylist
function remove_category_rel_from_category_list($thelist)
{
    return str_replace('rel="category tag"', 'rel="tag"', $thelist);
}

// Add page slug to body class, love this - Credit: Starkers Wordpress Theme
function add_slug_to_body_class($classes)
{
    global $post;
    if (is_home()) {
        $key = array_search('blog', $classes);
        if ($key > -1) {
            unset($classes[$key]);
        }
    } elseif (is_page()) {
        $classes[] = sanitize_html_class($post->post_name);
    } elseif (is_singular()) {
        $classes[] = sanitize_html_class($post->post_name);
    }

    return $classes;
}

// If Dynamic Sidebar Exists
if (function_exists('register_sidebar'))
{
    // Define Sidebar Widget Area 1
    register_sidebar(array(
        'name' => __('Widget Area 1', 'rainworld'),
        'description' => __('Description for this widget-area...', 'rainworld'),
        'id' => 'widget-area-1',
        'before_widget' => '<div id="%1$s" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));

    // Define Sidebar Widget Area 2
    register_sidebar(array(
        'name' => __('Widget Area 2', 'rainworld'),
        'description' => __('Description for this widget-area...', 'rainworld'),
        'id' => 'widget-area-2',
        'before_widget' => '<div id="%1$s" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));
}

// Remove Injected classes, ID's and Page ID's from Navigation <li> items
function my_css_attributes_filter($var)
{
    return is_array($var) ? array() : '';
}

// Pagination for paged posts, Page 1, Page 2, Page 3, with Next and Previous Links, No plugin
function rainworld_pagination()
{
    global $wp_query;
    $big = 999999999;
    echo paginate_links(array(
        'base' => str_replace($big, '%#%', get_pagenum_link($big)),
        'format' => '?paged=%#%',
        'current' => max(1, get_query_var('paged')),
        'total' => $wp_query->max_num_pages
    ));
}

// Remove WP logo from admin bar
function annointed_admin_bar_remove() {
        global $wp_admin_bar;

        /* Remove their stuff */
        $wp_admin_bar->remove_menu('wp-logo');
}


  function show_menu_items() {
    if (current_user_can('hrc')||current_user_can('board')) {
      /**
       * Keep only specific menu items and remove all others
       */
      global $menu;
      $hMenu = $menu;
      foreach ($hMenu as $nMenuIndex => $hMenuItem) {
        if (in_array($hMenuItem[2], array(
          'index.php',
          'users.php',
          'admin.php?page=wp_timesheets_user_report',
            'forms.php'
        ))
        ) {
          continue;
        }
        unset($menu[$nMenuIndex]);
      }
    }
  }

//function remove_admin_menu_items() {
//	$remove_menu_items = array(__('Tools'),__('Posts'));
//
//	global $menu;
//	end ($menu);
//	while (prev($menu)){
//		$item = explode(' ',$menu[key($menu)][0]);
//		if(in_array($item[0] != NULL?$item[0]:"" , $remove_menu_items)){
//			unset($menu[key($menu)]);}
//	}
//}
//
//add_action('admin_menu', 'remove_admin_menu_items');

function remove_menus(){
	if(!current_user_can('HRC')) {
		remove_menu_page( 'edit.php' );
	}
	remove_menu_page( 'tools.php' );
	if(current_user_can('HRC')){
		remove_menu_page('post-new.php?post_type=recipe');
	}
}
add_action( 'admin_menu', 'remove_menus' );


add_action( 'admin_bar_menu', 'remove_wp_bar_items', 999 );

function remove_wp_bar_items( $wp_admin_bar ) {
	if(current_user_can('staff_member') || current_user_can('volunteer')) {
		$wp_admin_bar->remove_node( 'new-post' );
	}
	if(current_user_can('hrc') || current_user_can('board')) {
		$wp_admin_bar->remove_node( 'new-recipe' );
	}

}
// Actions
//add_action('init', 'rainworld_header_scripts'); // Add Custom Scripts to wp_head
add_action('admin_menu', 'show_menu_items');
add_action('wp_enqueue_scripts', 'rainworld_styles'); // Add Theme Stylesheet
add_action('init', 'register_rainworld_menu'); // Add rainworld  Menu
add_action('init', 'rainworld_pagination'); // Add rainworld Pagination
add_action('wp_before_admin_bar_render', 'annointed_admin_bar_remove', 0); // Remove WP logo from admin bar

// Remove Actions
remove_action('wp_head', 'feed_links_extra', 3); // Display the links to the extra feeds such as category feeds
remove_action('wp_head', 'feed_links', 2); // Display the links to the general feeds: Post and Comment Feed
remove_action('wp_head', 'rsd_link'); // Display the link to the Really Simple Discovery service endpoint, EditURI link
remove_action('wp_head', 'wlwmanifest_link'); // Display the link to the Windows Live Writer manifest file.
remove_action('wp_head', 'index_rel_link'); // Index link
remove_action('wp_head', 'parent_post_rel_link', 10, 0); // Prev link
remove_action('wp_head', 'start_post_rel_link', 10, 0); // Start link
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // Display relational links for the posts adjacent to the current post.
remove_action('wp_head', 'wp_generator'); // Display the XHTML generator that is generated on the wp_head hook, WP version
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);

//Filters
add_filter('body_class', 'add_slug_to_body_class'); // Add slug to body class (Starkers build)
add_filter('widget_text', 'do_shortcode'); // Allow shortcodes in Dynamic Sidebar


function bp_remove_feeds() {
remove_action( 'wp', 'bp_activity_action_sitewide_feed', 3 );
remove_action( 'wp', 'bp_activity_action_personal_feed', 3 );
remove_action( 'wp', 'bp_activity_action_friends_feed', 3 );
remove_action( 'wp', 'bp_activity_action_my_groups_feed', 3 );
remove_action( 'wp', 'bp_activity_action_mentions_feed', 3 );
remove_action( 'wp', 'bp_activity_action_favorites_feed', 3 );
remove_action( 'wp', 'groups_action_group_feed', 3 );
}
add_action('init', 'bp_remove_feeds');

function remove_dashboard_meta() {
  remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
  remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
  remove_meta_box( 'dashboard_primary', 'dashboard', 'normal' );
  remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
  remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
  remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
  remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
  remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
  remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');//since 3.8
}
add_action( 'admin_init', 'remove_dashboard_meta' );

// Magic Number Stuff

function magic_number(){
  global $wpdb;

//  $mealNumber = $_POST['meals'];
    $lunchNumber = $_POST['lunch'];
    $dinnerNumber = $_POST['dinner'];
    $note = $_POST['note'];
  $staff_count = 20;
  $lunchNumber = $lunchNumber + $staff_count;
    $dinnerNumber = $dinnerNumber + $staff_count;
    date_default_timezone_set("America/New_York");
  $timestamp = date("Y-m-d");
    $current_date = date("Y-m-d");
    $time_submitted = date("h:ia");

  //$wpdb->show_errors();
	$results = $wpdb->get_row( "SELECT * from wp_magic_number WHERE date = '".$current_date."' ORDER BY date", ARRAY_A );

	// Check to see if there is already a record for today. If there isn't, insert one, else just update the existing one
	if($results[date]) {
		$wpdb->update('wp_magic_number', array('lunch'=>$lunchNumber,'dinner'=>$dinnerNumber,'note'=>$note, 'submittedAt'=>$time_submitted), array(date =>$current_date));
	} elseif (!$results[date]) {
		$wpdb->insert('wp_magic_number', array('lunch'=>$lunchNumber,'dinner'=>$dinnerNumber,'note'=>$note,'date'=>$timestamp, 'submittedAt'=>$time_submitted));
	}
	die();
}

add_action('wp_ajax_magic_number', 'magic_number');
add_action('wp_ajax_nopriv_magic_number', 'magic_number');

        show_admin_bar( true );


function staff_dropdown_list() {
    // query array
    $args = array(
        'role' => 'staff_member',
        'name'=>'user',
        'show_option_all' => 'All Users'
    );
    $users = get_users($args);
    if( empty($users) )
        return;
    echo'<select>';
    foreach( $users as $user ){
        echo '<option value="'.$user->data->display_name.'">'.$user->data->display_name.'</option>';
    }
    echo'</select>';
}

?>