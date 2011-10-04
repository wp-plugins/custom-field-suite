=== Custom Field Suite ===
Contributors: logikal16
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=E75CAQ74KG3ZU
Tags: fields, custom fields, pods, cck, more fields, extra fields, advanced custom fields
Requires at least: 3.2
Tested up to: 3.2.1
Stable tag: trunk

Custom Field Suite is the easiest way to create custom fields.

== Description ==

Visually create groups of custom fields that can be displayed on any edit page.

* Easily create groups of fields
* Assign edit pages that a field group should appear on
* Customize the field group layout (via drag-n-drop) on edit pages
* Create your own field types using the `cfs_field_types` hook!

= Field Types =
* Text (api returns text)
* Textarea (api returns text with `<br />`)
* Wysiwyg Editor (api returns html)
* Date (api returns text)
* True / False (api returns boolean)
* Select (api returns array of values)
* Relationship (api returns array of post objects)
* File Upload (api returns file url)
* Loop (repeatable fields!)

= API =
The plugin includes a friendly API for displaying a post's field data.

* Get all fields: $fields = $cfs->api->get_fields();
* Get a single field: $field = $cfs->api->get_field('my_field_name');

= Website =
http://uproot.us/custom-field-suite/

= Please Vote and Enjoy =
Your votes really make a difference! Thanks.


== Installation ==

1. Upload 'custom-field-suite' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Click on Settings -> Custom Field Suite


== Changelog ==

= 1.0.0 =
* Custom Field Suite.
