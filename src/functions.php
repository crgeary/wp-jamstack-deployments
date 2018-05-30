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

if (!function_exists('jamstack_deployments_fire_webhook_created_term')) {
    function jamstack_deployments_fire_webhook_created_term($id, $tax_id, $tax_slug) {
        \Crgeary\JAMstackDeployments\WebhookTrigger::triggerSaveTerm($id, $tax_id, $tax_slug);
    }
    add_action('created_term', 'jamstack_deployments_fire_webhook_created_term', 10, 3);
}

if (!function_exists('jamstack_deployments_fire_webhook_delete_term')) {
    function jamstack_deployments_fire_webhook_delete_term($id, $tax_id, $tax_slug, $term, $object_ids) {
        \Crgeary\JAMstackDeployments\WebhookTrigger::triggerSaveTerm($id, $tax_id, $tax_slug, $term, $object_ids);
    }
    add_action('delete_term', 'jamstack_deployments_fire_webhook_delete_term', 10, 5);
}

if (!function_exists('jamstack_deployments_fire_webhook_edit_term')) {
    function jamstack_deployments_fire_webhook_edit_term($id, $tax_id, $tax_slug) {
        \Crgeary\JAMstackDeployments\WebhookTrigger::triggerEditTerm($id, $tax_id, $tax_slug);
    }
    add_action('edit_term', 'jamstack_deployments_fire_webhook_edit_term', 10, 5);
}
