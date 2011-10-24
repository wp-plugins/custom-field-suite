=== Custom Field Suite ===
Contributors: logikal16
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=JMVGK3L35X6BU
Tags: fields, custom fields, pods, cck, more fields, extra fields, advanced custom fields
Requires at least: 3.2
Tested up to: 3.2.1
Stable tag: trunk

Custom Field Suite is the easiest way to visually manage custom fields.

== Description ==

Visually create groups of custom fields that can be displayed on any edit page.

* Easily create groups of fields
* Assign edit pages that a field group should appear on
* Customize the field group layout (via drag-n-drop) on edit pages
* Create your own field types using the `cfs_field_types` plugin hook!

= Field Types =
* Text (api returns text)
* Textarea (api returns text with `<br />`)
* Wysiwyg Editor (api returns html)
* Date (api returns text)
* True / False (api returns boolean)
* Select (api returns array of values)
* Relationship (api returns array of post IDs)
* File Upload (api returns file url)
* Loop (repeatable fields!)

= API =
The plugin includes a friendly API for displaying a post's field data.

* Get all fields: `$fields = $cfs->get();`
* Get a single field: `$field = $cfs->get('my_field_name');`

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

= 1.0.8 =
* Bugfix: File field interfering with WP editor uploader

= 1.0.7 =
* Bugfix: upgrade script
* Bugfix: "Add New Field" not working

= 1.0.6 =
* Custom field types now extend cfs_Field class
* Bugfix: white screen with File Upload field
* Added screenshot

= 1.0.5 =
* Bugfix: wysiwyg hyperlink button not working

= 1.0.4 =
* Bugfix: slashes being added before quotes
* Added Documentation links to overview page

= 1.0.3 =
* Code cleanup
* Bugfix: resolved several PHP notices
* Bugfix: relationship with 1 value would return null (thx Monika)
* Bugfix: file upload field now shows "Gallery" tab if available
* Improved CSS styling

= 1.0.2 =
* Bugfix: renaming a Loop field would also rename subfields
* Bugfix: relationship field showing ALL db posts (thx Monika)

= 1.0.1 =
* Bugfix: default values were not appearing

= 1.0.0 =
* Custom Field Suite.
