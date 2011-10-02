<?php

global $wpdb;

$cfs_options = $_POST['cfs']['options'];

if (empty($cfs_options))
{
    $cfs_options = array();
}

update_post_meta($post_id, 'cfs_options', $cfs_options);
