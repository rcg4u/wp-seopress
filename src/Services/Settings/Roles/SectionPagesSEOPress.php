<?php

namespace SEOPress\Services\Settings\Roles;

if ( ! defined('ABSPATH')) {
    exit;
}

class SectionPagesSEOPress {
    /**
     * @since 4.6.0
     *
     * @param string $keySettings
     *
     * @return void
     */
    public function render($keySettings) {
        $options = seopress_get_service('AdvancedOption')->getOption();

        global $wp_roles;

        if ( ! isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }

        foreach ($wp_roles->get_names() as $key => $value) {
            if ('administrator' === $key) {
                continue;
            }
            $uniqueKey   = sprintf('%s_%s', $keySettings, $key);
            $nameKey     = \sprintf('%s_%s', 'seopress_advanced_security_metaboxe', $keySettings);
            $dataOptions = isset($options[$nameKey]) ? $options[$nameKey] : [];

            if ($uniqueKey ==='titles-metas_editor') { ?>
                <p class="description"><?php _e('Check a user role to authorized it to edit a specific SEO page.','wp-seopress'); ?></p><br>
            <?php } ?>

            <div>
                <input
                    type="checkbox"
                    id="seopress_advanced_security_metaboxe_role_pages_<?php echo $uniqueKey; ?>"
                    value="1"
                    name="seopress_advanced_option_name[<?php echo $nameKey; ?>][<?php echo $key; ?>]"
                    <?php if (isset($dataOptions[$key])) {
                checked($dataOptions[$key], '1');
            } ?>
                />
                <label for="seopress_advanced_security_metaboxe_role_pages_<?php echo $uniqueKey; ?>">
                    <strong><?php echo $value; ?></strong> (<em><?php echo translate_user_role($value,  'default'); ?></em>)
                </label>
            </div>
            <?php
        }
    }

    /**
     * @since 4.6.0
     *
     * @param string $name
     * @param array  $params
     *
     * @return void
     */
    public function __call($name, $params) {
        $functionWithKey = explode('_', $name);
        if ( ! isset($functionWithKey[1])) {
            return;
        }

        $this->render($functionWithKey[1]);
    }

    /**
     * @since 4.6.0
     *
     * @return void
     */
    public function printSectionPages() {
        global $submenu;
        if ( ! isset($submenu['seopress-option'])) {
            return;
        }
        $menus = $submenu['seopress-option'];

        foreach ($menus as $key => $item) {
            $keyClean = sanitize_title($item[0]);
            if (in_array($keyClean, ['dashboard', 'license', 'schemas', 'redirections', 'broken-links'])) {
                continue;
            }

            add_settings_field(
                'seopress_advanced_security_metaboxe_' . $keyClean,
                $item[0],
                [$this, sprintf('render_%s', $keyClean)],
                'seopress-settings-admin-advanced-security',
                'seopress_setting_section_advanced_security'
            );
        }

        do_action('seopress_add_settings_field_advanced_security');
    }
}
