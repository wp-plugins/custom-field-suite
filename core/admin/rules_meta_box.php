<?php

global $post;

$options = (object) get_post_meta($post->ID, 'cfs_options', true);

?>

<table>
    <tr>
        <td class="label">
            <label><?php _e('Post Types', 'cfs'); ?></label>
            <p class="description"><?php _e('Display this field group for the selected post types', 'cfs'); ?></p>
        </td>
        <td>
            <?php
                $choices = get_post_types(array('public' => true));
                if (false !== ($key = array_search('attachment', $choices)))
                {
                    unset($choices[$key]);
                }

                $this->create_field((object) array(
                    'type' => 'select',
                    'input_name' => "cfs[options][post_types]",
                    'input_class' => '',
                    'options' => array('multiple' => '1', 'choices' => implode("\n", $choices)),
                    'value' => $options->post_types,
                ));
            ?>
        </td>
    </tr>
</table>
