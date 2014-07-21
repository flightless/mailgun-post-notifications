<?php

/**
 * The template for the text version of New Post notifications.
 *
 * Override this template by copying it to:
 * [your theme directory]/mailgun/text/new-post.php
 *
 * @var WP_Post $post
 */
?>

<?php printf( __('New post published at %s', 'mailgun-post-notifications'), get_bloginfo('name') ); ?>



<?php the_title(); ?>



<?php printf( __('URL: %s', 'mailgun-post-notifications'), get_the_permalink() ); ?>

