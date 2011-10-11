<input type="hidden" name="cfs[save]" value="<?php echo wp_create_nonce('cfs_save_fields'); ?>" />

<div class="fields">

<?php
global $post;

$results = $this->api->get_input_fields($post->ID, 0);

foreach ($results as $field)
{
    $this->field_html($field);
}
?>

</div>

<!-- [BEGIN] clone field html -->

<div class="field_clone hidden">

<?php
$field = (object) array(
    'id' => '',
    'name' => 'new_field',
    'label' => 'New Field',
    'type' => 'text',
    'instructions' => '',
    'weight' => 'clone',
);

$this->field_html($field);
?>

</div>

<!-- [END] clone field html -->

<div class="table_footer">
    <input type="button" class="button-primary cfs_add_field" value="Add New Field" />
</div>
