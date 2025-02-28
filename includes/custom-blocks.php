<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;
use Carbon_Fields\Block;

function crb_register_custom_fields() {
    $json_file = get_template_directory() . '/custom-blocks.json';

    if (!file_exists($json_file)) {
        return;
    }

    $blocks = json_decode(file_get_contents($json_file), true);

    if (!$blocks || !is_array($blocks)) {
        return;
    }

    foreach ($blocks as $block) {
        $block_name = __($block['name'], 'nh');
        $block_icon = $block['icon'] ?? 'block-default';
        $block_keywords = array_map('__', $block['keywords'] ?? []);
        $block_description = __($block['description'] ?? '', 'nh');

        $block_fields = [];
        foreach ($block['fields'] as $field) {
            $block_fields[] = generate_field($field);
        }

        Block::make($block_name)
            ->add_fields($block_fields)
            ->set_icon($block_icon)
            ->set_keywords($block_keywords)
            ->set_description($block_description)
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {
                echo '<code>' . print_r($fields, true) . '</code>';
            });
    }
}

function generate_field($field) {
    switch ($field['type']) {
        case 'text':
            return Field::make('text', $field['name'], __($field['label'], 'nh'));

        case 'image':
            return Field::make('image', $field['name'], __($field['label'], 'nh'))
                ->set_value_type('url');

        case 'rich_text':
            return Field::make('rich_text', $field['name'], __($field['label'], 'nh'));

        case 'radio':
            return Field::make('radio', $field['name'], __($field['label'], 'nh'))
                ->set_options($field['options']);

        case 'select':
            return Field::make('select', $field['name'], __($field['label'], 'nh'))
                ->set_options($field['options'])
                ->set_default_value(array_key_first($field['options']));

        case 'association':
            $types = array_map(fn($type) => ['type' => trim($type)], $field['types'] ?? ['post']);
            $max = intval($field['max'] ?? 0);
        
            return Field::make('association', $field['name'], __($field['label'], 'nh'))
            ->set_types(array_map(function($type) {
                return [
                    'type' => 'post',
                    'post_type' => $type,
                ];
            }, $types))
                ->set_max($max);

        case 'complex':
            $complex_field = Field::make('complex', $field['name'], __($field['label'], 'nh'))
                ->set_layout($field['layout'] ?? 'tabbed-horizontal');

            $group_key = sanitize_title($field['name']); // Generate unique key

            $complex_field->add_fields($group_key, array_map(function ($sub_field) {
                return generate_field($sub_field);
            }, $field['sub_fields']));

            return $complex_field;

        default:
            return Field::make('text', $field['name'], __($field['label'], 'nh'));
    }
}

add_action('carbon_fields_register_fields', 'crb_register_custom_fields');