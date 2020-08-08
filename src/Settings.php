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
        add_settings_section('general', __( 'General', 'wp-jamstack-deployments'), '__return_empty_string', $key);
        
        // ...

        $option = jamstack_deployments_get_options();

        add_settings_field('webhook_url', __( 'Build Hook URL', 'wp-jamstack-deployments' ), ['Crgeary\JAMstackDeployments\Field', 'url'], $key, 'general', [
            'name' => "{$key}[webhook_url]",
            'value' => jamstack_deployments_get_webhook_url(),
            'description' => sprintf( __( 'Your Build Hook URL. This is the URL that is pinged to start building/deploying the JAMstack site. See <a href="%1s" target="_blank" rel="noopener noreferrer">Netlify docs</a> or see <a href="%2s" target="_blank" rel="noopener noreferrer">Zeit docs</a>.', 'wp-jamstack-deployments' ), 'https://docs.netlify.com/configure-builds/build-hooks/', 'https://zeit.co/docs/v2/advanced/deploy-hooks/' )
        ]); 

        add_settings_field('webhook_method', __( 'Hook Method', 'wp-jamstack-deployments' ), ['Crgeary\JAMstackDeployments\Field', 'select'], $key, 'general', [
            'name' => "{$key}[webhook_method]",
            'value' => jamstack_deployments_get_webhook_method(),
            'choices' => [
                'post' => 'POST',
                'get' => 'GET'
            ],
            'default' => 'post',
            'description' => __( 'Set either GET or POST for the build hook request. Defaults to POST.', 'wp-jamstack-deployments' )
        ]);

        add_settings_field('deployment_badge_url', __( 'Badge Image URL', 'wp-jamstack-deployments' ), ['Crgeary\JAMstackDeployments\Field', 'url'], $key, 'general', [
            'name' => "{$key}[deployment_badge_url]",
            'value' => self::getBadgeImageUrl($option),
            'description' => sprintf( __( 'Your Badge URL. Input the URL to display a badge with the current site status on your WordPress back end. See <a href="%1s" target="_blank" rel="noopener noreferrer">Netlify docs</a>.', 'wp-jamstack-deployments' ), 'https://docs.netlify.com/monitor-sites/status-badges/#add-status-badges' )
        ]);

        add_settings_field('deployment_badge_link_url', __( 'Badge Link', 'wp-jamstack-deployments' ), ['Crgeary\JAMstackDeployments\Field', 'url'], $key, 'general', [
            'name' => "{$key}[deployment_badge_link_url]",
            'value' => isset($option['deployment_badge_link_url']) ? $option['deployment_badge_link_url'] : '',
            'description' => sprintf( __( 'The link to your deployments. See <a href="%1s" target="_blank" rel="noopener noreferrer">Netlify docs</a>.', 'wp-jamstack-deployments' ), 'https://www.netlify.com/docs/continuous-deployment/' )
        ]);

        add_settings_field('webhook_post_types', __( 'Post Types', 'wp-jamstack-deployments' ), ['Crgeary\JAMstackDeployments\Field', 'checkboxes'], $key, 'general', [
            'name' => "{$key}[webhook_post_types]",
            'value' => isset($option['webhook_post_types']) ? $option['webhook_post_types'] : [],
            'choices' => self::getPostTypes(),
            'description' => __( 'Only selected post types will trigger a deployment when created, updated or deleted.', 'wp-jamstack-deployments' ),
            'legend' => 'Post Types'
        ]);

        add_settings_field('webhook_taxonomies', __( 'Taxonomies', 'wp-jamstack-deployments' ), ['Crgeary\JAMstackDeployments\Field', 'checkboxes'], $key, 'general', [
            'name' => "{$key}[webhook_taxonomies]",
            'value' => isset($option['webhook_taxonomies']) ? $option['webhook_taxonomies'] : [],
            'choices' => self::getTaxonomies(),
            'description' => __( 'Only selected taxonomies will trigger a deployment when their terms are created, updated or deleted.', 'wp-jamstack-deployments' ),
            'legend' => 'Taxonomies'
        ]);


        add_settings_field('webhook_post_statuses', __('Post Statuses', 'wp-jamstack-deployments'), ['Crgeary\JAMstackDeployments\Field', 'checkboxes'], $key, 'general', [
            'name' => "{$key}[webhook_post_statuses]",
            'value' => isset($option['webhook_post_statuses']) ? $option['webhook_post_statuses'] : ['private', 'publish', 'trash'],
            'choices' => self::getStatuses(),
            'description' => __('Only posts with the selected statuses will trigger a deployment.', 'wp-jamstack-deployments'),
            'legend' => 'Post Statuses'
        ]);

        add_settings_field('webhook_acf', __( 'ACF', 'wp-jamstack-deployments' ), ['Crgeary\JAMstackDeployments\Field', 'checkboxes'], $key, 'general', [
            'name' => "{$key}[webhook_acf]",
            'value' => isset($option['webhook_acf']) ? $option['webhook_acf'] : [],
            'choices' => [
                'options' => __('Options Page', 'wp-jamstack-deployments'),
            ],
            'description' => __( 'Only selected ACF locations will trigger a deployment when they\'re saved.', 'wp-jamstack-deployments' ),
            'legend' => 'ACF'
        ]);
    }

    /**
     * Get the badge image URL, has fallback to old option name
     *
     * @param array $option
     * @return string
     */
    protected static function getBadgeImageUrl($option)
    {
        if (!empty($option['deployment_badge_url'])) {
            return $option['deployment_badge_url'];
        }

        return !empty($option['netlify_badge_url']) ? $option['netlify_badge_url'] : '';
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
     * Get an array of statuses in name > label format
     *
     * @return array
     */
    protected static function getStatuses()
    {
        return array_merge(get_post_statuses(), ['trash' => __('Trash', 'wp-jamstack-deployments')]);
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
            $input['webhook_url'] = trim($input['webhook_url']);
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

        if (!isset($input['webhook_post_statuses']) || !is_array($input['webhook_post_statuses'])) {
            $input['webhook_post_statuses'] = [];
        }

        return $input;
    }
}
