<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

function Postmeta() {
    // Register custom fields for the 'members' post type
    Container::make('post_meta', 'Custom Data')
        ->where('post_type', '=', 'member')
        ->add_fields(array(
            Field::make('text', 'position', 'Position'),
            // Add more fields as needed
            Field::make('textarea', 'bio', 'Biography'),
            Field::make('text', 'email', 'Email Address'),
            Field::make('image', 'profile_picture', 'Profile Picture')->set_value_type('url'),
        ));
}

add_action('carbon_fields_register_fields', 'Postmeta');



    // Register fields for the 'members' post type
    register_custom_graphql_fields('member', [
        ['name' => 'position', 'type' => 'String', 'description' => 'The position of the member', 'meta_key' => 'position'],
        ['name' => 'bio', 'type' => 'String', 'description' => 'Biography of the member', 'meta_key' => 'bio'],
        ['name' => 'email', 'type' => 'String', 'description' => 'Email address of the member', 'meta_key' => 'email'],
        ['name' => 'profile_picture', 'type' => 'String', 'description' => 'Profile picture URL of the member', 'meta_key' => 'profile_picture'],
    ]);
