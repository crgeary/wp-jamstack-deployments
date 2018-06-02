<?php

namespace Crgeary\JAMstackDeployments;

class WebhookTrigger
{
    /**
     * Setup hooks for triggering the webhook
     *
     * @return void
     */
    public static function init()
    {
        add_action('admin_init', [__CLASS__, 'trigger']);
        add_action('admin_bar_menu', [__CLASS__, 'adminBarTriggerButton']);

        add_action('admin_footer', [__CLASS__, 'adminBarCss']);
        add_action('wp_footer', [__CLASS__, 'adminBarCss']);
        
    }

    /**
     * When a post is saved or updated, fire this
     *
     * @param int $id
     * @param object $post
     * @param bool $update
     * @return void
     */
    public static function triggerSavePost($id, $post, $update)
    {
        if (wp_is_post_revision($id) || wp_is_post_autosave($id)) {
            return;
        }

        $statuses = apply_filters('jamstack_deployments_post_statuses', ['publish', 'private', 'trash'], $id, $post);

        if (!in_array(get_post_status($id), $statuses, true)) {
            return;
        }

        $option = get_option(CRGEARY_JAMSTACK_DEPLOYMENTS_OPTIONS_KEY);
        $post_types = apply_filters('jamstack_deployments_post_types', $option['webhook_post_types'] ?: [], $id, $post);

        if (!in_array(get_post_type($id), $post_types)) {
            return;
        }

        self::fireWebhook();
    }

    public static function triggerSaveTerm($id, $tax_id, $tax_slug)
    {
        self::fireWebhook();
    }

    public static function triggerDeleteTerm($id, $tax_id, $tax_slug, $term, $object_ids)
    {
        self::fireWebhook();
    }

    public static function triggerEditTerm($id, $tax_id, $tax_slug)
    {
        self::fireWebhook();
    }

    /**
     * Show the admin bar css
     * 
     * @todo move this somewhere else
     * @return void
     */
    public static function adminBarCss()
    {
        if (!is_admin_bar_showing()) {
            return;
        }

        ?><style>

        #wpadminbar .wp-jamstack-deployments-button > a {
            background-color: rgba(255, 255, 255, .25);
            color: #FFFFFF !important;
        }
        #wpadminbar .wp-jamstack-deployments-button > a:hover,
        #wpadminbar .wp-jamstack-deployments-button > a:focus {
            background-color: rgba(255, 255, 255, .25) !important;
        }

        </style><?php
    }

    /**
     * Add a "trigger webhook" button to the admin bar
     *
     * @param object $bar
     * @return void
     */
    public static function adminBarTriggerButton($bar)
    {
        $uri = wp_nonce_url(
            admin_url('admin.php?page=wp-jamstack-deployments&action=jamstack-deployment-trigger'),
            'crgeary_jamstack_deployment_trigger',
            'crgeary_jamstack_deployment_trigger'
        );

        $bar->add_node([
            'id' => 'wp-jamstack-deployments',
            'title' => 'Deploy Website',
            'parent' => 'top-secondary',
            'href' => $uri,
            'meta' => [
                'class' => 'wp-jamstack-deployments-button'
            ]
        ]);
    }

    /**
     * Trigger a request manually from the admin settings
     *
     * @return void
     */
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

        $args = [
            'blocking' => false
        ];

        $method = mb_strtolower($option['webhook_method']);

        do_action('jamstack_deployments_before_fire_webhook');

        App::instance()->logger->info("A {$method} request was made to the webhook.");

        if (isset($option['webhook_method']) && $method === 'get') {
            $return = wp_safe_remote_get($option['webhook_url'], $args);
        } else {
            $return = wp_safe_remote_post($option['webhook_url'], $args);
        }

        do_action('jamstack_deployments_after_fire_webhook');

        return $return;
    }
}
