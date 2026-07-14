=== atshift Fields (Maintenance for Custom Field Suite) ===
Contributors: mgibbs189, atshift
Tags: custom fields, postmeta, relationship, repeater, fields
Requires at least: 5.0
Tested up to: 7.0
Stable tag: 2.6.7.45.3
License: GPLv2
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html

Add custom fields to posts, pages, and custom post types.

== Description ==

Custom Field Suite (CFS) is a lightweight WordPress plugin for adding custom fields to posts, pages, and custom post types.

This package is a maintenance build based on the upstream Custom Field Suite 2.6.7 release. It keeps the basic CFS data structure and API compatibility while adding security hardening, admin compatibility fixes, and practical field types for existing CFS sites.

For setup instructions, field value output examples, migration notes, and implementation examples, see the [atshift Fields documentation site](https://cfs.at-shift.net/en/).

= Things to know =

* This is a maintenance build, not an official upstream release.
* Always back up your files and database before replacing an existing CFS installation.
* Deactivate the original Custom Field Suite plugin before activating this maintenance build.
* Test on a local or staging site before using it on a production site.
* CFS returns stored values; theme templates should still escape output with WordPress escaping functions.
* Added field types are not available in the original upstream CFS 2.6.7 release.

= Field types =

* Text
* Textarea
* WYSIWYG
* Tab Group
* Loop (repeatable fields)
* Horizontal Group
* Accordion Group (collapsible group)
* Conditional Group
* Phone Number
* Email Address
* Hyperlink
* URL
* Number
* Select
* Checkbox
* True / False
* Radio Button
* Date
* Time
* File Upload
* Photo Gallery
* Color
* Post Title (native WordPress title)
* Post Content (native WordPress content)
* Save / Publish (native WordPress save and publish controls)
* Post Categories (Standard / Global)
* Post Tags (Native)
* Featured Image (Native)
* Term
* Relationship
* User

= Added features in this maintenance build =

* Security hardening for known CFS 2.6.7 vulnerability classes.
* PHP 8.2+ and WordPress admin compatibility fixes.
* Checkbox and Radio Button fields.
* Phone Number, Email Address, Number, URL, and Time fields with format validation.
* Time field with hour and minute select menus.
* Photo Gallery field with sortable media selection and gallery-friendly output data.
* Native WordPress Post Title, Post Content, Save / Publish, Standard and Shared Taxonomy Post Categories, Post Tags, and Featured Image fields inside CFS field groups.
* Horizontal Group field for arranging multiple fields side by side, with evenly distributed and left-aligned layout options.
* Accordion Group field for organizing child fields into collapsible sections on post edit screens.
* Field Group editor buttons to add a new field directly below an existing field or inside a Loop, Horizontal Group, or Accordion Group.
* Color-coded structure badges and matching range backgrounds for Tabs, Loops, Horizontal Groups, and Accordion Groups.
* Improved drag-and-drop and Tab boundary handling in the Field Group editor.
* Inline validation messages and an error summary that opens the containing Tab, Loop, or Accordion Group and scrolls to the selected invalid field.
* Field type list ordering grouped by common editing workflows.
* Field Group parent / child synchronization to reduce cases where nested fields disappear from the post edit screen.
* Placement rule warnings for field groups that have no placement rules.
* Configurable placeholders for Text, Phone Number, Email Address, Hyperlink, and URL fields.

= Usage =

* Browse to the "Field Groups" admin menu.
* Create a Field Group containing one or more custom fields.
* Choose where the Field Group should appear, using the Placement Rules box.
* Use the CFS get method in your theme templates to display custom field values.

Example:

`<?php echo esc_html( CFS()->get( 'my_text_field' ) ); ?>`

For rich text fields, use an appropriate HTML sanitizer such as `wp_kses_post()`.

= Horizontal Group notes =

Horizontal Group is a layout field for placing multiple child fields side by side on the post edit screen. On narrow screens, the fields stack vertically.

Horizontal Groups are intended to contain multiple normal fields. Tabs, Loops, Accordions, Conditional Groups, and other Horizontal Groups cannot be placed inside a Horizontal Group.

= Accordion Group notes =

Accordion Group is a layout field for placing child fields inside a collapsible section on the post edit screen. It can be configured to open by default.

Tabs cannot be placed inside an Accordion Group.

= WordPress native field notes =

Post Title, Post Content, Save / Publish, Post Categories, Post Tags, and Featured Image edit the native WordPress objects directly. They are not CFS-only post meta fields.

For Post Categories, child category selection can also select parent categories, and removing a parent selection removes its child selections. If all categories are removed, WordPress' default category is restored.

= Security maintenance notes =

This package includes local security and compatibility hardening on top of the upstream 2.6.7 codebase.

The maintenance work addresses known vulnerability classes around Loop field code execution, Term field SQL injection, CFS form title / content stored XSS, and existing post updates through CFS forms without normal edit capability checks.

The changes were verified locally against the built-in CFS field types, added field types, and an upgrade path from the original 2.6.7 codebase. This package has also been adjusted for the WordPress.org plugin directory and published through the official WordPress.org SVN release flow. WordPress.org publication is not a substitute for an independent third-party security audit.

= Redistribution and license =

This maintenance build is distributed under the GNU General Public License version 2 (GPLv2), the same license as the upstream plugin. You may use, copy, modify, and redistribute this package, including modified versions, under GPLv2.

When redistributing this package, keep the GPLv2 license notice, preserve the original author attribution, include the source code, and make clear that this is a maintenance build inherited by @shift Yoshiya Tsuchisaka.

= Documentation and support =

* [Documentation site](https://cfs.at-shift.net/en/)
* [Field group setup guide](https://cfs.at-shift.net/en/guide/)
* [Field value output reference](https://cfs.at-shift.net/en/output/)
* [Implementation examples](https://cfs.at-shift.net/en/examples/)
* [Development repository](https://github.com/at-shift/at-shift-cfs)
* [Original Custom Field Suite source](https://github.com/mgibbs189/custom-field-suite)

== Changelog ==

= 2.6.7.45.3 =
* Added Post Content (Native) for editing native WordPress content from Field Groups.

= 2.6.7.45.2 =
* Improved automatic field names for group and native WordPress fields.
* Added required validation to Post Categories and role-controlled Move to Trash support to Save / Publish.

= 2.6.7.45.1 =
* Refined Save / Publish field status behavior and labels.
* Fixed the Field Group editor update button state after duplicate field name validation.

= 2.6.7.45 =
* Added native WordPress Post Title and Save / Publish fields.
* Improved Gutenberg / Block Editor and classic meta box compatibility.
* Improved validation error visibility across Tabs, Loops, and grouped fields.

= 2.6.7.44.0.1 =
* Added an after-input helper text setting for supported fields.
* Improved field setting labels, tooltips, placeholders, and multilingual translations.
* Removed default value settings from Email Address, Phone Number, and URL fields.
