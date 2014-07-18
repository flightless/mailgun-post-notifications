<?php

namespace Mailgun_Post_Notifications;
use Mailgun_Subscriptions\Admin_Page as Subscriptions;

/**
 * Class Admin_Page
 */
class Admin_Page {

	/**
	 * Hook into the subscriptions plugin admin page to add our options
	 */
	public function register() {
		add_settings_section(
			'post_notifications',
			__('New Post Notifications', 'mailgun-post-notifications'),
			'__return_false',
			Subscriptions::MENU_SLUG
		);

		add_settings_field(
			'mailgun_post_notifications_to_address',
			__('List to notify', 'mailgun-post-notifications'),
			array( $this, 'display_list_select' ),
			Subscriptions::MENU_SLUG,
			'post_notifications',
			array(
				'option' => 'mailgun_post_notifications_to_address',
				'descriptions' => __('Notifications for new posts will be sent to this list', 'mailgun-post-notifications' ),
			)
		);
		register_setting(
			Subscriptions::MENU_SLUG,
			'mailgun_post_notifications_to_address'
		);

		add_settings_field(
			'mailgun_post_notifications_from_name',
			__('"From" Name', 'mailgun-post-notifications'),
			array( $this, 'display_text_field' ),
			Subscriptions::MENU_SLUG,
			'post_notifications',
			array(
				'option' => 'mailgun_post_notifications_from_name',
				'description' => __('Notification emails will display this name in the "From" field. If blank, the blog name will be used.', 'mailgun-post-notifications'),
			)
		);
		register_setting(
			Subscriptions::MENU_SLUG,
			'mailgun_post_notifications_from_name'
		);

		add_settings_field(
			'mailgun_post_notifications_from_address',
			__('"From" Address', 'mailgun-post-notifications'),
			array( $this, 'display_text_field' ),
			Subscriptions::MENU_SLUG,
			'post_notifications',
			array(
				'option' => 'mailgun_post_notifications_from_address',
				'description' => __('Notification emails will display this email address in the "From" field. If blank, the admin email address will be used.', 'mailgun-post-notifications'),
			)
		);
		register_setting(
			Subscriptions::MENU_SLUG,
			'mailgun_post_notifications_from_address'
		);

		add_settings_field(
			'mailgun_post_notifications_subject',
			__('Subject', 'mailgun-post-notifications'),
			array( $this, 'display_text_field' ),
			Subscriptions::MENU_SLUG,
			'post_notifications',
			array(
				'option' => 'mailgun_post_notifications_subject',
				'description' => __('Subject line for the notification emails. <code>[blog_name]</code> will be replaced with the blog name. <code>[post_title]</code> will be replaced with the post title.', 'mailgun-post-notifications'),
				'default' => __('[[blog_name]] New Post: [post_title]', 'mailgun-post-notifications'),
			)
		);
		register_setting(
			Subscriptions::MENU_SLUG,
			'mailgun_post_notifications_subject'
		);

	}

	public function display_list_select( $args ) {
		if ( !isset($args['option']) ) {
			return;
		}
		$args = wp_parse_args( $args, array('default' => '', 'description' => '') );
		$lists = \Mailgun_Subscriptions\Plugin::instance()->get_lists();
		$value = get_option( $args['option'], $args['default'] );
		printf('<select name="%s">', esc_attr($args['option']));
		echo '<option value="">'.__('-- Select a List --', 'mailgun-post-notifications').'</option>';
		foreach ( $lists as $address => $data ) {
			printf('<option value="%s" %s>%s</option>', esc_attr($address), selected($address, $value, false), esc_html($address));
		}
		echo '</select>';
		if ( !empty($args['description']) ) {
			printf( '<p class="description">%s</p>', $args['description'] );
		}
	}

	public function display_text_field( $args ) {
		if ( !isset($args['option']) ) {
			return;
		}
		$args = wp_parse_args( $args, array('default' => '', 'description' => '') );
		$value = get_option( $args['option'], $args['default'] );
		printf( '<input type="text" value="%s" name="%s" class="widefat" />', esc_attr($value), esc_attr($args['option']) );
		if ( !empty($args['description']) ) {
			printf( '<p class="description">%s</p>', $args['description'] );
		}
	}

	public function display_textarea_field( $args ) {
		if ( !isset($args['option']) ) {
			return;
		}
		$args = wp_parse_args( $args, array('default' => '', 'description' => '', 'rows' => 5, 'cols' => 40) );
		$value = get_option( $args['option'], $args['default'] );
		printf( '<textarea rows="%s" cols="%s" name="%s" class="widefat">%s</textarea>', intval($args['rows']), intval($args['cols']), esc_attr($args['option']), esc_textarea($value) );
		if ( !empty($args['description']) ) {
			printf( '<p class="description">%s</p>', $args['description'] );
		}
	}
}
 