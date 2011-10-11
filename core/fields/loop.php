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

                                <div class="field cfs_<?php echo $sub_field->type; ?>">
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
                            <td class="remove"><span></span></td>
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
            <input type="hidden" class="row_count" value="<?php echo $num_rows; ?>" />

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
                        <td class="remove"><span></span></td>
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
        $sub_fields = $this->parent->api->get_input_fields($field->post_id, $field->id);
    ?>
        <tr class="field_option field_option_<?php echo $this->name; ?>">
            <td class="label">
                <label><?php _e('Loop Fields', 'cfs'); ?></label>
            </td>
            <td>
                <div class="loop_wrapper">
                    <div class="fields">

                    <?php
                        foreach ($sub_fields as $field)
                        {
                            $field->sub_field = true;
                            $this->parent->field_html($field);
                        }
                    ?>
                    </div>

                    <div class="table_footer">
                        <input type="button" class="button-primary cfs_add_field cfs_add_sub_field" value="Add New Field" />
                    </div>
                </div>
            </td>
        </tr>
    <?php
    }
}
