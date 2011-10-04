<?php

class cfs_Loop
{
    public $name;
    public $label;
    public $parent;

    function __construct($parent)
    {
        $this->name = 'loop';
        $this->label = __('Loop', 'cfs');
        $this->parent = $parent;
    }

    function html($field)
    {
        $results = $this->parent->api->get_input_fields($field->group_id, $field->id);
    ?>

        <div class="loop_wrapper">
            <div class="input_clone hidden">
                <table class="widefat">
                    <tbody>
                        <tr>
                            <td class="order"></td>
                            <td>
                            <?php foreach ($results as $sub_field) : ?>
                                <label><?php echo $sub_field->label; ?></label>

                                <?php if (!empty($sub_field->instructions)) : ?>
                                <p class="instructions"><?php echo $sub_field->instructions; ?></p>
                                <?php endif; ?>

                                <div class="cfs_<?php echo $sub_field->type; ?>">
                                <?php
                                    $this->parent->create_field((object) array(
                                        'type' => $sub_field->type,
                                        'input_name' => "cfs[input][$sub_field->id][clone][value][]",
                                        'input_class' => $sub_field->type,
                                        'options' => $sub_field->options,
                                    ));
                                ?>
                                </div>
                            <?php endforeach; ?>
                            </td>
                            <td class="remove"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

    <?php
        // Get the number of loop rows
        if ($results) :

            foreach ($results as $result)
            {
                $num_rows = count($result->value);
                break;
            }

            for ($i = 0; $i < $num_rows; $i++) :
    ?>
            <input type="hidden" class="loop_count" value="<?php echo $num_rows; ?>" />

            <table class="widefat">
                <tbody>
                    <tr>
                        <td class="order"></td>
                        <td>
                        <?php foreach ($results as $sub_field) : ?>
                            <label><?php echo $sub_field->label; ?></label>

                            <?php if (!empty($sub_field->instructions)) : ?>
                            <p class="instructions"><?php echo $sub_field->instructions; ?></p>
                            <?php endif; ?>

                            <div class="field cfs_<?php echo $sub_field->type; ?>">
                            <?php
                                $this->parent->create_field((object) array(
                                    'type' => $sub_field->type,
                                    'input_name' => "cfs[input][$sub_field->id][$i][value][]",
                                    'input_class' => $sub_field->type,
                                    'options' => $sub_field->options,
                                    'value' => $sub_field->value[$i],
                                ));
                            ?>
                            </div>
                        <?php endforeach; ?>
                        </td>
                        <td class="remove"></td>
                    </tr>
                </tbody>
            </table>

            <?php endfor; endif; ?>

        </div>

    <div class="table_footer">
        <input type="button" class="button-primary cfs_add_field" value="Add Row" />
    </div>

    <?php
    }

    function options_html($key, $field)
    {
        $results = $this->parent->api->get_input_fields($field->post_id, $field->id);
    ?>
        <tr class="field_option field_option_<?php echo $this->name; ?>">
            <td class="label">
                <label><?php _e('Loop Fields', 'cfs'); ?></label>
            </td>
            <td>
                <div class="loop_wrapper">
                    <?php $this->sub_fields_meta_box($results); ?>
                </div>
            </td>
        </tr>
    <?php
    }

    function input_head($field = null)
    {
    ?>
        
    <?php
    }

    function sub_fields_meta_box($sub_fields)
    {
    ?>
    <div class="fields">

    <?php foreach ($sub_fields as $field) : ?>

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
                                <?php foreach ($this->parent->fields as $type) : ?>
                                    <?php if ('loop' != $type->name) : ?>
                                        <?php $selected = ($type->name == $field->type) ? ' selected' : ''; ?>
                                        <option value="<?php echo $type->name; ?>"<?php echo $selected; ?>><?php echo $type->label; ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>

                        <?php if (method_exists($this->parent->fields[$field->type], 'options_html')) : ?>
                            <?php $this->parent->fields[$field->type]->options_html($field->weight, $field); ?>
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
                                <input type="hidden" name="cfs[fields][<?php echo $field->weight; ?>][sub_field]" value="1" />
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

    <div class="table_footer">
        <input type="button" class="button-primary cfs_add_field cfs_add_sub_field" value="Add New Field" />
    </div>

    <?php
    }
}
