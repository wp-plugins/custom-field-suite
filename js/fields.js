jQuery(function() {

    function update_order() {
        jQuery(".fields").each(function() {
            jQuery(this).find(".field").removeClass("even");
            jQuery(this).find(".field:even").addClass("even");
        });
        jQuery(".cfs_input").each(function() {
            jQuery(this).find("tr").removeClass("even");
            jQuery(this).find("tr:even").addClass("even");
        });
    }

    update_order();

    // Sortable
    jQuery(".fields").sortable({
        axis: "y",
        handle: "td.field_order",
        update: function(event, ui) { update_order(); }
    });

    // Select box enhancement
    jQuery(".chosen-select").chosen();

    // Add a new field
    jQuery(".cfs_add_field").live("click", function() {
        var parent = jQuery(this).closest(".table_footer").siblings(".fields");
        var html = jQuery(".field_clone").html().replace(/\[clone\]/g, "["+field_index+"]");

        if (jQuery(this).hasClass("cfs_add_sub_field")) {
            html = html.replace(/{sub_field}/g, "1");
        }

        parent.append(html);
        parent.find(".field:last .field_label a.cfs_edit_field").click();
        parent.find(".field:last .cfs_input .field_type select").change();
        field_index = field_index + 1;

        // Remove the "loop" type if already within a loop
        if (jQuery(this).hasClass("cfs_add_sub_field")) {
            parent.find('.field:last .cfs_input .field_type option[value="loop"]').remove();
        }
    });

    // Delete a field
    jQuery(".cfs_delete_field").live("click", function() {
        jQuery(this).closest(".field").remove();
    });

    // Pop open the edit fields
    jQuery(".cfs_edit_field").live("click", function() {
        var field = jQuery(this).closest(".field");
        field.toggleClass("form_open");
        field.find(".field_form_mask:first").animate({"height": "toggle"}, 500);
    });

    // Add or replace field_type options
    jQuery(".cfs_input .field_type select").live("change", function() {
        var type = jQuery(this).val();
        var input_name = jQuery(this).attr("name").replace("[type]", "");
        var html = options_html[type].replace(/cfs\[fields\]\[clone\]/g, input_name);
        jQuery(this).closest(".field").find("td.field_type").html(type);
        jQuery(this).closest(".cfs_input").find(".field_option").remove();
        jQuery(this).closest(".field_type").after(html);
    });

    // Auto-populate the field name
    jQuery(".cfs_input tr.field_label input").live("blur", function() {
        var label_text = jQuery(this).val();
        var name = jQuery(this).closest("tr").siblings("tr.field_name").find("input");
        if ("" == name.val()) {
            var val = label_text.replace(/\s/g, "_");
            val = val.replace(/[^a-zA-Z0-9_]/g, "");
            name.val(val.toLowerCase());
            name.trigger("keyup");
        }
    });

    jQuery(".cfs_input tr.field_label input").live("keyup", function() {
        var val = jQuery(this).val();
        jQuery(this).closest(".field").find("td.field_label:first a").html(val + "&nbsp;");
    });

    jQuery(".cfs_input tr.field_name input").live("keyup", function() {
        var val = jQuery(this).val();
        jQuery(this).closest(".field").find("td.field_name:first").html(val);
    });
});