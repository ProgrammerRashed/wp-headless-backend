<?php
/**
 * Headless WP Theme by Notionhive functions and definitions.
 *
 * @package WordPress
 * @subpackage Headless_WP_Notionhive
 * @since 1.0
 */

// Adds theme support for post formats.
function headless_wp_post_format_setup() {
    add_theme_support('post-formats', array('aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video'));
}
add_action('after_setup_theme', 'headless_wp_post_format_setup');



// Registers custom block styles.
function headless_wp_block_styles() {
    register_block_style(
        'core/list',
        array(
            'name'         => 'checkmark-list',
            'label'        => __('Checkmark', 'headless-wp-notionhive'),
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
add_action('init', 'headless_wp_block_styles');


// Require necessary files from the includes folder
require_once get_template_directory() . '/includes/enqueue.php';
require_once get_template_directory() . '/includes/custom-blocks.php';
require_once get_template_directory() . '/includes/custom-posts.php';
require_once get_template_directory() . '/includes/helper-functions.php';
require_once get_template_directory() . '/includes/post-meta.php';


