<link rel="stylesheet" type="text/css" href="<?php echo $this->url; ?>/css/screen_extra.css" />
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
                    <p><?php _e('See updates for', 'cfs'); ?> <a class="thickbox" href="<?php bloginfo('url'); ?>/wp-admin/plugin-install.php?tab=plugin-information&plugin=custom-field-suite&section=changelog&TB_iframe=1&width=640&height=480">v<?php echo $this->version; ?></a></p>
                </div>
                <div class="field">
                    <h4>Getting Started?</h4>
                    <p>
                        <a href="http://uproot.us/custom-field-suite/documentation/" target="_blank">Documentation</a> |
                        <a href="http://uproot.us/support/" target="_blank">Support</a> |
                        <a href="http://uproot.us/custom-field-suite/roadmap/" target="_blank">Roadmap</a>
                    </p>
                </div>
                <div class="field">
                    <h4><?php _e('Developed by', 'cfs'); ?> Matt Gibbs</h4>
                    <p>
                        <a href="http://twitter.com/logikal16/" target="_blank">Twitter</a> |
                        <a href="http://wordpress.org/extend/plugins/custom-field-suite/" target="_blank">Vote for this plugin</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
