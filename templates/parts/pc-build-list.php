<?php if ($query->have_posts()): ?>
    <ul class="pc-build-archive">
        <?php while ($query->have_posts()):
            $query->the_post(); ?>
            <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
        <?php endwhile; ?>
    </ul>

    <?php echo paginate_links(['total' => $query->max_num_pages]); ?>

<?php else: ?>
    <p><?php _e('Inga PC-Builds hittades.', 'pc-builds'); ?></p>
    <a href='/create-pc-build'><?php __('Skapa pc build', 'pc-builds') ?></a>;

<?php endif; ?>

<?php wp_reset_postdata(); ?>