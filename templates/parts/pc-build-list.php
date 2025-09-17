<div class="pc-builds-actions">
    <a href="/create-pc-build" class="pc-builds-btn">
        <?php esc_html_e('Skapa PC Build', 'pc-builds'); ?>
    </a>
</div>


<?php if ($query->have_posts()): ?>
    <ul class="grid-list">
        <?php while ($query->have_posts()):
            $query->the_post(); ?>
            <li class="card pc_build-card">
                <a href="<?php the_permalink(); ?>">
                    <p class="card-title"><?php the_title(); ?></p>
                    <div class="card-image">
                        <?php if (has_post_thumbnail()): ?>
                            <?php the_post_thumbnail('medium'); ?>
                        <?php else: ?>
                            <img src="<?php echo esc_url(wc_placeholder_img_src()); ?>" alt="Ingen bild" />
                        <?php endif; ?>
                    </div>
                    <p class="card-meta"><?php the_author(); ?></p>
                </a>
            </li>
        <?php endwhile; ?>
    </ul>

    <?php echo paginate_links(['total' => $query->max_num_pages]); ?>

<?php else: ?>
    <p><?php _e('Inga PC-Builds hittades.', 'pc-builds'); ?></p>
    <a href='/create-pc-build'><?php __('Skapa pc build', 'pc-builds') ?></a>;

<?php endif; ?>

<?php wp_reset_postdata(); ?>