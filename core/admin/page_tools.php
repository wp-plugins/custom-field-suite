<?php

global $wpdb;

$sql = "
SELECT ID, post_title
FROM $wpdb->posts
WHERE post_type = 'cfs' AND post_status = 'publish'
ORDER BY post_title";
$results = $wpdb->get_results($sql);
?>

<style type="text/css">
.content-container { padding-top: 15px; }
.nav-tab { cursor: pointer; }
.tab-content { display: none; }
.tab-content.active { display: block; }
#button-export, #button-sync { margin-top: 4px; }
#icon-edit { background: url(<?php echo $this->url; ?>/images/logo.png) no-repeat; }
</style>

<script>
(function($) {
    $(function() {
        $('.nav-tab').click(function() {
            $('.tab-content').removeClass('active');
            $('.nav-tab').removeClass('nav-tab-active');
            $('.tab-content.' + $(this).attr('rel')).addClass('active');
            $(this).addClass('nav-tab-active');
        });

        $('#button-export').click(function() {
            var groups = $('#export-field-groups').val();
            if (null != groups) {
                $.post(ajaxurl, {
                    action: 'cfs_ajax_handler',
                    action_type: 'export',
                    field_groups: $('#export-field-groups').val()
                },
                function(response) {
                    $('#export-output').text(response);
                    $('#export-area').show();
                });
            }
        });

        $('#button-import').click(function() {
            $.post(ajaxurl, {
                action: 'cfs_ajax_handler',
                action_type: 'import',
                import_code: $('#import-code').val()
            },
            function(response) {
                $('#import-message').html(response);
            });
        });

        $('#button-sync').click(function() {
            var groups = $('#sync-field-groups').val();
            if (null != groups) {
                $.post(ajaxurl, {
                    action: 'cfs_ajax_handler',
                    action_type: 'sync',
                    field_groups: $('#sync-field-groups').val()
                },
                function(response) {
                    $('#sync-message').html(response);
                });
            }
        });
    });
})(jQuery);
</script>

<div class="wrap">
    <div id="icon-edit" class="icon32"><br></div>
    <h2 class="nav-tab-wrapper">
        <a class="nav-tab nav-tab-active" rel="export"><?php _e('Export', 'cfs'); ?></a>
        <a class="nav-tab" rel="import"><?php _e('Import', 'cfs'); ?></a>
        <a class="nav-tab" rel="sync"><?php _e('Synchronize', 'cfs'); ?></a>
        <a class="nav-tab" rel="debug"><?php _e('Debug Information', 'cfs'); ?></a>
    </h2>

    <div class="content-container">

        <!-- Export -->

        <div class="tab-content export active">
            <h2><?php _e('Which field groups would you like to export?', 'cfs'); ?></h2>
            <table>
                <tr>
                    <td style="width:300px; vertical-align:top">
                        <div>
                            <select id="export-field-groups" style="width:300px; height:200px" multiple="multiple">
                                <?php foreach ($results as $result) : ?>
                                <option value="<?php echo $result->ID; ?>"><?php echo $result->post_title; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <input type="button" id="button-export" class="button" value="Export" />
                        </div>
                    </td>
                    <td style="width:300px; vertical-align:top">
                        <div id="export-area" style="display:none">
                            <div>
                                <textarea id="export-output" style="width:98%; height:200px"></textarea>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Import -->

        <div class="tab-content import">
            <h2><?php _e('Paste the import code below. Existing field groups will be skipped.', 'cfs'); ?></h2>
            <table>
                <tr>
                    <td style="width:300px; vertical-align:top">
                        <div>
                            <textarea id="import-code" style="width:98%; height:200px"></textarea>
                        </div>
                        <div>
                            <input type="button" id="button-import" class="button" value="Import" />
                        </div>
                    </td>
                    <td style="width:300px; vertical-align:top">
                        <div id="import-message"></div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Synchronize -->

        <div class="tab-content sync">
            <h2><?php _e('CFS will attempt to import custom field values.', 'cfs'); ?></h2>
            <table>
                <tr>
                    <td style="width:300px; vertical-align:top">
                        <div>
                            <select id="sync-field-groups" style="width:300px; height:200px" multiple="multiple">
                                <?php foreach ($results as $result) : ?>
                                <option value="<?php echo $result->ID; ?>"><?php echo $result->post_title; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <input type="button" id="button-sync" class="button" value="Synchronize" />
                        </div>
                    </td>
                    <td style="width:300px; vertical-align:top">
                        <div id="sync-message"></div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Debug Information -->

        <div class="tab-content debug">
            <h2><?php _e('Detailed information about your site:'); ?></h2>
<textarea style="width:600px; height:200px">
WordPress <?php global $wp_version; echo $wp_version; ?>

PHP <?php echo phpversion(); ?>

<?php echo $_SERVER['SERVER_SOFTWARE']; ?>

<?php echo $_SERVER['HTTP_USER_AGENT']; ?>


-- Active Plugins --
<?php
$all_plugins = get_plugins();
foreach ($all_plugins as $plugin_file => $plugin_data) {
    if (is_plugin_active($plugin_file)) {
        echo $plugin_data['Name'] . ' ' . $plugin_data['Version'] . "\n";
    }
}
?>
</textarea>
        </div>
    </div>
</div>
