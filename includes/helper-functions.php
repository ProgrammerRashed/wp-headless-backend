<?php
// Function to register custom GraphQL fields for a specific post type
function register_custom_graphql_fields($post_type, $fields) {
    foreach ($fields as $field) {
        register_graphql_field(ucfirst($post_type), $field['name'], [
            'type' => $field['type'],
            'description' => $field['description'],
            'resolve' => function ($post) use ($field) {
                return carbon_get_post_meta($post->ID, $field['meta_key']);
            }
        ]);
    }
}