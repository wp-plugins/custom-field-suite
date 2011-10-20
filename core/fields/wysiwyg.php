<?php

class cfs_Wysiwyg extends cfs_Field
{

    function __construct($parent)
    {
        $this->name = 'wysiwyg';
        $this->label = __('Wysiwyg Editor', 'cfs');
        $this->parent = $parent;
    }

    function html($field)
    {
    ?>
        <textarea name="<?php echo $field->input_name; ?>" class="<?php echo $field->input_class; ?>" rows="4"><?php echo $field->value; ?></textarea>
    <?php
    }

    function input_head()
    {
        wp_tiny_mce();
    ?>
        <script type="text/javascript">
        jQuery(function() {
            var wysiwyg_count = 0;

            jQuery(".cfs_input .field .wysiwyg").each(function() {

                // generate CSS id
                wysiwyg_count = wysiwyg_count + 1;
                var input_id = "cfs_wysiwyg_" + wysiwyg_count;
                jQuery(this).attr("id", input_id);

                // create wysiwyg
                tinyMCE.execCommand("mceAddControl", false, input_id);
            })
        });
        </script>
    <?php
    }

    function format_value_for_input($value)
    {
        return wp_richedit_pre($value[0]);
    }

    function format_value_for_api($value)
    {
        return apply_filters('the_content', $value[0]);
    }
}
