# JAMstack Deployments

A WordPress plugin for JAMstack deployments on Netlify (and other platforms).

## Preface

This plugin provides a way to fire off a request to a webhook when a post, page or custom post type is created/udpated/deleted. You're also able to fire off a request manually at the click of a button.

## Custom Actions

The `jamstack_deployments_fire_webhook` action can be used to fire the webhook at a custom point that you specify. For example, if you want to fire the webhook when a user registers, then you can use:

```php
add_action('user_register', 'jamstack_deployments_fire_webhook');
```

Or when a category/term is created:

```php
add_action('created_term', 'jamstack_deployments_fire_webhook', 10, 3);
```

## Running Code Before & After Webhooks

You can run code before or after you fire the webhook using the following actions:

* Before: `jamstack_deployments_before_fire_webhook`
* After: `jamstack_deployments_after_fire_webhook`
