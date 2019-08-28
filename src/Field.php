<?php

namespace Crgeary\JAMstackDeployments;

class Field
{
    /**
     * Render an input[type=url] field
     *
     * @param array $args
     * @return void
     */
    public static function url($args = [])
    {
        ?><div>
            <input type="url" class="regular-text" name="<?= esc_attr($args['name']); ?>" value="<?= esc_url($args['value']); ?>">
            <?= !empty($args['description']) ? "<p class=\"description\">{$args['description']}</p>" : ''; ?>
        </div><?php
    }

    /**
     * Render a select field
     *
     * @param array $args
     * @return void
     */
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

    /**
     * Render a set of checkboxes
     *
     * @param array $args
     * @return void
     */
    public static function checkboxes($args = [])
    {   
        $args['value'] = is_array($args['value']) ? $args['value'] : [$args['value']];

        ?><fieldset>
            <legend class="screen-reader-text"><?= $args['legend']; ?></legend>
            <?php foreach ($args['choices'] as $k => $v) : ?>
                <label>
                    <input type="checkbox"
                        name="<?= esc_attr("{$args['name']}[]"); ?>"
                        value="<?= esc_attr($k); ?>"
                        <?php checked(true, in_array($k, $args['value'], true)); ?>
                    />
                    <?= "$v<span class='screen-reader-text'>, the key/name is </span> <code>{$k}</code>"; ?>
                </label><br />
            <?php endforeach; ?>
            <?= !empty($args['description']) ? "<p class=\"description\">{$args['description']}</p>" : ''; ?>
        </fieldset><?php
    }
}
