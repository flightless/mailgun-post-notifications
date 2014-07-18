# MailGun Post Notifications

Add notifications for new posts to a site with the Mailgun Subscriptions plugin.

## Installation and Setup

First install and activate the Mailgun Subscriptions plugin.

Then install and activate this plugin just as a normal WordPress plugin.

You'll find the "Mailgun Lists" settings page in the Settings admin menu. After setting up your Mailgun API settings, you can select a list that will receive notifications of new posts.

## Hooks

`should_send_mailgun_post_notification` - Return `FALSE` to this filter to prevent a notification from sending.

`mailgun_post_notification_post_types` - Filter the post types for which notifications will be sent. Defaults to just `post`.

## Templates

All emails are sent with both a text version and an HTML version. You can independently manage the templates for each.

The default templates can be found in the plugin's `email-templates` directory. To override the default templates, created the directory `mailgun` in your theme, and copy the `html` and `text` directories from the plugin into this directory. Your theme's directory structure should look like:

```
[your_theme]
   - mailgun
        - html
             - new-post.php
        - text
             - new-post.php
   index.php
   style.css
   etc.
```

Within your templates, you have access to the global `$post` object, and normal template tags like `the_title()`, `the_permalink()`, and `the_excerpt()` will all work.