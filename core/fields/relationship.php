<?php

class cfs_Relationship extends cfs_Field
{

    function __construct($parent)
    {
        $this->name = 'relationship';
        $this->label = __('Relationship', 'cfs');
        $this->parent = $parent;
    }

    function html($field)
    {
        global $wpdb;

        $where = '';
        $selected_posts = array();
        $available_posts = array();

        // Limit to chosen post types
        if (isset($field->options['post_types']))
        {
            $where = array();
            foreach ($field->options['post_types'] as $type)
            {
                $where[] = $type;
            }
            $where = " AND post_type IN ('" . implode("','", $where) . "')";
        }


        $results = $wpdb->get_results("SELECT ID, post_type, post_status, post_title FROM $wpdb->posts WHERE post_status IN ('publish','private') $where ORDER BY post_title");
        foreach ($results as $result)
        {
            $result->post_title = ('private' == $result->post_status) ? '(Private) ' . $result->post_title : $result->post_title;
            $available_posts[] = $result;
        }

        if (!empty($field->value))
        {
            $results = $wpdb->get_results("SELECT ID, post_status, post_title FROM $wpdb->posts WHERE ID IN ($field->value) ORDER BY FIELD(ID,$field->value)");
            foreach ($results as $result)
            {
                $result->post_title = ('private' == $result->post_status) ? '(Private) ' . $result->post_title : $result->post_title;
                $selected_posts[$result->ID] = $result;
            }
        }
    ?>
        <div class="filter_posts">
            <input type="text" class="cfs_filter_input" autocomplete="off" />
            <div class="cfs_filter_help">
                <div class="cfs_help_text hidden">
                    <ul>
                        <li style="font-size:15px; font-weight:bold">Sample queries</li>
                        <li>"foobar" (find posts containing "foobar")</li>
                        <li>"type:page" (find pages)</li>
                        <li>"type:page foobar" (find pages containing "foobar")</li>
                        <li>"type:page,post foobar" (find posts or pages with "foobar")</li>
                        <li></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="available_posts post_list">
        <?php foreach ($available_posts as $post) : ?>
            <?php $class = (isset($selected_posts[$post->ID])) ? ' class="used"' : ''; ?>
            <div rel="<?php echo $post->ID; ?>" post_type="<?php echo $post->post_type; ?>"<?php echo $class; ?>><?php echo $post->post_title; ?></div>
        <?php endforeach; ?>
        </div>

        <div class="selected_posts post_list">
        <?php foreach ($selected_posts as $post) : ?>
            <div rel="<?php echo $post->ID; ?>"><span class="remove"></span><?php echo $post->post_title; ?></div>
        <?php endforeach; ?>
        </div>
        <div class="clear"></div>
        <input type="hidden" name="<?php echo $field->input_name; ?>" class="<?php echo $field->input_class; ?>" value="<?php echo $field->value; ?>" />
    <?php
    }

    function options_html($key, $field)
    {
        $post_types = isset($field->options['post_types']) ? $field->options['post_types'] : null;
        $choices = get_post_types(array('exclude_from_search' => false));
        $choices = implode("\n", $choices);
    ?>
        <tr class="field_option field_option_<?php echo $this->name; ?>">
            <td class="label">
                <label><?php _e('Post Types', 'cfs'); ?></label>
                <p class="description"><?php _e('Limit posts to the following types', 'cfs'); ?></p>
            </td>
            <td>
                <?php
                    $this->parent->create_field((object) array(
                        'type' => 'select',
                        'input_name' => "cfs[fields][$key][options][post_types]",
                        'input_class' => '',
                        'options' => array('choices' => $choices, 'multiple' => '1'),
                        'value' => $post_types,
                    ));
                ?>
            </td>
        </tr>
    <?php
    }

    function input_head($field = null)
    {
    ?>
        <link rel="stylesheet" type="text/css" href="<?php echo $this->parent->url; ?>/js/tipTip/tipTip.css" />
        <script type="text/javascript" src="<?php echo $this->parent->url; ?>/js/tipTip/jquery.tipTip.js"></script>
        <script type="text/javascript">
        function update_relationship_values(field) {
            var post_ids = [];
            field.find(".selected_posts div").each(function(idx) {
                post_ids[idx] = jQuery(this).attr("rel");
            });
            field.find("input.relationship").val(post_ids.join(","));
        }

        jQuery(function() {

            // tooltip
            jQuery(".cfs_filter_help").tipTip({
                maxWidth: "400px",
                content: jQuery(this).find(".cfs_help_text").html()
            });

            // sortable
            jQuery(".cfs_relationship .selected_posts").sortable({
                axis: "y",
                update: function(event, ui) {
                    var parent = jQuery(this).closest(".field");
                    update_relationship_values(parent);
                }
            });

            // add selected post
            jQuery(".cfs_relationship .available_posts div").click(function() {
                var div = jQuery(this);
                var parent = div.closest(".field");
                var post_id = div.attr("rel");
                var html = div.html();
                div.addClass("used");
                parent.find(".selected_posts").append('<div rel="'+post_id+'"><span class="remove"></span>'+html+'</div>');
                update_relationship_values(parent);
            });

            // remove selected post
            jQuery(".cfs_relationship .selected_posts span.remove").live("click", function() {
                var div = jQuery(this).parent();
                var parent = div.closest(".field");
                var post_id = div.attr("rel");
                parent.find(".available_posts div[rel="+post_id+"]").removeClass("used");
                div.remove();
                update_relationship_values(parent);
            });

            // filter available posts
            jQuery(".cfs_filter_input").keyup(function() {

                var input = jQuery(this).val();
                var output = { types: [], keywords: [] };
                var pieces = output.keywords = input.split(" ");
                var parent = jQuery(this).closest(".field");

                for (i in pieces) {
                    var piece = pieces[i];
                    if ("type:" == piece.substr(0, 5)) {
                        output.types = piece.substr(5);
                        if (output.types.indexOf(",") !== -1) {
                            output.types = output.types.split(",");
                        }
                        else {
                            output.types = [output.types];
                        }
                        output.keywords.splice(i, 1);
                    }
                }
                output.keywords = output.keywords.join(" ");

                var regex = new RegExp(output.keywords, "i");

                parent.find(".available_posts div:not(.used)").each(function() {

                    var div = jQuery(this);
                    var post_type = div.attr("post_type");

                    if (output.types.length > 0 && jQuery.inArray(post_type, output.types) < 0) {
                        div.addClass("hidden");
                        return;
                    }

                    if (-1 < div.html().search(regex)) {
                        div.removeClass("hidden");
                    }
                    else {
                        div.addClass("hidden");
                    }
                });
            });
        });
        </script>
    <?php
    }

    function format_value_for_api($value)
    {
        $return = false;

        if (!empty($value[0]))
        {
            if (false !== strpos($value[0], ','))
            {
                $return = array();
                $value = explode(',', $value[0]);
                foreach ($value as $v)
                {
                    $return[] = $v;
                }
            }
            else
            {
                $return = array($value[0]);
            }
        }

        return $return;
    }
}
