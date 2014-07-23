# MailGun Post Notifications

Add notifications for new posts to a site with the Mailgun Subscriptions plugin.

## Installation and Setup

First install and activate the Mailgun Subscriptions plugin.

Then install and activate this plugin just as a normal WordPress plugin.

You'll find the "Mailgun Lists" settings page in the Settings admin menu. After setting up your Mailgun API settings, you can select a list that will receive notifications of new posts.

## Hooks

### `should_send_mailgun_post_notification`

Return `FALSE` to this filter to prevent a notification from sending.

```php
function my_filter_for_should_send_mailgun_post_notification( $should_send, $post_id ) {
	if ( get_post_meta( $post_id, '_some_interesting_meta_key', TRUE ) == 1 ) {
		$should_send = FALSE;
	}
	return $should_send;
}
add_filter( 'should_send_mailgun_post_notification', 'my_filter_for_should_send_mailgun_post_notification', 10, 2 );
```

### `mailgun_post_notification_post_types`

Filter the post types for which notifications will be sent. Defaults to just `post`.

```php
function my_filter_for_mailgun_post_notification_post_types( $post_types ) {
	$post_types[] = 'page'; // notify subscribers when a new Page is published
	return $post_types;
}
add_filter( 'mailgun_post_notification_post_types', 'my_filter_for_mailgun_post_notification_post_types', 10, 1 );
```

### `mailgun_post_notification_api_arguments`

Filter any of the arguments sent to Mailgun to send the email (e.g., filter the 'from' or 'reply-to' headers)

```php
function my_filter_for_mailgun_post_notification_api_arguments( $args ) {
	unset($args['text']); // never send text version of the email
	return $args;
}
add_filter( 'mailgun_post_notification_api_arguments', 'my_filter_for_mailgun_post_notification_api_arguments', 10, 1 );
```

## Templates

All emails are sent with both a text version and an HTML version. You can independently manage the templates for each.

The default templates can be found in the plugin's `email-templates` directory. To override the default templates, created the directory `mailgun` in your theme, and copy the `html` and `text` directories from the plugin into this directory. Your theme's directory structure should look like:

```
[your_theme]
   - mailgun
   |  - html
   |  |  - new-post.php
   |  - text
   |  |  - new-post.php
   - index.php
   - style.css
   - etc.
```

Within your templates, you have access to the global `$post` object, and normal template tags like `the_title()`, `the_permalink()`, and `the_excerpt()` will all work.