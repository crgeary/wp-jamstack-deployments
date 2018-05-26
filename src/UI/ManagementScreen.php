<?php

namespace Crgeary\JAMstackDeployments\UI;

use Crgeary\JAMstackDeployments\View;

class ManagementScreen
{
    /**
     * Register the requred hooks for the admin screen
     *
     * @return void
     */
    public static function init()
    {
        add_action('admin_menu', [__CLASS__, 'addMenu']);
    }

    /**
     * Register an tools/management menu for the admin area
     *
     * @return void
     */
    public static function addMenu()
    {
        add_management_page(
            'JAMstack Deployments',
            'Deployments',
            'manage_options',
            'wp-jamstack-deployments',
            [__CLASS__, 'renderPage']
        );
    }

    /**
     * Render the management/tools page
     *
     * @return void
     */
    public static function renderPage()
    {
        ?><div class="wrap">

            <h2><?= get_admin_page_title(); ?></h2>

            <p>Webhook settings can be <a href="<?= esc_url(admin_url('/options-general.php?page=wp-jamstack-deployments-settings')); ?>">configured here</a></p>

            <h3>Logs</h3>

            <?= self::debugTable(); ?>

        </div><?php
    }

    protected static function debugTable()
    {
        $colors = [
            'error' => '#ef404a',
            'warning' => '#f36f31',
            'info' => '#008ccf',
            'debug' => 'inherit'
        ];

        if (!file_exists(CRGEARY_JAMSTACK_DEPLOYMENTS_DEBUG_FILE)) {
            return '<p>The debug/log file appears to be missing.</p>';
        }

        $entries = array_map('json_decode', file(CRGEARY_JAMSTACK_DEPLOYMENTS_DEBUG_FILE));
        $entries = array_filter($entries);

        return View::render('table.php', compact('colors', 'entries'));
    }
}