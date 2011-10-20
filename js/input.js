jQuery(function() {

    // Remove a loop row
    jQuery(".cfs_loop td.remove span").live("click", function() {
        jQuery(this).closest("table").remove();
    });

    // Add a new loop row
    jQuery(".cfs_add_field").click(function() {
        var parent = jQuery(this).closest(".table_footer").siblings(".loop_wrapper");
        var count = parent.find("input.row_count");
        var html = parent.find(".input_clone").html().replace(/\[clone\]/g, "["+count.val()+"]");
        count.val(parseInt(count.val()) + 1);
        parent.append(html);
    });

    // Remove clone fields on save
    jQuery("#publish").click(function() {
        jQuery(".loop_wrapper .input_clone").remove();
    });
});