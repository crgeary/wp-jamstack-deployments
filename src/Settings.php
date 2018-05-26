<?php

namespace Crgeary\JAMstackDeployments;

class Settings
{
    public static function init()
    {
        add_action('admin_init', [__CLASS__, 'register']);
    }

    public static function register()
    {
        $key = CRGEARY_JAMSTACK_DEPLOYMENTS_OPTIONS_KEY;

        register_setting($key, $key, [__CLASS__, 'sanitize']);
        add_settings_section('general', 'General', '__return_empty_string', $key);
        
        // ...

        $option = get_option($key);

        add_settings_field('webhook_url', 'Webhook URL', [__CLASS__, 'url'], $key, 'general', [
            'name' => "{$key}[webhook_url]",
            'value' => isset($option['webhook_url']) ? $option['webhook_url'] : '',
            'description' => 'Your Webhook URL. See <a href="https://www.netlify.com/docs/webhooks/" target="_blank" rel="noopener noreferrer">Netlify docs</a>.'
        ]);

        add_settings_field('webhook_method', 'Webhook Method', [__CLASS__, 'select'], $key, 'general', [
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

    public static function url($args = [])
    {
        ?><div>
            <input type="url" class="regular-text" name="<?= esc_attr($args['name']); ?>" value="<?= esc_url($args['value']); ?>">
            <?= !empty($args['description']) ? "<p class=\"description\">{$args['description']}</p>" : ''; ?>
        </div><?php
    }

    public static function select($args = [])
    {
        ?><div>
            <select name="<?= esc_attr($args['name']); ?>">
                <?php foreach ($args['choices'] as $k => $v) : ?>
                    <option value="<?= esc_attr($k); ?>" <?php selected($k, $args['value']); ?>><?= $v; ?></option>
                <?php endforeach; ?>
            </select>
            <?= !empty($args['description']) ? "<p class=\"description\">{$args['description']}</p>" : ''; ?>
        </div><?php
    }

    public static function sanitize($input)
    {
        return $input;
    }
}