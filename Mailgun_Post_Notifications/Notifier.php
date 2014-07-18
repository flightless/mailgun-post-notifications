<?php


namespace Mailgun_Post_Notifications;


class Notifier {
	protected $posts_to_notify = array();

	public function listen_for_saved_post( $post_id, $post ) {
		$this->posts_to_notify[] = $post_id;
	}

	protected function should_send_notifications_for( $post_id ) {
		if ( empty($post_id) ) {
			return FALSE;
		}
		if ( get_post_status($post_id) != 'publish' ) {
			return FALSE;
		}
		if ( get_post_meta( $post_id, '_mailgun_notification_sent', TRUE ) ) {
			return FALSE;
		}

		if ( !in_array( get_post_type($post_id), $this->post_types_to_notify() ) ) {
			return FALSE;
		}

		return apply_filters( 'should_send_mailgun_post_notification', TRUE, $post_id );
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