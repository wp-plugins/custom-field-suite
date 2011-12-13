<?php
/*
Plugin Name: Custom Field Suite
Plugin URI: http://uproot.us/custom-field-suite/
Description: Visually create custom field groups.
Version: 1.2.0
Author: Matt Gibbs
Author URI: http://uproot.us/
License: GPL
Copyright: Matt Gibbs
*/

$cfs = new Cfs();
$cfs->version = '1.2.0';

class Cfs
{
    public $dir;
    public $url;
    public $siteurl;
    public $version;
    public $fields;
    public $api;

    /*--------------------------------------------------------------------------------------
    *
    *    __construct
    *
    *    @author Matt Gibbs
    *    @since 1.0.0
    *
    *-------------------------------------------------------------------------------------*/

    function __construct()
    {
        $this->dir = (string) dirname(__FILE__);
        $this->url = plugins_url('custom-field-suite');
        $this->siteurl = get_bloginfo('url');

        // load the api
        include($this->dir . '/core/api.php');
        $this->api = new cfs_Api($this);

        // add actions
        add_action('init', array($this, 'init'));
        add_action('admin_head', array($this, 'admin_head'));
        add_action('admin_footer', array($this, 'admin_footer'));
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('save_post', array($this, 'save_post'));
        add_action('delete_post', array($this, 'delete_post'));

        // add translations
        load_plugin_textdomain('cfs', false, $this->dir . '/lang');

        // add css + js
        add_action('admin_print_scripts', array($this, 'admin_print_scripts'));
        add_action('admin_print_styles', array($this, 'admin_print_styles'));
    }


    /*--------------------------------------------------------------------------------------
    *
    *    init
    *
    *    @author Matt Gibbs
    *    @since 1.0.0
    *
    *-------------------------------------------------------------------------------------*/

    function init()
    {
        // perform upgrades
        include($this->dir . '/core/upgrade.php');

        // get all available field types
        $this->fields = $this->get_field_types();

        include($this->dir . '/core/actions/init.php');
    }


    /*--------------------------------------------------------------------------------------
    *
    *    get_field_types
    *
    *    @author Matt Gibbs
    *    @since 1.0.0
    *
    *-------------------------------------------------------------------------------------*/

    function get_field_types()
    {
        // include the parent field type
        include($this->dir . '/core/fields/field.php');

        $field_types = array(
            'text' => $this->dir . '/core/fields/text.php',
            'textarea' => $this->dir . '/core/fields/textarea.php',
            'wysiwyg' => $this->dir . '/core/fields/wysiwyg.php',
            'date' => $this->dir . '/core/fields/date/date.php',
            'true_false' => $this->dir . '/core/fields/true_false.php',
            'select' => $this->dir . '/core/fields/select.php',
            'relationship' => $this->dir . '/core/fields/relationship.php',
            'file' => $this->dir . '/core/fields/file.php',
            'loop' => $this->dir . '/core/fields/loop.php',
        );

        // support custom field types
        $field_types = apply_filters('cfs_field_types', $field_types);

        foreach ($field_types as $type => $path)
        {
            include($path);
            $class_name = 'cfs_' . ucwords($type);
            $field_types[$type] = new $class_name($this);
        }

        return $field_types;
    }


    /*--------------------------------------------------------------------------------------
    *
    *    get_matching_groups
    *
    *    @author Matt Gibbs
    *    @since 1.0.0
    *
    *-------------------------------------------------------------------------------------*/

    function get_matching_groups($post_id)
    {
        global $wpdb, $current_user;

        // Get variables
        $matches = array();
        $post_type = get_post_type($post_id);
        $user_roles = $current_user->roles;

        // Get all term ids associated with this post
        $sql = "
        SELECT tt.term_id
        FROM $wpdb->term_taxonomy tt
        INNER JOIN $wpdb->term_relationships tr ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tr.object_id = '$post_id'";
        $term_ids = $wpdb->get_results($sql, ARRAY_N);

        // Get all rules
        $sql = "
        SELECT p.ID, p.post_title, m.meta_value AS rules
        FROM $wpdb->posts p
        INNER JOIN $wpdb->postmeta m ON m.post_id = p.ID AND m.meta_key = 'cfs_rules'";
        $results = $wpdb->get_results($sql);

        foreach ($results as $result)
        {
            $rules = unserialize($result->rules);

            // Post types
            if (isset($rules['post_types']))
            {
                $operator = $rules['post_types']['operator'];
                $in_array = in_array($post_type, $rules['post_types']['values']);
                if (($in_array && '!=' == $operator) || (!$in_array && '==' == $operator))
                {
                    continue;
                }
            }

            // User roles
            if (isset($rules['user_roles']))
            {
                $operator = $rules['user_roles']['operator'];

                // Loop through user_roles
                $in_array = false;
                foreach ($user_roles as $role)
                {
                    if (in_array($role, $rules['user_roles']['values']))
                    {
                        $in_array = true;
                        break;
                    }
                }
                if (($in_array && '!=' == $operator) || (!$in_array && '==' == $operator))
                {
                    continue;
                }
            }

            // Taxonomies
            if (isset($rules['term_ids']))
            {
                $operator = $rules['term_ids']['operator'];

                // Loop through term_ids
                $in_array = false;
                foreach ($term_ids as $term_id)
                {
                    if (in_array($term_id, $rules['term_ids']['values']))
                    {
                        $in_array = true;
                        break;
                    }
                }
                if (($in_array && '!=' == $operator) || (!$in_array && '==' == $operator))
                {
                    continue;
                }
            }

            // Post IDs
            if (isset($rules['post_ids']))
            {
                $operator = $rules['post_ids']['operator'];
                $in_array = in_array($post_id, $rules['post_ids']['values']);
                if (($in_array && '!=' == $operator) || (!$in_array && '==' == $operator))
                {
                    continue;
                }
            }

            $matches[$result->ID] = $result->post_title;
        }

        return $matches;
    }


