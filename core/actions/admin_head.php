<?php

global $post, $wpdb;

/*---------------------------------------------------------------------------------------------
    Field management screen
---------------------------------------------------------------------------------------------*/

if ('cfs' == $GLOBALS['post_type'])
{
    foreach ($this->fields as $field_name => $field_data)
    {
        $options_html[$field_name] = '';
        if (method_exists($this->fields[$field_name], 'options_html'))
        {
            ob_start();
            $this->fields[$field_name]->options_html('clone', $field);
            $options_html[$field_name] = ob_get_clean();
        }
    }

    $field_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}cfs_fields WHERE post_id = '$post->ID'");
    $results = $this->api->get_input_fields($post->ID, 0);
?>

    <script type="text/javascript">

    field_index = <?php echo $field_count; ?>;
    options_html = <?php echo json_encode($options_html); ?>;

    </script>

    <script type="text/javascript" src="<?php echo $this->url; ?>/js/fields.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo $this->url; ?>/css/fields.css" />

<?php

    add_meta_box('cfs_fields', 'Fields', array($this, 'meta_box_fields'), 'cfs', 'normal', 'high');
    add_meta_box('cfs_rules', 'Placement Rules', array($this, 'meta_box_rules'), 'cfs', 'normal', 'high');
}

/*---------------------------------------------------------------------------------------------
    Field input
---------------------------------------------------------------------------------------------*/

else
{
    $field_group_ids = $this->get_matching_groups($post->ID);

    if (!empty($field_group_ids))
    {
?>

    <script type="text/javascript" src="<?php echo $this->url; ?>/js/input.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo $this->url; ?>/css/input.css" />

<?php
        // Support for multiple metaboxes
        foreach ($field_group_ids as $group_id => $title)
        {
            add_meta_box('cfs_input_' . $group_id, $title, array($this, 'meta_box_input'), $post->post_type, 'normal', 'high', array('group_id' => $group_id));

            // Add .cfs_input to the metabox CSS
            add_filter("postbox_classes_{$post->post_type}_cfs_input_{$group_id}", 'cfs_postbox_classes');
        }
    }
}

/*---------------------------------------------------------------------------------------------
    Helper functions
---------------------------------------------------------------------------------------------*/

function cfs_postbox_classes($classes)
{
    $classes[] = 'cfs_input';
    return $classes;
}
