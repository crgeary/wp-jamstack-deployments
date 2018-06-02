<?php

namespace Crgeary\JAMstackDeployments;

use Crgeary\JAMstackDeployments\UI\SettingsScreen;
use Crgeary\JAMstackDeployments\WebhookTrigger;
use Crgeary\JAMstackDeployments\Settings;

class App
{
    /**
     * Singleton instance
     * 
     * @var null|App
     */
    protected static $instance = null;

    /**
     * Create a new singleton instance
     * 
     * @return App
     */
    public static function instance()
    {
        if (!is_a(App::$instance, App::class)) {
            App::$instance = new App;
        }

        return App::$instance;
    }

    /**
     * Bootstrap the plugin
     * 
     * @return void
     */
    protected function __construct()
    {
        $this->constants();
        $this->includes();
        $this->hooks();
    }

    /**
     * Register constants
     *
     * @return void
     */
    protected function constants()
    {
        define('CRGEARY_JAMSTACK_DEPLOYMENTS_OPTIONS_KEY', 'wp_jamstack_deployments');
    }

    /**
     * Include/require files
     *
     * @return void
     */
    protected function includes()
    {
        require_once (CRGEARY_JAMSTACK_DEPLOYMENTS_PATH.'/src/UI/SettingsScreen.php');

        require_once (CRGEARY_JAMSTACK_DEPLOYMENTS_PATH.'/src/Settings.php');
        require_once (CRGEARY_JAMSTACK_DEPLOYMENTS_PATH.'/src/WebhookTrigger.php');
        require_once (CRGEARY_JAMSTACK_DEPLOYMENTS_PATH.'/src/Field.php');

        require_once (CRGEARY_JAMSTACK_DEPLOYMENTS_PATH.'/src/functions.php');
    }

    /**
     * Register actions & filters
     *
     * @return void
     */
    protected function hooks()
    {
        register_activation_hook(CRGEARY_JAMSTACK_DEPLOYMENTS_FILE, [$this, 'activation']);
        register_deactivation_hook(CRGEARY_JAMSTACK_DEPLOYMENTS_FILE, [$this, 'deactivation']);

        SettingsScreen::init();
        Settings::init();
        WebhookTrigger::init();
    }

    /**
     * Fires on plugin activation
     *
     * @return void
     */
    public function activation()
    {
        
    }

    /**
     * Fires on plugin deactivation
     *
     * @return void
     */
    public function deactivation()
    {

    }
}
