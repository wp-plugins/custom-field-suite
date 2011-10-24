<?php

class cfs_File extends cfs_Field
{

    function __construct($parent)
    {
        $this->name = 'file';
        $this->label = __('File Upload', 'cfs');
        $this->parent = $parent;

        // alter the media_send_to_editor response
        add_action('admin_head-media-upload-popup', array($this, 'popup_head'));
        add_filter('media_send_to_editor', array($this, 'media_send_to_editor'), 20, 3);
    }

    function html($field)
    {
        $file_url = is_numeric($field->value) ? wp_get_attachment_url($field->value) : $field->value;
    ?>
        <input type="button" class="media button" value="<?php _e('Add File', 'cfs'); ?>" />
        <input type="hidden" name="<?php echo $field->input_name; ?>" class="<?php echo $field->input_class; ?>" value="<?php echo $field->value; ?>" />
        <div class="file_url"><?php echo $file_url; ?></div>
    <?php
    }

    function popup_head()
    {
        // Don't interfere with the standard Media Uploader
        if (isset($_GET['cfs_file']))
        {
    ?>
        <script type="text/javascript">
        jQuery(function() {
            jQuery("form#filter").each(function() {
                jQuery(this).append('<input type="hidden" name="cfs_file" value="1" />');
            });
        });
        </script>
    <?php
        }
    }

    function media_send_to_editor($html, $id, $attachment)
    {
        parse_str($_POST["_wp_http_referer"], $postdata);

        if (isset($postdata['cfs_file']))
        {
    ?>
        <script type="text/javascript">
        self.parent.cfs_div.siblings(".file_url").html("<?php echo $attachment['url']; ?>");
        self.parent.cfs_div.siblings(".file").val("<?php echo $id; ?>");
        self.parent.cfs_div = null;
        self.parent.tb_remove();
        </script>
    <?php
            exit;
        }
        else
        {
            return $html;
        }
    }

    function input_head($field = null)
    {
        global $post;
    ?>
        <script type="text/javascript">
        jQuery(function() {
            jQuery(".cfs_input .media.button").live("click", function() {
                window.cfs_div = jQuery(this);
                tb_show("Attach file", "media-upload.php?post_id=<?php echo $post->ID; ?>&cfs_file=1&TB_iframe=1&width=640&height=480");
                return false;
            });
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
