<?php
if (!defined('ABSPATH'))
    exit;
get_header();

echo '<h1>' . __('Mina PC-Builds', 'pc-builds') . '</h1>';

$args = [
    'post_type' => 'pc-build',
    'posts_per_page' => 10,
    'post_author' => get_current_user_id(),
];
$query = new WP_Query($args);

include plugin_dir_path(__DIR__) . 'templates/parts/pc-build-filter-form.php';

include plugin_dir_path(__DIR__) . 'templates/parts/pc-build-list.php';

get_footer();
