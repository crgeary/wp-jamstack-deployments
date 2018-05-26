<?php

if (!function_exists('crgeary_jamstack_deployments_fire_webhook')) {
    /**
     * Trigger the webhook
     *
     * @return WP_Error|array
     */
    function crgeary_jamstack_deployments_fire_webhook()
    {
        $option = get_option(CRGEARY_JAMSTACK_DEPLOYMENTS_OPTIONS_KEY);
        $webhook = !empty($option['webhook_url']) ? $option['webhook_url'] : null;

        // if webhook isn't set (@todo - add debug message)
        if (!$webhook) {
            return;
        }

        // or if webhook isn't a url (@todo - add debug message)
        if (false === filter_var($webhook, FILTER_VALIDATE_URL)) {
            return;
        }

        if (isset($option['webhook_method']) && mb_strtolower($option['webhook_method']) === 'get') {
            return wp_safe_remote_get($option['webhook_url']);
        }
        
        return wp_safe_remote_post($option['webhook_url']);
    }
}

