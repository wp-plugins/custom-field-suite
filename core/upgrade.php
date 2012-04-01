<?php

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

$last_version = get_option('cfs_version');

if (version_compare($last_version, $this->version, '<'))
{
    // Add necessary tables
    if (version_compare($last_version, '1.0.0', '<'))
    {
        $sql = "
        CREATE TABLE {$wpdb->prefix}cfs_fields (
            id INT unsigned not null auto_increment,
            name TEXT,
            label TEXT,
            type TEXT,
            instructions TEXT,
            post_id INT unsigned,
            parent_id INT unsigned default 0,
            weight INT unsigned,
            options TEXT,
            PRIMARY KEY (id),
            INDEX post_id_idx (post_id)
        ) DEFAULT CHARSET=utf8";
        dbDelta($sql);

        $sql = "
        CREATE TABLE {$wpdb->prefix}cfs_values (
            id INT unsigned not null auto_increment,
            field_id INT unsigned,
            meta_id INT unsigned,
            post_id INT unsigned,
            weight INT unsigned,
            sub_weight INT unsigned,
            PRIMARY KEY (id),
            INDEX field_id_idx (field_id),
            INDEX post_id_idx (post_id)
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

    // Convert relationship values
    if (version_compare($last_version, '1.4.2', '<'))
    {
        $sql = "
        SELECT v.field_id, v.meta_id, v.post_id, v.weight, m.meta_key, m.meta_value, f.parent_id
        FROM {$wpdb->prefix}cfs_values v
        INNER JOIN {$wpdb->postmeta} m ON m.meta_id = v.meta_id
        INNER JOIN {$wpdb->prefix}cfs_fields f ON f.id = v.field_id AND f.name = m.meta_key AND f.type = 'relationship'
        WHERE m.meta_value LIKE '%,%'
        ORDER BY v.field_id";
        $results = $wpdb->get_results($sql);

        foreach ($results as $result)
        {
            $all_values = explode(',', $result->meta_value);
            $first_value = array_shift($all_values);

            // Update existing postmeta value
            $wpdb->update(
                $wpdb->postmeta,
                array('meta_value' => $first_value),
                array('meta_id' => $result->meta_id)
            );

            foreach ($all_values as $key => $the_id)
            {
                // Add row into postmeta
                $wpdb->insert($wpdb->postmeta, array(
                    'post_id' => $result->post_id,
                    'meta_key' => $result->meta_key,
                    'meta_value' => $the_id,
                ));
                $meta_id = $wpdb->insert_id;

                // See if relationship field is within a loop
                $weight = (0 < (int) $result->parent_id) ? $result->weight : ($key + 1);
                $sub_weight = (0 < (int) $result->parent_id) ? ($key + 1) : 0;

                // Add row into cfs_values
                $wpdb->insert($wpdb->prefix . 'cfs_values', array(
                    'field_id' => $result->field_id,
                    'meta_id' => $meta_id,
                    'post_id' => $result->post_id,
                    'weight' => $weight,
                    'sub_weight' => $sub_weight,
                ));
            }
        }
    }

    update_option('cfs_version', $this->version);
}
