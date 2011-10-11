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
                        <label>
                            <span class="required">*</span><?php _e('Label', 'cfs'); ?>
                            <span class="cfs_tooltip" title="<?php _e('The field name that editors will see', 'cfs'); ?>"></span>
                        </label>
                    </td>
                    <td>
                        <input type="text" name="cfs[fields][<?php echo $field->weight; ?>][label]" value="<?php echo empty($field->id) ? '' : $field->label; ?>" />
                    </td>
                </tr>
                <tr class="field_name">
                    <td class="label">
                        <label>
                            <span class="required">*</span><?php _e('Name', 'cfs'); ?>
                            <span class="cfs_tooltip" title="<?php _e('Only lowercase letters and underscores', 'cfs'); ?>"></span>
                        </label>
                    </td>
                    <td>
                        <input type="text" name="cfs[fields][<?php echo $field->weight; ?>][name]" value="<?php echo empty($field->id) ? '' : $field->name; ?>" />
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
                        <label>
                            <?php _e('Field Instructions', 'cfs'); ?>
                            <span class="cfs_tooltip" title="<?php _e('Instructions for authors when entering field data', 'cfs'); ?>"></span>
                        </label>
                    </td>
                    <td>
                        <input type="text" name="cfs[fields][<?php echo $field->weight; ?>][instructions]" value="<?php echo $field->instructions; ?>" />
                    </td>
                </tr>
                <tr class="field_save">
                    <td class="label">
                        <label><?php _e('Save Field', 'cfs'); ?></label>
                    </td>
                    <td>
                        <input type="hidden" name="cfs[fields][<?php echo $field->weight; ?>][id]" value="<?php echo $field->id; ?>" />
                        <input type="hidden" name="cfs[fields][<?php echo $field->weight; ?>][sub_field]" value="<?php echo $field->sub_field; ?>" />
                        <input type="submit" value="Save Field" class="button-primary" />
                        <a class="cfs_edit_field" title="continue editing" href="javascript:;">or continue editing</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
