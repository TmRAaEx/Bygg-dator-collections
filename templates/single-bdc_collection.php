<?php
if (!defined('ABSPATH')) {
    exit;
}


$products = get_post_meta(get_the_ID(), '_bdc_collection_products', true);
get_header(); ?>


<main class="bdc-collection-single">
    <h1 class="bdc-title"><?php the_title(); ?></h1>

    <div class="bdc-gallery">
        <?php if (has_post_thumbnail()) {
            the_post_thumbnail('large');
        } ?>

        <!--  Might add more images -->
    </div>

    <h2>Produkter i kollektionen</h2>
    <ul class="bdc-product-list">
        <?php

        $total_price = 0;
        foreach ($products as $product_id):
            // Hämta produktobjektet från ID
            $product = wc_get_product($product_id);
            $total_price += $product->get_price();
            // Hoppa över om produkten inte finns
            if (!$product)
                continue;
            ?>
            <li>
                <img src="<?php echo get_the_post_thumbnail_url($product_id, 'thumbnail'); ?>" alt="">
                <span class="bdc-product-name"><?php echo $product->get_name(); ?></span>
                <span class="bdc-product-price"><?php echo wc_price($product->get_price()); ?></span>
            </li>
        <?php endforeach; ?>

    </ul>
    <h3>Pris: <?php echo $total_price; ?> kr</h3>
    <button class="bdc-buy-btn">Köp hela kollektionen</button>

    <div class="bdc-meta">
        <p>Skapad av: <?php echo get_the_author_meta('display_name', get_post_field('post_author', get_the_ID())); ?></p>
        <p>Datum: <?php echo get_the_date(); ?></p>
    </div>

</main>


<?php get_footer(); ?>