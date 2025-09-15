<?php
$search = $args['search'] ?? '';
$selected_cat = $args['selected_cat'] ?? 0;
?>
<form method="get" class="pc-build-filter-form">
    <input type="text" name="s" placeholder="Sök builds" value="<?php echo esc_attr($search); ?>">
    <?php
    wp_dropdown_categories([
        'taxonomy' => 'pc_build_category',
        'name' => 'pc_build_category',
        'show_option_none' => __('Alla kategorier', 'pc-builds'),
        'selected' => $selected_cat,
    ]);
    ?>
    <select name="sort">
        <option value="date" <?php selected($_GET['sort'] ?? '', 'date'); ?>>Senast skapade</option>
        <option value="title" <?php selected($_GET['sort'] ?? '', 'title'); ?>>Alfabetiskt (A–Ö)</option>
    </select>
    <button type="submit">Filtrera</button>
</form>