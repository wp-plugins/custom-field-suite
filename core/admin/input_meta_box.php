<input type="hidden" name="cfs[save]" value="<?php echo wp_create_nonce('cfs_save_input'); ?>" />

<script type="text/javascript">
jQuery(function() {
    jQuery(".cfs_loop td.remove span").live("click", function() {
        jQuery(this).closest("table").remove();
    });

    // Add a new field
    jQuery(".cfs_add_field").click(function() {
        var parent = jQuery(this).closest(".table_footer").siblings(".loop_wrapper");
        var count = parent.find("input.row_count");
        var html = parent.find(".input_clone").html().replace(/\[clone\]/g, "["+count.val()+"]");
        count.val(parseInt(count.val()) + 1);
        parent.append(html);
    });

    // Remove clone fields on save
    jQuery("#publish").click(function() {
        jQuery(".loop_wrapper .input_clone").remove();
    });
});
</script>

<?php

$used_types = array();

// Passed from add_meta_box
$group_id = $metabox['args']['group_id'];

$input_fields = $this->api->get_input_fields($group_id);

// Add any necessary head scripts
foreach ($input_fields as $key => $field)
{
    if (!isset($used_types[$field->type]))
    {
        if (method_exists($this->fields[$field->type], 'input_head'))
        {
            $this->fields[$field->type]->input_head($field);
            $used_types[$field->type] = true;
        }
    }

    // Ignore sub-fields
    if (1 > (int) $field->parent_id)
    {
?>
<div class="field">
    <label><?php echo $field->label; ?></label>

    <?php if (!empty($field->instructions)) : ?>
    <p class="instructions"><?php echo $field->instructions; ?></p>
    <?php endif; ?>

    <div class="cfs_<?php echo $field->type; ?>">
    <?php
        $this->create_field((object) array(
            'id' => $field->id,
            'group_id' => $group_id,
            'post_id' => $field->post_id,
            'type' => $field->type,
            'input_name' => "cfs[input][$field->id][value]",
            'input_class' => $field->type,
            'options' => $field->options,
            'value' => $field->value,
        ));
    ?>
    </div>
</div>
<?php
    }
}
