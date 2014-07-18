<?php


namespace Mailgun_Post_Notifications;


class Notification {
	protected $post_id = 0;

	public function __construct( $post_id ) {
		$this->post_id = (int)$post_id;
	}

	public function send() {
		$address = $this->get_list_address();
		if ( empty($address) ) {
			return;
		}

		$this->setup_post_global();
		$subject = $this->get_subject();
		$html = $this->get_html();
		$text = $this->get_text();
		wp_reset_postdata();

		$api = \Mailgun_Subscriptions\Plugin::instance()->api();
		$response = $api->post( $this->get_domain($address).'/messages', array(
			'from' => $this->get_from_header(),
			'to' => $address,
			'subject' => $subject,
			'text' => $text,
			'html' => $html,
		));
		return;
	}

	protected function get_html() {
		global $post;
		$template = $this->get_template('html/new-post.php');
		ob_start();
		include($template);
		return ob_get_clean();
	}

	protected function get_text() {
		global $post;
		$template = $this->get_template('text/new-post.php');
		ob_start();
		include($template);
		return ob_get_clean();
	}

	protected function setup_post_global() {
		global $post;
		$post = get_post($this->post_id);
		setup_postdata( $post );
	}

	protected function get_template( $path ) {
		$file = locate_template('mailgun'.DIRECTORY_SEPARATOR.$path);
		if ( $file ) {
			return $file;
		}
		$plugin_path = Plugin::path( 'email-templates'.DIRECTORY_SEPARATOR.$path );
		if ( file_exists($plugin_path) ) {
			return $plugin_path;
		}
		return FALSE;
	}

	protected function get_list_address() {
		return get_option( 'mailgun_post_notifications_to_address', '' );
	}

	protected function get_from_header() {
		$from_name = get_option( 'mailgun_post_notifications_from_name', '' );
		if ( empty( $from_name ) ) {
			$from_name = get_bloginfo( 'name' );
		}
		$from_address = get_option( 'mailgun_post_notifications_from_address', '' );
		if ( empty( $from_address ) ) {
			$from_address = get_option( 'admin_email' );
		}

		return sprintf( '%s <%s>', $from_name, $from_address );
	}

	protected function get_subject() {
		$subject = get_option( 'mailgun_post_notifications_subject', '' );
		$subject = str_replace( '[blog_name]', get_bloginfo('name'), $subject );
		$subject = str_replace( '[post_title]', get_the_title(), $subject );
		return $subject;
	}

	protected function get_domain( $address ) {
		$parts = explode('@', $address);
		return end($parts);
	}
} 