# JAMstack Deployments

A WordPress plugin for JAMstack deployments on Netlify (and other platforms).

## Custom Actions

If you want fire the webhook at a custom action, then you can do so by attaching the `jamstack_deployments_fire_webhook` function to a WordPress action.

For example, if you want to fire the webhook when a user registers, then you can use:

```
add_action('user_register', 'jamstack_deployments_fire_webhook');
```

This will trigger a new deployment anytime a new user signs up to your site.

## Running Code Before & After Webhooks

You can run code before or after you fire the webhook using the following actions:

* Before: `jamstack_deployments_before_fire_webhook`
* After: `jamstack_deployments_after_fire_webhook`
