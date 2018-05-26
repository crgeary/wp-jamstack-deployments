<?php

namespace Crgeary\JAMstackDeployments;

class WebhookTrigger
{
    public static function init()
    {
        add_action('admin_init', [__CLASS__, 'trigger']);
    }

    public static function trigger()
    {
        if (!isset($_GET['action']) || 'jamstack-deployment-trigger' !== $_GET['action']) {
            return;
        }

        check_admin_referer('crgeary_jamstack_deployment_trigger', 'crgeary_jamstack_deployment_trigger');

        self::fireWebhook();

        wp_redirect(admin_url('admin.php?page=wp-jamstack-deployments'));
        exit;
    }

    /**
     * Fire off a request to the webhook
     *
     * @return WP_Error|array
     */
    public static function fireWebhook()
    {
        $option = get_option(CRGEARY_JAMSTACK_DEPLOYMENTS_OPTIONS_KEY);
        $webhook = !empty($option['webhook_url']) ? $option['webhook_url'] : null;

        if (!$webhook) {
            App::instance()->logger->error('The webhook hasn\'t been set.');
            return;
        }

        if (false === filter_var($webhook, FILTER_VALIDATE_URL)) {
            App::instance()->logger->error('The webhook isn\'t a valid url.');
            return;
        }

        if (isset($option['webhook_method']) && mb_strtolower($option['webhook_method']) === 'get') {
            App::instance()->logger->info('A GET request was made to the webhook.');
            return wp_safe_remote_get($option['webhook_url']);
        }
        
        App::instance()->logger->info('A POST request was made to the webhook.');
        return wp_safe_remote_post($option['webhook_url']);
    }
}
