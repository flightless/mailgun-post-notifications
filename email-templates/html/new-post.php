<?php

/**
 * The template for the HTML version of New Post notifications.
 *
 * @var WP_Post $post
 */
?>

<p><?php printf( __('A <a href="%s">new post</a> has been published at %s.', 'mailgun-post-notifications'), get_the_permalink(), get_bloginfo('name') ); ?></p>
<p><?php printf( __('Title: <a href="%1$s">%2$s</a>', 'mailgun-post-notifications'), get_the_permalink(), get_the_title() ); ?></p>
<p><?php printf( __('Link: <a href="%1$s">%1$s</a>', 'mailgun-post-notifications'), get_the_permalink() ); ?></p>
