<?php
$search = $args['search'] ?? '';
$selected_cat = $args['selected_cat'] ?? 0;
?>
<form method="get" class="pc-builds-filter-form">
    <input type="text" name="s" placeholder="<?php echo esc_attr__('Sök builds', 'pc-builds'); ?>"
        value="<?php echo esc_attr($search); ?>">

    <?php
    wp_dropdown_categories([
        'taxonomy' => 'pc_build_category',
        'name' => 'pc_build_category',
        'show_option_none' => __('Alla kategorier', 'pc-builds'),
        'selected' => $selected_cat,
    ]);
    ?>

    <select name="sort">
        <option value="date" <?php selected($_GET['sort'] ?? '', 'date'); ?>>
            <?php esc_html_e('Senast skapade', 'pc-builds'); ?>
        </option>
        <option value="title" <?php selected($_GET['sort'] ?? '', 'title'); ?>>
            <?php esc_html_e('Alfabetiskt (A–Ö)', 'pc-builds'); ?>
        </option>
    </select>

    <button type="submit"><?php esc_html_e('Filtrera', 'pc-builds'); ?></button>
</form>