<?php

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

$last_version = get_option('cfs_version');

if (version_compare($last_version, $this->version, '<'))
{
    $sql = "CREATE TABLE {$wpdb->prefix}cfs_fields (
        id INT unsigned not null auto_increment primary key,
        name TEXT,
        label TEXT,
        type TEXT,
        instructions TEXT,
        post_id INT unsigned,
        parent_id INT unsigned default 0,
        weight INT unsigned,
        options TEXT
    ) DEFAULT CHARSET=utf8";
    dbDelta($sql);

    $sql = "CREATE TABLE {$wpdb->prefix}cfs_values (
        id INT unsigned not null auto_increment primary key,
        field_id INT unsigned,
        meta_id INT unsigned,
        post_id INT unsigned,
        value TEXT,
        weight INT unsigned,
        sub_weight INT unsigned
    ) DEFAULT CHARSET=utf8";
    dbDelta($sql);

    $sql = "CREATE TABLE {$wpdb->prefix}cfs_rules (
        id INT unsigned not null auto_increment primary key,
        group_id INT unsigned,
        rule TEXT,
        value TEXT,
        weight INT unsigned
    ) DEFAULT CHARSET=utf8";
    dbDelta($sql);

    update_option('cfs_version', $this->version);
}
