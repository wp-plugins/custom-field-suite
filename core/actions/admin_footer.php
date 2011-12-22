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
            <h3 class="hndle"><span><?php _e('Custom Field Suite', 'cfs'); ?> <?php echo $this->version; ?></span></h3>
            <div class="inside">
                <div class="field">
                    <h4><?php _e('Changelog', 'cfs'); ?></h4>
                    <p><?php _e('See updates for', 'cfs'); ?> <a class="thickbox" href="<?php bloginfo('url'); ?>/wp-admin/plugin-install.php?tab=plugin-information&plugin=custom-field-suite&section=changelog&TB_iframe=1&width=640&height=480">v<?php echo $this->version; ?></a></p>
                </div>
                <div class="field">
                    <h4>Getting started?</h4>
                    <p>
                        <a href="http://uproot.us/custom-field-suite/" target="_blank">View the plugin website</a>
                    </p>
                </div>
                <div class="field">
                    <h4><?php _e('Please show your support!', 'cfs'); ?></h4>
                    <p>
                        <a href="http://wordpress.org/extend/plugins/custom-field-suite/" target="_blank">Rate the plugin</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
