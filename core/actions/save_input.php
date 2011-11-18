<?php

if (!isset($_POST['cfs']['input']))
{
    return;
}

$field_data = $_POST['cfs']['input'];
$post_data = array('ID' => $_POST['ID']);
$options = array('raw_input' => true);

$this->api->save_fields($field_data, $post_data, $options);
