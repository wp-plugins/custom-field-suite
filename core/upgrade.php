<?php

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

$last_version = get_option('cfs_version');

if (version_compare($last_version, $this->version, '<'))
{
    // Add necessary tables
    if (version_compare($last_version, '1.0.0', '<'))
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
    }

    // Replace the rules table
    if (version_compare($last_version, '1.2.0', '<'))
    {
        $rules = array();
        $results = $wpdb->get_results("SELECT group_id, rule, value FROM {$wpdb->prefix}cfs_rules");
        foreach ($results as $rule)
        {
            $rules[$rule->group_id]['post_types']['operator'] = '==';
            $rules[$rule->group_id]['post_types']['values'][] = $rule->value;
        }

        foreach ($rules as $post_id => $rule)
        {
            update_post_meta($post_id, 'cfs_rules', $rule);
        }

        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}cfs_rules");
    }

    update_option('cfs_version', $this->version);
}
