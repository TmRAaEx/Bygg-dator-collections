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
        'exclude_from_search' => false,
        'has_archive' => true,
        'menu_icon' => 'dashicons-desktop',
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
        plugin_dir_url(__FILE__) . 'assets/css/pc-builds.css',
        [],
        filemtime(plugin_dir_path(__FILE__) . 'assets/css/pc-builds.css')

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
        'search_nonce' => wp_create_nonce('pc_builds_search')
    ]);
}
add_action('wp_enqueue_scripts', 'pc_builds_enqueue_scripts');


add_filter('template_include', 'pc_builds_load_templates');


//adds custom query var for user based filtering
// work in progress: not fully implemented
add_filter('query_vars', function ($vars) {
    $vars[] = 'u';
    return $vars;
});

// load templates 
function pc_builds_load_templates($template)
{

    $template_dir = plugin_dir_path(__FILE__) . 'templates/';

    if (is_singular('pc-build')) {
        $plugin_template = $template_dir . 'single-pc-build.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }

    if (is_page(__('my-builds', 'pc-builds'))) {
        $plugin_template = $template_dir . 'archive-my-builds.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }

    if (is_post_type_archive('pc-build')) {
        $plugin_template = $template_dir . 'archive-pc-build.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }

    return $template; // fallback to theme template
}


// make woocommerce products searchable
add_filter('woocommerce_product_data_store_cpt_get_products_query', function ($query, $query_vars) {
    if (!empty($query_vars['s'])) {
        $query['s'] = $query_vars['s'];
    }
    return $query;
}, 10, 2);

//add pc_build_id on order  
add_action('woocommerce_checkout_create_order_line_item', function ($item, $cart_item_key, $values, $order) {
    if (!empty($values['pc_build_id'])) {
        $item->add_meta_data('pc_build_id', absint($values['pc_build_id']), true);
    }
}, 10, 4);


add_action('woocommerce_payment_complete', 'bdc_give_coupon', 10, 1);



function bdc_generate_coupon(string $amount, WP_User $user)
{

    $coupon_code = 'pcbuild-' . wp_generate_password(8, false, false);
    $discount_type = 'percent';

    $coupon = array(
        'post_title' => $coupon_code,
        'post_content' => '',
        'post_status' => 'publish',
        'post_author' => 1,
        'post_type' => 'shop_coupon'
    );
    $new_coupon_id = wp_insert_post($coupon);

    update_post_meta($new_coupon_id, 'discount_type', $discount_type);
    update_post_meta($new_coupon_id, 'coupon_amount', $amount);
    update_post_meta($new_coupon_id, 'individual_use', 'yes');
    update_post_meta($new_coupon_id, 'usage_limit', 1);
    update_post_meta($new_coupon_id, 'customer_email', $user->user_email);

    return $coupon_code;

}
function bdc_mail_coupon(string $coupon_code, WP_User $user)
{
    $subject = __('Du har fått en rabattkod!', 'pc-builds');

    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/coupon-email.php';
    $message = ob_get_clean();

    add_filter('wp_mail_content_type', function () {
        return 'text/html';
    });

    $sent = wp_mail($user->user_email, $subject, $message);

    remove_filter('wp_mail_content_type', function () {
        return 'text/html';
    });
}

function bdc_give_coupon($order_id)
{
    $order = wc_get_order($order_id);
    if (!$order)
        return;

    foreach ($order->get_items() as $item) {
        $pc_build_id = $item->get_meta('pc_build_id');

        if (!$pc_build_id)
            continue;

        $creator_id = get_post_field('post_author', $pc_build_id);

        if (!$creator_id)
            continue;

        $user = get_userdata($creator_id);
        if (!$user)
            continue;

        $coupon_code = bdc_generate_coupon('10', $user);

        bdc_mail_coupon($coupon_code, $user);
    }
}
//debug printing with file inside plugin
if (!function_exists('pc_build_log')) {
    function pc_build_log($message)
    {
        $upload_dir = plugin_dir_path(__FILE__);
        $log_file = $upload_dir . 'pc-builds.log';

        $time = date("Y-m-d H:i:s");
        $entry = "[$time] " . print_r($message, true) . PHP_EOL;

        file_put_contents($log_file, $entry, FILE_APPEND);
    }
}

add_filter('wp_get_nav_menu_items', 'custom_menu_items_visibility', 10, 2);

function custom_menu_items_visibility($items, $menu)
{
    foreach ($items as $key => $item) {
        if ($item->title === __('Mina builds', 'pc-builds') && !is_user_logged_in()) {
            unset($items[$key]);
        }


    }
    return $items;
}

