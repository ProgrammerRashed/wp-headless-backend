jQuery(document).ready(function($) {
    // Add Field Button
    $("#add-field").click(function() {
        $("#fields-container").append(`
            <div class="field">
                <select class="field-type">
                    <option value="text">Text</option>
                    <option value="image">Image</option>
                    <option value="radio">Radio</option>
                    <option value="select">Select</option>
                    <option value="association">Association</option>
                    <option value="complex">Complex</option>
                    <option value="rich_text">Rich Text</option>
                </select>
                <input type="text" class="field-name" placeholder="Field Name" required />
                <input type="text" class="field-label" placeholder="Field Label" required />
                
                <!-- Association Parameters -->
                <div class="association-params" style="display: none; margin-top: 10px;">
                    <input type="text" class="association-types" placeholder="Post Types (e.g., post,page)" style="width: 100%; margin-bottom: 5px;" />
                    <input type="number" class="association-max" placeholder="Max Items" style="width: 100%;" />
                </div>
                
                <button type="button" class="remove-field">❌ Delete Field</button>
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

        // Existing logic for sub-fields and options
        if (fieldType === "complex") {
            $field.find(".sub-fields").remove();
            $field.append(`
                <div class="sub-fields">
                    <h4>Sub Fields</h4>
                    <div class="sub-fields-container"></div>
                    <button type="button" class="add-sub-field">+ Add Sub Field</button>
                </div>
            `);
        } else {
            $field.find(".sub-fields").remove();
        }

        if (["select", "radio"].includes(fieldType)) {
            $field.find(".options").remove();
            $field.append(`
                <div class="options">
                    <h4>Options</h4>
                    <div class="options-container"></div>
                    <button type="button" class="add-option">+ Add Option</button>
                </div>
            `);
        } else {
            $field.find(".options").remove();
        }
    });

    // Handle Form Submission
    $("#block-generator-form").submit(function(event) {
        event.preventDefault();

        let fields = [];
        $(".field").each(function() {
            const $field = $(this);
            const fieldType = $field.find(".field-type").val();
            const fieldName = $field.find(".field-name").val();
            const fieldLabel = $field.find(".field-label").val();

            let fieldData = {
                type: fieldType,
                name: fieldName,
                label: fieldLabel
            };

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

        let block = {
            name: $("#block_name").val(),
            icon: "dashicons-star-filled",
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

