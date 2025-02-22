<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;
use Carbon_Fields\Block;

function crb_register_custom_fields() {

    // Hero Block
    Block::make(__('Hero', 'nh'))
        ->add_fields(array(
            Field::make('html', 'crb_information_text')
                ->set_html('<h2>Hero Block</h2>'),
            Field::make('text', 'title', __('Title', 'nh')),
            Field::make('complex', 'hero_items', __('Hero Items', 'nh'))
                ->set_layout('tabbed-horizontal')
                ->add_fields(array(
                    Field::make('text', 'title', __('Title', 'nh')),
                    Field::make('file', 'image', __('Image'))
                        ->set_value_type('url'),
                ))
        ))
        ->set_icon('star-filled')
        ->set_keywords([__('Hero Custom Block', 'nh')])
        ->set_description(__('Custom Block', 'nh'))
        ->set_render_callback(function ($fields, $attributes, $inner_blocks) {});

    // ✅ NEW: Testimonial Block
    Block::make(__('Testimonial', 'nh'))
        ->add_fields(array(
            Field::make('text', 'testimonial_text', __('Testimonial Text', 'nh')),
            Field::make('text', 'author_name', __('Author Name', 'nh')),
            Field::make('image', 'author_image', __('Author Image'))
                ->set_value_type('url'),
        ))
        ->set_icon('admin-comments')
        ->set_keywords([__('Testimonial', 'nh')])
        ->set_description(__('Testimonial block with an image and text.', 'nh'))
        ->set_render_callback(function ($fields, $attributes, $inner_blocks) {});

    // ✅ NEW: Call to Action (CTA) Block
    Block::make(__('Call to Action', 'nh'))
        ->add_fields(array(
            Field::make('text', 'cta_title', __('Title', 'nh')),
            Field::make('text', 'cta_button_text', __('Button Text', 'nh')),
            Field::make('text', 'cta_button_url', __('Button URL', 'nh')),
        ))
        ->set_icon('megaphone')
        ->set_keywords([__('CTA', 'nh')])
        ->set_description(__('Call to Action block with a title and button.', 'nh'))
        ->set_render_callback(function ($fields, $attributes, $inner_blocks) {});
}

add_action('carbon_fields_register_fields', 'crb_register_custom_fields');
