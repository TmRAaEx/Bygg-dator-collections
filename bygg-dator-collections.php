<?php
/**
 * Plugin Name: Bygg Dator Collections
 * Description: Plugin to handle the custom user created collections.
 */



include_once plugin_dir_path(__FILE__) . 'includes/class-bdc-cpt.php';

if (class_exists('BDC_CPT')) {
    BDC_CPT::init();
}
include_once plugin_dir_path(__FILE__) . 'includes/class-bdc-shortcodes.php';
BDC_Shortcodes::init();




class BDC_Assets
{

    public static function init()
    {
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_styles']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_js']);
    }

    public static function enqueue_styles()
    {
        wp_enqueue_style(
            'bdc-plugin-style',
            plugin_dir_url(__FILE__) . 'assets/css/bdc-plugin.css',
            [],
            filemtime(plugin_dir_path(__FILE__) . 'assets/css/bdc-plugin.css'),
            'all'
        );
    }

    public static function enqueue_js()
    {
        wp_enqueue_script(
            'bdc-create-collection',
            plugin_dir_url(__FILE__) . 'assets/js/create-collection.js',
            [],
            '1.0',
            true
        );


        wp_localize_script('bdc-create-collection', 'bdc_ajax_object', [
            'ajax_url' => admin_url('admin-ajax.php'),
        ]);
    }
}

BDC_Assets::init();


//register templates
add_filter('template_include', function ($template) {
    if (is_post_type_archive('bdc_collection')) {
        $plugin_template = plugin_dir_path(__FILE__) . 'templates/archive-bdc_collection.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }

    if (is_singular('bdc_collection')) {
        $plugin_template = plugin_dir_path(__FILE__) . 'templates/single-bdc_collection.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
    return $template;
});
