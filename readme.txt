=== atshift Fields (Maintenance for Custom Field Suite) ===
Contributors: mgibbs189, atshift
Tags: custom fields, postmeta, relationship, repeater, fields
Requires at least: 5.0
Tested up to: 7.0
Stable tag: 3.0.2
License: GPLv2
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html

Add custom fields to posts, pages, and custom post types.

== Description ==

atshift Fields is a maintenance build of Custom Field Suite for WordPress sites that rely on structured fields and need clearer, safer editing screens. It helps organize complex editing screens with Tab Groups, Horizontal Groups, Accordion Groups, Conditional Groups, and Loops.

Version 3 and later focus on practical field management for content-heavy sites. The Field Group editor has been redesigned for nested groups, drag-and-drop editing, responsive layouts, and multilingual admin labels. Conditional Groups can provide condition-specific areas for relevant child fields, while Tab Groups help split large field sets into manageable sections. It also adds native WordPress fields, including post title, content, save and publish controls, categories, tags, featured image, and post editing screen display controls.

v3 also adds Code View, Shortcode output through `CFS()->get()`, and role-aware controls for editing sensitive fields or hiding unnecessary native editor sections.

The plugin keeps compatibility with the original Custom Field Suite API and saved data while adding security hardening, admin compatibility fixes, and practical field types for existing CFS sites.

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
* Code View
* Shortcode
* Classic Meta Box Placement
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

= Tab Group notes =

Tab Group is a layout field for splitting fields on the post edit screen into tabbed sections.

Place a Tab Group before the fields that should appear in that tab. Another Tab Group starts another tab section.

= Horizontal Group notes =

Horizontal Group is a layout field for placing multiple child fields side by side on the post edit screen. On narrow screens, the fields stack vertically.

Horizontal Groups are intended to contain multiple normal fields. Tabs, Loops, Accordions, Conditional Groups, and other Horizontal Groups cannot be placed inside a Horizontal Group.

= Accordion Group notes =

Accordion Group is a layout field for placing child fields inside a collapsible section on the post edit screen. It can be configured to open by default.

Tabs cannot be placed inside an Accordion Group.

= Conditional Group notes =

Conditional Group is a layout field for displaying different child fields depending on a selected radio button or dropdown choice on the post edit screen.

Child fields can be assigned to condition-specific areas in the Field Group editor. Tabs and other Conditional Groups cannot be placed inside a Conditional Group.

= Classic Meta Box Placement notes =

Classic Meta Box Placement is a slot field for moving classic third-party meta boxes into a chosen position inside an atshift Fields field group. It does not save CFS field values; saving, nonces, permissions, scripts, and styles remain the responsibility of the original meta box plugin.

Block Editor-only panels are not supported. Native WordPress meta boxes and internal CFS meta boxes are shown as not recommended to move.

The original meta box header is hidden after placement because the atshift Fields field label and notes provide the heading. Display width can be set to side width (320px), 50%, 75%, or 100%; narrow screens always use 100% width.

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

== Installation ==

1. Back up your files and database before replacing an existing Custom Field Suite installation.
2. Deactivate the original Custom Field Suite plugin before activating this maintenance build.
3. Install and activate atshift Fields from the WordPress plugin directory, or upload the plugin folder to `/wp-content/plugins/`.
4. Open the atshift Fields / Field Groups admin screen.
5. Create a Field Group, add fields, and set Placement Rules for the edit screens where it should appear.
6. Use `CFS()->get()` in your theme templates to display saved field values.

For detailed guides and examples, see the [documentation site](https://cfs.at-shift.net/en/).

== Screenshots ==

1. Field Group editor with the field type selector and native WordPress field types.
2. Nested field groups using tabs, accordions, conditional groups, loops, and horizontal groups.
3. Dedicated field group inputs displayed on the WordPress page editing screen.
4. Placement rules for post types, post formats, user roles, posts, taxonomy terms, and page templates.
5. Extra display settings for edit-screen layout, native section visibility, and role-based hiding behavior.

== Frequently Asked Questions ==

= Is this the official successor to Custom Field Suite? =

No. atshift Fields is an independent, unofficial compatible maintenance and extension build distributed under GPLv2.

= Can I replace the original Custom Field Suite with atshift Fields? =

In many cases, yes. The plugin preserves the main data structures and API compatibility, but always back up your site and test on staging before replacement.

= Does atshift Fields preserve the original Custom Field Suite API? =

Yes. It preserves the main APIs used by existing themes, including `CFS()->get()`, `get_field_info()`, `get_reverse_related()`, `save()`, `find_fields()`, and `form()`.

= Which translations are bundled? =

The release package includes bundled translation files for ca, de_DE, es_ES, fa_IR, fr_FR, hu_HU, it_IT, ja, nl_NL, pl_PL, pt_BR, ru_RU, tr_TR, and zh_CN.

= Where can I find documentation? =

See the [atshift Fields documentation site](https://cfs.at-shift.net/en/) for setup guides, output examples, API notes, and implementation examples.

== Changelog ==

= 3.0.2 =

* Added Classic Meta Box Placement for placing classic third-party meta boxes inside atshift Fields field groups.

= 3.0.1.1 =

* Bundled translation files in the WordPress.org release package.

= 3.0.1 =

* Added a Shortcode field that returns rendered shortcode output through `CFS()->get()`.
* Added role-based shortcode editing controls. Users without permission do not see the field, and existing shortcode values are preserved when they save other fields.
* Added an Extra Display Setting to force the classic post edit Screen Options layout to 1 column.
* Added Side / Main Extra Display Settings for hiding unnecessary native editor sections with section-level role targeting.

= 3.0.0 =

* Redesigned the Field Group editor UI, including group hierarchy, spacing, drag-and-drop behavior, and responsive layouts.
* Clarified nested structures for Tab, Accordion, Loop, Horizontal, and Conditional groups.
* Added condition-specific drop areas for Conditional Groups so dragged fields can be assigned to each condition more easily.
* Added automatic field-name handling for group fields and native WordPress fields to reduce unnecessary manual input.
* Improved native WordPress field support for Post Title, Post Content, Save / Publish, Post Categories, Post Tags, and Featured Image.
* Refined Save / Publish status, visibility, publish date, draft, publish, and update behavior.
* Improved display and selection behavior for Post Categories (Standard / Global) and Post Tags.
* Improved admin previews and handling for Photo Gallery, File Upload, and image fields.
* Updated the atshift Fields Tool UI with clearer Export / Import / Reset guidance and translations.
* Improved placement rules, extras, tooltips, Select2/Selectize displays, and admin UI details.
* Refined required-field and group-structure warnings so issues are easier to understand.
* Improved metabox compatibility and display stability in both Block Editor and Classic Editor environments.
* Cleaned up internal PHP/CSS/JavaScript while preserving compatibility with the original Custom Field Suite API and saved data.
* Strengthened admin save and AJAX handling, including nonce and capability checks.
* Updated multilingual translations for new fields, settings, warnings, and tool screens.
