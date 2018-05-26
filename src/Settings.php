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

        $option = get_option($key);

        add_settings_field('webhook_url', 'Webhook URL', ['Crgeary\JAMstackDeployments\Field', 'url'], $key, 'general', [
            'name' => "{$key}[webhook_url]",
            'value' => isset($option['webhook_url']) ? $option['webhook_url'] : '',
            'description' => 'Your Webhook URL. See <a href="https://www.netlify.com/docs/webhooks/" target="_blank" rel="noopener noreferrer">Netlify docs</a>.'
        ]);

        add_settings_field('webhook_method', 'Webhook Method', ['Crgeary\JAMstackDeployments\Field', 'select'], $key, 'general', [
            'name' => "{$key}[webhook_method]",
            'value' => isset($option['webhook_method']) && in_array($option['webhook_method'], ['get', 'post']) ? $option['webhook_method'] : 'post',
            'choices' => [
                'post' => 'POST',
                'get' => 'GET'
            ],
            'default' => 'post',
            'description' => 'Set either GET or POST for the webhook request. Defaults to POST.'
        ]);
    }

    /**
     * Sanitize user input
     *
     * @var array $input
     * @return array
     */
    public static function sanitize($input)
    {
        if (isset($input['webhook_method']) && !in_array($input['webhook_method'], ['get', 'post'])) {
            $input['webhook_method'] = 'post';
        }

        return $input;
    }
}