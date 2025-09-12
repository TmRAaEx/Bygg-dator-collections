<?php
if (!defined('ABSPATH'))
    exit;

get_header();

if (have_posts()):
    while (have_posts()):
        the_post();

        $products = get_post_meta(get_the_ID(), '_pc_build_products', true); // array of product IDs
        ?>

        <h1><?php the_title(); ?></h1>
        <div><?php the_content(); ?></div>
        <div><img src="<?php echo the_post_thumbnail_url(); ?>"></div>
        <h2>Produkter</h2>
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
            <button type="submit" name="add_all_to_cart" value="1">Lägg alla i varukorgen</button>
        </form>

        <?php
        // Handle adding all products to cart
        if (isset($_POST['add_all_to_cart'])) {
            foreach ($products as $pid) {
                WC()->cart->add_to_cart($pid);
            }
            wc_add_notice('Alla produkter har lagts i varukorgen.', 'success');
            wp_redirect(get_permalink());
            exit;
        }

    endwhile;
endif;

get_footer();
