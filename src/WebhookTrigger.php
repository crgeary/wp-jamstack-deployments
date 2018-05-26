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

        crgeary_jamstack_deployments_fire_webhook();

        wp_redirect(admin_url('admin.php?page=wp-jamstack-deployments'));
        exit;
    }
}
