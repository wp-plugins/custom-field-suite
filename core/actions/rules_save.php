<?php

global $wpdb;

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
