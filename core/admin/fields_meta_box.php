<?php

global $post, $wpdb;

// store all field html options
foreach ($this->fields as $field_name => $field_data)
{
    if (method_exists($this->fields[$field_name], 'options_html'))
    {
        ob_start();
        $this->fields[$field_name]->options_html('clone', $field);
        $options_html[$field_name] = ob_get_clean();
    }
}

$field_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}cfs_fields WHERE post_id = '$post->ID'");
$results = $this->api->get_input_fields($post->ID, 0);

?>

<input type="hidden" name="cfs[save]" value="<?php echo wp_create_nonce('cfs_save_fields'); ?>" />

<script type="text/javascript">

field_index = <?php echo $field_count; ?>;
options_html = <?php echo json_encode($options_html); ?>;

jQuery(function() {

    function update_order() {
        jQuery(".fields").each(function() {
            jQuery(this).find(".field").removeClass("even");
            jQuery(this).find(".field:even").addClass("even");
        });
    }

    update_order();

    // Sortable
    jQuery(".fields").sortable({
        axis: "y",
        handle: "td.field_order",
        update: function(event, ui) { update_order(); }
    });

    // Add a new field
    jQuery(".cfs_add_field").click(function() {
        var parent = jQuery(this).closest(".table_footer").siblings(".fields");
        var html = jQuery(".field_clone").html().replace(/\[clone\]/g, "["+field_index+"]");

        if (jQuery(this).hasClass("cfs_add_sub_field")) {
            html = html.replace(/{sub_field}/g, "1");
        }

        parent.append(html);
        parent.find(".field:last .field_label a.cfs_edit_field").click();
        parent.find(".field:last .cfs_input .field_type select").change();
        field_index = field_index + 1;
    });

    // Delete a field
    jQuery(".cfs_delete_field").live("click", function() {
        if (confirm("Are you sure you want to delete this field?")) {
            jQuery(this).closest(".field").remove();
        }
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
        jQuery(this).closest(".field").find("td.field_label a").html(val);
    });

    jQuery(".cfs_input tr.field_name input").live("keyup", function() {
        var val = jQuery(this).val();
        jQuery(this).closest(".field").find("td.field_name").html(val);
    });
});
</script>

<div class="fields">

<?php foreach ($results as $field) : ?>

    <div class="field">
        <div class="field_meta">
            <table class="cfs widefat">
                <tr>
                    <td class="field_order">

                    </td>
                    <td class="field_label">
                        <strong>
                            <a class="cfs_edit_field row-title" title="Edit field" href="javascript:;"><?php echo $field->label; ?></a>
                        </strong>
                    </td>
                    <td class="field_name">
                        <?php echo $field->name; ?>
                    </td>
                    <td class="field_type">
                        <?php echo $field->type; ?>
                    </td>
                    <td class="field_delete">
                        <span class="cfs_delete_field"></span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="field_form_mask">
            <table class="cfs_input widefat">
                <tbody>
                    <tr class="field_label">
                        <td class="label">
                            <label><span class="required">*</span><?php _e('Field Label', 'cfs'); ?></label>
                            <p class="description"><?php _e('The field name that editors will see', 'cfs'); ?></p>
                        </td>
                        <td>
                            <input type="text" name="cfs[fields][<?php echo $field->weight; ?>][label]" value="<?php echo $field->label; ?>" />
                        </td>
                    </tr>
                    <tr class="field_name">
                        <td class="label">
                            <label><span class="required">*</span><?php _e('Field Name', 'cfs'); ?></label>
                            <p class="description"><?php _e('Only lowercase letters and underscores', 'cfs'); ?></p>
                        </td>
                        <td>
                            <input type="text" name="cfs[fields][<?php echo $field->weight; ?>][name]" value="<?php echo $field->name; ?>" />
                        </td>
                    </tr>
                    <tr class="field_type">
                        <td class="label">
                            <label><span class="required">*</span><?php _e('Field Type', 'cfs'); ?></label>
                        </td>
                        <td>
                            <select name="cfs[fields][<?php echo $field->weight; ?>][type]">
                            <?php foreach ($this->fields as $type) : ?>
                                <?php $selected = ($type->name == $field->type) ? ' selected' : ''; ?>
                                <option value="<?php echo $type->name; ?>"<?php echo $selected; ?>><?php echo $type->label; ?></option>
                            <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>

                    <?php if (method_exists($this->fields[$field->type], 'options_html')) : ?>
                        <?php $this->fields[$field->type]->options_html($field->weight, $field); ?>
                    <?php endif; ?>

                    <tr class="field_instructions">
                        <td class="label">
                            <label><?php _e('Field Instructions', 'cfs'); ?></label>
                            <p class="description"><?php _e('Instructions for authors when entering field data', 'cfs'); ?></p>
                        </td>
                        <td>
                            <textarea name="cfs[fields][<?php echo $field->weight; ?>][instructions]" rows="4"><?php echo $field->instructions; ?></textarea>
                        </td>
                    </tr>
                    <tr class="field_save">
                        <td class="label">
                            <label><?php _e('Save Field', 'cfs'); ?></label>
                        </td>
                        <td>
                            <input type="hidden" name="cfs[fields][<?php echo $field->weight; ?>][id]" value="<?php echo $field->id; ?>" />
                            <input type="submit" value="Save Field" class="button-primary" />
                            <a class="cfs_edit_field" title="continue editing" href="javascript:;">or continue editing</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

