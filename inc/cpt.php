<?php
add_action('init', 'create_post_type');

function create_post_type()
{
    // MEMBERS
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
            'supports' => array('title'),
            'rewrite' => array('slug' => 'members'),
            'menu_icon' => 'dashicons-star-filled',
            'taxonomies' => array('category', 'post_tag'),
            'show_in_graphql' => true,
            'graphql_single_name' => 'member',
            'graphql_plural_name' => 'members',
        )
    );

    // Blogs
    register_post_type(
        'blogs',
        array(
            'labels' => array(
                'name' => __('Blogs'),
                'singular_name' => __('Blog')
            ),
            'public' => true,
            'has_archive' => true,
            'show_in_rest' => true,
            'supports' => array('title'),
            'rewrite' => array('slug' => 'blogs'),
            'menu_icon' => 'dashicons-star-filled',
            'taxonomies' => array('category', 'post_tag'),
            'show_in_graphql' => true,
            'graphql_single_name' => 'blog',
            'graphql_plural_name' => 'blogs',
        )
    );



    // custom_navigations
    register_post_type(
        'custom_navigations',
        array(
            'labels' => array(
                'name' => __('Navigations'),
                'singular_name' => __('Navigation')
            ),
            'public' => true,
            'has_archive' => true,
            'show_in_rest' => true,
            'supports' => array('title', 'revisions', ),
            'rewrite' => array('slug' => 'custom_navigations'),
            'menu_icon' => 'dashicons-star-filled',
            'taxonomies' => array('category', 'post_tag'),
            'show_in_graphql' => true,
            'graphql_single_name' => 'custom_navigation',
            'graphql_plural_name' => 'custom_navigations',
        )
    );



    // SERVICES
    register_post_type(
        'services',
        array(
            'labels' => array(
                'name' => __('Services'),
                'singular_name' => __('Service')
            ),
            'public' => true,
            'has_archive' => true,
            'show_in_rest' => true,
            'supports' => array('title', ),
            'rewrite' => array('slug' => 'services'),
            'menu_icon' => 'dashicons-star-filled',
            'taxonomies' => array('category', 'post_tag'),
            'show_in_graphql' => true,
            'graphql_single_name' => 'service',
            'graphql_plural_name' => 'services',
        )
    );


    // ACHIEVEMENTS
    register_post_type(
        'achievements',
        array(
            'labels' => array(
                'name' => __('Achievements'),
                'singular_name' => __('Achievement')
            ),
            'public' => true,
            'has_archive' => true,
            'show_in_rest' => true,
            'supports' => array('title'),
            'rewrite' => array('slug' => 'achievement'),
            'menu_icon' => 'dashicons-star-filled',
            'show_in_graphql' => true,
            'graphql_single_name' => 'achievement',
            'graphql_plural_name' => 'achievements',
        )
    );


    // TESTIMONIALS
    register_post_type(
        'testimonials',
        array(
            'labels' => array(
                'name' => __('Testimonials'),
                'singular_name' => __('Testimonial')
            ),
            'public' => true,
            'has_archive' => true,
            'show_in_rest' => true,
            'supports' => array('title'),
            'rewrite' => array('slug' => 'testimonial'),
            'menu_icon' => 'dashicons-star-filled',
            'show_in_graphql' => true,
            'graphql_single_name' => 'testimonial',
            'graphql_plural_name' => 'testimonials',
        )
    );

    // CLIENTS
    register_post_type(
        'clients',
        array(
            'labels' => array(
                'name' => __('Clients'),
                'singular_name' => __('Client')
            ),
            'public' => true,
            'has_archive' => true,
            'show_in_rest' => true,
            'supports' => array('title'),
            'rewrite' => array('slug' => 'client'),
            'menu_icon' => 'dashicons-star-filled',
            'show_in_graphql' => true,
            'graphql_single_name' => 'client',
            'graphql_plural_name' => 'clients',
            'resolve' => function ($block) {
                $attributes = json_decode($block['attributesJSON'] ?? '', true);
                return expand_association($attributes['clients'] ?? [], 'clients');
            }

        )
    );


}

