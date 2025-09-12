<?php
if (!defined('ABSPATH'))
    exit;

function pc_builds_creation_form_shortcode()
{

    if (!is_user_logged_in()) {
        return '<p>' . __('You must be logged in to create a PC Build.', 'pc-builds') . '</p>';
    }

    ob_start();
    ?>
    <form id="pc-build-form" class="pc-build-form">
        <?php wp_nonce_field('pc_builds_create', 'pc_builds_nonce'); ?>

        <p class="pc-build-field">
            <label><?php _e('Namn på bygget', 'pc-builds'); ?></label>
            <input type="text" name="pc_build_title" required>
        </p>

        <p class="pc-build-field">
            <label><?php _e('Bild på bygget', 'pc-builds') ?></label>
            <input type="file" name="pc_build_image">
        </p>

        <p class="pc-build-field">
            <label><?php _e('Beskrivning', 'pc-builds'); ?></label>
            <textarea name="pc_build_description" rows="5"></textarea>
        </p>

        <p class="pc-build-field">
            <label><?php _e('Kategori', 'pc-builds'); ?></label>
            <?php
            wp_dropdown_categories([
                'taxonomy' => 'pc_build_category',
                'name' => 'pc_build_category',
                'show_option_none' => __('Välj kategori', 'pc-builds'),
                'hide_empty' => false
            ]);
            ?>
        </p>

        <p class="pc-build-field">
            <label><?php _e('Välj produkter (minst 2)', 'pc-builds'); ?></label>
            <select id="pc_build_products" name="pc_build_products[]" multiple required></select>
        </p>

        <p class="pc-build-field">
            <button type="submit"><?php _e('Skapa PC-build', 'pc-builds'); ?></button>
        </p>

        <div id="pc-build-message"></div>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('pc_build_creation_form', 'pc_builds_creation_form_shortcode');
