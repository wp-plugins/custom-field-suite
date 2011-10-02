<?php

global $wpdb;

//var_dump($_POST['cfs']['input']);die();

if (isset($_POST['cfs']['input']))
{
    $table_name = $wpdb->prefix . 'cfs_values';
    $cfs_input = $_POST['cfs']['input'];
    $post_id = $_POST['ID'];

    // Delete from cfs_values and postmeta
    $sql = "DELETE v, m
    FROM {$wpdb->prefix}cfs_values v
    LEFT JOIN {$wpdb->postmeta} m ON m.meta_id = v.meta_id
    WHERE v.post_id = '$post_id'";
    $wpdb->query($sql);

    // Get applicable field names
    $field_names = array();
    $field_ids = implode(',', array_keys($cfs_input));
    $results = $wpdb->get_results("SELECT id, name FROM {$wpdb->prefix}cfs_fields WHERE id IN ($field_ids)");
    foreach ($results as $result)
    {
        $field_names[$result->id] = $result->name;
    }

    // Save individial fields
    foreach ($cfs_input as $field_id => $value)
    {
        $weight = 0;
        $value = $value['value'];

        if (false !== $value && null !== $value)
        {
            foreach ((array) $value as $v)
            {
                // Insert into postmeta
                $data = array(
                    'post_id' => $post_id,
                    'meta_key' => $field_names[$field_id],
                    'meta_value' => $v,
                );
                $wpdb->insert($wpdb->postmeta, $data);
                $meta_id = $wpdb->insert_id;

                // Insert into cfs_values
                $data = array(
                    'field_id' => $field_id,
                    'meta_id' => $meta_id,
                    'post_id' => $post_id,
                    'value' => $v,
                    'weight' => $weight,
                );

                $wpdb->insert($table_name, $data);
                $weight++;
            }
        }
    }
}
