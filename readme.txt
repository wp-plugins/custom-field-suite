=== Custom Field Suite ===
Contributors: logikal16
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=JMVGK3L35X6BU
Tags: custom, fields, custom fields, cck, post types, gravity forms, field permissions
Requires at least: 3.2
Tested up to: 3.3.1
Stable tag: trunk

Custom Field Suite is the easiest way to visually manage custom fields.

== Description ==

Use Custom Field Suite to add custom fields to your WordPress edit screens.

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

= More Features =
* Customize where each field group will appear
* [Create your own field types](http://uproot.us/custom-field-suite/docs/custom-field-type/)
* [Gravity Forms integration](http://uproot.us/custom-field-suite/docs/gravity-forms-integration/)
* Custom Field Import (migrate existing custom fields into CFS)

= Documentation and Support =
* http://uproot.us/
* http://uproot.us/forums/


== Installation ==

1. Download and activate the plugin.
2. Browse to `Settings > Custom Field Suite` to configure.


== Screenshots ==
1. A custom field group, with several fields added.


== Changelog ==

= 1.4.1 =
* Bugfix: wysiwyg field breaks if editor defaults to HTML tab

= 1.4.0 =
* Ability to select private posts in placement rules (props @jevets)

= 1.3.9 =
* Updated translation file
* Cleaned up PHP notices

= 1.3.8 =
* Bugfix: custom translation file path incorrect

= 1.3.7 =
* Bugfix: gravity form data not saving to correct post type

= 1.3.6 =
* Added thumbnail for uploaded images
* Bugfix: loop not displaying properly when saving first 2+ rows
* Bugfix: wysiwyg field not loading when adding dynamically within loop

= 1.3.5 =
* Bugfix: rare bug with relationship select boxes
* Bugfix: private posts now appear within Placement Rules
* Bugfix: prevent "navigate away from page" box on save
* Upload button appears as "Attach File" instead of "Insert into Post"

= 1.3.4 =
* Added custom field import / mapping script

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