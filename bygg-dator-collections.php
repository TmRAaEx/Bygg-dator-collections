<?php

/**
 * Plugin Name: Bygg dator – PC Builds
 * Description: Registers a custom post type for PC builds so users can create and share complete computer setups.
 * Version:     1.0.0
 * Requires Plugins: woocommerce
 * Author:      tmraaex
 * Author URI: https://alexander-hirsch.se/en
 * Text Domain: pc-builds
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}


/**
 * Load plugin text domain for translations
 */
function pc_builds_load_textdomain()
{
    load_plugin_textdomain('pc-builds', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'pc_builds_load_textdomain');


/**
 * Register Custom Post Type: PC Builds
 */
function pc_builds_register_cpt()
{
    $labels = array(
        'name' => __('PC Builds', 'pc-builds'),
        'singular_name' => __('PC Build', 'pc-builds'),
        'menu_name' => __('PC Builds', 'pc-builds'),
        'name_admin_bar' => __('PC Build', 'pc-builds'),
        'add_new' => __('Add New', 'pc-builds'),
        'add_new_item' => __('Add New PC Build', 'pc-builds'),
        'edit_item' => __('Edit PC Build', 'pc-builds'),
        'new_item' => __('New PC Build', 'pc-builds'),
        'view_item' => __('View PC Build', 'pc-builds'),
        'search_items' => __('Search PC Builds', 'pc-builds'),
        'not_found' => __('No PC Builds found', 'pc-builds'),
        'not_found_in_trash' => __('No PC Builds found in Trash', 'pc-builds'),
        'all_items' => __('All PC Builds', 'pc-builds'),
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-desktop', // WordPress icon
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'show_in_rest' => true, // Enables Gutenberg/REST API
        'rewrite' => array('slug' => 'pc-builds'),
    );

    register_post_type('pc-build', $args);
}
add_action('init', 'pc_builds_register_cpt');


/**
 * Register taxonomy for PC Build Categories
 */
function pc_builds_register_taxonomy()
{

    $labels = array(
        'name' => __('PC Build Categories', 'pc-builds'),
        'singular_name' => __('PC Build Category', 'pc-builds'),
        'search_items' => __('Search PC Build Categories', 'pc-builds'),
        'all_items' => __('All PC Build Categories', 'pc-builds'),
        'edit_item' => __('Edit PC Build Category', 'pc-builds'),
        'update_item' => __('Update PC Build Category', 'pc-builds'),
        'add_new_item' => __('Add New PC Build Category', 'pc-builds'),
        'new_item_name' => __('New PC Build Category Name', 'pc-builds'),
        'menu_name' => __('PC Build Categories', 'pc-builds'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'pc-build-category'),
    );

    register_taxonomy('pc_build_category', array('pc-build'), $args);
}
add_action('init', 'pc_builds_register_taxonomy');


// Include shortcode file + ajax handlers
require_once plugin_dir_path(__FILE__) . 'inc/shortcode-create-pc-build.php';
require_once plugin_dir_path(__FILE__) . 'inc/ajax-handlers.php';

/**
 * Load all scripts needed
 * @return void
 */
function pc_builds_enqueue_scripts()
{
    // Enqueue Select2 for searchable select
    wp_enqueue_style(
        'select2-css',
        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
    );
    wp_enqueue_script(
        'select2',
        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
        ['jquery'],
        null,
        true
    );
    //load styles
    wp_enqueue_style(
        'pc-builds-css',
        plugin_dir_url(__FILE__) . 'assets/css/pc-builds.css'
    );


    //load ajax
    wp_enqueue_script(
        'pc-builds-ajax',
        plugin_dir_url(__FILE__) . 'assets/js/pc-builds-ajax.js',
        ['jquery', 'select2'],
        null,
        true
    );

    // Localize AJAX URL & nonce for JS
    wp_localize_script('pc-builds-ajax', 'pcBuildsAjax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('pc_builds_create'),
    ]);
}
add_action('wp_enqueue_scripts', 'pc_builds_enqueue_scripts');


add_filter('template_include', 'pc_builds_load_templates');


// load templates 
function pc_builds_load_templates($template)
{
    if (is_singular('pc-build')) {
        $plugin_template = plugin_dir_path(__FILE__) . 'templates/single-pc-build.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }

    if (is_post_type_archive('pc-build')) {
        $plugin_template = plugin_dir_path(__FILE__) . 'templates/archive-pc-build.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }

    return $template; // fallback to theme template
}