    /*--------------------------------------------------------------------------------------
    *
    *    create_field
    *
    *    @author Matt Gibbs
    *    @since 1.0.0
    *
    *-------------------------------------------------------------------------------------*/

    function create_field($field)
    {
        $this->fields[$field->type]->html($field);
    }


    /*--------------------------------------------------------------------------------------
    *
    *    get field values from api
    *
    *    @author Matt Gibbs
    *    @since 1.0.0
    *
    *-------------------------------------------------------------------------------------*/

    function get($field_name = false, $post_id = false)
    {
        if (false !== $field_name)
        {
            return $this->api->get_field($field_name, $post_id);
        }
        return $this->api->get_fields($post_id);
    }


    /*--------------------------------------------------------------------------------------
    *
    *    save field values (and post data)
    *
    *    @author Matt Gibbs
    *    @since 1.1.4
    *
    *-------------------------------------------------------------------------------------*/

    function save($field_data = array(), $post_data = array(), $options = array())
    {
        return $this->api->save_fields($field_data, $post_data, $options);
    }


    /*--------------------------------------------------------------------------------------
    *
    *    admin_head
    *
    *    @author Matt Gibbs
    *    @since 1.0.0
    *
    *-------------------------------------------------------------------------------------*/

    function admin_head()
    {
        if (in_array($GLOBALS['pagenow'], array('post.php', 'post-new.php')))
        {
            include($this->dir . '/core/actions/admin_head.php');
        }
    }


    /*--------------------------------------------------------------------------------------
    *
    *    admin_footer
    *
    *    @author Matt Gibbs
    *    @since 1.0.0
    *
    *-------------------------------------------------------------------------------------*/

    function admin_footer()
    {
        if ('cfs' == $GLOBALS['post_type'] && 'edit.php' == $GLOBALS['pagenow'])
        {
            include($this->dir . '/core/actions/admin_footer.php');
        }
    }


    /*--------------------------------------------------------------------------------------
    *
    *    admin_menu
    *
    *    @author Matt Gibbs
    *    @since 1.0.0
    *
    *-------------------------------------------------------------------------------------*/

    function admin_menu()
    {
        add_options_page(__('Custom Field Suite', 'cfs'), __('Custom Field Suite', 'cfs'), 'manage_options', 'edit.php?post_type=cfs');
    }


    /*--------------------------------------------------------------------------------------
    *
    *    save_post
    *
    *    @author Matt Gibbs
    *    @since 1.0.0
    *
    *-------------------------------------------------------------------------------------*/

    function save_post($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        {
            return $post_id;
        }

        if (!isset($_POST['cfs']['save']))
        {
            return $post_id;
        }

        if (wp_is_post_revision($post_id))
        {
            $post_id = wp_is_post_revision($post_id);
        }

        if (wp_verify_nonce($_POST['cfs']['save'], 'cfs_save_fields'))
        {
            include($this->dir . '/core/actions/save_fields.php');
        }
        elseif (wp_verify_nonce($_POST['cfs']['save'], 'cfs_save_input'))
        {
            if (isset($_POST['cfs']['input']))
            {
                $field_data = $_POST['cfs']['input'];
                $post_data = array('ID' => $_POST['ID']);
                $options = array('raw_input' => true);
                $this->api->save_fields($field_data, $post_data, $options);
            }
        }

        return $post_id;
    }


    /*--------------------------------------------------------------------------------------
    *
    *    delete_post
    *
    *    @author Matt Gibbs
    *    @since 1.0.0
    *
    *-------------------------------------------------------------------------------------*/

    function delete_post($post_id)
    {
        global $wpdb;

        if ('cfs' == get_post_type($post_id))
        {
            $wpdb->query("DELETE FROM {$wpdb->prefix}cfs_fields WHERE post_id = '$post_id'");
        }
        else
        {
            $wpdb->query("DELETE FROM {$wpdb->prefix}cfs_values WHERE post_id = '$post_id'");
        }

        return true;
    }


    /*--------------------------------------------------------------------------------------
    *
    *    admin_print_scripts
    *
    *    @author Matt Gibbs
    *    @since 1.0.0
    *
    *-------------------------------------------------------------------------------------*/

    function admin_print_scripts()
    {
        $scripts = array('jquery', 'jquery-ui-core', 'media-upload', 'thickbox', 'editor');
        wp_enqueue_script($scripts);
    }


    /*--------------------------------------------------------------------------------------
    *
    *    admin_print_styles
    *
    *    @author Matt Gibbs
    *    @since 1.0.0
    *
    *-------------------------------------------------------------------------------------*/

    function admin_print_styles()
    {
        wp_enqueue_style('thickbox');
    }


    /*--------------------------------------------------------------------------------------
    *
    *    meta_box
    *
    *    @author Matt Gibbs
    *    @since 1.0.0
    *
    *-------------------------------------------------------------------------------------*/

    function meta_box($post, $metabox)
    {
        $box = $metabox['args']['box'];
        include($this->dir . "/core/admin/meta_box_$box.php");
    }


    /*--------------------------------------------------------------------------------------
    *
    *    field_html
    *
    *    @author Matt Gibbs
    *    @since 1.0.3
    *
    *-------------------------------------------------------------------------------------*/

    function field_html($field)
    {
        $field->sub_field = isset($field->sub_field) ? 1 : '{sub_field}';

        include($this->dir . '/core/admin/field_html.php');
    }
}
