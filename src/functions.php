<?php

if (!function_exists('jamstack_deployments_get_options')) {
    /**
     * Return the plugin settings/options
     *
     * @return array
     */
    function jamstack_deployments_get_options() {
        return get_option(CRGEARY_JAMSTACK_DEPLOYMENTS_OPTIONS_KEY, []);
    }
}

if (!function_exists('jamstack_deployments_get_webhook_url')) {
    /**
     * Return the webhook url
     *
     * @return string|null
     */
    function jamstack_deployments_get_webhook_url() {
        $options = jamstack_deployments_get_options();
        return isset($options['webhook_url']) ? $options['webhook_url'] : null;
    }
}

if (!function_exists('jamstack_deployments_get_webhook_method')) {
    /**
     * Return the webhook method (get/post)
     *
     * @return string
     */
    function jamstack_deployments_get_webhook_method() {
        $options = jamstack_deployments_get_options();
        $method = isset($options['webhook_method']) ? $options['webhook_method'] : 'post';
        return mb_strtolower($method);
    }
}

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
    // duplicates functionality of 'transition_post_status'
    // add_action('save_post', 'jamstack_deployments_fire_webhook_save_post', 10, 3);
}

if (!function_exists('jamstack_deployments_fire_webhook_transition_post_status')) {
    /**
     * Fire a request to the webhook when a the status is changed
     *
     * @param string $new
     * @param string $old
     * @param WP_Post $post
     * @return void
     */
    function jamstack_deployments_fire_webhook_transition_post_status($new, $old, $post) {
        \Crgeary\JAMstackDeployments\WebhookTrigger::triggerPostTransition($new, $old, $post);
    }
    add_action('transition_post_status', 'jamstack_deployments_fire_webhook_transition_post_status', 10, 3);
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

if (!function_exists('jamstack_deployments_fire_webhook_acf_save_post')) {
    /**
     * Fire a request to the webhook when an ACF option page is saved
     *
     * @param int $id
     * @return void
     */
    function jamstack_deployments_fire_webhook_acf_save_post($id) {
        $option = jamstack_deployments_get_options();
        if (isset($option['webhook_acf']) && in_array('options', $option['webhook_acf'], true) && 'options' === $id) {
            \Crgeary\JAMstackDeployments\WebhookTrigger::fireWebhook();    
        }
    }
    add_action('acf/save_post', 'jamstack_deployments_fire_webhook_acf_save_post', 10, 3);
}
