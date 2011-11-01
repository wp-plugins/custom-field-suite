<?php

class cfs_Select extends cfs_Field
{

    function __construct($parent)
    {
        $this->name = 'select';
        $this->label = __('Select', 'cfs');
        $this->parent = $parent;
    }

    function html($field)
    {
        $multiple = '';
        $choices = array();

        // Handle choice labels (value : label)
        $temp = explode("\n", $field->options['choices']);

        foreach ($temp as $val)
        {
            if (false !== strpos($val, ' : '))
            {
                $choice = explode(' : ', $val);
                $choices[$choice[0]] = $choice[1];
            }
            else
            {
                $choices[$val] = $val;
            }
        }

        // Multi-select
        if ('1' == $field->options['multiple'])
        {
            $multiple = ' multiple';

            if (empty($field->input_class))
            {
                $field->input_class = 'multiple';
            }
            else
            {
                $field->input_class .= ' multiple';
            }
        }

        // Make sure the select box returns an array
        if ('[]' != substr($field->input_name, -2))
        {
            $field->input_name .= '[]';
        }
    ?>
        <select name="<?php echo $field->input_name; ?>" class="<?php echo $field->input_class; ?>"<?php echo $multiple; ?>>
        <?php foreach ($choices as $val => $label) : ?>
            <?php $selected = in_array($val, (array) $field->value) ? ' selected' : ''; ?>
            <option value="<?php echo htmlspecialchars($val); ?>"<?php echo $selected; ?>><?php echo htmlspecialchars($label); ?></option>
        <?php endforeach; ?>
        </select>
    <?php
    }

    function options_html($key, $field)
    {
    ?>
        <tr class="field_option field_option_<?php echo $this->name; ?>">
            <td class="label">
                <label><?php _e('Choices', 'cfs'); ?></label>
                <p class="description"><?php _e('Enter one choice per line', 'cfs'); ?></p>
            </td>
            <td>
                <?php
                    $this->parent->create_field((object) array(
                        'type' => 'textarea',
                        'input_name' => "cfs[fields][$key][options][choices]",
                        'input_class' => '',
                        'value' => $field->options['choices'],
                    ));
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo $this->name; ?>">
            <td class="label">
                <label><?php _e('Select multiple values?', 'cfs'); ?></label>
            </td>
            <td>
                <?php
                    $this->parent->create_field((object) array(
                        'type' => 'true_false',
                        'input_name' => "cfs[fields][$key][options][multiple]",
                        'input_class' => '',
                        'value' => $field->options['multiple'],
                        'options' => array('message' => 'This a multi-select field'),
                    ));
                ?>
            </td>
        </tr>
    <?php
    }

    function format_value_for_input($value)
    {
        return $value;
    }

    function format_value_for_api($value)
    {
        return $value;
    }
}
