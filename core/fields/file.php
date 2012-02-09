<?php

class cfs_File extends cfs_Field
{

    function __construct($parent)
    {
        $this->name = 'file';
        $this->label = __('File Upload', 'cfs');
        $this->parent = $parent;

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

    function popup_head()
    {
        // Don't interfere with the default Media popup
        if (isset($_GET['cfs_file']))
        {
            // Ensure that "Insert into Post" appears
            $post_type = get_post_type($_GET['post_id']);
            add_post_type_support($post_type, 'editor');
    ?>
        <script type="text/javascript">
        (function($) {
            $(function() {
                $('form#filter').each(function() {
                    $(this).append('<input type="hidden" name="cfs_file" value="1" />');
                });

                $('#media-items').bind('DOMNodeInserted', function() {
                    $('tr.image_alt').hide();
                    $('tr.post_excerpt').hide();
                    $('tr.url').hide();
                    $('tr.align').hide();
                    $('tr.image-size').hide();
                    $('tr.submit input.button').val('<?php _e('Attach File', 'cfs'); ?>');
                }).trigger('DOMNodeInserted');
            });
        })(jQuery);
        </script>
    <?php
        }
    }

    function media_send_to_editor($html, $id, $attachment)
    {
        parse_str($_POST['_wp_http_referer'], $postdata);

        if (isset($postdata['cfs_file']))
        {
    ?>
        <script type="text/javascript">
        self.parent.cfs_div.hide();
        self.parent.cfs_div.siblings('.media.button.remove').show();
        self.parent.cfs_div.siblings('.file_url').html('<?php echo $attachment['url']; ?>');
        self.parent.cfs_div.siblings('.file').val('<?php echo $id; ?>');
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
        (function($) {
            $(function() {
                $('.cfs_input .media.button.add').live('click', function() {
                    window.cfs_div = $(this);
                    tb_show('Attach file', 'media-upload.php?post_id=<?php echo $post->ID; ?>&cfs_file=1&TB_iframe=1&width=640&height=480');
                    return false;
                });
                $('.cfs_input .media.button.remove').live('click', function() {
                    $(this).siblings('.file_url').html('');
                    $(this).siblings('.file').val('');
                    $(this).siblings('.media.button.add').show();
                    $(this).hide();
                });
            });
        })(jQuery);
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
