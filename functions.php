<?php
/**
 * Headless WP Theme by Notionhive functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Headless_WP_Notionhive
 * @since 1.0
 */

// Adds theme support for post formats.
function headless_wp_post_format_setup() {
    add_theme_support( 'post-formats', array( 'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video' ) );
}
add_action( 'after_setup_theme', 'headless_wp_post_format_setup' );

// Enqueues styles.
function headless_wp_enqueue_styles() {
    wp_enqueue_style(
        'headless-wp-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme()->get( 'Version' )
    );
}
add_action( 'wp_enqueue_scripts', 'headless_wp_enqueue_styles' );

// Registers custom block styles.
function headless_wp_block_styles() {
    register_block_style(
        'core/list',
        array(
            'name'         => 'checkmark-list',
            'label'        => __( 'Checkmark', 'headless-wp-notionhive' ),
            'inline_style' => '
            ul.is-style-checkmark-list {
                list-style-type: "\2713";
            }
            ul.is-style-checkmark-list li {
                padding-inline-start: 1ch;
            }',
        )
    );
}
add_action( 'init', 'headless_wp_block_styles' );

// Registers pattern categories.
function headless_wp_pattern_categories() {
    register_block_pattern_category(
        'headless_wp_page',
        array(
            'label'       => __( 'Pages', 'headless-wp-notionhive' ),
            'description' => __( 'A collection of full page layouts.', 'headless-wp-notionhive' ),
        )
    );
}
add_action( 'init', 'headless_wp_pattern_categories' );



// Require necessary files from the includes folder
require_once get_template_directory() . '/includes/blocks.php';
require_once get_template_directory() . '/includes/custom-posts.php';
require_once get_template_directory() . '/includes/helper-functions.php';
require_once get_template_directory() . '/includes/post-meta.php';