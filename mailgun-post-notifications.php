<?php
/*
Plugin Name: Mailgun Post Notifications
Plugin URI: https://github.com/flightless/mailgun-subscriptions
Description: Send new post notifications to a Mailgun mailing list
Author: Flightless
Author URI: http://flightless.us/
Version: 1.0
Text Domain: mailgun-post-notifications
Domain Path: /languages
*/

if ( !function_exists('mailgun_post_notifications_load') ) {

	function mailgun_post_notifications_load() {
		add_action( 'init', 'mailgun_post_notifications_load_textdomain', 10, 0 );
		if ( !mailgun_post_notifications_version_check() ) {
			add_action( 'admin_notices', 'mailgun_post_notifications_version_notice' );
			return;
		}
		if ( !mailgun_post_notifications_dependency_check() ) {
			add_action( 'admin_notices', 'mailgun_post_notifications_dependency_notice' );
			return;
		}
		require_once('Mailgun_Post_Notifications/Plugin.php');
		\Mailgun_Post_Notifications\Plugin::init(__FILE__);
	}

	function mailgun_post_notifications_load_textdomain() {
		$domain = 'mailgun-post-notifications';
		// The "plugin_locale" filter is also used in load_plugin_textdomain()
		$locale = apply_filters('plugin_locale', get_locale(), $domain);

		load_textdomain($domain, WP_LANG_DIR.'/mailgun-post-notifications/'.$domain.'-'.$locale.'.mo');
		load_plugin_textdomain($domain, FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
	}

	function mailgun_post_notifications_version_check() {
		if ( version_compare(PHP_VERSION, '5.3.2', '>=') ) {
			return TRUE;
		}
		return FALSE;
	}

	function mailgun_post_notifications_version_notice() {
		$message = sprintf(__('MailGun Post Notifications requires PHP version %s or higher. You are using version %s.', 'mailgun-post-notifications'), '5.3.2', PHP_VERSION);
		printf( '<div class="error"><p>%s</p></div>', $message );
	}

	function mailgun_post_notifications_dependency_check() {
		if ( function_exists('mailgun_subscriptions_load') ) {
			return TRUE;
		}
		return FALSE;
	}

	function mailgun_post_notifications_dependency_notice() {
		$message = sprintf(__('MailGun Post Notifications requires the Mailgun Mailing List Subscriptions plugin.', 'mailgun-post-notifications'), '5.3.2', PHP_VERSION);
		printf( '<div class="error"><p>%s</p></div>', $message );
	}

	add_action( 'plugins_loaded', 'mailgun_post_notifications_load' );
}