<?php

/**
 * The template for the HTML version of New Post notifications.
 *
 * Override this template by copying it to:
 * [your theme directory]/mailgun/html/new-post.php
 *
 * @var WP_Post $post
 */

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<style type="text/css" media="all">
		a {
			text-decoration: none;
			color: #1e6ec1;
		}
	</style>
	<title><?php bloginfo( 'name' ); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>

<body class="subscription-body-tag"
      style="direction: ltr; background: #FFF; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 14px; color: #666; text-align: center; margin: 0; padding: 0;">

<div style="direction: ltr; max-width: 600px; margin: 0 auto; overflow: hidden;">
	<table border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff" class="subscribe-wrapper"
	       style="width: 100%; background-color: #fff; text-align: left; max-width: 1024px; min-width: 320px; margin: 0 auto;">
		<tr>
			<td>
				<table border="0" cellspacing="0" cellpadding="0" height="8"
				       class="subscribe-header-wrap"
				       style="width: 100%; background-color: #1e6ec1; height: 8px;">
					<tr>
						<td></td>
					</tr>
				</table>

				<table border="0" cellspacing="0" cellpadding="0" class="subscribe-header"
				       style="width: 100%; color: #1e6ec1; font-size: 1.6em; background-color: #EFEFEF; border-bottom: 1px solid #DDD; margin: 0; padding: 0;">
					<tr>
						<td>
							<h2 class="subscribe-title"
							    style="font-size: 16px!important; line-height: 1; font-weight: 400; color: #464646; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 5px 20px!important; padding: 0;">
								<?php printf( __('New post published at <strong>%s</strong>', 'mailgun-post-notifications'), get_bloginfo('name') ); ?></h2>
						</td>
					</tr>
				</table>

				<table style="width: 100%" border="0" cellspacing="0" cellpadding="20" bgcolor="#ffffff">
					<tr>
						<td>
							<table style="width: 100%" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td valign="top" class="the-post">
										<table style="width: 100%" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td>
													<h2 class="post-title"
													    style="color: #555; margin: 0; font-size: 20px;">
														<a
															href="<?php the_permalink(); ?>"
															style="color: #1e6ec1; text-decoration: none !important;"><?php the_title(); ?></a></h2>
													<span style="color: #888;"><?php printf( __('by %s', 'mailgun-post-notifications'), the_author() ); ?></span>
												</td>
											</tr>
										</table>

										<div style="direction:ltr;margin-top:1em;max-width:560px">
											<p style="direction:ltr;font-size:14px;line-height:1.4em;color:#444;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;margin:0 0 1em"><?php the_excerpt(); ?></p>
											<p style="direction:ltr;font-size:14px;line-height:1.4em;color:#444;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;margin:0 0 1em"><a href="<?php the_permalink(); ?>"><?php _e('Read more', 'mailgun-post-notifications'); ?></a></p>
										</div>

										<div class="meta"
										     style="direction: ltr; color: #999; font-size: .9em; margin-top: 4px; line-height: 160%; padding: 15px 0 15px; border-top: 1px solid #eee; border-bottom: 1px solid #eee; overflow: hidden">
											<?php echo get_the_date(); ?>
											<?php the_tags(' | '.__('Tags: ', 'mailgun-post-notifications'), ', '); ?>
											| <?php _e('Categories:'); ?> <?php the_category(', '); ?>
											| <?php _e('URL:'); ?> <a style="text-decoration: underline; color: #1e6ec1;"
											          href="<?php the_permalink(); ?>"><?php the_permalink(); ?></a>
										</div>


									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>

			</td>
		</tr>
	</table>

	<table border="0" cellspacing="0" cellpadding="0" height="3"
	       class="subscribe-footer-wrap"
	       style="width: 100%; background-color: #1e6ec1; height: 3px;">
		<tr>
			<td></td>
		</tr>
	</table>
</div>
</body>
</html>