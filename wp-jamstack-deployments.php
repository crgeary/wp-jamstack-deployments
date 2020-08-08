<?php

/**
 * Plugin Name: JAMstack Deployments
 * Plugin URI: https://github.com/crgeary/wp-jamstack-deployments
 * Description: A WordPress plugin for JAMstack deployments on Netlify (and other platforms).
 * Author: Christopher Geary
 * Author URI: https://crgeary.com
 * Version: 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

define('CRGEARY_JAMSTACK_DEPLOYMENTS_FILE', __FILE__);
define('CRGEARY_JAMSTACK_DEPLOYMENTS_PATH', untrailingslashit(plugin_dir_path(__FILE__)));
define('CRGEARY_JAMSTACK_DEPLOYMENTS_URL', untrailingslashit(plugin_dir_url(__FILE__)));

require_once (CRGEARY_JAMSTACK_DEPLOYMENTS_PATH.'/src/App.php');

Crgeary\JAMstackDeployments\App::instance();
