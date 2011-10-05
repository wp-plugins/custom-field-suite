<?php

/*--------------------------------------------------------------------------------------
*
*    Supported field methods:
*
*    __construct($parent)
*        Add plugin hooks, basic field settings
*
*    html($field)
*        Build the field HTML
*
*    options_html($key, $field)
*        Build the field options area
*
*    admin_head($field = null)
*        Add styles/JS above the field management form
*
*    input_head($field = null)
*        Add styles/JS above the input form
*
*    format_value_for_api($value)
*        Format the value for get_field()
*
*    format_value_for_input($value)
*        Format the value for the HTML input
*
*    load_value($field)
*        Override how the plugin loads the value
*
*    save_value($field)
*        Override how the plugin saves the value
*
*-------------------------------------------------------------------------------------*/

class cfs_Text
{
    public $name;
    public $label;
    public $parent;

    function __construct($parent)
    {
        $this->name = 'text';
        $this->label = __('Text', 'cfs');
        $this->parent = $parent;
    }

    function html($field)
    {
    ?>
        <input type="text" name="<?php echo $field->input_name; ?>" class="<?php echo $field->input_class; ?>" value="<?php echo $field->value; ?>" />
    <?php
    }

    function options_html($key, $field)
    {
    ?>
        <tr class="field_option field_option_<?php echo $this->name; ?>">
            <td class="label">
                <label><?php _e('Default Value', 'cfs'); ?></label>
            </td>
            <td>
                <?php
                    $this->parent->create_field((object) array(
                        'type' => 'text',
                        'input_name' => "cfs[fields][$key][options][default_value]",
                        'input_class' => '',
                        'value' => $field->options['default_value'],
                    ));
                ?>
            </td>
        </tr>
    <?php
    }

    function format_value_for_input($value)
    {
        return htmlspecialchars($value[0], ENT_QUOTES);
    }
}
