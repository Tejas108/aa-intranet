<?php
/*
* Plugin Name: BuddyPress Group Chat
* Plugin URI: https://wordpress.org/plugins/buddypress-group-chat/
* Author: Ruddernation Designs
* Author URI: http://profiles.wordpress.org/ruddernation
* Description: This plugin is used for BuddyPress to all Group creators to allow the use of TinyChat in groups,
* The Chat also includes youtube/soundcloud for all users, Even if you're not a moderator.
* Version: 1.6.7
* Requires at least: WordPress 4.0, BuddyPress 2.0
* Tested up to: WordPress 4.3, BuddyPress 2.3
* Site Wide Only: true
* Date: 05th September 2015
* License: GPLv3
* License URI: http://www.gnu.org/licenses/gpl-3.0.html
* Important - Only load if BuddyPress Groups is activated! - This will now check if it is enabled.
*/
function bp_tinychat_group_chat_init() {
	    global $wpdb;
    if ( is_multisite() && BP_ROOT_BLOG != $wpdb->blogid ) {
	return;
    }
    if ( ! bp_is_active( 'groups' ) ) {
	return;
    }
	require( dirname( __FILE__ ) . '/chat-core.php' );}
add_action( 'bp_include', 'bp_tinychat_group_chat_init' , 96);
function bp_tinychat_group_chat_activate() {
	global $wpdb;
	if ( !empty($wpdb->charset) )
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
$sql[] = "CREATE TABLE {$wpdb->base_prefix}bp_tinychat_group_chat (
id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
group_id bigint(20) NOT NULL,
user_id bigint(20) NOT NULL,
message_content text) {$charset_collate};";
	$sql[] = "CREATE TABLE {$wpdb->base_prefix}bp_tinychat_group_chat_online (
id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
group_id bigint(20) NOT NULL,
user_id bigint(20) NOT NULL,
timestamp int(11) NOT NULL) {$charset_collate};";
	require_once( ABSPATH . 'wp-admin/upgrade-functions.php' );
	dbDelta($sql);
	update_site_option( 'bp-tinychat-group-chat-db-version', BP_TINYCHAT_GROUP_CHAT_DB_VERSION );
}
register_activation_hook( __FILE__, 'bp_tinychat_group_chat_activate' ); ?>