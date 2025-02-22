<?php
add_action('init', 'create_post_type');

function create_post_type()
{


            // Members
            register_post_type(
            'members',
            array(
            'labels' => array(
                'name' => __('Members'),
                'singular_name' => __('Member')
            ),
            'public' => true,
            'has_archive' => true,
            'show_in_rest' => true,
            'supports' => array('title', 'editor', 'thumbnail', 'custom-fields', 'revisions', 'excerpt', 'author', 'page-attributes'),
            'rewrite' => array('slug' => 'members'),
            'menu_icon' => 'dashicons-admin-users',
            'taxonomies' => array('category', 'post_tag'),
            'show_in_graphql' => true,
            'graphql_single_name' => 'member',
            'graphql_plural_name' => 'members',
            ));

        
}
