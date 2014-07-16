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

	public function display_available_lists() {
		$lists = $this->get_mailing_lists_from_cache();
		?>
		<table class="form-table">
			<thead>
				<tr>
					<th scope="col"><?php _e('Address', 'mailgun-post-notifications'); ?></th>
					<th scope="col"><?php _e('Name', 'mailgun-post-notifications'); ?></th>
					<th scope="col" style="width: auto;"><?php _e('Description', 'mailgun-post-notifications'); ?></th>
					<th scope="col" style="width:80px;"><?php _e('Hidden', 'mailgun-post-notifications'); ?></th>
				</tr>
			</thead>
			<tbody>
		<?php
		foreach ( $lists as $item ) {
			echo '<tr>';
			printf( '<th scope="row">%s</th>', esc_html($item->address) );
			printf( '<td class="mailgun-name">%s%s</td>', esc_html($item->name), $this->get_name_field($item->address, $item->name) );
			printf( '<td class="mailgun-description">%s</td>', $this->get_description_field($item->address, $item->description) );
			printf( '<td class="mailgun-hidden">%s</td>', $this->get_hidden_list_checkbox($item->address) );
			echo '</tr>';
		}

		// new list fields
		?>
		<tr>
			<td colspan="4"><strong><?php _e('Create a new list', 'mailgun-post-notifications'); ?></strong></td>
		</tr>
		<tr>
			<td><input type="text" value="" class="widefat" name="mailgun_new_list[address]" /></td>
			<td class="mailgun-name"><input type="text" class="widefat" value="" name="mailgun_new_list[name]" /></td>
			<td class="mailgun-description"><textarea class="widefat" rows="2" cols="40" name="mailgun_new_list[description]"></textarea></textarea></td>
			<td class="mailgun-hidden"></td>
		</tr>
		<?php

		echo '</tbody></table>';
	}

	private function get_name_field( $address, $name ) {
		return sprintf( '<input type="hidden" name="mailgun_lists[%s][name]" value="%s" />', esc_attr($address), esc_attr($name) );
	}

	private function get_hidden_list_checkbox( $address ) {
		$list = new Mailing_List($address);
		return sprintf( '<input type="checkbox" name="mailgun_lists[%s][hidden]" value="1" %s />', esc_attr($address), checked($list->is_hidden(), TRUE, FALSE) );
	}

	private function get_description_field( $address, $default = '' ) {
		$list = new Mailing_List($address);
		return sprintf( '<textarea name="mailgun_lists[%s][description]" class="widefat">%s</textarea>', esc_attr($address), esc_textarea( $list->exists() ? $list->get_description() : $default) );
	}

	private function cache_lists( $lists ) {
		set_transient( 'mailgun_mailing_lists', $lists );
	}

	private function get_mailing_lists_from_cache() {
		$lists = get_transient('mailgun_mailing_lists');
		if ( empty($lists) ) {
			$lists = $this->get_mailing_lists_from_api();
			$this->cache_lists($lists);
		}
		return $lists;
	}

	private function get_mailing_lists_from_api() {
		$api = Plugin::instance()->api();
		$response = $api->get('lists');
		if ( !$response || $response['response']['code'] != 200 ) {
			return array();
		}
		return $response['body']->items;
	}

	private function clear_invalid_lists() {
		$lists = $this->get_mailing_lists_from_cache();
		$addresses = wp_list_pluck( $lists, 'address' );
		$saved = get_option('mailgun_lists');
		$gone = array_diff( array_keys($saved), $addresses );
		if ( !empty($gone) ) {
			foreach ( $gone as $address ) {
				unset($saved[$address]);
			}
			update_option( 'mailgun_lists', $saved );
		}
	}

	public function save_new_list( $submitted ) {
		if ( !empty($submitted['address']) && isset($submitted['name']) && isset($submitted['description']) ) {
			$address = $submitted['address'];
			$name = $submitted['name'] ? $submitted['name'] : $submitted['address'];
			$description = $submitted['description'];
			$api = Plugin::instance()->api();
			$response = $api->post('lists', array(
				'address' => $address,
				'name' => $name,
				'description' => $description,
			));
			if ( $response && $response['response']['code'] == 200 ) {
				$saved_lists = get_option('mailgun_lists');
				$saved_lists[$address] = array(
					'name' => $name,
					'description' => $description,
					'hidden' => 0
				);
				update_option( 'mailgun_lists', $saved_lists );
			}
		}
		return false;
	}

	public function display_confirmation_page_field( $args ) {
		if ( empty($args['option']) ) {
			return;
		}
		$current = get_option( $args['option'], 0 );
		wp_dropdown_pages(array(
			'selected' => $current,
			'name' => $args['option'],
			'show_option_none' => __('-- New Page --', 'mailgun-post-notifications'),
			'option_none_value' => 0,
		));
	}

	public function save_confirmation_page_field( $value ) {
		if ( empty($value) ) {
			$value = $this->create_new_confirmation_page();
		}
		return $value;
	}

	public function create_new_confirmation_page() {
		$title = __('Subscription Confirmed', 'mailgun-post-notifications');
		$content = Template::confirmation_page();
		$new_post = array(
			'post_title' => apply_filters( 'mailgun_confirmation_page_default_title', $title ),
			'post_content' => apply_filters( 'mailgun_confirmation_page_default_content', $content ),
			'post_type' => 'page',
			'post_status' => 'publish'
		);
		return wp_insert_post( $new_post );
	}

	public function get_confirmation_email_field_description() {
		$description = __("This email will contain a link for users to confirm their subscriptions. Your template should contain the following shortcodes:<br />
			<code>[link]</code> &ndash; This becomes a link back to your site with a unique code to confirm the user's subscription request.<br />
			<code>[email]</code> &ndash; This is the user's email address.<br />
			<code>[lists]</code> &ndash; This is a list of the lists the user opted to subscribe to.", 'mailgun-post-notifications' );
		return $description;
	}

	public function get_welcome_email_field_description() {
		$description = __("This email will be sent to users after they confirm their subscription. Leave blank to disable this email. Your template can contain the following shortcodes:<br />
			<code>[email]</code> &ndash; This is the user's email address.<br />
			<code>[lists]</code> &ndash; This is a list of the lists the user opted to subscribe to.", 'mailgun-post-notifications' );
		return $description;
	}
}
 