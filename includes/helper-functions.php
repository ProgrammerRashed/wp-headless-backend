<?php
/*****************************************************************************/
 /***  Function to register custom GraphQL fields for a specific post type ***/
/*****************************************************************************/
 

function register_custom_graphql_fields($post_type, $fields) {
    foreach ($fields as $field) {
        register_graphql_field(ucfirst($post_type), $field['name'], [
            'type' => $field['type'],
            'description' => $field['description'],
            'resolve' => function ($post) use ($field) {
                return carbon_get_post_meta($post->ID, $field['meta_key']);
            }
        ]);
    }
}


/*****************************************************************************/
 /**************** Add Admin Menu for Block Generator *********************/
/*****************************************************************************/

add_action('admin_menu', function() {
    add_menu_page(
        'Carbon Fields Block Generator', 
        'Block Generator', 
        'manage_options',
        'carbon-fields-block-generator',
        'render_block_generator_ui', // Callback function
        'dashicons-admin-generic',
        90 // Position
    );
});

/*****************************************************************************/
 /***************************  Render Block Generator UI *********************/
/*****************************************************************************/

function render_block_generator_ui() {
    ?>
    <div class="wrap">
        <h1>Carbon Fields Block Generator</h1>
        <p>Create custom blocks for Carbon Fields without writing code!</p>

        <form id="block-generator-form" method="post">
            <?php
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

            <?php if ($edit_block): ?>
                <input type="hidden" name="edit_block_id" value="<?php echo esc_attr($edit_block['id']); ?>">
            <?php endif; ?>

            <h3>Block Details</h3>
            <div class="block-details-inputs">
                <input type="text" id="block_name" name="block_name" placeholder="Block Name" value="<?php echo $edit_block ? esc_attr($edit_block['name']) : ''; ?>" required />
                <input type="text" id="block_keywords" name="block_keywords" placeholder="Keywords (comma separated)" value="<?php echo $edit_block ? esc_attr(implode(',', $edit_block['keywords'])) : ''; ?>" />
                <textarea id="block_description" name="block_description" placeholder="Block Description"><?php echo $edit_block ? esc_textarea($edit_block['description']) : ''; ?></textarea>
            </div>

            <h3>Fields</h3>
            <div id="fields-container">
                <?php if ($edit_block && !empty($edit_block['fields'])): ?>
                    <?php foreach ($edit_block['fields'] as $field): ?>
                        <div class="field">
                            <div class="main-fields">
                                <select class="field-type" name="field_type[]">
                                    <option value="text" <?php selected($field['type'], 'text'); ?>>Text</option>
                                    <option value="image" <?php selected($field['type'], 'image'); ?>>Image</option>
                                    <option value="radio" <?php selected($field['type'], 'radio'); ?>>Radio</option>
                                    <option value="select" <?php selected($field['type'], 'select'); ?>>Select</option>
                                    <option value="association" <?php selected($field['type'], 'association'); ?>>Association</option>
                                    <option value="complex" <?php selected($field['type'], 'complex'); ?>>Complex</option>
                                    <option value="rich_text" <?php selected($field['type'], 'rich_text'); ?>>Rich Text</option>
                                </select>
                                <input type="text" class="field-name" name="field_name[]" placeholder="Field Name" value="<?php echo esc_attr($field['name']); ?>" required />
                                <input type="text" class="field-label" name="field_label[]" placeholder="Field Label" value="<?php echo esc_attr($field['label']); ?>" required />
                                
                                <!-- Association Parameters -->
                                <?php if ($field['type'] === 'association'): ?>
                                    <div class="association-params">
                                        <input type="text" class="association-types" 
                                            value="<?php echo esc_attr(implode(',', $field['types'] ?? [])); ?>" 
                                            placeholder="Post Types (comma-separated)" 
                                        />
                                        <input type="number" class="association-max" 
                                            value="<?php echo esc_attr($field['max'] ?? 0); ?>" 
                                            placeholder="Max Items" 
                                        />
                                    </div>
                                <?php endif; ?>

                                <!-- Options for Select/Radio -->
                                <?php if (in_array($field['type'], ['select', 'radio']) && !empty($field['options'])): ?>
                                    <div class="options">
                                        <h4>Options</h4>
                                        <div class="options-container">
                                            <?php foreach ($field['options'] as $key => $value): ?>
                                                <div class="option">
                                                    <input type="text" class="option-key" 
                                                        name="options[<?php echo esc_attr($field['name']); ?>][<?php echo esc_attr($key); ?>][key]" 
                                                        value="<?php echo esc_attr($key); ?>" 
                                                        placeholder="Key" required 
                                                    />
                                                    <input type="text" class="option-value" 
                                                        name="options[<?php echo esc_attr($field['name']); ?>][<?php echo esc_attr($key); ?>][value]" 
                                                        value="<?php echo esc_attr($value); ?>" 
                                                        placeholder="Value" required 
                                                    />
                                                    <button type="button" class="remove-option">❌</button>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <button type="button" class="add-option">+ Add Option</button>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Sub Fields for Complex -->
                            <?php if ($field['type'] === 'complex' && !empty($field['sub_fields'])): ?>
                                <div class="sub-fields">
                                    <h4>Sub Fields</h4>
                                    <div class="sub-fields-container">
                                        <?php foreach ($field['sub_fields'] as $sub_field): ?>
                                            <div class="sub-field">
                                                <select class="sub-field-type" 
                                                    name="sub_fields[<?php echo esc_attr($field['name']); ?>][][type]">
                                                    <option value="text" <?php selected($sub_field['type'], 'text'); ?>>Text</option>
                                                    <option value="image" <?php selected($sub_field['type'], 'image'); ?>>Image</option>
                                                </select>
                                                <input type="text" class="sub-field-name" 
                                                    name="sub_fields[<?php echo esc_attr($field['name']); ?>][][name]" 
                                                    value="<?php echo esc_attr($sub_field['name']); ?>" 
                                                    placeholder="Sub-field Name" required 
                                                />
                                                <input type="text" class="sub-field-label" 
                                                    name="sub_fields[<?php echo esc_attr($field['name']); ?>][][label]" 
                                                    value="<?php echo esc_attr($sub_field['label']); ?>" 
                                                    placeholder="Sub-field Label" required 
                                                />
                                                <button type="button" class="remove-sub-field">❌</button>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button type="button" class="add-sub-field">+ Add Sub Field</button>
                                </div>
                            <?php endif; ?>

                            <button type="button" class="remove-field">❌ Delete Field</button>
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
                foreach ($blocks as $block) {
                    $edit_url = admin_url('admin.php?page=carbon-fields-block-generator&edit_block=' . urlencode($block['id']));
                    echo '<div class="block-item">';
                    echo '<div class="block-name">' . esc_html($block['name']) . '</div>';
                    echo '<a href="' . esc_url($edit_url) . '" class="edit-link">Edit</a>';
                    echo '</div>';
                }
            } else {
                echo '<div>No blocks found.</div>';
            }
            ?>
        </div>
    </div>
    <?php
}


