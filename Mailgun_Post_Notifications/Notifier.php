<?php


namespace Mailgun_Post_Notifications;


class Notifier {
	protected $posts_to_notify = array();

	public function listen_for_saved_post( $post_id, $post ) {
		$this->posts_to_notify[] = $post_id;
	}

	protected function should_send_notifications_for( $post_id ) {
		$send = TRUE;
		if ( empty($post_id) ) {
			return FALSE; // short circuit, as most of the following checks will cause an error without an ID
		}

		if ( get_post_status($post_id) != 'publish' ) { // only notify for published posts
			$send = FALSE;
		}

		if ( get_post_meta( $post_id, '_mailgun_notification_sent', TRUE ) ) { // only one notification per post
			$send = FALSE;
		}

		if ( !in_array( get_post_type($post_id), $this->post_types_to_notify() ) ) { // only notify for whitelisted post types
			$send = FALSE;
		}

		$post_time = get_post_time( 'U', TRUE, $post_id );
		if ( time() - $post_time > ( DAY_IN_SECONDS / 2 ) ) { // only notify for posts published in the last 12 hours
			$send = FALSE;
		}

		return apply_filters( 'should_send_mailgun_post_notification', $send, $post_id );
	}

	protected function post_types_to_notify() {
		return apply_filters( 'mailgun_post_notification_post_types', array( 'post' ) );
	}

	public function send_notifications() {
		foreach ( $this->posts_to_notify as $post_id ) {
			if ( $this->should_send_notifications_for( $post_id ) ) {
				try {
					$notification = new Notification($post_id);
					$notification->send();
				} catch ( \Exception $e ) {
					// fail silently
				}
				update_post_meta( $post_id, '_mailgun_notification_sent', time() );
			}
		}
	}
} 