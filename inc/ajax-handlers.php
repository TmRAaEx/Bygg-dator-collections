<?php
add_action('wp_ajax_pc_build_products_search', 'pc_build_products_search');
add_action('wp_ajax_nopriv_pc_build_products_search', 'pc_build_products_search'); // <-- add this line
function pc_build_products_search()
{
    check_ajax_referer('pc_builds_search', 'nonce');

    $q = sanitize_text_field($_GET['q'] ?? '');
    $products = wc_get_products(['limit' => 10, 'search' => $q]);

    $results = [];
    foreach ($products as $p) {
        $results[] = ['id' => $p->get_id(), 'text' => $p->get_name()];
    }

    wp_send_json($results);
}


add_action('wp_ajax_pc_build_create', 'pc_builds_create_callback');

function pc_builds_create_callback()
{
    check_ajax_referer('pc_builds_create', 'nonce');

    $title = sanitize_text_field($_POST['pc_build_title'] ?? '');
    $description = sanitize_textarea_field($_POST['pc_build_description'] ?? '');
    $category = intval($_POST['pc_build_category'] ?? 0);
    $products = array_map('intval', $_POST['pc_build_products'] ?? []);

    if (!$title || count($products) < 2) {
        wp_send_json_error(__('Please provide a title and select at least 2 products.', 'pc-builds'));
    }

    $post_id = wp_insert_post([
        'post_title' => $title,
        'post_content' => $description,
        'post_type' => 'pc-build',
        'post_status' => 'publish',
        'post_author' => get_current_user_id(),
    ]);

    if ($post_id) {
        wp_set_post_terms($post_id, [$category], 'pc_build_category');
        update_post_meta($post_id, '_pc_build_products', $products);

        if (!empty($_FILES['pc_build_image']) && !empty($_FILES['pc_build_image']['tmp_name'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attachment_id = media_handle_upload('pc_build_image', $post_id);

            if (is_wp_error($attachment_id)) {
                wp_send_json_error(__('Failed to upload image.', 'pc-builds'));
            } else {
                set_post_thumbnail($post_id, $attachment_id);
            }

        }

        wp_send_json_success(['url' => get_permalink($post_id)]);
    }

    wp_send_json_error(__('Failed to create PC Build.', 'pc-builds'));
}
