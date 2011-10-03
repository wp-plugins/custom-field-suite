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
        $options = (object) array_merge(array(), $options);

        $post_id = empty($post_id) ? $post->ID : (int) $post_id;

        // Return cached results
        if (isset($this->data[$post_id]))
        {
            return $this->data[$post_id];
        }

        $field_data = array();

        // Get all field groups for this post
        $group_ids = $this->parent->get_matching_groups($post_id);

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
                if (method_exists($this->parent->fields[$field->type], 'load_value'))
                {
                    $value = $this->parent->fields[$field->type]->load_value($field);
                }
                else
                {
                    $sql = "
                    SELECT v.value, v.weight
                    FROM {$wpdb->prefix}cfs_values v
                    WHERE v.post_id = '$post_id' AND v.field_id = '$field->id'
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
            if (method_exists($this->parent->fields[$field->type], 'format_value_for_input'))
            {
                $value = $this->parent->fields[$field->type]->format_value_for_input($value);
            }
            else
            {
                $value = $value[0];
            }
        }

        // Format value for api
        else
        {
            if (method_exists($this->parent->fields[$field->type], 'format_value_for_api'))
            {
                $value = $this->parent->fields[$field->type]->format_value_for_api($value);
            }
            else
            {
                $value = $value[0];
            }
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

                if (!empty($values[$field->id]))
                {
                    $field->value = $values[$field->id];
                }

                $fields[$field->id] = $field;
            }
        }

        return $fields;
    }
}
