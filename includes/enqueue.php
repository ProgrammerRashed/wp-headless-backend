<?php
/*****************************************************************************/
 /***************************  Enqueues styles. ******************************/
/*****************************************************************************/
function headless_wp_enqueue_styles() {
    wp_enqueue_style(
        'headless-wp-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'headless_wp_enqueue_styles');





/**
 * Enqueue Admin Scripts & Localize Data
 */


function enqueue_admin_scripts() {
    wp_enqueue_script(
        'block-generator-script',
        get_template_directory_uri() . '/block-generator.js',
        array('jquery'),
        null,
        true
    );

    wp_localize_script('block-generator-script', 'block_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('carbon_fields_block_nonce'),
    ));

    // Enqueue CSS
    wp_enqueue_style(
        'custom-style',
        get_template_directory_uri() . '/custom-style.css', 
        array(),
        wp_get_theme()->get('Version') 
    );
}
add_action('admin_enqueue_scripts', 'enqueue_admin_scripts');
