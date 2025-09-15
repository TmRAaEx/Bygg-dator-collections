<?php
if (!defined('ABSPATH'))
    exit;
get_header();

echo '<h1>' . __('PC-Builds', 'pc-builds') . '</h1>';


//TODO develop further so it works togheter with the existing filtering
$user_id = get_query_var('u');

$args = [
    'post_type' => 'pc-build',
    'posts_per_page' => 10,
    'post_author' => $user_id
];


$query = new WP_Query($args);


echo $user_id;
echo "<a href='/create-pc-build'>" . __('Skapa pc build', 'pc-builds') . "</a>";

include plugin_dir_path(__DIR__) . 'templates/parts/pc-build-filter-form.php';

include plugin_dir_path(__DIR__) . 'templates/parts/pc-build-list.php';

get_footer();
