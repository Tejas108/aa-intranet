<?php
/*
Plugin Name: Re-send Welcome Email
Description: Re-send welcome email to specific users
Version: 0.4.2
Author: Andreas Baumgartner

$Id: re-send-welcome-email.php 38:f90deffb2524 2012-02-28 13:21 +0100 Andreas Baumgartner $
$Tag: tip $

*/

/*  Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : PLUGIN AUTHOR EMAIL)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// i18n
if(!load_plugin_textdomain('resend_welcome_email','/wp-content/languages/'))
	load_plugin_textdomain('resend_welcome_email','/wp-content/plugins/re-send-welcome-email/translations/');

add_action('admin_menu', 'resend_welcome_admin');

function resend_welcome_admin()
{
	add_users_page(__('Resend Welcome', 'resend_welcome_email'), __('Resend Welcome Email', 'resend_welcome_email'), 'manage_options', __FILE__, 'resend_welcome_settings_page');
}
function resend_welcome_settings_page()
{
?>

<div class="wrap">
<h2><?php _e('Resend Welcome Email', 'resend_welcome_email'); ?></h2>
<p><?php _e("This will reset the user's password and re-send their &quot;Welcome&quot; email with username and password.", 'resend_welcome_email'); ?></p>
<form method="POST">

<p><?php _e('Re-send welcome email for this user (<b>note: the user\'s password will be reset</b>):', 'resend_welcome_email');?> <?php wp_dropdown_users(array('orderby' => 'user_nicename', 'show' => 'user_login')); ?></p>
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Send e-mail', 'resend_welcome_email') ?>" />
    </p>
</form>
<?php
if (isset($_POST['user']))
{
	$uid = $_POST['user'];

	// Generate a password
	$password = substr(md5(uniqid(microtime())), 0, 7);
	$user_info = get_userdata($uid);

	wp_update_user(array('ID' => $uid, 'user_pass' => $password));

	// Send welcome email (there might be a better function for this, I didn't check)
	wp_new_user_notification($uid, $password);

	$message = sprintf(__('E-mail sent for user %s.', 'resend_welcome_email'), $user_info->user_login);
	printf('<div id="message" class="updated fade"><p>%s</p></div>', $message);
}
?>


</div><?php } ?>
