<?php

if (!function_exists('jamstack_deployments_fire_webhook')) {
    function jamstack_deployments_fire_webhook() {
        \Crgeary\JAMstackDeployments\WebhookTrigger::fireWebhook();
    }
}
