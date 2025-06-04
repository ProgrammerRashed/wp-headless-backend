<?php

/**
 * headless functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package headless
 */

if ( !defined( '_S_VERSION' ) ) {
    // Replace the version number of the theme on each release.
    define( '_S_VERSION', '1.0.0' );
}

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
define( 'THEMEROOT', get_stylesheet_directory_uri() );
define( 'IMG', THEMEROOT . '/dist/img' );

add_action( 'after_setup_theme', 'theme_load' );
function theme_load() {
    require_once 'vendor/autoload.php';
    \Carbon_Fields\Carbon_Fields::boot();
}

/**
 * Setup
 *
 * @return void
 */

add_theme_support( 'post-thumbnails' );


/**
 * Enqueue scripts and styles for admin
 */

function headless_admin_scripts() {
    wp_enqueue_style( 'headless-admin-style', get_template_directory_uri() . '/admin.css', array(), _S_VERSION, 'all' );
}

add_action( 'admin_enqueue_scripts', 'headless_admin_scripts' );

// add global inc files
require_once get_template_directory() . '/inc/global.php';

/**
 * Blocks
 */
require get_template_directory() . '/inc/blocks.php';

/**
 * Custom Post Types
 */

require get_template_directory() . '/inc/cpt.php';

/**
 * Post Meta
 */

require get_template_directory() . '/inc/post-meta.php';

/**
 * Rest API Routes
 */
require get_template_directory() . '/webhook/next.php';
require get_template_directory() . '/inc/rest-api/nh_register_job_apply_endpoint.php';
require get_template_directory() . '/inc/rest-api/nh_register_contact_endpoint.php';

function add_noindex() {
    if ( is_front_page() || is_home() || is_category() || is_tag() || is_search() || is_archive() || is_single() ) {
        echo '<meta name="robots" content="noindex, follow">';
    }
}
add_action( 'wp_head', 'add_noindex' );

add_filter( 'xmlrpc_enabled', '__return_false' );