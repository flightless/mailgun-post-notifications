<?php

/**
 * The template for the text version of New Post notifications.
 *
 * @var WP_Post $post
 */
?>
<?php printf( __('A new post has been published at %s.', 'mailgun-post-notifications'), get_bloginfo('name') ); ?>

<?php printf( __('Title: %s', 'mailgun-post-notifications'), get_the_title() ); ?>

<?php printf( __('Link: %s', 'mailgun-post-notifications'), get_the_permalink() ); ?>

