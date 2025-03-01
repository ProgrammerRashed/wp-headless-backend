<?php
/*****************************************************************************/
 /*********  Function to register custom post type data to  GraphQL **********/
/*****************************************************************************/
 


function register_dynamic_graphql_fields() {
    $json_file = get_template_directory() . '/custom-post-types.json';

    if (!file_exists($json_file)) {
        return;
    }

    $post_types = json_decode(file_get_contents($json_file), true);

    if (!$post_types || !is_array($post_types)) {
        return;
    }

    foreach ($post_types as $post_type) {
        if (!empty($post_type['show_in_graphql']) && $post_type['show_in_graphql'] === true) {
            $post_type_name = ucfirst($post_type['graphql_single_name'] ?? $post_type['slug']);

            if (!empty($post_type['fields']) && is_array($post_type['fields'])) {
                add_action('graphql_register_types', function () use ($post_type_name, $post_type) {
                    foreach ($post_type['fields'] as $field) {
                        register_graphql_field($post_type_name, $field['name'], [
                            'type' => $field['graphql_type'] ?? 'String',
                            'description' => $field['label'] ?? '',
                            'resolve' => function ($post) use ($field) {
                                return carbon_get_post_meta($post->ID, $field['name']);
                            }
                        ]);
                    }
                });
            }
        }
    }
}
add_action('init', 'register_dynamic_graphql_fields');

/*****************************************************************************/
 /**************** Add Admin Menu for Block and CPT Generator *********************/
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


