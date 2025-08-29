<?php

if (!defined('ABSPATH'))
    exit;

class BDC_Shortcodes
{

    public static function init()
    {
        add_shortcode('bdc_collection_form', [__CLASS__, 'render_collection_form']);
    }

    public static function render_collection_form()
    {
        if (!is_user_logged_in()) {
            return '<p>Du måste vara inloggad för att skapa en kollektion.</p>';
        }



        ob_start();
        ?>
        <form method="post" class="bdc-collection-form">
            <?php wp_nonce_field('bdc_save_collection', 'bdc_collection_nonce'); ?>

            <label for="collection_title">Kollektionens namn:</label>
            <input type="text" name="collection_title" id="collection_title" required>

            <label for="bdc-selected-products">Produkter i kollektionen:</label>
            <ul id="bdc-selected-products">
                <!-- Selected products injected by js -->
            </ul>
            <button type="button" id="bdc-add-product" class="button">Lägg till produkt</button>

            <label for="collection_image" id="image_upload-label">Ladda upp bild: <img
                    src="<?php echo plugin_dir_url(__FILE__); ?>../assets/images/upload.svg" alt="">
                <input type="file" name="collection_image" id="collection_image" accept="image/*">
            </label>

            <button type="submit">Skapa kollektion</button>
        </form>

        <!-- Modal -->
        <div id="bdc-product-modal" style="display:none;">
            <input type="text" id="bdc-product-search" placeholder="Sök produkt...">
            <select id="bdc-product-category-filter">
                <option value="">Alla kategorier</option>
                <?php
                $categories = get_terms([
                    'taxonomy' => 'product_cat',
                    'hide_empty' => false,
                ]);
                foreach ($categories as $cat) {
                    echo '<option value="' . esc_attr($cat->term_id) . '">' . esc_html($cat->name) . '</option>';
                }
                ?>
            </select>
            <ul id="bdc-product-results">
                <!-- Search results injected by js -->
            </ul>
            <button type="button" id="bdc-close-modal">Stäng</button>
        </div>
        <?php
        return ob_get_clean();
    }


    public static function save_collection($data)
    {
        $user_id = get_current_user_id();

        // Skapa CPT
        $post_id = wp_insert_post([
            'post_title' => sanitize_text_field($data['collection_title']),
            'post_type' => 'bdc_collection',
            'post_status' => 'publish',
            'post_author' => $user_id,
        ]);

        if (!$post_id)
            return;

        // Koppla produkter
        if (!empty($data['products'])) {
            update_post_meta($post_id, '_bdc_collection_products', array_map('intval', $data['products']));
        }

        // Hantera uppladdad bild
        if (!empty($_FILES['collection_image']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');

            $attachment_id = media_handle_upload('collection_image', $post_id);
            if (!is_wp_error($attachment_id)) {
                set_post_thumbnail($post_id, $attachment_id);
            }
        }
    }
}

add_action('wp_ajax_bdc_search_products', 'bdc_search_products');
add_action('wp_ajax_nopriv_bdc_search_products', 'bdc_search_products');


// AJAX handler to search for products
function bdc_search_products()
{
    $search = sanitize_text_field($_GET['search'] ?? '');
    $category = intval($_GET['category'] ?? 0);

    $args = ['limit' => 10, 'status' => 'publish'];

    if ($search)
        $args['search'] = $search;
    if ($category)
        $args['category'] = [$category];

    $products = wc_get_products($args);
    $result = [];

    foreach ($products as $p) {
        $result[] = ['id' => $p->get_id(), 'name' => $p->get_name()];
    }

    wp_send_json($result);
}
//AJAX handler for saving collection
add_action('wp_ajax_bdc_save_collection', function () {
    if (!isset($_POST['bdc_collection_nonce']) || !wp_verify_nonce($_POST['bdc_collection_nonce'], 'bdc_save_collection')) {
        wp_send_json_error('Ogiltig nonce');
    }

    $data = [
        'collection_title' => $_POST['collection_title'] ?? '',
        'products' => $_POST['products'] ?? [],
    ];

    BDC_Shortcodes::save_collection($data);

    wp_send_json_success(['message' => 'Kollektionen skapad!']);
});
