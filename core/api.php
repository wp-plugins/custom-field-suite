<?php

class cfs_Api
{
    public $parent;
    public $data;

    /*--------------------------------------------------------------------------------------
    *
    *    __construct
    *
    *    @author Matt Gibbs
    *    @since 1.0.0
    *
    *-------------------------------------------------------------------------------------*/

    function __construct($parent)
    {
        $this->parent = $parent;
    }


    /*--------------------------------------------------------------------------------------
    *
    *    get_field
    *
    *    @author Matt Gibbs
    *    @since 1.0.0
    *
    *-------------------------------------------------------------------------------------*/

    function get_field($field_name, $post_id = false)
    {
        global $post;

        $post_id = empty($post_id) ? $post->ID : (int) $post_id;

        // Trigger get_fields
        if (!isset($this->data[$post_id][$field_name]))
        {
            $fields = $this->get_fields($post_id);
            return $fields[$field_name];
        }

        return $this->data[$post_id][$field_name];
    }


    /*--------------------------------------------------------------------------------------
    *
    *    get_fields
    *
    *    @author Matt Gibbs
    *    @since 1.0.0
    *
    *-------------------------------------------------------------------------------------*/

    function get_fields($post_id = false, $options = array())
    {
        global $post, $wpdb;

        $defaults = array(
            'for_input' => false,
        );
        $options = (object) array_merge($defaults, $options);

        $post_id = empty($post_id) ? $post->ID : (int) $post_id;

        // Return cached results
        if (isset($this->data[$post_id]))
        {
            return $this->data[$post_id];
        }

        $field_data = array();

        // Get all field groups for this post
        $group_ids = $this->parent->get_matching_groups($post_id, true);

        if (!empty($group_ids))
        {
            $group_ids = implode(',', array_keys($group_ids));
            $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cfs_fields WHERE post_id IN ($group_ids) ORDER BY weight");
            foreach ($results as $result)
            {
                $fields[$result->id] = $result;
            }
        }

        // Now, get the values for each field
        if (!empty($fields))
        {
            foreach ($fields as $field)
            {
                // Unserialize the options
                $field->options = (@unserialize($field->options)) ? unserialize($field->options) : array();

                // Load the value if necessary
                $value = $this->parent->fields[$field->type]->load_value($field);

                if (null === $value)
                {
                    $sql = "
                    SELECT m.meta_value AS value, v.weight
                    FROM {$wpdb->prefix}cfs_values v
                    INNER JOIN {$wpdb->postmeta} m ON m.meta_id = v.meta_id
                    WHERE m.post_id = '$post_id' AND v.field_id = '$field->id'
                    ORDER BY v.weight, v.sub_weight";

                    $results = $wpdb->get_results($sql);

                    // Clean the SQL results
                    $value = array();
                    foreach ($results as $result)
                    {
                        if (0 < (int) $field->parent_id)
                        {
                            $value[$result->weight][] = $result->value;
                        }
                        else
                        {
                            $value[] = $result->value;
                        }
                    }
                }

                // Format input data differently from API data
                if (false !== $options->for_input)
                {
                    // Loop field
                    if (0 < (int) $field->parent_id)
                    {
                        foreach ($value as $weight => $loop_values)
                        {
                            $field_data[$field->id][$weight] = $this->apply_value_filters($field, $loop_values, $options);
                        }
                    }
                    // Basic field
                    else
                    {
                        $field_data[$field->id] = $this->apply_value_filters($field, $value, $options);
                    }
                }
                else
                {
                    // Loop field
                    if (0 < (int) $field->parent_id)
                    {
                        // Get the field name from the ID
                        $parent_field_name = $fields[$field->parent_id]->name;

                        foreach ($value as $weight => $loop_values)
                        {
                            $field_data[$parent_field_name][$weight][$field->name] = $this->apply_value_filters($field, $loop_values, $options);
                        }
                    }
                    // Basic field
                    else
                    {
                        $field_data[$field->name] = $this->apply_value_filters($field, $value, $options);
                    }
                }
            }
        }

        $this->data[$post_id] = $field_data;
        return $field_data;
    }


