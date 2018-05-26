<?php

namespace Crgeary\JAMstackDeployments;

class View
{
    /**
     * Echo, or return the view
     * 
     * @param string $view
     * @param array $data
     * @param boolean $return
     * @return void|string
     */
    public static function render($view, $data = [], $return = true)
    {
        if ($return) {
            ob_start();
            self::getView($view, $data);
            return ob_get_clean();
        }

        self::getView($view, $data);
    }

    /**
     * Render the view
     * 
     * @param string $__view
     * @param array $__data
     * @return void
     */
    protected static function getView($__view, $__data = [])
    {
        extract($__data);

        include (CRGEARY_JAMSTACK_DEPLOYMENTS_PATH."/views/{$__view}");
    }
}