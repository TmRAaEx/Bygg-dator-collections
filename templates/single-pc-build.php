<?php
if (!defined('ABSPATH'))
    exit;

get_header();

if (have_posts()):
    while (have_posts()):
        the_post();

        $products = get_post_meta(get_the_ID(), '_pc_build_products', true);
        $products = apply_filters('pc_build_display_products', $products, get_the_ID());
        ?>

        <h1><?php the_title(); ?></h1>
        <div><?php the_content(); ?></div>
        <div><img src="<?php echo the_post_thumbnail_url(); ?>"></div>
        <h2><?php _e('Produkter', 'pc-builds'); ?></h2>
        <ul class="pc-build-products">
            <?php foreach ($products as $pid):
                $product = wc_get_product($pid); ?>
                <li>
                    <a href="<?php echo get_permalink($pid); ?>">
                        <?php echo esc_html($product->get_name()); ?>
                    </a> - <?php echo wc_price($product->get_price()); ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <form method="post">
            <button type="submit" name="add_all_to_cart" value="1"><?php _e('Lägg alla i varukorgen', 'pc-builds'); ?></button>
        </form>

        <?php
        // Handle adding all products to cart
        if (isset($_POST['add_all_to_cart'])) {
            $cart_products = apply_filters('pc_build_cart_products', $products, get_the_ID());
            foreach ($cart_products as $pid) {
                WC()->cart->add_to_cart($pid, 1, 0, [], [
                    'pc_build_id' => get_the_ID()
                ]);
            }
            do_action('pc_build_added_to_cart', get_the_ID(), get_current_user_id());
            wc_add_notice(__('Alla produkter har lagts i varukorgen.', 'pc-builds'), 'success');
            exit;
        }


    endwhile;
endif;

get_footer();