    /*--------------------------------------------------------------------------------------
    *
    *    get_reverse_related
    *
    *    @author Matt Gibbs
    *    @since 1.4.4
    *
    *-------------------------------------------------------------------------------------*/

    function get_reverse_related($field_name, $post_id)
    {
        global $wpdb;

        $sql = $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value = %s", $field_name, $post_id);
        $results = $wpdb->get_results($sql);
        $output = array();

        foreach ($results as $result)
        {
            $output[] = $result->post_id;
        }
        return $output;
    }


    /*--------------------------------------------------------------------------------------
    *
    *    get_labels
    *
    *    @author Matt Gibbs
    *    @since 1.3.3
    *
    *-------------------------------------------------------------------------------------*/

    function get_labels($field_name = false, $post_id = false)
    {
        global $post, $wpdb;

        $post_id = empty($post_id) ? $post->ID : (int) $post_id;

        // Get all field groups for this post
        $group_ids = $this->parent->get_matching_groups($post_id, true);

        $labels = array();

        if (!empty($group_ids))
        {
            $group_ids = implode(',', array_keys($group_ids));
            $results = $wpdb->get_results("SELECT name, label FROM {$wpdb->prefix}cfs_fields WHERE post_id IN ($group_ids) ORDER BY weight");
            foreach ($results as $result)
            {
                if (empty($field_name))
                {
                    $labels[$result->name] = $result->label;
                }
                elseif ($result->name == $field_name)
                {
                    $labels = $result->label;
                }
            }
        }
        return $labels;
    }


    /*--------------------------------------------------------------------------------------
    *
    *    apply_value_filters
    *
    *    @author Matt Gibbs
    *    @since 1.0.0
    *
    *-------------------------------------------------------------------------------------*/

    function apply_value_filters($field, $value, $options)
    {
        // Format value for input
        if (false !== $options->for_input)
        {
            $value = $this->parent->fields[$field->type]->format_value_for_input($value);
        }

        // Format value for api
        else
        {
            $value = $this->parent->fields[$field->type]->format_value_for_api($value);
        }

        return $value;
    }


    /*--------------------------------------------------------------------------------------
    *
    *    get_input_fields
    *
    *    @author Matt Gibbs
    *    @since 1.0.0
    *
    *-------------------------------------------------------------------------------------*/

    function get_input_fields($group_id, $parent_id = false)
    {
        global $post, $wpdb;

        $fields = array();

        $values = $this->get_fields($post->ID, array('for_input' => true));

        if ($group_id)
        {
            $where = (false !== $group_id) ? "WHERE post_id = $group_id" : '';

            if (false !== $parent_id)
            {
                $where .= " AND parent_id = $parent_id";
            }

            $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cfs_fields $where ORDER BY weight");

            foreach ($results as $field)
            {
                // Unserialize the options
                $field->options = (@unserialize($field->options)) ? unserialize($field->options) : array();

                // If no field value exists, set it to NULL
                $field->value = isset($values[$field->id]) ? $values[$field->id] : null;

                if (isset($field->options['default_value']) && empty($field->value))
                {
                    // Sub-fields expect an array
                    if (false !== $parent_id)
                    {
                        $field->value = (array) $field->options['default_value'];
                    }
                    else
                    {
                        $field->value = $field->options['default_value'];
                    }
                }

                $fields[$field->id] = $field;
            }
        }

        return $fields;
    }


    /*--------------------------------------------------------------------------------------
    *
    *    save_fields
    *
    *    @author Matt Gibbs
    *    @since 1.1.3
    *
    *-------------------------------------------------------------------------------------*/

