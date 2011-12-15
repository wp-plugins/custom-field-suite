<?php

class cfs_File extends cfs_Field
{

    function __construct($parent)
    {
        $this->name = 'file';
        $this->label = __('File Upload', 'cfs');
        $this->parent = $parent;

        add_action('admin_head', array($this, 'admin_head'));
        add_action('admin_head-media-upload-popup', array($this, 'popup_head'));
        add_filter('media_send_to_editor', array($this, 'media_send_to_editor'), 20, 3);
    }

    function html($field)
    {
        $file_url = is_numeric($field->value) ? wp_get_attachment_url($field->value) : $field->value;
        if (empty($field->value))
        {
            $css_hide = array('add' => '', 'remove' => ' hidden');
        }
        else
        {
            $css_hide = array('add' => ' hidden', 'remove' => '');
        }
    ?>
        <input type="button" class="media button add<?php echo $css_hide['add']; ?>" value="<?php _e('Add File', 'cfs'); ?>" />
        <input type="button" class="media button remove<?php echo $css_hide['remove']; ?>" value="<?php _e('Remove', 'cfs'); ?>" />
        <input type="hidden" name="<?php echo $field->input_name; ?>" class="<?php echo $field->input_class; ?>" value="<?php echo $field->value; ?>" />
        <span class="file_url"><?php echo $file_url; ?></span>
    <?php
    }

    function admin_head()
    {
        $post_type = get_post_type($_GET['post']);
        $has_editor = post_type_supports($post_type, 'editor');

        if (!$has_editor)
        {
    ?>
        <style type="text/css">#poststuff .postarea { display: none; }</style>
    <?php
        }
    }

    function popup_head()
    {
        // Don't interfere with the standard Media manager
        if (isset($_GET['cfs_file']))
        {
            // Add the "Insert into Post" button
            $post_type = get_post_type($_GET['post_id']);
            add_post_type_support($post_type, 'editor');
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
            jQuery(".cfs_input .media.button.add").live("click", function() {
                window.cfs_div = jQuery(this);
                tb_show("Attach file", "media-upload.php?post_id=<?php echo $post->ID; ?>&cfs_file=1&TB_iframe=1&width=640&height=480");
                jQuery(this).siblings(".media.button.remove").show();
                jQuery(this).hide();
                return false;
            });
            jQuery(".cfs_input .media.button.remove").live("click", function() {
                jQuery(this).siblings(".file_url").html("");
                jQuery(this).siblings(".file").val("");
                jQuery(this).siblings(".media.button.add").show();
                jQuery(this).hide();
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