add_action('admin_menu', function() {
    add_menu_page(
        'Custom Post Type Generator',
        'CPT Generator',
        'manage_options',
        'custom-post-type-generator',
        'render_cpt_generator_ui',
        'dashicons-admin-generic',
        90
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
                <input class="item" type="text" id="block_name" name="block_name" placeholder="Block Name" value="<?php echo $edit_block ? esc_attr($edit_block['name']) : ''; ?>" required />
                <input class="item" type="text" id="block_keywords" name="block_keywords" placeholder="Keywords (comma separated)" value="<?php echo $edit_block ? esc_attr(implode(',', $edit_block['keywords'])) : ''; ?>" />
                <textarea class="item" id="block_description" name="block_description" placeholder="Block Description"><?php echo $edit_block ? esc_textarea($edit_block['description']) : ''; ?></textarea>
            </div>

            <h3>Fields</h3>
            <div id="fields-container">
                <?php if ($edit_block && !empty($edit_block['fields'])): ?>
                    <?php foreach ($edit_block['fields'] as $field): ?>
                        <div class="field">
                            <div class="main-fields">
                                <select class="field-type field-item" name="field_type[]">
                                    <option value="text" <?php selected($field['type'], 'text'); ?>>Text</option>
                                    <option value="image" <?php selected($field['type'], 'image'); ?>>Image</option>
                                    <option value="radio" <?php selected($field['type'], 'radio'); ?>>Radio</option>
                                    <option value="select" <?php selected($field['type'], 'select'); ?>>Select</option>
                                    <option value="association" <?php selected($field['type'], 'association'); ?>>Association</option>
                                    <option value="complex" <?php selected($field['type'], 'complex'); ?>>Complex</option>
                                    <option value="rich_text" <?php selected($field['type'], 'rich_text'); ?>>Rich Text</option>
                                    <option value="checkbox" <?php selected($field['type'], 'checkbox'); ?>>Checkbox</option>


                                </select>
                                <input type="text" class="field-name field-item" name="field_name[]" placeholder="Field Name" value="<?php echo esc_attr($field['name']); ?>" required />
                                <input type="text" class="field-label field-item" name="field_label[]" placeholder="Field Label" value="<?php echo esc_attr($field['label']); ?>" required />
                                
                                <!-- Association Parameters -->
                                <?php if ($field['type'] === 'association'): ?>
                                    <div class="association-params field-item">
                                        <input type="text" class="association-types field-item" 
                                            value="<?php echo esc_attr(implode(',', $field['types'] ?? [])); ?>" 
                                            placeholder="Post Types (comma-separated)" 
                                        />
                                        <input type="number" class="association-max field-item" 
                                            value="<?php echo esc_attr($field['max'] ?? 0); ?>" 
                                            placeholder="Max Items" 
                                        />
                                    </div>
                                <?php endif; ?>

                                <!-- Checkbox -->
                                <?php if ($field['type'] === 'checkbox'): ?>
                                    <input type="checkbox" class="field-checkbox field-item" name="field_checkbox[]" value="1" <?php checked(!empty($field['default'])); ?> />
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
                                                    <button type="button" class="remove-option field-item">❌ Delete Option</button>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <button type="button" class="add-option">+ Add Option</button>
                                    </div>
                                <?php endif; ?>

                                <button type="button" class="remove-field field-item">❌ Delete Field</button>
                            </div>

                            <!-- Sub Fields for Complex -->
                            <?php if ($field['type'] === 'complex' && !empty($field['sub_fields'])): ?>
                                <div class="sub-fields">
                                    <h4>Sub Fields</h4>
                                    <div class="sub-fields-container">
                                        <?php foreach ($field['sub_fields'] as $sub_field): ?>
                                            <div class="sub-field">
                                                <select class="sub-field-type field-item" 
                                                    name="sub_fields[<?php echo esc_attr($field['name']); ?>][][type]">
                                                    <option value="text" <?php selected($sub_field['type'], 'text'); ?>>Text</option>
                                                    <option value="image" <?php selected($sub_field['type'], 'image'); ?>>Image</option>
                                                </select>
                                                <input type="text" class="sub-field-name field-item" 
                                                    name="sub_fields[<?php echo esc_attr($field['name']); ?>][][name]" 
                                                    value="<?php echo esc_attr($sub_field['name']); ?>" 
                                                    placeholder="Sub-field Name" required 
                                                />
                                                <input type="text" class="sub-field-label field-item" 
                                                    name="sub_fields[<?php echo esc_attr($field['name']); ?>][][label]" 
                                                    value="<?php echo esc_attr($sub_field['label']); ?>" 
                                                    placeholder="Sub-field Label" required 
                                                />
                                                <button type="button" class="remove-sub-field field-item">❌ Delete Field</button>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button type="button" class="add-sub-field">+ Add Sub Field</button>
                                </div>
                            <?php endif; ?>

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

    // Ensure checkboxes are always stored as true/false
    if (!empty($block_data['fields']) && is_array($block_data['fields'])) {
        foreach ($block_data['fields'] as &$field) {
            if ($field['type'] === 'checkbox') {
                // Ensure default is explicitly set to false if not checked
                $field['default'] = isset($field['default']) ? filter_var($field['default'], FILTER_VALIDATE_BOOLEAN) : false;
            }
        }
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


/*****************************************************************************/
/******************* Render Custom Post Type Generator UI ********************/
/*****************************************************************************/
function render_cpt_generator_ui() {
    $json_file = get_template_directory() . '/custom-post-types.json';
    $existing_cpts = file_exists($json_file) ? json_decode(file_get_contents($json_file), true) : [];
    ?>
    <div class="wrap">
        <h1>Custom Post Type Generator</h1>
        <p>Create or Edit Custom Post Types dynamically!</p>

        <h3>Manage Existing Post Types</h3>
        <div class="cpt-select-container">
            <select id="edit_cpt_selector">
                <option value="">-- Select a CPT to Edit --</option>
                <?php foreach ($existing_cpts as $index => $cpt): ?>
                    <option value="<?php echo esc_attr(json_encode(['index' => $index] + $cpt)); ?>">
                        <?php echo esc_html($cpt['name'] ?? ''); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="button" id="delete-cpt" class="button button-danger" style="display: none;">Delete Selected CPT</button>
        </div>

        <form id="cpt-generator-form">
            <h3>Post Type Details</h3>
            <input type="hidden" id="edit_cpt_index" name="edit_cpt_index" value="">
            <input type="text" id="cpt_name" name="cpt_name" placeholder="Post Type Name" required>
            <input type="text" id="cpt_slug" name="cpt_slug" placeholder="Slug (e.g., portfolio)" required>
            <input type="text" id="cpt_singular" name="cpt_singular" placeholder="Singular Label" required>
            <input type="text" id="cpt_plural" name="cpt_plural" placeholder="Plural Label" required>

            <h3>Supports</h3>
            <div id="cpt-supports">
                <?php $support_options = ['title', 'editor', 'thumbnail', 'revisions', 'excerpt', 'author', 'page-attributes']; ?>
                <?php foreach ($support_options as $option): ?>
                    <label><input type="checkbox" name="cpt_supports[]" value="<?php echo $option; ?>"> <?php echo ucfirst($option); ?></label>
                <?php endforeach; ?>
            </div>

            <h3>Select Icon</h3>
            <select id="cpt_icon" name="cpt_icon">
                <?php $dashicons = ['admin-post', 'admin-users', 'portfolio', 'testimonial', 'archive', 'clipboard', 'star-filled']; ?>
                <?php foreach ($dashicons as $icon): ?>
                    <option value="dashicons-<?php echo $icon; ?>"><?php echo ucfirst(str_replace('-', ' ', $icon)); ?></option>
                <?php endforeach; ?>
            </select>

            <h3>Custom Fields</h3>
            <div id="custom-fields-container"></div>
            <button type="button" id="add-custom-field" class="button">+ Add Custom Field</button>

            <br><br>
            <?php wp_nonce_field('save_cpt_nonce', 'cpt_nonce'); ?>
            <button type="submit" id="save-cpt" class="button button-primary">Save Custom Post Type</button>
        </form>
    </div>

    <style>
        .cpt-select-container {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .button-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            color: white;
        }
        
        .button-danger:hover {
            background-color: #bb2d3b;
            border-color: #bb2d3b;
            color: white;
        }
    </style>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Add ajaxurl and nonce to global scope
        window.ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        const cptNonce = '<?php echo wp_create_nonce('save_cpt_nonce'); ?>';

        const fieldsContainer = document.getElementById("custom-fields-container");
        const form = document.getElementById("cpt-generator-form");
        const deleteButton = document.getElementById("delete-cpt");

        // Add Custom Field
        document.getElementById("add-custom-field").addEventListener("click", function() {
            const fieldHTML = `
                <div class="custom-field" style="margin: 10px 0; padding: 10px; border: 1px solid #ddd;">
                    <input type="text" class="field-name" placeholder="Field Name" required>
                    <input type="text" class="field-label" placeholder="Field Label" required>
                    <select class="field-type">
                        <option value="text">Text</option>
                        <option value="textarea">Textarea</option>
                        <option value="image">Image</option>
                        <option value="checkbox">Checkbox</option>
                    </select>
                    <button type="button" class="button remove-field">Remove</button>
                </div>
            `;
            fieldsContainer.insertAdjacentHTML("beforeend", fieldHTML);
        });

        // Remove Custom Field
        fieldsContainer.addEventListener("click", function(e) {
            if (e.target.classList.contains("remove-field")) {
                e.target.closest(".custom-field").remove();
            }
        });

        // Edit Existing CPT
        document.getElementById("edit_cpt_selector").addEventListener("change", function() {
            const selectedData = this.value ? JSON.parse(this.value) : null;
            deleteButton.style.display = this.value ? 'inline-block' : 'none';
            
            if (!selectedData) return;

            document.getElementById("edit_cpt_index").value = selectedData.index;
            document.getElementById("cpt_name").value = selectedData.name;
            document.getElementById("cpt_slug").value = selectedData.slug;
            document.getElementById("cpt_singular").value = selectedData.singular;
            document.getElementById("cpt_plural").value = selectedData.plural;
            document.getElementById("cpt_icon").value = selectedData.icon;

            // Set supports checkboxes
            document.querySelectorAll('#cpt-supports input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = selectedData.supports?.includes(checkbox.value) || false;
            });

            // Set custom fields
            fieldsContainer.innerHTML = '';
            if (selectedData.fields) {
                selectedData.fields.forEach(field => {
                    const fieldHTML = `
                        <div class="custom-field" style="margin: 10px 0; padding: 10px; border: 1px solid #ddd;">
                            <input type="text" class="field-name" value="${field.name}" placeholder="Field Name" required>
                            <input type="text" class="field-label" value="${field.label}" placeholder="Field Label" required>
                            <select class="field-type">
                                <option value="text" ${field.type === 'text' ? 'selected' : ''}>Text</option>
                                <option value="textarea" ${field.type === 'textarea' ? 'selected' : ''}>Textarea</option>
                                <option value="image" ${field.type === 'image' ? 'selected' : ''}>Image</option>
                                <option value="checkbox" ${field.type === 'checkbox' ? 'selected' : ''}>Checkbox</option>
                            </select>
                            <button type="button" class="button remove-field">Remove</button>
                        </div>
                    `;
                    fieldsContainer.insertAdjacentHTML("beforeend", fieldHTML);
                });
            }
        });

        // Delete CPT Handler
        deleteButton.addEventListener("click", function() {
            const selectedOption = document.getElementById("edit_cpt_selector").value;
            if (!selectedOption || !confirm('Are you sure you want to delete this post type? This action cannot be undone!')) {
                return;
            }

            const selectedData = JSON.parse(selectedOption);
            
            fetch(ajaxurl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: new URLSearchParams({
                    action: 'delete_custom_post_type',
                    security: cptNonce,
                    index: selectedData.index
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Success: ' + data.data.message);
                    window.location.reload();
                } else {
                    alert('Error: ' + data.data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting.');
            });
        });

        // Form Submission
        form.addEventListener("submit", function(e) {
            e.preventDefault();

            const formData = {
                index: document.getElementById("edit_cpt_index").value,
                name: document.getElementById("cpt_name").value.trim(),
                slug: document.getElementById("cpt_slug").value.trim(),
                singular: document.getElementById("cpt_singular").value.trim(),
                plural: document.getElementById("cpt_plural").value.trim(),
                icon: document.getElementById("cpt_icon").value,
                supports: Array.from(document.querySelectorAll('#cpt-supports input:checked')).map(cb => cb.value),
                fields: Array.from(document.querySelectorAll(".custom-field")).map(field => ({
                    name: field.querySelector(".field-name").value.trim(),
                    label: field.querySelector(".field-label").value.trim(),
                    type: field.querySelector(".field-type").value
                }))
            };

            fetch(ajaxurl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: new URLSearchParams({
                    action: 'save_custom_post_type',
                    security: cptNonce,
                    data: JSON.stringify(formData)
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Success: ' + data.data.message);
                    window.location.reload();
                } else {
                    alert('Error: ' + data.data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while saving.');
            });
        });
    });
    </script>
    <?php
}

/*****************************************************************************/
/********************* AJAX Handler for Saving CPT ********************/
/*****************************************************************************/
add_action('wp_ajax_save_custom_post_type', 'handle_save_custom_post_type');
function handle_save_custom_post_type() {
    check_ajax_referer('save_cpt_nonce', 'security');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Unauthorized access']);
    }

    $data = json_decode(stripslashes($_POST['data']), true);
    $json_file = get_template_directory() . '/custom-post-types.json';
    $existing_cpts = file_exists($json_file) ? json_decode(file_get_contents($json_file), true) : [];

    // Validate required fields
    if (empty($data['name']) || empty($data['slug']) || empty($data['singular']) || empty($data['plural'])) {
        wp_send_json_error(['message' => 'All required fields must be filled']);
    }

    // Prepare CPT data with defaults
    $cpt_data = [
        'name' => sanitize_text_field($data['name']),
        'slug' => sanitize_title($data['slug']),
        'singular' => sanitize_text_field($data['singular']),
        'plural' => sanitize_text_field($data['plural']),
        'icon' => sanitize_text_field($data['icon']),
        'supports' => array_map('sanitize_text_field', $data['supports'] ?? []),
        'fields' => array_map(function($field) {
            return [
                'name' => sanitize_text_field($field['name']),
                'label' => sanitize_text_field($field['label']),
                'type' => sanitize_text_field($field['type'])
            ];
        }, $data['fields'] ?? []),
        
        // Default values
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'rewrite' => [
            'slug' => sanitize_title($data['slug']) // Use the provided slug
        ],
        'taxonomies' => ['category'],
        'show_in_graphql' => true,
        'graphql_single_name' => sanitize_title($data['singular'], '_'),
        'graphql_plural_name' => sanitize_title($data['plural'], '_')
    ];

    // Update or add new entry
    if (isset($data['index']) && $data['index'] !== '' && isset($existing_cpts[$data['index']])) {
        $existing_cpts[$data['index']] = $cpt_data;
    } else {
        $existing_cpts[] = $cpt_data;
    }

    // Save to file
    if (file_put_contents($json_file, json_encode($existing_cpts, JSON_PRETTY_PRINT))) {
        wp_send_json_success(['message' => 'Custom Post Type saved successfully']);
    } else {
        wp_send_json_error(['message' => 'Failed to save Custom Post Type']);
    }
}



// Add AJAX handler for deletion
add_action('wp_ajax_delete_custom_post_type', 'handle_delete_custom_post_type');
function handle_delete_custom_post_type() {
    check_ajax_referer('save_cpt_nonce', 'security');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Unauthorized access']);
    }

    $index = isset($_POST['index']) ? intval($_POST['index']) : -1;
    $json_file = get_template_directory() . '/custom-post-types.json';
    $existing_cpts = file_exists($json_file) ? json_decode(file_get_contents($json_file), true) : [];

    if ($index === -1 || !isset($existing_cpts[$index])) {
        wp_send_json_error(['message' => 'Invalid post type selection']);
    }

    // Remove the CPT entry
    array_splice($existing_cpts, $index, 1);

    // Save updated list
    if (file_put_contents($json_file, json_encode($existing_cpts, JSON_PRETTY_PRINT))) {
        wp_send_json_success(['message' => 'Custom Post Type deleted successfully']);
    } else {
        wp_send_json_error(['message' => 'Failed to delete Custom Post Type']);
    }
}