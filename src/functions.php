<?php

if (!function_exists('jamstack_deployments_fire_webhook')) {
    /**
     * Fire a request to the webhook.
     *
     * @return void
     */
    function jamstack_deployments_fire_webhook() {
        \Crgeary\JAMstackDeployments\WebhookTrigger::fireWebhook();
    }
}

if (!function_exists('jamstack_deployments_force_fire_webhook')) {
    /**
     * Fire a request to the webhook immediately. 
     *
     * @return void
     */
    function jamstack_deployments_force_fire_webhook() {
        \Crgeary\JAMstackDeployments\WebhookTrigger::fireWebhook();
    }
}

if (!function_exists('jamstack_deployments_fire_webhook_save_post')) {
    /**
     * Fire a request to the webhook when a post has been saved.
     *
     * @param int $id
     * @param WP_Post $post
     * @param boolean $update
     * @return void
     */
    function jamstack_deployments_fire_webhook_save_post($id, $post, $update) {
        \Crgeary\JAMstackDeployments\WebhookTrigger::triggerSavePost($id, $post, $update);
    }
    add_action('save_post', 'jamstack_deployments_fire_webhook_save_post', 10, 3);
}

if (!function_exists('jamstack_deployments_fire_webhook_created_term')) {
    /**
     * Fire a request to the webhook when a term has been created.
     *
     * @param int $id
     * @param int $post
     * @param string $tax_slug
     * @return void
     */
    function jamstack_deployments_fire_webhook_created_term($id, $tax_id, $tax_slug) {
        \Crgeary\JAMstackDeployments\WebhookTrigger::triggerSaveTerm($id, $tax_id, $tax_slug);
    }
    add_action('created_term', 'jamstack_deployments_fire_webhook_created_term', 10, 3);
}

if (!function_exists('jamstack_deployments_fire_webhook_delete_term')) {
    /**
     * Fire a request to the webhook when a term has been removed.
     *
     * @param int $id
     * @param int $post
     * @param string $tax_slug
     * @param object $term
     * @param array $object_ids
     * @return void
     */
    function jamstack_deployments_fire_webhook_delete_term($id, $tax_id, $tax_slug, $term, $object_ids) {
        \Crgeary\JAMstackDeployments\WebhookTrigger::triggerSaveTerm($id, $tax_id, $tax_slug, $term, $object_ids);
    }
    add_action('delete_term', 'jamstack_deployments_fire_webhook_delete_term', 10, 5);
}

if (!function_exists('jamstack_deployments_fire_webhook_edit_term')) {
    /**
     * Fire a request to the webhook when a term has been modified.
     *
     * @param int $id
     * @param int $post
     * @param string $tax_slug
     * @return void
     */
    function jamstack_deployments_fire_webhook_edit_term($id, $tax_id, $tax_slug) {
        \Crgeary\JAMstackDeployments\WebhookTrigger::triggerEditTerm($id, $tax_id, $tax_slug);
    }
    add_action('edit_term', 'jamstack_deployments_fire_webhook_edit_term', 10, 3);
}
