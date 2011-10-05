<link rel="stylesheet" type="text/css" href="<?php echo $this->url; ?>/css/style.screen_extra.css" />
<script type="text/javascript">
jQuery(function() {
    jQuery(".wp-list-table").before(jQuery("#posts-sidebar-box").html());
});
</script>

<div id="posts-sidebar-box" class="hidden">
    <div class="posts-sidebar" id="poststuff">
        <div class="postbox">
            <div class="handlediv"><br></div>
            <h3 class="hndle"><span><?php _e('Custom Field Suite', 'cfs'); ?> v<?php echo $this->version; ?></span></h3>
            <div class="inside">
                <div class="field">
                    <h4><?php _e('Changelog', 'cfs'); ?></h4>
                    <p><?php _e('See updates for', 'cfs'); ?> <a class="thickbox" href="<?php bloginfo('url'); ?>/wp-admin/plugin-install.php?tab=plugin-information&plugin=custom-field-suite&section=changelog&TB_iframe=true&width=640&height=559">v<?php echo $this->version; ?></a></p>
                </div>
                <div class="field">
                    <h4><?php _e('Developed by', 'cfs'); ?> Matt Gibbs</h4>
                    <p>
                        <a href="http://wordpress.org/extend/plugins/custom-field-suite/" target="_blank">Vote for CFS</a> |
                        <a href="http://twitter.com/logikal16/" target="_blank">Twitter</a>
                    </p>
                </div>
                <div class="field">
                    <p>
                        Adapted from Elliot Condon's wonderful <a href="http://wordpress.org/extend/plugins/advanced-custom-fields/" target="_blank">Advanced Custom Fields</a> plugin.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
