<?php
if (!defined('ABSPATH'))
    exit;

get_header(); ?>

<h1>Alla PC-Builds</h1>

<?php
// Get filter values
$selected_cat = isset($_GET['pc_build_category']) ? intval($_GET['pc_build_category']) : 0;
$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

// WP_Query arguments
$args = [
    'post_type' => 'pc-build',
    'posts_per_page' => 10,
    's' => $search,
];

if ($selected_cat > 0) {
    $args['tax_query'] = [
        [
            'taxonomy' => 'pc_build_category',
            'field' => 'term_id',
            'terms' => $selected_cat,
        ]
    ];
}

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'date';

if ($sort === 'title') {
    $args['orderby'] = 'title';
    $args['order'] = 'ASC';
} else {
    $args['orderby'] = 'date';
    $args['order'] = 'DESC';
}

$args = apply_filters('pc_build_archive_query_args', $args, $_GET);

$query = new WP_Query($args);
?>

<!-- TODO: Make fancier -->
<a href="/create-pc-build">Skapa pc build</a>

<!-- Filter Form -->
<form method="get" class="pc-build-filter-form">
    <input type="text" name="s" placeholder="Sök builds" value="<?php echo esc_attr($search); ?>">
    <?php
    wp_dropdown_categories([
        'taxonomy' => 'pc_build_category',
        'name' => 'pc_build_category',
        'show_option_none' => 'Alla kategorier',
        'selected' => $selected_cat
    ]);
    ?>
    <select name="sort">
        <option value="date" <?php selected($_GET['sort'] ?? '', 'date'); ?>>Senast skapade</option>
        <option value="title" <?php selected($_GET['sort'] ?? '', 'title'); ?>>Alfabetiskt (A-Ö)</option>
    </select>
    <button type="submit">Filtrera</button>
</form>

<?php if ($query->have_posts()): ?>
    <ul class="pc-build-archive">
        <?php while ($query->have_posts()):
            $query->the_post();
            ?>
            <li>
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </li>
        <?php endwhile; ?>
    </ul>

    <!-- Pagination -->
    <?php
    echo paginate_links([
        'total' => $query->max_num_pages
    ]);
?>
<?php else: ?>
    <p>Inga PC-Builds hittades.</p>
<?php endif; ?>

<?php wp_reset_postdata(); ?>
<?php get_footer(); ?>