    function save_fields($field_data = array(), $post_data = array(), $options = array())
    {
        global $wpdb;

        $defaults = array(
            'raw_input' => false,
        );
        $options = (object) array_merge($defaults, $options);

        // create post if the ID is missing
        if (empty($post_data['ID']))
        {
            $post_defaults = array(
                'post_title' => 'Untitled',
                'post_content' => '',
                'post_content_filtered' => '',
                'post_excerpt' => '',
                'to_ping' => '',
                'pinged' => '',
            );
            $post_data = array_merge($post_defaults, $post_data);
            $post_id = wp_insert_post($post_data);
        }
        else
        {
            $post_id = $post_data['ID'];

            if (1 < count($post_data))
            {
                $wpdb->update($wpdb->posts, $post_data, array('ID' => $post_id));
            }
        }

        // If NOT "raw_input", then flatten the data!
        if (false === $options->raw_input)
        {
            // Get available fields for this post (we need to find the field IDs)
            $field_groups = $this->parent->get_matching_groups($post_id);
            $group_ids = array_keys($field_groups);

            // Get the fields
            $fields = array();
            $cfs_input = array();
            $field_name_lookup = array();

            $results = $wpdb->get_results("SELECT id, name, type, parent_id FROM {$wpdb->prefix}cfs_fields WHERE post_id IN (" . implode(',', $group_ids) . ") ORDER BY parent_id, weight");
            foreach ($results as $result)
            {
                $field_name_lookup[$result->id] = $result->name;

                if (0 < (int) $result->parent_id)
                {
                    $parent_name = $field_name_lookup[$result->parent_id];
                    $fields["$parent_name $result->name"] = array('id' => $result->id, 'type' => $result->type);
                }
                else
                {
                    $fields[$result->name] = array('id' => $result->id, 'type' => $result->type);
                }
            }

            // Loop through the fields
            foreach ($field_data as $field_name => $field_value)
            {
                $the_field = $fields[$field_name];

                // Get the field type
                $field_type = $the_field['type'];

                if ('loop' == $field_type)
                {
                    foreach ($field_value as $loop_row)
                    {
                        foreach ($loop_row as $sub_field_name => $sub_field_value)
                        {
                            $sub_field_id = $fields["$field_name $sub_field_name"]['id'];
                            $cfs_input[$sub_field_id][]['value'][] = $sub_field_value;
                        }
                    }
                }
                else
                {
                    $cfs_input[$the_field['id']]['value'] = $field_value;
                }
            }
        }
        else
        {
            $cfs_input = $field_data;

            // If saving raw input, delete existing postdata
            $sql = "
            DELETE v, m
            FROM {$wpdb->prefix}cfs_values v
            LEFT JOIN {$wpdb->postmeta} m ON m.meta_id = v.meta_id
            WHERE v.post_id = '$post_id'";
            $wpdb->query($sql);
        }

        $field_names = array();
        $field_types = array();
        $field_ids = implode(',', array_keys($cfs_input));

        // Delete from cfs_values and postmeta
        $sql = "
        DELETE v, m
        FROM {$wpdb->prefix}cfs_values v
        LEFT JOIN {$wpdb->postmeta} m ON m.meta_id = v.meta_id
        WHERE v.post_id = '$post_id' and v.field_id IN ($field_ids)";
        $wpdb->query($sql);

        $results = $wpdb->get_results("SELECT id, type, name FROM {$wpdb->prefix}cfs_fields WHERE id IN ($field_ids)");
        foreach ($results as $result)
        {
            $field_names[$result->id] = $result->name;
            $field_types[$result->id] = $result->type;
        }

        // Save each field
        foreach ($cfs_input as $field_id => $values)
        {
            $weight = 0;
            $sub_weight = 0;

            // clean the values
            $values = stripslashes_deep($values);

            // Basic field
            if (isset($values['value']))
            {
                // Trigger the pre_save field hook
                $values['value'] = $this->parent->fields[$field_types[$field_id]]->pre_save($values['value']);

                foreach ((array) $values['value'] as $v)
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
                        'weight' => $weight,
                        'sub_weight' => $sub_weight,
                    );

                    $wpdb->insert($wpdb->prefix . 'cfs_values', $data);
                    $weight++;
                }
            }

            // Loop field
            elseif (is_array($values))
            {
                foreach ($values as $key => $value)
                {
                    $sub_weight = 0;

                    // Trigger the pre_save field hook
                    $value['value'] = $this->parent->fields[$field_types[$field_id]]->pre_save($value['value']);

                    foreach ((array) $value['value'] as $v)
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
                            'weight' => $weight,
                            'sub_weight' => $sub_weight,
                        );

                        $wpdb->insert($wpdb->prefix . 'cfs_values', $data);
                        $sub_weight++;
                    }
                    $weight++;
                }
            }
        }
    }
}