/*****************************************************************************/
 /*****************  Handle AJAX Request to Save Custom Blocks ***************/
/*****************************************************************************/
add_action('wp_ajax_save_custom_blocks', function() {
    // Security Check
    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'carbon_fields_block_nonce')) {
        wp_send_json_error(['message' => 'Security check failed!']);
    }

    // Check if Block Data Exists
    if (!isset($_POST['block_data'])) {
        wp_send_json_error(['message' => 'Block data is missing!']);
    }

    $json_file = get_template_directory() . '/custom-blocks.json';
    $block_data = json_decode(stripslashes($_POST['block_data']), true);

    if (!$block_data || !is_array($block_data)) {
        wp_send_json_error(['message' => 'Invalid block data format!']);
    }

    // Load Existing Blocks
    $blocks = file_exists($json_file) ? json_decode(file_get_contents($json_file), true) : [];
    if (!is_array($blocks)) {
        $blocks = [];
    }

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
        // Append New Block
        $block_data['id'] = uniqid('block_', true); // Generate a unique ID
        $blocks[] = $block_data;
    }

    // Save Back to File
    if (file_put_contents($json_file, json_encode($blocks, JSON_PRETTY_PRINT)) === false) {
        wp_send_json_error(['message' => 'Failed to write to file!']);
    }

    wp_send_json_success(['message' => 'Block saved successfully!', 'blocks' => $blocks]);
});