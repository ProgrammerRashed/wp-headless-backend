<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;


function register_dynamic_custom_fields() {
    $json_file = get_template_directory() . '/custom-post-types.json';

    if (!file_exists($json_file)) {
        return;
    }

    $post_types = json_decode(file_get_contents($json_file), true);

    if (!$post_types || !is_array($post_types)) {
        return;
    }

    foreach ($post_types as $post_type) {
        if (!empty($post_type['fields']) && is_array($post_type['fields'])) {
            Container::make('post_meta', __('Custom Fields'))
                ->where('post_type', '=', $post_type['slug'])
                ->set_context('side')
                ->set_priority('high') 
                ->add_fields(array_map('generate_dynamic_field', $post_type['fields']));
        }
    }
}

function generate_dynamic_field($field) {
    switch ($field['type']) {
        case 'text':
            return Field::make('text', $field['name'], __($field['label']));

        case 'textarea':
            return Field::make('textarea', $field['name'], __($field['label']));

        case 'url':
            return Field::make('text', $field['name'], __($field['label']))
                ->set_attribute('type', 'url');

        case 'image':
            return Field::make('image', $field['name'], __($field['label']))
                ->set_value_type('url');

        case 'checkbox':
            return Field::make('checkbox', $field['name'], __($field['label']))
                ->set_option_value('yes');

        case 'select':
            return Field::make('select', $field['name'], __($field['label']))
                ->set_options($field['options'] ?? []);

        case 'radio':
            return Field::make('radio', $field['name'], __($field['label']))
                ->set_options($field['options'] ?? []);

        default:
            return Field::make('text', $field['name'], __($field['label']));
    }
}

add_action('carbon_fields_register_fields', 'register_dynamic_custom_fields');
