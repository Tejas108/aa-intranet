<?php
/*
Plugin Name: BP Group Email
Version: 1.0.6
Plugin URI: http://premium.wpmudev.org/project/buddypress-group-email
Description: This plugin adds group email functionality to BuddyPress allowing a group admin or moderator to send an email to all the other members in the group.
Author: Aaron Edwards (Incsub)
Author URI: http://uglyrobot.com
Network: true
WDP ID: 110

Copyright 2009-2011 Incsub (http://incsub.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//------------------------------------------------------------------------//

//---Config---------------------------------------------------------------//

//------------------------------------------------------------------------//

$bp_group_email_current_version = '1.0.6';

/* Only load code that needs BuddyPress to run once BP is loaded and initialized. */
function bp_group_email_init() {
	if (class_exists('BP_Group_Extension'))
		require_once( dirname( __FILE__ ) . '/includes/bp-group-email.php' );
}
add_action( 'bp_init', 'bp_group_email_init' );

function bp_group_email_localization() {
  // Load up the localization file if we're using WordPress in a different language
	// Place it in this plugin's "languages" folder and name it "groupemail-[value in wp-config].mo"
	load_plugin_textdomain( 'groupemail', FALSE, '/bp-group-email/languages' );
}
add_action( 'plugins_loaded', 'bp_group_email_localization' );


?>