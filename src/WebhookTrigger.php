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

        add_action('admin_footer', [__CLASS__, 'adminBarCssAndJs']);
        add_action('wp_footer', [__CLASS__, 'adminBarCssAndJs']);
        
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueueScripts']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueueScripts']);

        add_action('wp_ajax_wp_jamstack_deployments_manual_trigger', [__CLASS__, 'ajaxTrigger']);
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

        $saved_post_statuses = isset($option['webhook_post_statuses']) ? $option['webhook_post_statuses'] : ['publish', 'private', 'trash'];
        $statuses = apply_filters('jamstack_deployments_post_statuses', $saved_post_statuses, $id, $post);

        if (!in_array(get_post_status($id), $statuses, true)) {
            return;
        }

        $option = jamstack_deployments_get_options();
        $saved_post_types = isset($option['webhook_post_types']) ? $option['webhook_post_types'] : [];
        $post_types = apply_filters('jamstack_deployments_post_types', $saved_post_types, $id, $post);

        if (!in_array(get_post_type($id), $post_types, true)) {
            return;
        }

        self::fireWebhook();
    }

    /**
     * Fire a request to the webhook when a term has been created.
     *
     * @param int $id
     * @param int $post
     * @param string $tax_slug
     * @return void
     */
    public static function triggerSaveTerm($id, $tax_id, $tax_slug)
    {
        if (!self::canFireForTaxonomy($id, $tax_id, $tax_slug)) {
            return;
        }

        self::fireWebhook();
    }

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
    public static function triggerDeleteTerm($id, $tax_id, $tax_slug, $term, $object_ids)
    {
        if (!self::canFireForTaxonomy($id, $tax_id, $tax_slug)) {
            return;
        }

        self::fireWebhook();
    }

    /**
     * Fire a request to the webhook when a term has been modified.
     *
     * @param int $id
     * @param int $post
     * @param string $tax_slug
     * @return void
     */
    public static function triggerEditTerm($id, $tax_id, $tax_slug)
    {
        if (!self::canFireForTaxonomy($id, $tax_id, $tax_slug)) {
            return;
        }
        
        self::fireWebhook();
    }

    /**
     * Check if the given taxonomy is one that should fire the webhook
     *
     * @param int $id
     * @param int $tax_id
     * @param string $tax_slug
     * @return boolean
     */
    protected static function canFireForTaxonomy($id, $tax_id, $tax_slug)
    {
        $option = jamstack_deployments_get_options();
        $taxonomies = apply_filters('jamstack_deployments_taxonomies', $option['webhook_taxonomies'] ?: [], $id, $tax_id);

        return in_array($tax_slug, $taxonomies, true);
    }

    /**
     * Show the admin bar css & js
     * 
     * @todo move this somewhere else
     * @return void
     */
    public static function adminBarCssAndJs()
    {
        if (!is_admin_bar_showing()) {
            return;
        }

        ?><style>

        #wpadminbar .wp-jamstack-deployments-button > a {
            background-color: rgba(255, 255, 255, .2) !important;
            color: #FFFFFF !important;
        }
        #wpadminbar .wp-jamstack-deployments-button > a:hover,
        #wpadminbar .wp-jamstack-deployments-button > a:focus {
            background-color: rgba(255, 255, 255, .25) !important;
        }

        #wpadminbar .wp-jamstack-deployments-button svg {
            width: 12px;
            height: 12px;
            margin-left: 5px;
        }

        #wpadminbar .wp-jamstack-deployments-badge > .ab-item {
            display: flex;
            align-items: center;
        }

        </style><?php
    }

    /**
     * Enqueue js to the admin & frontend
     * 
     * @return void
     */
    public static function enqueueScripts()
    {
        wp_enqueue_script(
            'wp-jamstack-deployments-adminbar',
            CRGEARY_JAMSTACK_DEPLOYMENTS_URL.'/assets/admin.js',
            ['jquery'],
            filemtime(CRGEARY_JAMSTACK_DEPLOYMENTS_PATH.'/assets/admin.js')
        );

        $button_nonce = wp_create_nonce('wp-jamstack-deployments-button-nonce');

        wp_localize_script('wp-jamstack-deployments-adminbar', 'wpjd', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'deployment_button_nonce' => $button_nonce,
        ]);
    }

    /**
     * Add a "trigger webhook" button to the admin bar
     *
     * @param object $bar
     * @return void
     */
    public static function adminBarTriggerButton($bar)
    {
        $option = jamstack_deployments_get_options();
        $image = '';

        if (!empty($option['deployment_badge_url'])) {
            $image = $option['deployment_badge_url'];
        } else if (!empty($option['netlify_badge_url'])) {
            $image = $option['netlify_badge_url'];
        }

        if (!empty($image)) {
            $bar->add_node([
                'id' => 'wp-jamstack-deployments-netlify-badge',
                'title' => sprintf('<img src="%s" alt />', $image),
                'href' => empty($option['deployment_badge_link_url']) ? 'javascript:void(0)' : $option['deployment_badge_link_url'],
                'parent' => 'top-secondary',
                'meta' => [
                    'class' => 'wp-jamstack-deployments-badge',
                    'target' => empty($option['deployment_badge_link_url']) ? '_self' : '_blank',
                ]
            ]);
        }

        $bar->add_node([
            'id' => 'wp-jamstack-deployments',
            'title' => 'Deploy Website <svg aria-hidden="true" focusable="false" data-icon="upload" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M296 384h-80c-13.3 0-24-10.7-24-24V192h-87.7c-17.8 0-26.7-21.5-14.1-34.1L242.3 5.7c7.5-7.5 19.8-7.5 27.3 0l152.2 152.2c12.6 12.6 3.7 34.1-14.1 34.1H320v168c0 13.3-10.7 24-24 24zm216-8v112c0 13.3-10.7 24-24 24H24c-13.3 0-24-10.7-24-24V376c0-13.3 10.7-24 24-24h136v8c0 30.9 25.1 56 56 56h80c30.9 0 56-25.1 56-56v-8h136c13.3 0 24 10.7 24 24zm-124 88c0-11-9-20-20-20s-20 9-20 20 9 20 20 20 20-9 20-20zm64 0c0-11-9-20-20-20s-20 9-20 20 9 20 20 20 20-9 20-20z"></path></svg>',
            'parent' => 'top-secondary',
            'href' => 'javascript:void(0)',
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

        wp_redirect(admin_url('admin.php?page=wp-jamstack-deployments-settings'));
        exit;
    }

    /**
     * Trigger a webhook when a post transitions to published
     *
     * @param string $new
     * @param string $old
     * @param WP_Post $post
     *
     * @return void
     */
    public static function triggerPostTransition($new, $old, $post)
    {
        $id = $post->ID;
        $option = jamstack_deployments_get_options();
        
        $saved_post_types = isset($option['webhook_post_types']) ? $option['webhook_post_types'] : [];
        $post_types = apply_filters('jamstack_deployments_post_types', $saved_post_types, $id, $post);

        if (!in_array(get_post_type($id), $post_types, true)) {
            return;
        }

        $saved_post_statuses = isset($option['webhook_post_statuses']) ? $option['webhook_post_statuses'] : ['publish', 'private', 'trash'];
        $statuses = apply_filters('jamstack_deployments_post_statuses', $saved_post_statuses, $id, $post);

        if (!in_array(get_post_status($id), $statuses, true)) {
            return;
        }

        self::fireWebhook();
    }

    /**
     * Trigger a request manually from the admin settings
     *
     * @return void
     */
    public static function ajaxTrigger()
    {
        check_ajax_referer('wp-jamstack-deployments-button-nonce', 'security');

        self::fireWebhook();

        echo 1;
        exit;
    }

    /**
     * Fire off a request to the webhook
     *
     * @return WP_Error|array
     */
    public static function fireWebhook()
    {
        $webhook = jamstack_deployments_get_webhook_url();

        if (!$webhook) {
            return;
        }

        if (false === filter_var($webhook, FILTER_VALIDATE_URL)) {
            return;
        }

        $args = apply_filters('jamstack_deployments_webhook_request_args', [
            'blocking' => false
        ]);

        $method = jamstack_deployments_get_webhook_method();

        do_action('jamstack_deployments_before_fire_webhook');

        if ($method === 'get') {
            $return = wp_safe_remote_get($webhook, $args);
        } else {
            $return = wp_safe_remote_post($webhook, $args);
        }

        do_action('jamstack_deployments_after_fire_webhook');

        return $return;
    }
}
