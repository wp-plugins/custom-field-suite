<?php

class cfs_Date
{
    public $name;
    public $label;
    public $parent;

    function __construct($parent)
    {
        $this->name = 'date';
        $this->label = __('Date', 'cfs');
        $this->parent = $parent;
    }

    function html($field)
    {
    ?>
        <input type="text" name="<?php echo $field->input_name; ?>" class="<?php echo $field->input_class; ?>" value="<?php echo $field->value; ?>" />
    <?php
    }

    function input_head($field = null)
    {
    ?>
        <link rel="stylesheet" type="text/css" href="<?php echo $this->parent->url; ?>/core/fields/date/style.date.css" />
        <script type="text/javascript" src="<?php echo $this->parent->url; ?>/core/fields/date/jquery.ui.js"></script>
        <script type="text/javascript" src="<?php echo $this->parent->url; ?>/core/fields/date/jquery.ui.datepicker.js"></script>
        <script type="text/javascript" src="<?php echo $this->parent->url; ?>/core/fields/date/jquery.ui.timepicker.js"></script>
        <script type="text/javascript">
        jQuery(function() {
            jQuery(".cfs_input input.date").datetimepicker({
                stepMinute: 5
            });
        });
        </script>
    <?php
    }
}
