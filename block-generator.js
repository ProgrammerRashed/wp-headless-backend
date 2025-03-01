jQuery(document).ready(function($) {
    // Add Field Button
    $("#add-field").click(function() {
        $("#fields-container").append(`
            <div class="field">
                <div class="main-fields">
                    <select class="field-type field-item">
                        <option value="text">Text</option>
                        <option value="image">Image</option>
                        <option value="radio">Radio</option>
                        <option value="select">Select</option>
                        <option value="association">Association</option>
                        <option value="complex">Complex</option>
                        <option value="rich_text">Rich Text</option>
                        <option value="checkbox">Checkbox</option>
                    </select>
                    <input type="text" class="field-name field-item" placeholder="Field Name" required />
                    <input type="text" class="field-label field-item" placeholder="Field Label" required />
    
                    <!-- Checkbox Field (Hidden by Default) -->
                    <input type="checkbox" class="checkbox-default field-item field-checkbox" />
                
    
                    <button type="button" class="remove-field field-item">❌ Delete Field</button>
                </div>
            </div>
        `);
    });
    

    // Toggle Association Parameters
    $(document).on("change", ".field-type", function() {
        const fieldType = $(this).val();
        const $field = $(this).closest(".field");

        // Show/hide association parameters
        if (fieldType === "association") {
            $field.find(".association-params").show();
        } else {
            $field.find(".association-params").hide();
        }

        // Handle Sub-Fields (Complex Fields)
        if (fieldType === "complex") {
            $field.find(".sub-fields").remove();
            $field.append(`
                <div class="sub-fields">
                    <h4>Sub Fields</h4>
                    <div class="sub-fields-container"></div>
                    <button type="button" class="add-sub-field field-item">+ Add Sub Field</button>
                </div>
            `);
        } else {
            $field.find(".sub-fields").remove();
        }

        // Handle Options (Select/Radio)
        if (["select", "radio"].includes(fieldType)) {
            $field.find(".options").remove();
            $field.append(`
                <div class="options">
                    <h4>Options</h4>
                    <div class="options-container"></div>
                    <button type="button" class="add-option field-item">+ Add Option</button>
                </div>
            `);
        } else {
            $field.find(".options").remove();
        }


        // Show/hide checkbox field
        if (fieldType === "checkbox") {
            $field.find(".checkbox-container").show();
        } else {
            $field.find(".checkbox-container").hide();
        }
    });

    // Add Option Button (for Select/Radio Fields)
    $(document).on("click", ".add-option", function() {
        const $container = $(this).siblings(".options-container");

        $container.append(`
            <div class="option field-item">
                <input type="text" class="option-key" placeholder="Option Key" required />
                <input type="text" class="option-value" placeholder="Option Value" required />
                <button type="button" class="remove-option field-item">❌ Delete Option</button>
            </div>
        `);
    });

    // Remove Option
    $(document).on("click", ".remove-option", function() {
        $(this).closest(".option").remove();
    });

    // Add Sub Field Button (for Complex Fields)
    $(document).on("click", ".add-sub-field", function() {
        const $container = $(this).siblings(".sub-fields-container");

        $container.append(`
            <div class="sub-field field-item">
                <select class="sub-field-type field-item">
                    <option value="text">Text</option>
                    <option value="image">Image</option>
                    <option value="radio">Radio</option>
                    <option value="select">Select</option>
                    <option value="association">Association</option>
                    <option value="complex">Complex</option>
                    <option value="rich_text">Rich Text</option>
                </select>
                <input type="text" class="sub-field-name field-item" placeholder="Sub Field Name" required />
                <input type="text" class="sub-field-label field-item" placeholder="Sub Field Label" required />
                <button type="button" class="remove-sub-field field-item">❌ Delete Field</button>
            </div>
        `);
    });

    // Remove Sub Field
    $(document).on("click", ".remove-sub-field", function() {
        $(this).closest(".sub-field").remove();
    });

    // Remove Field
    $(document).on("click", ".remove-field", function() {
        $(this).closest(".field").remove();
    });

    // Handle Form Submission
    $("#block-generator-form").submit(function(event) {
        event.preventDefault();

        let fields = [];
        $(".field").each(function() {
            const $field = $(this);
            const fieldType = $field.find(".field-type").val();
            let fieldName = $field.find(".field-name").val().trim();
                fieldName = fieldName.toLowerCase().replace(/\s+/g, "_"); // 
            const fieldLabel = $field.find(".field-label").val();

            let fieldData = {
                type: fieldType,
                name: fieldName,
                label: fieldLabel
            };

        // Handle Checkbox 
            if (fieldType === "checkbox") {
                fieldData.default = $field.find(".checkbox-default").is(":checked");
            }

            // Handle Association Parameters
            if (fieldType === "association") {
                fieldData.types = $field.find(".association-types").val().split(",").map(t => t.trim());
                fieldData.max = parseInt($field.find(".association-max").val()) || 0;
            }

            // Handle Sub-Fields (Complex)
            if (fieldType === "complex") {
                fieldData.sub_fields = [];
                $field.find(".sub-field").each(function() {
                    fieldData.sub_fields.push({
                        type: $(this).find(".sub-field-type").val(),
                        name: $(this).find(".sub-field-name").val(),
                        label: $(this).find(".sub-field-label").val()
                    });
                });
            }

            // Handle Options (Select/Radio)
            if (["select", "radio"].includes(fieldType)) {
                fieldData.options = {};
                $field.find(".option").each(function() {
                    const key = $(this).find(".option-key").val();
                    const value = $(this).find(".option-value").val();
                    fieldData.options[key] = value;
                });
            }

            fields.push(fieldData);
        });


        let blockName = $("#block_name").val().trim();

        // Prevent adding "Custom" multiple times
        if (!blockName.toLowerCase().startsWith("custom ")) {
            blockName = "Custom " + blockName;
        }

        let block = {
            name: blockName,
            icon: "layout",
            keywords: $("#block_keywords").val().split(","),
            description: $("#block_description").val(),
            fields: fields
        };

        const editBlockId = $("input[name='edit_block_id']").val();
        if (editBlockId) block.id = editBlockId;

        $.post(block_ajax.ajax_url, {
            action: "save_custom_blocks",
            security: block_ajax.nonce,
            block_data: JSON.stringify(block),
            edit_block_id: editBlockId
        }, function(response) {
            if (response.success) {
                alert(response.data.message);
                location.reload();
            } else {
                alert("Error: " + response.data.message);
            }
        }, "json");
    });
});
