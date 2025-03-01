<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;
use Carbon_Fields\Block;

function crb_register_custom_fields() {

    // Hero Block
    Block::make(__('Hero'))
        ->add_fields(array(
            Field::make('html', 'crb_information_text')
                ->set_html('<h2>Hero Block</h2>'),
            Field::make('text', 'title', __('Title')),
            Field::make('complex', 'hero_items', __('Hero Items'))
                ->set_layout('tabbed-horizontal')
                ->add_fields(array(
                    Field::make('text', 'title', __('Title')),
                    Field::make('file', 'image', __('Image'))
                        ->set_value_type('url'),
                ))
        ))
        ->set_icon('star-filled')
        ->set_keywords([__('Hero Custom Block')])
        ->set_description(__('Custom Block'))
        ->set_render_callback(function ($fields, $attributes, $inner_blocks) {});

    // ✅ NEW: Testimonial Block
    Block::make(__('Testimonial'))
        ->add_fields(array(
            Field::make('text', 'testimonial_text', __('Testimonial Text')),
            Field::make('text', 'author_name', __('Author Name')),
            Field::make('image', 'author_image', __('Author Image'))
                ->set_value_type('url'),
        ))
        ->set_icon('admin-comments')
        ->set_keywords([__('Testimonial')])
        ->set_description(__('Testimonial block with an image and text.'))
        ->set_render_callback(function ($fields, $attributes, $inner_blocks) {});

    // ✅ NEW: Call to Action (CTA) Block
    Block::make(__('Call to Action'))
        ->add_fields(array(
            Field::make('text', 'cta_title', __('Title')),
            Field::make('text', 'cta_button_text', __('Button Text')),
            Field::make('text', 'cta_button_url', __('Button URL')),
        ))
        ->set_icon('megaphone')
        ->set_keywords([__('CTA')])
        ->set_description(__('Call to Action block with a title and button.'))
        ->set_render_callback(function ($fields, $attributes, $inner_blocks) {});

           // Hero Block
    Block::make(__('Seconder Hero'))
    ->add_fields(array(
        Field::make('html', 'crb_information_text')
            ->set_html('<h2>Hero Block</h2>'),
        Field::make('text', 'title', __('Title')),
        Field::make('complex', 'hero_items', __('Hero Items'))
            ->set_layout('tabbed-horizontal')
            ->add_fields(array(
                Field::make('text', 'title', __('Title')),
                Field::make('file', 'image', __('Image'))
                    ->set_value_type('url'),
            ))
    ))
    ->set_icon('star-filled')
    ->set_keywords([__('Hero Custom Block')])
    ->set_description(__('Custom Block'))
    ->set_render_callback(function ($fields, $attributes, $inner_blocks) {});
}

add_action('carbon_fields_register_fields', 'crb_register_custom_fields');


function load_custom_blocks_from_json() {
    $json_file = get_template_directory() . '/custom-blocks.json';
    if (!file_exists($json_file)) return;

    $blocks = json_decode(file_get_contents($json_file), true);
    if (!$blocks) return;

    foreach ($blocks as $block) {
        Block::make(__($block['name']))
            ->add_fields(array_map('generate_field', $block['fields']))
            ->set_icon($block['icon'] ?? 'block-default')
            ->set_keywords($block['keywords'] ?? [])
            ->set_description($block['description'] ?? '')
            ->set_render_callback(function ($fields) {
                echo "<pre>" . print_r($fields, true) . "</pre>";
            });
    }
}
add_action('carbon_fields_register_fields', 'load_custom_blocks_from_json');