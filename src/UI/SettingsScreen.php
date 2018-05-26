<?php

namespace Crgeary\JAMstackDeployments\UI;

class SettingsScreen
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
        add_options_page(
            'JAMstack Deployments',
            'Deployments',
            'manage_options',
            'wp-jamstack-deployments-settings',
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
            
            <form method="post" action="<?= esc_url(admin_url('options.php')); ?>">
                <?php

                settings_fields(CRGEARY_JAMSTACK_DEPLOYMENTS_OPTIONS_KEY);
                do_settings_sections(CRGEARY_JAMSTACK_DEPLOYMENTS_OPTIONS_KEY);

                submit_button('Save Settings', 'primary', 'submit', false);

                ?>
            </form>

        </div><?php
    }
}