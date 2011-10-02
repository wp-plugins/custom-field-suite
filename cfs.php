<?php
/*
Plugin Name: Custom Field Suite
Plugin URI: http://uproot.us/custom-field-suite/
Description: Create groups of custom fields.
Version: 1.0.0
Author: Matt Gibbs
Author URI: http://uproot.us/
License: GPL
Copyright: Matt Gibbs
*/

$cfs = new Cfs();
$cfs->version = '1.0.0';

/*
 * @TODO: public forms
 * @TODO: conditional fields
 * @TODO: field validation (validate() method in field class)
 * @TODO: custom query_posts (dot-traversal)
 */
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

        // add js
        add_action('admin_print_scripts', array($this, 'admin_print_scripts'));

        // add css
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
        $this->upgrade();

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
        global $wpdb;

        $matches = array();
        $post_type = get_post_type($post_id);

        $sql = "SELECT p.ID, p.post_title, m.meta_value
        FROM $wpdb->posts p
        INNER JOIN $wpdb->postmeta m ON m.post_id = p.ID AND m.meta_key = 'cfs_options'
        WHERE p.post_type = 'cfs' AND p.post_status = 'publish'";
        $results = $wpdb->get_results($sql);

        if ($results)
        {
            foreach ($results as $result)
            {
                $meta_value = @unserialize($result->meta_value);

                if ($meta_value && isset($meta_value['post_types']))
                {
                    if (in_array($post_type, $meta_value['post_types']))
                    {
                        $matches[$result->ID] = $result->post_title;
                    }
                }
            }
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
    *    admin_head
    *
    *    @author Matt Gibbs
    *    @since 1.0.0
    *
    *-------------------------------------------------------------------------------------*/

    function admin_head()
    {
        include($this->dir . '/core/actions/admin_head.php');
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
            include($this->dir . '/core/actions/fields_save.php');
            include($this->dir . '/core/actions/rules_save.php');
        }
        elseif (wp_verify_nonce($_POST['cfs']['save'], 'cfs_save_input'))
        {
            include($this->dir . '/core/actions/input_save.php');
        }
        else
        {
            return $post_id;
        }
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
        // jquery
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');

        // file upload
        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
        wp_enqueue_script('editor');
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
    *    _fields_meta_box
    *
    *    @author Matt Gibbs
    *    @since 1.0.0
    *
    *-------------------------------------------------------------------------------------*/

    function _fields_meta_box()
    {
        include($this->dir . '/core/admin/fields_meta_box.php');
    }


    /*--------------------------------------------------------------------------------------
    *
    *    _rules_meta_box
    *
    *    @author Matt Gibbs
    *    @since 1.0.0
    *
    *-------------------------------------------------------------------------------------*/

    function _rules_meta_box()
    {
        include($this->dir . '/core/admin/rules_meta_box.php');
    }


    /*--------------------------------------------------------------------------------------
    *
    *    _input_meta_box
    *
    *    @author Matt Gibbs
    *    @since 1.0.0
    *
    *-------------------------------------------------------------------------------------*/

    function _input_meta_box($post, $metabox)
    {
        include($this->dir . '/core/admin/input_meta_box.php');
    }


    /*--------------------------------------------------------------------------------------
    *
    *    upgrade
    *
    *    @author Matt Gibbs
    *    @since 1.0.0
    *
    *-------------------------------------------------------------------------------------*/

    function upgrade()
    {
        include($this->dir . '/core/upgrade.php');
    }


    /*--------------------------------------------------------------------------------------
    *
    *    third-party (plugin) integration
    *
    *    @author Matt Gibbs
    *    @since 1.0.0
    *
    *-------------------------------------------------------------------------------------*/

    function third_party()
    {

    }
}
