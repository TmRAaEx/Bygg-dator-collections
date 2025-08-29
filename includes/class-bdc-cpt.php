<?php

if (!defined('ABSPATH')) {
    exit;
}

class BDC_CPT
{
    public static function init()
    {
        add_action('init', [__CLASS__, 'register_collections_cpt']);
        add_filter('template_include', [__CLASS__, 'load_plugin_templates']);
    }

    public static function register_collections_cpt()
    {


        //TODO: Add date, creator




        $labels = [
            'name' => __('Kollektioner', 'bygg-dator-collections'),
            'singular_name' => __('Kollektion', 'bygg-dator-collections'),
            'add_new' => __('Skapa ny', 'bygg-dator-collections'),
            'add_new_item' => __('Skapa ny kollektion', 'bygg-dator-collections'),
            'edit_item' => __('Redigera kollektion', 'bygg-dator-collections'),
            'new_item' => __('Ny kollektion', 'bygg-dator-collections'),
            'view_item' => __('Visa kollektion', 'bygg-dator-collections'),
            'search_items' => __('Sök kollektioner', 'bygg-dator-collections'),
            'not_found' => __('Ingen kollektion hittades', 'bygg-dator-collections'),
            'not_found_in_trash' => __('Ingen kollektion hittades i papperskorgen', 'bygg-dator-collections'),
            'all_items' => __('Alla kollektioner', 'bygg-dator-collections'),
        ];

        $args = [
            'labels' => $labels,
            'public' => true,
            'has_archive' => true, 
            'show_in_menu' => true,
            'menu_position' => 25,
            'menu_icon' => 'dashicons-products',
            'supports' => ['title', 'editor', 'author', 'thumbnail'],
            'show_in_rest' => true,
            'rewrite' => [
                'slug' => 'kollektioner',  // URL: /kollektioner/
                'with_front' => false
            ],
        ];

        register_post_type('bdc_collection', $args);
    }

    /**
     * Load plugin templates for CPT
     */
    public static function load_plugin_templates($template)
    {
        if (is_post_type_archive('bdc_collection')) {
            $plugin_template = plugin_dir_path(__FILE__) . 'templates/archive-bdc_collection.php';
            if (file_exists($plugin_template))
                return $plugin_template;
        }

        if (is_singular('bdc_collection')) {
            $plugin_template = plugin_dir_path(__FILE__) . 'templates/single-bdc_collection.php';
            if (file_exists($plugin_template))
                return $plugin_template;
        }

        return $template;
    }
}

// Init CPT
BDC_CPT::init();
