<?php

global $wpdb;

if (isset($_POST['cfs']['fields']))
{
    $weight = 0;
    $last_parent = 0;
    $cfs_fields = $_POST['cfs']['fields'];
    $table_name = $wpdb->prefix . 'cfs_fields';

    // remove all existing fields
    $wpdb->query("DELETE FROM $table_name WHERE post_id = '$post_id'");

    foreach ($cfs_fields as $key => $field)
    {
        if ('clone' !== (string) $key)
        {
            // clean the field
            $field = stripslashes_deep($field);

            // check for sub-field
            $parent_id = 0;

            if (isset($field['sub_field']) && '1' === (string) $field['sub_field'])
            {
                $parent_id = $last_parent;
            }

            $data = array(
                'name' => $field['name'],
                'label' => $field['label'],
                'type' => $field['type'],
                'instructions' => $field['instructions'],
                'post_id' => $post_id,
                'parent_id' => $parent_id,
                'weight' => $weight,
                'options' => serialize($field['options']),
            );

            // use an existing ID if available
            if (isset($field['id']))
            {
                $data['id'] = (int) $field['id'];
            }

            // insert the field
            $wpdb->insert($table_name, $data);

            // set the new parent id if needed
            if (0 == $parent_id)
            {
                $last_parent = $wpdb->insert_id;
            }

            $weight++;
        }
    }
}
