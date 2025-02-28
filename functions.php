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
require_once get_template_directory() . '/includes/custom-blocks.php';
require_once get_template_directory() . '/includes/custom-posts.php';
require_once get_template_directory() . '/includes/helper-functions.php';
require_once get_template_directory() . '/includes/post-meta.php';

/**
 * Add Admin Menu for Block Generator
 */
add_action('admin_menu', function() {
    add_menu_page(
        'Carbon Fields Block Generator', // Page title
        'Block Generator', // Menu title
        'manage_options', // Capability
        'carbon-fields-block-generator', // Slug
        'render_block_generator_ui', // Callback function
        'dashicons-admin-generic', // Icon
        90 // Position
    );
});

/**
 * Render Block Generator UI
 */


function render_block_generator_ui() {
    // Check if we're editing a block
    $edit_block_id = isset($_GET['edit_block']) ? sanitize_text_field($_GET['edit_block']) : null;

    // Load blocks from JSON
    $json_file = get_template_directory() . '/custom-blocks.json';
    $blocks = [];
    if (file_exists($json_file)) {
        $blocks = json_decode(file_get_contents($json_file), true);
    }

    // Find the block to edit
    $edit_block = null;
    if ($edit_block_id) {
        foreach ($blocks as $block) {
            if ($block['id'] === $edit_block_id) {
                $edit_block = $block;
                break;
            }
        }
    }
    ?>
    <div class="wrap">
        <h1><?php echo $edit_block ? 'Edit Block' : 'Create Block'; ?></h1>
        <p>Create or edit custom blocks for Carbon Fields without writing code!</p>

        <form id="block-generator-form" method="post">
            <?php if ($edit_block): ?>
                <input type="hidden" name="edit_block_id" value="<?php echo esc_attr($edit_block['id']); ?>">
            <?php endif; ?>

            <h3>Block Details</h3>
            <input type="text" id="block_name" name="block_name" placeholder="Block Name" value="<?php echo $edit_block ? esc_attr($edit_block['name']) : ''; ?>" required />
            <input type="text" id="block_icon" name="block_icon" placeholder="Icon (e.g., admin-comments)" value="<?php echo $edit_block ? esc_attr($edit_block['icon']) : ''; ?>" />
            <input type="text" id="block_keywords" name="block_keywords" placeholder="Keywords (comma separated)" value="<?php echo $edit_block ? esc_attr(implode(',', $edit_block['keywords'])) : ''; ?>" />
            <textarea id="block_description" name="block_description" placeholder="Block Description"><?php echo $edit_block ? esc_textarea($edit_block['description']) : ''; ?></textarea>

            <h3>Fields</h3>
            <div id="fields-container">
                <?php if ($edit_block && !empty($edit_block['fields'])): ?>
                    <?php foreach ($edit_block['fields'] as $field): ?>
                        <div class="field">
                            <select class="field-type" name="field_type[]">
                                <option value="text" <?php selected($field['type'], 'text'); ?>>Text</option>
                                <option value="image" <?php selected($field['type'], 'image'); ?>>Image</option>
                                <option value="radio" <?php selected($field['type'], 'radio'); ?>>Radio</option>
                                <option value="association" <?php selected($field['type'], 'association'); ?>>Association</option>
                            </select>
                            <input type="text" class="field-name" name="field_name[]" placeholder="Field Name" value="<?php echo esc_attr($field['name']); ?>" required />
                            <input type="text" class="field-label" name="field_label[]" placeholder="Field Label" value="<?php echo esc_attr($field['label']); ?>" required />
                            <button type="button" class="remove-field">❌</button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" id="add-field">+ Add Field</button>

            <br><br>
            <button type="submit"><?php echo $edit_block ? 'Update Block' : 'Save Block'; ?></button>
        </form>

        <h2>Generated Blocks</h2>
        <div id="generated-blocks">
            <?php
            if ($blocks && is_array($blocks)) {
                $block_links = array_map(function ($block) {
                    $edit_url = admin_url('admin.php?page=carbon-fields-block-generator&edit_block=' . urlencode($block['id']));
                    return '<div class="' . 'block-item' . '"> <div>' . esc_html($block['name']) . '</div> <a href="' . esc_url($edit_url) . '">' . 'Edit'. '</a></div> ';
                }, $blocks);
                echo implode($block_links);
            } else {
                echo 'No blocks found.';
            }
            ?>
        </div>
    </div>
    <?php
}
/**
 * Enqueue Admin Scripts & Localize Data
 */
function enqueue_admin_scripts() {
    // Enqueue JavaScript
    wp_enqueue_script(
        'block-generator-script',
        get_template_directory_uri() . '/block-generator.js', // External JS file
        array('jquery'),
        null,
        true
    );

    // Localize script for AJAX
    wp_localize_script('block-generator-script', 'block_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('carbon_fields_block_nonce'),
    ));

    // Enqueue CSS
    wp_enqueue_style(
        'block-generator-style',
        get_template_directory_uri() . '/block-generator.css', // Path to CSS file
        array(),
        wp_get_theme()->get('Version') // Version for cache busting
    );
}
add_action('admin_enqueue_scripts', 'enqueue_admin_scripts');

/**
 * Handle AJAX Request to Save Custom Blocks
 */
add_action('wp_ajax_save_custom_blocks', function() {
    // Security check
    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'carbon_fields_block_nonce')) {
        wp_send_json_error(['message' => 'Security check failed!']);
    }

    // Load existing blocks
    $json_file = get_template_directory() . '/custom-blocks.json';
    $blocks = file_exists($json_file) ? json_decode(file_get_contents($json_file), true) : [];

    // Parse incoming block data
    $block_data = json_decode(stripslashes($_POST['block_data']), true);

    // Check if we're editing an existing block
    $edit_block_id = $_POST['edit_block_id'] ?? null;
    if ($edit_block_id) {
        foreach ($blocks as &$block) {
            if ($block['id'] === $edit_block_id) {
                $block = $block_data; // Replace the block with updated data
                break;
            }
        }
    } else {
        // Append a new block
        $block_data['id'] = uniqid('block_', true); // Generate a unique ID
        $blocks[] = $block_data;
    }

    // Save back to file
    if (file_put_contents($json_file, json_encode($blocks, JSON_PRETTY_PRINT)) === false) {
        wp_send_json_error(['message' => 'Failed to write to file!']);
    }

    wp_send_json_success(['message' => 'Block saved successfully!', 'blocks' => $blocks]);
});
