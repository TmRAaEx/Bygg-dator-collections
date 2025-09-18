<?php
if (!defined('ABSPATH'))
    exit;

get_header();

if (have_posts()):
    while (have_posts()):
        the_post();

        $products = get_post_meta(get_the_ID(), '_pc_build_products', true);
        $products = apply_filters('pc_build_display_products', $products, get_the_ID());

        $total_price = 0;
        ?>

        <h1><?php the_title(); ?></h1>
        <div class="pc-build-content"><?php the_content(); ?></div>

        <?php if (has_post_thumbnail()): ?>
            <div class="pc-build-thumbnail">
                <img src="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'large')); ?>"
                    alt="<?php the_title_attribute(); ?>">
            </div>
        <?php endif; ?>

        <h2><?php _e('Produkter', 'pc-builds'); ?></h2>
        <ul class="pc-build-products">
            <?php foreach ($products as $pid):
                $product = wc_get_product($pid);
                if (!$product)
                    continue;
                $total_price += (float) $product->get_price();
                ?>
                <li class="pc-build-product">
                    <a href="<?php echo get_permalink($pid); ?>" class="pc-build-product-link">
                        <?php echo $product->get_image('thumbnail'); ?>
                        <span class="pc-build-product-name"><?php echo esc_html($product->get_name()); ?></span>
                    </a>
                    <span class="pc-build-product-price"><?php echo wc_price($product->get_price()); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="pc-build-total">
            <strong><?php _e('Totalpris:', 'pc-builds'); ?></strong>
            <?php echo wc_price($total_price); ?>
        </div>

        <form method="post" class="pc-build-buy-form">
            <button type="submit" name="add_all_to_cart" value="1" class="pc-build-add-btn">
                <?php _e('Lägg alla i varukorgen', 'pc-builds'); ?>
            </button>
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
            wp_safe_redirect(wc_get_cart_url());
            exit;
        }

    endwhile;
endif;

get_footer();
