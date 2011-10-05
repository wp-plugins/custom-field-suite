<?php

global $post;

function cfs_postbox_classes($classes)
{
    $classes[] = 'cfs_input';
    return $classes;
}

if (in_array($GLOBALS['pagenow'], array('post.php', 'post-new.php')))
{
    // Building custom post types
    if ('cfs' == $GLOBALS['post_type'])
    {
        echo '<link rel="stylesheet" type="text/css" href="' . $this->url . '/css/style.fields.css" />';
        add_meta_box('cfs_fields', 'Fields', array($this, '_fields_meta_box'), 'cfs', 'normal', 'high');
        add_meta_box('cfs_rules', 'Placement Rules', array($this, '_rules_meta_box'), 'cfs', 'normal', 'high');
    }

    // Displaying fields on post edit pages
    else
    {
        $field_group_ids = $this->get_matching_groups($post->ID);

        if (!empty($field_group_ids))
        {
            echo '<link rel="stylesheet" type="text/css" href="' . $this->url . '/css/style.input.css" />';

            foreach ($field_group_ids as $group_id => $title)
            {
                add_meta_box('cfs_input_' . $group_id, $title, array($this, '_input_meta_box'), $post->post_type, 'normal', 'high', array('group_id' => $group_id));

                // Add the "cfs_input" CSS class to the meta box
                add_filter("postbox_classes_{$post->post_type}_cfs_input_{$group_id}", 'cfs_postbox_classes');
            }
        }
    }
}
