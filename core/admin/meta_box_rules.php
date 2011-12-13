<?php

global $post, $wpdb, $wp_roles;

$rules = get_post_meta($post->ID, 'cfs_rules', true);

// Post types
$post_types = get_post_types(array('public' => true));
if (false !== ($key = array_search('attachment', $post_types)))
{
    unset($post_types[$key]);
}

// User roles
foreach ($wp_roles->roles as $key => $role)
{
    $user_roles[] = $key;
}

// Post IDs
$results = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_status = 'publish' ORDER BY post_title");
foreach ($results as $result)
{
    $post_ids[] = "$result->ID : $result->post_title";
}

// Term IDs
$sql = "
SELECT t.term_id, t.name, tt.taxonomy
FROM $wpdb->terms t
INNER JOIN $wpdb->term_taxonomy tt ON tt.term_id = t.term_id AND tt.taxonomy != 'post_tag'
ORDER BY tt.parent, tt.taxonomy, t.name";
$results = $wpdb->get_results($sql);
foreach ($results as $result)
{
    $term_ids[] = "$result->term_id :  $result->name ($result->taxonomy)";
}
?>

<table style="width:100%">
    <tr>
        <td class="label">
            <label><?php _e('Post Types', 'cfs'); ?></label>
        </td>
        <td style="width:80px">
            <select name="cfs[rules][operator][post_types]" style="width:80px">
                <option value="==">equals</option>
                <option value="!=">is not</option>
            </select>
        </td>
        <td>
            <?php
                $this->create_field((object) array(
                    'type' => 'select',
                    'input_name' => "cfs[rules][post_types]",
                    'input_class' => 'chosen-select',
                    'options' => array('multiple' => '1', 'choices' => implode("\n", $post_types)),
                    'value' => $rules['post_types']['values'],
                ));
            ?>
        </td>
    </tr>
    <tr>
        <td class="label">
            <label><?php _e('User Roles', 'cfs'); ?></label>
        </td>
        <td style="width:80px">
            <select name="cfs[rules][operator][user_roles]" style="width:80px">
                <option value="==">equals</option>
                <option value="!=">is not</option>
            </select>
        </td>
        <td>
            <?php
                $this->create_field((object) array(
                    'type' => 'select',
                    'input_name' => "cfs[rules][user_roles]",
                    'input_class' => 'chosen-select',
                    'options' => array('multiple' => '1', 'choices' => implode("\n", $user_roles)),
                    'value' => $rules['user_roles']['values'],
                ));
            ?>
        </td>
    </tr>
    <tr>
        <td class="label">
            <label><?php _e('Posts', 'cfs'); ?></label>
        </td>
        <td style="width:80px">
            <select name="cfs[rules][operator][post_ids]" style="width:80px">
                <option value="==">equals</option>
                <option value="!=">is not</option>
            </select>
        </td>
        <td>
            <?php
                $this->create_field((object) array(
                    'type' => 'select',
                    'input_name' => "cfs[rules][post_ids]",
                    'input_class' => 'chosen-select',
                    'options' => array('multiple' => '1', 'choices' => implode("\n", $post_ids)),
                    'value' => $rules['post_ids']['values'],
                ));
            ?>
        </td>
    </tr>
    <tr>
        <td class="label">
            <label><?php _e('Taxonomy Terms', 'cfs'); ?></label>
        </td>
        <td style="width:80px">
            <select name="cfs[rules][operator][term_ids]" style="width:80px">
                <option value="==">equals</option>
                <option value="!=">is not</option>
            </select>
        </td>
        <td>
            <?php
                $this->create_field((object) array(
                    'type' => 'select',
                    'input_name' => "cfs[rules][term_ids]",
                    'input_class' => 'chosen-select',
                    'options' => array('multiple' => '1', 'choices' => implode("\n", $term_ids)),
                    'value' => $rules['term_ids']['values'],
                ));
            ?>
        </td>
    </tr>
</table>
