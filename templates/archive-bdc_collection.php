<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header(); ?>

<div class="bdc-archive">
    <h1><?php post_type_archive_title(); ?></h1>
    <a href="/skapa-kollektion">Skapa ny kollektion</a>
    <?php if (have_posts()): ?>
        <ul class="bdc-collections-list">
            <?php while (have_posts()):
                the_post(); ?>
                <li class="bdc-collection-item">
                    <a href="<?php the_permalink(); ?>">
                        <?php if (has_post_thumbnail()): ?>
                            <div class="bdc-collection-thumb">
                                <?php the_post_thumbnail('medium'); ?>
                            </div>
                        <?php endif; ?>
                        <h2 class="bdc-collection-title"><?php the_title(); ?></h2>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>

        <div class="bdc-pagination">
            <?php
            the_posts_pagination([
                'prev_text' => __('« Föregående', 'bygg-dator-collections'),
                'next_text' => __('Nästa »', 'bygg-dator-collections'),
            ]);
            ?>
        </div>
    <?php else: ?>
        <p><?php _e('Inga kollektioner hittades.', 'bygg-dator-collections'); ?></p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>