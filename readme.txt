=== Custom Field Suite ===
Contributors: logikal16
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=JMVGK3L35X6BU
Tags: fields, custom fields, pods, cck, more fields, extra fields, gravity forms
Requires at least: 3.2
Tested up to: 3.3.1
Stable tag: trunk

Custom Field Suite is the easiest way to visually manage custom fields.

== Description ==

Visually create custom fields that can be used on any edit page.

Now with [Gravity Forms integration!](http://uproot.us/custom-field-suite/documentation/gravity-forms-integration/)

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
* http://uproot.us/forums/


== Installation ==

1. Download and activate the plugin.
2. Browse to `Settings > Custom Field Suite` to configure.


== Screenshots ==
1. A custom field group, with several fields added.


== Changelog ==

= 1.3.3 =
* Upgraded chosen.js
* Added get_labels() API method
* Bugfix: Javascript issues for some fields within loop (wysiwyg, date, relationship)

= 1.3.2 =
* Bugfix: in some cases, the "User Roles" placement rule prevented values from displaying
* Bugfix: only published field groups should appear on edit pages

= 1.3.1 =
* Added private posts to relationship field

= 1.3.0 =
* Gravity Forms integration!
* Better error handling for the API save() method

= 1.2.6 =
* Bugfix: issue with taxonomy rules / post_format (props Hylkep)
* Changed default date format to yyyy-mm-dd

= 1.2.5 =
* Bugfix: editor issues with WP 3.3
* Updated translation file
* CFS post type excluded from rules list (props @thecorkboard)
* Minor code cleanup

= 1.2.4 =
* Bugfix: editor not appearing when adding new posts

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
