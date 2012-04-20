(function($) {
    $(function() {
        function update_order() {
            // toggle active classes
        }

        update_order();

        // Sortable
        $('.fields').sortable({
            axis: 'y',
            handle: 'td.field_order',
            update: function(event, ui) { update_order(); }
        });

        // Select box enhancement
        $('.chosen-select').chosen();

        // Add a new field
        $('.cfs_add_field').live('click', function() {
            var parent = $(this).closest('.table_footer').siblings('.fields');
            var html = $('.field_clone').html().replace(/\[clone\]/g, '['+field_index+']');

            if ($(this).hasClass('cfs_add_sub_field')) {
                html = html.replace(/{sub_field}/g, '1');
            }

            parent.append(html);
            parent.find('.field:last .field_label a.cfs_edit_field').click();
            parent.find('.field:last .cfs_input .field_type select').change();
            field_index = field_index + 1;

            // Remove the "loop" type if already within a loop
            if ($(this).hasClass('cfs_add_sub_field')) {
                parent.find('.field:last .cfs_input .field_type option[value="loop"]').remove();
            }
        });

        // Delete a field
        $('.cfs_delete_field').live('click', function() {
            $(this).closest('.field').remove();
        });

        // Pop open the edit fields
        $('.cfs_edit_field').live('click', function() {
            var field = $(this).closest('.field');
            field.toggleClass('form_open');
            field.find('.field_form_mask:first').animate({height: 'toggle'}, 500);
        });

        // Add or replace field_type options
        $('.cfs_input .field_type select').live('change', function() {
            var type = $(this).val();
            var input_name = $(this).attr('name').replace('[type]', '');
            var html = options_html[type].replace(/cfs\[fields\]\[clone\]/g, input_name);
            $(this).closest('.field').find('td.field_type').html(type);
            $(this).closest('.cfs_input').find('.field_option').remove();
            $(this).closest('.field_type').after(html);
        });

        // Auto-populate the field name
        $('.cfs_input tr.field_label input').live('blur', function() {
            var label_text = $(this).val();
            var name = $(this).closest('tr').siblings('tr.field_name').find('input');
            if ('' == name.val()) {
                var val = label_text.replace(/\s/g, '_');
                val = val.replace(/[^a-zA-Z0-9_]/g, '');
                name.val(val.toLowerCase());
                name.trigger('keyup');
            }
        });

        $('.cfs_input tr.field_label input').live('keyup', function() {
            var val = $(this).val();
            $(this).closest('.field').find('td.field_label:first a').html(val + '&nbsp;');
        });

        $('.cfs_input tr.field_name input').live('keyup', function() {
            var val = jQuery(this).val();
            $(this).closest('.field').find('td.field_name:first').html(val);
        });
    });
})(jQuery);
