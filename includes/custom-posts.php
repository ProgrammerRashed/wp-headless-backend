<?php

function register_custom_post_types() {
    $json_file = get_template_directory() . '/custom-post-types.json';

    if (!file_exists($json_file)) {
        return;
    }

    $post_types = json_decode(file_get_contents($json_file), true);

    if (!$post_types || !is_array($post_types)) {
        return;
    }

    foreach ($post_types as $post_type) {
        $singular = $post_type['singular'] ?? $post_type['name'];
        $plural = $post_type['plural'] ?? $post_type['name'] . 's';
        $slug = $post_type['slug'] ?? sanitize_title($post_type['name']);

        $labels = [
            'name' => __($plural),
            'singular_name' => __($singular),
            'add_new' => __('Add New'),
            'add_new_item' => __('Add New ' . $singular),
            'edit_item' => __('Edit ' . $singular),
            'new_item' => __('New ' . $singular),
            'view_item' => __('View ' . $singular),
            'search_items' => __('Search ' . $plural),
            'not_found' => __('No ' . strtolower($plural) . ' found'),
            'not_found_in_trash' => __('No ' . strtolower($plural) . ' found in Trash'),
            'all_items' => __('All ' . $plural),
            'archives' => __($singular . ' Archives'),
            'attributes' => __($singular . ' Attributes'),
        ];

        $args = [
            'labels' => $labels,
            'public' => $post_type['public'] ?? true,
            'has_archive' => $post_type['has_archive'] ?? true,
            'supports' => $post_type['supports'] ?? ['title', 'editor'],
            'menu_icon' => $post_type['icon'] ?? 'dashicons-admin-post',
            'rewrite' => ['slug' => $slug],
            'show_in_rest' => $post_type['show_in_rest'] ?? true, // Ensure REST API support
            'hierarchical' => $post_type['hierarchical'] ?? false, // Allow parent-child relationships
            'query_var' => true, // Allow querying by slug
            'exclude_from_search' => $post_type['exclude_from_search'] ?? false, // Show in search?
        ];

        // Add Taxonomies if provided
        if (!empty($post_type['taxonomies'])) {
            $args['taxonomies'] = $post_type['taxonomies'];
        }

        // Add GraphQL Support if provided
        if (!empty($post_type['show_in_graphql']) && $post_type['show_in_graphql'] === true) {
            $args['show_in_graphql'] = true;
            $args['graphql_single_name'] = $post_type['graphql_single_name'] ?? $slug;
            $args['graphql_plural_name'] = $post_type['graphql_plural_name'] ?? $plural;
        }

        register_post_type($slug, $args);
    }
}
add_action('init', 'register_custom_post_types');
