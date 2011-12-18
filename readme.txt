=== Custom Field Suite ===
Contributors: logikal16
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=JMVGK3L35X6BU
Tags: fields, custom fields, pods, cck, more fields, extra fields, advanced custom fields
Requires at least: 3.2
Tested up to: 3.3
Stable tag: trunk

Custom Field Suite is the easiest way to visually manage custom fields.

== Description ==

Visually create custom fields that can be used on any edit page.

= Field Types =
* Text (api returns text)
* Textarea (api returns text with `<br />`)
* Wysiwyg Editor (api returns html)
* Date (api returns text)
* True / False (api returns 0 or 1)
* Select (api returns array of values)
* Relationship (api returns array of post IDs)
* File Upload (api returns file url)
* Loop (repeatable fields!)
* [Custom field types](http://uproot.us/custom-field-suite/documentation/custom-field-type/)

= Documentation and Support =
* http://uproot.us/custom-field-suite/
* http://uproot.us/custom-field-suite/documentation/
* http://uproot.us/support/


== Installation ==

1. Upload 'custom-field-suite' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Click on Settings -> Custom Field Suite


== Screenshots ==
1. A custom field group, with several fields added.


== Changelog ==

= 1.2.3 =
* Bugfix: rule matching not working correctly

= 1.2.2 =
* Bugfix: unable to add new field group (props @brewern)
* Bugfix: file fields not working without post type "editor" support
* Updated timepicker script

= 1.2.1 =
* Excluded tags from taxonomy rules list

= 1.2.0 =
* New placement rule: User Role
* New placement rule: Taxonomy Term
* New placement rule: Post ID
* Removed cfs_rules table in favor of postmeta
* WP 3.3 compatibility

= 1.1.5 =
* Bugfix: unselected checkbox not saving

= 1.1.4 =
* Added $cfs->save(), an alias of $cfs->api->save_fields()

= 1.1.3 =
* New API function: save_fields!
* Bugfix: minor select box saving issue
* Please BACK UP YOUR DATABASE before upgrading

= 1.1.2 =
* Bugfix: cannot edit fields without a label

= 1.1.1 =
* Added support for Select field label (VA : Virginia)
* Added translation support

= 1.1.0 =
* Bugfix: true_false field not outputting false
* true/false field now returns an INTEGER (1 or 0)
