<?php
/*
Plugin Name: Fresh Start
Plugin URI: http://freshplugs.com/fresh-start
Description: Tool that provides a genuinely clean slate for purist developers by removing all default WordPress content and resetting nonessential options while preserving general settings, user data, and theme files.
Version: 0.9
Author: Fresh Plugs
Author URI: http://freshplugs.com

Fresh Start is released under the Creative Commons 0 license.
http://creativecommons.org/publicdomain/zero/1.0/

*/

if ( is_admin() ) :

	// Creates menu item in Tools and limits header to plugin page only.
	function fresh_start_menu() {
		$FreshStart = add_management_page( 'Fresh Start', 'Fresh Start', 'manage_options', 'fresh-start', 'fresh_start_options' );
		add_action( 'admin_print_scripts-'.$FreshStart, 'fresh_start_head' );
	} add_action('admin_menu', 'fresh_start_menu');
	
	// Includes plugin scripts.
	function fresh_start_head() {
		wp_enqueue_script( 'fresh-start', plugins_url('fresh-start.js', __FILE__), 'jquery' );
	}
	
	// Removes content from databse.
	function fresh_start_ajax() {
		global $wpdb;
		$wpdb->query("delete from $wpdb->comments");
		$wpdb->query("alter table $wpdb->comments auto_increment = 1");
		$wpdb->query("delete from $wpdb->commentmeta");
		$wpdb->query("alter table $wpdb->commentmeta auto_increment = 1");
		_e("<p>Comments removed&hellip;</p>");
		$wpdb->query("delete from $wpdb->links");
		$wpdb->query("alter table $wpdb->links auto_increment = 1");
		_e("<p>Links removed&hellip;</p>");
		$wpdb->query("delete from $wpdb->posts");
		$wpdb->query("alter table $wpdb->posts auto_increment = 1");
		$wpdb->query("delete from $wpdb->postmeta");
		$wpdb->query("alter table $wpdb->postmeta auto_increment = 1");
		_e("<p>Posts removed&hellip;</p>");
		$wpdb->query("delete from $wpdb->terms");
		$wpdb->query("alter table $wpdb->terms auto_increment = 1");
		$wpdb->query("delete from $wpdb->term_relationships");
		$wpdb->query("alter table $wpdb->term_relationships auto_increment = 1");
		$wpdb->query("delete from $wpdb->term_taxonomy");
		$wpdb->query("alter table $wpdb->term_taxonomy auto_increment = 1");
		_e("<p>Terms removed&hellip;</p>");
		if ( get_option('blogdescription') == 'Just another WordPress site' ) update_option('blogdescription','');
		update_option('use_smilies', 0);
		update_option('mailserver_url', '');
		update_option('mailserver_login', '');
		update_option('mailserver_pass', '');
		update_option('default_pingback_flag', 0);
		update_option('default_ping_status', 'closed');
		update_option('default_comment_status', 'closed');
		update_option('require_name_email', 0);
		update_option('thread_comments', 0);
		update_option('comments_notify', 0);
		update_option('moderation_notify', 0);
		update_option('comment_whitelist', 0);
		update_option('show_avatars', 0);
		update_option('thumbnail_size_w', 0);
		update_option('thumbnail_size_h', 0);
		update_option('thumbnail_crop', 0);
		update_option('medium_size_w', 0);
		update_option('medium_size_h', 0);
		update_option('large_size_w', 0);
		update_option('large_size_h', 0);
		update_option('embed_autourls', 0);
		update_option('embed_size_w', '');
		update_option('embed_size_h', 0);
		update_option('uploads_use_yearmonth_folders', '');
		_e("<p>Options updated&hellip;</p>");
		global $current_user;
		$dashboard_boxes = array(
			'dashboard_recent_comments',
			'dashboard_incoming_links',
			'dashboard_plugins',
			'dashboard_quick_press',
			'dashboard_recent_drafts',
			'dashboard_primary',
			'dashboard_secondary');
		update_user_meta( $current_user->ID, 'metaboxhidden_dashboard', $dashboard_boxes );
		_e("<p>Dashboard updated&hellip;</p>");
		$success = __('<p style="color: green;">Fresh Start completed successfully.</p>
		<p><strong>You should remove this plugin to prevent accidental use in the future.</strong></p>
		<p><a href="'.get_bloginfo('url').'/wp-admin/plugins.php">Go to Plugins page</a></p>');
		die($success);
	} add_action( 'wp_ajax_fresh_start', 'fresh_start_ajax' );
	
	// Displays plugin page.
	function fresh_start_options() {
		if ( ! current_user_can('manage_options') ) wp_die( __('You do not have sufficient permissions to access this page.') ); ?>
	
	<div class="wrap">
		<h2>Fresh Start</h2>
		<p><?php _e("This tool destroys <strong>ALL</strong> default WordPress content from the database and should only be used immediately after installation."); ?></p>
		
		<form action="" id="fresh-start-form" method="post"> 
		<p><?php _e("Type <em>Fresh Start</em> in the field below and click <strong>Go</strong>."); ?></p>
		<fieldset>
			<input type="text" name="fresh-start" placeholder="Case Sensitive" required />
			<input type="submit" class="button" value="Go" />
		</fieldset>
		</form>
		
		<div id="fresh-ajax"></div><!-- #fresh-ajax -->
	</div><!-- .wrap -->

<?php } endif; ?>
