<?php

if (!function_exists('jamstack_deployments_fire_webhook')) {
    function jamstack_deployments_fire_webhook() {
        \Crgeary\JAMstackDeployments\WebhookTrigger::fireWebhook();
    }
}

if (!function_exists('jamstack_deployments_fire_webhook_save_post')) {
    function jamstack_deployments_fire_webhook_save_post($id, $post, $update) {
        \Crgeary\JAMstackDeployments\WebhookTrigger::triggerSavePost($id, $post, $update);
    }
    add_action('save_post', 'jamstack_deployments_fire_webhook_save_post', 10, 3);
}
