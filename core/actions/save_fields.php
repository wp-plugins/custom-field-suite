<?php

global $wpdb;

/*---------------------------------------------------------------------------------------------
    Save fields
---------------------------------------------------------------------------------------------*/

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
            if (0 < (int) $field['id'])
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

/*---------------------------------------------------------------------------------------------
    Save rules
---------------------------------------------------------------------------------------------*/

$cfs_rules = $_POST['cfs']['rules'];
$table_name = $wpdb->prefix . 'cfs_rules';

$wpdb->query("DELETE FROM $table_name WHERE group_id = '$post_id'");

if (!empty($cfs_rules['post_types']))
{
    foreach ($cfs_rules['post_types'] as $post_type)
    {
        $data = array(
            'group_id' => $post_id,
            'rule' => 'post_type ==',
            'value' => $post_type,
            'weight' => 0,
        );
        $wpdb->insert($table_name, $data);
    }
}