<?php endforeach; ?>

</div>

<!-- CLONE THIS HTML FOR NEW FIELDS -->

<div class="field_clone hidden">
    <div class="field">
        <div class="field_meta">
            <table class="cfs widefat">
                <tr>
                    <td class="field_order">

                    </td>
                    <td class="field_label">
                        <strong>
                            <a class="cfs_edit_field row-title" title="Edit field" href="javascript:;">New Field</a>
                        </strong>
                    </td>
                    <td class="field_name">
                        new_field
                    </td>
                    <td class="field_type">
                        text
                    </td>
                    <td class="field_delete">
                        <span class="cfs_delete_field"></span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="field_form_mask hidden">
            <table class="cfs_input widefat">
                <tbody>
                    <tr class="field_label">
                        <td class="label">
                            <label><span class="required">*</span><?php _e('Field Label', 'cfs'); ?></label>
                            <p class="description"><?php _e('The field name that editors will see', 'cfs'); ?></p>
                        </td>
                        <td>
                            <input type="text" name="cfs[fields][clone][label]" value="" />
                        </td>
                    </tr>
                    <tr class="field_name">
                        <td class="label">
                            <label><span class="required">*</span><?php _e('Field Name', 'cfs'); ?></label>
                            <p class="description"><?php _e('Only lowercase letters and underscores', 'cfs'); ?></p>
                        </td>
                        <td>
                            <input type="text" name="cfs[fields][clone][name]" value="" />
                        </td>
                    </tr>
                    <tr class="field_type">
                        <td class="label">
                            <label><span class="required">*</span><?php _e('Field Type', 'cfs'); ?></label>
                        </td>
                        <td>
                            <select name="cfs[fields][clone][type]">
                            <?php foreach ($this->fields as $type) : ?>
                                <?php $selected = ('text' == $type->name) ? ' selected' : ''; ?>
                                <option value="<?php echo $type->name; ?>"<?php echo $selected; ?>><?php echo $type->label; ?></option>
                            <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr class="field_instructions">
                        <td class="label">
                            <label><?php _e('Field Instructions', 'cfs'); ?></label>
                            <p class="description"><?php _e('Instructions for authors when entering field data', 'cfs'); ?></p>
                        </td>
                        <td>
                            <textarea name="cfs[fields][clone][instructions]" rows="4"></textarea>
                        </td>
                    </tr>
                    <tr class="field_save">
                        <td class="label">
                            <label><?php _e('Save Field', 'cfs'); ?></label>
                        </td>
                        <td>
                            <input type="hidden" name="cfs[fields][clone][sub_field]" value="{sub_field}" />
                            <input type="submit" value="Save Field" class="button-primary" />
                            <a class="cfs_edit_field" title="continue editing" href="javascript:;">or continue editing</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- END: CLONE HTML -->

<div class="table_footer">
    <input type="button" class="button-primary cfs_add_field" value="Add New Field" />
</div>
