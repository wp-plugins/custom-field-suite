<?php

class cfs_File extends cfs_Field
{

    function __construct($parent)
    {
        $this->name = 'file';
        $this->label = __('File Upload', 'cfs');
        $this->parent = $parent;

        // alter the media_send_to_editor response
        add_filter('media_send_to_editor', array($this, 'media_send_to_editor'), 20, 3);
    }

    function html($field)
    {
        global $post;

        $file_url = is_numeric($field->value) ? wp_get_attachment_url($field->value) : $field->value;
    ?>
        <a href="media-upload.php?post_id=<?php echo $post->ID; ?>&TB_iframe=1&width=640&height=480" class="thickbox media button"><?php _e('Add File', 'cfs'); ?></a>
        <div class="file_url"><?php echo $file_url; ?></div>
        <input type="hidden" name="<?php echo $field->input_name; ?>" class="<?php echo $field->input_class; ?>" value="<?php echo $field->value; ?>" />
    <?php
    }

    function media_send_to_editor($html, $id, $attachment)
    {
        $media = array(
            'id' => $id,
            'url' => $attachment['url'],
        );

        // return "html" param
        return json_encode($media);
    }

    function input_head($field = null)
    {
    ?>
        <script type="text/javascript">
        jQuery(function() {
            jQuery(".cfs_input .media.button").live("click", function() {
                jQuery(".cfs_input input.media.button").removeClass("active");
                jQuery(this).addClass("active");
            });

            window.send_to_editor = function(html) {
                var file = jQuery.parseJSON(html);
                jQuery(".cfs_input .media.button.active").closest(".field").find(".file_url").html(file.url);
                jQuery(".cfs_input .media.button.active").closest(".field").find(".file:last").val(file.id);
                tb_remove();
            }
        });
        </script>
    <?php
    }

    function format_value_for_api($value)
    {
        if (is_numeric($value[0]))
        {
            return wp_get_attachment_url($value[0]);
        }
        return $value[0];
    }
}
