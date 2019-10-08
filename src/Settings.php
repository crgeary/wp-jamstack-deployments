<?php

namespace Crgeary\JAMstackDeployments;

class Settings
{
    /**
     * Setup required hooks for the Settings
     *
     * @return void
     */
    public static function init()
    {
        add_action('admin_init', [__CLASS__, 'register']);
    }

    /**
     * Register settings & fields
     *
     * @return void
     */
    public static function register()
    {
        $key = CRGEARY_JAMSTACK_DEPLOYMENTS_OPTIONS_KEY;

        register_setting($key, $key, [__CLASS__, 'sanitize']);
        add_settings_section('general', 'General', '__return_empty_string', $key);
        
        // ...

        $option = jamstack_deployments_get_options();

        add_settings_field('webhook_url', 'Webhook URL', ['Crgeary\JAMstackDeployments\Field', 'url'], $key, 'general', [
            'name' => "{$key}[webhook_url]",
            'value' => jamstack_deployments_get_webhook_url(),
            'description' => 'Your Webhook URL. See <a href="https://www.netlify.com/docs/webhooks/" target="_blank" rel="noopener noreferrer">Netlify docs</a>.'
        ]);

        add_settings_field('webhook_method', 'Webhook Method', ['Crgeary\JAMstackDeployments\Field', 'select'], $key, 'general', [
            'name' => "{$key}[webhook_method]",
            'value' => jamstack_deployments_get_webhook_method(),
            'choices' => [
                'post' => 'POST',
                'get' => 'GET'
            ],
            'default' => 'post',
            'description' => 'Set either GET or POST for the webhook request. Defaults to POST.'
        ]);

        add_settings_field('netlify_badge_url', 'Badge Image URL', ['Crgeary\JAMstackDeployments\Field', 'url'], $key, 'general', [
            'name' => "{$key}[netlify_badge_url]",
            'value' => isset($option['netlify_badge_url']) ? $option['netlify_badge_url'] : '',
            'description' => 'Your Badge URL. See <a href="https://www.netlify.com/docs/continuous-deployment/" target="_blank" rel="noopener noreferrer">Netlify docs</a>.'
        ]);

        add_settings_field('deployment_badge_link_url', 'Badge Link', ['Crgeary\JAMstackDeployments\Field', 'url'], $key, 'general', [
            'name' => "{$key}[deployment_badge_link_url]",
            'value' => isset($option['deployment_badge_link_url']) ? $option['deployment_badge_link_url'] : '',
            'description' => 'The link to your deployments. See <a href="https://www.netlify.com/docs/continuous-deployment/" target="_blank" rel="noopener noreferrer">Netlify docs</a>.'
        ]);

        add_settings_field('webhook_post_types', 'Post Types', ['Crgeary\JAMstackDeployments\Field', 'checkboxes'], $key, 'general', [
            'name' => "{$key}[webhook_post_types]",
            'value' => isset($option['webhook_post_types']) ? $option['webhook_post_types'] : [],
            'choices' => self::getPostTypes(),
            'description' => 'Only selected post types will trigger a deployment when created, updated or deleted.',
            'legend' => 'Post Types'
        ]);

        add_settings_field('webhook_taxonomies', 'Taxonomies', ['Crgeary\JAMstackDeployments\Field', 'checkboxes'], $key, 'general', [
            'name' => "{$key}[webhook_taxonomies]",
            'value' => isset($option['webhook_taxonomies']) ? $option['webhook_taxonomies'] : [],
            'choices' => self::getTaxonomies(),
            'description' => 'Only selected taxonomies will trigger a deployment when their terms are created, updated or deleted.',
            'legend' => 'Taxonomies'
        ]);
    }

    /**
     * Get an array of post types in name > label format
     *
     * @return array
     */
    protected static function getPostTypes()
    {
        $return = [];

        foreach (get_post_types(null, 'objects') as $choice) {
            $return[$choice->name] = $choice->labels->name;
        }

        return $return;
    }

    /**
     * Get an array of taxonomies in name > label format
     *
     * @return array
     */
    protected static function getTaxonomies()
    {
        $return = [];

        foreach (get_taxonomies(null, 'objects') as $choice) {
            $return[$choice->name] = $choice->labels->name;
        }

        return $return;
    }

    /**
     * Sanitize user input
     *
     * @var array $input
     * @return array
     */
    public static function sanitize($input)
    {
        if (!empty($input['webhook_url'])) {
            $input['webhook_url'] = sanitize_text_field($input['webhook_url']);
        }

        if (isset($input['webhook_method']) && !in_array($input['webhook_method'], ['get', 'post'])) {
            $input['webhook_method'] = 'post';
        }

        if (!isset($input['webhook_post_types']) || !is_array($input['webhook_post_types'])) {
            $input['webhook_post_types'] = [];
        }

        if (!isset($input['webhook_taxonomies']) || !is_array($input['webhook_taxonomies'])) {
            $input['webhook_taxonomies'] = [];
        }

        return $input;
    }
}
