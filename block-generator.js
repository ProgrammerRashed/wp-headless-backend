jQuery(document).ready(function($) {
    $("#add-field").click(function() {
        $("#fields-container").append(`
            <div class="field">
                <select class="field-type">
                    <option value="text">Text</option>
                    <option value="image">Image</option>
                    <option value="radio">Radio</option>
                    <option value="association">Association</option>
                </select>
                <input type="text" class="field-name" placeholder="Field Name" required />
                <input type="text" class="field-label" placeholder="Field Label" required />
                <button type="button" class="remove-field">❌</button>
            </div>
        `);
    });

    $(document).on("click", ".remove-field", function() {
        $(this).parent().remove();
    });

    $("#block-generator-form").submit(function(event) {
        event.preventDefault();
    
        let fields = [];
        $(".field").each(function() {
            fields.push({
                type: $(this).find(".field-type").val(),
                name: $(this).find(".field-name").val(),
                label: $(this).find(".field-label").val()
            });
        });
    
        let block = {
            name: $("#block_name").val(),
            icon: $("#block_icon").val(),
            keywords: $("#block_keywords").val().split(","),
            description: $("#block_description").val(),
            fields: fields
        };
    
        let edit_block_id = $("input[name='edit_block_id']").val();
        if (edit_block_id) {
            block.id = edit_block_id;
        }
    
        $.post(block_ajax.ajax_url, {
            action: "save_custom_blocks",
            security: block_ajax.nonce,
            block_data: JSON.stringify(block),
            edit_block_id: edit_block_id
        }, function(response) {
            if (response.success) {
                alert(response.data.message);
                location.reload(); // Reload the page to reflect changes
            } else {
                alert("Error: " + response.data.message);
            }
        }, "json");
    });
});
